<?php

namespace App\Http\Controllers;

use App\Models\Pick;
use App\Services\PickemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PickController extends Controller
{
    public function __construct(private PickemService $service) {}

    // ── GET /next-race/submit ────────────────────────────────────────────────
    public function showSubmit()
    {
        $sessionKey  = $this->service->getSessionKey();
        $year        = $this->service->getYear($sessionKey);
        $race        = $this->service->getRace($sessionKey);
        $drivers     = $this->service->getDrivers($year);
        // @dd($drivers);
        $races       = $this->service->getSessionRaces($sessionKey);
        $currentPick = Auth::user()->getPicks($sessionKey);
        $lastPicks   = Auth::user()->getLastPickNumbers();
        // @dd($lastPicks);

        // Already submitted — go straight to results
        if ($currentPick) {
            return redirect()->route('next-race');
        }

        // Bonus tier (based on which session hasn't started yet)
        $bonus        = 1.0;
        $bonusLabel   = 'NO BONUS';
        $bonusDisplay = '+0%';
        $bonusColor   = '#888888';

        $tiers = [
            ['mult' => 1.50, 'label' => 'EARLY BIRD', 'display' => '+50%', 'color' => '#22c55e'],
            ['mult' => 1.25, 'label' => 'EARLY',       'display' => '+25%', 'color' => '#86efac'],
            ['mult' => 1.10, 'label' => 'BONUS',        'display' => '+10%', 'color' => '#fbbf24'],
            ['mult' => 1.00, 'label' => 'NO BONUS',     'display' => '+0%',  'color' => '#888888'],
            ['mult' => 0.50, 'label' => 'LATE PENALTY', 'display' => '-50%', 'color' => '#E10600'],
        ];

        if ($races->isNotEmpty()) {
            $now = now('America/Chicago');
            foreach ($races as $index => $sessionRace) {
                if ($now->lt($sessionRace->date_start)) {
                    $tier         = $tiers[$index] ?? $tiers[3];
                    $bonus        = $tier['mult'];
                    $bonusLabel   = $tier['label'];
                    $bonusDisplay = $tier['display'];
                    $bonusColor   = $tier['color'];
                    break;
                }
            }
        }
        
        $numPicks = $this->service->getPicks($sessionKey)->count();

        return view('next-race.submit', compact(
            'race', 'drivers', 'bonus', 'bonusLabel', 'bonusDisplay', 'bonusColor',
            'sessionKey', 'year', 'numPicks', 'lastPicks'
        ));
    }

    // ── GET /next-race ───────────────────────────────────────────────────────
    public function showResult()
    {
        $sessionKey  = $this->service->getSessionKey();
        $year        = $this->service->getYear($sessionKey);
        $race        = $this->service->getRace($sessionKey);
        $currentPick = Auth::user()->getPicks($sessionKey);

        // Not submitted yet — redirect to the form
        if (! $currentPick) {
            return redirect()->route('next-race.submit');
        }

        $picks = $this->service->getPicks($sessionKey);

        return view('next-race.index', compact(
            'race', 'picks', 'currentPick', 'sessionKey', 'year'
        ));
    }

    // ── POST /submit-picks ───────────────────────────────────────────────────
    public function submit(Request $request)
    {
        $payload = json_decode($request->input('bettor'), true);

        if (! is_array($payload) || empty($payload['bets']) || count($payload['bets']) !== 3) {
            return back()->withErrors(['error' => 'Invalid payload']);
        }

        $bets = array_map('intval', $payload['bets']);
        if (in_array(0, $bets, true)) {
            return back()->withErrors(['error' => 'Missing bets']);
        }

        $sessionKey = $this->service->getSessionKey();
        $bonus = floatval($payload['bonus'] ?? 1.0);

        Pick::updateOrCreate(
            ['user_id' => Auth::id(), 'session_key' => $sessionKey],
            [
                'd1_id' => $bets[0],
                'd2_id' => $bets[1],
                'd3_id' => $bets[2],
                'bonus' => $bonus,
                'score' => 0,
            ]
        );

        // Send notifications to other users who opted in
        $this->service->getSmsService()->notifyOthers(auth()->user(), $bonus, $sessionKey);

        return redirect()->route('next-race');
    }
}

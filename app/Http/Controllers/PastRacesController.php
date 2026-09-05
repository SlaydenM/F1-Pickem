<?php

namespace App\Http\Controllers;

use App\Models\Race;
use App\Services\PickemService;
use Illuminate\Http\Request;

class PastRacesController extends Controller
{
    public function __construct(private PickemService $service) {}

    public function index(Request $request)
    {
        // All years that have at least one Grand Prix in the DB
        $years = Race::where('type', 'G')
            ->get()
            ->map(fn ($r) => intdiv((int) $r->session_key, 1000) + 2023)
            ->unique()
            ->sortDesc()
            ->values();

        // Resolve session key: explicit param → year param → latest completed race
        $sessionKey = (int) $request->query('sessionKey', 0);

        if (! $sessionKey) {
            $yearParam = (int) $request->query('year', 0);

            if ($yearParam) {
                $start = ($yearParam - 2023) * 1000 + 1;
                $end   = ($yearParam - 2023) * 1000 + 999;
                $latest = Race::where('type', 'G')
                    ->whereBetween('session_key', [$start, $end])
                    ->where('date_start', '<', now())
                    ->orderByDesc('session_key')
                    ->first();
                $sessionKey = $latest ? (int) $latest->session_key : 0;
            } else {
                $latest = Race::where('type', 'G')
                    ->where('date_start', '<', now())
                    ->orderByDesc('session_key')
                    ->first();
                $sessionKey = $latest ? (int) $latest->session_key : 0;
            }
        }

        $year = $sessionKey
            ? intdiv($sessionKey, 1000) + 2023
            : ($years->first() ?? now()->year);

        $start = ($year - 2023) * 1000 + 1;
        $end   = ($year - 2023) * 1000 + 999;

        $racesForYear = Race::where('type', 'G')
            ->whereBetween('session_key', [$start, $end])
            ->where('date_start', '<', now())
            ->orderBy('session_key')
            ->get();

        $race        = null;
        $picks       = collect();
        $winners     = collect();
        $correctBets = [];

        if ($sessionKey) {
            $race        = $this->service->getRace($sessionKey);
            $picks       = $this->service->getPicks($sessionKey);
            $winners     = $this->service->getWinners($sessionKey);
            $correctBets = $this->service->getCorrectBets($winners, $picks);
        }

        return view('past-races', compact(
            'years', 'year', 'racesForYear', 'sessionKey',
            'race', 'picks', 'winners', 'correctBets'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\PickemService;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(private PickemService $service) {}

    public function index()
    {
        $sessionKey  = $this->service->getSessionKey();
        $year        = $this->service->getYear($sessionKey);
        $race        = $this->service->getRace($sessionKey);
        $players     = $this->service->getPlayers($year);
        $rankChanges = $this->service->getRankChanges($sessionKey, $players);
        $round       = $sessionKey % 1000;

        $currentPick = Auth::user()->getPicks($sessionKey);

        // Derive current user's rank and score from the sorted standings
        $myIndex = $players->search(fn ($p) => $p->id === Auth::id());
        $myRank  = $myIndex !== false ? $myIndex + 1 : null;
        $myScore = $myIndex !== false ? $players[$myIndex]->total_score : 0;

        return view('home', compact(
            'race', 'players', 'rankChanges', 'year', 'round',
            'sessionKey', 'currentPick', 'myRank', 'myScore'
        ));
    }
}

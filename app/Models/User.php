<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function picks()
    {
        return $this->hasMany(Pick::class);
    }

    public function user_settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Get the user's name (Player::getName)
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get total yearly score (Player::getTotal)
     */
    public function getTotal(int $year): float
    {
        // Get all session keys for the given year (e.g., 3001 to 3024)
        $startKey = ($year - 2023) * 1000;
        $endKey = $startKey + 999;

        return $this->picks()
            ->whereBetween('session_key', [$startKey, $endKey])
            ->get()
            ->sum(function ($pick) {
                return $pick->score * $pick->bonus; // Incorporating the bonus logic
            });
    }

    /**
     * Get score for a specific session (Player::getScore)
     */
    public function getScore(int $sessionKey): float
    {
        $pick = $this->picks()->where('session_key', $sessionKey)->first();
        return $pick ? ($pick->score * $pick->bonus) : 0;
    }

    /**
     * Set/Update score for a session (Player::setScore)
     */
    public function updateScore(int $sessionKey, float $score): void
    {
        $this->picks()->updateOrCreate(
            ['session_key' => $sessionKey],
            ['score' => $score]
        );
    }

    /**
     * Get picks for a specific session (Player::getPicks)
     */
    public function getPicks(int $sessionKey) {
        return $this->picks()->where('session_key', $sessionKey)->first();
    }

    public function getLastPickNumbers(): array
    {   
        $latestPick = $this->picks()->orderByDesc('session_key')->first();

        if (!$latestPick) {
            return [];
        }

        return array_filter([
            'first' => $latestPick->d1_id,
            'tenth' => $latestPick->d2_id,
            'last'  => $latestPick->d3_id,
        ]);
    }

    /**
     * Update d1, d2, d3, and bonus (Player::setPicks)
     */
    public function setPicks(int $sessionKey, int $d1_id, int $d2_id, int $d3_id, float $bonus = 1.0): void
    {
        $this->picks()->updateOrCreate(
            ['session_key' => $sessionKey],
            [
                'd1_id' => $d1_id,
                'd2_id' => $d2_id,
                'd3_id' => $d3_id,
                'bonus' => $bonus
            ]
        );
    }

    /**
     * Get bonus for a specific session (Player::getBonus)
     */
    public function getBonus(int $sessionKey): float
    {
        $pick = $this->picks()->where('session_key', $sessionKey)->first();
        return $pick ? $pick->bonus : 1.0; // Default bonus is 1.0 if not set
    }
}

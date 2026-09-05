<?php

namespace Tests\Unit;

use App\Models\Driver;
use App\Models\Winner;
use PHPUnit\Framework\TestCase;

class WinnerModelTest extends TestCase
{
    public function test_winner_has_driver_relationship(): void
    {
        $winner = new Winner();

        $relation = $winner->driver();

        $this->assertNotNull($relation);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertSame(Driver::class, $relation->getRelated()::class);
    }
}

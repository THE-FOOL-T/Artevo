<?php

namespace Tests\Feature;

use App\Models\Artifact;
use App\Models\Museum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function artifacts_index_does_not_have_n_plus_one_problem(): void
    {
        Artifact::factory()->count(10)->create(['status' => Artifact::STATUS_PUBLIC]);

        DB::enableQueryLog();

        $response = $this->get('/artifacts');
        
        $response->assertOk();

        $queryCount = count(DB::getQueryLog());
        
        // We expect a small number of queries (pagination, eagerly loaded relations), not N queries
        $this->assertLessThan(15, $queryCount, "Expected less than 15 queries, but ran {$queryCount}. N+1 problem might exist.");
    }

    /** @test */
    public function museums_index_does_not_have_n_plus_one_problem(): void
    {
        Museum::factory()->count(10)->create();

        DB::enableQueryLog();

        $response = $this->get('/museums');

        $response->assertOk();

        $queryCount = count(DB::getQueryLog());

        // We expect a small number of queries (pagination, eagerly loaded relations, counts), not N queries
        $this->assertLessThan(15, $queryCount, "Expected less than 15 queries, but ran {$queryCount}. N+1 problem might exist.");
    }
}

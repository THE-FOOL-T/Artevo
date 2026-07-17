<?php

namespace Database\Factories;

use App\Models\Artifact;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CertificateFactory extends Factory
{
    protected $model = Certificate::class;

    public function definition(): array
    {
        return [
            'artifact_id' => Artifact::factory(),
            'issued_to' => User::factory(),
            'issued_by' => User::factory()->admin(),
            'type' => Certificate::TYPE_VERIFICATION,
            'serial' => 'ARTEVO-' . now()->year . '-' . strtoupper(Str::random(8)),
            'reference_type' => null,
            'reference_id' => null,
            'notes' => null,
            'revoked_at' => null,
            'revocation_reason' => null,
        ];
    }
}

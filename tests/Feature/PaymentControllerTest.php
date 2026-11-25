<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeCompanyAndUser(string $role = 'commercial'): array
    {
        $company = Company::create(['name' => 'NexaGestion', 'currency' => 'XOF']);
        $user = User::create([
            'company_id' => $company->id,
            'name' => 'User',
            'email' => $role.'@test.test',
            'password' => Hash::make('Password12345'),
            'role' => $role,
        ]);

        return [$company, $user];
    }

    public function test_commercial_can_list_payments(): void
    {
        [$company, $user] = $this->makeCompanyAndUser('commercial');

        $this->actingAs($user);
        $this->get(route('payments.index'))->assertOk();
    }

    public function test_reader_cannot_access_payments(): void
    {
        [$company, $user] = $this->makeCompanyAndUser('reader');

        $this->actingAs($user);
        $this->get(route('payments.index'))->assertStatus(403);
    }
}

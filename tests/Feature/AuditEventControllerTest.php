<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuditEventControllerTest extends TestCase
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

    public function test_user_can_view_audit_log(): void
    {
        [$company, $user] = $this->makeCompanyAndUser('commercial');

        $this->actingAs($user);
        $this->get(route('audit-events.index'))->assertOk();
    }

    public function test_guest_is_redirected(): void
    {
        $this->get(route('audit-events.index'))->assertRedirect(route('login'));
    }
}

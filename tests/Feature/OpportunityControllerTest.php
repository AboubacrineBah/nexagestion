<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OpportunityControllerTest extends TestCase
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

    public function test_commercial_can_create_opportunity(): void
    {
        [$company, $user] = $this->makeCompanyAndUser('commercial');
        $client = Client::create(['company_id' => $company->id, 'created_by' => $user->id, 'name' => 'Acme', 'type' => 'prospect']);

        $this->actingAs($user);
        $response = $this->post(route('opportunities.store'), [
            'client_id' => $client->id,
            'title' => 'Nouvelle opportunité',
            'stage' => 'new',
            'expected_amount' => 100000,
        ]);

        $response->assertRedirect(route('opportunities.index'));
        $this->assertDatabaseHas('opportunities', ['title' => 'Nouvelle opportunité', 'company_id' => $company->id]);
    }

    public function test_reader_cannot_access_opportunities(): void
    {
        [$company, $user] = $this->makeCompanyAndUser('reader');

        $this->actingAs($user);
        $this->get(route('opportunities.index'))->assertStatus(403);
    }
}

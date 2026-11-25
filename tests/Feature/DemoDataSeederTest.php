<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use RuntimeException;
use Tests\TestCase;

class DemoDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeder_creates_cross_module_demo_data_without_duplicates(): void
    {
        $company = Company::create([
            'name' => 'Entreprise de test',
            'currency' => 'XOF',
        ]);

        $existingClient = Client::create([
            'company_id' => $company->id,
            'name' => 'Client existant',
            'type' => 'client',
            'email' => 'existing@example.test',
        ]);

        $this->seed();
        $this->assertDatabaseCount('contracts', 2);
        $this->seed();

        $this->assertDatabaseCount('clients', 4);
        $this->assertDatabaseCount('opportunities', 2);
        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseCount('stock_movements', 2);
        $this->assertDatabaseCount('sales_documents', 2);
        $this->assertDatabaseCount('sales_document_lines', 2);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('employees', 2);
        $this->assertDatabaseCount('contracts', 2);
        $this->assertDatabaseCount('leave_requests', 2);
        $this->assertDatabaseCount('payslips', 6);
        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseHas('users', [
            'company_id' => $company->id,
            'email' => 'owner.demo@example.test',
            'role' => 'owner',
        ]);
        $this->assertDatabaseHas('users', [
            'company_id' => $company->id,
            'email' => 'manager.demo@example.test',
            'role' => 'manager',
        ]);
        $this->assertDatabaseHas('users', [
            'company_id' => $company->id,
            'email' => 'commercial.demo@example.test',
            'role' => 'commercial',
        ]);
        $this->assertDatabaseHas('users', [
            'company_id' => $company->id,
            'email' => 'finance.demo@example.test',
            'role' => 'finance',
        ]);
        $this->assertModelExists($existingClient);
        $this->assertDatabaseHas('clients', [
            'company_id' => $company->id,
            'email' => 'alpha.demo@example.test',
            'type' => 'prospect',
        ]);

        $product = Product::query()->where('sku', 'DEMO-ITEM-001')->firstOrFail();
        $this->assertSame(20, $product->quantity_in_stock);
        $this->assertDatabaseHas('stock_movements', [
            'company_id' => $company->id,
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 20,
        ]);
        $this->assertDatabaseHas('sales_documents', [
            'company_id' => $company->id,
            'reference' => 'FAC-DEMO-0001',
            'status' => 'partial',
            'total' => '100000.00',
        ]);
        $this->assertDatabaseHas('payments', [
            'sales_document_id' => $company->salesDocuments()->where('reference', 'FAC-DEMO-0001')->value('id'),
            'reference' => 'PAY-DEMO-0001',
            'amount' => '25000.00',
        ]);

        $awa = Employee::query()->where('email', 'awa.demo@example.test')->firstOrFail();
        $septemberPayslip = $awa->payslips()->whereDate('pay_period', '2026-09-01')->firstOrFail();
        $this->assertSame(35000000, $septemberPayslip->gross_salary_cents);
        $this->assertSame(3500000, $septemberPayslip->deductions_cents);
        $this->assertSame(31500000, $septemberPayslip->net_salary_cents);
        $this->assertSame('paid', $septemberPayslip->status);
        $this->assertSame('2026-09-30', $septemberPayslip->paid_at->toDateString());

        $novemberPayslip = $awa->payslips()->whereDate('pay_period', '2026-11-01')->firstOrFail();
        $this->assertSame('draft', $novemberPayslip->status);
        $this->assertNull($novemberPayslip->paid_at);
    }

    public function test_demo_owner_can_sign_in_with_the_generated_password(): void
    {
        $company = Company::create([
            'name' => 'Entreprise de test',
            'currency' => 'XOF',
        ]);

        Artisan::call('db:seed', ['--class' => DemoDataSeeder::class]);

        $output = Artisan::output();
        $this->assertSame(
            1,
            preg_match(
                '/Compte démo créé : owner\.demo@example\.test \(owner\) - mot de passe : ([a-f0-9]{32})/',
                $output,
                $matches,
            ),
        );
        $user = User::query()->where('email', 'owner.demo@example.test')->firstOrFail();

        $this->post('/connexion', [
            'email' => $user->email,
            'password' => $matches[1],
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertSame($company->id, $user->company_id);
    }

    public function test_demo_accounts_are_not_created_outside_local_or_testing_environments(): void
    {
        Company::create([
            'name' => 'Entreprise de test',
            'currency' => 'XOF',
        ]);
        $this->app['env'] = 'production';

        $this->app->make(DemoDataSeeder::class)->run();

        $this->assertDatabaseMissing('users', ['email' => 'owner.demo@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'manager.demo@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'commercial.demo@example.test']);
        $this->assertDatabaseMissing('users', ['email' => 'finance.demo@example.test']);
    }

    public function test_default_seeder_refuses_to_seed_when_company_scope_is_ambiguous(): void
    {
        Company::create(['name' => 'Entreprise Alpha', 'currency' => 'XOF']);
        Company::create(['name' => 'Entreprise Beta', 'currency' => 'XOF']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Demo data seeding requires exactly one company.');

        $this->seed();
    }
}

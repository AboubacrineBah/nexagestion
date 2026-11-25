<?php

namespace Tests\Feature;

use App\Events\InvoiceValidated;
use App\Models\Client;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Product;
use App\Models\SalesDocument;
use App\Models\SalesDocumentLine;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_reader_users_cannot_view_company_business_data(): void
    {
        $company = Company::create([
            'name' => 'NexaGestion',
            'email' => 'hello@nexagestion.test',
            'currency' => 'XOF',
        ]);

        $reader = User::create([
            'company_id' => $company->id,
            'name' => 'Lecteur',
            'email' => 'reader@nexagestion.test',
            'password' => Hash::make('Password12345'),
            'role' => 'reader',
        ]);

        $client = Client::create([
            'company_id' => $company->id,
            'created_by' => $reader->id,
            'name' => 'Acme',
            'type' => 'prospect',
            'email' => 'contact@acme.test',
        ]);

        $document = SalesDocument::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'created_by' => $reader->id,
            'reference' => 'DEV-2026-0001',
            'type' => 'quote',
            'status' => 'draft',
            'issued_at' => now()->toDateString(),
            'currency' => 'XOF',
            'subtotal' => 1000,
            'total' => 1000,
        ]);

        $this->actingAs($reader);

        $this->assertFalse(Gate::allows('viewAny', Client::class));
        $this->assertFalse(Gate::allows('view', $client));
        $this->assertFalse(Gate::allows('viewAny', SalesDocument::class));
        $this->assertFalse(Gate::allows('view', $document));
    }

    public function test_commercial_users_can_view_company_business_data(): void
    {
        $company = Company::create([
            'name' => 'NexaGestion',
            'email' => 'sales@nexagestion.test',
            'currency' => 'XOF',
        ]);

        $commercial = User::create([
            'company_id' => $company->id,
            'name' => 'Commercial',
            'email' => 'commercial@nexagestion.test',
            'password' => Hash::make('Password12345'),
            'role' => 'commercial',
        ]);

        $client = Client::create([
            'company_id' => $company->id,
            'created_by' => $commercial->id,
            'name' => 'Contoso',
            'type' => 'client',
            'email' => 'contact@contoso.test',
        ]);

        $document = SalesDocument::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'created_by' => $commercial->id,
            'reference' => 'FAC-2026-0001',
            'type' => 'invoice',
            'status' => 'unpaid',
            'issued_at' => now()->toDateString(),
            'currency' => 'XOF',
            'subtotal' => 2000,
            'total' => 2000,
        ]);

        $this->actingAs($commercial);

        $this->assertTrue(Gate::allows('viewAny', Client::class));
        $this->assertTrue(Gate::allows('view', $client));
        $this->assertTrue(Gate::allows('viewAny', SalesDocument::class));
        $this->assertTrue(Gate::allows('view', $document));
    }

    public function test_reader_users_are_blocked_on_module_routes(): void
    {
        $company = Company::create([
            'name' => 'NexaGestion',
            'currency' => 'XOF',
        ]);

        $reader = User::create([
            'company_id' => $company->id,
            'name' => 'Lecteur',
            'email' => 'reader2@nexagestion.test',
            'password' => Hash::make('Password12345'),
            'role' => 'reader',
        ]);

        $this->actingAs($reader);

        $this->get('/clients')->assertStatus(403);
        $this->get('/ventes')->assertStatus(403);
    }

    public function test_hr_manager_can_access_employee_module(): void
    {
        $company = Company::create([
            'name' => 'NexaGestion',
            'currency' => 'XOF',
        ]);

        $manager = User::create([
            'company_id' => $company->id,
            'name' => 'Manager RH',
            'email' => 'rh-manager@nexagestion.test',
            'password' => Hash::make('Password12345'),
            'role' => 'manager',
        ]);

        $this->actingAs($manager);

        $this->get('/employees')->assertOk();

        $response = $this->post('/employees', [
            'first_name' => 'Amina',
            'last_name' => 'Diallo',
            'position' => 'Chargée de paie',
            'email' => 'amina@nexagestion.test',
            'phone' => '+22300000000',
            'status' => 'active',
            'hire_date' => '2026-01-15',
            'monthly_salary_cents' => 350000,
        ]);

        $response->assertRedirect('/employees');
        $this->assertDatabaseHas('employees', ['email' => 'amina@nexagestion.test', 'company_id' => $company->id]);
    }

    public function test_invoice_validation_decrements_stock_and_creates_stock_movements(): void
    {
        $company = Company::create([
            'name' => 'NexaGestion',
            'currency' => 'XOF',
        ]);

        $commercial = User::create([
            'company_id' => $company->id,
            'name' => 'Commercial',
            'email' => 'commercial2@nexagestion.test',
            'password' => Hash::make('Password12345'),
            'role' => 'commercial',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Ordinateur portable',
            'sku' => 'LAP-001',
            'purchase_price_cents' => 500000,
            'sale_price_cents' => 750000,
            'quantity_in_stock' => 15,
            'alert_threshold' => 5,
            'is_active' => true,
        ]);

        $client = Client::create([
            'company_id' => $company->id,
            'created_by' => $commercial->id,
            'name' => 'Bureau Test',
            'type' => 'client',
            'email' => 'client@bureau.test',
        ]);

        $document = SalesDocument::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'created_by' => $commercial->id,
            'reference' => 'FAC-2026-0009',
            'type' => 'invoice',
            'status' => 'unpaid',
            'issued_at' => now()->toDateString(),
            'currency' => 'XOF',
            'subtotal' => 1500000,
            'total' => 1500000,
        ]);

        SalesDocumentLine::create([
            'sales_document_id' => $document->id,
            'product_id' => $product->id,
            'description' => 'Ordinateur portable',
            'quantity' => 2,
            'unit_price' => 750000,
            'line_total' => 1500000,
        ]);

        $document->load('lines');

        event(new InvoiceValidated($document));

        $product->refresh();

        $this->assertSame(13, $product->quantity_in_stock);
        $this->assertDatabaseHas('stock_movements', [
            'company_id' => $company->id,
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 2,
        ]);
        $this->assertSame(1, StockMovement::where('product_id', $product->id)->count());
    }

    public function test_manager_can_create_contract_and_manual_stock_adjustment(): void
    {
        $company = Company::create([
            'name' => 'NexaGestion',
            'currency' => 'XOF',
        ]);

        $manager = User::create([
            'company_id' => $company->id,
            'name' => 'Manager RH',
            'email' => 'manager-final@nexagestion.test',
            'password' => Hash::make('Password12345'),
            'role' => 'manager',
        ]);

        $employee = Employee::create([
            'company_id' => $company->id,
            'user_id' => $manager->id,
            'first_name' => 'Mariam',
            'last_name' => 'Toure',
            'email' => 'mariam@nexagestion.test',
            'position' => 'Analyste RH',
            'status' => 'active',
            'hire_date' => '2026-01-10',
            'monthly_salary_cents' => 420000,
        ]);

        $this->actingAs($manager);

        $this->post('/employees/'.$employee->id.'/contracts', [
            'type' => 'permanent',
            'status' => 'active',
            'start_date' => '2026-01-10',
            'salary_cents' => 420000,
            'notes' => 'Contrat de travail permanent',
        ])->assertRedirect('/employees/'.$employee->id.'/contracts');

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Scanner mobile',
            'sku' => 'SCAN-010',
            'purchase_price_cents' => 180000,
            'sale_price_cents' => 260000,
            'quantity_in_stock' => 6,
            'alert_threshold' => 2,
            'is_active' => true,
        ]);

        $this->post('/products/'.$product->id.'/stock-movements', [
            'direction' => 'increase',
            'quantity' => 4,
            'notes' => 'Réapprovisionnement de sécurité',
        ])->assertRedirect('/products/'.$product->id.'/stock-movements');

        $product->refresh();
        $this->assertSame(10, $product->quantity_in_stock);
        $this->assertDatabaseHas('stock_movements', ['product_id' => $product->id, 'type' => 'in', 'quantity' => 4]);
    }

    public function test_quote_conversion_keeps_product_reference_and_company_scope_is_enforced(): void
    {
        $company = Company::create([
            'name' => 'NexaGestion',
            'currency' => 'XOF',
        ]);

        $commercial = User::create([
            'company_id' => $company->id,
            'name' => 'Commercial',
            'email' => 'commercial-quote@nexagestion.test',
            'password' => Hash::make('Password12345'),
            'role' => 'commercial',
        ]);

        $product = Product::create([
            'company_id' => $company->id,
            'name' => 'Imprimante',
            'sku' => 'IMP-2026',
            'purchase_price_cents' => 250000,
            'sale_price_cents' => 420000,
            'quantity_in_stock' => 9,
            'alert_threshold' => 3,
            'is_active' => true,
        ]);

        $client = Client::create([
            'company_id' => $company->id,
            'created_by' => $commercial->id,
            'name' => 'Entreprise Demo',
            'type' => 'prospect',
            'email' => 'contact@demo.test',
        ]);

        $quote = SalesDocument::create([
            'company_id' => $company->id,
            'client_id' => $client->id,
            'created_by' => $commercial->id,
            'reference' => 'DEV-2026-0007',
            'type' => 'quote',
            'status' => 'accepted',
            'issued_at' => now()->toDateString(),
            'currency' => 'XOF',
            'subtotal' => 420000,
            'total' => 420000,
        ]);

        SalesDocumentLine::create([
            'sales_document_id' => $quote->id,
            'product_id' => $product->id,
            'description' => 'Imprimante',
            'quantity' => 1,
            'unit_price' => 420000,
            'line_total' => 420000,
        ]);

        $this->actingAs($commercial);

        $this->post('/ventes/'.$quote->id.'/convertir-en-facture')
            ->assertRedirect();

        $invoice = SalesDocument::where('source_document_id', $quote->id)->firstOrFail();
        $this->assertDatabaseHas('sales_document_lines', [
            'sales_document_id' => $invoice->id,
            'product_id' => $product->id,
        ]);

        $anotherCompany = Company::create(['name' => 'Autre Entreprise', 'currency' => 'EUR']);
        $otherProduct = Product::create([
            'company_id' => $anotherCompany->id,
            'name' => 'Autre produit',
            'sku' => 'IMP-OTHER-2026',
            'purchase_price_cents' => 1000,
            'sale_price_cents' => 2000,
            'quantity_in_stock' => 5,
            'alert_threshold' => 1,
            'is_active' => true,
        ]);

        $this->post('/ventes', [
            'client_id' => $client->id,
            'type' => 'invoice',
            'issued_at' => now()->toDateString(),
            'lines' => [[
                'product_id' => $otherProduct->id,
                'description' => 'Produit hors société',
                'quantity' => 1,
                'unit_price' => 2500,
            ]],
        ])->assertSessionHasErrors(['lines.0.product_id']);
    }
}

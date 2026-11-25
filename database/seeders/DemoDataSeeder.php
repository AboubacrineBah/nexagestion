<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::query()->limit(2)->get();

        if ($companies->count() !== 1) {
            throw new RuntimeException('Demo data seeding requires exactly one company.');
        }

        $company = $companies->firstOrFail();

        DB::transaction(function () use ($company): void {
            $this->seedDemoUsers($company);
            $clients = $this->seedClients($company);
            $this->seedOpportunities($company, $clients);
            $products = $this->seedProducts($company);
            $this->seedSales($company, $clients, $products);
            $this->seedHumanResources($company);
            $this->seedPayroll($company);
        });
    }

    private function seedDemoUsers(Company $company): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        foreach ([
            [
                'email' => 'owner.demo@example.test',
                'name' => 'Propriétaire Démo',
                'role' => 'owner',
            ],
            [
                'email' => 'manager.demo@example.test',
                'name' => 'Gestionnaire RH Démo',
                'role' => 'manager',
            ],
            [
                'email' => 'commercial.demo@example.test',
                'name' => 'Commercial Démo',
                'role' => 'commercial',
            ],
            [
                'email' => 'finance.demo@example.test',
                'name' => 'Finance Démo',
                'role' => 'finance',
            ],
        ] as $accountData) {
            $temporaryPassword = bin2hex(random_bytes(16));
            $user = User::query()->firstOrCreate(
                ['email' => $accountData['email']],
                [
                    'company_id' => $company->id,
                    'name' => $accountData['name'],
                    'password' => Hash::make($temporaryPassword),
                    'role' => $accountData['role'],
                    'email_verified_at' => now(),
                ],
            );

            if ($user->company_id !== $company->id || $user->role !== $accountData['role']) {
                throw new RuntimeException("Demo account [{$accountData['email']}] is assigned to a different company or role.");
            }

            if ($user->wasRecentlyCreated) {
                $this->command?->line(sprintf(
                    'Compte démo créé : %s (%s) - mot de passe : %s',
                    $accountData['email'],
                    $accountData['role'],
                    $temporaryPassword,
                ));
            }
        }
    }

    /**
     * @return array<string, Client>
     */
    private function seedClients(Company $company): array
    {
        $clients = [];

        foreach ([
            [
                'email' => 'alpha.demo@example.test',
                'name' => 'Client Démo Alpha',
                'type' => 'prospect',
                'address' => 'Adresse fictive - Démo',
                'notes' => 'Données fictives de démonstration.',
            ],
            [
                'email' => 'beta.demo@example.test',
                'name' => 'Client Démo Beta',
                'type' => 'client',
                'address' => 'Adresse fictive - Démo',
                'notes' => 'Données fictives de démonstration.',
            ],
            [
                'email' => 'gamma.demo@example.test',
                'name' => 'Client Démo Gamma',
                'type' => 'prospect',
                'address' => 'Adresse fictive - Démo',
                'notes' => 'Données fictives de démonstration.',
            ],
        ] as $clientData) {
            $clients[$clientData['email']] = $company->clients()->firstOrCreate(
                ['email' => $clientData['email']],
                $clientData,
            );
        }

        return $clients;
    }

    /**
     * @param  array<string, Client>  $clients
     */
    private function seedOpportunities(Company $company, array $clients): void
    {
        foreach ([
            [
                'client_email' => 'alpha.demo@example.test',
                'title' => 'Opportunité Démo Alpha',
                'expected_amount' => '1250000.00',
                'next_action_at' => '2026-11-25',
            ],
            [
                'client_email' => 'gamma.demo@example.test',
                'title' => 'Opportunité Démo Gamma',
                'expected_amount' => '750000.00',
                'next_action_at' => '2026-12-02',
            ],
        ] as $opportunityData) {
            Opportunity::query()->firstOrCreate(
                [
                    'company_id' => $company->id,
                    'client_id' => $clients[$opportunityData['client_email']]->id,
                    'title' => $opportunityData['title'],
                ],
                [
                    'stage' => 'new',
                    'expected_amount' => $opportunityData['expected_amount'],
                    'next_action_at' => $opportunityData['next_action_at'],
                    'notes' => 'Données fictives de démonstration.',
                ],
            );
        }
    }

    /**
     * @return array<string, Product>
     */
    private function seedProducts(Company $company): array
    {
        $products = [];

        foreach ([
            [
                'sku' => 'DEMO-ITEM-001',
                'name' => 'Article Démo Alpha',
                'description' => 'Article fictif pour les démonstrations.',
                'purchase_price_cents' => 150000,
                'sale_price_cents' => 250000,
                'alert_threshold' => 3,
                'opening_quantity' => 20,
            ],
            [
                'sku' => 'DEMO-ITEM-002',
                'name' => 'Article Démo Beta',
                'description' => 'Article fictif pour les démonstrations.',
                'purchase_price_cents' => 90000,
                'sale_price_cents' => 150000,
                'alert_threshold' => 2,
                'opening_quantity' => 15,
            ],
        ] as $productData) {
            $product = $company->products()->firstOrCreate(
                ['sku' => $productData['sku']],
                [
                    'name' => $productData['name'],
                    'description' => $productData['description'],
                    'purchase_price_cents' => $productData['purchase_price_cents'],
                    'sale_price_cents' => $productData['sale_price_cents'],
                    'quantity_in_stock' => 0,
                    'alert_threshold' => $productData['alert_threshold'],
                    'is_active' => true,
                ],
            );

            $movement = $product->stockMovements()->firstOrCreate(
                [
                    'reference_type' => 'demo_seed',
                    'reference_id' => 1,
                ],
                [
                    'company_id' => $company->id,
                    'type' => 'in',
                    'quantity' => $productData['opening_quantity'],
                    'unit_price_cents' => $product->purchase_price_cents,
                    'notes' => 'Entrée de stock fictive de démonstration.',
                ],
            );

            if ($movement->wasRecentlyCreated) {
                $product->increment('quantity_in_stock', $movement->quantity);
            }

            $products[$productData['sku']] = $product->refresh();
        }

        return $products;
    }

    /**
     * @param  array<string, Client>  $clients
     * @param  array<string, Product>  $products
     */
    private function seedSales(Company $company, array $clients, array $products): void
    {
        $quote = $company->salesDocuments()->firstOrCreate(
            ['reference' => 'DEV-DEMO-0001'],
            [
                'client_id' => $clients['alpha.demo@example.test']->id,
                'type' => 'quote',
                'status' => 'draft',
                'issued_at' => '2026-11-13',
                'due_at' => '2026-12-13',
                'currency' => $company->currency,
                'subtotal' => '5000.00',
                'total' => '5000.00',
                'notes' => 'Devis fictif de démonstration.',
            ],
        );

        $quote->lines()->firstOrCreate(
            ['product_id' => $products['DEMO-ITEM-001']->id],
            [
                'description' => $products['DEMO-ITEM-001']->name,
                'quantity' => '2.00',
                'unit_price' => '2500.00',
                'line_total' => '5000.00',
            ],
        );

        $invoice = $company->salesDocuments()->firstOrCreate(
            ['reference' => 'FAC-DEMO-0001'],
            [
                'client_id' => $clients['beta.demo@example.test']->id,
                'type' => 'invoice',
                'status' => 'partial',
                'issued_at' => '2026-11-13',
                'due_at' => '2026-12-13',
                'currency' => $company->currency,
                'subtotal' => '100000.00',
                'total' => '100000.00',
                'notes' => 'Facture fictive partiellement réglée pour une prestation sans stock.',
            ],
        );

        $invoice->lines()->firstOrCreate(
            [
                'product_id' => null,
                'description' => 'Prestation de démonstration',
            ],
            [
                'quantity' => '1.00',
                'unit_price' => '100000.00',
                'line_total' => '100000.00',
            ],
        );

        $invoice->payments()->firstOrCreate(
            ['reference' => 'PAY-DEMO-0001'],
            [
                'company_id' => $company->id,
                'amount' => '25000.00',
                'paid_at' => '2026-11-13',
                'method' => 'bank',
                'notes' => 'Règlement fictif de démonstration.',
            ],
        );
    }

    private function seedHumanResources(Company $company): void
    {
        foreach ([
            [
                'first_name' => 'Awa',
                'last_name' => 'Démo Alpha',
                'position' => 'Assistante de gestion',
                'email' => 'awa.demo@example.test',
                'status' => 'active',
                'hire_date' => '2025-02-01',
                'monthly_salary_cents' => 35000000,
                'leave_start' => '2026-12-07',
                'leave_end' => '2026-12-11',
                'leave_status' => 'pending',
            ],
            [
                'first_name' => 'Moussa',
                'last_name' => 'Démo Beta',
                'position' => 'Technicien support',
                'email' => 'moussa.demo@example.test',
                'status' => 'active',
                'hire_date' => '2025-05-15',
                'monthly_salary_cents' => 28000000,
                'leave_start' => '2026-12-14',
                'leave_end' => '2026-12-15',
                'leave_status' => 'approved',
            ],
        ] as $employeeData) {
            $employee = $company->employees()->firstOrCreate(
                ['email' => $employeeData['email']],
                [
                    'first_name' => $employeeData['first_name'],
                    'last_name' => $employeeData['last_name'],
                    'position' => $employeeData['position'],
                    'status' => $employeeData['status'],
                    'hire_date' => $employeeData['hire_date'],
                    'monthly_salary_cents' => $employeeData['monthly_salary_cents'],
                ],
            );

            $employee->contracts()->firstOrCreate(
                ['type' => 'permanent'],
                [
                    'status' => 'active',
                    'start_date' => $employeeData['hire_date'],
                    'salary_cents' => $employeeData['monthly_salary_cents'],
                    'notes' => 'Contrat fictif de démonstration.',
                ],
            );

            $employee->leaveRequests()->firstOrCreate(
                ['type' => 'annual'],
                [
                    'start_date' => $employeeData['leave_start'],
                    'end_date' => $employeeData['leave_end'],
                    'status' => $employeeData['leave_status'],
                    'reason' => 'Demande de congé fictive de démonstration.',
                ],
            );
        }
    }

    private function seedPayroll(Company $company): void
    {
        foreach ([
            [
                'email' => 'awa.demo@example.test',
                'deductions_cents' => 3500000,
            ],
            [
                'email' => 'moussa.demo@example.test',
                'deductions_cents' => 2800000,
            ],
        ] as $employeePayroll) {
            $employee = $company->employees()
                ->where('email', $employeePayroll['email'])
                ->firstOrFail();

            foreach ([
                [
                    'pay_period' => '2026-09-01',
                    'status' => 'paid',
                    'paid_at' => '2026-09-30',
                ],
                [
                    'pay_period' => '2026-10-01',
                    'status' => 'paid',
                    'paid_at' => '2026-10-31',
                ],
                [
                    'pay_period' => '2026-11-01',
                    'status' => 'draft',
                    'paid_at' => null,
                ],
            ] as $period) {
                $payslipExists = $employee->payslips()
                    ->whereDate('pay_period', $period['pay_period'])
                    ->exists();

                if (! $payslipExists) {
                    $employee->payslips()->create([
                        'gross_salary_cents' => $employee->monthly_salary_cents,
                        'deductions_cents' => $employeePayroll['deductions_cents'],
                        'net_salary_cents' => $employee->monthly_salary_cents - $employeePayroll['deductions_cents'],
                        'status' => $period['status'],
                        'paid_at' => $period['paid_at'],
                        'notes' => 'Fiche de paie fictive de démonstration.',
                        'pay_period' => $period['pay_period'],
                    ]);
                }
            }
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PayrollControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_manager_can_list_and_view_company_payslips(): void
    {
        $company = Company::create([
            'name' => 'Entreprise RH',
            'currency' => 'XOF',
        ]);
        $manager = User::create([
            'company_id' => $company->id,
            'name' => 'Responsable RH',
            'email' => 'hr-manager@example.test',
            'password' => Hash::make('Password12345'),
            'role' => 'manager',
        ]);
        $employee = $this->createEmployee($company, 'Awa', 'Demo');
        $payslip = $this->createPayslip($employee);

        $otherCompany = Company::create([
            'name' => 'Autre entreprise',
            'currency' => 'XOF',
        ]);
        $otherEmployee = $this->createEmployee($otherCompany, 'Employe', 'Confidentiel');
        $this->createPayslip($otherEmployee);

        $this->actingAs($manager)
            ->get(route('payroll.index'))
            ->assertOk()
            ->assertSee('Awa Demo')
            ->assertDontSee('Employe Confidentiel');

        $this->get(route('payroll.show', $payslip))
            ->assertOk()
            ->assertSee('315 000,00')
            ->assertSee('Salaire net');
    }

    public function test_non_hr_user_is_forbidden_from_payroll(): void
    {
        $company = Company::create([
            'name' => 'Entreprise RH',
            'currency' => 'XOF',
        ]);
        $commercial = User::create([
            'company_id' => $company->id,
            'name' => 'Commercial',
            'email' => 'commercial@example.test',
            'password' => Hash::make('Password12345'),
            'role' => 'commercial',
        ]);

        $this->actingAs($commercial)
            ->get(route('payroll.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_sign_in_for_payroll(): void
    {
        $this->get(route('payroll.index'))
            ->assertRedirect(route('login'));
    }

    public function test_manager_cannot_view_another_company_payslip(): void
    {
        $company = Company::create([
            'name' => 'Entreprise RH',
            'currency' => 'XOF',
        ]);
        $manager = User::create([
            'company_id' => $company->id,
            'name' => 'Responsable RH',
            'email' => 'hr-manager@example.test',
            'password' => Hash::make('Password12345'),
            'role' => 'manager',
        ]);

        $otherCompany = Company::create([
            'name' => 'Autre entreprise',
            'currency' => 'XOF',
        ]);
        $otherEmployee = $this->createEmployee($otherCompany, 'Employe', 'Confidentiel');
        $payslip = $this->createPayslip($otherEmployee);

        $this->actingAs($manager)
            ->get(route('payroll.show', $payslip))
            ->assertNotFound();
    }

    private function createEmployee(Company $company, string $firstName, string $lastName): Employee
    {
        return Employee::create([
            'company_id' => $company->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower($firstName.'.'.$lastName).'@example.test',
            'status' => 'active',
            'hire_date' => '2025-01-01',
            'monthly_salary_cents' => 35000000,
        ]);
    }

    private function createPayslip(Employee $employee): Payslip
    {
        return $employee->payslips()->create([
            'pay_period' => '2026-09-01',
            'gross_salary_cents' => 35000000,
            'deductions_cents' => 3500000,
            'net_salary_cents' => 31500000,
            'status' => 'paid',
            'paid_at' => '2026-09-30',
        ]);
    }
}

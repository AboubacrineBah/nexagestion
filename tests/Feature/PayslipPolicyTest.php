<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Payslip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PayslipPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_payslip_access_requires_hr_role_and_matching_company(): void
    {
        $company = Company::create([
            'name' => 'Entreprise RH',
            'currency' => 'XOF',
        ]);
        $otherCompany = Company::create([
            'name' => 'Autre entreprise',
            'currency' => 'XOF',
        ]);
        $manager = User::create([
            'company_id' => $company->id,
            'name' => 'Responsable RH',
            'email' => 'hr-manager@example.test',
            'password' => Hash::make('Password12345'),
            'role' => 'manager',
        ]);
        $commercial = User::create([
            'company_id' => $company->id,
            'name' => 'Commercial',
            'email' => 'commercial@example.test',
            'password' => Hash::make('Password12345'),
            'role' => 'commercial',
        ]);
        $employee = $this->createEmployee($company, 'employee@example.test');
        $otherEmployee = $this->createEmployee($otherCompany, 'other-employee@example.test');
        $payslip = $this->createPayslip($employee);
        $otherPayslip = $this->createPayslip($otherEmployee);

        $this->actingAs($manager);
        $this->assertTrue(Gate::allows('viewAny', Payslip::class));
        $this->assertTrue(Gate::allows('view', $payslip));
        $this->assertFalse(Gate::allows('view', $otherPayslip));

        $this->actingAs($commercial);
        $this->assertFalse(Gate::allows('viewAny', Payslip::class));
        $this->assertFalse(Gate::allows('view', $payslip));
    }

    private function createEmployee(Company $company, string $email): Employee
    {
        return Employee::create([
            'company_id' => $company->id,
            'first_name' => 'Employe',
            'last_name' => 'Test',
            'email' => $email,
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

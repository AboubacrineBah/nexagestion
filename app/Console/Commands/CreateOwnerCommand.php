<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateOwnerCommand extends Command
{
    protected $signature = 'nexagestion:create-owner';

    protected $description = 'Crée le premier compte propriétaire et son entreprise';

    public function handle(): int
    {
        if (User::query()->exists()) {
            $this->error('Des utilisateurs existent déjà. Contactez un administrateur.');

            return self::FAILURE;
        }
        $companyName = $this->ask('Nom de l’entreprise');
        $name = $this->ask('Nom complet du propriétaire');
        $email = $this->ask('Adresse e-mail');
        $password = $this->secret('Mot de passe (12 caractères minimum)');
        if (mb_strlen((string) $password) < 12 || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Adresse e-mail ou mot de passe invalide.');

            return self::FAILURE;
        }
        DB::transaction(function () use ($companyName, $name, $email, $password): void {
            $company = Company::create(['name' => $companyName, 'currency' => 'XOF']);
            User::create(['company_id' => $company->id, 'name' => $name, 'email' => $email, 'password' => Hash::make($password), 'role' => 'owner']);
        });
        $this->info('Compte propriétaire créé. Vous pouvez vous connecter à l’application.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // id_number, phone y address son NOT NULL en migraciones; el formulario Jetstream base no los envía.
        $idNumber = $input['id_number'] ?? null;
        if ($idNumber === null || $idNumber === '') {
            do {
                $idNumber = 'REG-'.Str::upper(Str::random(10));
            } while (User::where('id_number', $idNumber)->exists());
        }

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'id_number' => $idNumber,
            'phone' => $input['phone'] ?? 'N/A',
            'address' => $input['address'] ?? 'N/A',
        ]);
    }
}

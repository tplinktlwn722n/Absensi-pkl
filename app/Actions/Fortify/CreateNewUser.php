<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Education;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            'phone' => ['required', 'string', 'max:64', 'unique:users'],
            'gender' => ['required', 'string', 'in:male,female'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'major_id' => ['required', 'exists:divisions,id'],
            'school' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        // Cari atau buat sekolah baru
        $school = Education::firstOrCreate(
            ['name' => $input['school']],
            ['name' => $input['school']]
        );

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'gender' => $input['gender'],
            'address' => $input['address'],
            'city' => $input['city'],
            'major_id' => $input['major_id'],
            'school_id' => $school->id,
            'password' => Hash::make($input['password']),
            'raw_password' => $input['password'],
        ]);
    }
}

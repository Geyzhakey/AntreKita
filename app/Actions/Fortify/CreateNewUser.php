<?php

namespace App\Actions\Fortify;

use App\Models\InstitutionWhitelist;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        $normalizedWa = User::normalizeWhatsapp($input['whatsapp_number'] ?? '');

        $data = array_merge($input, [
            'email' => strtolower(trim($input['email'] ?? '')),
            'whatsapp_number' => $normalizedWa,
        ]);

        Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'whatsapp_number' => [
                'required',
                'string',
                'regex:/^\+62[0-9]{8,14}$/',
                Rule::unique(User::class, 'whatsapp_number'),
            ],
            'password' => $this->passwordRules(),
        ], [
            'whatsapp_number.regex' => 'Nomor WhatsApp harus aktif dengan format +628... atau 08... (8-14 digit).',
            'whatsapp_number.unique' => 'Nomor WhatsApp ini sudah terdaftar di sistem AntreKita.',
        ])->validate();

        $whitelist = InstitutionWhitelist::where('email', $data['email'])
            ->where('status', 'approved')
            ->first();

        return User::create([
            'name' => trim($data['name']),
            'email' => $data['email'],
            'whatsapp_number' => $data['whatsapp_number'],
            'role' => $whitelist ? 'admin' : 'visitor',
            'is_verified_admin' => (bool) $whitelist,
            'password' => Hash::make($data['password']),
        ]);
    }
}

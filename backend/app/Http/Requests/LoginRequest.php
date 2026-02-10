<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Czy uzytkownik moze wykonac to zadanie
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reguly walidacji
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    /**
     * Komunikaty bledow walidacji
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email jest wymagany',
            'email.email' => 'Podaj prawidłowy adres email',
            'password.required' => 'Hasło jest wymagane',
        ];
    }
}

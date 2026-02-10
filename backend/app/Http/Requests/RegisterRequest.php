<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'imie' => 'required|string|max:50',
            'nazwisko' => 'required|string|max:50',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => 'required|string|min:8',
            'telefon' => [
                'required',
                'regex:/^[0-9]{9}$/',
            ],
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
            'imie.required' => 'Imię jest wymagane',
            'nazwisko.required' => 'Nazwisko jest wymagane',
            'email.required' => 'Email jest wymagany',
            'email.email' => 'Podaj prawidłowy adres email',
            'email.unique' => 'Ten email jest już zajęty',
            'email.regex' => 'Nieprawidłowy format email',
            'password.required' => 'Hasło jest wymagane',
            'password.min' => 'Hasło musi mieć minimum 8 znaków',
            'telefon.required' => 'Numer telefonu jest wymagany',
            'telefon.regex' => 'Numer telefonu musi składać się z 9 cyfr',
        ];
    }
}

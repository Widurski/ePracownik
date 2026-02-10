<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DodajGodzinyRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'data_pracy' => 'required|date|before_or_equal:today',
            'liczba_godzin' => 'required|numeric|min:0.5|max:24',
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
            'user_id.required' => 'Wybierz pracownika',
            'user_id.exists' => 'Wybrany pracownik nie istnieje',
            'data_pracy.required' => 'Data jest wymagana',
            'data_pracy.date' => 'Podaj prawidłową datę',
            'data_pracy.before_or_equal' => 'Data nie może być w przyszłości',
            'liczba_godzin.required' => 'Podaj liczbę godzin',
            'liczba_godzin.numeric' => 'Liczba godzin musi być liczbą',
            'liczba_godzin.min' => 'Minimum 0.5 godziny',
            'liczba_godzin.max' => 'Maksymalnie 24 godziny',
        ];
    }
}

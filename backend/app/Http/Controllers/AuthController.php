<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Rejestracja nowego uzytkownika
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $token = Str::random(64);

        $user = User::create([
            'name' => $request->imie.' '.$request->nazwisko,
            'imie' => $request->imie,
            'nazwisko' => $request->nazwisko,
            'email' => $request->email,
            'telefon' => $request->telefon,
            'password' => Hash::make($request->password),
            'role_id' => 1,
            'is_active' => false,
            'activation_token' => $token,
        ]);

        try {
            Mail::raw(
                "Witaj {$user->imie}!\n\nAby aktywować konto kliknij w link:\n".
                url("/api/activate/{$token}").
                "\n\nPozdrawiamy,\nZespół ePracownik",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('ePracownik - Aktywacja konta');
                }
            );
        } catch (\Exception $e) {
            // jesli serwer mailowy nie skonfigurowany to pomijamy
        }

        return response()->json([
            'message' => 'Konto utworzone. Sprawdź email aby aktywować konto.',
            'user' => $user,
        ], 201);
    }

    /**
     * Logowanie uzytkownika
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Nieprawidłowy email lub hasło',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'error' => 'Konto nie jest aktywne. Sprawdź email.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Zalogowano pomyślnie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'imie' => $user->imie,
                'nazwisko' => $user->nazwisko,
                'email' => $user->email,
                'rola' => $user->role->nazwa,
            ],
        ]);
    }

    /**
     * Wylogowanie uzytkownika
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Wylogowano']);
    }

    /**
     * Aktywacja konta uzytkownika
     */
    public function activate(string $token): JsonResponse
    {
        $user = User::where('activation_token', $token)->first();

        if (! $user) {
            return response()->json(['error' => 'Nieprawidłowy token aktywacyjny'], 404);
        }

        $user->is_active = true;
        $user->activation_token = null;
        $user->save();

        return response()->json(['message' => 'Konto zostało aktywowane. Możesz się teraz zalogować.']);
    }

    /**
     * Pobieranie danych zalogowanego uzytkownika
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('role');

        return response()->json([
            'id' => $user->id,
            'imie' => $user->imie,
            'nazwisko' => $user->nazwisko,
            'email' => $user->email,
            'telefon' => $user->telefon,
            'rola' => $user->role->nazwa,
        ]);
    }
}

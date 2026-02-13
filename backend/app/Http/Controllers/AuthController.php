<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Mail\AccountActivation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class AuthController extends Controller
{

    public function register(RegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $login = $this->generateLogin($validated['first_name'], $validated['last_name']);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'login' => $login,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'] ?? null,
            'phone_number' => $validated['phone_number'],
            'password' => Hash::make($validated['password']),
            'role_id' => 1,
            'is_active' => empty($validated['email']),
            'activation_token' => !empty($validated['email']) ? Str::random(60) : null,
        ]);

        if (!empty($validated['email'])) {
            Mail::to($user->email)->send(new AccountActivation($user, $user->activation_token));
            return new JsonResponse([
                'message' => 'Konto utworzone pomyślnie. Sprawdź email aby aktywować konto.',
                'user' => $user,
                'login' => $login,
            ], 201);
        }

        return new JsonResponse([
            'message' => 'Konto utworzone pomyślnie. Twój login to: ' . $login,
            'user' => $user,
            'login' => $login,
        ], 201);
    }


    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $user = User::where('login', $validated['login'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return new JsonResponse([
                'error' => 'Nieprawidłowy login lub hasło',
            ], 401);
        }

        if (! $user->is_active) {
            return new JsonResponse([
                'error' => 'Konto nie jest aktywne. Sprawdź email.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return new JsonResponse([
            'message' => 'Zalogowano pomyślnie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'login' => $user->login,
                'role' => $user->role?->name,
            ],
        ]);
    }


    public function logout(Request $request): JsonResponse
    {

        $user = $request->user();
        if ($user) {
            $user->currentAccessToken()->delete();
        }

        return new JsonResponse(['message' => 'Wylogowano']);
    }


    public function me(Request $request): JsonResponse
    {

        $user = $request->user();
        $user->load('role');

        return new JsonResponse([
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'login' => $user->login,
            'phone_number' => $user->phone_number,
            'role' => $user->role?->name,
        ]);
    }


    public function activateAccount(string $token): JsonResponse
    {
        $user = User::where('activation_token', $token)->first();

        if (! $user) {
            return new JsonResponse(['message' => 'Nieprawidłowy token aktywacji'], 404);
        }

        $user->is_active = true;
        $user->activation_token = null;
        $user->email_verified_at = now();
        $user->save();

        return new JsonResponse(['message' => 'Konto zostało aktywowane. Możesz się zalogować.']);
    }


    private function generateLogin(string $firstName, string $lastName): string
    {
        $base = mb_strtolower(
            str_replace(
                ['ą', 'ć', 'ę', 'ł', 'ń', 'ó', 'ś', 'ź', 'ż', 'Ą', 'Ć', 'Ę', 'Ł', 'Ń', 'Ó', 'Ś', 'Ź', 'Ż'],
                ['a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z', 'a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z'],
                $firstName . '.' . $lastName
            )
        );

        $base = preg_replace('/[^a-z0-9.]/', '', $base);

        $login = $base;
        $counter = 1;
        while (User::where('login', $login)->exists()) {
            $login = $base . $counter;
            $counter++;
        }

        return $login;
    }
}

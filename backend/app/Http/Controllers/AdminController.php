<?php

namespace App\Http\Controllers;

use App\Models\GodzinaPracy;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Pobieranie listy uzytkownikow z filtrowaniem
     */
    public function getUzytkownicy(Request $request): JsonResponse
    {
        $query = User::with('role');

        if ($request->has('rola') && $request->rola !== '') {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('nazwa', $request->rola);
            });
        }

        if ($request->has('szukaj') && $request->szukaj !== '') {
            $szukaj = $request->szukaj;
            $query->where(function ($q) use ($szukaj) {
                $q->where('imie', 'like', "%{$szukaj}%")
                    ->orWhere('nazwisko', 'like', "%{$szukaj}%")
                    ->orWhere('email', 'like', "%{$szukaj}%");
            });
        }

        $uzytkownicy = $query->get();

        return response()->json($uzytkownicy);
    }

    /**
     * Pobieranie pojedynczego uzytkownika
     */
    public function getUzytkownik(int $id): JsonResponse
    {
        $user = User::with('role')->find($id);

        if (! $user) {
            return response()->json(['error' => 'Użytkownik nie znaleziony'], 404);
        }

        return response()->json($user);
    }

    /**
     * Dodawanie nowego uzytkownika
     */
    public function dodajUzytkownika(Request $request): JsonResponse
    {
        $request->validate([
            'imie' => 'required|string|max:50',
            'nazwisko' => 'required|string|max:50',
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => 'required|string|min:8',
            'telefon' => ['required', 'regex:/^[0-9]{9}$/'],
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->imie.' '.$request->nazwisko,
            'imie' => $request->imie,
            'nazwisko' => $request->nazwisko,
            'email' => $request->email,
            'telefon' => $request->telefon,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'is_active' => true,
        ]);

        $user->load('role');

        return response()->json([
            'message' => 'Użytkownik dodany',
            'user' => $user,
        ], 201);
    }

    /**
     * Edycja uzytkownika
     */
    public function edytujUzytkownika(int $id, Request $request): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['error' => 'Użytkownik nie znaleziony'], 404);
        }

        $rules = [
            'imie' => 'sometimes|string|max:50',
            'nazwisko' => 'sometimes|string|max:50',
            'email' => [
                'sometimes',
                'email',
                'unique:users,email,'.$id,
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'telefon' => ['sometimes', 'regex:/^[0-9]{9}$/'],
            'role_id' => 'sometimes|exists:roles,id',
        ];

        if ($request->has('password') && $request->password !== '') {
            $rules['password'] = 'string|min:8';
        }

        $request->validate($rules);

        if ($request->has('imie')) {
            $user->imie = $request->imie;
        }
        if ($request->has('nazwisko')) {
            $user->nazwisko = $request->nazwisko;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('telefon')) {
            $user->telefon = $request->telefon;
        }
        if ($request->has('role_id')) {
            $user->role_id = $request->role_id;
        }
        if ($request->has('is_active')) {
            $user->is_active = $request->is_active;
        }

        if ($request->has('password') && $request->password !== '') {
            $user->password = Hash::make($request->password);
        }

        $user->name = $user->imie.' '.$user->nazwisko;
        $user->save();
        $user->load('role');

        return response()->json([
            'message' => 'Użytkownik zaktualizowany',
            'user' => $user,
        ]);
    }

    /**
     * Usuwanie uzytkownika
     */
    public function usunUzytkownika(int $id, Request $request): JsonResponse
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['error' => 'Użytkownik nie znaleziony'], 404);
        }

        if ($user->id === $request->user()->id) {
            return response()->json(['error' => 'Nie możesz usunąć samego siebie'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Użytkownik usunięty']);
    }

    /**
     * Raporty miesieczne
     */
    public function getRaporty(Request $request): JsonResponse
    {
        $rok = $request->get('rok', date('Y'));
        $miesiac = $request->get('miesiac', date('m'));

        $godziny = GodzinaPracy::with(['pracownik', 'dodajacy'])
            ->whereYear('data_pracy', $rok)
            ->whereMonth('data_pracy', $miesiac)
            ->get()
            ->groupBy('user_id')
            ->map(function ($wpisy) {
                $pracownik = $wpisy->first()->pracownik;

                return [
                    'pracownik' => $pracownik->imie.' '.$pracownik->nazwisko,
                    'email' => $pracownik->email,
                    'suma_godzin' => $wpisy->sum('liczba_godzin'),
                    'dni_pracy' => $wpisy->count(),
                ];
            })
            ->values();

        $statystyki = [
            'liczba_uzytkownikow' => User::count(),
            'liczba_pracownikow' => User::whereHas('role', function ($q) {
                $q->where('nazwa', 'pracownik');
            })->count(),
            'suma_godzin_miesiac' => GodzinaPracy::whereYear('data_pracy', $rok)->whereMonth('data_pracy', $miesiac)->sum('liczba_godzin'),
        ];

        return response()->json([
            'godziny' => $godziny,
            'statystyki' => $statystyki,
            'rok' => $rok,
            'miesiac' => $miesiac,
        ]);
    }

    /**
     * Eksport danych do CSV
     */
    public function eksportujCSV(Request $request): Response
    {
        $rok = $request->get('rok', date('Y'));
        $miesiac = $request->get('miesiac', date('m'));

        $godziny = GodzinaPracy::with('pracownik')
            ->whereYear('data_pracy', $rok)
            ->whereMonth('data_pracy', $miesiac)
            ->orderBy('data_pracy')
            ->get();

        $csv = "Imie,Nazwisko,Data,Godziny\n";
        foreach ($godziny as $g) {
            $csv .= "{$g->pracownik->imie},{$g->pracownik->nazwisko},{$g->data_pracy->format('Y-m-d')},{$g->liczba_godzin}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=raport_{$rok}_{$miesiac}.csv",
        ]);
    }

    /**
     * Pobieranie listy rol
     */
    public function getRole(): JsonResponse
    {
        return response()->json(Role::all());
    }
}

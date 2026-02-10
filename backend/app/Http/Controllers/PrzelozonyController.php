<?php

namespace App\Http\Controllers;

use App\Http\Requests\DodajGodzinyRequest;
use App\Models\GodzinaPracy;
use App\Models\Komentarz;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrzelozonyController extends Controller
{
    /**
     * Dodawanie godzin pracy pracownikowi
     */
    public function dodajGodziny(DodajGodzinyRequest $request): JsonResponse
    {
        $pracownik = User::with('role')->find($request->user_id);

        if (! $pracownik || ! $pracownik->isPracownik()) {
            return response()->json(['error' => 'Wybrany użytkownik nie jest pracownikiem'], 400);
        }

        $istnieje = GodzinaPracy::where('user_id', $request->user_id)
            ->where('data_pracy', $request->data_pracy)
            ->exists();

        if ($istnieje) {
            return response()->json(['error' => 'Godziny dla tego pracownika w tym dniu już istnieją'], 400);
        }

        $godziny = GodzinaPracy::create([
            'user_id' => $request->user_id,
            'data_pracy' => $request->data_pracy,
            'liczba_godzin' => $request->liczba_godzin,
            'dodane_przez' => $request->user()->id,
        ]);

        $godziny->load(['pracownik', 'dodajacy']);

        return response()->json([
            'message' => 'Godziny dodane',
            'godziny' => $godziny,
        ], 201);
    }

    /**
     * Pobieranie listy pracownikow (zespol)
     */
    public function getZespol(): JsonResponse
    {
        $pracownicy = User::whereHas('role', function ($q) {
            $q->where('nazwa', 'pracownik');
        })->with('role')->get();

        return response()->json($pracownicy);
    }

    /**
     * Pobieranie godzin pracy konkretnego pracownika
     */
    public function getGodzinyPracownika(int $userId, Request $request): JsonResponse
    {
        $pracownik = User::find($userId);
        if (! $pracownik) {
            return response()->json(['error' => 'Pracownik nie znaleziony'], 404);
        }

        $query = GodzinaPracy::where('user_id', $userId)
            ->with(['komentarze.autor', 'dodajacy']);

        if ($request->has('rok') && $request->has('miesiac')) {
            $query->whereYear('data_pracy', $request->rok)
                ->whereMonth('data_pracy', $request->miesiac);
        }

        $godziny = $query->orderBy('data_pracy', 'desc')->get();

        $suma = $godziny->sum('liczba_godzin');

        return response()->json([
            'pracownik' => $pracownik->imie.' '.$pracownik->nazwisko,
            'godziny' => $godziny,
            'suma' => $suma,
        ]);
    }

    /**
     * Dodawanie komentarza do wpisu godzin
     */
    public function dodajKomentarz(Request $request): JsonResponse
    {
        $request->validate([
            'godzina_pracy_id' => 'required|exists:godziny_pracy,id',
            'tresc' => 'required|string|max:500',
        ]);

        $komentarz = Komentarz::create([
            'godzina_pracy_id' => $request->godzina_pracy_id,
            'user_id' => $request->user()->id,
            'tresc' => $request->tresc,
        ]);

        $komentarz->load('autor');

        return response()->json([
            'message' => 'Komentarz dodany',
            'komentarz' => $komentarz,
        ], 201);
    }

    /**
     * Pobieranie statystyk zespolu
     */
    public function getStatystyki(Request $request): JsonResponse
    {
        $rok = $request->get('rok', date('Y'));
        $miesiac = $request->get('miesiac', date('m'));

        $pracownicy = User::whereHas('role', function ($q) {
            $q->where('nazwa', 'pracownik');
        })
            ->withCount(['godzinyPracy' => function ($q) use ($rok, $miesiac) {
                $q->whereYear('data_pracy', $rok)->whereMonth('data_pracy', $miesiac);
            }])
            ->get()
            ->map(function ($p) use ($rok, $miesiac) {
                $suma = GodzinaPracy::where('user_id', $p->id)
                    ->whereYear('data_pracy', $rok)
                    ->whereMonth('data_pracy', $miesiac)
                    ->sum('liczba_godzin');

                return [
                    'id' => $p->id,
                    'imie' => $p->imie,
                    'nazwisko' => $p->nazwisko,
                    'dni_pracy' => $p->godziny_pracy_count,
                    'suma_godzin' => $suma,
                ];
            });

        return response()->json([
            'pracownicy' => $pracownicy,
            'rok' => $rok,
            'miesiac' => $miesiac,
        ]);
    }
}

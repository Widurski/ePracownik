<?php

namespace App\Http\Controllers;

use App\Models\GodzinaPracy;
use App\Models\Komentarz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PracownikController extends Controller
{
    /**
     * Pobieranie godzin pracy zalogowanego pracownika
     */
    public function getMojeGodziny(Request $request): JsonResponse
    {
        $godziny = GodzinaPracy::where('user_id', $request->user()->id)
            ->with(['komentarze.autor', 'dodajacy'])
            ->orderBy('data_pracy', 'desc')
            ->get();

        return response()->json($godziny);
    }

    /**
     * Pobieranie godzin pracy w danym miesiacu
     */
    public function getGodzinyMiesiac(int $rok, int $miesiac, Request $request): JsonResponse
    {
        $godziny = GodzinaPracy::where('user_id', $request->user()->id)
            ->whereYear('data_pracy', $rok)
            ->whereMonth('data_pracy', $miesiac)
            ->with(['komentarze.autor', 'dodajacy'])
            ->orderBy('data_pracy', 'desc')
            ->get();

        $suma = $godziny->sum('liczba_godzin');

        return response()->json([
            'godziny' => $godziny,
            'suma' => $suma,
            'rok' => $rok,
            'miesiac' => $miesiac,
        ]);
    }

    /**
     * Podsumowanie godzin w biezacym miesiacu
     */
    public function getPodsumowanie(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $rok = date('Y');
        $miesiac = date('m');

        $godzinyMiesiac = GodzinaPracy::where('user_id', $userId)
            ->whereYear('data_pracy', $rok)
            ->whereMonth('data_pracy', $miesiac)
            ->sum('liczba_godzin');

        $dniMiesiac = GodzinaPracy::where('user_id', $userId)
            ->whereYear('data_pracy', $rok)
            ->whereMonth('data_pracy', $miesiac)
            ->count();

        $ostatnieWpisy = GodzinaPracy::where('user_id', $userId)
            ->with('dodajacy')
            ->orderBy('data_pracy', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'godziny_miesiac' => $godzinyMiesiac,
            'dni_miesiac' => $dniMiesiac,
            'ostatnie_wpisy' => $ostatnieWpisy,
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

        $godzina = GodzinaPracy::find($request->godzina_pracy_id);

        if (! $godzina || $godzina->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Możesz komentować tylko swoje dni pracy'], 403);
        }

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
}

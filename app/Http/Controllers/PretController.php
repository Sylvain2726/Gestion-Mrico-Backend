<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pret;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class PretController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Client $client)
    {
        $prets = $client->prets;

        return response()->json([
            'message' => 'Prêts trouvés',
            'prets' => $prets,
        ], 200);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'date_echeant' => 'required|date',
                'montant_total' => 'required|numeric',
                'client_id' => 'required|exists:users,id',
            ]);

            $pret = Pret::create([
                'date_echeant' => $request->date_echeant,
                'montant_total' => $request->montant_total,
                'client_id' => $request->client_id,
            ]);

            return response()->json([
                'message' => 'Prêt créé avec succès',
                'pret' => $pret,
            ], 201);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'Erreur lors de la création du prêt',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pret $pret)
    {

        $pret = pret::find($pret->id);

        if (!$pret) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Prêt non trouvé',
                ], 404)
            );
        }



        return response()->json([
            'message' => 'Prêt trouvé',
            'pret' => $pret->load('client'),
        ], 200);
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pret $pret)
    {
        $request->validate([
            'date_echeant' => 'required|date',
            'montant_total' => 'required|numeric',
            //'client_id' => 'required|exists:clients,id',
        ]);

        $pret->update($request->all());

        return response()->json([
            'message' => 'Prêt mis à jour avec succès',
            'pret' => $pret,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pret $pret)
    {
        $pret->delete();

        return response()->json([
            'message' => 'Prêt supprimé avec succès',
        ], 200);
    }
}

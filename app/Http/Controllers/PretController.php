<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Pret;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class PretController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/prets-client/{client}",
     *     summary="Liste des prêts d'un client",
     *     description="Récupère la liste des prêts associés à un client spécifique.",
     *     tags={"Prêts"},
     *
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Prêts trouvés",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Prêts trouvés"),
     *             @OA\Property(property="prets", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/prets",
     *     summary="Créer un prêt",
     *     description="Crée un nouveau prêt pour un client.",
     *     tags={"Prêts"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"date_echeant", "montant_total", "client_id"},
     *
     *             @OA\Property(property="date_echeant", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="montant_total", type="number", format="float", example=1000.50),
     *             @OA\Property(property="client_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Prêt créé avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Prêt créé avec succès"),
     *             @OA\Property(property="pret", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erreur lors de la création du prêt",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Erreur lors de la création du prêt"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
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
            // throw $th;
            return response()->json([
                'message' => 'Erreur lors de la création du prêt',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/prets/{pret}",
     *     summary="Afficher un prêt",
     *     description="Récupère les informations d'un prêt spécifique.",
     *     tags={"Prêts"},
     *
     *     @OA\Parameter(
     *         name="pret",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Prêt trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Prêt trouvé"),
     *             @OA\Property(property="pret", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Prêt non trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Prêt non trouvé")
     *         )
     *     )
     * )
     */
    public function show(Pret $pret)
    {

        $pret = pret::find($pret->id);

        if (! $pret) {
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
     * @OA\Put(
     *     path="/api/prets/{pret}",
     *     summary="Mettre à jour un prêt",
     *     description="Met à jour les informations d'un prêt existant.",
     *     tags={"Prêts"},
     *
     *     @OA\Parameter(
     *         name="pret",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"date_echeant", "montant_total"},
     *
     *             @OA\Property(property="date_echeant", type="string", format="date", example="2023-12-31"),
     *             @OA\Property(property="montant_total", type="number", format="float", example=1500.75)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Prêt mis à jour avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Prêt mis à jour avec succès"),
     *             @OA\Property(property="pret", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Prêt non trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Prêt non trouvé")
     *         )
     *     )
     * )
     */
    public function update(Request $request, Pret $pret)
    {
        $request->validate([
            'date_echeant' => 'required|date',
            'montant_total' => 'required|numeric',
            // 'client_id' => 'required|exists:clients,id',
        ]);

        $pret->update($request->all());

        return response()->json([
            'message' => 'Prêt mis à jour avec succès',
            'pret' => $pret,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/prets/{pret}",
     *     summary="Supprimer un prêt",
     *     description="Supprime un prêt spécifique.",
     *     tags={"Prêts"},
     *
     *     @OA\Parameter(
     *         name="pret",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Prêt supprimé avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Prêt supprimé avec succès")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Prêt non trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Prêt non trouvé")
     *         )
     *     )
     * )
     */
    public function destroy(Pret $pret)
    {
        $pret->delete();

        return response()->json([
            'message' => 'Prêt supprimé avec succès',
        ], 200);
    }
}

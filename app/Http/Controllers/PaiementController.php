<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaiementRequest;
use App\Http\Requests\UpdatePaiementRequest;
use App\Models\Paiement;
use App\Models\Pret;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PaiementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/paiements",
     *     summary="Liste des paiements",
     *     description="Récupère la liste de tous les paiements.",
     *     tags={"Paiements"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $paiements = Paiement::with('pret.client')->get();

        return response()->json([
            'success' => true,
            'data' => $paiements,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/paiements",
     *     summary="Créer un paiement",
     *     description="Crée un nouveau paiement pour un prêt.",
     *     tags={"Paiements"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"montant_payer", "mode_paiement", "pret_id"},
     *
     *             @OA\Property(property="montant_payer", type="number", format="float", example=500.75),
     *             @OA\Property(property="mode_paiement", type="string", example="especes"),
     *             @OA\Property(property="pret_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Paiement créé avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Paiement créé avec succès."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(StorePaiementRequest $request): JsonResponse
    {
        $paiement = Paiement::create($request->validated());
        $paiement->load('pret.client');

        return response()->json([
            'success' => true,
            'message' => 'Paiement créé avec succès.',
            'data' => $paiement,
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/paiements/{id}",
     *     summary="Afficher un paiement",
     *     description="Récupère les informations d'un paiement spécifique.",
     *     tags={"Paiements"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Paiement trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Paiement non trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Paiement non trouvé.")
     *         )
     *     )
     * )
     */
    public function show(string $id): JsonResponse
    {
        $paiement = Paiement::with('pret.client')->find($id);

        if (! $paiement) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non trouvé.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'success' => true,
            'data' => $paiement,
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/paiements/{id}",
     *     summary="Mettre à jour un paiement",
     *     description="Met à jour les informations d'un paiement existant.",
     *     tags={"Paiements"},
     *
     *     @OA\Parameter(
     *         name="id",
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
     *
     *             @OA\Property(property="montant_payer", type="number", format="float", example=600.50),
     *             @OA\Property(property="mode_paiement", type="string", example="orange_money"),
     *             @OA\Property(property="pret_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Paiement mis à jour avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Paiement mis à jour avec succès."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Paiement non trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Paiement non trouvé.")
     *         )
     *     )
     * )
     */
    public function update(UpdatePaiementRequest $request, string $id): JsonResponse
    {
        $paiement = Paiement::find($id);

        if (! $paiement) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non trouvé.',
            ], Response::HTTP_NOT_FOUND);
        }

        $paiement->update($request->validated());
        $paiement->load('pret.client');

        return response()->json([
            'success' => true,
            'message' => 'Paiement mis à jour avec succès.',
            'data' => $paiement,
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/paiements/{id}",
     *     summary="Supprimer un paiement",
     *     description="Supprime un paiement spécifique.",
     *     tags={"Paiements"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Paiement supprimé avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Paiement supprimé avec succès.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Paiement non trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Paiement non trouvé.")
     *         )
     *     )
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $paiement = Paiement::find($id);

        if (! $paiement) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non trouvé.',
            ], Response::HTTP_NOT_FOUND);
        }

        $paiement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paiement supprimé avec succès.',
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/paiements-pret/{pret}",
     *     summary="Paiements d'un prêt",
     *     description="Récupère la liste des paiements associés à un prêt spécifique.",
     *     tags={"Paiements"},
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
     *         description="Paiements trouvés",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Prêt non trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Prêt non trouvé.")
     *         )
     *     )
     * )
     */
    public function getByPret(string $pretId): JsonResponse
    {
        $pret = Pret::find($pretId);

        if (! $pret) {
            return response()->json([
                'success' => false,
                'message' => 'Prêt non trouvé.',
            ], Response::HTTP_NOT_FOUND);
        }

        $paiements = $pret->paiements()->with('pret.client')->get();

        return response()->json([
            'success' => true,
            'data' => $paiements,
        ]);
    }
}

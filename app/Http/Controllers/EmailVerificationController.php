<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailVerificationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/email/verify/{id}/{hash}",
     *     summary="Vérifier l'email",
     *     description="Vérifie l'adresse email de l'utilisateur via le lien reçu par email.",
     *     tags={"Vérification Email"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Parameter(
     *         name="hash",
     *         in="path",
     *         required=true,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Email vérifié avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Email vérifié avec succès"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Lien de vérification invalide ou expiré",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Lien de vérification invalide ou expiré")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Utilisateur non trouvé")
     *         )
     *     )
     * )
     */
    public function verify(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, (string) sha1($user->getEmailForVerification()))) {
            return view('verification.error');
        }

        if ($user->hasVerifiedEmail()) {
            return view('verification.success');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return view('verification.success');
    }

    /**
     * @OA\Post(
     *     path="/api/email/resend",
     *     summary="Renvoyer l'email de vérification",
     *     description="Renvoyer l'email de vérification à l'utilisateur connecté.",
     *     tags={"Vérification Email"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Email de vérification renvoyé",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Email de vérification renvoyé")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Email déjà vérifié",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Email déjà vérifié")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Non authentifié")
     *         )
     *     )
     * )
     */
    public function resend(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email déjà vérifié',
            ], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email de vérification renvoyé',
        ], 200);
    }
}

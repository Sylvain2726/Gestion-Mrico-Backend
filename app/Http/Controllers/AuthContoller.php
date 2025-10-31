<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginFormRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthContoller extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Connexion utilisateur",
     *     description="Permet à un utilisateur de se connecter avec email et mot de passe.",
     *     tags={"Authentification"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Vous êtes connecté"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Email ou mot de passe incorrect",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Email ou mot de passe incorrect")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Une erreur est survenue"),
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function login(LoginFormRequest $request)
    {

        try {
            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $usr = User::find($user->id);

                // Vérifier si l'email est vérifié
                if (! $usr->hasVerifiedEmail()) {
                    Auth::logout();

                    return response()->json([
                        'message' => 'Veuillez vérifier votre email avant de vous connecter. Un email de vérification a été envoyé.',
                        'email_verification_required' => true,
                    ], 403);
                }

                $request->session()->regenerate();

                return response()->json([
                    'message' => 'Vous êtes connecté',
                    'user' => $user,
                ]);
            }

            return response()->json([
                'message' => 'Email ou mot de passe incorrect',
            ], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Une erreur est survenue',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Déconnexion utilisateur",
     *     description="Permet à un utilisateur de se déconnecter.",
     *     tags={"Authentification"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/update-password",
     *     summary="Mise à jour du mot de passe",
     *     description="Permet à un utilisateur connecté de mettre à jour son mot de passe.",
     *     tags={"Authentification"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *
     *             @OA\Property(property="current_password", type="string", format="password", example="oldpassword123"),
     *             @OA\Property(property="new_password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Mot de passe mis à jour avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Mot de passe mis à jour avec succès"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Mot de passe actuel incorrect",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Le mot de passe actuel est incorrect")
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
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! $user) {

            throw new HttpResponseException(
                response()->json([
                    'message' => 'Utilisateur non connecté',
                ], 404)
            );
        }

        $utilisateur = User::find($user->id);

        if (! $utilisateur) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'message' => 'Utilisateur non trouvé',
                ], 404)
            );
        }

        if (! Hash::check($request->current_password, $user->password)) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'message' => 'Le mot de passe actuel est incorrect',
                ], 401)
            );
        }

        $utilisateur->password = Hash::make($request->new_password);
        $utilisateur->save();

        return response()->json([
            'message' => 'Mot de passe mis à jour avec succès',
            'user' => $utilisateur,
        ]);
    }
}

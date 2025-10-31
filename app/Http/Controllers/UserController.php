<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateFormRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Liste des utilisateurs",
     *     description="Récupère la liste de tous les utilisateurs.",
     *     tags={"Utilisateurs"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Liste récupérée avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Users retrieved successfully"),
     *             @OA\Property(property="users", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $users = User::all();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'users' => $users,
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Créer un utilisateur",
     *     description="Crée un nouvel utilisateur.",
     *     tags={"Utilisateurs"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "tel"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="tel", type="string", example="1234567890")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur créé avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erreur serveur",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(property="errors", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'tel' => 'required|string|max:20',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tel' => $request->tel,
            ]);

            // Envoyer l'email de vérification
            $user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Inscription réussie ! Veuillez vérifier votre email pour activer votre compte.',
                'user' => $user,
                'email_verification_required' => true,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/users/{id}",
     *     summary="Mettre à jour un utilisateur",
     *     description="Met à jour les informations d'un utilisateur existant.",
     *     tags={"Utilisateurs"},
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
     *             required={"name", "email", "tel"},
     *
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="tel", type="string", example="1234567890")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Utilisateur mis à jour avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Utilisateur mis à jour avec succès"),
     *             @OA\Property(property="user", type="object")
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
    public function update(UserUpdateFormRequest $request, User $user)
    {
        // Check if user exists
        if (! $user) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Utilisateur non trouvé',
                ], 404)
            );
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'tel' => $request->tel,
        ]);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès',
            'user' => $user,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Afficher un utilisateur",
     *     description="Récupère les informations d'un utilisateur spécifique.",
     *     tags={"Utilisateurs"},
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
     *         description="Utilisateur récupéré avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Utilisateur récupéré avec succès"),
     *             @OA\Property(property="user", type="object")
     *         )
     *     )
     * )
     */
    public function show(User $user)
    {
        return response()->json([
            'message' => 'Utilisateur récupéré avec succès',
            'user' => $user,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Supprimer un utilisateur",
     *     description="Supprime un utilisateur spécifique.",
     *     tags={"Utilisateurs"},
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
     *         description="Utilisateur supprimé avec succès",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Utilisateur supprimé avec succès")
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
    public function destroy(User $user)
    {
        // Check if user exists
        if (! $user) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Utilisateur non trouvé',
                ], 404)
            );
        }

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès',
        ], 200);
    }
}

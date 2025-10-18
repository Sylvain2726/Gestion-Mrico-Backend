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
    public function login(LoginFormRequest $request)
    {

        try {


            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {

                $request->session()->regenerate();

                return response()->json([
                    'message' => 'Vous êtes connecté',
                    'user' => Auth::user(),
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

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!$user) {

            throw new HttpResponseException(
                response()->json([
                    'message' => 'Utilisateur non connecté',
                ], 404)
            );
        }

        $utilisateur = User::find($user->id);

        if (!$utilisateur) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'message' => 'Utilisateur non trouvé',
                ], 404)
            );
        }

        if (!Hash::check($request->current_password, $user->password)) {
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

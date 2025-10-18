<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Http\Requests\UserUpdateFormRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();

        return response()->json([
            'message' => 'Users retrieved successfully',
            'users' => $users,
        ], 200);
    }
    public function store(Request $request)
    {

        try {
            //code...
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tel' => $request->tel,
            ]);

            //Send email verification notification
            //$user->sendEmailVerificationNotification();



            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
            ], 201);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $th->getMessage(),
            ], 500);
        }
    }

    //Update user 
    public function update(UserUpdateFormRequest $request, User $user)
    {
        //Check if user exists
        if (!$user) {
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

    public function show(User $user)
    {
        return response()->json([
            'message' => 'Utilisateur récupéré avec succès',
            'user' => $user,
        ], 200);
    }

    public function destroy(User $user)
    {
        //Check if user exists
        if (!$user) {
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

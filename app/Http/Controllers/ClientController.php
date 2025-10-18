<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Compte;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = User::all();

        return response()->json([
            'clients' => $clients,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */    public function store(Request $request)
    {

        try {
            DB::beginTransaction();

            $client = Client::create([
                'name' => $request->name,
                'email' => $request->email,
                //'password' => Hash::make($request->password),
                'tel' => $request->tel,
            ]);

            $compte = $client->compte()->create([
                'num_compte' => $client->id,
            ]);

            $compte->update([
                'num_compte' => $compte->id . rand(1000, 9999),
            ]);

            $compte->save();

            $client->save();


            DB::commit();

            //Send email verification notification
            //$user->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'Registration successful',
                'client' => $client->load('compte'),
            ], 201);
        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return response()->json([
            'client' => $client,
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $newClient = $client->update([
            'name' => $request->name,
            'email' => $request->email,
            'tel' => $request->tel,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json([
            'message' => 'Client supprimé avec succès',
        ], 200);
    }
}

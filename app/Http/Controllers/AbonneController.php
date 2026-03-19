<?php

namespace App\Http\Controllers;

use App\Models\Abonne;
use Illuminate\Http\Request;

class AbonneController extends Controller
{
    /**
     * Liste des abonnés (Admin seulement)
     */
    public function index()
    {
        return response()->json(Abonne::latest()->get());
    }

    /**
     * Inscription à la newsletter (Public)
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:abonnes,email',
        ], [
            'email.unique' => 'Cet email est déjà inscrit à notre newsletter.',
        ]);

        $abonne = Abonne::create([
            'email' => $request->email
        ]);

        return response()->json([
            'message' => 'Merci ! Votre inscription à la newsletter a été prise en compte.',
            'data' => $abonne
        ], 201);
    }

    /**
     * Désinscription / Suppression d'un abonné (Admin)
     */
    public function destroy(Abonne $abonne)
    {
        $abonne->delete();

        return response()->json([
            'message' => 'Abonné supprimé avec succès.'
        ]);
    }
}

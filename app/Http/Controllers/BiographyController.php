<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use Illuminate\Http\Request;

class BiographyController extends Controller
{
    public function index()
    {
        return response()->json(Biography::with('user')->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string',
            'nationalite' => 'required|string',
            'parcours_scolaire' => 'required|string',
            'parcours_professionnel' => 'required|string',
            'parcours_politique' => 'required|string',
            'photo' => 'nullable|image|file',
        ]);

        $data = $request->except(['photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('biographies', 'public');
        }

        $data['auteur'] = auth()->id();

        $biography = Biography::create($data);

        return response()->json($biography, 201);
    }

    public function show(Biography $biography)
    {
        return response()->json($biography->load('user'));
    }

    public function update(Request $request, Biography $biography)
    {
        $request->validate([
            'nom' => 'sometimes|string',
            'prenom' => 'sometimes|string',
            'date_naissance' => 'sometimes|date',
            'lieu_naissance' => 'sometimes|string',
            'nationalite' => 'sometimes|string',
            'parcours_scolaire' => 'sometimes|string',
            'parcours_professionnel' => 'sometimes|string',
            'parcours_politique' => 'sometimes|string',
            'photo' => 'nullable|image|file',
        ]);

        $data = $request->except(['photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('biographies', 'public');
        }

        $biography->update($data);

        return response()->json($biography);
    }

    public function destroy(Biography $biography)
    {
        $biography->delete();

        return response()->json([
            'message' => 'Biographie supprimée avec succès'
        ]);
    }
}

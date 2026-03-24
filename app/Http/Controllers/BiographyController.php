<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class BiographyController extends Controller
{
    #[OA\Get(
        path: "/biography",
        summary: "Obtenir la biographie unique",
        tags: ["Biographie"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Détails de la biographie",
                content: new OA\JsonContent(ref: "#/components/schemas/Biography")
            ),
            new OA\Response(response: 404, description: "Biographie non trouvée")
        ]
    )]
    public function index()
    {
        $biography = Biography::with('user')->first();
        if (!$biography) {
            return response()->json(['message' => 'Biographie non trouvée'], 404);
        }
        return response()->json($biography);
    }

    #[OA\Post(
        path: "/biography",
        summary: "Créer ou mettre à jour la biographie unique",
        tags: ["Biographie"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["nom", "prenom"],
                    properties: [
                        new OA\Property(property: "nom", type: "string"),
                        new OA\Property(property: "prenom", type: "string"),
                        new OA\Property(property: "date_naissance", type: "string", format: "date"),
                        new OA\Property(property: "lieu_naissance", type: "string"),
                        new OA\Property(property: "nationalite", type: "string"),
                        new OA\Property(property: "parcours_scolaire", type: "string"),
                        new OA\Property(property: "parcours_professionnel", type: "string"),
                        new OA\Property(property: "parcours_politique", type: "string"),
                        new OA\Property(property: "photo", type: "string", format: "binary")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Biographie enregistrée avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Biography")
            )
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'date_naissance' => 'nullable|date',
            'lieu_naissance' => 'nullable|string',
            'nationalite' => 'nullable|string',
            'parcours_scolaire' => 'nullable|string',
            'parcours_professionnel' => 'nullable|string',
            'parcours_politique' => 'nullable|string',
            'photo' => 'nullable|image|file',
        ]);

        $data = $request->except(['photo']);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('biographies', 'public');
        }

        $data['auteur'] = auth()->id();

        // On utilise updateOrCreate pour n'avoir qu'un seul enregistrement (celui avec l'id 1)
        $biography = Biography::updateOrCreate(['id' => 1], $data);

        return response()->json($biography);
    }

    /**
     * Suppression de la biographie (RAZ du singleton)
     */
    #[OA\Delete(
        path: "/biography",
        summary: "Supprimer la biographie unique",
        tags: ["Biographie"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Biographie supprimée")
        ]
    )]
    public function destroy()
    {
        Biography::truncate(); // Supprime tout pour réinitialiser le singleton
        return response()->json(['message' => 'Biographie supprimée avec succès']);
    }
}

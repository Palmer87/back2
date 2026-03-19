<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class BiographyController extends Controller
{
    #[OA\Get(
        path: "/biographies",
        summary: "Liste toutes les biographies",
        tags: ["Biographies"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des biographies",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Biography"))
            )
        ]
    )]
    public function index()
    {
        return response()->json(Biography::with('user')->latest()->get());
    }

    #[OA\Post(
        path: "/biographies",
        summary: "Créer une nouvelle biographie",
        tags: ["Biographies"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["nom", "prenom", "date_naissance", "lieu_naissance", "nationalite", "parcours_scolaire", "parcours_professionnel", "parcours_politique"],
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
                response: 201,
                description: "Biographie créée avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Biography")
            )
        ]
    )]
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

    #[OA\Get(
        path: "/biographies/{biography}",
        summary: "Détails d'une biographie",
        tags: ["Biographies"],
        parameters: [
            new OA\Parameter(name: "biography", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Biographie trouvée", content: new OA\JsonContent(ref: "#/components/schemas/Biography")),
            new OA\Response(response: 404, description: "Biographie non trouvée")
        ]
    )]
    public function show(Biography $biography)
    {
        return response()->json($biography->load('user'));
    }

    #[OA\Put(
        path: "/biographies/{biography}",
        summary: "Mettre à jour une biographie",
        tags: ["Biographies"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "biography", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nom", type: "string"),
                    new OA\Property(property: "prenom", type: "string"),
                    new OA\Property(property: "date_naissance", type: "string", format: "date"),
                    new OA\Property(property: "lieu_naissance", type: "string"),
                    new OA\Property(property: "nationalite", type: "string"),
                    new OA\Property(property: "parcours_scolaire", type: "string"),
                    new OA\Property(property: "parcours_professionnel", type: "string"),
                    new OA\Property(property: "parcours_politique", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Biographie mise à jour", content: new OA\JsonContent(ref: "#/components/schemas/Biography"))
        ]
    )]
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

    #[OA\Delete(
        path: "/biographies/{biography}",
        summary: "Supprimer une biographie",
        tags: ["Biographies"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "biography", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Biographie supprimée")
        ]
    )]
    public function destroy(Biography $biography)
    {
        $biography->delete();

        return response()->json([
            'message' => 'Biographie supprimée avec succès'
        ]);
    }
}

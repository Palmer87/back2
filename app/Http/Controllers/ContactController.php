<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ContactController extends Controller
{
    #[OA\Get(
        path: "/contacts",
        summary: "Liste tous les messages de contact (Admin)",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des messages",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Contact"))
            )
        ]
    )]
    public function index()
    {
        return response()->json(\App\Models\Contact::latest()->get());
    }

    #[OA\Post(
        path: "/contact",
        summary: "Envoyer un message de contact (Public)",
        tags: ["Contacts"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nom", "email", "telephone", "sujet", "message"],
                properties: [
                    new OA\Property(property: "nom", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "telephone", type: "string"),
                    new OA\Property(property: "sujet", type: "string"),
                    new OA\Property(property: "message", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Message envoyé avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Contact")
                    ]
                )
            )
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telephone' => 'required|string|max:20',
            'sujet' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contact = \App\Models\Contact::create($validated);

        return response()->json([
            'message' => 'Message envoyé avec succès',
            'data' => $contact
        ], 201);
    }

    #[OA\Get(
        path: "/contacts/{id}",
        summary: "Détails d'un message de contact (Admin)",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Message trouvé", content: new OA\JsonContent(ref: "#/components/schemas/Contact")),
            new OA\Response(response: 404, description: "Message non trouvé")
        ]
    )]
    public function show($id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        return response()->json($contact);
    }

    #[OA\Delete(
        path: "/contacts/{id}",
        summary: "Supprimer un message de contact (Admin)",
        tags: ["Contacts"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Message supprimé")
        ]
    )]
    public function destroy($id)
    {
        $contact = \App\Models\Contact::findOrFail($id);
        $contact->delete();

        return response()->json([
            'message' => 'Message supprimé avec succès'
        ]);
    }
}

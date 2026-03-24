<?php

namespace App\Http\Controllers;

use App\Models\Abonne;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterMail;
use OpenApi\Attributes as OA;

class AbonneController extends Controller
{
    #[OA\Get(
        path: "/abonnes",
        summary: "Liste tous les abonnés (Admin)",
        tags: ["Abonnés"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des abonnés",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Abonne"))
            )
        ]
    )]
    public function index()
    {
        return response()->json(Abonne::latest()->paginate(20));
    }

    #[OA\Post(
        path: "/newsletter/subscribe",
        summary: "Inscription à la newsletter (Public)",
        tags: ["Abonnés"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "subscriber@example.com")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Inscription réussie",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Abonne")
                    ]
                )
            ),
            new OA\Response(response: 422, description: "Email déjà inscrit ou invalide")
        ]
    )]
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

    #[OA\Delete(
        path: "/abonnes/{abonne}",
        summary: "Supprimer un abonné (Admin)",
        tags: ["Abonnés"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "abonne", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Abonné supprimé")
        ]
    )]
    public function destroy(Abonne $abonne)
    {
        $abonne->delete();

        return response()->json([
            'message' => 'Abonné supprimé avec succès.'
        ]);
    }

   
}

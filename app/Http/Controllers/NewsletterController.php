<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use App\Models\Abonne;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterMail;
use OpenApi\Attributes as OA;

class NewsletterController extends Controller
{
    #[OA\Get(
        path: "/newsletters",
        summary: "Liste des newsletters (archives et projets)",
        tags: ["Newsletters"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des newsletters récupérée avec succès",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Newsletter"))
            )
        ]
    )]
    public function index()
    {
        return response()->json(Newsletter::orderBy('created_at', 'desc')->get());
    }

    #[OA\Post(
        path: "/newsletters",
        summary: "Enregistrer une nouvelle newsletter (projet ou archive)",
        tags: ["Newsletters"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["sujet", "contenu", "statut"],
                properties: [
                    new OA\Property(property: "sujet", type: "string", example: "Sujet de la newsletter"),
                    new OA\Property(property: "contenu", type: "string", example: "Contenu HTML ou texte..."),
                    new OA\Property(property: "statut", type: "string", enum: ["brouillon", "envoyé"], example: "brouillon"),
                    new OA\Property(property: "date_programmee", type: "string", format: "date-time", nullable: true),
                    new OA\Property(property: "date_envoi", type: "string", format: "date-time", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Newsletter créée avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Newsletter")
            )
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sujet' => 'required|string|max:255',
            'contenu' => 'required|string',
            'statut' => 'required|in:brouillon,envoyé',
            'date_programmee' => 'nullable|date',
            'date_envoi' => 'nullable|date',
        ]);

        $newsletter = Newsletter::create($validated);

        return response()->json([
            'message' => 'Newsletter créée avec succès',
            'data' => $newsletter
        ], 201);
    }

    #[OA\Get(
        path: "/newsletters/{newsletter}",
        summary: "Détails d'une newsletter",
        tags: ["Newsletters"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "newsletter",
                in: "path",
                description: "ID de la newsletter",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Newsletter trouvée",
                content: new OA\JsonContent(ref: "#/components/schemas/Newsletter")
            ),
            new OA\Response(response: 404, description: "Newsletter non trouvée")
        ]
    )]
    public function show(Newsletter $newsletter)
    {
        return response()->json($newsletter);
    }

    #[OA\Put(
        path: "/newsletters/{newsletter}",
        summary: "Mettre à jour une newsletter",
        tags: ["Newsletters"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "newsletter",
                in: "path",
                description: "ID de la newsletter",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "sujet", type: "string"),
                    new OA\Property(property: "contenu", type: "string"),
                    new OA\Property(property: "statut", type: "string", enum: ["brouillon", "envoyé"]),
                    new OA\Property(property: "date_programmee", type: "string", format: "date-time", nullable: true),
                    new OA\Property(property: "date_envoi", type: "string", format: "date-time", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Newsletter mise à jour avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Newsletter")
            )
        ]
    )]
    public function update(Request $request, Newsletter $newsletter)
    {
        $validated = $request->validate([
            'sujet' => 'sometimes|required|string|max:255',
            'contenu' => 'sometimes|required|string',
            'statut' => 'sometimes|required|in:brouillon,envoyé',
            'date_programmee' => 'nullable|date',
            'date_envoi' => 'nullable|date',
        ]);

        $newsletter->update($validated);

        return response()->json([
            'message' => 'Newsletter mise à jour avec succès',
            'data' => $newsletter
        ]);
    }

    #[OA\Delete(
        path: "/newsletters/{newsletter}",
        summary: "Supprimer une newsletter",
        tags: ["Newsletters"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "newsletter",
                in: "path",
                description: "ID de la newsletter",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Newsletter supprimée avec succès"
            )
        ]
    )]
    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();

        return response()->json([
            'message' => 'Newsletter supprimée avec succès'
        ]);
    }

    #[OA\Post(
        path: "/newsletters/{id}/send",
        summary: "Envoyer une newsletter à tous les abonnés",
        tags: ["Newsletters"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID de la newsletter",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["sujet", "message"],
                properties: [
                    new OA\Property(property: "sujet", type: "string"),
                    new OA\Property(property: "message", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Newsletter envoyée avec succès"
            )
        ]
    )]
    public function sendNewsletter(Request $request)
    {
        $request->validate([
            'sujet' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $abonnes = Abonne::all();

        foreach ($abonnes as $abonne) {
            Mail::to($abonne->email)->send(new NewsletterMail($request->sujet, $request->message));
        }

        return response()->json([
            'message' => 'Newsletter envoyée avec succès.'
        ]);
    }       
}

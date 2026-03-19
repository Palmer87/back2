<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class DocumentController extends Controller
{
    #[OA\Get(
        path: "/documents",
        summary: "Liste tous les documents",
        tags: ["Documents"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des documents",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Document"))
            )
        ]
    )]
    public function index()
    {
        return response()->json(Document::latest()->get());
    }

    #[OA\Post(
        path: "/documents",
        summary: "Ajouter un nouveau document",
        tags: ["Documents"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["title", "description", "file", "type"],
                    properties: [
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "description", type: "string"),
                        new OA\Property(property: "file", type: "string", format: "binary"),
                        new OA\Property(property: "type", type: "string", enum: ["Convocation", "Rapport", "PV", "Autre"])
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Document créé avec succès",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "data", ref: "#/components/schemas/Document")
                    ]
                )
            )
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,png|max:10240', // 10MB max
            'type' => 'required|in:Convocation,Rapport,PV,Autre',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $path = $request->file('file')->store('documents', 'public');

        $document = Document::create([
            'title' => $request->title,
            'description' => $request->description,
            'file' => $path,
            'type' => $request->type,
            'views' => 0,
            'downloads' => 0,
        ]);

        return response()->json([
            'message' => 'Document créé avec succès.',
            'data' => $document
        ], 201);
    }

    #[OA\Get(
        path: "/documents/{document}",
        summary: "Détails d'un document",
        tags: ["Documents"],
        parameters: [
            new OA\Parameter(name: "document", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Document trouvé", content: new OA\JsonContent(ref: "#/components/schemas/Document")),
            new OA\Response(response: 404, description: "Document non trouvé")
        ]
    )]
    public function show(Document $document)
    {
        $document->increment('views');
        return response()->json($document);
    }

    #[OA\Put(
        path: "/documents/{document}",
        summary: "Mettre à jour un document",
        tags: ["Documents"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "document", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "type", type: "string", enum: ["Convocation", "Rapport", "PV", "Autre"])
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Document mis à jour", content: new OA\JsonContent(ref: "#/components/schemas/Document"))
        ]
    )]
    public function update(Request $request, Document $document)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'file' => 'sometimes|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,png|max:10240',
            'type' => 'sometimes|required|in:Convocation,Rapport,PV,Autre',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['title', 'description', 'type']);

        if ($request->hasFile('file')) {
            // Delete old file
            Storage::disk('public')->delete($document->file);
            // Store new file
            $data['file'] = $request->file('file')->store('documents', 'public');
        }

        $document->update($data);

        return response()->json([
            'message' => 'Document mis à jour avec succès.',
            'data' => $document
        ]);
    }

    #[OA\Delete(
        path: "/documents/{document}",
        summary: "Supprimer un document",
        tags: ["Documents"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "document", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Document supprimé")
        ]
    )]
    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file);
        $document->delete();

        return response()->json([
            'message' => 'Document supprimé avec succès.'
        ]);
    }

    #[OA\Get(
        path: "/documents/{document}/download",
        summary: "Télécharger un document",
        tags: ["Documents"],
        parameters: [
            new OA\Parameter(name: "document", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Fichier prêt au téléchargement"),
            new OA\Response(response: 404, description: "Fichier introuvable")
        ]
    )]
    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file)) {
            return response()->json(['message' => 'Fichier introuvable.'], 404);
        }

        $document->increment('downloads');
        return Storage::disk('public')->download($document->file, $document->title);
    }
}

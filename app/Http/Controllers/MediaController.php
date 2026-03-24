<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class MediaController extends Controller
{
    #[OA\Get(
        path: "/media",
        summary: "Liste tous les médias",
        tags: ["Médias"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des médias",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Media"))
            )
        ]
    )]
    public function index()
    {
        return response()->json(Media::latest()->get());
    }

    #[OA\Post(
        path: "/media",
        summary: "Téléverser un nouveau média",
        tags: ["Médias"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["path"],
                    properties: [
                        new OA\Property(property: "path", type: "string", format: "binary", description: "Le fichier à téléverser"),
                        new OA\Property(property: "name", type: "string", description: "Nom facultatif pour le média")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Média créé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Media")
            )
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'path' => 'required|file|max:10240', // 10MB max
            'name' => 'nullable|string'
        ]);

        if ($request->hasFile('path')) {
            $file = $request->file('path');
            $path = $file->store('media', 'public');
            
            $media = Media::create([
                'name' => $request->name ?? $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
            ]);

            return response()->json($media, 201);
        }

        return response()->json(['message' => 'Aucun fichier fourni'], 400);
    }

    #[OA\Get(
        path: "/media/{id}",
        summary: "Détails d'un média",
        tags: ["Médias"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Média trouvé", content: new OA\JsonContent(ref: "#/components/schemas/Media")),
            new OA\Response(response: 404, description: "Média non trouvé")
        ]
    )]
    public function show(string $id)
    {
        $media = Media::findOrFail($id);
        return response()->json($media);
    }

    #[OA\Delete(
        path: "/media/{id}",
        summary: "Supprimer un média",
        tags: ["Médias"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Média supprimé")
        ]
    )]
    public function destroy(string $id)
    {
        $media = Media::findOrFail($id);
        
        // Supprimer le fichier physique
        Storage::disk('public')->delete($media->path);
        
        $media->delete();

        return response()->json(['message' => 'Média supprimé avec succès']);
    }
}

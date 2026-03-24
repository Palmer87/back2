<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class ProjectController extends Controller
{
    #[OA\Get(
        path: "/projects",
        summary: "Liste tous les projets",
        tags: ["Projets"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des projets",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Project"))
            )
        ]
    )]
    public function index()
    {
        return response()->json(Project::with('user')->latest()->get());
    }

    #[OA\Post(
        path: "/projects",
        summary: "Créer un nouveau projet",
        tags: ["Projets"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["title", "description"],
                    properties: [
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "description", type: "string"),
                        new OA\Property(property: "status", type: "string", enum: ["realise", "en_cours", "a_venir"], example: "en cours"),
                        new OA\Property(property: "image_path", type: "string", format: "binary"),
                        new OA\Property(property: "start_date", type: "string", format: "date"),
                        new OA\Property(property: "end_date", type: "string", format: "date")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Projet créé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Project")
            )
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'status' => 'required|in:realise,en_cours,a_venir',
            'image_path' => 'nullable|image|file',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data = $request->except(['image_path']);

        // Gestion de l'image
        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('projects', 'public');
        }

        // Slug automatique
        $data['slug'] = Str::slug($request->title);
        
        // Auteur (utilisateur connecté)
        $data['auteur'] = auth()->id();

        $project = Project::create($data);

        return response()->json($project, 201);
    }

    #[OA\Get(
        path: "/projects/{project}",
        summary: "Détails d'un projet",
        tags: ["Projets"],
        parameters: [
            new OA\Parameter(name: "project", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Projet trouvé", content: new OA\JsonContent(ref: "#/components/schemas/Project")),
            new OA\Response(response: 404, description: "Projet non trouvé")
        ]
    )]
    public function show(Project $project)
    {
        return response()->json($project->load('user'));
    }

    #[OA\Put(
        path: "/projects/{project}",
        summary: "Mettre à jour un projet",
        tags: ["Projets"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "project", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "title", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "status", type: "string", enum: ["realise", "en_cours", "a_venir"], example: "en cours"),
                    new OA\Property(property: "start_date", type: "string", format: "date"),
                    new OA\Property(property: "end_date", type: "string", format: "date")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Projet mis à jour", content: new OA\JsonContent(ref: "#/components/schemas/Project"))
        ]
    )]
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:realise,en_cours,a_venir',
            'image_path' => 'nullable|image|file',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
        ]);

        $data = $request->except(['image_path']);

        if ($request->hasFile('image_path')) {
            $data['image_path'] = $request->file('image_path')->store('projects', 'public');
        }

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->title);
        }

        $project->update($data);

        return response()->json($project);
    }

    #[OA\Delete(
        path: "/projects/{project}",
        summary: "Supprimer un projet",
        tags: ["Projets"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "project", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Projet supprimé")
        ]
    )]
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message' => 'Projet supprimé avec succès'
        ]);
    }
}

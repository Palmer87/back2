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
        return response()->json(Project::with('user')->latest()->paginate(15));
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
                        new OA\Property(property: "image_path", type: "string", format: "binary", description: "Image principale"),
                        new OA\Property(property: "images[]", type: "array", items: new OA\Items(type: "string", format: "binary"), description: "Images supplémentaires"),
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
            'images' => 'nullable|array',
            'images.*' => 'image|file',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $data = $request->except(['image_path', 'images']);

        // Gestion de l'image
      
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('projects'), $filename);
        
            $data['image_path'] = 'projects/' . $filename;
        }

        // Gestion des images multiples
        if ($request->hasFile('images')) {
            $uploadedImages = [];
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . Str::random(5) . '_' . $file->getClientOriginalName();
                $file->move(public_path('projects'), $filename);
                $uploadedImages[] = 'projects/' . $filename;
            }
            $data['images'] = $uploadedImages;
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
            'images' => 'nullable|array',
            'images.*' => 'image|file',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
        ]);

        $data = $request->except(['image_path', 'images']);

        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('projects'), $filename);
        
            $data['image_path'] = 'projects/' . $filename;
        }

        // Gestion des images multiples (ajout aux existantes)
        if ($request->hasFile('images')) {
            $existingImages = $project->images ?? [];
            $newImages = [];
            foreach ($request->file('images') as $file) {
                $filename = time() . '_' . Str::random(5) . '_' . $file->getClientOriginalName();
                $file->move(public_path('projects'), $filename);
                $newImages[] = 'projects/' . $filename;
            }
            $data['images'] = array_merge($existingImages, $newImages);
        }
        

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->title);
        }

        $project->update($data);

        return response()->json($project);
    }

    #[OA\Delete(
        path: "/projects/{project}/images",
        summary: "Supprimer une image spécifique d'un projet",
        tags: ["Projets"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "project", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["image_path"],
                properties: [
                    new OA\Property(property: "image_path", type: "string", example: "projects/image.png")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Image supprimée"),
            new OA\Response(response: 404, description: "Image non trouvée")
        ]
    )]
    public function deleteImage(Request $request, Project $project)
    {
        $request->validate([
            'image_path' => 'required|string'
        ]);

        $imagePath = $request->image_path;
        $images = $project->images ?? [];

        if (($key = array_search($imagePath, $images)) !== false) {
            unset($images[$key]);
            
            // Supprimer le fichier physique
            $fullPath = public_path($imagePath);
            if (file_exists($fullPath) && is_file($fullPath)) {
                unlink($fullPath);
            }

            $project->update(['images' => array_values($images)]);

            return response()->json([
                'message' => 'Image supprimée avec succès',
                'images' => $project->images
            ]);
        }

        return response()->json(['message' => 'Image non trouvée dans ce projet'], 404);
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
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Accès refusé. Réservé aux administrateurs.'], 403);
        }
        $project->delete();

        return response()->json([
            'message' => 'Projet supprimé avec succès'
        ]);
    }
}

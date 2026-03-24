<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class ArticleController extends Controller
{
    #[OA\Get(
        path: "/articles",
        summary: "Liste tous les articles",
        tags: ["Articles"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des articles",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Article"))
            )
        ]
    )]
    public function index()
    {
        try  {
            return response()->json(Article::latest()->get());
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération des articles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    #[OA\Post(
        path: "/articles",
        summary: "Créer un nouvel article",
        tags: ["Articles"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["title", "content", "typePart"],
                    properties: [
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "content", type: "string"),
                        new OA\Property(property: "image_url", type: "string", format: "binary"),
                        new OA\Property(property: "video_url", type: "string", format: "binary"),
                        new OA\Property(property: "typePart", type: "string", enum  : ['communique', 'discours', 'interview', 'autre']),
                        new OA\Property(property: "publier_le", type: "string", format: "date"),
                        new OA\Property(property: "retirer_le", type: "string", format: "date")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Article créé avec succès",
                content: new OA\JsonContent(ref: "#/components/schemas/Article")
            )
        ]
    )]
    public function store(Request $request)
    {
        $auteur = Auth::user();
        try {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'nullable|file|image',
            'video_url' => 'nullable|file',
            'typePart' => 'required|string',
            'publier_le' => 'nullable|date',
            'retirer_le' => 'nullable|date',
        ]);

        $data = $request->except(['image_url', 'video_url']);

        if (isset($data['typePart']) && in_array(mb_strtolower($data['typePart'], 'UTF-8'), ['communiqué', 'communique'])) {
            $data['typePart'] = 'communique';
        }

        // Gestion image
        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->store('images', 'public');
        }

        // Gestion vidéo
        if ($request->hasFile('video_url')) {
            $data['video_url'] = $request->file('video_url')->store('videos', 'public');
        }

        // Slug automatique
        $data['slug'] = Str::slug($request->title);
        $data['auteur'] = $auteur->id;  
        $article = Article::create($data);

        return response()->json($article, 201);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la création de l\'article',
            'error' => $e->getMessage()
        ], 500);
    }
        }

    #[OA\Get(
        path: "/articles/{article}",
        summary: "Détails d'un article",
        tags: ["Articles"],
        parameters: [
            new OA\Parameter(name: "article", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Article trouvé", content: new OA\JsonContent(ref: "#/components/schemas/Article")),
            new OA\Response(response: 404, description: "Article non trouvé")
        ]
    )]
    public function show(Article $article)
    {
        return response()->json($article);
    }

    #[OA\Put(
        path: "/articles/{article}",
        summary: "Mettre à jour un article",
        tags: ["Articles"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "article", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "title", type: "string"),
                        new OA\Property(property: "content", type: "string"),
                        new OA\Property(property: "typePart", type: "string"),
                        new OA\Property(property: "image_url", type: "string", format: "binary"),
                        new OA\Property(property: "video_url", type: "string", format: "binary"),
                        new OA\Property(property: "publier_le", type: "string", format: "date"),
                        new OA\Property(property: "retirer_le", type: "string", format: "date")
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Article mis à jour", content: new OA\JsonContent(ref: "#/components/schemas/Article"))
        ]
    )]
    public function update(Request $request, Article $article)
    {
        $data = $request->all();

        if (isset($data['typePart']) && in_array(mb_strtolower($data['typePart'], 'UTF-8'), ['communiqué', 'communique'])) {
            $data['typePart'] = 'communique';
        }

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->store('images', 'public');
        }

        if ($request->hasFile('video_url')) {
            $data['video_url'] = $request->file('video_url')->store('videos', 'public');
        }

        $article->update($data);

        return response()->json($article);
    }

    #[OA\Delete(
        path: "/articles/{article}",
        summary: "Supprimer un article",
        tags: ["Articles"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(name: "article", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Article supprimé")
        ]
    )]
    public function destroy(Article $article)
    {
        try {
            $article->delete();
            return response()->json([
            'message' => 'Article supprimé avec succès'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Erreur lors de la suppression de l\'article',
            'error' => $e->getMessage()
        ], 500);
    }
    }
    // publier un article
#[OA\Put(
    path: "/articles/{article}/publish",
    summary: "Publier un article",
    tags: ["Articles"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(name: "article", in: "path", required: true, schema: new OA\Schema(type: "integer"))
    ],
    responses: [
        new OA\Response(response: 200, description: "Article publié", content: new OA\JsonContent(ref: "#/components/schemas/Article"))
    ]
)]
public function publish(Article $article)
{
    $article->update([
        'publier' => true,
        'publier_le' => now(),
        'retirer_le' => now()->addDays(30)
    ]);

    return response()->json($article);
}
// retirer un article
#[OA\Put(
    path: "/articles/{article}/unpublish",
    summary: "Retirer un article",
    tags: ["Articles"],
    security: [["bearerAuth" => []]],
    parameters: [
        new OA\Parameter(name: "article", in: "path", required: true, schema: new OA\Schema(type: "integer"))
    ],
    responses: [
        new OA\Response(response: 200, description: "Article retiré", content: new OA\JsonContent(ref: "#/components/schemas/Article"))
    ]
)]
public function unpublish(Article $article)
{
    $article->update([
        'publier' => false,
        'retirer_le' => now()
    ]);

    return response()->json($article);
}   

//liste des articles publier
#[OA\Get(
    path: "/articles/published",
    summary: "Liste tous les articles publiés",
    tags: ["Articles"],
    responses: [
        new OA\Response(
            response: 200,
            description: "Liste des articles publiés",
            content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Article"))
        )
    ]
)]
public function published()
{
    return response()->json(Article::where('publier', true)->get());
}   

}
<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        return response()->json(Article::latest()->get());
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image_url' => 'nullable|file|image',
            'video_url' => 'nullable|file',
            'typePart' => 'required|string',
            'publier' => 'required|boolean',
            'publier_le' => 'nullable|date',
            'retirer_le' => 'nullable|date',
        ]);

        $data = $request->except(['image_url', 'video_url']);

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

        $article = Article::create($data);

        return response()->json($article, 201);
    }

    public function show(Article $article)
    {
        return response()->json($article);
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->all();

        if ($request->hasFile('image_url')) {
            $data['image_url'] = $request->file('image_url')->store('images', 'public');
        }

        if ($request->hasFile('video_url')) {
            $data['video_url'] = $request->file('video_url')->store('videos', 'public');
        }

        $article->update($data);

        return response()->json($article);
    }

    public function destroy(Article $article)
    {
        $article->delete();

        return response()->json([
            'message' => 'Article supprimé avec succès'
        ]);
    }
}
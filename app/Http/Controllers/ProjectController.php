<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(Project::with('user')->latest()->get());
    }

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

    public function show(Project $project)
    {
        return response()->json($project->load('user'));
    }

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

    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'message' => 'Projet supprimé avec succès'
        ]);
    }
}

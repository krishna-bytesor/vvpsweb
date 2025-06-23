<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /**
     * List all projects.
     */
    public function index()
    {
        $projects = Project::all();

        // Add full URLs for images
        foreach ($projects as $project) {
            $project->images = array_map(fn($path) => Storage::disk('s3')->url($path), $project->images ?? []);
        }

        return response()->json(['data' => $projects], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Store a new Project with multiple images/PDFs.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'images'    => 'nullable',
            'images.*'  => 'file|mimes:jpeg,png,jpg,gif,webp,pdf,txt|max:5120',
        ]);

        $paths = [];
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach ($files as $file) {
                $paths[] = $file->store('gallery/images', 's3');
            }
        }

        $project = Project::create([
            'title'  => $validated['title'],
            'images' => count($paths) ? $paths : null,
        ]);

        $fullUrls = array_map(fn($path) => Storage::disk('s3')->url($path), $paths);

        return response()->json([
            'message' => 'Project created',
            'data'    => [
                'id'     => $project->id,
                'title'  => $project->title,
                'images' => $fullUrls,
            ],
        ], 201, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Show a specific project.
     */
    public function show(Project $project)
    {
        $project->images = array_map(fn($path) => Storage::disk('s3')->url($path), $project->images ?? []);

        return response()->json(['data' => $project], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Update a project's title and/or images/PDFs.
     */
    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $data = $request->validate([
            'title'     => 'sometimes|string|max:255',
            'images.*'  => 'file|mimes:jpeg,png,jpg,gif,webp,pdf,txt|max:5120',
        ]);

        $paths = $project->images ?? [];

        if ($request->hasFile('images')) {
            // Delete old files
            foreach ($paths as $old) {
                Storage::disk('s3')->delete($old);
            }

            // Store new files
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('gallery/images', 's3');
            }

            $data['images'] = $paths;
        }

        $project->update($data);

        $fullUrls = array_map(fn($path) => Storage::disk('s3')->url($path), $paths);

        return response()->json([
            'message' => 'Project updated',
            'data'    => [
                'id'     => $project->id,
                'title'  => $project->title,
                'images' => $fullUrls,
            ],
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * Delete a project and its images.
     */
    public function destroy($id)
    {
        $project = Project::findOrFail($id);

        foreach ($project->images ?? [] as $path) {
            Storage::disk('s3')->delete($path);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.'
        ], Response::HTTP_OK);
    }
}

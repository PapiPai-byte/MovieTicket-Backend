<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMovieRequest;
use App\Http\Requests\Admin\UpdateMovieRequest;
use App\Http\Resources\V1\MovieResource;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MovieAdminController extends Controller
{
    /**
     * List all movies for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $movies = Movie::orderByDesc('id')->get();

        return $this->successResponse(
            MovieResource::collection($movies),
            'Admin movies retrieved.'
        );
    }

    /**
     * Store a newly created movie.
     */
    public function store(StoreMovieRequest $request): JsonResponse
    {
        $data = $request->validated();

        $genres = $data['genre'] ?? [];
        if (is_string($genres)) {
            $genres = array_map('trim', explode(',', $genres));
        }

        $movie = Movie::create([
            'title' => $data['title'],
            'slug' => ! empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['title']),
            'synopsis' => $data['synopsis'] ?? null,
            'genre' => $genres,
            'duration_minutes' => (int) ($data['duration_minutes'] ?? $data['duration'] ?? 120),
            'rating' => (float) ($data['rating'] ?? 0.0),
            'poster_url' => $data['poster_url'] ?? $data['poster'] ?? null,
            'trailer_url' => $data['trailer_url'] ?? $data['trailer'] ?? null,
            'release_date' => $data['release_date'] ?? $data['releaseDate'] ?? null,
            'status' => $data['status'] ?? 'now_showing',
        ]);

        return $this->successResponse(
            new MovieResource($movie),
            'Movie created successfully.',
            201
        );
    }

    /**
     * Display a movie.
     */
    public function show(int $id): JsonResponse
    {
        $movie = Movie::findOrFail($id);

        return $this->successResponse(
            new MovieResource($movie),
            'Movie details retrieved.'
        );
    }

    /**
     * Update an existing movie.
     */
    public function update(UpdateMovieRequest $request, int $id): JsonResponse
    {
        $movie = Movie::findOrFail($id);
        $data = $request->validated();

        if (isset($data['genre'])) {
            $genres = $data['genre'];
            if (is_string($genres)) {
                $genres = array_map('trim', explode(',', $genres));
            }
            $data['genre'] = $genres;
        }

        if (isset($data['duration'])) {
            $data['duration_minutes'] = (int) $data['duration'];
        }
        if (isset($data['poster'])) {
            $data['poster_url'] = $data['poster'];
        }
        if (isset($data['trailer'])) {
            $data['trailer_url'] = $data['trailer'];
        }
        if (isset($data['releaseDate'])) {
            $data['release_date'] = $data['releaseDate'];
        }

        $movie->update($data);

        return $this->successResponse(
            new MovieResource($movie->fresh()),
            'Movie updated successfully.'
        );
    }

    /**
     * Delete a movie.
     */
    public function destroy(int $id): JsonResponse
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return $this->successResponse(null, 'Movie deleted successfully.');
    }
}


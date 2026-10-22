<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MovieResource;
use App\Http\Resources\V1\ShowtimeResource;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    /**
     * List movies with search, filter, and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = max(1, min((int) $request->input('per_page', 8), 50));

        $query = Movie::query()
            ->search($request->input('search'))
            ->withStatus($request->input('status'))
            ->withGenre($request->input('genre'))
            ->orderBy('id', 'asc');

        $paginator = $query->paginate($perPage);

        return $this->successResponse([
            'data' => MovieResource::collection($paginator->items()),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ], 'Movies retrieved successfully.');
    }

    /**
     * Get a specific movie by slug or numeric ID.
     */
    public function show(string $movie): JsonResponse
    {
        $movieModel = is_numeric($movie)
            ? Movie::findOrFail($movie)
            : Movie::where('slug', $movie)->firstOrFail();

        return $this->successResponse(
            new MovieResource($movieModel),
            'Movie details retrieved.'
        );
    }

    /**
     * Get all showtimes for a movie by slug or ID.
     */
    public function showtimes(string $movie): JsonResponse
    {
        $movieModel = is_numeric($movie)
            ? Movie::findOrFail($movie)
            : Movie::where('slug', $movie)->firstOrFail();

        $showtimes = $movieModel->showtimes()
            ->with(['screen.cinema'])
            ->where('starts_at', '>=', now())
            ->where('status', 'active')
            ->orderBy('starts_at', 'asc')
            ->get();

        return $this->successResponse(
            ShowtimeResource::collection($showtimes),
            'Showtimes retrieved successfully.'
        );
    }
}


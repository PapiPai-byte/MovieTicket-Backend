<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShowtimeRequest;
use App\Http\Requests\Admin\UpdateShowtimeRequest;
use App\Http\Resources\V1\ShowtimeResource;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowtimeAdminController extends Controller
{
    /**
     * List all showtimes for administration.
     */
    public function index(Request $request): JsonResponse
    {
        $showtimes = Showtime::with(['movie', 'screen.cinema'])
            ->orderByDesc('starts_at')
            ->get();

        return $this->successResponse(
            ShowtimeResource::collection($showtimes),
            'Showtimes retrieved successfully.'
        );
    }

    /**
     * Store a new showtime.
     */
    public function store(StoreShowtimeRequest $request): JsonResponse
    {
        $data = $request->validated();

        $movieId = $data['movie_id'] ?? null;
        if (! $movieId && ! empty($data['movie'])) {
            $movie = is_numeric($data['movie'])
                ? Movie::find($data['movie'])
                : Movie::where('title', $data['movie'])->first();
            $movieId = $movie?->id;
        }

        $screenId = $data['screen_id'] ?? null;
        if (! $screenId && ! empty($data['screen'])) {
            $screen = is_numeric($data['screen'])
                ? Screen::find($data['screen'])
                : Screen::where('name', $data['screen'])->first();
            $screenId = $screen?->id;
        }

        if (! $movieId) {
            $movieId = Movie::first()?->id ?? 1;
        }
        if (! $screenId) {
            $screenId = Screen::first()?->id ?? 1;
        }

        $startsAt = $data['starts_at'] ?? $data['startsAt'];

        $showtime = Showtime::create([
            'movie_id' => $movieId,
            'screen_id' => $screenId,
            'starts_at' => $startsAt,
            'price' => (float) $data['price'],
            'status' => $data['status'] ?? 'active',
        ]);

        return $this->successResponse(
            new ShowtimeResource($showtime->load(['movie', 'screen.cinema'])),
            'Showtime created successfully.',
            201
        );
    }

    /**
     * Show a single showtime.
     */
    public function show(int $id): JsonResponse
    {
        $showtime = Showtime::with(['movie', 'screen.cinema'])->findOrFail($id);

        return $this->successResponse(
            new ShowtimeResource($showtime),
            'Showtime details retrieved.'
        );
    }

    /**
     * Update an existing showtime.
     */
    public function update(UpdateShowtimeRequest $request, int $id): JsonResponse
    {
        $showtime = Showtime::findOrFail($id);
        $data = $request->validated();

        if (isset($data['startsAt'])) {
            $data['starts_at'] = $data['startsAt'];
        }

        $showtime->update($data);

        return $this->successResponse(
            new ShowtimeResource($showtime->fresh(['movie', 'screen.cinema'])),
            'Showtime updated successfully.'
        );
    }

    /**
     * Delete a showtime.
     */
    public function destroy(int $id): JsonResponse
    {
        $showtime = Showtime::findOrFail($id);
        $showtime->delete();

        return $this->successResponse(null, 'Showtime deleted successfully.');
    }
}


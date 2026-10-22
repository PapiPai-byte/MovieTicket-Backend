<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCinemaRequest;
use App\Http\Requests\Admin\UpdateCinemaRequest;
use App\Http\Resources\V1\CinemaResource;
use App\Models\Cinema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CinemaAdminController extends Controller
{
    /**
     * List all cinemas.
     */
    public function index(Request $request): JsonResponse
    {
        $cinemas = Cinema::withCount('screens')->orderBy('name')->get();

        return $this->successResponse(
            CinemaResource::collection($cinemas),
            'Cinemas retrieved successfully.'
        );
    }

    /**
     * Store a new cinema.
     */
    public function store(StoreCinemaRequest $request): JsonResponse
    {
        $data = $request->validated();

        $location = $data['location'] ?? trim(($data['address'] ?? '').' '.($data['city'] ?? ''));

        $cinema = Cinema::create([
            'name' => $data['name'],
            'location' => ! empty($location) ? $location : 'Main Plaza',
            'address' => $data['address'] ?? $location,
            'city' => $data['city'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        return $this->successResponse(
            new CinemaResource($cinema),
            'Cinema created successfully.',
            201
        );
    }

    /**
     * Show a single cinema.
     */
    public function show(int $id): JsonResponse
    {
        $cinema = Cinema::withCount('screens')->findOrFail($id);

        return $this->successResponse(
            new CinemaResource($cinema),
            'Cinema details retrieved.'
        );
    }

    /**
     * Update an existing cinema.
     */
    public function update(UpdateCinemaRequest $request, int $id): JsonResponse
    {
        $cinema = Cinema::findOrFail($id);
        $data = $request->validated();

        if (empty($data['location']) && (! empty($data['address']) || ! empty($data['city']))) {
            $data['location'] = trim(($data['address'] ?? '').' '.($data['city'] ?? ''));
        }

        $cinema->update($data);

        return $this->successResponse(
            new CinemaResource($cinema->fresh()),
            'Cinema updated successfully.'
        );
    }

    /**
     * Delete a cinema.
     */
    public function destroy(int $id): JsonResponse
    {
        $cinema = Cinema::findOrFail($id);
        $cinema->delete();

        return $this->successResponse(null, 'Cinema deleted successfully.');
    }
}


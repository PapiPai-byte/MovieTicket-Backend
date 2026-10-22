<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScreenRequest;
use App\Http\Requests\Admin\UpdateScreenRequest;
use App\Http\Resources\V1\ScreenResource;
use App\Models\Cinema;
use App\Models\Screen;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScreenAdminController extends Controller
{
    /**
     * List all screens.
     */
    public function index(Request $request): JsonResponse
    {
        $screens = Screen::with('cinema')->orderBy('cinema_id')->orderBy('name')->get();

        return $this->successResponse(
            ScreenResource::collection($screens),
            'Screens retrieved successfully.'
        );
    }

    /**
     * Store a new screen and automatically initialize its seats.
     */
    public function store(StoreScreenRequest $request): JsonResponse
    {
        $data = $request->validated();

        $cinemaId = $data['cinema_id'] ?? null;
        if (! $cinemaId && ! empty($data['cinema'])) {
            $cinema = is_numeric($data['cinema'])
                ? Cinema::find($data['cinema'])
                : Cinema::where('name', $data['cinema'])->first();

            $cinemaId = $cinema?->id;
        }

        if (! $cinemaId) {
            $cinemaId = Cinema::firstOrCreate(
                ['name' => 'Downtown Cineplex'],
                ['location' => 'Main Avenue, Suite 100', 'city' => 'Metropolis']
            )->id;
        }

        $screen = Screen::create([
            'cinema_id' => $cinemaId,
            'name' => $data['name'],
            'total_rows' => (int) ($data['total_rows'] ?? 8),
            'seats_per_row' => (int) ($data['seats_per_row'] ?? 10),
            'type' => $data['type'] ?? 'standard',
            'status' => $data['status'] ?? 'active',
        ]);

        // Auto-generate seats for this new screen
        $screen->generateSeats();

        return $this->successResponse(
            new ScreenResource($screen->load('cinema')),
            'Screen created successfully with generated seats.',
            201
        );
    }

    /**
     * Show a screen.
     */
    public function show(int $id): JsonResponse
    {
        $screen = Screen::with(['cinema', 'seats'])->findOrFail($id);

        return $this->successResponse(
            new ScreenResource($screen),
            'Screen details retrieved.'
        );
    }

    /**
     * Update an existing screen.
     */
    public function update(UpdateScreenRequest $request, int $id): JsonResponse
    {
        $screen = Screen::findOrFail($id);
        $data = $request->validated();

        $screen->update($data);

        return $this->successResponse(
            new ScreenResource($screen->fresh(['cinema'])),
            'Screen updated successfully.'
        );
    }

    /**
     * Delete a screen.
     */
    public function destroy(int $id): JsonResponse
    {
        $screen = Screen::findOrFail($id);
        $screen->delete();

        return $this->successResponse(null, 'Screen deleted successfully.');
    }
}


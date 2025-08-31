<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    use ImageUploadTrait;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $orderBy = $request->input('order_by', 'created_at');
            $direction = $request->input('direction', 'desc');
            $perPage = (int) $request->input('per_page', 10);

            $query = Slider::query();
            // Search by title, type, or starting_price
            if ($request->filled('search')) {
                $search = request()->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('type', 'LIKE', "%{$search}%")
                        ->orWhere('starting_price', 'LIKE', "%{$search}%");
                });
            }

            // ✅ Apply dynamic order
            $allowedSorts = ['created_at', 'serial', 'title', 'type', 'starting_price'];
            if (in_array($orderBy, $allowedSorts)) {
                $query->orderBy($orderBy, $direction);
            } else {
                // Fallback
                $query->orderBy('created_at', 'desc');
                $query->orderBy('serial', 'asc');
            }
            // Paginate the results (10 per page)
            $sliders = $query->paginate($perPage);
            return $this->responseSuccess($sliders, "Sliders retrieved successfully.");
        } catch (\Exception $th) {
            Log::error('Error fetching sliders: ' . $th->getMessage());
            return $this->responseError('Failed to fetch sliders. Please try again later.', [
                "server" => $th->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'banner' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|string',
            'title' => 'required|string',
            'starting_price' => 'required|string',
            'btn_url' => 'required|string',
            'serial' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->responseValidationError($validator->errors()->toArray(), "Validation failed!");
        }

        try {
            // Handle Image Upload
            $banner = $request->file('banner');
            $bannerName = time() . '.' . $banner->getClientOriginalExtension();
            $bannerPath = $this->uploadImage($banner, $bannerName, 'cloudinary', 'sliders');

            // Store the data in the database
            $slider = Slider::create([
                'banner' => $bannerPath,
                'type' => $request->type,
                'title' => $request->title,
                'starting_price' => $request->starting_price,
                'btn_url' => $request->btn_url,
                'serial' => $request->serial,
                'status' => $request->status,
            ]);
            return $this->responseCreated(['id' => $slider->id], "Slider created successfully!");
        } catch (\Exception $e) {
            Log::error('Error creating slider: ' . $e->getMessage());
            return $this->responseError('Failed to create slider. Please try again later.', [
                "server" => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

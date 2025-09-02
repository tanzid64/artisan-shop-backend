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
            $search = $request->input('search', '');
            $perPage = $request->input('per_page', 10);
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDir = $request->input('sort_dir', 'desc');
            $status = $request->input('status', 'all');

            $query = Slider::query()->select('id', 'type', 'title', 'starting_price', 'btn_url', 'serial', 'status', 'created_at');

            // Apply search filter if provided
            if (!empty($search)) {
                $query->where(
                    function ($q) use ($search) {
                        $q->where('type', 'like', "%$search%")
                            ->orWhere('title', 'like', "%$search%")
                            ->orWhere('starting_price', 'like', "%$search%");
                    }
                );
            }

            // Apply status filter
            if ($status !== 'all') {
                if ($status === 'active') {
                    $query->where('status', 1);
                } elseif ($status === 'inactive') {
                    $query->where('status', false);
                }
            }

            // Apply sorting
            $query->orderBy($sortBy, $sortDir);

            // Apply Pagination
            $sliders = $query->paginate($perPage);

            if ($sliders->isEmpty()) {
                return $this->responseNotFound('No sliders found.');
            }
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
            $bannerUrl = $this->getImageUrl($bannerPath, 'cloudinary');

            // Store the data in the database
            $slider = Slider::create([
                'banner' => $bannerPath,
                'type' => $request->type,
                'title' => $request->title,
                'starting_price' => $request->starting_price,
                'banner_url' => $bannerUrl,
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

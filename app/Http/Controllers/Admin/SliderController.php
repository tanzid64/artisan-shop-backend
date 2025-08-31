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
    public function index()
    {
        try {
            $sliders = Slider::paginate(10);
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

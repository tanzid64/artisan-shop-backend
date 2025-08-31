<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        } catch (\Throwable $th) {
            //throw $th;
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

<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Assets;
use App\Models\Anchor; // Import the Anchor model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Storage;

class SiteController extends Controller
{
    // Show the form to create a new site
    public function create()
    {
        return view('sites.create');
    }

   public function store(Request $request)
   {
       // Ensure the user is authenticated
       if (!Auth::check()) {
           return response()->json(['error' => 'Unauthorized'], 401);
       }

       // Get the logged-in user's email
       $userEmail = Auth::user()->email;

       // Validate the incoming request
       $request->validate([
           'name' => 'required|unique:sites,name',
           'description' => 'required|string',
           'anchors' => 'required|array|min:3',  // Ensuring at least 3 anchors
           'assets' => 'required|array|min:1',  // Ensuring at least 1 asset
           'assets.*.device_uid' => 'required|string|unique:assets,device_uid',
           'assets.*.device_icon' => 'required|string',
           'assets.*.device_name' => 'required|string',
           'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
       ]);

       // Store the image in the 'public/images' folder
       $imagePath = $request->file('image')->store('images', 'public');

       // Create new site
       $site = Site::create([
           'email' => $userEmail,
           'name' => $request->name,
           'description' => $request->description,
           'image_url' => asset('storage/' . $imagePath),
       ]);

       // Store anchors
       foreach ($request->anchors as $anchorData) {
           $anchorData['site_id'] = $site->id; // Associate anchor with the site
           Anchor::create($anchorData); // Create anchor for the site
       }

       // Store assets
       foreach ($request->assets as $assetData) {
           $assetData['site_id'] = $site->id; // Associate asset with the site
           Assets::create($assetData); // Create asset for the site
       }

       return redirect()->route('sites.index')->with('success', 'Site created successfully!');
   }

    // Get all sites by email
    public function index()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userEmail = Auth::user()->email;
        $sites = Site::where('email', $userEmail)->get(['id', 'name', 'description', 'image_url']);

        return view('sites.index')->with('sites', $sites);
    }

    // Show the form to edit an existing site
    public function edit($id)
    {
        $site = Site::with(['anchors', 'assets'])->findOrFail($id);
        return view('sites.edit', compact('site'));
    }

    public function update(Request $request, $id)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the incoming request
        $request->validate([
            'name' => 'required|unique:sites,name,' . $id, // Ignore unique validation for the current site
            'description' => 'required|string',
            'anchors' => 'required|array|min:3',  // Ensuring at least 3 anchors
            'assets' => 'required|array|min:1',  // Ensuring at least 1 asset
            'assets.*.device_uid' => 'required|string|unique:assets,device_uid',
            'assets.*.device_icon' => 'required|string',
            'assets.*.device_name' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048' // Optional
        ]);

        // Find the site by ID
        $site = Site::findOrFail($id);

        // Store the image if it exists and delete the old image if necessary
        if ($request->hasFile('image')) {
            // Optionally delete the old image
            if ($site->image_url) {
                $oldImagePath = str_replace(asset('storage/'), 'public/', $site->image_url);
                Storage::delete($oldImagePath);
            }
            $imagePath = $request->file('image')->store('images', 'public');
            $site->image_url = asset('storage/' . $imagePath);
        }

        // Update the site details
        $site->name = $request->name;
        $site->description = $request->description;
        $site->save();

        // Update anchors
        $site->anchors()->delete(); // Delete existing anchors
        foreach ($request->anchors as $anchorData) {
            $anchorData['site_id'] = $site->id; // Associate anchor with the site
            Anchor::create($anchorData); // Create anchor for the site
        }

        // Update assets
        $site->assets()->delete(); // Delete existing assets
        foreach ($request->assets as $assetData) {
            $assetData['site_id'] = $site->id; // Associate asset with the site
            Assets::create($assetData); // Create asset for the site
        }

        return redirect()->route('sites.index')->with('success', 'Site updated successfully!');
    }


    // Delete site
    public function destroy(Site $site)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Delete associated anchors
        $site->anchors()->delete();

        // Delete the site
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site deleted successfully!');
        }
        public function show($siteId)
        {
            $site = Site::with(['anchors', 'assets'])->findOrFail($siteId);
            // dd($site);
            // Calculate max width and height based on anchors' data
            $maxWidth = $site->anchors->max('x');
            $maxHeight = $site->anchors->max('y');

            return view('sites.show', compact('site', 'maxWidth', 'maxHeight'));
        }




}

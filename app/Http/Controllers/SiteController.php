<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSiteRequest;
use App\Jobs\CreatePantheonSite;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    // Method to display the form
    public function showForm()
    {
        return view('create-site');
    }

    // Method to handle form submission
    public function create(CreateSiteRequest $request)
    {
    //    dd($request->all()); // Debugging line to check form data
        // Validate request data
        $validatedData = $request->validated();

        // Dispatch job to queue with validated data
        CreatePantheonSite::dispatch($validatedData)->onQueue('default');

        // Optionally, return a response or redirect to indicate successful submission
        return redirect()->back()->with('success', 'Site creation request submitted successfully.');
    }
}

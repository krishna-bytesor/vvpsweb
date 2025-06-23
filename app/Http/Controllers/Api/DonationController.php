<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends ApiController
{
    public function index()
    {
        return $this->respondWith(
            Donation::where('user_id', Auth::id())->paginate()
        );
    }

    public function store(Request $request)
    {
        $input = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:15',
            'description' => 'required|string|max:255',
        ]);

        $input['user_id'] = Auth::id();

        $donation = Donation::create($input);

        return $this->respondWith(
            $donation,
            'Donation created successfully.',
            201
        );
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Officer;
use Illuminate\Support\Facades\Log;

class LandingPageController extends Controller
{
    public function index()
    {
        try {
            // Get upcoming events
            $upcomingEvents = Event::where('event_date', '>=', now())
                                 ->orderBy('event_date', 'asc')
                                 ->take(3)
                                 ->get();

            // Get organization officers for leadership section
            $officers = Officer::whereIn('position', ['President', 'Vice President', 'Secretary'])
                             ->get();

            return view('LandingPage', compact('upcomingEvents', 'officers'));
        } catch (\Exception $e) {
            Log::error('Error in landing page: ' . $e->getMessage());
            return view('LandingPage');
        }
    }

    public function contactUs(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|max:1000'
            ]);

            // Here you can add logic to save contact form data or send email
            // For now, we'll just return a success response

            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for your message. We will get back to you soon!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in contact form: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sorry, there was an error sending your message.'
            ], 500);
        }
    }
} 
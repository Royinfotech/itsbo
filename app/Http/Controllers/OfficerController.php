<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OfficerController extends Controller
{
    public function index()
    {
        try {
            // Fetch all officers and order by creation date
            $officers = Officer::orderBy('created_at', 'desc')->get();
            
            // Log the number of officers found
            Log::info('Officers retrieved successfully', [
                'count' => $officers->count(),
                'officers' => $officers->toArray()
            ]);

            // Check if officers collection is empty
            if ($officers->isEmpty()) {
                Log::info('No officers found in the database');
            }

            // Return the view with the officers data
            return view('Secretary.Officers', compact('officers'));
        } catch (\Exception $e) {
            Log::error('Error retrieving officers: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return view with empty collection in case of error
            return view('Secretary.Officers', ['officers' => collect([])]);
        }
    }

    public function store(Request $request)
    {
        try {
            // Get the current open school year
            $currentSchoolYear = \App\Models\SchoolYear::where('is_open', true)->first();
            if (!$currentSchoolYear) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No open school year/semester. Please contact the SuperAdmin.'
                ], 422);
            }

            // Officer limit check
            $officerCount = Officer::where('school_year_id', $currentSchoolYear->id)->count();
            if ($officerCount >= $currentSchoolYear->officer_limit) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Officer limit reached for this school year/semester.'
                ], 422);
            }

            // Allowed positions check
            $positions = $currentSchoolYear->open_positions ?? [];
            $validated = $request->validate([
                'last_name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'birthdate' => 'required|date',
                'email' => 'required|email|unique:officers',
                'position' => ['required', 'string', \Illuminate\Validation\Rule::in($positions)],
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10048'
            ]);

            $imagePath = $request->file('image')->store('officers', 'public');

            $officer = Officer::create([
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'birthdate' => $request->birthdate,
                'email' => $request->email,
                'position' => $request->position,
                'image_path' => $imagePath,
                'school_year_id' => $currentSchoolYear->id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Officer added successfully',
                'officer' => $officer
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add officer. Please try again.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $officer = Officer::findOrFail($id);

            // Log the incoming request data
            Log::info('Update request data:', $request->all());

            $validated = $request->validate([
                'last_name' => 'required|string|max:255',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'birthdate' => 'required|date',
                'email' => 'required|email|unique:officers,email,' . $id,
                'position' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($id, $officer) {
                        // Skip validation if position hasn't changed
                        if ($value === $officer->position) {
                            return;
                        }

                        // Define position limits
                        $singleOfficerPositions = [
                            'President',
                            'Vice President',
                            'Assistant Secretary',
                            'Secretary',
                            'Treasurer',
                            'Assistant Treasurer',
                            'Auditor',
                            'Pio1',
                            '2nd Year Representative',
                            '3rd Year Representative',
                            '4th Year Representative'
                        ];

                        $twoOfficerPositions = [
                            '1st Year Representative',
                            'Sgt at Arms'
                        ];

                        $fourOfficerPositions = [
                            'Pio2'
                        ];

                        // Get current count for the position, excluding the current officer
                        $currentCount = Officer::where('position', $value)
                            ->where('id', '!=', $id)
                            ->count();

                        // Check limits based on position
                        if (in_array($value, $singleOfficerPositions) && $currentCount >= 1) {
                            $fail("Only 1 officer is allowed for the position of {$value}.");
                        } elseif (in_array($value, $twoOfficerPositions) && $currentCount >= 2) {
                            $fail("Only 2 officers are allowed for the position of {$value}.");
                        } elseif (in_array($value, $fourOfficerPositions) && $currentCount >= 4) {
                            $fail("Only 4 officers are allowed for the position of {$value}.");
                        }
                    }
                ],
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ], [
                'last_name.required' => 'The last name field is required.',
                'last_name.string' => 'The last name must be a text.',
                'last_name.max' => 'The last name may not be greater than 255 characters.',
                'first_name.required' => 'The first name field is required.',
                'first_name.string' => 'The first name must be a text.',
                'first_name.max' => 'The first name may not be greater than 255 characters.',
                'middle_name.string' => 'The middle name must be a text.',
                'middle_name.max' => 'The middle name may not be greater than 255 characters.',
                'birthdate.required' => 'The birthdate field is required.',
                'birthdate.date' => 'The birthdate must be a valid date.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email is already in use by another officer.',
                'position.required' => 'The position field is required.',
                'position.string' => 'The position must be a text.',
                'image.image' => 'The file must be an image.',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
                'image.max' => 'The image may not be greater than 2MB.'
            ]);

            $updateData = [
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'birthdate' => $request->birthdate,
                'email' => $request->email,
                'position' => $request->position,
            ];

            // Handle image update if new image is uploaded
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('officers', 'public');
                $updateData['image_path'] = $imagePath;
            }

            $officer->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Officer updated successfully'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Officer update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update officer. Please try again.'
            ], 500);
        }
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Support\Facades\Log;

class OrgController extends Controller
{
    public function index()
    {
        try {
            // Get all officers grouped by their positions
            $officers = Officer::all()->groupBy('position');
            
            // Get specific officers for each position
            $president = Officer::where('position', 'President')->first();
            $vicePresident = Officer::where('position', 'Vice President')->first();
            $secretary = Officer::where('position', 'Secretary')->first();
            $assistantSecretary = Officer::where('position', 'Assistant Secretary')->first();
            $treasurer = Officer::where('position', 'Treasurer')->first();
            $assistantTreasurer = Officer::where('position', 'Assistant Treasurer')->first();
            $auditor = Officer::where('position', 'Auditor')->first();
            
            // Get all officers for positions with multiple officers
            $sgtAtArms = Officer::where('position', 'Sgt at Arms')->orderBy('id')->get();
            $pio1 = Officer::where('position', 'Pio1')->first();
            $pio2 = Officer::where('position', 'Pio2')->orderBy('id')->get();
            $firstYearRep = Officer::where('position', '1st Year Representative')->orderBy('id')->get();
            $secondYearRep = Officer::where('position', '2nd Year Representative')->first();
            $thirdYearRep = Officer::where('position', '3rd Year Representative')->first();
            $fourthYearRep = Officer::where('position', '4th Year Representative')->first();

            // Debug log to check the data
            Log::info('Sgt at Arms count: ' . $sgtAtArms->count());
            Log::info('PIO 2 count: ' . $pio2->count());
            Log::info('First Year Rep count: ' . $firstYearRep->count());

            return view('Secretary.OrStruct', compact(
                'president',
                'vicePresident',
                'secretary',
                'assistantSecretary',
                'treasurer',
                'assistantTreasurer',
                'auditor',
                'sgtAtArms',
                'pio1',
                'pio2',
                'firstYearRep',
                'secondYearRep',
                'thirdYearRep',
                'fourthYearRep'
            ));
        } catch (\Exception $e) {
            Log::error('Error fetching organizational structure: ' . $e->getMessage());
            return view('Secretary.OrStruct')->with('error', 'Failed to load organizational structure');
        }
    }
} 
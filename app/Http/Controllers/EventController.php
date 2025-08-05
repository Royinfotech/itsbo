<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    // Add helper method to check active school year
    private function getActiveSchoolYear()
    {
        $activeSchoolYear = SchoolYear::where('is_open', true)->first();
        if (!$activeSchoolYear) {
            throw new \Exception('No active school year found. Please open a school year first.');
        }
        return $activeSchoolYear;
    }

    public function index()
    {
        try {
            // Get active school year
            $activeSchoolYear = $this->getActiveSchoolYear();
            
            // Get events for current school year only
            $events = Event::where('school_year_id', $activeSchoolYear->id)->get();
            return response()->json($events);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Check for active school year first
            $activeSchoolYear = $this->getActiveSchoolYear();

            $request->validate([
                'event_name' => 'required|string|max:255',
                'event_location' => 'required|string|max:255',
                'event_date' => 'required|date',
                'time_duration' => 'required|in:Whole Day,Half Day: Morning,Half Day: Afternoon',
            ]);
        
            // Check if event exists in current school year
            $existingEvent = Event::where('event_name', $request->event_name)
                                ->where('event_date', $request->event_date)
                                ->where('school_year_id', $activeSchoolYear->id)
                                ->first();
        
            if ($existingEvent) {
                return response()->json(['error' => 'Event with the same name and date already exists in this school year!'], 409);
            }
        
            // Check for time conflicts within current school year
            $conflict = $this->checkTimeConflictLogic($request->event_date, $request->time_duration, null, $activeSchoolYear->id);
            
            if ($conflict['exists']) {
                return response()->json(['error' => $conflict['message']], 409);
            }
        
            // Store new event with school year
            $event = Event::create([
                'event_name' => $request->event_name,
                'event_location' => $request->event_location,
                'event_date' => $request->event_date,
                'time_duration' => $request->time_duration,
                'open_scan_type' => null,
                'is_finished' => false,
                'school_year_id' => $activeSchoolYear->id
            ]);
        
            return response()->json([
                'message' => 'Event created successfully!', 
                'event' => $event,
                'school_year' => $activeSchoolYear->year
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkConflict(Request $request)
    {
        try {
            // Get parameters from the request
            $eventId = $request->input('event_id', null);
            $eventDate = $request->input('event_date');
            $eventDuration = $request->input('event_duration');
            
            Log::info('Checking conflict with params:', [
                'event_id' => $eventId,
                'event_date' => $eventDate,
                'event_duration' => $eventDuration
            ]);
            
            // Validate required parameters
            if (!$eventDate || !$eventDuration) {
                return response()->json([
                    "error" => "Missing required parameters", 
                    "conflict" => false
                ], 400);
            }
            
            $conflict = $this->checkTimeConflictLogic($eventDate, $eventDuration, $eventId);
            
            return response()->json([
                "conflict" => $conflict['exists'],
                "message" => $conflict['message']
            ]);
        } catch (\Exception $e) {
            Log::error('Error in conflict check: ' . $e->getMessage());
            return response()->json([
                "error" => "Server error: " . $e->getMessage(), 
                "conflict" => false
            ], 500);
        }
    }
    
    public function checkDuplicate(Request $request)
    {
        try {
            $eventId = $request->input('event_id');
            $eventName = $request->input('event_name');
            $eventDate = $request->input('event_date');
            
            if (!$eventId || !$eventName || !$eventDate) {
                return response()->json([
                    'error' => 'Missing required parameters',
                    'duplicate' => false
                ], 400);
            }
            
            // Check if an event with the same name and date exists (excluding the current event)
            $duplicate = Event::where('event_name', $eventName)
                          ->where('event_date', $eventDate)
                          ->where('event_id', '!=', $eventId)
                          ->exists();
            
            return response()->json(['duplicate' => $duplicate]);
        } catch (\Exception $e) {
            Log::error('Error checking duplicate: ' . $e->getMessage());
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage(),
                'duplicate' => false
            ], 500);
        }
    }
    
    public function update(Request $request)
    {
        try {
            $request->validate([
                'event_id' => 'required',
                'event_name' => 'required|string|max:255',
                'event_location' => 'required|string|max:255',
                'event_date' => 'required|date',
                'time_duration' => 'required|string',
            ]);
            
            Log::info('Updating event with data:', $request->all());

            $event = Event::where('event_id', $request->event_id)->first();
            if (!$event) {
                return response()->json(['error' => 'Event not found'], 404);
            }
            
            // Check for duplicate events (same name and date, different event_id)
            $duplicate = Event::where('event_name', $request->event_name)
                          ->where('event_date', $request->event_date)
                          ->where('event_id', '!=', $request->event_id)
                          ->exists();
            
            if ($duplicate) {
                return response()->json(['error' => 'An event with this name and date already exists!'], 409);
            }
            
            // If the date or time_duration has changed, check for conflicts
            if ($event->event_date != $request->event_date || $event->time_duration != $request->time_duration) {
                $conflict = $this->checkTimeConflictLogic($request->event_date, $request->time_duration, $request->event_id);
                
                if ($conflict['exists']) {
                    return response()->json(['error' => $conflict['message']], 409);
                }
            }

            // Update the event if no conflicts
            $event->update([
                'event_name' => $request->event_name,
                'event_location' => $request->event_location,
                'event_date' => $request->event_date,
                'time_duration' => $request->time_duration,
            ]);

            return response()->json(['success' => true, 'message' => 'Event updated successfully!']);
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());
            return response()->json(['error' => 'Server error: ' . $e->getMessage()], 500);
        }
    }
    
    public function getEventDetails($id)
    {
        try {
            $event = Event::findOrFail($id);
            
            // Add available scan types based on event duration
            $event->available_scan_types = $this->getAvailableScanTypes($event->time_duration);
            
            return response()->json($event);
        } catch (\Exception $e) {
            Log::error('Error fetching event details:', [
                'event_id' => $id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Event not found'], 404);
        }
    }
    
    // Add this new helper method
    private function getAvailableScanTypes($timeDuration)
    {
        $scanTypes = [];
        
        switch ($timeDuration) {
            case 'Whole Day':
                $scanTypes = [
                    'morning' => ['am_in', 'am_out'],
                    'afternoon' => ['pm_in', 'pm_out']
                ];
                break;
                
            case 'Half Day: Morning':
                $scanTypes = [
                    'morning' => ['am_in', 'am_out'],
                    'afternoon' => []
                ];
                break;
                
            case 'Half Day: Afternoon':
                $scanTypes = [
                    'morning' => [],
                    'afternoon' => ['pm_in', 'pm_out']
                ];
                break;
                
            default:
                $scanTypes = [
                    'morning' => [],
                    'afternoon' => []
                ];
        }
        
        return $scanTypes;
    }

    // Helper method to check time conflicts - returns array with conflict status and message
    private function checkTimeConflictLogic($eventDate, $eventDuration, $excludeEventId = null, $schoolYearId = null)
    {
        try {
            // Check event date is at least 3 days in future
            $eventDateTime = strtotime($eventDate);
            $threeDaysFromNow = strtotime('+3 days');
            
            if ($eventDateTime < $threeDaysFromNow) {
                return [
                    'exists' => true,
                    'message' => 'Events must be scheduled at least 3 days in advance.'
                ];
            }

            // Get events on same date in current school year
            $query = Event::where('event_date', $eventDate);
            
            if ($excludeEventId) {
                $query->where('event_id', '!=', $excludeEventId);
            }

            if ($schoolYearId) {
                $query->where('school_year_id', $schoolYearId);
            }
            
            $eventsOnSameDate = $query->get();
            
            Log::info('Events on date ' . $eventDate . ':', [
                'count' => $eventsOnSameDate->count(),
                'excludeId' => $excludeEventId,
                'events' => $eventsOnSameDate->toArray()
            ]);
            
            // If no events on this date, there's no conflict
            if ($eventsOnSameDate->isEmpty()) {
                return ['exists' => false, 'message' => 'No conflicts'];
            }
            
            // Check for whole day events
            $hasWholeDay = $eventsOnSameDate->contains('time_duration', 'Whole Day');
            
            // Check for half-day events
            $hasMorning = $eventsOnSameDate->contains('time_duration', 'Half Day: Morning');
            $hasAfternoon = $eventsOnSameDate->contains('time_duration', 'Half Day: Afternoon');
            
            Log::info('Event types on date:', [
                'hasWholeDay' => $hasWholeDay,
                'hasMorning' => $hasMorning,
                'hasAfternoon' => $hasAfternoon,
                'requestedDuration' => $eventDuration
            ]);
            
            // Rule 1: If there's already a "whole day" event, no other events allowed
            if ($hasWholeDay) {
                return [
                    'exists' => true, 
                    'message' => 'There is already a whole day event on this date!'
                ];
            }
            
            // Rule 2: If adding a "whole day" event, no other events should exist
            if ($eventDuration == 'Whole Day') {
                if ($eventsOnSameDate->isNotEmpty()) {
                    return [
                        'exists' => true, 
                        'message' => 'Cannot add a whole day event when other events exist on this date!'
                    ];
                }
            }
            
            // Rule 3: If there are both morning and afternoon events, no more events allowed
            if ($hasMorning && $hasAfternoon) {
                return [
                    'exists' => true, 
                    'message' => 'Both morning and afternoon slots are already taken on this date!'
                ];
            }
            
            // Rule 4: If adding a morning event, check if morning slot is taken
            if ($eventDuration == 'Half Day: Morning' && $hasMorning) {
                return [
                    'exists' => true, 
                    'message' => 'There is already a morning event on this date!'
                ];
            }
            
            // Rule 5: If adding an afternoon event, check if afternoon slot is taken
            if ($eventDuration == 'Half Day: Afternoon' && $hasAfternoon) {
                return [
                    'exists' => true, 
                    'message' => 'There is already an afternoon event on this date!'
                ];
            }
            
            // No conflicts found
            return ['exists' => false, 'message' => 'No conflicts'];
        } catch (\Exception $e) {
            Log::error('Error in checkTimeConflictLogic: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'eventDate' => $eventDate,
                'eventDuration' => $eventDuration,
                'excludeEventId' => $excludeEventId
            ]);
            throw $e; // Re-throw to be caught by the calling method
        }
    }
}

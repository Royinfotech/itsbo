<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link rel="stylesheet" href="{{ asset('assets/css/Event.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Add Bootstrap CSS for modal styling -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<body>
    <div class="container">
        <h2>Create an Event</h2>
        <form id="eventForm" action="{{ route('events.store') }}" method="POST">
            @csrf  <!-- Laravel's security token -->
        
            <label for="eventName">Event Name:</label>
            <input type="text" id="eventName" name="event_name" required>
        
            <label for="eventLocation">Event Location:</label>
            <input type="text" id="eventLocation" name="event_location" required>
        
            <label for="eventDate">Event Date:</label>
            <input type="date" id="eventDate" name="event_date" required>
        
            <label for="eventDuration">Event Duration:</label>
            <select id="eventDuration" name="time_duration" required>
                <option value="Whole Day">Whole Day</option>
                <option value="Half Day: Morning">Half Day: Morning</option>
                <option value="Half Day: Afternoon">Half Day: Afternoon</option>
            </select>
            <hr class="form-divider">
        
<div style="display: flex; justify-content: center;">
    <button type="submit" id="createBtn" style="border-radius: 15px;">Create Event</button>
</div>


        </form>
        
        
    </div>
    

    <div class="container">
        <h2>Event Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Event Title</th>
                    <th>Event Location</th>
                    <th>Event Date</th>
                    <th>Event Duration</th>
                </tr>
            </thead>
            <tbody id="eventList">
                @if(isset($event->event_date))
                    {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}
                @endif
            </tbody>
        </table>
    </div>

   <!-- This should be in your HTML file -->
<div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                <span class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form id="editEventForm">
            <input type="hidden" id="modalEventId" name="event_id">
            
            <div class="form-group">
              <label for="modalEventName">Event Name</label>
              <input type="text" class="form-control" id="modalEventName" name="event_name" required>
            </div>
            
            <div class="form-group">
              <label for="modalEventLocation">Location</label>
              <input type="text" class="form-control" id="modalEventLocation" name="event_location" required>
            </div>
            
            <div class="form-group">
              <label for="modalEventDate">Date</label>
              <input type="date" class="form-control" id="modalEventDate" name="event_date" required>
            </div>
            
            <div class="form-group">
              <label for="modalEventDuration">Duration</label>
              <select class="form-control" id="modalEventDuration" name="time_duration" required>
                <option value="Whole Day">Whole Day</option>
                <option value="Half Day: Morning">Half Day: Morning</option>
                <option value="Half Day: Afternoon">Half Day: Afternoon</option>
              </select>
            </div>
            
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

    <!-- Add Bootstrap JS for modal functionality -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('assets/js/Event.js') }}"></script>
</body>
</html>

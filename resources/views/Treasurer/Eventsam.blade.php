@extends('layouts.secretary')

@section('content')
    <div class="dashboard-container">
        <div class="dashboard-card calendar-card">
            <h3>Calendar of Activities</h3>
            <table class="event-table">
                <thead>
                    <tr>
                        <th>Event Title</th>
                        <th>Event Location</th>
                        <th>Event Date</th>
                        <th>Event Duration</th>
                    </tr>
                </thead>
                <tbody id="eventTableBody">
                    @foreach($events as $event)
                    <tr>
                        <td>{{ $event->event_name }}</td>
                        <td>{{ $event->event_location }}</td>
                        <td>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</td>
                        <td>{{ $event->time_duration }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

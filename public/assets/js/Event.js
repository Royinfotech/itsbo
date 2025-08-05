document.addEventListener("DOMContentLoaded", function () {
    fetchEvents();

    document.getElementById("eventForm").addEventListener("submit", function (event) {
        event.preventDefault();
        const form = this;
        const formData = new FormData(form);
        const eventDate = formData.get('event_date');
        const timeDuration = formData.get('time_duration');

        const url = `/events/check-conflict?event_date=${encodeURIComponent(eventDate)}&event_duration=${encodeURIComponent(timeDuration)}`;
        
        fetch(url)
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(({ status, data }) => {
                // Check for 3-day advance requirement error first
                if (status === 422 && data.error === 'Event must be created at least 3 days in advance.') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Date Too Soon',
                        text: '❌ You must create an event at least 3 days before it happens.',
                        confirmButtonColor: '#6d1b1b',
                    });
                    return;
                }

                // Check if there's a conflict
                if (data.conflict) {
                    // Check if the conflict is specifically about the 3-day rule
                    if (data.error && data.error.includes('3 days in advance')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Date Too Soon',
                            text: '❌ You must create an event at least 3 days before it happens.',
                            confirmButtonColor: '#6d1b1b',
                        });
                    } else {
                        // It's a regular time conflict with other events
                        Swal.fire({
                            icon: 'warning',
                            title: 'Time Conflict',
                            text: data.message || data.error || '⚠️ This conflicts with existing events on the selected date.',
                            confirmButtonColor: '#6d1b1b',
                        });
                    }
                    return;
                }

                // If no conflicts, submit the form
                submitEventForm(form, formData);
            })
            .catch(error => {
                console.error("Validation error:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: '❌ Unable to validate event: ' + error.message,
                    confirmButtonColor: '#6d1b1b',
                });
            });
    });

    document.getElementById("editEventForm").addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        const eventId = formData.get('event_id');
        const eventDate = formData.get('event_date');
        const timeDuration = formData.get('time_duration');

        const url = `/events/check-conflict?event_id=${encodeURIComponent(eventId)}&event_date=${encodeURIComponent(eventDate)}&event_duration=${encodeURIComponent(timeDuration)}`;

        fetch(url)
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return response.json().then(data => {
                        if (!response.ok) throw new Error(data.error || 'Conflict check failed');
                        return data;
                    });
                } else {
                    return response.text().then(text => {
                        throw new Error("Non-JSON response: " + text);
                    });
                }
            })
            .then(data => {
                if (data.conflict) {
                    // Check if the conflict is specifically about the 3-day rule
                    if (data.error && data.error.includes('3 days in advance')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Date Too Soon',
                            text: '❌ You must create an event at least 3 days before it happens.',
                            confirmButtonColor: '#6d1b1b',
                        });
                    } else {
                        // It's a regular time conflict with other events
                        Swal.fire({
                            icon: 'warning',
                            title: 'Time Conflict',
                            text: data.message || data.error || '⚠️ Conflict with existing events.',
                            confirmButtonColor: '#6d1b1b',
                        });
                    }
                    return;
                }

                updateEvent(formData);
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Conflict Check Error',
                    text: '❌ ' + error.message,
                    confirmButtonColor: '#6d1b1b',
                });
            });
    });

    function submitEventForm(form, formData) {
        fetch(form.action, {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            }
        })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: '✅ ' + body.message,
                        confirmButtonColor: '#6d1b1b',
                    });
                    form.reset();
                    fetchEvents();
                } else if (status === 409) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Conflict Detected',
                        text: '⚠️ ' + body.error,
                        confirmButtonColor: '#6d1b1b',
                    });
                } else {
                    throw new Error(body.error || 'Failed to create event.');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Error',
                    text: '❌ ' + error.message,
                    confirmButtonColor: '#6d1b1b',
                });
            });
    }

    function updateEvent(formData) {
        fetch("/events/update", {
            method: "POST",
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            }
        })
            .then(response => {
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    return response.json().then(data => {
                        if (!response.ok) throw new Error(data.error || 'Update failed');
                        return data;
                    });
                } else {
                    return response.text().then(text => {
                        throw new Error("Non-JSON response: " + text);
                    });
                }
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '✅ Event updated successfully!',
                    confirmButtonColor: '#6d1b1b',
                });
                $("#editEventModal").modal("hide");
                fetchEvents();
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Update Error',
                    text: '❌ ' + error.message,
                    confirmButtonColor: '#6d1b1b',
                });
            });
    }

function fetchEvents() {
    fetch("/events")
        .then(response => response.json())
        .then(events => {
            const eventList = document.getElementById("eventList");
            eventList.innerHTML = "";

            events.forEach(event => {
                // Format the date
                const formattedDate = formatEventDate(event.event_date);
                
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${event.event_name}</td>
                    <td>${event.event_location}</td>
                    <td>${formattedDate}</td>
                    <td>${event.time_duration}</td>
                `;
                row.classList.add('clickable-row');
                row.dataset.event = JSON.stringify(event);
                row.addEventListener("click", function () {
                    openEditModal(JSON.parse(this.dataset.event));
                });
                eventList.appendChild(row);
            });

            const style = document.createElement('style');
            style.textContent = `
                .clickable-row { cursor: pointer; }
                .clickable-row:hover { background-color: #f5f5f5; }
            `;
            document.head.appendChild(style);
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Loading Error',
                text: '❌ Error loading events. Please refresh.',
                confirmButtonColor: '#6d1b1b',
            });
        });
}

function formatEventDate(dateString) {
    try {
        // Create a Date object from the date string
        const date = new Date(dateString);
        
        // Check if the date is valid
        if (isNaN(date.getTime())) {
            return dateString; // Return original if invalid
        }
        
        // Format the date as "Month day, year"
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        
        return date.toLocaleDateString('en-US', options);
    } catch (error) {
        console.error('Error formatting date:', error);
        return dateString; // Return original string if formatting fails
    }
}

    function openEditModal(event) {
        const eventId = event.id || event.event_id;
        if (!eventId) {
            Swal.fire({
                icon: 'error',
                title: 'Missing Data',
                text: '❌ Cannot edit event. Missing ID.',
                confirmButtonColor: '#6d1b1b',
            });
            return;
        }

        document.getElementById("modalEventId").value = eventId;
        document.getElementById("modalEventName").value = event.event_name;
        document.getElementById("modalEventLocation").value = event.event_location;
        document.getElementById("modalEventDate").value = event.event_date;
        document.getElementById("modalEventDuration").value = event.time_duration;

        const editForm = document.getElementById("editEventForm");
        editForm.dataset.originalName = event.event_name;
        editForm.dataset.originalDate = event.event_date;
        editForm.dataset.originalDuration = event.time_duration;

        $("#editEventModal").modal("show");
    }
});
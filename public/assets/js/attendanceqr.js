document.addEventListener('DOMContentLoaded', function() {
    let selectedEventId = '';
    let currentScanType = '';
    let isProcessing = false;
    let html5QrcodeScanner;
    let alertShowing = false;
    let currentEventDuration = '';
    let currentEventDate = '';
    let isEventToday = false;

    // Function to check if a date is today
    function isDateToday(dateString) {
        const today = new Date();
        const eventDate = new Date(dateString);
        
        return today.getFullYear() === eventDate.getFullYear() &&
               today.getMonth() === eventDate.getMonth() &&
               today.getDate() === eventDate.getDate();
    }

    function enableDurationAndScanTypes(duration, eventDate = null) {
        const durationSelect = document.getElementById('timeduration_select');
        const scanTypeSelect = document.getElementById('scan_type');
        const morningOptions = document.querySelectorAll('.morning-option');
        const afternoonOptions = document.querySelectorAll('.afternoon-option');
        const dateWarning = document.getElementById('dateWarning');
        
        // Check if event is today
        isEventToday = eventDate ? isDateToday(eventDate) : false;
        
        // Show/hide date warning
        if (eventDate && !isEventToday) {
            dateWarning.style.display = 'block';
        } else {
            dateWarning.style.display = 'none';
        }
        
        // Reset everything first
        durationSelect.disabled = false;
        scanTypeSelect.disabled = false;
        durationSelect.value = '';
        scanTypeSelect.value = '';
        
        // Reset all scan type options
        morningOptions.forEach(option => {
            option.disabled = false;
            option.style.display = '';
        });
        afternoonOptions.forEach(option => {
            option.disabled = false;
            option.style.display = '';
        });

        if (duration && isEventToday) {
            // Set the duration and disable the select (read-only)
            durationSelect.value = duration;
            durationSelect.disabled = true;
            
            // Apply restrictions based on event duration
            switch(duration) {
                case 'Half Day: Morning':
                    // Hide afternoon scan types
                    afternoonOptions.forEach(option => {
                        option.disabled = true;
                        option.style.display = 'none';
                    });
                    break;
                
                case 'Half Day: Afternoon':
                    // Hide morning scan types
                    morningOptions.forEach(option => {
                        option.disabled = true;
                        option.style.display = 'none';
                    });
                    break;
                
                case 'Whole Day':
                default:
                    // Show all scan types (already done above)
                    break;
            }
        } else {
            // Event not today or no event selected, disable everything
            durationSelect.disabled = true;
            scanTypeSelect.disabled = true;
            morningOptions.forEach(option => {
                option.style.display = 'none';
                option.disabled = true;
            });
            afternoonOptions.forEach(option => {
                option.style.display = 'none';
                option.disabled = true;
            });
        }
    }

    function updateTimeDurationDisplay(duration) {
        const durationElement = document.getElementById('timeDuration');
        if (duration) {
            durationElement.textContent = `Time Duration: ${duration}`;
            durationElement.style.display = 'block';
        } else {
            durationElement.textContent = '';
            durationElement.style.display = 'none';
        }
    }

    document.getElementById('event_id').addEventListener('change', function() {
        selectedEventId = this.value;
        const selectedOption = this.options[this.selectedIndex];
        currentEventDuration = selectedOption ? selectedOption.getAttribute('data-duration') : '';
        currentEventDate = selectedOption ? selectedOption.getAttribute('data-date') : '';

        if (!selectedEventId) {
            updateTimeDurationDisplay('');
            enableDurationAndScanTypes('', null);
            // Disable buttons
            document.getElementById('openScanTypeBtn').disabled = true;
            document.getElementById('closeScanTypeBtn').disabled = true;
            document.getElementById('printAttendanceBtn').disabled = true;
            document.getElementById('finishEventBtn').disabled = true;
            // Clear scan type display
            document.getElementById('currentScanType').textContent = '';
            return;
        }

        // Update duration display and enable appropriate duration/scan types
        updateTimeDurationDisplay(currentEventDuration);
        enableDurationAndScanTypes(currentEventDuration, currentEventDate);

        // Check if event is today and not finished
        const isFinished = selectedOption && selectedOption.disabled;
        isEventToday = isDateToday(currentEventDate);
        
        // Enable/disable buttons based on event status and date
        const shouldDisableButtons = isFinished || !isEventToday;
        document.getElementById('openScanTypeBtn').disabled = shouldDisableButtons;
        document.getElementById('closeScanTypeBtn').disabled = shouldDisableButtons;
        document.getElementById('printAttendanceBtn').disabled = isFinished; // Print can work for any date
        document.getElementById('finishEventBtn').disabled = shouldDisableButtons;
        
        if (isFinished) {
            document.getElementById('currentScanType').textContent = 'Event is finished';
        } else if (!isEventToday) {
            document.getElementById('currentScanType').textContent = 'Event is not scheduled for today';
        } else {
            updateCurrentScanType();
        }
    });

    document.getElementById('openScanTypeBtn').addEventListener('click', function() {
        if (!selectedEventId) {
            Swal.fire({
                icon: 'warning',
                title: 'No Event Selected',
                text: 'Please select an event first!',
                confirmButtonColor: '#800000'
            });
            return;
        }

        if (!isEventToday) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Event Date',
                text: 'Attendance scanning is only allowed on the event date!',
                confirmButtonColor: '#800000'
            });
            return;
        }

        const scanType = document.getElementById('scan_type').value;
        const timeDuration = document.getElementById('timeduration_select').value;
        
        if (!timeDuration) {
            Swal.fire({
                icon: 'warning',
                title: 'No Time Duration',
                text: 'Event duration not available. Please select a valid event!',
                confirmButtonColor: '#800000'
            });
            return;
        }
        
        if (!scanType) {
            Swal.fire({
                icon: 'warning',
                title: 'No Scan Type Selected',
                text: 'Please select a scan type first!',
                confirmButtonColor: '#800000'
            });
            return;
        }
        
        // Show loading state
        Swal.fire({
            title: 'Opening scan type...',
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false
        });

        fetch('/attendance/open-scan-type', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ event_id: selectedEventId, scan_type: scanType })
        })
        .then(r => r.json())
        .then(data => {
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'Success' : 'Error',
                text: data.message,
                confirmButtonColor: '#800000'
            });
            updateCurrentScanType();
        });
    });
    
    document.getElementById('printAttendanceBtn').addEventListener('click', function() {
        if (!selectedEventId) {
            Swal.fire({
                icon: 'warning',
                title: 'No Event Selected',
                text: 'Please select an event first!',
                confirmButtonColor: '#800000'
            });
            return;
        }

        // Open print page in new window/tab
        const printUrl = `/attendance/print?event_id=${selectedEventId}`;
        const printWindow = window.open(printUrl, '_blank');
        
        // Optional: Auto-print when page loads
        printWindow.onload = function() {
            setTimeout(() => {
                printWindow.print();
            }, 1000);
        };
    });

    document.getElementById('closeScanTypeBtn').addEventListener('click', function() {
        if (!selectedEventId) {
            Swal.fire({
                icon: 'warning',
                title: 'No Event Selected',
                text: 'Please select an event first!',
                confirmButtonColor: '#800000'
            });
            return;
        }

        if (!isEventToday) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Event Date',
                text: 'Attendance operations are only allowed on the event date!',
                confirmButtonColor: '#800000'
            });
            return;
        }

        Swal.fire({
            title: 'Closing scan type...',
            didOpen: () => {
                Swal.showLoading();
            },
            allowOutsideClick: false
        });

        fetch('/attendance/close-scan-type', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ event_id: selectedEventId })
        })
        .then(r => r.json())
        .then(data => {
            Swal.fire({
                icon: data.success ? 'success' : 'error',
                title: data.success ? 'Success' : 'Error',
                text: data.message,
                confirmButtonColor: '#800000'
            });
            updateCurrentScanType();
        });
    });

    document.getElementById('finishEventBtn').addEventListener('click', function() {
        if (!selectedEventId) {
            Swal.fire({
                icon: 'warning',
                title: 'No Event Selected',
                text: 'Please select an event first!',
                confirmButtonColor: '#800000'
            });
            return;
        }

        if (!isEventToday) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Event Date',
                text: 'Event operations are only allowed on the event date!',
                confirmButtonColor: '#800000'
            });
            return;
        }

        // First warning about printing restriction
        Swal.fire({
            title: 'Important Notice',
            html: `
                <div style="text-align: left; margin: 10px 0;">
                    <p><strong>⚠️ Warning:</strong> Once you finish this event, you will <strong>NOT</strong> be able to print attendance reports for this event.</p>
                    <p>Please make sure to print any required attendance reports before proceeding.</p>
                    <p>Do you want to print the attendance report now?</p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonColor: '#28a745',
            denyButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Print First',
            denyButtonText: 'No, Finish Event',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // User wants to print first
                const printUrl = `/attendance/print?event_id=${selectedEventId}`;
                const printWindow = window.open(printUrl, '_blank');
                
                // Auto-print when page loads
                printWindow.onload = function() {
                    setTimeout(() => {
                        printWindow.print();
                    }, 1000);
                };
                
                // After printing, ask again if they want to finish
                setTimeout(() => {
                    Swal.fire({
                        title: 'Print Complete',
                        text: 'Now that you\'ve printed the report, do you want to finish the event?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#800000',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Finish Event',
                        cancelButtonText: 'Cancel'
                    }).then((finishResult) => {
                        if (finishResult.isConfirmed) {
                            proceedWithFinishEvent();
                        }
                    });
                }, 2000);
                
            } else if (result.isDenied) {
                // User wants to finish without printing
                Swal.fire({
                    title: 'Final Confirmation',
                    html: `
                        <div style="text-align: left; margin: 10px 0;">
                            <p><strong>Are you absolutely sure?</strong></p>
                            <p>This action cannot be undone and will:</p>
                            <ul style="text-align: left; margin: 10px 0; padding-left: 20px;">
                                <li>Prevent any further attendance scanning</li>
                                <li><strong>Permanently disable printing attendance reports</strong></li>
                            </ul>
                        </div>
                    `,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Finish Event',
                    cancelButtonText: 'Cancel'
                }).then((finalResult) => {
                    if (finalResult.isConfirmed) {
                        proceedWithFinishEvent();
                    }
                });
            }
            // If cancelled, do nothing
        });
        
        // Function to actually finish the event
        function proceedWithFinishEvent() {
            Swal.fire({
                title: 'Finishing event...',
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false
            });

            fetch('/attendance/finish-event', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ event_id: selectedEventId })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Event Finished',
                        text: 'Event has been successfully marked as finished',
                        confirmButtonColor: '#800000'
                    });
                    // Disable the event in the dropdown
                    const eventOption = document.querySelector(`option[value="${selectedEventId}"]`);
                    if (eventOption) {
                        eventOption.disabled = true;
                        eventOption.textContent += ' (Finished)';
                    }
                    // Reset selection
                    document.getElementById('event_id').value = '';
                    selectedEventId = '';
                    currentEventDuration = '';
                    currentEventDate = '';
                    isEventToday = false;
                    // Update displays
                    updateTimeDurationDisplay('');
                    enableDurationAndScanTypes('', null);
                    // Disable buttons
                    document.getElementById('openScanTypeBtn').disabled = true;
                    document.getElementById('closeScanTypeBtn').disabled = true;
                    document.getElementById('printAttendanceBtn').disabled = true;
                    document.getElementById('finishEventBtn').disabled = true;
                    // Update scan type display
                    document.getElementById('currentScanType').textContent = '';
                } else {
                    throw new Error(data.message || 'Failed to finish event');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Failed to finish event',
                    confirmButtonColor: '#800000'
                });
            });
        }
    });

    function updateCurrentScanType() {
        if (!selectedEventId) {
            document.getElementById('currentScanType').textContent = '';
            return;
        }
        
        if (!isEventToday) {
            document.getElementById('currentScanType').textContent = 'Event is not scheduled for today';
            return;
        }
        
        fetch(`/attendance/open-scan-type?event_id=${selectedEventId}`)
            .then(r => r.json())
            .then(data => {
                currentScanType = data.open_scan_type;
                document.getElementById('currentScanType').textContent = currentScanType
                    ? `Current Open: ${currentScanType.replace('_', ' ').toUpperCase()}`
                    : 'No scan type open';
            });
    }

    // Updated showResult function with better feedback
    function showResult(message, isSuccess, studentName = null) {
        return new Promise(resolve => {
            const html = studentName 
                ? `${message}<br><small class="text-muted">Student: ${studentName}</small>` 
                : message;

            Swal.fire({
                icon: isSuccess ? 'success' : 'error',
                title: isSuccess ? 'Success' : 'Error',
                html: html,
                confirmButtonColor: '#800000',
                timer: isSuccess ? 2000 : undefined,
                timerProgressBar: isSuccess,
                position: isSuccess ? 'top-end' : 'center',
                showConfirmButton: !isSuccess
            }).then(() => {
                if (isSuccess) {
                    document.getElementById('scanResult').innerHTML = '';
                }
                resolve();
            });
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        // Prevent scanning if processing or alert is showing
        if (isProcessing || alertShowing) {
            return;
        }

        if (!selectedEventId) {
            showBlockingAlert('Please select an event before scanning.', false);
            return;
        }

        // Check if event is today
        if (!isEventToday) {
            showBlockingAlert('Attendance scanning is only allowed on the event date.', false);
            return;
        }

        // Get current scan type
        const currentScanType = document.getElementById('currentScanType').textContent;
        if (!currentScanType || currentScanType === 'No scan type open' || currentScanType === 'Event is not scheduled for today') {
            showBlockingAlert('No scan type is currently open. Please open a scan type first.', false);
            return;
        }

        try {
            const qrData = JSON.parse(decodedText);
            
            // Set processing flag
            isProcessing = true;
            
            // Pause scanner while processing
            html5QrcodeScanner.pause();

            // Show processing alert
            alertShowing = true;
            Swal.fire({
                title: 'Processing...',
                text: 'Recording attendance',
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false
            });

            fetch('/attendance/scan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    student_id: qrData.student_id,
                    event_id: selectedEventId
                })
            })
            .then(async response => {
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Server error');
                return data;
            })
            .then(data => {
                if (!data.success) throw new Error(data.message || 'Failed to record attendance');

                // Show success alert and wait for it to be closed
                return showBlockingAlert(
                    `${data.message}<br><small class="text-muted">Student: ${data.student_name || ''}</small>`,
                    true
                );
            })
            .catch(error => {
                // Show error alert and wait for it to be closed
                return showBlockingAlert(error.message || 'Failed to record attendance', false);
            })
            .finally(() => {
                // Reset flags and resume scanner
                isProcessing = false;
                alertShowing = false;
                html5QrcodeScanner.resume();
            });

        } catch (error) {
            console.error('QR code parsing error:', error);
            showBlockingAlert('Invalid QR code format', false);
        }
    }

    // Updated alert function that blocks scanning
    function showBlockingAlert(message, isSuccess) {
        alertShowing = true;
        return Swal.fire({
            icon: isSuccess ? 'success' : 'error',
            title: isSuccess ? 'Success' : 'Error',
            html: message,
            confirmButtonColor: '#800000',
            allowOutsideClick: false,
            timer: 2000,
            timerProgressBar: true
        }).then(() => {
            alertShowing = false;
        });
    }

    // Initialize QR Scanner with lower FPS for better control
    html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        { 
            fps: 5,
            qrbox: 250,
            aspectRatio: 1.0
        }
    );
    html5QrcodeScanner.render(onScanSuccess);

    // Initial setup - disable everything until an event is selected
    enableDurationAndScanTypes('', null);
});
document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Attendance Script Loaded!");
    fetchEvents();

    const eventDropdown = document.getElementById("eventTitle");
    const timeDurationInput = document.getElementById("event");
    const yearLevelDropdown = document.getElementById("yearLevel");
    const attendanceTableBody = document.getElementById("attendanceTableBody");
    const submitButton = document.getElementById("submitAttendance");
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("searchButton");

    // Initially hide the submit button
    submitButton.style.display = 'none';

    let currentEventId = null;
    let eventMap = {};
    let currentPage = 1;
    const rowsPerPage = 12;
    let allLogs = [];
    let allStudents = []; // Store all students for filtering

    function fetchEvents() {
        fetch('/attendance/get-events')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log("📢 API Response:", data);
    
                let events = Array.isArray(data) ? data : [];
                if (data.error) {
                    console.error("❌ Error from server:", data.error);
                    return;
                }
    
                if (!eventDropdown) {
                    console.error("❌ eventDropdown not found!");
                    return;
                }
    
                eventDropdown.innerHTML = '<option value="">-- Select an Event --</option>';
                eventMap = {};
    
                events.forEach(event => {
                    let option = document.createElement("option");
                    option.value = event.event_id;
                    option.textContent = event.event_name;
                    eventDropdown.appendChild(option);
    
                    eventMap[event.event_id] = event;
                });
            })
            .catch(error => {
                console.error("❌ Error fetching events:", error);
                alert("Failed to load events. Please try refreshing the page.");
            });
    }

    function updateCheckboxStates(timeDuration) {
        console.log("Updating checkboxes for duration:", timeDuration);
        
        const rows = attendanceTableBody.getElementsByTagName('tr');
        
        Array.from(rows).forEach(row => {
            const amInBox = row.querySelector('.amIn');
            const amOutBox = row.querySelector('.amOut');
            const pmInBox = row.querySelector('.pmIn');
            const pmOutBox = row.querySelector('.pmOut');
            
            // First, disable all checkboxes
            [amInBox, amOutBox, pmInBox, pmOutBox].forEach(checkbox => {
                if (checkbox) {
                    checkbox.disabled = true;
                }
            });
            
            // Check if attendance is finalized
            const isFinalized = row.dataset.status === 'finalized';
            if (isFinalized) {
                return; // Skip enabling checkboxes if finalized
            }
            
            // Then enable appropriate checkboxes based on time duration
            switch(timeDuration) {
                case 'Half Day: Morning':
                    if (amInBox) amInBox.disabled = false;
                    if (amOutBox) amOutBox.disabled = false;
                    if (pmInBox) {
                        pmInBox.disabled = true;
                        pmInBox.checked = false;
                    }
                    if (pmOutBox) {
                        pmOutBox.disabled = true;
                        pmOutBox.checked = false;
                    }
                    break;
                    
                case 'Half Day: Afternoon':
                    if (amInBox) {
                        amInBox.disabled = true;
                        amInBox.checked = false;
                    }
                    if (amOutBox) {
                        amOutBox.disabled = true;
                        amOutBox.checked = false;
                    }
                    if (pmInBox) pmInBox.disabled = false;
                    if (pmOutBox) pmOutBox.disabled = false;
                    break;
                    
                case 'Whole Day':
                    [amInBox, amOutBox, pmInBox, pmOutBox].forEach(checkbox => {
                        if (checkbox) checkbox.disabled = false;
                    });
                    break;
            }
        });

        // Show/hide submit button based on finalization status
        const isFinalized = Array.from(rows).some(row => row.dataset.status === 'finalized');
        submitButton.style.display = currentEventId && !isFinalized ? 'block' : 'none';
    }

    function addCheckboxListener(checkbox, row, field) {
        checkbox.addEventListener('change', function(e) {
            e.preventDefault();
            
            if (!currentEventId) {
                alert("Please select an event first!");
                checkbox.checked = !checkbox.checked;
                return;
            }

            const studentId = row.dataset.studentId;
            console.log('Saving attendance for:', {
                studentId: studentId,
                eventId: currentEventId,
                field: field,
                value: this.checked
            });

            // Immediately save to database
            fetch('/update-attendance', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    event_id: currentEventId,
                    student_id: studentId,
                    field: field,
                    value: this.checked
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Failed to save attendance');
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Save response:', data);
                if (data.success) {
                    showTemporaryStatus(row, true);
                    updateRowStatus(row);
                } else {
                    throw new Error(data.message || 'Failed to save attendance');
                }
            })
            .catch(error => {
                console.error('Save error:', error);
                showTemporaryStatus(row, false);
                checkbox.checked = !checkbox.checked; // Revert checkbox state
                alert("Error saving attendance: " + error.message);
            });
        });
    }

    function updateRowStatus(row) {
        const timeDuration = timeDurationInput.value;
        const amIn = row.querySelector('.amIn')?.checked || false;
        const amOut = row.querySelector('.amOut')?.checked || false;
        const pmIn = row.querySelector('.pmIn')?.checked || false;
        const pmOut = row.querySelector('.pmOut')?.checked || false;

        let isComplete = false;

        switch(timeDuration) {
            case 'Half Day: Morning':
                isComplete = amIn && amOut;
                break;
            case 'Half Day: Afternoon':
                isComplete = pmIn && pmOut;
                break;
            case 'Whole Day':
                isComplete = amIn && amOut && pmIn && pmOut;
                break;
        }

        row.classList.toggle('present', isComplete);
        row.classList.toggle('absent', !isComplete);
    }

    function showTemporaryStatus(row, success) {
        const status = document.createElement('div');
        status.className = `status-indicator ${success ? 'success' : 'error'}`;
        status.textContent = success ? '✓' : '✗';
        
        row.appendChild(status);
        
        setTimeout(() => {
            status.remove();
        }, 2000);
    }

    // Add search functionality
    function filterStudents(searchTerm) {
        if (!searchTerm) {
            displayStudents(allStudents);
            return;
        }

        searchTerm = searchTerm.toLowerCase();
        const filteredStudents = allStudents.filter(student => {
            const studentId = student.student_id.toLowerCase();
            const studentName = `${student.last_name}, ${student.first_name} ${student.middle_name || ''}`.toLowerCase();
            return studentId.includes(searchTerm) || studentName.includes(searchTerm);
        });

        displayStudents(filteredStudents);
    }

    function displayStudents(students) {
        attendanceTableBody.innerHTML = "";

        if (!Array.isArray(students) || students.length === 0) {
            attendanceTableBody.innerHTML = `
                <tr>
                    <td colspan="7" style="text-align: center;">No students found for the selected criteria</td>
                </tr>`;
            // Remove any existing pagination
            const existingPagination = document.querySelector('.pagination-container');
            if (existingPagination) {
                existingPagination.remove();
            }
            return;
        }

        // Calculate pagination
        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;
        const paginatedStudents = students.slice(startIndex, endIndex);
        const totalPages = Math.ceil(students.length / rowsPerPage);

        // Display paginated students
        paginatedStudents.forEach(student => {
            let row = document.createElement("tr");
            row.dataset.studentId = student.student_id;
            row.dataset.status = student.status || '';
            
            row.innerHTML = `
                <td>${student.student_id}</td>
                <td>${student.last_name}, ${student.first_name} ${student.middle_name ? student.middle_name.charAt(0) + '.' : ''}</td>
                <td>${student.year_level}</td>
                <td><input type="checkbox" class="attendanceCheckbox amIn" ${student.am_in ? 'checked' : ''} disabled></td>
                <td><input type="checkbox" class="attendanceCheckbox amOut" ${student.am_out ? 'checked' : ''} disabled></td>
                <td><input type="checkbox" class="attendanceCheckbox pmIn" ${student.pm_in ? 'checked' : ''} disabled></td>
                <td><input type="checkbox" class="attendanceCheckbox pmOut" ${student.pm_out ? 'checked' : ''} disabled></td>
            `;
            
            attendanceTableBody.appendChild(row);
            
            // Enable appropriate checkboxes and add listeners
            const checkboxes = {
                '.amIn': 'am_in',
                '.amOut': 'am_out',
                '.pmIn': 'pm_in',
                '.pmOut': 'pm_out'
            };

            for (let [selector, field] of Object.entries(checkboxes)) {
                const checkbox = row.querySelector(selector);
                if (checkbox) {
                    addCheckboxListener(checkbox, row, field);
                }
            }

            // Update row status based on existing attendance
            updateRowStatus(row);
        });

        // Remove any existing pagination
        const existingPagination = document.querySelector('.pagination-container');
        if (existingPagination) {
            existingPagination.remove();
        }

        // Add pagination controls only if there are multiple pages
        if (totalPages > 1) {
            const paginationContainer = document.createElement('div');
            paginationContainer.className = 'pagination-container';
            paginationContainer.innerHTML = `
                <button id="prevPageBtn" class="pagination-button" ${currentPage === 1 ? 'disabled' : ''}>Previous</button>
                <span class="pagination-info">Page ${currentPage} of ${totalPages}</span>
                <button id="nextPageBtn" class="pagination-button" ${currentPage === totalPages ? 'disabled' : ''}>Next</button>
            `;

            // Insert pagination after the table
            const tableContainer = document.querySelector('.attendance-table');
            tableContainer.appendChild(paginationContainer);

            // Add event listeners for pagination buttons
            document.getElementById('prevPageBtn').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayStudents(students);
                }
            });

            document.getElementById('nextPageBtn').addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    displayStudents(students);
                }
            });
        }

        // Update checkbox states based on event duration
        updateCheckboxStates(timeDurationInput.value);
    }

    // Add event listeners for search
    searchButton.addEventListener('click', () => {
        filterStudents(searchInput.value);
    });

    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            filterStudents(searchInput.value);
        }
    });

    // Modify the existing fetchStudents function
    function fetchStudents(yearLevel = "") {
        if (!currentEventId) {
            console.log("No event selected");
            return;
        }

        console.log("Fetching students for event:", currentEventId, "year level:", yearLevel);

        fetch(`/get-students-with-attendance?year_level=${encodeURIComponent(yearLevel)}&event_id=${currentEventId}`)
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.error || 'Failed to load students');
                    });
                }
                return response.json();
            })
            .then(students => {
                console.log("Received students data:", students);
                allStudents = students; // Store all students
                filterStudents(searchInput.value); // Apply current search filter
            })
            .catch(error => {
                console.error("Error fetching students:", error);
                attendanceTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; color: red;">
                            Error: ${error.message}
                        </td>
                    </tr>`;
            });
    }
    
// Submit button click handler
submitButton.addEventListener('click', function() {
    if (!currentEventId) {
        Swal.fire({
            icon: 'warning',
            title: 'No Event Selected',
            text: 'Please select an event first!',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Show confirmation dialog with SweetAlert
    Swal.fire({
        title: 'Finalize Attendance?',
        html: `
            <div style="text-align: left; margin: 20px 0;">
                <p><strong>⚠️ Important:</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This action cannot be undone</li>
                    <li>Once finalized, you <strong>cannot print the attendance report</strong> for this event</li>
                    <li>Make sure you have printed all necessary reports before proceeding</li>
                </ul>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, finalize it!',
        cancelButtonText: 'Cancel',
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Disable submit button while processing
            submitButton.disabled = true;

            // Show loading alert
            Swal.fire({
                title: 'Processing...',
                text: 'Finalizing attendance, please wait.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Finalize attendance
            fetch('/finalize-attendance', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    event_id: currentEventId,
                    attendance_date: new Date().toISOString().split('T')[0]
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => {
                        throw new Error(err.message || 'Failed to finalize attendance');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Attendance finalized successfully!',
                        confirmButtonText: 'OK'
                    });
                    fetchStudents(yearLevelDropdown.value); // Refresh the list
                    submitButton.style.display = 'none'; // Hide the button after finalization
                } else {
                    throw new Error(data.message || 'Failed to finalize attendance');
                }
            })
            .catch(error => {
                console.error("Error finalizing attendance:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to finalize attendance: ' + error.message,
                    confirmButtonText: 'OK'
                });
            })
            .finally(() => {
                // Re-enable submit button
                submitButton.disabled = false;
            });
        }
    });
});

    // Event dropdown change handler
    eventDropdown.addEventListener("change", function() {
        currentEventId = this.value;
        const selectedEvent = eventMap[currentEventId];
        
        if (selectedEvent) {
            timeDurationInput.value = selectedEvent.time_duration;
            fetchStudents(yearLevelDropdown.value);
            submitButton.style.display = 'block';
        } else {
            timeDurationInput.value = "";
            attendanceTableBody.innerHTML = "";
            submitButton.style.display = 'none';
        }
    });

    // Year Level dropdown change handler
    yearLevelDropdown.addEventListener("change", function() {
        if (currentEventId) {
            fetchStudents(this.value);
        }
    });

    // Modal functionality
    const modal = document.getElementById("reportModal");
    const generateReportBtn = document.getElementById("generateReport");
    const span = document.getElementsByClassName("close")[0];
    const printReportBtn = document.getElementById("printReport");
    const reportEventSelect = document.getElementById("reportEventSelect");

    // Open modal when Generate Report button is clicked
    generateReportBtn.onclick = function() {
        modal.style.display = "block";
        populateReportEvents();
    }

    // Close modal when X is clicked
    span.onclick = function() {
        modal.style.display = "none";
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Function to populate events in the report modal
    function populateReportEvents() {
        // Clear existing options
        reportEventSelect.innerHTML = '<option value="">-- Select Event --</option>';
        
        // Use the existing eventMap to populate the select
        for (let eventId in eventMap) {
            let event = eventMap[eventId];
            let option = document.createElement("option");
            option.value = event.event_id;
            option.textContent = event.event_name;
            reportEventSelect.appendChild(option);
        }
    }

    // Print report button click handler
    printReportBtn.onclick = function() {
        const selectedEvent = reportEventSelect.value;
        const selectedYearLevel = document.getElementById("reportYearLevel").value;
        
        if (!selectedEvent) {
            alert("Please select an event");
            return;
        }

        // Create URL with parameters
        const url = `/print-attendance-report?event_id=${selectedEvent}&year_level=${selectedYearLevel}`;
        
        // Open in new window/tab
        window.open(url, '_blank');
    }

    // Logs Modal functionality
    const logsModal = document.getElementById("logsModal");
    const showLogsBtn = document.getElementById("showLogs");
    const closeLogsBtn = document.getElementsByClassName("close-logs")[0];
    const logsEventSelect = document.getElementById("logsEventSelect");
    const logsYearLevel = document.getElementById("logsYearLevel");
    const logsTableBody = document.getElementById("logsTableBody");

    // Open logs modal
    showLogsBtn.onclick = function() {
        logsModal.style.display = "block";
        populateLogsEvents();
        if (logsEventSelect.value) {
            fetchAttendanceLogs();
        }
    }

    // Close logs modal
    closeLogsBtn.onclick = function() {
        logsModal.style.display = "none";
    }

    // Close when clicking outside
    window.onclick = function(event) {
        if (event.target == logsModal) {
            logsModal.style.display = "none";
        }
    }

    // Populate events in logs modal
    function populateLogsEvents() {
        logsEventSelect.innerHTML = '<option value="">-- Select Event --</option>';
        
        for (let eventId in eventMap) {
            let event = eventMap[eventId];
            let option = document.createElement("option");
            option.value = event.event_id;
            option.textContent = event.event_name;
            logsEventSelect.appendChild(option);
        }
    }

    // Fetch attendance logs
    function fetchAttendanceLogs() {
        const selectedEvent = logsEventSelect.value;
        const selectedYearLevel = logsYearLevel.value;
        
        if (!selectedEvent) {
            logsTableBody.innerHTML = '';
            updatePaginationControls(); // Update pagination even when no event is selected
            return;
        }

        // Show loading state
        logsTableBody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Loading...</td></tr>';

        fetch(`/get-attendance-logs?event_id=${selectedEvent}&year_level=${selectedYearLevel}`)
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    allLogs = result.data || []; // Store all logs, default to empty array if no data
                    currentPage = 1; // Reset to first page
                    displayLogsPage(); // Display first page
                    updatePaginationControls();
                } else {
                    throw new Error(result.message || 'Failed to fetch logs');
                }
            })
            .catch(error => {
                console.error('Error fetching logs:', error);
                logsTableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; color: red;">
                    Error loading logs: ${error.message}</td></tr>`;
                allLogs = []; // Reset logs array on error
                updatePaginationControls(); // Update pagination to show no pages
            });
    }

    function displayLogsPage() {
        if (!allLogs || allLogs.length === 0) {
            logsTableBody.innerHTML = `<tr><td colspan="7" style="text-align: center;">
                No attendance records found</td></tr>`;
            return;
        }

        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const pageData = allLogs.slice(start, end);
        
        logsTableBody.innerHTML = '';
        
        pageData.forEach(log => {
            let row = document.createElement('tr');
            
            // Create main row with attendance status
            row.innerHTML = `
                <td>${formatDate(log.attendance_date)}</td>
                <td>${log.student_id}</td>
                <td>${log.last_name}, ${log.first_name} ${log.middle_name ? log.middle_name.charAt(0) + '.' : ''}</td>
                <td>${log.year_level}</td>
            `;

            // Add attendance columns based on event duration
            switch (log.time_duration) {
                case 'Half Day: Morning':
                    row.innerHTML += `
                        <td>${log.am_in ? '✓' : '-'}</td>
                        <td>${log.am_out ? '✓' : '-'}</td>
                    `;
                    break;
                case 'Half Day: Afternoon':
                    row.innerHTML += `
                        <td>${log.pm_in ? '✓' : '-'}</td>
                        <td>${log.pm_out ? '✓' : '-'}</td>
                    `;
                    break;
                case 'Whole Day':
                    row.innerHTML += `
                        <td>${log.am_in ? '✓' : '-'}</td>
                        <td>${log.am_out ? '✓' : '-'}</td>
                        <td>${log.pm_in ? '✓' : '-'}</td>
                        <td>${log.pm_out ? '✓' : '-'}</td>
                    `;
                    break;
            }

            // Add action history
            let actionHistoryHtml = '';
            if (log.action_history && log.action_history.length > 0) {
                actionHistoryHtml = `
                    <td>
                        <div class="action-logs-container">
                            ${log.action_history.map(action => `
                                <div class="action-log">
                                    <div class="action-type">
                                        ${formatActionType(action.action_type)}: 
                                        <span class="${action.action === 'checked' ? 'checked' : 'unchecked'}">
                                            ${action.action}
                                        </span>
                                    </div>
                                    <div class="action-details">
                                        By: ${action.performed_by}<br>
                                        On: ${formatDateTime(action.action_timestamp)}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </td>
                `;
            } else {
                actionHistoryHtml = '<td>No changes logged</td>';
            }
            row.innerHTML += actionHistoryHtml;
            
            logsTableBody.appendChild(row);
        });
    }

    // Format date for display
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Format date and time for display
    function formatDateTime(timestamp) {
        if (!timestamp) return '-';
        const date = new Date(timestamp);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }

    // Format action type for display
    function formatActionType(actionType) {
        const types = {
            'am_in': 'Morning In',
            'am_out': 'Morning Out',
            'pm_in': 'Afternoon In',
            'pm_out': 'Afternoon Out'
        };
        return types[actionType] || actionType;
    }

    // Get time in status
    function getTimeIn(log) {
        if (log.time_duration.includes('Morning')) {
            return log.am_in ? '✓' : '-';
        } else if (log.time_duration.includes('Afternoon')) {
            return log.pm_in ? '✓' : '-';
        } else {
            return [
                log.am_in ? '✓' : '-',
                log.pm_in ? '✓' : '-'
            ].filter(time => time !== '-').join(', ') || '-';
        }
    }

    // Get time out status
    function getTimeOut(log) {
        if (log.time_duration.includes('Morning')) {
            return log.am_out ? '✓' : '-';
        } else if (log.time_duration.includes('Afternoon')) {
            return log.pm_out ? '✓' : '-';
        } else {
            return [
                log.am_out ? '✓' : '-',
                log.pm_out ? '✓' : '-'
            ].filter(time => time !== '-').join(', ') || '-';
        }
    }

    // Event listeners for filters
    logsEventSelect.addEventListener('change', fetchAttendanceLogs);
    logsYearLevel.addEventListener('change', fetchAttendanceLogs);

    // Add this function to handle pagination controls
    function updatePaginationControls() {
        const totalPages = Math.ceil(allLogs.length / rowsPerPage);
        const currentPageSpan = document.getElementById('currentPage');
        const totalPagesSpan = document.getElementById('totalPages');
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');

        if (currentPageSpan) currentPageSpan.textContent = currentPage;
        if (totalPagesSpan) totalPagesSpan.textContent = totalPages;
        
        if (prevButton) prevButton.disabled = currentPage === 1;
        if (nextButton) nextButton.disabled = currentPage === totalPages || totalPages === 0;
    }

    // Add event listeners for pagination buttons
    document.addEventListener('DOMContentLoaded', function() {
        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');

        if (prevButton) {
            prevButton.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    displayLogsPage();
                    updatePaginationControls();
                }
            });
        }

        if (nextButton) {
            nextButton.addEventListener('click', () => {
                const totalPages = Math.ceil(allLogs.length / rowsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    displayLogsPage();
                    updatePaginationControls();
                }
            });
        }
    });
});
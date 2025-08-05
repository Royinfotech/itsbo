<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Approval</title>
    <link rel="stylesheet" href="{{ asset('assets/css/Approval.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Fix SweetAlert z-index to appear above modal */
        .swal2-container {
            z-index: 99999 !important;
        }
        
        /* Ensure modal has a lower z-index */
        .modal {
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <h2>Students Pending Approval</h2>
        <div class="table-wrapper">
            <table id="approvalTable">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Year Level</th>
                        <th>Birthdate</th>
                        <th>Age</th>
                        <th>Birthplace</th>
                        <th>Action</th>
                        
                    </tr>
                </thead>
                <tbody id="approvalList">
                    @foreach($students as $student)
                    <tr data-id="{{ $student->id }}" 
                    data-studentname="{{ $student->student_name }}"
                    data-email="{{ $student->email }}"
                    data-yearlevel="{{ $student->year_level }}"
                    data-birthdate="{{ $student->birthdate }}"
                    data-age="{{ $student->age }}"
                    data-birthplace="{{ $student->birthplace }}">
                        <td>{{ $student->student_id }}</td>
                        <td>{{ $student->student_name }}</td>
                        <td>{{ $student->year_level }}</td>
                        <td>{{ $student->birthdate }}</td>
                        <td>{{ $student->age }}</td>
                        <td>{{ $student->birthplace }}</td>

                        <td>
                            <button class="approve-btn" data-id="{{ $student->id }}">Approve</button>
                            <button class="decline-btn" data-id="{{ $student->id }}">Decline</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="studentApprovalModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Student Details</h2>
            <div class="form-grid">
    <div class="form-group"><label>Student Name:</label><input type="text" id="modalStudentName" readonly></div>
    <div class="form-group"><label>Email Address:</label><input type="email" id="modalEmail" readonly></div>
    <div class="form-group"><label>Year Level:</label><input type="text" id="modalYearLevel" readonly></div>
    <div class="form-group"><label>Birthdate:</label><input type="date" id="modalBirthdate" readonly></div>
    <div class="form-group"><label>Age:</label><input type="number" id="modalAge" readonly></div>
    <div class="form-group"><label>Birthplace:</label><input type="text" id="modalBirthplace" readonly></div>
</div>
<div class="modal-actions">
    <button id="editBtn" type="button">Edit</button>
    <button id="saveBtn" type="button" style="display:none;">Save</button>
    <button id="cancelBtn" type="button" style="display:none;">Cancel</button>
</div>
        </div>
    </div>

    <script>
        function calculateAge(birthdate) {
            const birth = new Date(birthdate);
            const today = new Date();
            let age = today.getFullYear() - birth.getFullYear();
            const m = today.getMonth() - birth.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
                age--;
            }
            return age;
        }

        $(document).ready(function () {
            // Show student details in modal
$("#approvalList").on("click", "tr", function (e) {
    if (!$(e.target).hasClass("approve-btn") && !$(e.target).hasClass("decline-btn")) {
        const row = $(this);
        $("#modalStudentName").val(row.data("studentname"));
        $("#modalEmail").val(row.data("email"));
        $("#modalYearLevel").val(row.data("yearlevel"));
        $("#modalBirthdate").val(row.data("birthdate"));
        $("#modalAge").val(row.data("age"));
        $("#modalBirthplace").val(row.data("birthplace"));
        
        // Store the student ID for editing
        $("#studentApprovalModal").data("student-id", row.data("id"));
        
        $("#studentApprovalModal").show();
    }
});

            // Close modal functionality
            $(".close").click(function() {
                $("#studentApprovalModal").hide();
                // Reset form to readonly state
                $("#modalStudentName, #modalEmail, #modalYearLevel, #modalBirthdate, #modalAge, #modalBirthplace").prop("readonly", true);
                $("#editBtn").show();
                $("#saveBtn, #cancelBtn").hide();
            });

            // Close modal when clicking outside of it
            $(window).click(function(event) {
                if (event.target.id === "studentApprovalModal") {
                    $("#studentApprovalModal").hide();
                    // Reset form to readonly state
                    $("#modalStudentName, #modalEmail, #modalYearLevel, #modalBirthdate, #modalAge, #modalBirthplace").prop("readonly", true);
                    $("#editBtn").show();
                    $("#saveBtn, #cancelBtn").hide();
                }
            });

            // Set up AJAX CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Approve button handler
            $('.approve-btn').click(function(e) {
                e.preventDefault();
                const studentId = $(this).data('id');
                const row = $(this).closest('tr');
                const studentName = row.data('studentname');

                Swal.fire({
                    title: 'Confirm Approval',
                    text: `Are you sure you want to approve ${studentName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, approve!',
                    customClass: {
                        container: 'swal-high-z-index'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/secretary/approve-student/${studentId}`,
                            type: 'POST',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Approved!',
                                    text: 'Student has been approved successfully.',
                                    icon: 'success',
                                    customClass: {
                                        container: 'swal-high-z-index'
                                    }
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message || 'Failed to approve student',
                                    icon: 'error',
                                    customClass: {
                                        container: 'swal-high-z-index'
                                    }
                                });
                            }
                        });
                    }
                });
            });

            // Edit functionality
let originalData = {};

$("#editBtn").click(function() {
    // Store original data
    originalData = {
        studentName: $("#modalStudentName").val(),
        email: $("#modalEmail").val(),
        yearLevel: $("#modalYearLevel").val(),
        birthdate: $("#modalBirthdate").val(),
        age: $("#modalAge").val(),
        birthplace: $("#modalBirthplace").val()
    };
    
    // Enable editing
    $("#modalStudentName, #modalEmail, #modalYearLevel, #modalBirthdate, #modalAge, #modalBirthplace").prop("readonly", false);
    $("#editBtn").hide();
    $("#saveBtn, #cancelBtn").show();
});

$("#cancelBtn").click(function() {
    // Restore original data
    $("#modalStudentName").val(originalData.studentName);
    $("#modalEmail").val(originalData.email);
    $("#modalYearLevel").val(originalData.yearLevel);
    $("#modalBirthdate").val(originalData.birthdate);
    $("#modalAge").val(originalData.age);
    $("#modalBirthplace").val(originalData.birthplace);
    
    // Disable editing
    $("#modalStudentName, #modalEmail, #modalYearLevel, #modalBirthdate, #modalAge, #modalBirthplace").prop("readonly", true);
    $("#editBtn").show();
    $("#saveBtn, #cancelBtn").hide();
});

$("#saveBtn").click(function() {
    const studentId = $("#studentApprovalModal").data("student-id");
    const updatedData = {
        student_name: $("#modalStudentName").val(),
        email: $("#modalEmail").val(),
        year_level: $("#modalYearLevel").val(),
        birthdate: $("#modalBirthdate").val(),
        age: $("#modalAge").val(),
        birthplace: $("#modalBirthplace").val()
    };
    
    $.ajax({
        url: `/secretary/update-student/${studentId}`,
        type: 'PUT',
        data: updatedData,
        success: function(response) {
            Swal.fire({
                title: 'Updated!',
                text: 'Student data has been updated successfully.',
                icon: 'success',
                customClass: {
                    container: 'swal-high-z-index'
                }
            }).then(() => {
                location.reload();
            });
        },
        error: function(xhr) {
            Swal.fire({
                title: 'Error!',
                text: xhr.responseJSON?.message || 'Failed to update student',
                icon: 'error',
                customClass: {
                    container: 'swal-high-z-index'
                }
            });
        }
    });
});

            // Decline button handler
            $('.decline-btn').click(function(e) {
                e.preventDefault();
                const studentId = $(this).data('id');
                const row = $(this).closest('tr');
                const studentName = row.data('studentname');

                Swal.fire({
                    title: 'Confirm Decline',
                    text: `Are you sure you want to decline ${studentName}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, decline!',
                    customClass: {
                        container: 'swal-high-z-index'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/secretary/decline-student/${studentId}`,
                            type: 'POST',
                            success: function(response) {
                                Swal.fire({
                                    title: 'Declined!',
                                    text: 'Student has been declined.',
                                    icon: 'success',
                                    customClass: {
                        container: 'swal-high-z-index'
                    }
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message || 'Failed to decline student',
                                    icon: 'error',
                                    customClass: {
                        container: 'swal-high-z-index'
                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
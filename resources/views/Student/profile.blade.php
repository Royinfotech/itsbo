<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* === General Styles === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* === Profile Card === */
        .profile-container {
            background: #fff;
            width: 800px;
            max-width: 100%;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-top: 8px solid maroon;
        }

        .profile-header {
            text-align: center;
            font-size: 24px;
            color: maroon;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* === Profile Picture === */
        .profile-pic-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-pic-container img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid maroon;
            object-fit: cover;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        /* === Info Rows === */
        .profile-info .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .info-row label {
            font-weight: bold;
            color: #555;
            width: 130px;
        }

        .info-row p {
            color: #333;
            flex: 1;
            border-bottom: 2px solid maroon;
            padding: 5px 0;
            margin: 0;
        }

        /* === Button === */
        .edit-profile-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50%;
            margin: 20px auto 0;
            padding: 12px;
            background: maroon;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .edit-profile-btn:hover {
            background: #600000;
        }

        /* === Modal === */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            overflow-y: auto;
        }

        .modal.show {
            display: block;
        }

        .modal-content {
            background: #fff;
            margin: 5% auto;
            width: 90%;
            max-width: 500px;
            border-radius: 10px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header, .modal-footer {
            padding: 15px 20px;
            background: #800000;
            color: #fff;
            border-radius: 10px 10px 0 0;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .btn-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            margin: 0;
        }

        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background: #f9f9f9;
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
        
        .password-field {
            position: relative;
        }

        .password-input-group {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 5px;
        }

        .password-toggle:hover {
            color: maroon;
        }

        .form-group input[type="password"] {
            padding-right: 35px;
        }

        .photo-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-profile-pic {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid maroon;
            margin-bottom: 10px;
        }

        .photo-upload-btn {
            background: maroon;
            color: #fff;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .photo-upload-btn:hover {
            background: #600000;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            margin-left: 10px;
        }

        .btn-primary {
            background-color: maroon;
            color: white;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        /* === Responsive === */
        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-row label {
                margin-bottom: 4px;
            }
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <div class="profile-header"><i class="fas fa-user"></i> Student Profile</div>

        <div class="profile-pic-container">
            <img id="profilePic" src="{{ $student->photo ? asset('storage/' . $student->photo) : asset('assets/pictures/default-student.jpg') }}" alt="Profile Picture">
        </div>

        <div class="profile-info">
            <div class="info-row">
                <label>Student ID</label>
                <p>{{ $student->student_id }}</p>
            </div>
            <div class="info-row">
                <label>Name</label>
                <p>{{ $student->student_name }}</p>
            </div>
            <div class="info-row">
                <label>Year Level</label>
                <p>{{ $student->year_level }}</p>
            </div>
            <div class="info-row">
                <label>Email</label>
                <p>{{ $student->email }}</p>
            </div>

            <div class="button-group">
                <button id="editProfileBtn" class="edit-profile-btn">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <button id="changePasswordBtn" class="edit-profile-btn" style="margin-top: 10px; background: #8B0000;">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-key"></i> Change Password</h2>
                <button class="btn-close" id="closePasswordModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="form-group password-field">
                        <label>Current Password</label>
                        <div class="password-input-group">
                            <input type="password" id="currentPassword" name="current_password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('currentPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group password-field">
                        <label>New Password</label>
                        <div class="password-input-group">
                            <input type="password" id="newPassword" name="new_password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('newPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group password-field">
                        <label>Confirm New Password</label>
                        <div class="password-input-group">
                            <input type="password" id="confirmPassword" name="confirm_password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" id="cancelPasswordBtn"><i class="fas fa-times"></i> Cancel</button>
                <button class="btn btn-primary" type="button" id="savePasswordBtn"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Profile Edit Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                <button class="btn-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div class="photo-section">
                        <img id="modalProfilePic" src="{{ $student->photo ? asset('storage/' . $student->photo) : asset('assets/pictures/default-student.jpg') }}" class="modal-profile-pic" alt="Profile Picture">
                        <br>
                        <input type="file" id="photoInput" name="photo" accept="image/*" hidden>
                        <button type="button" id="changePhotoBtn" class="photo-upload-btn"><i class="fas fa-camera"></i> Change Photo</button>
                        <p style="color: #555; font-size: 12px;">Max 2MB | JPG, PNG, GIF</p>
                    </div>

                    <div class="form-group">
                        <label>Student ID</label>
                        <input type="text" value="{{ $student->student_id }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" value="{{ $student->student_name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="{{ $student->email }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Year Level</label>
                        <input type="text" value="{{ $student->year_level }}" disabled>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" id="cancelBtn"><i class="fas fa-times"></i> Cancel</button>
                <button class="btn btn-primary" type="button" id="saveBtn"><i class="fas fa-save"></i> Save</button>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = event.currentTarget.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Profile Edit Modal Elements
            const modal = document.getElementById('editProfileModal');
            const editBtn = document.getElementById('editProfileBtn');
            const closeBtn = document.getElementById('closeModal');
            const cancelBtn = document.getElementById('cancelBtn');
            const saveBtn = document.getElementById('saveBtn');
            const changePhotoBtn = document.getElementById('changePhotoBtn');
            const photoInput = document.getElementById('photoInput');
            const modalPic = document.getElementById('modalProfilePic');
            const mainPic = document.getElementById('profilePic');

            // Password Modal Elements
            const passwordModal = document.getElementById('passwordModal');
            const changePasswordBtn = document.getElementById('changePasswordBtn');
            const closePasswordBtn = document.getElementById('closePasswordModal');
            const cancelPasswordBtn = document.getElementById('cancelPasswordBtn');
            const savePasswordBtn = document.getElementById('savePasswordBtn');
            const passwordForm = document.getElementById('changePasswordForm');

            let selectedFile = null;

            function openModal() {
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                modalPic.src = mainPic.src;
            }

            function closeModal() {
                modal.classList.remove('show');
                document.body.style.overflow = 'auto';
                selectedFile = null;
                modalPic.src = mainPic.src;
            }

            editBtn?.addEventListener('click', openModal);
            closeBtn?.addEventListener('click', closeModal);
            cancelBtn?.addEventListener('click', closeModal);

            window.addEventListener('click', e => {
                if (e.target === modal) closeModal();
            });

            changePhotoBtn?.addEventListener('click', () => photoInput.click());

            photoInput?.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File',
                        text: 'Please upload a valid image file.',
                        confirmButtonColor: 'maroon'
                    });
                    this.value = '';
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Too Large',
                        text: 'Image must be under 2MB.',
                        confirmButtonColor: 'maroon'
                    });
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = e => modalPic.src = e.target.result;
                reader.readAsDataURL(file);
                selectedFile = file;
            });

            saveBtn?.addEventListener('click', () => {
                if (!selectedFile) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Changes',
                        text: 'No new image selected.',
                        confirmButtonColor: 'maroon'
                    });
                    return;
                }

                const formData = new FormData();
                formData.append('photo', selectedFile);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                // Show loading
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update your profile photo.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(`/student/profile/update-photo/{{ $student->student_id }}`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        mainPic.src = modalPic.src;
                        Swal.fire({
                            icon: 'success',
                            title: 'Profile Updated',
                            text: 'Photo updated successfully!',
                            confirmButtonColor: 'maroon'
                        }).then(() => {
                            closeModal();
                        });
                    } else {
                        throw new Error(data.message || 'Update failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'An error occurred while updating your photo.',
                        confirmButtonColor: 'maroon'
                    });
                });
            });

            // Password Modal Functions
            function openPasswordModal() {
                passwordModal.classList.add('show');
                document.body.style.overflow = 'hidden';
                passwordForm.reset();
            }

            function closePasswordModal() {
                passwordModal.classList.remove('show');
                document.body.style.overflow = 'auto';
                passwordForm.reset();
            }

            changePasswordBtn?.addEventListener('click', openPasswordModal);
            closePasswordBtn?.addEventListener('click', closePasswordModal);
            cancelPasswordBtn?.addEventListener('click', closePasswordModal);

            window.addEventListener('click', e => {
                if (e.target === passwordModal) closePasswordModal();
            });

            savePasswordBtn?.addEventListener('click', () => {
                const currentPassword = document.getElementById('currentPassword').value.trim();
                const newPassword = document.getElementById('newPassword').value.trim();
                const confirmPassword = document.getElementById('confirmPassword').value.trim();

                // Basic validation
                if (!currentPassword || !newPassword || !confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'All fields are required',
                        text: 'Please fill in all password fields.',
                        confirmButtonColor: 'maroon'
                    });
                    return;
                }

                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Passwords do not match',
                        text: 'New password and confirmation must match.',
                        confirmButtonColor: 'maroon'
                    });
                    return;
                }

                if (newPassword.length < 8) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Password too short',
                        text: 'New password must be at least 8 characters long.',
                        confirmButtonColor: 'maroon'
                    });
                    return;
                }

                // Show loading
                Swal.fire({
                    title: 'Updating Password...',
                    text: 'Please wait while we update your password.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send password change request
                fetch(`/student/change-password/{{ $student->student_id }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        // Try to get error message from response
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                        }).catch(() => {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json(); // FIXED: Added parentheses
                })
                .then(data => {
                    if (data && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message || 'Password updated successfully!',
                            confirmButtonColor: 'maroon'
                        }).then(() => {
                            closePasswordModal();
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update password');
                    }
                })
                .catch(error => {
                    console.error('Password change error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to update password. Please try again.',
                        confirmButtonColor: 'maroon'
                    });
                });
            });
        });
    </script>

</body>
</html>
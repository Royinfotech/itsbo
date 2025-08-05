<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Student Profile</title>
    <link rel="stylesheet" href="{{ asset('assets/css/profile.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="profile-container">
        <!-- Profile Picture Section -->
        <div class="profile-pic-container">
            <img id="profilePic" 
                 src="{{ $student->photo ? asset('storage/' . $student->photo) : asset('assets/pictures/default-student.jpg') }}" 
                 alt="Profile Picture">
            <form id="photoForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" id="photoInput" name="photo" accept="image/*" style="display: none;">
                <button type="button" onclick="document.getElementById('photoInput').click()" class="photo-btn">
                    <i class="fas fa-camera"></i> Change Photo
                </button>
            </form>
        </div>

        <!-- Student Information -->
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
        </div>
    </div>

    <script src="{{ asset('assets/js/profile.js') }}"></script>
    <script>
    document.getElementById('photoInput').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            // Show loading alert
            Swal.fire({
                title: 'Uploading...',
                text: 'Please wait while we update your photo',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('photo', this.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('/student/profile/update-photo/{{ $student->student_id }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('profilePic').src = URL.createObjectURL(this.files[0]);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Your profile photo has been updated',
                        confirmButtonColor: '#800000'
                    });
                } else {
                    throw new Error(data.error || 'Failed to update photo');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Failed to update photo',
                    confirmButtonColor: '#800000'
                });
                console.error('Error:', error);
            });
        }
    });
    </script>
</body>
</html>

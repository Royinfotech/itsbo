<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="MyWebSite" />
    <link rel="manifest" href="/site.webmanifest" />
  <style>

body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    background: url('assets/pictures/LoginBackg.png') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
}

.main-container {
  width: 700px;
  max-width: 100%;
  height: 700px;
  border-radius: 20px;
  overflow: hidden;
  display: flex;
  box-shadow: 0px 20px 30px rgba(0, 0, 0, 0.2);
}

.logo-side {
  background: linear-gradient(120deg, #800000, #660000);
  width: 28%;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 10px;
}

.login-side {
  width: 72%;
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 5px;
  position: relative;
}

/* Modal Background */
.modal-alert {
  display: none;
  position: fixed;
  z-index: 9999;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
}

/* Modal Box */
.modal-box {
  background: #fff0f0;
  border-top: 6px solid #800000;
  color: #800000;
  padding: 20px 30px;
  border-radius: 12px;
  max-width: 400px;
  width: 90%;
  box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  position: relative;
  animation: fadeInScale 0.3s ease;
}

/* Close Button */
.modal-close {
  position: absolute;
  top: 10px;
  right: 15px;
  color: #800000;
  font-size: 22px;
  font-weight: bold;
  cursor: pointer;
}

/* Fade In Animation */
@keyframes fadeInScale {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

/* Password Input Container */
.password-container {
  position: relative;
  display: flex;
  align-items: center;
}

.password-container .form-control {
  padding-right: 45px;
}

.password-toggle {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #800000;
  font-size: 16px;
  z-index: 10;
  padding: 5px;
  transition: color 0.3s ease;
}

.password-toggle:hover {
  color: #660000;
}

.login-content {
  width: 98%;
  padding-top: 10px;
  padding-bottom: 50px;
  margin-left: 20px;
  min-height: 660px;
  margin-right: 20px;
}

.text-center {
  text-align: center;
  margin-top: 60px;
  margin-bottom: 10px;
}

.itsbo {

  color: #1b1a1a;
  font-weight: 900;
  font-size: 30px;
  
}

.gateway {
  color: #800000;
  font-weight: 700;
  font-family: 'Great Vibes', cursive;
  font-size: 30px;
}

.flip-container {
  perspective: 2000px;
  width: 100%;
  height: 100%;
  position: relative;
}

.flip-card {
  width: 95%;
  height: 100%;
  transition: transform 0.8s;
  transform-style: preserve-3d;
  position: relative;
}

.flip-card.flip {
  transform: rotateY(180deg);
}

.form-page {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
  padding: 10px;
  background: white;
}

.form-page.back {
  transform: rotateY(180deg);
}

.form-group {
  margin-bottom: 15px;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.form-label {
  display: block;
  font-weight: bold;
  font-size: 14px;
  margin-bottom: 5px;
  text-align: left;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 2px solid #eee;
  border-radius: 12px;
  font-size: 14px;
  background: #f8f8f8;
  box-sizing: border-box;
}

.form-control.error {
  border-color: #dc3545;
  background-color: #fff5f5;
  box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.form-control:focus {
  border-color: #800000;
  background: white;
  box-shadow: 0 0 0 3px rgba(128, 0, 0, 0.1);
}

/* Photo Upload Styles */
.photo-upload-container {
  text-align: center;
  margin-bottom: 10px;
}

.photo-upload-area {
  width: 100px;
  height: 100px;
  border: 3px dashed #800000;
  border-radius: 50%;
  margin: 0 auto 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  overflow: hidden;
  position: relative;
}

.photo-upload-area:hover {
  border-color: #660000;
  background-color: rgba(128, 0, 0, 0.05);
}

.photo-upload-area.has-image {
  border-style: solid;
  border-width: 2px;
}

.photo-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
}

.upload-placeholder {
  text-align: center;
  color: #800000;
}

.upload-placeholder i {
  font-size: 30px;
  margin-bottom: 5px;
  display: block;
}

.upload-placeholder span {
  font-size: 12px;
  font-weight: 400;
}

.photo-upload-input {
  display: none;
}

.photo-upload-text {
  font-size: 12px;
  color: #666;
  margin-top: 5px;
}

.btn {
  margin-top: 5px;
  padding: 9px 15px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, #800000, #660000);
  color: white;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
}

.logo-container {
  perspective: 1000px;
  width: 120px;
  height: 120px;
  margin: auto;
}

.logo {
  width: 100%;
  height: 100%;
  position: relative;
  transform-style: preserve-3d;
  animation: logoRotate 3s ease-in-out infinite;
}

@keyframes logoRotate {
  0% { transform: rotateY(0deg); }
  50% { transform: rotateY(180deg); }
  100% { transform: rotateY(360deg); }
}

.logo-front, .logo-back {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
  border-radius: 60%;
  overflow: hidden;
}

.logo-back {
  transform: rotateY(180deg);
}

.logo-front img, .logo-back img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.logo:hover {
  animation-play-state: paused;
}

@media (max-width: 991px) {
  .main-container {
    flex-direction: column;
    width: 95%;
    height: auto;
  }

  .logo-side {
    width: 100%;
    height: auto;
    padding: 15px;
  }

  .login-side {
    width: 100%;
    padding: 15px;
  }

  .login-content {
    min-height: auto;
    padding: 20px;
    margin: 0;
  }

  .itsbo {
    font-size: 20px;
  }

  .gateway {
    font-size: 32px;
  }

  .form-control {
    font-size: 13px;
    padding: 9px;
  }

  .btn {
    font-size: 14px;
    padding: 10px 14px;
  }

  .logo-container {
    width: 70px;
    height: 70px;
  }

  .flip-container,
  .flip-card {
    height: auto;
  }

  .form-page {
    position: relative;
    transform: none !important;
    backface-visibility: visible !important;
  }

  .flip-card {
    transform: none !important;
  }

  .flip-card.flip .form-page.front {
    display: none;
  }

  .flip-card:not(.flip) .form-page.back {
    display: none;
  }

  .photo-upload-area {
    width: 70px;
    height: 70px;
  }
}

@media (max-width: 576px) {
  .main-container {
    flex-direction: column;
    width: 100%;
    height: auto;
    padding: 10px;
  }

  .itsbo {
    font-size: 20px;
  }

  .gateway {
    font-size: 28px;
  }

  .form-control {
    font-size: 12px;
    padding: 8px;
  }

  .btn {
    font-size: 13px;
    padding: 8px 12px;
  }

  .form-label {
    font-size: 13px;
  }

  .photo-upload-area {
    width: 60px;
    height: 60px;
  }

  .login-content {
    padding: 15px;
  }
}

.notification {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 15px 25px;
  color: white;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  z-index: 1000;
  font-size: 14px;
  font-weight: 500;
  display: none;
}

.notification.success {
  background: linear-gradient(135deg, #006400, #800000);
}

.notification.error {
  background: linear-gradient(135deg, #800000, #660000);
}

.notification.show {
  display: block;
}

/* Additional styles for select elements */
select.form-control optgroup {
  font-weight: bold;
  color: #800000;
  background-color: #f8f8f8;
}

select.form-control option {
  font-weight: normal;
  padding: 8px;
}

select.form-control option:hover {
  background-color: #800000;
  color: white;
}

</style>

</head>
<body>

@if(session('success'))
<div class="notification success show" id="successNotification">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="notification error show" id="errorNotification">
    {{ $errors->first() }}
</div>
@endif

<div class="modal-alert" id="modalAlert">
  <div class="modal-box">
    <span class="modal-close" onclick="closeModal()">&times;</span>
    <h4 style="margin-top: 0;">Incomplete Information</h4>
    <p>Please fill out all required fields before proceeding to the next step.</p>
  </div>
</div>


<div class="main-container">
  <div class="logo-side">
    <div class="logo-container">
      <div class="logo">
        <div class="logo-front">
          <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo Front">
        </div>
        <div class="logo-back">
          <img src="{{ asset('assets/pictures/itsbo.png') }}" alt="ITSBO Logo Back">
        </div>
      </div>
    </div>
  </div>

  <div class="login-side">
    <div class="login-content">

      <div class="text-center mb-4">
        <h1 class="itsbo">IT Student <span class="gateway">Register</span></h1>
      </div>

      <div class="flip-container">
        <div class="flip-card" id="flipCard">
           

          <!-- Page 1 -->
          <div class="form-page front">
            <form id="page1Form">
              @csrf
              
              <div class="form-group">
                <label class="form-label">Student Name (FirstName/LastName/M.I.)</label>
                <input type="text" name="student_name" class="form-control" value="{{ old('student_name') }}" required>
              </div>
              <div class="form-group">
                <label class="form-label">iLearn ID (LMS ID)</label>
               <input type="text" name="student_id" class="form-control" value="{{ old('student_id') }}" 
                pattern="[0-9]+" 
                title="Please enter numbers only"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                required>
              </div>
              <div class="form-group">
                <label class="form-label">Year Level (Accepted Year Level: 1,2,3,4)</label>
                <input type="number" name="year_level" class="form-control" value="{{ old('year_level') }}" required>
              </div>
              <div class="form-group">
                <label class="form-label">Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" class="form-control" value="{{ old('birthdate') }}" required>
              </div>
              <div class="form-group">
                <label class="form-label">Age (Auto-Calc)</label>
                <input type="text" id="age" class="form-control" value="{{ old('age') }}" readonly>
              </div>
              <div class="form-group">
                <label class="form-label">Birthplace (City or Municipality Only)</label>
                <input type="text" name="birthplace" class="form-control" value="{{ old('birthplace') }}" required 
                       pattern="^[a-zA-Z\s,.-]*$"
                       title="Please enter valid location (letters, spaces, commas, periods, and hyphens only)">
              </div>


              <button type="button" class="btn" onclick="nextPage()">Next ➡️</button>
            </form>
          </div>

          <!-- Page 2 -->
          <div class="form-page back">
            <form method="POST" action="{{ route('register.post') }}" enctype="multipart/form-data" id="registrationForm">
              @csrf
              <!-- Hidden fields -->
              <input type="hidden" name="student_name" id="hidden_student_name" value="{{ old('student_name') }}">
              <input type="hidden" name="student_id" id="hidden_student_id" value="{{ old('student_id') }}">
              <input type="hidden" name="year_level" id="hidden_year_level" value="{{ old('year_level') }}">
              <input type="hidden" name="birthdate" id="hidden_birthdate" value="{{ old('birthdate') }}">
              <input type="hidden" name="age" id="hidden_age" value="{{ old('age') }}">
              <input type="hidden" name="birthplace" id="hidden_birthplace" value="{{ old('birthplace') }}">
              
              <!-- Rest of your form fields -->
              
              <!-- Photo Upload Section -->
              <div class="photo-upload-container">
                <div class="photo-upload-area" onclick="document.getElementById('photoInput').click()">
                  <div class="upload-placeholder" id="uploadPlaceholder">
                    <i class="fas fa-camera"></i>
                    <span>Upload Photo</span>
                  </div>
                  <img id="photoPreview" class="photo-preview" style="display: none;">
                </div>
                <input type="file" id="photoInput" name="photo" class="photo-upload-input" accept="image/*">
                <div class="photo-upload-text">
                  Click to upload your photo
                </div>
              </div>

              

              <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
              </div>
              <div class="form-group">
                <label class="form-label">Username</label>
                <input type="username" name="username" class="form-control" value="{{ old('username') }}" required>
              </div>
              <div class="form-group">
              <label class="form-label">Password</label>
              <div class="password-container">
              <input type="password" name="password" id="password" class="form-control"
                  pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*\W).{8,}" required
                title="Must contain at least 8 characters, including UPPER/lowercase, number and symbol">
               <i class="fas fa-eye password-toggle" id="passwordToggle"></i>
              </div>
              </div>
              <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <div class="password-container">
               <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
             <i class="fas fa-eye password-toggle" id="confirmPasswordToggle"></i>
             </div>
              </div>
              

              <div style="display: flex; justify-content: space-between;">
                <button type="button" class="btn" onclick="previousPage()">⬅️ Back</button>
                <button type="submit" class="btn">Register</button>
              </div>
              

            </form>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
  // Password visibility toggles state
let passwordStates = {
  password: false,
  password_confirmation: false
};

function togglePassword(inputId, toggleId) {
  const input = document.getElementById(inputId);
  const toggle = document.getElementById(toggleId);
  
  if (input.type === 'password') {
    input.type = 'text';
    toggle.classList.remove('fa-eye');
    toggle.classList.add('fa-eye-slash');
    passwordStates[inputId] = true;
  } else {
    input.type = 'password';
    toggle.classList.remove('fa-eye-slash');
    toggle.classList.add('fa-eye');
    passwordStates[inputId] = false;
  }
}

function preservePasswordStates() {
  // When going back to page 2, restore password visibility states
  Object.keys(passwordStates).forEach(inputId => {
    const input = document.getElementById(inputId);
    const toggleId = inputId === 'password' ? 'passwordToggle' : 'confirmPasswordToggle';
    const toggle = document.getElementById(toggleId);
    
    if (input && toggle) {
      if (passwordStates[inputId]) {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
      } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
      }
    }
  });
}
  const flipCard = document.getElementById('flipCard');

  // Check if there are old values and determine which page to show
  const hasOldValues = @json(old() ? true : false);
  const hasErrors = @json($errors->any());

  // Field mapping to determine which page contains the error
  const page1Fields = ['student_name', 'student_id', 'year_level', 'birthdate', 'age', 'birthplace'];
  const page2Fields = ['email', 'username', 'password', 'password_confirmation', 'photo'];

  function initializeForm() {
    if (hasOldValues && hasErrors) {
      // Restore all form data
      restoreFormData();
      
      // Determine which page has errors
      const errorFields = @json($errors->keys());
      const hasPage1Errors = errorFields.some(field => page1Fields.includes(field));
      const hasPage2Errors = errorFields.some(field => page2Fields.includes(field));
      
      // Show the appropriate page
      if (hasPage1Errors) {
        // Stay on page 1 and highlight errors
        flipCard.classList.remove('flip');
        highlightErrors(errorFields, 1);
      } else if (hasPage2Errors) {
        // Go to page 2 and highlight errors
        flipCard.classList.add('flip');
        setTimeout(preservePasswordStates, 100);
        highlightErrors(errorFields, 2);
      }
    } else if (hasOldValues) {
      // No errors but has old values, restore data
      restoreFormData();
    }

    // Calculate age if birthdate is present
    const birthdateInput = document.getElementById('birthdate');
    if (birthdateInput.value) {
      calculateAge();
    }
  }

  function restoreFormData() {
    const oldData = @json(old());
    
    // Restore page 1 data
    Object.keys(oldData).forEach(key => {
      const input = document.querySelector(`input[name="${key}"]`);
      const hiddenInput = document.querySelector(`input[name="${key}"]#hidden_${key}`);
      
      if (input && oldData[key]) {
        input.value = oldData[key];
      }
      if (hiddenInput && oldData[key]) {
        hiddenInput.value = oldData[key];
      }
    });
  }

  function highlightErrors(errorFields, page) {
    errorFields.forEach(field => {
      const input = document.querySelector(`input[name="${field}"]`);
      if (input) {
        input.classList.add('error');
        // Remove error class on focus
        input.addEventListener('focus', function() {
          this.classList.remove('error');
        });
      }
    });
  }

  function nextPage() {
    const form = document.getElementById('page1Form');
    let allValid = true;

    // Validate all required fields
    form.querySelectorAll('input[required]').forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('error');
            allValid = false;
        } else {
            input.classList.remove('error');
        }
    });

    if (allValid) {
        // Transfer values to hidden fields
        document.getElementById('hidden_student_name').value = form.querySelector('[name="student_name"]').value;
        document.getElementById('hidden_student_id').value = form.querySelector('[name="student_id"]').value;
        document.getElementById('hidden_year_level').value = form.querySelector('[name="year_level"]').value;
        document.getElementById('hidden_birthdate').value = form.querySelector('[name="birthdate"]').value;
        document.getElementById('hidden_age').value = document.getElementById('age').value;
        document.getElementById('hidden_birthplace').value = form.querySelector('[name="birthplace"]').value;

        document.getElementById('modalAlert').style.display = 'none';
        flipCard.classList.add('flip');
    } else {
        document.getElementById('modalAlert').style.display = 'flex';
    }
}

function closeModal() {
  document.getElementById('modalAlert').style.display = 'none';
}

  function previousPage() {
    // Transfer current page 2 values to page 1 if they exist
    const emailInput = document.querySelector('input[name="email"]');
    const usernameInput = document.querySelector('input[name="username"]');
    
    flipCard.classList.remove('flip');
  }

  function calculateAge() {
    const birthdate = new Date(document.getElementById('birthdate').value);
    const today = new Date();
    let age = today.getFullYear() - birthdate.getFullYear();
    const m = today.getMonth() - birthdate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
      age--;
    }
    document.getElementById('age').value = age;
    document.getElementById('hidden_age').value = age;
  }

  document.getElementById('birthdate').addEventListener('input', calculateAge);

  // Photo Upload Functionality
  document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const preview = document.getElementById('photoPreview');
        const placeholder = document.getElementById('uploadPlaceholder');
        const uploadArea = document.querySelector('.photo-upload-area');
        
        preview.src = e.target.result;
        preview.style.display = 'block';
        placeholder.style.display = 'none';
        uploadArea.classList.add('has-image');
      };
      reader.readAsDataURL(file);
    }
  });

  // Replace your existing notification functions with these
function showSuccessNotification() {
    const notification = document.getElementById('successNotification');
    notification.classList.add('show');
    setTimeout(() => {
        notification.classList.remove('show');
        // Redirect to login page after showing success message
        window.location.href = "{{ route('login') }}";
    }, 3000);
}

function showErrorNotification(message) {
    const notification = document.getElementById('errorNotification');
    notification.textContent = message;
    notification.classList.add('show');
    setTimeout(() => {
        notification.classList.remove('show');
    }, 3000);
}

// Form submission handler
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate password match
    const password = this.querySelector('input[name="password"]').value;
    const confirmPassword = this.querySelector('input[name="password_confirmation"]').value;
    
    if (password !== confirmPassword) {
        showErrorNotification('Passwords do not match!');
        return;
    }

    this.submit();
});

// Handle server-side messages
@if(session('success'))
    document.addEventListener('DOMContentLoaded', () => {
        showSuccessNotification();
    });
@endif

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => {
        showErrorNotification("{{ $errors->first() }}");
    });
@endif

    // Handle notifications
    document.addEventListener('DOMContentLoaded', function() {
        preservePasswordStates();
        const successNotification = document.getElementById('successNotification');
        const errorNotification = document.getElementById('errorNotification');

        if (successNotification) {
            setTimeout(() => {
                successNotification.classList.remove('show');
            }, 5000);
        }

        if (errorNotification) {
            setTimeout(() => {
                errorNotification.classList.remove('show');
            }, 5000);
        }

        // Initialize form with preserved data
        initializeForm();
        // Add event listeners for password toggles
document.getElementById('passwordToggle').addEventListener('click', function() {
  togglePassword('password', 'passwordToggle');
});

document.getElementById('confirmPasswordToggle').addEventListener('click', function() {
  togglePassword('password_confirmation', 'confirmPasswordToggle');
});
    });

    // Form submission handler
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.textContent = 'Processing registration...';
        document.body.appendChild(notification);
        setTimeout(() => notification.classList.add('show'), 100);
    });
</script>

</body>

</html>
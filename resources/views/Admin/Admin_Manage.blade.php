<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
    <link rel="stylesheet" href="{{ asset('assets/css/Admin_manage.css') }}">
    <style>
        .add-user-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
            transition: background-color 0.3s, opacity 0.3s;
        }
        .add-user-btn:hover {
            background-color: #0056b3;
        }
        .modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 5px;
        }
        .close {
            color: #c81414;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: rgb(104, 5, 5);
        }
        .form-group {
            margin-bottom: 10px;
            color: rgb(2, 2, 2);
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 80%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 80%;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .user-table tbody tr {
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .user-table tbody tr:hover {
            background-color: #f5f5f5;
        }
        .user-details {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .user-details p {
            margin: 5px 0;
        }
        .status-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .status-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            flex: 1;
        }
        .activate-btn {
            background-color: #4CAF50;
            color: white;
        }
        .deactivate-btn {
            background-color: #f44336;
            color: white;
        }
        .activate-btn:hover {
            background-color: #45a049;
        }
        .deactivate-btn:hover {
            background-color: #da190b;
        }
        .manage-users-title {
            color: rgb(255, 255, 255);
        }
        .add-user-title {
            color: rgb(146, 11, 11);
        }
        .user-type-selection {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .user-type-selection label {
            color: white;
            margin-right: 20px;
            cursor: pointer;
        }
        .user-type-selection input[type="radio"] {
            margin-right: 5px;
        }
        .btn {
            padding: 10px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 10px;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-primary {
            background-color: #45a049;
            color: white;
        }
        .btn-sm {
            padding: 3px 8px;
            font-size: 11px;
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .status-cell {
            text-transform: capitalize;
        }
        .search-container {
            margin-bottom: 10px;
        }
        .search-input {
            width: 300px;
            padding: 10px;
            border-radius: 15px;
            margin-right: 10px;
        }
        .hidden {
            display: none;
        }
       /* Year Level Sorting Styles */
        .year-level-sorting {
         background-color: rgba(255, 255, 255, 0.1);
         padding: 15px;
         border-radius: 5px;
          margin-bottom: 15px;
          display: flex;
           gap: 15px;
         align-items: end; /* Align all items to bottom */
          flex-wrap: wrap;
         justify-content: center;
        }

.year-level-sorting .form-group {
    margin-bottom: 0;
    color: white;
    min-width: 100px;
}

.year-level-sorting .form-group label {
    color: white;
    font-weight: bold;
    margin-bottom: 5px;
}

.year-level-sorting select {
    width: 100%;
    padding: 6px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    height: 34px; /* Match button height */
}

.year-level-sorting .btn {
    height: 50px;
    border-radius: 5px;
    white-space: nowrap;
}
        .year-level-sorting .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <h2 class="manage-users-title">Manage Users</h2>
        <button class="add-user-btn" id="addUserBtn">Add User</button>
  
        <div class="container">
            <div class="user-type-selection">
                <label>
                    <input type="radio" name="userType" value="system" checked>
                    System Users
                </label>
                <label>
                    <input type="radio" name="userType" value="student">
                    Student Users
                </label>
            </div>

            <div class="search-container" id="searchContainer">
                    
                <div class="year-level-sorting" id="yearLevelSorting" style="display: none;">
                    <div class="form-group">
                        <label for="yearLevelFilter">Filter by Year Level:</label>
                        <select id="yearLevelFilter">
                            <option value="">All Year Levels</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="sortOrder">Sort Order:</label>
                        <select id="sortOrder">
                            <option value="asc">Ascending (1st → 4th Year)</option>
                            <option value="desc">Descending (4th → 1st Year)</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="applySorting()">Apply Sorting</button>
                </div>

                <div>
                      <input type="text" id="searchInput" class="search-input" placeholder="Search by Student ID or Student Name...">
                    </div>
                    </div>

            <div id="userTable" class="user-table">         
                <h3 id="userTableTitle">System Users</h3>
                <table>
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="add-user-title">Add New User</h2>
            <form id="addUserForm">
                @csrf
                <!-- System User Fields -->
                <div id="systemUserFields">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username">
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role">
                            <option value="">Select Role</option>
                        </select>
                    </div>
                </div>
                
                <!-- Student User Fields -->
                <div id="studentUserFields" style="display: none;">
                    <div class="form-group">
                        <label for="student_id">Student ID:</label>
                        <input type="text" id="student_id" name="student_id">
                    </div>
                    <div class="form-group">
                        <label for="student_name">Student Name:</label>
                        <input type="text" id="student_name" name="student_name">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="year_level">Year Level:</label>
                        <select id="year_level" name="year_level">
                            <option value="">Select Year Level</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                    </div>
                </div>
                
                <!-- Common Fields -->
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="submit-btn">Add User</button>
            </form>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="userDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="detailsModalTitle">User Details</h2>
            <div class="user-details">
                <p id="detailUserIdRow"><strong>User ID:</strong> <span id="detailUserId"></span></p>
                <p><strong id="detailUsernameLabel">Username:</strong> <span id="detailUsername"></span></p>
                <p><strong>Role:</strong> <span id="detailRole"></span></p>
                <p><strong>Status:</strong> <span id="detailStatus"></span></p>
                <p id="detailStudentId" style="display: none;"><strong>Student ID:</strong> <span id="detailStudentIdValue"></span></p>
                <p id="detailYearLevel" style="display: none;"><strong>Year Level:</strong> <span id="detailYearLevelValue"></span></p>
                <p id="detailEmail" style="display: none;"><strong>Email:</strong> <span id="detailEmailValue"></span></p>
            </div>
            <div class="status-buttons">
                <button class="status-btn activate-btn" id="activateBtn">Activate</button>
                <button class="status-btn deactivate-btn" id="deactivateBtn">Deactivate</button>
            </div>
        </div>
    </div>

    <script>
        // Get the modals
const addUserModal = document.getElementById("addUserModal");
const userDetailsModal = document.getElementById("userDetailsModal");
const addUserBtn = document.getElementById("addUserBtn");
const searchContainer = document.getElementById("searchContainer");
const closeBtns = document.getElementsByClassName("close");
const form = document.getElementById("addUserForm");
let selectedUserId = null;
let currentUserType = 'system';
let currentUsersData = [];

// Role options for different user types
const roleOptions = {
    system: [
        { value: 'treasurer', text: 'Treasurer' },
        { value: 'secretary', text: 'Secretary' },
        { value: 'admin', text: 'Admin' },
        { value: 'superadmin', text: 'Superadmin' }
    ],
    student: [
        { value: 'student', text: 'Student' }
    ]
};

document.addEventListener('DOMContentLoaded', function() {
    fetchUsers();
    updateRoleOptions();
    updateTableHeaders();
    updateFormFields();
    updateUIForUserType();
});

// Add event listeners for radio buttons
document.querySelectorAll('input[name="userType"]').forEach(radio => {
    radio.addEventListener('change', function() {
        currentUserType = this.value;
        updateTableTitle();
        updateRoleOptions();
        updateTableHeaders();
        updateFormFields();
        updateUIForUserType();
        toggleYearLevelSorting(); // Add this line
        // Clear search input when switching user types
        document.getElementById('searchInput').value = '';
        // Reset sorting controls
        document.getElementById('yearLevelFilter').value = '';
        document.getElementById('sortOrder').value = 'asc';
        fetchUsers();
    });
});

// Function to update UI based on user type
function updateUIForUserType() {
    if (currentUserType === 'student') {
        // Hide Add User button and show search container for students
        addUserBtn.classList.add('hidden');
        searchContainer.classList.remove('hidden');
        // Update search placeholder
        document.getElementById('searchInput').placeholder = 'Search by Student ID or Student Name...';
    } else {
        // Show Add User button and hide search container for system users
        addUserBtn.classList.remove('hidden');
        searchContainer.classList.add('hidden');
        // Clear search input
        document.getElementById('searchInput').value = '';
    }
}

// Function to show/hide year level sorting controls based on user type
function toggleYearLevelSorting() {
    const sortingDiv = document.getElementById('yearLevelSorting');
    if (currentUserType === 'student') {
        sortingDiv.style.display = 'flex';
    } else {
        sortingDiv.style.display = 'none';
    }
}

// Function to get current users data
function getCurrentUsersData() {
    return currentUsersData;
}

// Function to apply sorting based on selected options
function applySorting() {
    const yearFilter = document.getElementById('yearLevelFilter').value;
    const sortOrder = document.getElementById('sortOrder').value;
    
    // Check if we have data to work with
    if (!currentUsersData || currentUsersData.length === 0) {
        console.warn('No user data available for sorting');
        return;
    }
    
    // Get current users data
    let usersToSort = [...currentUsersData];
    
    // Filter by year level if selected
    if (yearFilter && currentUserType === 'student') {
        usersToSort = usersToSort.filter(user => user.year_level == yearFilter);
    }
    
    // Sort users only if we're dealing with students
    if (currentUserType === 'student') {
        usersToSort = usersToSort.sort((a, b) => {
            const aLevel = parseInt(a.year_level) || 999;
            const bLevel = parseInt(b.year_level) || 999;
            
            if (sortOrder === 'desc') {
                return bLevel - aLevel; // Descending order
            } else {
                return aLevel - bLevel; // Ascending order
            }
        });
    }
    
    // Display sorted/filtered users
    displayUsers({ success: true, data: usersToSort });
}

// Helper function to sort users by year level - only for students
function sortUsersByYearLevel(users) {
    // Only apply year level sorting for student user type
    if (currentUserType !== 'student') {
        return users;
    }
    
    return users.sort((a, b) => {
        // Get year level as numbers (1, 2, 3, 4)
        const aLevel = parseInt(a.year_level) || 999; // Default to 999 for unknown levels
        const bLevel = parseInt(b.year_level) || 999;
        
        // Primary sort by year level
        if (aLevel !== bLevel) {
            return aLevel - bLevel;
        }
        
        // Secondary sort by name if year levels are the same
        return (a.student_name || a.name || '').localeCompare(b.student_name || b.name || '');
    });
}

// Function to update table title
function updateTableTitle() {
    const title = document.getElementById('userTableTitle');
    title.textContent = currentUserType === 'system' ? 'System Users' : 'Student Users';
}

// Function to update form fields based on user type
function updateFormFields() {
    const systemFields = document.getElementById('systemUserFields');
    const studentFields = document.getElementById('studentUserFields');
    
    if (currentUserType === 'student') {
        systemFields.style.display = 'none';
        studentFields.style.display = 'block';
        
        // Make student fields required
        document.getElementById('student_id').required = true;
        document.getElementById('student_name').required = true;
        document.getElementById('year_level').required = true;
        document.getElementById('email').required = true;
        document.getElementById('username').required = false;
        document.getElementById('role').required = false;
    } else {
        systemFields.style.display = 'block';
        studentFields.style.display = 'none';
        
        // Make system fields required
        document.getElementById('username').required = true;
        document.getElementById('role').required = true;
        document.getElementById('student_id').required = false;
        document.getElementById('student_name').required = false;
        document.getElementById('email').required = false;
        document.getElementById('year_level').required = false;
    }
}

// Function to update role options in the modal
function updateRoleOptions() {
    const roleSelect = document.getElementById('role');
    
    // Clear existing options
    roleSelect.innerHTML = '<option value="">Select Role</option>';
    
    // Add role options based on user type
    roleOptions[currentUserType].forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option.value;
        optionElement.textContent = option.text;
        roleSelect.appendChild(optionElement);
    });
}

// Function to update table headers based on user type
function updateTableHeaders() {
    const thead = document.querySelector('#userTable thead tr');
    if (currentUserType === 'student') {
        thead.innerHTML = `
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Year Level</th>
            <th>Email</th>
            <th>Status</th>
        `;
    } else {
        thead.innerHTML = `
            <th>User ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Status</th>
        `;
    }
}

// Function to display users in table
function displayUsers(data) {
    const tbody = document.querySelector('#userTable tbody');
    tbody.innerHTML = '';

    if (data.success && data.data && Array.isArray(data.data)) {
        if (data.data.length > 0) {
            data.data.forEach(user => {
                const row = document.createElement('tr');
                if (currentUserType === 'student') {
                    const yearLevelText = user.year_level ? `${user.year_level}${getOrdinalSuffix(user.year_level)} Year` : '';
                    row.innerHTML = `
                        <td>${user.student_id || ''}</td>
                        <td>${user.student_name || ''}</td>
                        <td>${user.year_level}</td>
                        <td>${user.email || ''}</td>
                        <td class="status-cell">${user.status || ''}</td>
                    `;
                } else {
                    row.innerHTML = `
                        <td>${user.id || ''}</td>
                        <td>${user.username || ''}</td>
                        <td>${user.role || ''}</td>
                        <td class="status-cell">${user.status || ''}</td>
                    `;
                }
                row.dataset.userId = user.id;
                row.dataset.userData = JSON.stringify(user);
                tbody.appendChild(row);

                row.addEventListener('click', (e) => {
                    if (!e.target.classList.contains('status-toggle')) {
                        showUserDetails(user);
                    }
                });
            });
        } else {
            const colCount = currentUserType === 'student' ? 5 : 4;
            tbody.innerHTML = `<tr><td colspan="${colCount}" style="text-align: center;">No ${currentUserType} users found</td></tr>`;
        }
    } else {
        const colCount = currentUserType === 'student' ? 5 : 4;
        tbody.innerHTML = `<tr><td colspan="${colCount}" style="text-align: center;">Error loading users</td></tr>`;
    }
}

// Function to get ordinal suffix for year level
function getOrdinalSuffix(num) {
    const j = num % 10;
    const k = num % 100;
    if (j == 1 && k != 11) {
        return "st";
    }
    if (j == 2 && k != 12) {
        return "nd";
    }
    if (j == 3 && k != 13) {
        return "rd";
    }
    return "th";
}

// Function to fetch users
function fetchUsers() {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const userType = document.querySelector('input[name="userType"]:checked').value;

    fetch(`/admin/users?type=${userType}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Store the fetched data for sorting
        if (data.success && data.data) {
            currentUsersData = data.data;
        } else {
            currentUsersData = [];
        }
        displayUsers(data);
    })
    .catch(error => {
        console.error('Error fetching users:', error);
        currentUsersData = []; // Reset on error
        const tbody = document.querySelector('#userTable tbody');
        const colCount = currentUserType === 'student' ? 5 : 4;
        tbody.innerHTML = `<tr><td colspan="${colCount}" style="text-align: center;">Error loading users: ${error.message}</td></tr>`;
    });
}

// Function to show user details
function showUserDetails(user) {
    selectedUserId = user.id;
    
    // Handle different user types
    if (currentUserType === 'student') {
        // For student users
        document.getElementById('detailsModalTitle').textContent = 'Student Details';
        document.getElementById('detailUserIdRow').style.display = 'none';
        document.getElementById('detailUsernameLabel').textContent = 'Student Name:';
        document.getElementById('detailUsername').textContent = user.student_name || '';
        document.getElementById('detailRole').textContent = 'Student';
        document.getElementById('detailStatus').textContent = user.status || '';
        
        // Show student-specific fields
        const studentIdField = document.getElementById('detailStudentId');
        const emailField = document.getElementById('detailEmail');
        const yearLevelField = document.getElementById('detailYearLevel');
        
        document.getElementById('detailStudentIdValue').textContent = user.student_id || '';
        studentIdField.style.display = 'block';
        
        document.getElementById('detailEmailValue').textContent = user.email || '';
        emailField.style.display = 'block';
        
        const yearLevelText = user.year_level ? `${user.year_level}${getOrdinalSuffix(user.year_level)} Year` : '';
        document.getElementById('detailYearLevelValue').textContent = yearLevelText;
        yearLevelField.style.display = 'block';
    } else {
        // For system users
        document.getElementById('detailsModalTitle').textContent = 'User Details';
        document.getElementById('detailUserIdRow').style.display = 'block';
        document.getElementById('detailUsernameLabel').textContent = 'Username:';
        document.getElementById('detailUserId').textContent = user.id || '';
        document.getElementById('detailUsername').textContent = user.username || '';
        document.getElementById('detailRole').textContent = user.role || '';
        document.getElementById('detailStatus').textContent = user.status || '';
        
        // Hide student-specific fields
        const studentIdField = document.getElementById('detailStudentId');
        const yearLevelField = document.getElementById('detailYearLevel');
        const emailField = document.getElementById('detailEmail');
        
        studentIdField.style.display = 'none';
        emailField.style.display = 'none';
        yearLevelField.style.display = 'none';
    }

    const activateBtn = document.getElementById('activateBtn');
    const deactivateBtn = document.getElementById('deactivateBtn');

    if (activateBtn && deactivateBtn) {
        const isSuperadmin = user.role && user.role.toLowerCase() === 'superadmin';

        activateBtn.style.display = user.status === 'inactive' && !isSuperadmin ? 'block' : 'none';
        deactivateBtn.style.display = user.status === 'active' && !isSuperadmin ? 'block' : 'none';
        
        deactivateBtn.disabled = isSuperadmin;
    }

    userDetailsModal.style.display = "block";
}

// Function to update user status
function updateUserStatus(userId, newStatus) {
    const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
    if (userRow) {
        const userData = JSON.parse(userRow.dataset.userData);
        if (userData.role && userData.role.toLowerCase() === 'superadmin' && newStatus === 'inactive') {
            alert("You cannot deactivate a Superadmin.");
            return;
        }
    }

    fetch('/admin/update-user-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            user_id: userId,
            status: newStatus,
            user_type: currentUserType
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('User status updated successfully');
            fetchUsers(); // Refresh the table
            userDetailsModal.style.display = "none"; // Close the modal
        } else {
            throw new Error(data.message || 'Failed to update user status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating user status: ' + error.message);
    });
}

// Handle form submission
form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (password !== confirmPassword) {
        alert('Passwords do not match!');
        return;
    }
    
    const formData = new FormData(form);
    formData.append('user_type', currentUserType);
    
    fetch('/admin/add-user', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User added successfully!');
            form.reset();
            addUserModal.style.display = "none";
            fetchUsers();
        } else {
            alert('Error: ' + (data.message || 'Failed to add user'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding user: ' + error.message);
    });
});

// Event listeners for status buttons in modal
document.getElementById('activateBtn').addEventListener('click', () => updateUserStatus(selectedUserId, 'active'));
document.getElementById('deactivateBtn').addEventListener('click', () => updateUserStatus(selectedUserId, 'inactive'));

// Open add user modal when button is clicked
addUserBtn.onclick = function() {
    addUserModal.style.display = "block";
}

// Close modals when X is clicked
Array.from(closeBtns).forEach(btn => {
    btn.onclick = function() {
        addUserModal.style.display = "none";
        userDetailsModal.style.display = "none";
    }
});

// Close modals when clicking outside
window.onclick = function(event) {
    if (event.target == addUserModal) {
        addUserModal.style.display = "none";
    }
    if (event.target == userDetailsModal) {
        userDetailsModal.style.display = "none";
    }
}

// Add this function for client-side search (like payment.js)
function handleStudentSearchInput() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const tableRows = document.querySelectorAll('#userTable tbody tr');
    tableRows.forEach(row => {
        // Get Student ID and Name columns (adjust indexes if needed)
        const studentId = row.children[0]?.textContent.toLowerCase() || '';
        const studentName = row.children[1]?.textContent.toLowerCase() || '';
        if (studentId.includes(searchTerm) || studentName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Attach the event for student user type
document.getElementById('searchInput').addEventListener('input', function() {
    if (currentUserType === 'student') {
        handleStudentSearchInput();
    }
});
    </script>
</body>
</html>
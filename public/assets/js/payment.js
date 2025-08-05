// Payment Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializePaymentManagement();
});

// Global variables
let currentStudentId = null;
let paymentFors = [];
let currentPaymentForId = null;

// Initialize the payment management system
function initializePaymentManagement() {
    setupEventListeners();
    loadPaymentFors();
    setCurrentDate();
    setupModalEvents();
}

// Setup all event listeners
function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('studentSearch');
    if (searchInput) {
        searchInput.addEventListener('input', handleStudentSearch);
    }

    // Form submissions
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', handlePaymentSubmission);
    }

    const newPaymentForm = document.getElementById('newPaymentForm');
    if (newPaymentForm) {
        newPaymentForm.addEventListener('submit', handleNewPaymentSubmission);
    }

    const editPaymentForForm = document.getElementById('editPaymentForForm');
    if (editPaymentForForm) {
        editPaymentForForm.addEventListener('submit', handleEditPaymentForSubmission);
    }

    const editPaymentForm = document.getElementById('editPaymentForm');
    if (editPaymentForm) {
        editPaymentForm.addEventListener('submit', handleEditPaymentSubmission);
    }

    // Payment for selection change
    const paymentForSelect = document.getElementById('payment_for');
    if (paymentForSelect) {
        paymentForSelect.addEventListener('change', handlePaymentForChange);
    }

    const paymentModal = document.getElementById('paymentModal');
    if (paymentModal) {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'style' && 
                    paymentModal.style.display === 'block') {
                }
            });
        });
        
        observer.observe(paymentModal, { attributes: true });
    }
}

// Setup modal events
function setupModalEvents() {
    // Payment Modal close events
    const paymentModal = document.getElementById('paymentModal');
    const closeButtons = paymentModal?.querySelectorAll('.close');
    closeButtons?.forEach(button => {
        button.addEventListener('click', closePaymentModal);
    });

    // New Payment Modal close events
    const newPaymentModal = document.getElementById('newPaymentModal');
    const newCloseButtons = newPaymentModal?.querySelectorAll('.close');
    newCloseButtons?.forEach(button => {
        button.addEventListener('click', closeNewPaymentModal);
    });

    // Payment For Details Modal close events
    const paymentForDetailsModal = document.getElementById('paymentForDetailsModal');
    const detailsCloseButtons = paymentForDetailsModal?.querySelectorAll('.close');
    detailsCloseButtons?.forEach(button => {
        button.addEventListener('click', closePaymentForDetailsModal);
    });
    // Edit Payment Modal close events
    const editPaymentModal = document.getElementById('editPaymentModal');
    const editCloseButtons = editPaymentModal?.querySelectorAll('.close');
    editCloseButtons?.forEach(button => {
        button.addEventListener('click', closeEditPaymentModal);
    });
}

// Student search functionality
function handleStudentSearch() {
    const searchTerm = document.getElementById('studentSearch').value.toLowerCase();
    const tableRows = document.querySelectorAll('#studentTableBody tr');

    tableRows.forEach(row => {
        const studentId = row.querySelector('[data-student-id]')?.textContent.toLowerCase() || '';
        const studentName = row.querySelector('[data-student-name]')?.textContent.toLowerCase() || '';
        
        if (studentId.includes(searchTerm) || studentName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// View payment details for a specific student
async function viewPaymentDetails(studentId) {
    try {
        currentStudentId = studentId;
        document.getElementById('paymentModal').style.display = 'block';
        
        // Show loading state
        showLoading('Loading student details...');
        
        const response = await fetch(`/payments/${studentId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch student details');
        }

        const data = await response.json();
        
        // Populate modal with student data
        populateStudentModal(data.student);
        
        // Display payment history directly
        displayPaymentHistory(data.payments);
        
        // Display student payment summary
        displayStudentPaymentSummaryFromData(data.payment_fors);
        
        // Show the modal
        document.getElementById('paymentModal').style.display = 'block';
        
        hideLoading();
        
    } catch (error) {
        console.error('Error loading student details:', error);
        showAlert('Error loading student details. Please try again.', 'error');
        hideLoading();
    }
}

// Populate student modal with data
function populateStudentModal(student) {
    document.getElementById('student_id').value = student.student_id || '';
    document.getElementById('student_name').value = student.student_name || '';
    document.getElementById('year_level').value = student.year_level || '';
    
    // Reset form fields
    document.getElementById('payment_for').value = '';
    document.getElementById('amount').value = '';
    document.getElementById('remaining_amount').textContent = '';
}

// Display payment history table
function displayPaymentHistory(payments) {
    const container = document.getElementById('paymentHistoryTable');
    
    if (!payments || payments.length === 0) {
        container.innerHTML = '<p>No payment history found.</p>';
        return;
    }

    const table = `
        <h3>Payment History</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>OR Number</th>
                        <th>Payment For</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${payments.map(payment => `
                        <tr>
                            <td>${formatDate(payment.created_at)}</td>
                            <td>${payment.or_number}</td>
                            <td>${payment.payment_for_name}</td>
                            <td>₱${formatCurrency(payment.amount)}</td>
                            <td>
                                <button onclick="editPayment(${payment.id})" style="background-color: #9b0808ff;color: white;padding: 6px 12px;border: none;border-radius: 4px;cursor: pointer;font-size: 13px;margin: 2px;">Edit</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = table;
}

// Display student payment summary from data
function displayStudentPaymentSummaryFromData(paymentFors) {
    const summaryBody = document.getElementById('paymentSummaryBody');
    let totalExpected = 0;
    let totalPaid = 0;

    const rows = paymentFors.map(item => {
        totalExpected += parseFloat(item.amount);
        totalPaid += parseFloat(item.total_paid);
        
        const remaining = item.remaining_amount;
        const progress = item.amount > 0 ? (item.total_paid / item.amount * 100) : 0;
        
        return `
            <tr>
                <td>${item.name}</td>
                <td>₱${formatCurrency(item.amount)}</td>
                <td>₱${formatCurrency(item.total_paid)}</td>
                <td>₱${formatCurrency(remaining)}</td>
                <td>
                    <div class="mini-progress-bar">
                        <div class="mini-progress-fill" style="width: ${Math.min(progress, 100)}%"></div>
                    </div>
                    <span class="progress-text">${progress.toFixed(1)}%</span>
                </td>
            </tr>
        `;
    }).join('');

    summaryBody.innerHTML = rows;

    // Update total progress
    const totalProgress = totalExpected > 0 ? (totalPaid / totalExpected * 100) : 0;
    const progressBar = document.getElementById('totalProgressBar');
    const progressText = document.getElementById('totalProgressText');
    const amountText = document.getElementById('totalAmountText');

    if (progressBar) progressBar.style.width = `${Math.min(totalProgress, 100)}%`;
    if (progressText) progressText.textContent = `${totalProgress.toFixed(1)}%`;
    if (amountText) amountText.textContent = `₱${formatCurrency(totalPaid)} / ₱${formatCurrency(totalExpected)}`;
}

// Handle payment form submission
async function handlePaymentSubmission(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const paymentData = {
        student_id: currentStudentId,
        or_number: formData.get('or_number'),
        payment_for_id: formData.get('payment_for'),
        amount: parseFloat(formData.get('amount')),
        payment_date: new Date().toISOString().split('T')[0] // Current date in YYYY-MM-DD format
    };

    // Validate form data
    if (!validatePaymentData(paymentData)) {
        return;
    }

    try {
        showLoading('Processing payment...');

        const response = await fetch('/payments', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(paymentData)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Failed to process payment');
        }

        if (result.success) {
            showAlert(result.message, 'success');
            
            // Reset form
            event.target.reset();
            
            // Reload student details to refresh payment history and summary
            viewPaymentDetails(currentStudentId);
            
            // Refresh the main table
            refreshStudentTable();
        } else {
            showAlert(result.message, 'error');
        }
        
        hideLoading();
        
    } catch (error) {
        console.error('Error processing payment:', error);
        showAlert(error.message || 'Error processing payment. Please try again.', 'error');
        hideLoading();
    }
}

// Validate payment data
function validatePaymentData(data) {
    if (!data.or_number || data.or_number.trim() === '') {
        showAlert('Please enter OR Number', 'error');
        return false;
    }
    
    if (!data.payment_for_id) {
        showAlert('Please select Payment For', 'error');
        return false;
    }
    
    if (!data.amount || data.amount <= 0) {
        showAlert('Please enter a valid amount', 'error');
        return false;
    }
    
    return true;
}

// Handle new payment for creation
async function handleNewPaymentSubmission(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const paymentForData = {
        payment_name: formData.get('payment_name'),
        payment_amount: parseFloat(formData.get('payment_amount')),
    };

    try {
        showLoading('Creating payment for...');

        const response = await fetch('/payment-fors', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(paymentForData)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Failed to create payment for');
        }

       if (result.success) {
    showAlert(result.message, 'success');
    
    // Reset form
    event.target.reset();
    
    // Reload payment fors for dropdown
    await loadPaymentFors();
    
    // Refresh the payment for table
    await refreshPaymentForTable();
    
    // Close the modal
    closeNewPaymentModal();
} else {
    showAlert(result.message, 'error');
}
        
        hideLoading();
        
    } catch (error) {
        console.error('Error creating payment for:', error);
        showAlert(error.message || 'Error creating payment for. Please try again.', 'error');
        hideLoading();
    }
}

// Load payment fors for dropdown
async function loadPaymentFors() {
    try {
        const response = await fetch('/payment-fors', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch payment fors');
        }

        const result = await response.json();
        
        // Handle both success and error responses
        if (result.success) {
            paymentFors = result.data; // Use the data property
            populatePaymentForDropdown();
        } else {
            throw new Error(result.message || 'Failed to load payment types');
        }
        
    } catch (error) {
        console.error('Error loading payment fors:', error);
        // Optionally show user-friendly error message
    }
}

// Populate payment for dropdown
function populatePaymentForDropdown() {
    const select = document.getElementById('payment_for');
    if (!select) return;

    // Clear existing options except the first one
    select.innerHTML = '<option value="">Select Payment For</option>';
    
    paymentFors.forEach(paymentFor => {
        const option = document.createElement('option');
        option.value = paymentFor.id;
        option.textContent = `${paymentFor.name} - ₱${formatCurrency(paymentFor.amount)}`;
        option.dataset.amount = paymentFor.amount;
        select.appendChild(option);
    });
}

// Handle payment for selection change
function handlePaymentForChange(event) {
    const selectedOption = event.target.selectedOptions[0];
    const amountInput = document.getElementById('amount');
    const remainingSpan = document.getElementById('remaining_amount');
    
    if (selectedOption && selectedOption.dataset.amount) {
        amountInput.value = selectedOption.dataset.amount;
        updateRemainingAmount(event.target.value);
    } else {
        amountInput.value = '';
        remainingSpan.textContent = '';
    }
}

// Show new payment modal
function showNewPaymentModal() {
    document.getElementById('newPaymentModal').style.display = 'block';
}

// Close new payment modal
function closeNewPaymentModal() {
    document.getElementById('newPaymentModal').style.display = 'none';
}

// Add this new function instead:
async function editPayment(paymentId) {
    try {
        showLoading('Loading payment details...');
        
        const response = await fetch(`/payments/edit/${paymentId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch payment details');
        }

        const payment = await response.json();
        populateEditPaymentForm(payment);
        
        document.getElementById('editPaymentModal').style.display = 'block';
        hideLoading();
        
    } catch (error) {
        console.error('Error loading payment details:', error);
        showAlert('Error loading payment details. Please try again.', 'error');
        hideLoading();
    }
}

// Add function to populate edit form
function populateEditPaymentForm(payment) {
    document.getElementById('edit_or_number').value = payment.or_number;
    document.getElementById('edit_payment_for').value = payment.payment_for_id;
    document.getElementById('edit_amount').value = payment.amount;
    
    // Store payment ID for update
    document.getElementById('editPaymentForm').dataset.paymentId = payment.id;
}

// Add function to handle edit form submission
async function handleEditPaymentSubmission(event) {
    event.preventDefault();
    
    const paymentId = event.target.dataset.paymentId;
    const formData = new FormData(event.target);
    
    const updateData = {
        or_number: formData.get('or_number'),
        payment_for_id: formData.get('payment_for'),
        amount: parseFloat(formData.get('amount')),
        payment_date: formData.get('payment_date')
    };

    try {
        showLoading('Updating payment...');

        const response = await fetch(`/payments/${paymentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(updateData)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Failed to update payment');
        }

        if (result.success) {
            showAlert(result.message, 'success');
            
            // Close edit modal
            closeEditPaymentModal();
            
            // Reload student details to refresh payment history
            viewPaymentDetails(currentStudentId);
            
            // Refresh the main table
            refreshStudentTable();
        } else {
            showAlert(result.message, 'error');
        }
        
        hideLoading();
        
    } catch (error) {
        console.error('Error updating payment:', error);
        showAlert(error.message || 'Error updating payment. Please try again.', 'error');
        hideLoading();
    }
}

// Add function to close edit modal
function closeEditPaymentModal() {
    document.getElementById('editPaymentModal').style.display = 'none';
}
// Show payment for details
async function showPaymentForDetails(paymentForId) {
    try {
        currentPaymentForId = paymentForId;
        showLoading('Loading payment details...');

        const response = await fetch(`/payment-fors/${paymentForId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch payment for details');
        }

        const paymentFor = await response.json();
        populatePaymentForDetails(paymentFor);
        
        document.getElementById('paymentForDetailsModal').style.display = 'block';
        hideLoading();
        
    } catch (error) {
        console.error('Error loading payment for details:', error);
        showAlert('Error loading payment details. Please try again.', 'error');
        hideLoading();
    }
}

// Populate payment for details
function populatePaymentForDetails(paymentFor) {
    // View mode
    document.getElementById('view_payment_name').textContent = paymentFor.name;
    document.getElementById('view_payment_amount').textContent = `₱${formatCurrency(paymentFor.amount)}`;
    document.getElementById('view_status').textContent = paymentFor.status || 'Active';
    document.getElementById('view_created_at').textContent = formatDate(paymentFor.created_at);
    document.getElementById('view_updated_at').textContent = formatDate(paymentFor.updated_at);
    
    // Edit form
    document.getElementById('edit_payment_name').value = paymentFor.name;
    document.getElementById('edit_payment_amount').value = paymentFor.amount;
    
    // Show view mode by default
    document.getElementById('paymentForDetailsView').style.display = 'block';
    document.getElementById('editPaymentForForm').style.display = 'none';
}

// Start editing payment for
function startEdit() {
    document.getElementById('paymentForDetailsView').style.display = 'none';
    document.getElementById('editPaymentForForm').style.display = 'block';
}

// Cancel editing
function cancelEdit() {
    document.getElementById('paymentForDetailsView').style.display = 'block';
    document.getElementById('editPaymentForForm').style.display = 'none';
}

// Handle edit payment for submission
async function handleEditPaymentForSubmission(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const updateData = {
        payment_name: formData.get('payment_name'),
        payment_amount: parseFloat(formData.get('payment_amount')),
    };

    try {
        showLoading('Updating payment for...');

        const response = await fetch(`/payment-fors/${currentPaymentForId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(updateData)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            // Success case with SweetAlert
            if (result.show_alert) {
                await Swal.fire({
                    icon: result.alert_type || 'success',
                    title: 'Success!',
                    text: result.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                showAlert(result.message, 'success');
            }
            
            // Refresh data
            await loadPaymentFors();
            await refreshPaymentForTable();
            
            // Close modal
            closePaymentForDetailsModal();
            
        } else {
            // Error case with SweetAlert
            if (result.show_alert) {
                let icon = result.alert_type || 'error';
                let title = '';
                
                switch(icon) {
                    case 'warning':
                        title = 'Cannot Update!';
                        break;
                    case 'info':
                        title = 'No Changes!';
                        break;
                    case 'error':
                        title = 'Error!';
                        break;
                    default:
                        title = 'Error!';
                }
                
                await Swal.fire({
                    icon: icon,
                    title: title,
                    text: result.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
            } else {
                showAlert(result.message, 'error');
            }
        }
        
        hideLoading();
        
    } catch (error) {
        console.error('Error updating payment for:', error);
        hideLoading();
        
        await Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Error updating payment for. Please try again.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    }
}

// Close payment for details modal
function closePaymentForDetailsModal() {
    document.getElementById('paymentForDetailsModal').style.display = 'none';
    currentPaymentForId = null;
}

// Close payment modal
function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    currentStudentId = null;
}

async function refreshStudentTable() {
    console.log('Student table refresh requested - you may want to reload the page or implement specific refresh logic');
}

// Update student table with new data
function updateStudentTable(students) {
    const tbody = document.getElementById('studentTableBody');
    if (!tbody) return;

    tbody.innerHTML = students.map(student => `
        <tr onclick="viewPaymentDetails(${student.id})" style="cursor: pointer;">
            <td data-student-id>${student.student_id}</td>
            <td data-student-name>${student.student_name}</td>
            <td>${student.year_level}</td>
            <td>₱${formatCurrency(student.total_paid || 0)}</td>
        </tr>
    `).join('');
}

// Refresh payment for table
async function refreshPaymentForTable() {
    try {
        const response = await fetch('/payment-fors', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            const result = await response.json();
            // Check if the response has the expected structure
            if (result.success && result.data) {
                updatePaymentForTable(result.data);
            } else {
                console.error('Unexpected response structure:', result);
            }
        }
    } catch (error) {
        console.error('Error refreshing payment for table:', error);
    }
}

// Update payment for table
function updatePaymentForTable(paymentFors) {
    const tbody = document.getElementById('paymentForTableBody');
    if (!tbody) return;

    if (!paymentFors || paymentFors.length === 0) {
        tbody.innerHTML = '<tr><td colspan="2" style="text-align: center;">No payment types found</td></tr>';
        return;
    }

    tbody.innerHTML = paymentFors.map(paymentFor => `
        <tr onclick="showPaymentForDetails(${paymentFor.id})" style="cursor: pointer;">
            <td>${paymentFor.name}</td>
            <td>₱${formatCurrency(paymentFor.amount)}</td>
        </tr>
    `).join('');
}

// Set current date
function setCurrentDate() {
    const dateInput = document.getElementById('current_date');
    if (dateInput) {
        const today = new Date();
        dateInput.value = today.toLocaleDateString('en-PH');
    }
}

// Utility functions
function formatCurrency(amount) {
    return parseFloat(amount).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-PH') + ' ' + date.toLocaleTimeString('en-PH');
}

function showAlert(message, type = 'info') {
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        padding: 15px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        max-width: 350px;
        word-wrap: break-word;
    `;
    
    // Set colors based on type
    switch(type) {
        case 'success':
            alert.style.backgroundColor = '#d4edda';
            alert.style.color = '#155724';
            alert.style.border = '1px solid #c3e6cb';
            break;
        case 'error':
            alert.style.backgroundColor = '#f8d7da';
            alert.style.color = '#721c24';
            alert.style.border = '1px solid #f5c6cb';
            break;
        default:
            alert.style.backgroundColor = '#d1ecf1';
            alert.style.color = '#0c5460';
            alert.style.border = '1px solid #bee5eb';
    }
    
    alert.textContent = message;
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}

function showLoading(message = 'Loading...') {
    // Remove existing loading overlay
    hideLoading();
    
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
    `;
    
    const loader = document.createElement('div');
    loader.style.cssText = `
        background: white;
        padding: 20px 30px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    `;
    
    loader.innerHTML = `
        <div style="border: 3px solid #f3f3f3; border-top: 3px solid #3498db; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; margin: 0 auto 15px;"></div>
        <div>${message}</div>
    `;
    
    // Add CSS animation
    if (!document.getElementById('loadingStyles')) {
        const style = document.createElement('style');
        style.id = 'loadingStyles';
        style.textContent = `
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }
    
    overlay.appendChild(loader);
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

function applyYearLevelSorting() {
    const yearLevel = document.getElementById('yearLevelFilter').value;
    const sortOrder = document.getElementById('sortOrder').value;
    const rows = Array.from(document.querySelectorAll('#studentTableBody tr'));

    // Filter by year level
    rows.forEach(row => {
        const year = row.children[2]?.textContent.trim();
        if (!yearLevel || year === yearLevel) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Sort rows by year level
    const visibleRows = rows.filter(row => row.style.display !== 'none');
    visibleRows.sort((a, b) => {
        const aYear = parseInt(a.children[2]?.textContent.trim() || 0);
        const bYear = parseInt(b.children[2]?.textContent.trim() || 0);
        return sortOrder === 'asc' ? aYear - bYear : bYear - aYear;
    });

    // Re-append sorted rows
    const tbody = document.getElementById('studentTableBody');
    visibleRows.forEach(row => tbody.appendChild(row));
}

// Optional: Reset sorting when search is used
document.getElementById('studentSearch').addEventListener('input', function() {
    // Optionally, you can call applyYearLevelSorting() here if you want to keep sorting after search
});
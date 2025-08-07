<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fines Generation - ITSBO Treasurer</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/fines.css') }}">
</head>
<body>
    <div class="container">
        <!-- Sidebar Placeholder -->
        <div class="sidebar">
            <div class="sidebar-content">
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1><i class="fas fa-gavel"></i> Fines Generation</h1>
            </div>

            <!-- Search and Filter Container -->
            <div class="search-container">
                <div class="search-box">
                    <input type="text" id="studentSearch" placeholder="Search by Student ID or Name...">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><i class="fas fa-id-card"></i> Student ID</th>
                            <th><i class="fas fa-user"></i> Student Name</th>
                            <th><i class="fas fa-dollar-sign"></i> Total Fines</th>
                            <th><i class="fas fa-minus-circle"></i> Less Fines Payment</th>
                            <th><i class="fas fa-balance-scale"></i> Remaining Balance</th>
                            <th><i class="fas fa-credit-card"></i> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($finesData as $data)
                            <tr>
                                <td>{{ $data['student']->student_id }}</td>
                                <td class="student-name">{{ $data['student']->student_name ?? $data['student']->student_name }}</td>
                                <td>₱{{ number_format($data['total_fines'], 2) }}</td>
                                <td>₱{{ number_format($data['less_payment'], 2) }}</td>
                                <td>₱{{ number_format($data['remaining_balance'], 2) }}</td>
                                <td>
                                    <button class="pay-btn" {{ $data['remaining_balance'] <= 0 ? 'disabled' : '' }}>
                                        <i class="fas fa-credit-card"></i> Pay
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const searchInput = document.getElementById('studentSearch');
    const tableBody = document.querySelector('tbody');
    const rows = tableBody.getElementsByTagName('tr');

    // Store original rows for reset (only active students)
    const originalRows = Array.from(rows);

    // Search functionality
    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        // Loop through all table rows (only active students)
        Array.from(originalRows).forEach(row => {
            const studentId = row.cells[0].textContent.toLowerCase();
            const studentName = row.cells[1].textContent.toLowerCase();
            
            // Check if text matches search
            const matches = studentId.includes(searchTerm) || 
                          studentName.includes(searchTerm);
            
            // Show/hide row based on match
            row.style.display = matches ? '' : 'none';
        });

        // If search is empty, restore all active student rows
        if (searchTerm === '') {
            originalRows.forEach(row => {
                row.style.display = '';
            });
        }

        // Reattach event listeners after search
        attachRowEventListeners();
    });

    // Function to reattach event listeners to visible rows
    function attachRowEventListeners() {
        document.querySelectorAll('tbody tr:not([style*="display: none"])').forEach(row => {
            // Remove existing listeners first
            row.removeEventListener('mouseenter', handleMouseEnter);
            row.removeEventListener('mouseleave', handleMouseLeave);
            
            // Add new listeners
            row.addEventListener('mouseenter', handleMouseEnter);
            row.addEventListener('mouseleave', handleMouseLeave);
        });

        // Reattach pay button listeners
        document.querySelectorAll('.pay-btn').forEach(btn => {
            // Remove existing listener if any
            btn.removeEventListener('click', handlePayButtonClick);
            // Add new listener
            btn.addEventListener('click', handlePayButtonClick);
        });
    }

    // Handler functions
    function handleMouseEnter() {
        this.style.background = 'linear-gradient(135deg, rgba(139, 0, 0, 0.05), rgba(160, 82, 45, 0.05))';
    }

    function handleMouseLeave() {
        this.style.background = '';
    }

    function handlePayButtonClick() {
        if (!this.disabled) {
            const row = this.closest('tr');
            const studentId = row.cells[0].textContent;
            const studentName = row.cells[1].textContent;
            const remainingBalance = row.cells[4].textContent;
            
            // Add your payment logic here
            console.log('Payment initiated for:', {
                studentId,
                studentName,
                remainingBalance
            });
        }
    }

    // Initial attachment of event listeners
    attachRowEventListeners();
});
</script>

    <style>
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .search-container {
            display: flex;
            align-items: center;
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .search-box {
            position: relative;
            flex: 1;
        }

        .search-box input {
            width: 100%;
            padding: 10px 35px 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .search-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
    </style>
</body>
</html>
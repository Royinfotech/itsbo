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
        // Add interactive functionality
        document.querySelectorAll('.pay-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!this.disabled) {
                    // Add ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    ripple.style.position = 'absolute';
                    ripple.style.borderRadius = '50%';
                    ripple.style.background = 'rgba(255, 255, 255, 0.6)';
                    ripple.style.transform = 'scale(0)';
                    ripple.style.animation = 'ripple 0.6s linear';
                    ripple.style.pointerEvents = 'none';
                    
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                    
                    // Here you would typically handle the payment process
                    console.log('Payment initiated for student:', this.closest('tr').querySelector('.student-name').textContent);
                }
            });
        });

        // Add hover effects to table rows
        document.querySelectorAll('tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.background = 'linear-gradient(135deg, rgba(139, 0, 0, 0.05), rgba(160, 82, 45, 0.05))';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.background = '';
            });
        });
    </script>

    <style>
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    </style>
</body>
</html>
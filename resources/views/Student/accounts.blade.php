<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Account Summary</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --maroon-primary: #670820;
            --maroon-light: #9d1a37;
            --maroon-dark: #660019;
            --maroon-bg: #faf7f8;
            --maroon-accent: #f4e6ea;
            --text-primary: #2c2c2c;
            --text-secondary: #666;
            --shadow: 0 8px 32px rgba(128, 0, 32, 0.1);
            --border-radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin-left: 50px;
            padding: 2rem;
            
        }

        .card {
            background: var(--maroon-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--maroon-accent);
            padding: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--maroon-primary), var(--maroon-light));
            border-radius: var(--border-radius);
            color: white;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            z-index: 1;
            position: relative;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-item {
            background: linear-gradient(135deg, var(--maroon-accent), rgba(255, 255, 255, 0.8));
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid rgba(128, 0, 32, 0.1);
        }

        .summary-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(128, 0, 32, 0.15);
        }

        .summary-item .icon {
            font-size: 2rem;
            color: var(--maroon-primary);
            margin-bottom: 1rem;
        }

        .summary-item .label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-item .amount {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--maroon-primary);
        }

        .balance-positive {
            color: #28a745 !important;
        }

        .balance-negative {
            color: #dc3545 !important;
        }

        .section-title {
            background: linear-gradient(90deg, var(--maroon-primary), var(--maroon-light));
            color: white;
            padding: 1.5rem 2rem;
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            margin: 2rem 0 0 0;
        }

        .table-container {
            background: white;
            border-radius: 0 0 var(--border-radius) var(--border-radius);
            box-shadow: var(--shadow);
            overflow-x: auto;
            border: 1px solid var(--maroon-accent);
            border-top: none;
            width: 100%;
        }

        .table {
            margin: 0;
            border-collapse: collapse;
            font-size: 0.95rem;
            width: 100%;
        }

        .table thead th {
            background: var(--maroon-accent);
            color: var(--maroon-dark);
            padding: 1.2rem 1.5rem;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .table tbody td {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .table tbody tr:hover td {
            background: var(--maroon-bg);
        }

        .amount-display {
            font-weight: 600;
            color: var(--maroon-primary);
        }

        .date-display {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .or-number-display {
            font-family: monospace;
            background: var(--maroon-accent);
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-size: 0.85rem;
            color: var(--maroon-dark);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-secondary);
            font-style: italic;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--maroon-light);
            margin-bottom: 1rem;
            display: block;
        }

        .back-account-row {
            background: rgba(220, 53, 69, 0.05) !important;
        }

        .back-account-row:hover {
            background: rgba(220, 53, 69, 0.1) !important;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .table thead th,
            .table tbody td {
                padding: 1rem;
                font-size: 0.9rem;
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card fade-in">
        <div class="header">
            <h2><i class="fas fa-graduation-cap"></i> My Current Account</h2>
            <h4 style="color: var(--maroon-accent); font-weight: 600;">
                <i class="fas fa-calendar-alt"></i> School Year: {{ $activeSchoolYear->year }}
            </h4>
        </div>

        <div class="summary-grid">
            <div class="summary-item">
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="label">Total Payable</div>
                <div class="amount">₱{{ number_format($totalPayable, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="label">Total Paid</div>
                <div class="amount">₱{{ number_format($totalPaid, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="label">Outstanding Balance</div>
                <div class="amount {{ ($totalPayable - $totalPaid) > 0 ? 'balance-negative' : 'balance-positive' }}">
                    ₱{{ number_format($totalPayable - $totalPaid, 2) }}
                </div>
            </div>
        </div>

        <div class="fade-in">
            <div class="section-title">
                <i class="fas fa-receipt"></i>
                Payments This School Year
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-tag"></i> Payment For</th>
                            <th><i class="fas fa-peso-sign"></i> Amount</th>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-file-alt"></i> OR Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->paymentFor->name ?? '' }}</td>
                            <td class="amount-display">₱{{ number_format($payment->amount, 2) }}</td>
                            <td class="date-display">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                            <td><span class="or-number-display">{{ $payment->or_number }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fas fa-receipt"></i>
                                No payments found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="fade-in">
            <div class="section-title">
                <i class="fas fa-history"></i>
                Back Accounts (Unpaid from Previous School Years)
            </div>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar-alt"></i> School Year</th>
                            <th><i class="fas fa-tag"></i> Payment For</th>
                            <th><i class="fas fa-peso-sign"></i> Amount Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backAccounts as $back)
                        <tr class="back-account-row">
                            <td>{{ $back['school_year'] }}</td>
                            <td>{{ $back['payment_for'] }}</td>
                            <td class="amount-display">₱{{ number_format($back['amount_due'], 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="empty-state">
                                <i class="fas fa-history"></i>
                                No back accounts.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fadeElements = document.querySelectorAll('.fade-in');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        fadeElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });
    });
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Report</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px 0 0 0;
            text-align: center;
            position: relative;
            background-color: #f9fafb;
            display: flex;
            justify-content: center;
            height: auto; /* Remove full height to move content up */
        }

        /* Background Image */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('./pictures/LoginBackg.png') no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            opacity: 0.3;
            z-index: -1;
        }

        /* Dashboard Container */
        .dashboard-container {
            width: 90%;
            max-width: 1000px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            display: flex;
            margin-left: 50px;
            flex-direction: column;
            align-items: center;
            transition: 0.3s ease;
            margin-top: 20px;
            border-top: 5px solid maroon;
        }

        h2 {
            color: #000000;
            font-weight: 600;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #6d1b1b;
            color: white;
        }

        /* Print Container */
        .print-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            width: 100%;
        }

        select {
            padding: 8px;
            border: 1px solid #6d1b1b;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            width: 100%;
            max-width: 300px;
        }

        button {
            background-color: #6d1b1b;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            transition: 0.3s ease;
            width: 100%;
            max-width: 200px;
        }

        button:hover {
            background-color: #541414;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .dashboard-container {
                width: 90%;
                margin-left: 0; /* Remove sidebar spacing */
            }

            table {
                font-size: 14px;
            }

            th, td {
                padding: 8px;
            }

            button, select {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .dashboard-container {
                width: 95%;
                padding: 15px;
            }

            table {
                font-size: 12px;
            }

            th, td {
                padding: 6px;
            }

            button {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Transaction Report</h2>

        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Year Level</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody id="transactionList"></tbody>
        </table>

        <div class="print-container">
            <label for="paymentMonth">Month of Payment:</label>
            <select id="paymentMonth">
                <option value="January">January</option>
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option>
                <option value="May">May</option>
                <option value="June">June</option>
                <option value="July">July</option>
                <option value="August">August</option>
                <option value="September">September</option>
                <option value="October">October</option>
                <option value="November">November</option>
                <option value="December">December</option>
            </select>
            <button onclick="printReport()">Print Report</button>
        </div>
    </div>

    <script>
        function loadTransactions() {
            let students = JSON.parse(localStorage.getItem('students')) || [];
            let transactionList = document.getElementById('transactionList');
            transactionList.innerHTML = "";

            students.forEach(student => {
                let row = `<tr>
                    <td>${student.studentID}</td>
                    <td>${student.studentName}</td>
                    <td>${student.yearLevel}</td>
                    <td>${student.email}</td>
                </tr>`;
                transactionList.innerHTML += row;
            });
        }

        function printReport() {
            let month = document.getElementById('paymentMonth').value;
            alert("Printing report for " + month);
            window.print();
        }

        window.onload = loadTransactions;
    </script>
</body>
</html>

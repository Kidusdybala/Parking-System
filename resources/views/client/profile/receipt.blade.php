
    <style>
        body {
            font-family: "Courier New", monospace;
            text-align: center;
            margin: auto;
            padding: 20px;
        }

        .receipt-container {
            max-width: 350px;
            border: 1px solid black;
            padding: 15px;
            text-align: center;
            margin: auto;
        }

        h2 {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border-bottom: 1px dashed black;
            padding: 5px;
            text-align: left;
        }

        .footer {
            border-top: 1px dashed black;
            padding-top: 10px;
            font-size: 12px;
        }

        .print-btn {
            margin-top: 15px;
            padding: 8px 15px;
            font-size: 14px;
            cursor: pointer;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>

    <div class="receipt-container" id="receipt-content">
        <h2>Payment Receipt</h2>
        <hr>

        <table>
            <tr>
                <th>Name:</th>
                <td>{{ $receiptData['user'] }}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>{{ $receiptData['email'] }}</td>
            </tr>
            <tr>
                <th>Amount Paid:</th>
                <td>${{ number_format($receiptData['amount'], 2) }}</td>
            </tr>
            <tr>
                <th>Date:</th>
                <td>{{ $receiptData['date'] }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for your payment!</p>
        </div>
    </div>

    <button class="print-btn" onclick="printReceipt()">Print Receipt</button>

    <script>
        function printReceipt() {
            let printContent = document.getElementById('receipt-content').innerHTML;
            let originalContent = document.body.innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>


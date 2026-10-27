<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Invoice: {{ $application->application_id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 20px;
            font-size: 13px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #4361ee;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #4361ee;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .section-title {
            color: #4361ee;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f6f8fa;
            font-weight: bold;
        }
        .summary-box {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
            gap: 20px;
        }
        .summary-item {
            padding: 10px 15px;
            background: #f9f9f9;
            border-radius: 4px;
            text-align: right;
        }
        .btn-print {
            margin-top: 20px;
            padding: 8px 16px;
            background: #4361ee;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" class="btn-print">Print / Save as PDF</button>
        <button onclick="window.history.back()" style="padding: 8px 16px; margin-left: 10px; cursor: pointer;">Back</button>
    </div>

    <div class="header">
        <h1>Invoice</h1>
        <p>Application ID: <strong>{{ $application->application_id }}</strong></p>
    </div>

    <table style="border: none; margin-bottom: 20px;">
        <tr style="border: none;">
            <td style="border: none; vertical-align: top; width: 50%;">
                <div class="section-title">Student Information</div>
                <p><strong>Name:</strong> {{ $application->student->first_name }} {{ $application->student->last_name }}</p>
                <p><strong>Email:</strong> {{ $application->student->email ?? '-' }}</p>
                <p><strong>Phone:</strong> {{ $application->student->phone ?? '-' }}</p>
                <p><strong>Passport:</strong> {{ $application->student->passport_number ?? '-' }}</p>
            </td>
            <td style="border: none; vertical-align: top; width: 50%;">
                <div class="section-title">University Details</div>
                <p><strong>Country:</strong> {{ $application->university->country->name ?? 'N/A' }}</p>
                <p><strong>University:</strong> {{ $application->university->name ?? 'N/A' }}</p>
                <p><strong>Course:</strong> {{ $application->course->name ?? 'N/A' }}</p>
                <p><strong>Intake:</strong> {{ $application->intake->intake_name ?? 'N/A' }}</p>
            </td>
        </tr>
    </table>

    <div class="section-title">Fee Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount (BDT)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Tuition Fee</td>
                <td style="text-align: right;">{{ number_format($application->tuition_fee ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Service Charge</td>
                <td style="text-align: right;">{{ number_format($application->service_charge ?? 0, 2) }}</td>
            </tr>
            <tr style="font-weight: bold; background-color: #fafafa;">
                <td>Total Payable</td>
                <td style="text-align: right;">{{ number_format(($application->tuition_fee ?? 0) + ($application->service_charge ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary-box">
        <div class="summary-item">
            <div>Total Paid:</div>
            <strong style="color: #00ab55; font-size: 16px;">
                {{ number_format($application->payments->where('payment_status', 'completed')->sum('amount'), 2) }} BDT
            </strong>
        </div>
        <div class="summary-item">
            <div>Balance Due:</div>
            <strong style="color: #e7515a; font-size: 16px;">
                {{ number_format(max(0, ($application->tuition_fee ?? 0) + ($application->service_charge ?? 0) - $application->payments->where('payment_status', 'completed')->sum('amount')), 2) }} BDT
            </strong>
        </div>
    </div>
</body>
</html>

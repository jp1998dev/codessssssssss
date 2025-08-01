<!DOCTYPE html>
<html>

<head>
    <title>Collection Summary</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }

        @media print {
            body {
                font-size: 12px;
            }

            table {
                page-break-inside: avoid;
            }

            h1,
            h2 {
                page-break-after: avoid;
            }

            .table th {
                background-color: #eee !important;
                -webkit-print-color-adjust: exact;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            background-color: #fff;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 18px;
            margin-top: 40px;
            border-bottom: 2px solid #333;
            padding-bottom: 4px;
        }

        p strong {
            font-size: 14px;
        }
    </style>
</head>

<body>
    <h1>Cashier Collection Summary - Cashier 1</h1>
    <p>Date: July 13, 2025</p>

    <h2>College (Tuition Fee)</h2>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>DATE</th>
                <th>OR #</th>
                <th>NAME</th>
                <th>COURSE/YEAR</th>
                <th>GRADING PERIOD</th>
                <th>METHOD</th>
                <th>AMOUNT</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collegePayments as $p)
            <tr>
                <td>{{ \Carbon\Carbon::parse($p->payment_date)->toDateString() }}</td>
                <td>{{ $p->or_number }}</td>
                <td>{{ $p->student->full_name ?? 'N/A' }}</td>
                <td>{{ $p->student->courseMapping->program->code ?? 'N/A' }} - {{$p->student->courseMapping->yearLevel->name}}</td>
                <td>{{ $p->grading_period }}</td>
                <td>{{ $p->payment_method }}</td>
                <td>₱{{ number_format($p->amount, 2) }}</td>
                <td>{{ $p->is_void ? 'Voided' : 'Paid' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Subtotal (Net of Voided): ₱72,000.00</strong></p>

    <h2>Senior High</h2>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>DATE</th>
                <th>OR #</th>
                <th>NAME</th>
                <th>STRAND/YEAR</th>
                <th>REMARKS</th>
                <th>METHOD</th>
                <th>AMOUNT</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shsPayments as $p)
            <tr>
                <td>{{ \Carbon\Carbon::parse($p->payment_date)->toDateString() }}</td>
                <td>{{ $p->or_number }}</td>
                <td>{{ $p->student->full_name}}</td>
                <td>{{$p->student->enrollment->strand}} - Grade {{$p->student->enrollment->grade_level}}</td>
                <td>{{ $p->remarks ?? ''}}</td>
                <td>{{ $p->payment_method ?? '' }}</td>
                <td>₱{{ $p->amount ?? 0.00}}</td>
                <td>{{ $p->is_void ? 'Voided' : 'Paid' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Subtotal: ₱20,000.00</strong></p>

    <h2>Other Fees</h2>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>DATE</th>
                <th>OR #</th>
                <th>NAME</th>
                <th>COURSE/STRAND</th>
                <th>REMARKS</th>
                <th>AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($otherPayments as $p)
            <tr>
                <td>{{ \Carbon\Carbon::parse($p->payment_date)->toDateString() }}</td>
                <td>{{ $p->or_number }}</td>
                <td>{{ $p->college->full_name ?? $p->shs->full_name}}</td>
                <td>{{$p->shs->enrollment->strand ?? $p->college->courseMapping->program->code}}</td>
                <td>{{ $p->remarks }}</td>
                <td>₱{{ $p->amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Subtotal: ₱450.00</strong></p>

    <h2>Uniform</h2>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>DATE</th>
                <th>TXN #</th>
                <th>NAME</th>
                <th>COURSE/STRAND</th>
                <th>REMARKS</th>
                <th>AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($uniformPayments as $p)
            <tr>
                <td>{{ \Carbon\Carbon::parse($p->payment_date)->toDateString() }}</td>
                <td>{{ $p->trans_no }}</td>
                <td>{{ $p->college->full_name ?? $p->shs->full_name}}</td>
                <td>{{$p->shs->enrollment->strand ?? $p->college->courseMapping->program->code}}</td>
                <td>{{ $p->remarks }}</td>
                <td>₱{{ $p->amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Subtotal: ₱1,050.00</strong></p>

    <h2>Old Account</h2>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>DATE</th>
                <th>OR #</th>
                <th>NAME</th>
                <th>COURSE/STRAND</th>
                <th>PARTICULAR</th>
                <th>AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($oldPayments as $p)
            <tr>
                <td>{{ \Carbon\Carbon::parse($p->payment_date)->toDateString() }}</td>
                <td>{{ $p->or_number }}</td>
                <td>{{ $p->student->name }}</td>
                <td>{{$p->student->course_strand}}</td>
                <td>{{ $p->particulars }}</td>
                <td>₱{{ $p->amount }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Subtotal: ₱3,500.00</strong></p>

    <h2>Voided Transactions Log</h2>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>DATE</th>
                <th>OR #</th>
                <th>PAYEE NAME</th>
                <th>CATEGORY</th>
                <th>AMOUNT</th>
                <th>VOIDED BY</th>
                <th>REASON</th>
            </tr>
        </thead>
        <tbody>
            @foreach($voided as $v)
            <tr>
                <td>{{ \Carbon\Carbon::parse($v->payment_date)->toDateString() }}</td>
                <td>{{ $v->or_number ?? $v->trans_no ?? 'N/A' }}</td>
                <td>
                    {{ $v->student->full_name 
                    ?? $v->college->full_name 
                    ?? $v->shs->full_name 
                    ?? 'N/A' }}
                </td>
                <td>
                    @php
                        if (isset($v->category)) {
                            echo $v->category;
                        } elseif (isset($v->particulars)) {
                            echo 'Old Account';
                        } elseif (isset($v->remarks)) {
                            echo 'Other Fee';
                        } elseif (isset($v->grading_period)) {
                            echo 'College';
                        } elseif (isset($v->student->enrollment->strand)) {
                            echo 'SHS';
                        } else {
                            echo 'Unknown';
                        }
                    @endphp
                </td>
                <td>₱{{ number_format($v->amount ?? 0, 2) }}</td>
                <td>{{ $v->voided_by->name ?? 'Unknown' }}</td>
                <td>{{ $v->void_reason ?? 'No reason given' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Actual vs System Total Summary</h2>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>CASHIER</th>
                <th>SYSTEM TOTAL</th>
                <th>ACTUAL COLLECTION</th>
                <th>VARIANCE</th>
                <th>NOTES</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $collection->cashier->name }}</td>
                <td>₱{{ $collection->system_collection}}</td>
                <td>₱{{ $collection->actual_collection}}</td>
                <td>₱{{ $collection->variance}}</td>
                <td>{{ $collection->note}}</td>
            </tr>
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>

</html>

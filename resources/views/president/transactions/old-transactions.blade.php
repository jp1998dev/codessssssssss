@extends('layouts.main')

@section('tab_title', 'Accounting Dashboard')
@section('president_sidebar')
@include('president.president_sidebar')
@endsection

@section('content')

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        @include('layouts.topbar')

        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Old Account Transactions</h1>
            </div>

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="date" id="old-from-date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="date" id="old-to-date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Year Graduated</label>
                    <select id="old-year-level" class="form-select">
                        <option value="">All</option>
                        @foreach($payments->pluck('year_graduated')->unique()->filter() as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Course/Strand</label>
                    <select id="old-program" class="form-select">
                        <option value="">All</option>
                        @foreach($payments->pluck('course_strand')->unique()->filter() as $course)
                        <option value="{{ $course }}">{{ $course }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="mb-2">
                <button id="old-exportPdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>
                <button id="old-exportExcel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</button>
                <button id="old-printView" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="oldTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>OR Number</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Course/Strand</th>
                            <th>Year Graduated</th>
                            <th>Remarks</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->or_number ?? 'Unknown' }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}</td>
                            <td>{{ $payment->full_name }}</td>
                            <td>{{ $payment->course_strand }}</td>
                            <td>{{ $payment->year_graduated }}</td>
                            <td>{{ $payment->remarks ?? 'Unknown' }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @include('layouts.footer')
    </div>
</div>
@endsection

<!-- Scripts -->
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function() {
        const oldTable = $('#oldTable').DataTable({
            dom: 'Bfrtip',
            bbuttons: [{
                    extend: 'excelHtml5',
                    title: 'Old Account Transactions'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Old Account Transactions',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader = {
                            bold: true,
                            fontSize: 12,
                            color: 'white',
                            fillColor: '#007bff',
                            alignment: 'center'
                        };
                        doc.styles.title = {
                            fontSize: 18,
                            bold: true,
                            alignment: 'center'
                        };
                        doc.content[1].table.widths = ['*', '*', '*', '*', '*', '*', '*'];
                        doc.content[1].margin = [0, 0, 0, 0];
                    }
                },
                {
                    extend: 'print',
                    title: 'Old Account Transactions'
                }
            ],
            initComplete: function() {
                $('.dt-buttons').hide();
            },
            order: [
                [1, 'desc']
            ],
            responsive: true
        });

        // Filters
        $.fn.dataTable.ext.search.push(function(settings, data) {
            if (settings.nTable.id !== 'oldTable') return true;

            const date = new Date(data[1]);
            const from = $('#old-from-date').val() ? new Date($('#old-from-date').val()) : null;
            const to = $('#old-to-date').val() ? new Date($('#old-to-date').val()) : null;
            const program = $('#old-program').val();
            const year = $('#old-year-level').val();

            const matchDate = (!from || date >= from) && (!to || date <= to);
            const matchProgram = !program || data[3].includes(program);
            const matchYear = !year || data[4].includes(year);

            return matchDate && matchProgram && matchYear;
        });

        $('#old-from-date, #old-to-date, #old-program, #old-year-level').on('change', function() {
            oldTable.draw();
        });

        $('#old-exportPdf').on('click', () => oldTable.button('.buttons-pdf').trigger());
        $('#old-exportExcel').on('click', () => oldTable.button('.buttons-excel').trigger());
        $('#old-printView').on('click', () => oldTable.button('.buttons-print').trigger());
    });
</script>
@endsection
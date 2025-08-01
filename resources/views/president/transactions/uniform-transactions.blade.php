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
            @php
            $strands = \App\Models\Strand::all();
            $programs = \App\Models\Program::all();
            $yearLevels = \App\Models\YearLevel::all();
            @endphp
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Uniform Transactions</h1>
            </div>
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-3" id="transactionTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="shs-tab" data-toggle="tab" href="#shs" role="tab">Senior High</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="college-tab" data-toggle="tab" href="#college" role="tab">College</a>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="transactionTabsContent">

                <!-- SHS TAB -->
                <div class="tab-pane fade show active" id="shs" role="tabpanel">
                    <!-- SHS Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" id="shs-from-date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" id="shs-to-date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Grade Level</label>
                            <select id="shs-grade-level" class="form-select">
                                <option value="">All</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Strand</label>
                            <select id="shs-strand" class="form-select">
                                <option value="">All</option>
                                @foreach($strands as $strand)
                                <option value="{{ $strand->strand_name }}">{{ $strand->strand_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- SHS Export Buttons -->
                    <div class="mb-2">
                        <button id="shs-exportPdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>
                        <button id="shs-exportExcel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</button>
                        <button id="shs-printView" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
                    </div>

                    <!-- SHS Table -->
                    <div class="table-responsive">
                        <table id="shsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Transaction No.</th>
                                    <th>Date</th>
                                    <th>Name of Payee</th>
                                    <th>Strand & Grade Level</th>
                                    <th>Remarks</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shspayments as $payment)
                                <tr>
                                    <td>{{ $payment->trans_no ?? 'Unknown' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}</td>
                                    <td>{{ $payment->full_name }}</td>
                                    <td>{{ $payment->strand }} - Grade {{ $payment->grade_level }}</td>
                                    <td>{{ $payment->remarks ?? 'Unknown' }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- COLLEGE TAB -->
                <div class="tab-pane fade" id="college" role="tabpanel">
                    <!-- College Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" id="college-from-date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" id="college-to-date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Year Level</label>
                            <select id="college-year-level" class="form-select">
                                <option value="">All</option>
                                @foreach($yearLevels as $level)
                                <option value="{{ $level->name }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Program</label>
                            <select id="college-program" class="form-select">
                                <option value="">All</option>
                                @foreach($programs as $program)
                                <option value="{{ $program->code }}">{{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- College Export Buttons -->
                    <div class="mb-2">
                        <button id="college-exportPdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>
                        <button id="college-exportExcel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</button>
                        <button id="college-printView" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
                    </div>

                    <!-- College Table -->
                    <div class="table-responsive">
                        <table id="collegeTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Transaction No.</th>
                                    <th>Date</th>
                                    <th>Name of Payee</th>
                                    <th>Course</th>
                                    <th>Year Level & Semester</th>
                                    <th>Remarks</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collegespayments as $payment)
                                <tr>
                                    <td>{{ $payment->trans_no ?? 'Unknown' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}</td>
                                    <td>{{ $payment->full_name }}</td>
                                    <td>{{ $payment->course }}</td>
                                    <td>{{ $payment->year_level }} - {{ $payment->semester }}</td>
                                    <td>{{ $payment->remarks ?? 'Unknown' }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- End of Main Content -->

        @include('layouts.footer')

    </div>
</div>
<!-- End of Content Wrapper -->
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- REQUIRED for DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function() {
        // SHS DataTable
        const shsTable = $('#shsTable').DataTable({
            initComplete: function() {
                $('.dt-buttons').hide();
            },
            order: [
                [1, 'desc']
            ],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'Senior High Other Transactions'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Senior High Other Transactions',
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
                        doc.content[1].table.widths = ['*', '*', '*', '*', '*', '*'];
                        doc.content[1].margin = [0, 0, 0, 0];
                    }
                },
                {
                    extend: 'print',
                    title: 'Senior High  Other Transactions'
                }
            ],
            responsive: true
        });

        // College DataTable
        const collegeTable = $('#collegeTable').DataTable({
            order: [
                [1, 'desc']
            ],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'College Other Transactions'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'College Other Transactions',
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
                    title: 'College  Other Transactions'
                }
            ],
            initComplete: function() {
                $('.dt-buttons').hide();
            },
            responsive: true
        });


        $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust();
        });

        // SHS Filters
        $.fn.dataTable.ext.search.push(function(settings, data) {
            if (settings.nTable.id !== 'shsTable') return true;

            const date = new Date(data[1]);
            const from = $('#shs-from-date').val() ? new Date($('#shs-from-date').val()) : null;
            const to = $('#shs-to-date').val() ? new Date($('#shs-to-date').val()) : null;
            const grade = $('#shs-grade-level').val();
            const strand = $('#shs-strand').val();
            const levelStrand = `${strand} - Grade ${grade}`;

            const matchDate = (!from || date >= from) && (!to || date <= to);
            const matchStrandGrade = (!strand && !grade) || data[3].includes(levelStrand);

            return matchDate && matchStrandGrade;
        });

        // College Filters
        $.fn.dataTable.ext.search.push(function(settings, data) {
            if (settings.nTable.id !== 'collegeTable') return true;

            const date = new Date(data[1]);
            const from = $('#college-from-date').val() ? new Date($('#college-from-date').val()) : null;
            const to = $('#college-to-date').val() ? new Date($('#college-to-date').val()) : null;
            const program = $('#college-program').val();
            const year = $('#college-year-level').val();

            const matchDate = (!from || date >= from) && (!to || date <= to);
            const matchProgram = !program || data[3].includes(program);
            const matchYear = !year || data[4].includes(year);

            return matchDate && matchProgram && matchYear;
        });

        // SHS filter events
        $('#shs-from-date, #shs-to-date, #shs-grade-level, #shs-strand').on('change', function() {
            shsTable.draw();
        });

        // College filter events
        $('#college-from-date, #college-to-date, #college-program, #college-year-level').on('change', function() {
            collegeTable.draw();
        });

        // Export buttons
        $('#shs-exportPdf').on('click', () => shsTable.button('.buttons-pdf').trigger());
        $('#shs-exportExcel').on('click', () => shsTable.button('.buttons-excel').trigger());
        $('#shs-printView').on('click', () => shsTable.button('.buttons-print').trigger());

        $('#college-exportPdf').on('click', () => collegeTable.button('.buttons-pdf').trigger());
        $('#college-exportExcel').on('click', () => collegeTable.button('.buttons-excel').trigger());
        $('#college-printView').on('click', () => collegeTable.button('.buttons-print').trigger());
    });
</script>
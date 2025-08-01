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
            $programs = \App\Models\Program::all();
            $yearLevels = \App\Models\YearLevel::all();
            @endphp
            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">College Transactions</h1>
                <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                        class="fas fa-download fa-sm text-white-50"></i> Download Report</a> -->
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <!-- <div class="card-header">
                            <input type="date" id="from">
                            <input type="date" id="to">
                        </div> -->
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">

                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <label for="from-date" class="small mb-1">From Date</label>
                                <input type="date" id="from-date" class="form-control" />
                            </div>

                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <label for="to-date" class="small mb-1">To Date</label>
                                <input type="date" id="to-date" class="form-control" />
                            </div>

                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <label for="year-level-filter" class="small mb-1">Year Level</label>
                                <select name="year_level" class="form-select" id="year-level-filter">
                                    <option value="">All</option>
                                    @foreach($yearLevels as $levels)
                                    <option value="{{ $levels->name }}">{{ $levels->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex flex-column" style="max-width: 400px;">
                                <label for="program-filter" class="small mb-1">Program</label>
                                <select name="program" class="form-select" id="program-filter">
                                    <option value="">All</option>
                                    @foreach($programs as $program)
                                    <option value="{{ $program->code }}">{{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button id="exportPdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>
                                <button id="exportExcel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</button>
                                <button id="printView" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
                            </div>
                        </div>



                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="paymentsTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>OR No.</th>
                                            <th>Date</th>
                                            <th>Name of Payee</th>
                                            <th>Course & Year Level</th>
                                            <th>Grading Period</th>
                                            <th>Payment Method</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $payment)
                                        @php
                                        $admission = $admissions->firstWhere(
                                        'student_id',
                                        $payment->student_id,
                                        );
                                        $payorName = $admission
                                        ? $admission->first_name .
                                        ' ' .
                                        $admission->middle_name .
                                        ' ' .
                                        $admission->last_name
                                        : 'Unknown Payor';
                                        $gradingPeriod = $payment ? $payment->grading_period : 'Unknown';
                                        $courseName =
                                        $admission &&
                                        $admission->programCourseMapping &&
                                        $admission->programCourseMapping->program
                                        ? $admission->programCourseMapping->program->code
                                        : 'Unknown Course';

                                        $level = $admission->programCourseMapping?->yearLevel?->name
                                        @endphp
                                        <tr>
                                            <td>{{ $payment->or_number }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}
                                            </td>
                                            <td>{{ $payorName }}</td>
                                            <td>{{ $courseName }} - {{ $level }}</td>
                                            <td>{{ $gradingPeriod }}</td>
                                            <td>{{ $payment->payment_method ?? 'Unknown Method'}}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            <td>Paid</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- End Page Content -->




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
        const table = $('#paymentsTable').DataTable({
            responsive: true,
            pageLength: 10,
            order: [
                [1, 'asc']
            ],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'College Transactions'
                },
                {
                    extend: 'pdfHtml5',
                    text: 'PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'College Transactions',
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
                    title: 'College Transactions'
                }
            ],
            initComplete: function() {
                $('.dt-buttons').hide();
            }
        });

        // Custom filter for date range
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const dateColumnIndex = 1; // Assuming date is in the second column (zero-indexed)
            const dateStr = data[dateColumnIndex];
            const date = new Date(dateStr);

            // Check if startDate and endDate are defined
            if (window.startDate && window.endDate) {
                return date >= window.startDate && date <= window.endDate;
            }
            return true; // No filtering if dates are not set
        });

        // Function to set date range and redraw table
        function setFilter(startDate, endDate) {
            window.startDate = startDate;
            window.endDate = endDate;
            table.draw();
        }

        // // Event handlers for filter buttons
        // $('#filter-today').on('click', function() {
        //     const today = new Date();
        //     const start = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        //     const end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 1);
        //     setFilter(start, end);
        // });

        // $('#filter-week').on('click', function() {
        //     const today = new Date();
        //     const start = new Date(today.getFullYear(), today.getMonth(), today.getDate() - today
        //         .getDay());
        //     const end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 7);
        //     setFilter(start, end);
        // });

        // $('#filter-month').on('click', function() {
        //     const today = new Date();
        //     const start = new Date(today.getFullYear(), today.getMonth(), 1);
        //     const end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        //     setFilter(start, end);
        // });

        // $('#filter-year').on('click', function() {
        //     const today = new Date();
        //     const start = new Date(today.getFullYear(), 0, 1);
        //     const end = new Date(today.getFullYear(), 11, 31);
        //     setFilter(start, end);
        // });

        // $('#filter-clear').on('click', function() {
        //     window.startDate = null;
        //     window.endDate = null;
        //     table.draw();
        // });
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const dateStr = data[1];
            const paymentDate = new Date(dateStr);

            const fromDateVal = $('#from-date').val();
            const toDateVal = $('#to-date').val();

            if (fromDateVal && toDateVal) {
                const fromDate = new Date(fromDateVal);
                const toDate = new Date(toDateVal);
                toDate.setHours(23, 59, 59, 999);

                return paymentDate >= fromDate && paymentDate <= toDate;
            }

            if (fromDateVal && !toDateVal) {
                const fromDate = new Date(fromDateVal);
                return paymentDate >= fromDate;
            }

            if (!fromDateVal && toDateVal) {
                const toDate = new Date(toDateVal);
                toDate.setHours(23, 59, 59, 999);
                return paymentDate <= toDate;
            }

            return true;
        });

        $('#from-date, #to-date').on('change', function() {
            $('#paymentsTable').DataTable().draw();
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const yearCourse = data[3];

            const selectedProgram = $('#program-filter').val();
            const selectedYear = $('#year-level-filter').val();
            const selectedYearCourse = `${selectedProgram} - ${selectedYear}`;

            const matchProgram = selectedYearCourse === "" || yearCourse.includes(selectedYearCourse);
            return matchProgram;
        });
        $('#year-level-filter, #program-filter').on('change', function() {
            table.draw();
        });

        $('#exportPdf').on('click', function() {
            table.button('.buttons-pdf').trigger();
        });
        $('#exportExcel').on('click', function() {
            table.button('.buttons-excel').trigger();
        });
        $('#printView').on('click', function() {
            table.button('.buttons-print').trigger();
        });
    });
</script>
@extends('layouts.main')

@section('tab_title', 'Accounting Dashboard')
@section('president_sidebar')
@include('president.president_sidebar')
@endsection

@section('content')

<style>
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    td div.truncate-cell {
        max-width: 150px;
    }

    @media (max-width: 768px) {
        td div.truncate-cell {
            max-width: 80px;
        }
    }

    #paymentsTable thead tr th {
        background-color: #1cc88a;
    }

    #paymentsTable thead tr th {
        background-color: #1cc88a;
    }

    #typePayee,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    #typePayee td {
        text-align: center;
        font-size: small;
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        @include('layouts.topbar')

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Senior High Student Summary</h1>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <label for="semesterFilter" class="small mb-1">Semester</label>
                                <select id="semesterFilter" class="form-select">
                                    <option value="0">All</option>
                                    @foreach($schoolYears as $y)
                                    <option value="{{ $y->id }}" {{ $y->is_active === 1 ? 'selected' : '' }}>
                                        {{ $y->semester }} {{ $y->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <label for="courseFilter" class="small mb-1">Strand</label>
                                <select class="form-select" name="" id="courseFilter">
                                    <option value="">All</option>
                                    @foreach($strands as $p)
                                    <option value="{{$p->strand_name}}">{{$p->strand_name}}</option>
                                    @endforeach
                                </select>

                            </div>

                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <label class="small mb-1">Balance Range</label>
                                <input type="number" id="balanceMin" class="form-control mb-1" placeholder="Min ₱">
                                <input type="number" id="balanceMax" class="form-control" placeholder="Max ₱">
                            </div>
                            <div>
                                <table id="typePayee">
                                    <thead>

                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>WITH VOUCHER</td>
                                            <td id="withv"></td>
                                        </tr>
                                        <tr>
                                            <td>WITH ESC</td>
                                            <td id="withesc"></td>
                                        </tr>
                                        <tr>
                                            <td>NO VOUCHER</td>
                                            <td id="nov"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end">
                                <button id="exportExcel" class="btn btn-success btn-sm mt-4">Download Report</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="data-loading" class="text-center py-4">
                                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">loading data...</p>
                                </div>
                                <div id="data-content" style="display: none;">
                                    <table id="paymentsTable" class="table table-bordered table-hover nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th colspan="21" class="text-left" style="font-weight: bold; font-size: 21px; background-color: #50ba05ff;">
                                                    STUDENT ACCOUNTS SUMMARY
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="21" class="text-left" style="font-size: 14px; background-color: #faeff7ff;">
                                                    <span id="currentLabel">
                                                        {{ $schoolYears->firstWhere('is_active', 1)->semester ?? '' }} {{ $schoolYears->firstWhere('is_active', 1)->name ?? '' }}
                                                    </span>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th>LRN</th>
                                                <th>Name</th>
                                                <th>Strand</th>
                                                <th>Type of Payee</th>
                                                <th>Initial Payment</th>
                                                <th>Balance</th>
                                                <th colspan="5" class="text-center">Less Payment</th>
                                                <th>Remaining Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Table OUTSIDE of the row container -->
            <div class="card shadow mb-4 mt-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <div id="summary-loading" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">loading data...</p>
                        </div>
                        <div id="summary-content" style="display: none;">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>TOTAL TUITION FEES</th>
                                        <th>TOTAL ENROLLED</th>
                                        <th>TOTAL FULLY PAID</th>
                                        <th>STOPPED</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong class="text-success summary-total-tuition"></strong></td>
                                        <td><strong class="text-success summary-total-enrolled"></strong></td>
                                        <td><strong class="text-success summary-total-paid"></strong></td>
                                        <td><strong class="text-success">0</strong></td>
                                    </tr>
                                </tbody>
                            </table>

                            <table class="table table-bordered text-center mt-3">
                                <thead>
                                    <tr>
                                        <th colspan="3" style="background-color: #e6d7fa;">ASSESSMENT OF FEES</th>

                                        <th rowspan="2" style="background-color: #fff3cd;">LESS PAYMENTS</th>
                                        <th colspan="3" style="background-color: #f9bffaff;">TOTAL OF PAYEE</th>
                                        <th rowspan="2" rowspan="2" style="background-color: #f0f8e2; vertical-align: middle;">TOTAL
                                            BALANCE</th>
                                    </tr>
                                    <tr>
                                        <th style="background-color: #f0f8e2;">TOTAL</th>
                                        <th style="background-color: #f0f8e2;">INITIAL PAYMENT</th>
                                        <th style="background-color: #f0f8e2;">BALANCE</th>
                                        <th style="background-color: #f0f8e2;">WITH VOUCHER</th>
                                        <th style="background-color: #f0f8e2;">WITH ESC</th>
                                        <th style="background-color: #f0f8e2;">NO VOUCHER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong class="summary-assessment">...</strong></td>
                                        <td><strong class="summary-initial">...</strong></td>
                                        <td><strong class="summary-balance">...</strong></td>
                                        <td><strong class="summary-less">...</strong></td>
                                        <td><strong class="summary-esc">...</strong></td>
                                        <td><strong class="summary-voucher">...</strong></td>
                                        <td><strong class="summary-novoucher">...</strong></td>

                                        <td><strong class="summary-balance">...</strong></td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const schoolYearSelect = document.getElementById('semesterFilter');
        const courseFilter = document.getElementById('courseFilter');
        // const balanceMin = document.getElementById('balanceMin');
        // const balanceMax = document.getElementById('balanceMax');

        function fetchSummary(schoolYearId) {
            document.getElementById('summary-loading').style.display = 'block';
            document.getElementById('summary-content').style.display = 'none';
            document.getElementById('data-loading').style.display = 'block';
            document.getElementById('data-content').style.display = 'none';
            fetch(`/vpadmin/shs-summary/data/${schoolYearId}`)
                .then(res => res.json())
                .then(data => {
                    console.log(data);
                    if (!data.students) {
                        alert("No data returned");
                        return;
                    }


                    const selected = schoolYearSelect.options[schoolYearSelect.selectedIndex].text;
                    document.getElementById('currentLabel').innerText = selected;

                    const dataTable = $('#paymentsTable').DataTable();
                    dataTable.clear();

                    data.students.forEach(d => {
                        dataTable.row.add([
                            `<div class="truncate-cell text-truncate">${d.lrn_number}</div>`,
                            `<div class="truncate-cell text-truncate">${d.name}</div>`,
                            `<div class="truncate-cell text-truncate">${d.strand}</div>`,
                            `<div class="truncate-cell text-truncate">${d.type_of_pee}</div>`,
                            `₱${!isNaN(parseFloat(d.initial_payment)) ? parseFloat(d.initial_payment).toFixed(2) : '0.00'}`,
                            `₱${!isNaN(parseFloat(d.balance)) ? parseFloat(d.balance).toFixed(2) : '0.00'}`,
                            `₱${!isNaN(parseFloat(d.less_payments[0])) ? parseFloat(d.less_payments[0]).toFixed(2) : '0.00'}`,
                            `₱${!isNaN(parseFloat(d.less_payments[1])) ? parseFloat(d.less_payments[1]).toFixed(2) : '0.00'}`,
                            `₱${!isNaN(parseFloat(d.less_payments[2])) ? parseFloat(d.less_payments[2]).toFixed(2) : '0.00'}`,
                            `₱${!isNaN(parseFloat(d.less_payments[3])) ? parseFloat(d.less_payments[3]).toFixed(2) : '0.00'}`,
                            `₱${!isNaN(parseFloat(d.less_payments[4])) ? parseFloat(d.less_payments[4]).toFixed(2) : '0.00'}`,
                            `₱${!isNaN(parseFloat(d.rem_balance)) ? parseFloat(d.rem_balance).toFixed(2) : '0.00'}`
                        ]);
                    });


                    dataTable.draw();


                    const summary = data.summary;
                    // document.querySelectorAll('.summary-total-units').forEach(el => el.textContent = summary.total_units);
                    document.querySelectorAll('.summary-total-tuition').forEach(el => el.textContent = parseFloat(summary.total_tuition_fees).toFixed(2));
                    document.querySelectorAll('.summary-total-enrolled').forEach(el => el.textContent = summary.total_enrolled);
                    document.querySelectorAll('.summary-total-paid').forEach(el => el.textContent = summary.total_fully_paid);
                    document.querySelectorAll('.summary-assessment').forEach(el => el.textContent = parseFloat(summary.assessment_total).toFixed(2));
                    document.querySelectorAll('.summary-initial').forEach(el => el.textContent = parseFloat(summary.initial_payment).toFixed(2));
                    document.querySelectorAll('.summary-balance').forEach(el => el.textContent = parseFloat(summary.balance_total).toFixed(2));

                    document.querySelectorAll('.summary-less').forEach(el => el.textContent = parseFloat(summary.total_less).toFixed(2));
                    document.querySelectorAll('.summary-voucher').forEach(el => el.textContent = summary.voucher_total);
                    document.querySelectorAll('.summary-esc').forEach(el => el.textContent = summary.esc_total);
                    document.querySelectorAll('.summary-novoucher').forEach(el => el.textContent = summary.novoucher_total);
                    document.getElementById('withv').textContent = summary.voucher_count;
                    document.getElementById('withvesc').textContent = summary.esc_count;
                    document.getElementById('nov').textContent = summary.novoucher_count;

                    document.getElementById('summary-loading').style.display = 'none';
                    document.getElementById('summary-content').style.display = 'block';
                    document.getElementById('data-loading').style.display = 'none';
                    document.getElementById('data-content').style.display = 'block';
                });
        }

        schoolYearSelect.addEventListener('change', () => {
            fetchSummary(schoolYearSelect.value);
        });

        courseFilter.addEventListener('change', () => fetchSummary(schoolYearSelect.value));
        // balanceMin.addEventListener('input', () => fetchSummary(schoolYearSelect.value));
        // balanceMax.addEventListener('input', () => fetchSummary(schoolYearSelect.value));

        fetchSummary(schoolYearSelect.value);
    });
</script>

<script>
    $(document).ready(function() {
        const table = $('#paymentsTable').DataTable({
            responsive: true,
            pageLength: 10,
            dom: 'Blfrtip',
            buttons: [{
                extend: 'excelHtml5',
                title: 'Student Summary',
                text: 'Export Excel'
            }],
            initComplete: function() {
                $('.dt-buttons').hide();
            }
        });

        $('#courseFilter').on('keyup change', function() {
            table.column(2).search(this.value).draw()
        });

        $('#paidStatusFilter').on('change', function() {
            table.column(21).search(this.value).draw();
        });
        // $('#semesterFilter').on('change', function() {
        //     let selectedSemester = $(this).val() || 'All';


        //     $('#currentLabel').text(selectedSemester !== 'All' ? selectedSemester : 'All Semesters');
        //     table.column(0).search(this.value).draw();
        // });


        $.fn.dataTable.ext.search.push(function(settings, data) {
            let min = parseFloat($('#balanceMin').val().replace(/[^0-9.-]+/g, "")) || 0;
            let max = parseFloat($('#balanceMax').val().replace(/[^0-9.-]+/g, "")) || Infinity;
            let balance = parseFloat(data[19].replace(/[^0-9.-]+/g, "")) || 0;

            return balance >= min && balance <= max;
        });

        $('#balanceMin, #balanceMax').on('keyup change', function() {
            table.draw();
        });


    });
</script>
<script>
    document.getElementById('exportExcel').addEventListener('click', async () => {
        try {
            const response = await fetch('/templates/Student_Accounts_Summary.xlsx');
            const buffer = await response.arrayBuffer();

            const workbook = new ExcelJS.Workbook();
            await workbook.xlsx.load(buffer);

            const sheet = workbook.getWorksheet(1);
            const startRow = 6;

            const table = $('#paymentsTable').DataTable();
            const rows = [];


            const currentLabelText = $('#currentLabel').text().trim();


            const headerRow = sheet.getRow(2);


            headerRow.eachCell({
                includeEmpty: true
            }, (cell) => {
                cell.value = currentLabelText;

            });

            headerRow.commit();


            table.rows({
                search: 'applied'
            }).every(function() {
                const rowData = this.data();
                const cleanRow = rowData.map(cell => {
                    const text = $('<div>').html(cell).text().trim();
                    return text.replace(/^₱/, '').trim();
                });
                rows.push(cleanRow);
            });


            rows.forEach((row, i) => {
                const targetRow = sheet.getRow(startRow + i);
                targetRow.values = row;

                const templateRow = sheet.getRow(startRow);
                templateRow.eachCell({
                    includeEmpty: true
                }, (cell, colNumber) => {
                    const targetCell = targetRow.getCell(colNumber);
                    if (cell.style) {
                        targetCell.style = JSON.parse(JSON.stringify(cell.style));
                    }
                });

                targetRow.commit();
            });

            const bufferOut = await workbook.xlsx.writeBuffer();
            const blob = new Blob([bufferOut], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'StudentSummary_Filled.xlsx';
            link.click();
            URL.revokeObjectURL(url);
        } catch (err) {
            console.error('Export failed:', err);
        }
    });
</script>




@endsection
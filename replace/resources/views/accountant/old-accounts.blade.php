@extends('layouts.main')

@section('tab_title', 'Old Accounts')
@section('accountant_sidebar')
@include('accountant.accountant_sidebar')
@endsection

@section('content')
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        @include('layouts.topbar')

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Old Accounts</h1>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#newAccountModal">
                                <i class="fas fa-plus-circle"></i> New Account
                            </button>
                        </div>
                        <div class="card-header flex-wrap d-flex gap-3 align-items-end">
                            <div>
                                <label class="small mb-1">From</label>
                                <input type="date" id="filter-from" class="form-control form-control-sm">
                            </div>
                            <div>
                                <label class="small mb-1">To</label>
                                <input type="date" id="filter-to" class="form-control form-control-sm">
                            </div>
                            <div>
                                <label class="small mb-1">Name</label>
                                <input type="text" id="filter-name" class="form-control form-control-sm" placeholder="Search name">
                            </div>
                            <div>
                                <label class="small mb-1">Course</label>
                                <input type="text" id="filter-course" class="form-control form-control-sm" placeholder="Search course">
                            </div>
                            <div>
                                <label class="small mb-1">Year Graduated</label>
                                <select id="filter-year" class="form-control form-control-sm">
                                    <option value="">All</option>
                                    @foreach($oldAccounts->pluck('year_graduated')->unique()->sort() as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="small mb-1">Particular</label>
                                <input type="text" id="filter-particular" class="form-control form-control-sm" placeholder="Search particular">
                            </div>
                            <div>
                                <label class="small mb-1">Remarks</label>
                                <input type="text" id="filter-remarks" class="form-control form-control-sm" placeholder="Search remarks">
                            </div>

                            <div class="d-flex gap-2 ms-auto">
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
                                            <th>Date</th>
                                            <th>Name</th>
                                            <th>Course/Strand</th>
                                            <th>Year Graduated</th>
                                            <th>Particular</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($oldAccounts as $account)
                                        <tr data-id="{{ $account->id }}">
                                            <td>{{ \Carbon\Carbon::parse($account->created_at)->format('M d, Y') }}</td>
                                            <td>{{ $account->name }}</td>
                                            <td>{{ $account->course_strand }}</td>
                                            <td>{{ $account->year_graduated }}</td>
                                            <td>{{ $account->particular }}</td>
                                            <td>{{ $account->balance }}</td>
                                            <td>{{ $account->remarks }}</td>
                                            <td>
                                                <span class="badge {{ $account->is_paid ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $account->is_paid ? 'Paid' : 'Unpaid' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success mark-paid">Mark as Paid</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="newAccountModal" tabindex="-1" aria-labelledby="newAccountLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="newAccountForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newAccountLabel">New Old Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="form-error" class="alert alert-danger d-none"></div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course/Strand</label>
                                <input type="text" class="form-control" name="course_strand" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Year Graduated</label>
                                <input type="text" class="form-control" name="year_graduated" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Balance</label>
                                <input type="number" step="0.01" class="form-control" name="balance" value="0.00">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Particular</label>
                                <input type="text" class="form-control" name="particular" value="Back Account">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <input type="text" class="form-control" name="remarks">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Save Account</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @include('layouts.footer')
    </div>
</div>
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
@section('scripts')
<script>
    $('#newAccountForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const url = `{{ route('accountant.old_accounts.store') }}`;
        const data = form.serialize();

        $('#form-error').addClass('d-none').html('');

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(res) {
                $('#newAccountModal').modal('hide');
                form[0].reset();
                $('#ajax-success').removeClass('d-none').html(res.message);
                location.reload();
            },
            error: function(err) {
                let msg = 'Something went wrong.';

                if (err.status === 422 && err.responseJSON?.errors) {
                    msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                } else if (err.responseJSON?.message) {
                    msg = err.responseJSON.message;
                } else if (err.responseText) {
                    msg = err.responseText;
                }

                $('#form-error').removeClass('d-none').html(msg);
            }
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('.mark-paid').on('click', function() {
            const row = $(this).closest('tr');
            const id = row.data('id');

            $.ajax({
                url: `/accountant/old-accounts/${id}/mark-as-paid`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    location.reload();
                },
                error: function(err) {
                    $('#ajax-error').removeClass('d-none').html('Failed to mark as paid.');
                    console.error(err);
                }
            });
        });

    });
</script>

<script>
    $(document).ready(function() {
        const table = $('#paymentsTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'Old Accounts',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Old Accounts',
                    exportOptions: {
                        columns: ':not(:last-child)'
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
                    }
                },
                {
                    extend: 'print',
                    title: 'Old Accounts',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }
            ],
            order: [],
            initComplete: function() {
                $('.dt-buttons').hide();
            }
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            const from = $('#filter-from').val();
            const to = $('#filter-to').val();
            const name = $('#filter-name').val().toLowerCase();
            const course = $('#filter-course').val().toLowerCase();
            const year = $('#filter-year').val().toLowerCase();
            const particular = $('#filter-particular').val().toLowerCase();
            const remarks = $('#filter-remarks').val().toLowerCase();

            const date = new Date(data[0]); 
            const nameData = data[1].toLowerCase();
            const courseData = data[2].toLowerCase();
            const yearData = data[3].toLowerCase();
            const particularData = data[4].toLowerCase();
            const remarksData = data[6].toLowerCase();

            if (from && date < new Date(from)) return false;
            if (to && date > new Date(to)) return false;
            if (name && !nameData.includes(name)) return false;
            if (course && !courseData.includes(course)) return false;
            if (year && year !== '' && yearData !== year) return false;
            if (particular && !particularData.includes(particular)) return false;
            if (remarks && !remarksData.includes(remarks)) return false;

            return true;
        });


        $('#filter-from, #filter-to, #filter-name, #filter-course, #filter-year, #filter-particular, #filter-remarks').on('input change', function() {
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
@endsection
@extends('layouts.main')

@section('tab_title', 'Dashboard')
@section('vpadmin_sidebar')
@include('vp_admin.vpadmin_sidebar')
@endsection


@section('content')
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        @include('layouts.topbar')

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Bank Deposit</h1>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                       
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
                                <label class="small mb-1">Deposit Slip #</label>
                                <input type="text" id="filter-slip" class="form-control form-control-sm" placeholder="Enter slip #">
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
                            <div class="mb-2">
                                <h5 class="text-end text-primary fw-bold">
                                    TOTAL BANKED: ₱{{ number_format($totalBanked ?? 0.00, 2) }}
                                </h5>
                            </div>
                            <div class="table-responsive">
                                <table id="paymentsTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>System Collection</th>
                                            <th>Total Deposit</th>
                                            <th>Deposit Slip #</th>
                                            <th>Remarks</th>
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($deposits as $deposit)
                                        <tr data-id="{{ $deposit->id }}">
                                            <td>{{ \Carbon\Carbon::parse($deposit->created_at)->format('M d, Y') }}</td>
                                            <td>{{ number_format($deposit->system_collection, 2) }}</td>

                                            <td class="editable" data-name="total_deposited">{{ number_format($deposit->total_deposited, 2) }}</td>
                                            <td class="editable" data-name="slip">{{ $deposit->slip }}</td>
                                            <td class="editable" data-name="remarks">{{ $deposit->remarks }}</td>

                                          
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
    $(document).ready(function() {
        $('.edit-btn').on('click', function() {
            const row = $(this).closest('tr');
            row.find('.editable').each(function() {
                const value = $(this).text().trim();
                const name = $(this).data('name');
                $(this).html(`<input type="text" class="form-control form-control-sm" name="${name}" value="${value}">`);
            });
            row.find('.edit-btn').addClass('d-none');
            row.find('.save-btn, .cancel-btn').removeClass('d-none');
        });

        $('.cancel-btn').on('click', function() {
            location.reload();
        });

        $('.save-btn').on('click', function() {
            const row = $(this).closest('tr');
            const id = row.data('id');

            const data = {
                total_deposited: row.find('input[name="total_deposited"]').val(),
                slip: row.find('input[name="slip"]').val(),
                remarks: row.find('input[name="remarks"]').val(),
                _token: '{{ csrf_token() }}',
            };

            $.ajax({
                url: `/accountant/deposits/${id}`,
                method: 'PUT',
                data: data,
                success: function(res) {
                    location.reload();
                },
                error: function(err) {
                    console.error(err);
                    let message = 'Failed to update.';

                    if (err.responseJSON && err.responseJSON.message) {
                        message = err.responseJSON.message;
                    } else if (err.responseJSON && err.responseJSON.errors) {
                        message = Object.values(err.responseJSON.errors).flat().join('<br>');
                    }

                    $('#ajax-error').removeClass('d-none').html(message);
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
            // buttons: ['excel', 'pdf', 'print'],
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'Bank Deposits',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Bank Deposits',
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
                        doc.content[1].table.widths = ['*', '*', '*', '*', '*', '*'];
                    }
                },
                {
                    extend: 'print',
                    title: 'Bank Deposits',
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
            const slipSearch = $('#filter-slip').val().toLowerCase();
            const remarksSearch = $('#filter-remarks').val().toLowerCase();

            const date = data[0]
            const slip = data[3].toLowerCase();
            const remarks = data[4].toLowerCase();

            const tableDate = new Date(date);

            if (from) {
                const fromDate = new Date(from);
                if (tableDate < fromDate) return false;
            }

            if (to) {
                const toDate = new Date(to);
                if (tableDate > toDate) return false;
            }

            if (slipSearch && !slip.includes(slipSearch)) return false;
            if (remarksSearch && !remarks.includes(remarksSearch)) return false;

            return true;
        });


        $('#filter-from, #filter-to, #filter-slip, #filter-remarks').on('input change', function() {
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
@extends('layouts.main')

@section('tab_title', 'Transactions')
@section('accountant_sidebar')
@include('accountant.accountant_sidebar')
@endsection

@section('content')
@php
$cashiers = \App\Models\User::where('role', 'cashier')->get();
@endphp

<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        @include('layouts.topbar')

        <div class="container-fluid">
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Daily Collection</h1>
            </div>

            <div class="row justify-content-center mt-4">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <label for="from-date" class="small mb-1">Date</label>
                                <input type="date" id="from-date" class="form-control" />
                            </div>

                            <div class="d-flex flex-column" style="max-width: 200px;">
                                <label for="cashier-filter" class="small mb-1">Cashier</label>
                                <select id="cashier-filter" class="form-select">
                                    <option value="">All</option>
                                    @foreach($cashiers as $index => $cashier)
                                    <option value="{{ $cashier->id }}">{{$cashier->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- <div class="d-flex gap-2 mt-4">
                                <button id="exportPdf" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf"></i> PDF</button>
                                <button id="exportExcel" class="btn btn-success btn-sm"><i class="fas fa-file-excel"></i> Excel</button>
                                <button id="printView" class="btn btn-secondary btn-sm"><i class="fas fa-print"></i> Print</button>
                            </div> -->
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="paymentsTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Cashier</th>
                                            <th>Category</th>
                                            <th>Total Transactions</th>
                                            <th>Amount Collected</th>
                                           
                                        </tr>
                                    </thead>
                                   <tbody id="collection-body"></tbody>
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
        const table = $('#paymentsTable').DataTable({
            responsive: true,
            pageLength: 10,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    title: 'Daily Collections'
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Daily Collections',
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
                        doc.content[1].table.widths = ['*', '*', '*', '*'];
                    }
                },
                {
                    extend: 'print',
                    title: 'Daily Collections'
                }
            ],
            initComplete: function() {
                $('.dt-buttons').hide();
            }
        });

        function fetchCollections() {
            const date = $('#from-date').val();
            const cashierId = $('#cashier-filter').val();

            if (!date) {
                $('#collection-body').html('<tr><td colspan="4" class="text-center text-muted">Please select a date.</td></tr>');
                return;
            }

            $.ajax({
                url: '/accountant/daily-collection/data',
                method: 'GET',
                data: {
                    date,
                    cashier_id: cashierId
                },
                success: function(response) {
                    const tbody = $('#collection-body');
                    // tbody.empty();

                    // if (!response.data || response.data.length === 0) {
                    //     tbody.append('<tr><td colspan="4" class="text-center text-muted">No data found.</td></tr>');
                    //     return;
                    // }
                    console.log("data: ", response)
                    table.clear();

                    response.data.forEach(row => {
                        table.row.add([
                            row.cashier ?? '',
                            row.category ?? '',
                            row.count ?? 0,
                            row.total ?? '₱0.00'
                            
                        ]);
                    });

                    table.draw();

                },
                error: function(err) {
                    console.error(err);
                    $('#collection-body').html('<tr><td colspan="4" class="text-danger text-center">Error loading data.</td></tr>');
                }
            });
        }

        $('#from-date, #cashier-filter').on('change', fetchCollections);

        // Auto-set to today
        const today = new Date().toISOString().split('T')[0];
        $('#from-date').val(today).trigger('change');

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
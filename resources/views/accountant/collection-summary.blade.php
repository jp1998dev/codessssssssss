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
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="paymentsTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Cashier</th>
                                            <th>System Total</th>
                                            <th>Actual Collection</th>
                                            <th>Variance</th>
                                            <th>Notes</th>
                                            <th>Action</th>
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
                url: '/accountant/collection-summary/data',
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
                    // console.log("data: ", response.data)
                    const data = response.data;
                    table.clear();
                    data.forEach(row => {
                        const collection = row.collection
                        const actual = parseFloat(collection.actual_collection ?? 0);
                        const system = parseFloat(collection.system_collection ?? 0);
                        const variance = actual - system;

                        table.row.add([
                            row.cashier ?? '',
                            `₱${system.toFixed(2)}`,
                            `<input type="number" step="0.01" class="form-control actual-input" name="actual" data-id="${collection.id}" value="${actual}">`,
                            `<span class="variance-value" data-id="${collection.id}">₱${variance.toFixed(2)}</span>`,
                            `<input type="text" class="form-control note-input" name="note" data-id="${collection.id}" value="${collection.note ?? ''}">`,
                            `<button class="btn btn-sm btn-success submit-btn" data-id="${collection.id}">Submit</button>
                            <button class="btn btn-sm btn-success export-btn" data-id="${collection.id}">Export</button>
                            `,

                        ]);

                    });
                    table.draw();
                },
                error: function(err) {
                    console.error(err);
                    $('#collection-body').html('<tr><td colspan="4" class="text-danger text-center">Error loading data.</td></tr>');
                }
            });
            $('#paymentsTable').on('input', '.actual-input', function() {
                const id = $(this).data('id');
                const actual = parseFloat($(this).val()) || 0;
                const system = parseFloat(
                    $(`.variance-value[data-id="${id}"]`)
                    .closest('tr')
                    .find('td').eq(1).text().replace(/[₱,]/g, '')
                ) || 0;

                const variance = actual - system;
                $(`.variance-value[data-id="${id}"]`).text(`₱${variance.toFixed(2)}`);
            });

        }
        $('#paymentsTable').on('click', '.submit-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const actual = $(`.actual-input[data-id="${id}"]`).val();
            const note = $(`.note-input[data-id="${id}"]`).val();
            console.log(id);
            $.ajax({
                url: '/accountant/daily-collection/submit',
                method: 'POST',
                data: {
                    id: id,
                    actual: actual,
                    note: note,
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    window.location.reload();
                },
                error: function(err) {
                    console.error(err);
                    alert('Error submitting Collection.');
                }
            });
        });

        $('#paymentsTable').on('click', '.export-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            // const actual = $(`.actual-input[data-id="${id}"]`).val();
            // const note = $(`.note-input[data-id="${id}"]`).val();
            console.log(id);
            $.get(`/accountant/collection-summary/print/${id}`, function(html) {
                const printWindow = window.open('', '_blank');
                printWindow.document.open();
                printWindow.document.write(html);
                printWindow.document.close();
                setTimeout(() => {
                    printWindow.document.title = ' ';
                    printWindow.focus();
                }, 100);
            });

        });

        $('#from-date, #cashier-filter').on('change', fetchCollections);

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
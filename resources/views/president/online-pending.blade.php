  <!-- BACK END FUNCTION IS IN THE BILLING CONTROLLER -->
  @extends('layouts.main')

  @section('tab_title', 'President Dashboard')
  @section('president_sidebar')
  @include('president.president_sidebar')
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
                  <h1 class="h3 mb-0 text-gray-800">Pending Online Payments</h1>
              </div>

              <div class="row justify-content-center mt-4">
                  <div class="col-md-12">
                      <div class="card shadow mb-4">

                          <div class="card-body">
                              <div class="mb-3">
                                  <label for="statusFilter" class="form-label">Filter by Status:</label>
                                  <select id="statusFilter" class="form-control" style="max-width: 200px;">
                                      <option value="">All</option>
                                      <option value="pending">Pending</option>
                                      <option value="approved">Approved</option>
                                      <option value="dropped">Dropped</option>
                                  </select>
                              </div>
                              <div class="table-responsive">
                                  <table id="paymentsTable" class="table table-bordered">
                                      <thead>
                                          <tr>
                                              <th>Date</th>
                                              <th>Name</th>
                                              <th>Amount</th>
                                              <th>Transaction No.</th>
                                              <th>Status</th>
                                              <th>Action</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          @foreach($payments as $p)
                                          <tr>
                                              <td>{{ $p->created_at->format('Y-m-d') ?? '' }}</td>
                                              <td>{{ $p->student->full_name ?? 'N/A' }}</td>
                                              <td>{{ $p->amount }}</td>
                                              <td>{{ $p->ref_number }}</td>
                                              <td class="
                                                        @if($p->status === 'pending') text-warning
                                                        @elseif($p->status === 'approved') text-success
                                                        @elseif($p->status === 'dropped') text-danger
                                                        @endif
                                                    ">
                                                  <b>{{ ucfirst($p->status ?? '') }}</b>
                                              </td>

                                              <td>
                                                  @php
                                                  $approveRoute = $p instanceof \App\Models\ShsPayment
                                                  ? route('president.shs.approve', $p->payment_id)
                                                  : route('president.college.approve', $p->id);

                                                  $rejectRoute = $p instanceof \App\Models\ShsPayment
                                                  ? route('president.shs.reject', $p->payment_id)
                                                  : route('president.college.reject', $p->id);
                                                  @endphp

                                                  <form method="POST" action="{{ $approveRoute }}" class="d-inline">
                                                      @csrf
                                                      <button class="btn btn-success btn-sm" title="Approve">
                                                          <i class="fas fa-check"></i>
                                                      </button>
                                                  </form>

                                                  <form method="POST" action="{{ $rejectRoute }}" class="d-inline">
                                                      @csrf
                                                      <button class="btn btn-danger btn-sm" title="Reject">
                                                          <i class="fas fa-times"></i>
                                                      </button>
                                                  </form>
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
          $('#statusFilter').on('change', function() {
              const value = $(this).val();
              const columnIndex = 4;
              $('#paymentsTable').DataTable().column(columnIndex).search(value).draw();
          });

      });
  </script>
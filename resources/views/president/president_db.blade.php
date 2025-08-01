  <!-- BACK END FUNCTION IS IN THE BILLING CONTROLLER -->
  @extends('layouts.main')

  @section('tab_title', 'President Dashboard')
  @section('president_sidebar')
  @include('president.president_sidebar')
  @endsection

  @section('content')

  <div id="content-wrapper" class="d-flex flex-column">

      <div id="content">

          @include('layouts.topbar')

          <div class="container-fluid">

              <!-- Page Headifng -->
              <div class="d-sm-flex align-items-center justify-content-between mb-4">
                  <h1 class="h3 mb-0 text-gray-800">President's Dashboard</h1>

              </div>

              <!-- Example: Export for Daily Sales -->
              <button class="btn btn-success btn-sm mb-2" onclick="exportTableToExcel('dailySalesTable', 'Daily_Sales')">
                  Export Daily Collection
              </button>

              <!-- Example: Export for Enrollment Heatmap -->
              <button class="btn btn-success btn-sm mb-2"
                  onclick="exportTableToExcel('enrollmentHeatmap', 'Enrollment_Heatmap')">
                  Export Enrollment Heatmap
              </button>

              <!-- Example: Export for Unpaid Balances -->
              <button class="btn btn-success btn-sm mb-2" onclick="exportListToExcel('unpaidList', 'Unpaid_Balances')">
                  Export Unpaid Balances
              </button>

              <div class="row">
                  <div class="d-flex flex-wrap justify-content-between">
                      <div class="card mb-4 me-2" style="flex: 1 1 300px; min-width: 400px; max-width: 48%;">
                          @include('shared.daily_collection_card')
                      </div>
                      <div class="card mb-4" style="flex: 1 1 300px; min-width: 400px; max-width: 48%;">
                          @include('shared.semester-collection-card')
                      </div>
                  </div>
              </div>
              <!-- Row: Revenue Dashboard -->
              <div class="row">
                  <!-- Left Column: Two Small Cards -->
                  <div class="col-xl-4 col-lg-5">
                      <!-- Balance Due Card -->
                      <div class="card shadow mb-4">
                          <div class="card-header py-3">
                              <h6 class="m-0 font-weight-bold text-primary">📊 Balance Due (College)</h6>
                          </div>
                          <div class="card-body">
                              <canvas id="scholarshipPieChart"></canvas>
                          </div>
                      </div>
                       <div class="card shadow mb-4">
                          <div class="card-header py-3">
                              <h6 class="m-0 font-weight-bold text-primary">📊 Balance Due (Senior High)</h6>
                          </div>
                          <div class="card-body">
                              <canvas id="scholarshipPieChartShs"></canvas>
                          </div>
                      </div>

                      <!-- Daily Sales Card -->

                  </div>
                  <!-- Initial Payments Pie Chart (4 columns) -->
                  <div class="col-xl-4 col-lg-5 mb-4">
                      <div class="card shadow">
                          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                              <h6 class="m-0 font-weight-bold text-primary">Initial Payments per Program</h6>
                          </div>
                          <div class="card-body">
                              <div class="chart-container" style="position: relative; height: 450px;">
                                  <canvas id="paymentSourcesChart"></canvas>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <div class="d-flex flex-wrap justify-content-between">
                      <div class="card mb-4 me-2" style="flex: 1 1 300px; min-width: 400px; max-width: 48%;">
                          <!-- <div class="col-xl-8 col-lg-7"> -->
                          <div class="card shadow mb-4">
                              <div class="card-header py-3">
                                  <h6 class="m-0 font-weight-bold text-primary">💰College Revenue Trends</h6>
                              </div>
                              <div class="card-body">
                                  <canvas id="revenueChart"></canvas>
                              </div>
                          </div>
                          <!-- </div> -->
                      </div>
                      <div class="card mb-4" style="flex: 1 1 300px; min-width: 400px; max-width: 48%;">
                          <!-- <div class="col-xl-8 col-lg-7"> -->
                          <div class="card shadow mb-4">
                              <div class="card-header py-3">
                                  <h6 class="m-0 font-weight-bold text-primary">💰Senior High Revenue Trends</h6>
                              </div>
                              <div class="card-body">
                                  <canvas id="revenueChartShs"></canvas>
                              </div>
                          </div>
                          <!-- </div> -->
                      </div>
                  </div>
              </div>



              <div>
                  <div class="container">
                      <div class="row">
                          @include('shared.fullpaid-chart')
                      </div>
                      <div class="row">
                          @include('shared.shs-fullpaid-chart')
                      </div>
                  </div>
              </div>
              <!-- Row: Enrollment Analytics -->
              <div class="row">
                  <!-- Heatmap-style Enrollment Table -->
                  <div class="col-xl-12 col-lg-12">
                      <div class="card shadow mb-4">
                          <div class="card-header py-3">
                              <h6 class="m-0 font-weight-bold text-primary"> Program-wise Enrollment Heatmap (College)</h6>
                          </div>
                          <div class="card-body">
                              <table class="table table-bordered text-center" id="enrollmentHeatmap">
                                  @php
                                  $totalsPerYear = [];
                                  $grandTotal = 0;
                                  @endphp
                                  <thead class="thead-dark">
                                      <tr>
                                          <th>Program</th>
                                          <th>1st Year</th>
                                          <th>2nd Year</th>
                                          <th>3rd Year</th>
                                          <th>4th Year</th>
                                          <th>Total</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @foreach ($programs as $program)
                                      @php
                                      $programTotal = 0;
                                      @endphp
                                      <tr>
                                          <td>{{ $program->name }}</td>
                                          @foreach ($yearLevels as $year)
                                          @php
                                          $count = $enrollmentData[$program->name][$year->name] ?? 0;
                                          $programTotal += $count;
                                          $totalsPerYear[$year->name] = ($totalsPerYear[$year->name] ?? 0) + $count;
                                          $grandTotal += $count;

                                          $class = $count >= 100
                                          ? 'bg-success text-white'
                                          : ($count >= 60
                                          ? 'bg-warning text-dark'
                                          : 'bg-danger text-white');
                                          @endphp
                                          <td class="{{ $class }}">{{ $count }}</td>
                                          @endforeach
                                          @php
                                          $class2 = $programTotal >= 100
                                          ? 'bg-success text-white'
                                          : ($programTotal >= 60
                                          ? 'bg-warning text-dark'
                                          : 'bg-danger text-white');
                                          @endphp
                                          <td class="{{ $class2 }}"><strong>{{ $programTotal }}</strong></td>
                                      </tr>
                                      @endforeach

                                      {{-- Total per year level row --}}
                                      <tr class="table-secondary font-weight-bold">
                                          <td><strong>Total</strong></td>
                                          @foreach ($yearLevels as $year)
                                          @php
                                          $count = $totalsPerYear[$year->name] ?? 0;
                                          $class = $count >= 100
                                          ? 'bg-success text-white'
                                          : ($count >= 60
                                          ? 'bg-warning text-dark'
                                          : 'bg-danger text-white');
                                          @endphp
                                          <td class="{{ $class }}"><strong>{{ $count }}</strong></td>
                                          @endforeach
                                          <td class="{{ $class }}"><strong>{{ $grandTotal }}</strong></td>
                                      </tr>
                                  </tbody>
                              </table>

                          </div>
                      </div>
                  </div>
              </div>
              <div class="row">
                  <!-- Heatmap-style Enrollment Table -->
                  <div class="col-xl-12 col-lg-12">
                      <div class="card shadow mb-4">
                          <div class="card-header py-3">
                              <h6 class="m-0 font-weight-bold text-primary"> Program-wise Enrollment Heatmap (Senior High)</h6>
                          </div>
                          <div class="card-body">
                              <table class="table table-bordered text-center" id="enrollmentHeatmap">
                                  <thead class="thead-dark">
                                      <tr>
                                          <th>Strand</th>
                                          <th>Grade 11</th>
                                          <th>Grade 12</th>
                                          <th>Total</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @php
                                      $grandTotal = 0;
                                      $totalG11 = 0;
                                      $totalG12 = 0;
                                      @endphp

                                      @foreach ($shsData as $shs)
                                      @php
                                      $g11 = $shs['g11'];
                                      $g12 = $shs['g12'];
                                      $total = $g11 + $g12;

                                      $totalG11 += $g11;
                                      $totalG12 += $g12;
                                      $grandTotal += $total;

                                      $g11Class = $g11 >= 100 ? 'bg-success text-white' : ($g11 >= 60 ? 'bg-warning text-dark' : 'bg-danger text-white');
                                      $g12Class = $g12 >= 100 ? 'bg-success text-white' : ($g12 >= 60 ? 'bg-warning text-dark' : 'bg-danger text-white');
                                      $totalClass = $total >= 100 ? 'bg-success text-white' : ($total >= 60 ? 'bg-warning text-dark' : 'bg-danger text-white');
                                      @endphp
                                      <tr>
                                          <td>{{ $shs['name'] }}</td>
                                          <td class="{{ $g11Class }}">{{ $g11 }}</td>
                                          <td class="{{ $g12Class }}">{{ $g12 }}</td>
                                          <td class="{{ $totalClass }}"><strong>{{ $total }}</strong></td>
                                      </tr>
                                      @endforeach

                                      <tr class="table-secondary font-weight-bold">
                                          <td><strong>Total</strong></td>
                                          @php
                                          $g11Class = $totalG11 >= 100 ? 'bg-success text-white' : ($totalG11 >= 60 ? 'bg-warning text-dark' : 'bg-danger text-white');
                                          $g12Class = $totalG12 >= 100 ? 'bg-success text-white' : ($totalG12 >= 60 ? 'bg-warning text-dark' : 'bg-danger text-white');
                                          $totalClass = $grandTotal >= 100 ? 'bg-success text-white' : ($grandTotal >= 60 ? 'bg-warning text-dark' : 'bg-danger text-white');
                                          @endphp
                                          <td class="{{ $g11Class }}"><strong>{{ $totalG11 }}</strong></td>
                                          <td class="{{ $g12Class }}"><strong>{{ $totalG12 }}</strong></td>
                                          <td class="{{ $totalClass }}"><strong>{{ $grandTotal }}</strong></td>
                                      </tr>
                                  </tbody>
                              </table>


                          </div>
                      </div>
                  </div>
              </div>
              <!-- Row: Financial Alerts -->
              <div class="row mt-4">
                  <div class="col-xl-12">
                      <div class="card shadow mb-4">
                          <div class="card-header py-3 bg-danger">
                              <h6 class="m-0 font-weight-bold text-white">
                                  Top 10 Unpaid Balances (₱10,000+) — {{ $activeSY->name ?? 'No Active School Year' }}
                              </h6>
                          </div>
                          <div class="card-body">
                              @if ($topUnpaid->isEmpty())
                              <p class="text-muted">No unpaid balances over ₱10,000 found.</p>
                              @else
                              <ul class="list-group" id="unpaidList">

                                  @foreach ($topUnpaid as $billing)
                                  <li class="list-group-item d-flex justify-content-between align-items-center">
                                      {{ $billing->student->full_name ?? $billing->student_id }}
                                      <span
                                          class="badge badge-danger badge-pill">₱{{ number_format($billing->balance_due, 2) }}</span>
                                  </li>
                                  @endforeach
                              </ul>
                              @endif
                          </div>
                      </div>
                  </div>
              </div>


          </div>
          <!-- /.container-fluid -->

          <!-- End of Main Content -->

          @include('layouts.footer')

      </div>
      <!-- End of Content Wrapper -->
  </div>
  @endsection



  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('scholarshipPieChart').getContext('2d');

          // Fetch balance due data from the backend
          fetch('/api/balance-due') // Update with the correct endpoint
              .then(response => response.json())
              .then(data => {
                  const labels = data.map(item => item.semester); // X-axis labels (semesters)
                  const balanceDue = data.map(item => item.total_balance_due); // Values (total balance due)

                  // Render the pie chart
                  new Chart(ctx, {
                      type: 'pie',
                      data: {
                          labels: labels,
                          datasets: [{
                              label: 'Balance Due',
                              data: balanceDue,
                              backgroundColor: [
                                  'rgba(255, 99, 132, 0.2)',
                                  'rgba(54, 162, 235, 0.2)',
                                  'rgba(255, 206, 86, 0.2)',
                                  'rgba(75, 192, 192, 0.2)',
                                  'rgba(153, 102, 255, 0.2)'
                              ],
                              borderColor: [
                                  'rgba(255, 99, 132, 1)',
                                  'rgba(54, 162, 235, 1)',
                                  'rgba(255, 206, 86, 1)',
                                  'rgba(75, 192, 192, 1)',
                                  'rgba(153, 102, 255, 1)'
                              ],
                              borderWidth: 1
                          }]
                      },
                      options: {
                          responsive: true,
                          plugins: {
                              legend: {
                                  position: 'top',
                              }
                          }
                      }
                  });
              })
              .catch(error => console.error('Error fetching balance due data:', error));
      });
  </script>
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('scholarshipPieChartShs').getContext('2d');

          // Fetch balance due data from the backend
          fetch('/api/balance-due/shs') // Update with the correct endpoint
              .then(response => response.json())
              .then(data => {
                  const labels = data.map(item => item.semester); // X-axis labels (semesters)
                  const balanceDue = data.map(item => item.total_balance_due); // Values (total balance due)

                  // Render the pie chart
                  new Chart(ctx, {
                      type: 'pie',
                      data: {
                          labels: labels,
                          datasets: [{
                              label: 'Balance Due',
                              data: balanceDue,
                              backgroundColor: [
                                  'rgba(255, 99, 132, 0.2)',
                                  'rgba(54, 162, 235, 0.2)',
                                  'rgba(255, 206, 86, 0.2)',
                                  'rgba(75, 192, 192, 0.2)',
                                  'rgba(153, 102, 255, 0.2)'
                              ],
                              borderColor: [
                                  'rgba(255, 99, 132, 1)',
                                  'rgba(54, 162, 235, 1)',
                                  'rgba(255, 206, 86, 1)',
                                  'rgba(75, 192, 192, 1)',
                                  'rgba(153, 102, 255, 1)'
                              ],
                              borderWidth: 1
                          }]
                      },
                      options: {
                          responsive: true,
                          plugins: {
                              legend: {
                                  position: 'top',
                              }
                          }
                      }
                  });
              })
              .catch(error => console.error('Error fetching balance due data:', error));
      });
  </script>

  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('revenueChart').getContext('2d');

          // Fetch revenue data from the backend
          fetch('/api/revenue-trends') // Update with the correct endpoint
              .then(response => response.json())
              .then(data => {
                  // Combine school_year and semester for labels
                  const labels = data.map(item => `${item.school_year} - ${item.semester}`);
                  const revenue = data.map(item => item.total_revenue);

                  // Render the chart
                  new Chart(ctx, {
                      type: 'line',
                      data: {
                          labels: labels,
                          datasets: [{
                              label: 'Total Revenue',
                              data: revenue,
                              backgroundColor: 'rgba(54, 162, 235, 0.2)',
                              borderColor: 'rgba(54, 162, 235, 1)',
                              borderWidth: 1,
                              tension: 0.1 // Makes the line slightly curved
                          }]
                      },
                      options: {
                          responsive: true,
                          plugins: {
                              legend: {
                                  position: 'top',
                              },
                              tooltip: {
                                  callbacks: {
                                      label: function(context) {
                                          return `Revenue: ₱${context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                      }
                                  }
                              }
                          },
                          scales: {
                              y: {
                                  beginAtZero: true,
                                  ticks: {
                                      callback: function(value) {
                                          return '₱' + value.toLocaleString('en-PH');
                                      }
                                  }
                              }
                          }
                      }
                  });
              })
              .catch(error => console.error('Error fetching revenue trends:', error));
      });
  </script>
  <script>
      document.addEventListener('DOMContentLoaded', function() {
          const ctx = document.getElementById('revenueChartShs').getContext('2d');

          // Fetch revenue data from the backend
          fetch('/api/revenue-trends/shs') // Update with the correct endpoint
              .then(response => response.json())
              .then(data => {
                  // Combine school_year and semester for labels
                  const labels = data.map(item => `${item.school_year} - ${item.semester}`);
                  const revenue = data.map(item => item.total_revenue);

                  // Render the chart
                  new Chart(ctx, {
                      type: 'line',
                      data: {
                          labels: labels,
                          datasets: [{
                              label: 'Total Revenue',
                              data: revenue,
                              backgroundColor: 'rgba(54, 162, 235, 0.2)',
                              borderColor: 'rgba(54, 162, 235, 1)',
                              borderWidth: 1,
                              tension: 0.1 // Makes the line slightly curved
                          }]
                      },
                      options: {
                          responsive: true,
                          plugins: {
                              legend: {
                                  position: 'top',
                              },
                              tooltip: {
                                  callbacks: {
                                      label: function(context) {
                                          return `Revenue: ₱${context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                      }
                                  }
                              }
                          },
                          scales: {
                              y: {
                                  beginAtZero: true,
                                  ticks: {
                                      callback: function(value) {
                                          return '₱' + value.toLocaleString('en-PH');
                                      }
                                  }
                              }
                          }
                      }
                  });
              })
              .catch(error => console.error('Error fetching revenue trends:', error));
      });
  </script>
  <script>
      function exportTableToExcel(tableId, filename = '') {
          let table = document.getElementById(tableId);
          let wb = XLSX.utils.table_to_book(table, {
              sheet: "Sheet1"
          });
          XLSX.writeFile(wb, filename + ".xlsx");
      }

      function exportListToExcel(listId, filename = '') {
          const list = document.getElementById(listId);
          const rows = [
              ['Name', 'Balance']
          ];

          list.querySelectorAll('li').forEach(item => {
              const name = item.childNodes[0].textContent.trim();
              const balance = item.querySelector('span').textContent.trim();
              rows.push([name, balance]);
          });

          const ws = XLSX.utils.aoa_to_sheet(rows);
          const wb = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
          XLSX.writeFile(wb, filename + ".xlsx");
      }
  </script>
  <script>
      const programData = @json($programFinancials);
  </script>

  <script>
      document.addEventListener("DOMContentLoaded", function() {
          const labels = programData.map(item => item.program_name);
          const initialPayments = programData.map(item => parseFloat(item.total_initial_payment));
          const balances = programData.map(item => parseFloat(item.total_balance_due));

          // 📊 Balance Due Bar Chart (8 cols)
          const ctxBalance = document.getElementById('balanceDistributionChart').getContext('2d');
          new Chart(ctxBalance, {
              type: 'bar',
              data: {
                  labels: labels,
                  datasets: [{
                      label: 'Balance Due',
                      data: balances,
                      backgroundColor: 'rgba(255, 99, 132, 0.7)',
                      borderColor: 'rgba(255, 99, 132, 1)',
                      borderWidth: 1,
                      barPercentage: 0.6,
                      maxBarThickness: 100
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      title: {
                          display: true,
                          text: 'Balance Due per Program',
                          font: {
                              size: 18
                          }
                      },
                      legend: {
                          display: false
                      }
                  },
                  scales: {
                      y: {
                          beginAtZero: true,
                          ticks: {
                              stepSize: 1000
                          }
                      }
                  }
              }
          });

          // 🥧 Initial Payments Pie Chart (4 cols)
          const ctxInitial = document.getElementById('paymentSourcesChart').getContext('2d');
          new Chart(ctxInitial, {
              type: 'pie',
              data: {
                  labels: labels,
                  datasets: [{
                      label: 'Initial Payment',
                      data: initialPayments,
                      backgroundColor: [
                          'rgba(54, 162, 235, 0.7)',
                          'rgba(255, 206, 86, 0.7)',
                          'rgba(75, 192, 192, 0.7)',
                          'rgba(153, 102, 255, 0.7)',
                          'rgba(255, 159, 64, 0.7)',
                          'rgba(255, 99, 132, 0.7)',
                          'rgba(201, 203, 207, 0.7)'
                      ],
                      borderColor: 'rgba(255, 255, 255, 1)',
                      borderWidth: 1
                  }]
              },
              options: {
                  responsive: true,
                  maintainAspectRatio: false,
                  plugins: {
                      title: {
                          display: true,
                          text: 'Initial Payments per Program',
                          font: {
                              size: 18
                          }
                      },
                      legend: {
                          position: 'right'
                      }
                  }
              }
          });
      });
  </script>
   <div class="card shadow mb-4" >
       <div class="card-header py-3">
           <div class="mb-3">
               <label for="dailyDate" class="form-label">Select Date</label>
               <input type="date" class="form-control w-auto d-inline-block" id="dailyDate">
           </div>
           <h6 class="m-0 font-weight-bold text-primary">💵 Daily Collections (Current Semester)</h6>
       </div>
       <div class="card-body">
           <div class="table-responsive">
               <table class="table table-bordered" id="dailySalesTable">
                   @php
                   $activeSemester = \App\Models\SchoolYear::where('is_active', 1)->first();
                   @endphp
                   <thead>
                       <tr>
                           <th>CATEGORY</th>
                           <th>TRANSACTION COUNT</th>
                           <th>TOTAL AMOUNT</th>
                       </tr>
                   </thead>
                   <tbody>
                       <tr>
                           <td>College</td>
                           <td>0</td>
                           <td>0.00</td>
                       </tr>
                       <tr>
                           <td>Senior High</td>
                           <td>0</td>
                           <td>0.00</td>
                       </tr>
                       <tr>
                           <td>Other Fees</td>
                           <td>0</td>
                           <td>0.00</td>
                       </tr>
                       <tr>
                           <td>Uniform</td>
                           <td>0</td>
                           <td>0.00</td>
                       </tr>
                       <tr>
                           <td>Old Account</td>
                           <td>0</td>
                           <td>0.00</td>
                       </tr>
                       <tr>
                           <td>Total</td>
                           <td>0</td>
                           <td>0.00</td>
                       </tr>
                   </tbody>
               </table>
           </div>
           <div class="mt-3 text-muted">
               <small>Current Semester: {{ $activeSemester->name ?? '' }} -
                   {{ $activeSemester->semester ?? '' }}</small>
           </div>
       </div>
   </div>

   <script>
       document.addEventListener('DOMContentLoaded', function() {
           const dateInput = document.getElementById('dailyDate');
           const tableBody = document.querySelector('#dailySalesTable tbody');

           const rowsMap = {
               college: 'collegeCollection',
               shs: 'shsCollection',
               other: 'otherFeeCollection',
               uniform: 'uniformCollection',
               old: 'oldAccountCollection'
           };

           function fetchData(date) {
               fetch(`/api/daily-collections?date=${date}`)
                   .then(res => res.json())
                   .then(data => {
                       const rows = [{
                               label: 'College',
                               key: rowsMap.college
                           },
                           {
                               label: 'Senior High',
                               key: rowsMap.shs
                           },
                           {
                               label: 'Other Fees',
                               key: rowsMap.other
                           },
                           {
                               label: 'Uniform',
                               key: rowsMap.uniform
                           },
                           {
                               label: 'Old Account',
                               key: rowsMap.old
                           },
                       ];

                       let totalAmount = 0;
                       let totalCount = 0;

                       const trElements = tableBody.querySelectorAll('tr');
                       rows.forEach((row, i) => {
                           const rowData = data[row.key] || {
                               amount: 0,
                               count: 0
                           };
                           trElements[i].children[1].textContent = rowData.count;
                           trElements[i].children[2].textContent = `₱${parseFloat(rowData.amount).toFixed(2)}`;
                           totalAmount += parseFloat(rowData.amount);
                           totalCount += parseInt(rowData.count);
                       });

                       const totalRow = trElements[5];
                       totalRow.children[1].textContent = totalCount;
                       totalRow.children[2].textContent = `₱${totalAmount.toFixed(2)}`;
                   })
                   .catch(err => {
                       console.error('Fetch error:', err);
                   });
           }


           const today = new Date().toISOString().split('T')[0];
           dateInput.value = today;


           fetchData(today);


           dateInput.addEventListener('change', () => {
               fetchData(dateInput.value);
           });


           setInterval(() => {
               fetchData(dateInput.value);
           }, 30000);
       });
   </script>
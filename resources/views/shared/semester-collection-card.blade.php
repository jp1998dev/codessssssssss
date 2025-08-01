   <div class="card shadow mb-4" > 
       <div class="card-header py-3">
           @php
           $activeSemester = \App\Models\SchoolYear::where('is_active', 1)->first();
           $allSemester = \App\Models\SchoolYear::all();
           @endphp
           <div class="mb-3">
               <label for="semester" class="form-label">Select Semester</label>
               <select name="" id="semester" class="form-select w-auto d-inline-block">
                   @foreach( $allSemester as $sem )
                   <option value="{{ $sem->id }}">{{$sem->semester}} SY {{$sem->name}}</option>
                   @endforeach
               </select>
           </div>
           <h6 class="m-0 font-weight-bold text-primary">💵 Semester Collections</h6>
       </div>
       <div class="card-body">
           <div class="table-responsive">
               <table class="table table-bordered" id="semesteSalesTable">

                   <thead>
                       <tr>
                           <th>CATEGORY</th>
                           <th>TOTAL TRANSACTIONS</th>
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
           const sameInput = document.getElementById('semester');
           const tableBody = document.querySelector('#semesteSalesTable tbody');

           const rowsMap = {
               college: 'collegeCollection',
               shs: 'shsCollection',
               other: 'otherFeeCollection',
               uniform: 'uniformCollection',
               old: 'oldAccountCollection'
           };

           function fetchData(semiD) {
               fetch(`/api/semester-collections?semester_id=${semiD}`)
                   .then(res => res.json())
                   .then(data => {
                       console.log(data);
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
           fetchData(sameInput.value);


           sameInput.addEventListener('change', () => {
               fetchData(sameInput.value);
           });


           setInterval(() => {
               fetchData(sameInput.value);
           }, 30000);
       });
   </script>
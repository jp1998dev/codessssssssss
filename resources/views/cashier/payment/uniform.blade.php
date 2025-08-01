@extends('layouts.main')

@section('tab_title', 'Payments')
@section('vpacademic_sidebar')
@include('vp_academic.vpacademic_sidebar')
@endsection

@section('content')

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        @include('layouts.topbar')

        <div class="container-fluid">
            @include('layouts.success-message')


            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Uniform Payments</h1>


                <!-- New Payment Button -->
                <button class="btn btn-primary" data-toggle="modal" data-target="#newPaymentModal">
                    New Payment
                </button>
            </div>

            @php
            use Illuminate\Support\Str;
            @endphp <!-- Display Billings in Table -->


            <div class="row justify-content-center mt-4">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs" id="paymentTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="college-tab" data-bs-toggle="tab" href="#college" role="tab">College</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="shs-tab" data-bs-toggle="tab" href="#shs" role="tab">SHS</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body tab-content" id="paymentTabsContent">
                            <!-- College Tab -->
                            <div class="tab-pane fade show active" id="college" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="collegePaymentsTable">
                                        <thead>
                                            <tr>
                                                <th>Student ID</th>
                                                <th>Student Name</th>
                                                <th>Amount</th>
                                                <th>Payment Date</th>
                                                <th>Remarks</th>
                                                <th>Trans No</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payments->where('student_id', '!=', null) as $payment)
                                            <tr>
                                                <td>{{ $payment->student->student_id ?? 'N/A' }}</td>
                                                <td>{{ $payment->student->full_name ?? 'N/A' }}</td>
                                                <td>{{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                                <td>{{ $payment->remarks }}</td>
                                                <td>{{ $payment->trans_no }}</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm"
                                                        onclick="reprintReceipt(
                                                        '{{ $payment->student->full_name ?? 'N/A' }}',
                                                        '{{ number_format($payment->amount, 2) }}',
                                                        '{{ $payment->remarks }}',
                                                        '{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}',
                                                        '{{ $payment->trans_no }}',
                                                        '{{ $activeSchoolYear->name ?? 'N/A' }}',
                                                        '{{ $activeSchoolYear->semester ?? 'N/A' }}'
                                                    )">Reprint</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- SHS Tab -->
                            <div class="tab-pane fade" id="shs" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="shsPaymentsTable">
                                        <thead>
                                            <tr>
                                                <th>LRN</th>
                                                <th>Student Name</th>
                                                <th>Amount</th>
                                                <th>Payment Date</th>
                                                <th>Remarks</th>
                                                <th>Trans No</th>
                                                <th>Action</th>
                                                <!-- <th>Action</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payments->where('lrn_number', '!=', null) as $payment)
                                            <tr>
                                                <td>{{ $payment->student->lrn_number ?? 'N/A' }}</td>
                                                <td>{{ $payment->student->full_name ?? 'N/A' }}</td>
                                                <td>{{ number_format($payment->amount, 2) }}</td>
                                                <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                                                <td>{{ $payment->remarks }}</td>
                                                <td>{{ $payment->trans_no }}</td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm"
                                                        onclick="reprintReceipt(
                                                                `{{ $payment->student->full_name ?? 'N/A' }}`,
                                                                `{{ number_format($payment->amount, 2) }}`,
                                                                `{{ $payment->remarks }}`,
                                                                `{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}`,
                                                                `{{ $payment->trans_no }}`,
                                                                `{{ $activeSchoolYear->name ?? 'N/A' }}`,
                                                                `{{ $activeSchoolYear->semester ?? 'N/A' }}`
                                                    )">Reprint</button>
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




            <!-- New Payment Modal -->


            <!-- PAYMENT MODAL -->
            <div class="modal fade" id="newPaymentModal" tabindex="-1" aria-labelledby="newPaymentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route( 'uniformpayment.input') }}" id="paymentForm">
                        @csrf
                        <div class="modal-content shadow-lg rounded-3">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title fw-bold" id="newPaymentModalLabel">New Payment Entry</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body px-4 py-3">
                                <!-- Hidden inputs -->
                                <input type="text" name="student_type" id="student_type" value="1" hidden>
                                <input type="hidden" id="schoolYearInput" name="school_year" value="{{ $activeSchoolYear->name ?? '' }}">
                                <input type="hidden" id="semesterInput" name="semester" value="{{ $activeSchoolYear->semester ?? '' }}">
                                <!-- Student Info Section -->
                                <div class="border rounded p-3 mb-4">
                                    <h6 class="text-primary fw-semibold mb-3">Student Information</h6>
                                    <div class="mb-3 position-relative">
                                        <label for="searchStudent" class="form-label">Search Student</label>
                                        <input type="text" class="form-control" id="searchStudent" name="student_id" placeholder="Search by Student ID">
                                        <input type="text" class="form-control d-none" id="student_lrn" name="student_lrn" placeholder="Search by Student Lrn">
                                        <ul id="searchSuggestions" class="list-group position-absolute w-100 mt-1" style="z-index: 1050;"></ul>
                                    </div>
                                    <div>
                                        <label for="studentName" class="form-label">Student Name</label>
                                        <input type="text" class="form-control" id="studentName" readonly required>
                                        <input type="text" id="course" hidden>
                                    </div>
                                </div>


                                <!-- POS Fee Table (SELECT FEES) -->
                                <div class="border rounded p-3 mb-4">
                                    <h6 class="text-info fw-semibold mb-3">Select Other Fees</h6>
                                    <div class="table-responsive">
                                        <div class="flex justify-content-between mb-3">
                                            <select name="" id="selectedUniform" class="form-select mb-3">
                                                @foreach ($uniformFees as $fee)
                                                <option value="{{ $fee->id }}">{{ $fee->name }} - ₱{{ number_format($fee->amount, 2) }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-success mb-3" type="button" id="addFeeButton">
                                                Add
                                            </button>
                                        </div>
                                        <table class="table table-bordered align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Fee Name</th>
                                                    <th>Amount (₱)</th>
                                                    <th>Quantity</th>
                                                    <th>Total (₱)</th>
                                                </tr>
                                            </thead>
                                            <tbody id="feesTableBody"></tbody>
                                        </table>
                                    </div>

                                    <div class="text-end mt-3">
                                        <h5 class="fw-bold">Grand Total: <span id="grand-total">₱0.00</span></h5>
                                    </div>
                                </div>
                                <input type="hidden" class="form-control" id="amount" name="payment_amount" value="23423ffsf" required>
                                <input type="text" name="trans_no" id="trans_no" hidden required>
                            </div>

                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button class="btn btn-primary fw-bold" id="submitBtn">Submit & Print</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- @push('scripts') -->
            <script src="https://demo.qz.io/js/qz-tray.js"></script>
            <!-- <script>
                    window.onload = function () {
                        if (typeof qz === 'undefined') {
                            alert("QZ Tray is not loaded.");
                            return;
                        }

                        qz.websocket.connect().then(() => {
                            alert("QZ Tray connected successfully!");
                            return qz.printers.find();
                        }).then(printers => {
                            console.log("Available printers:", printers);
                        }).catch(err => {
                            alert("QZ Tray connection failed: " + err);
                        });
                    };
                </script> -->


            <!-- @endpush -->
            <script>
                const uniformsCount = @json($payments->count());
            </script>
            <script>
                function generateTransNo() {
                    const count = uniformsCount + 1;
                    const countStr = String(count).padStart(3, '0');

                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0');
                    const day = String(today.getDate()).padStart(2, '0');

                    const dateStr = `${year}${month}${day}`;
                    const transactionCode = `TRX-${dateStr}-${countStr}`;
                    console.log(transactionCode);
                    return transactionCode;
                }
            </script>
            <script>
                function updateTotals() {
                    let grandTotal = 0;

                    document.querySelectorAll('.quantity-input').forEach(input => {
                        const feeId = input.dataset.feeId;
                        const quantity = parseFloat(input.value) || 0;
                        const amount = parseFloat(document.querySelector(`.fee-amount[data-fee-id="${feeId}"]`).textContent.replace(/₱|,/g, '')) || 0;
                        const total = quantity * amount;
                        grandTotal += total;

                        document.getElementById(`fee-total-${feeId}`).textContent = `₱${total.toFixed(2)}`;
                    });

                    document.getElementById('grand-total').textContent = `₱${grandTotal.toFixed(2)}`;
                    document.getElementById('amount').value = grandTotal.toFixed(2); // update hidden input
                }

                // Listen for input changes
                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('quantity-input')) {
                        updateTotals();
                    }
                });
            </script>

            <script>
                document.getElementById('submitBtn').addEventListener('click', submitAndPrint);
                // Set school year and semester when modal opens
                $('#newPaymentModal').on('show.bs.modal', function() {
                    @if(isset($activeSchoolYear))
                    document.getElementById('schoolYearInput').value = '{{ $activeSchoolYear->name }}';
                    document.getElementById('semesterInput').value = '{{ $activeSchoolYear->semester }}';
                    @endif
                });

                function submitAndPrint(event) {
                    event.preventDefault();
                    const printNo = document.getElementById('trans_no');
                    printNo.value = generateTransNo();
                    const schoolYear = document.getElementById('schoolYearInput').value;
                    const semester = document.getElementById('semesterInput').value;

                    if (!schoolYear || !semester) {
                        alert('Please ensure school year and semester are set before proceeding.');
                        return;
                    }
                    
                    printReceipt().then(() => {
                        document.getElementById('paymentForm').submit();
                    }).catch((err) => {
                        alert("Printing failed: " + err);
                    });
                }

                function connectToQZ() {
                    if (typeof qz === 'undefined') {
                        alert("QZ Tray is not loaded.");
                        return;
                    }

                    qz.websocket.connect().then(() => {
                        qz.printers.find().then(printers => console.log(printers));
                        alert("QZ Tray connected successfully!");
                        return qz.printers.find();
                    }).then(printers => {
                        console.log("Available printers:", printers);
                    }).catch(err => {
                        alert("QZ Tray connection failed: " + err);
                    });
                }

                function printReceipt() {
                    return new Promise((resolve, reject) => {
                        if (typeof qz === 'undefined') {
                            alert("QZ Tray is not loaded.");
                            reject("QZ not loaded");
                            return;
                        }

                        qz.websocket.connect().then(() => {
                            qz.printers.find().then(printers => console.log(printers));
                            const config = qz.configs.create("Microsoft Print to PDF");

                            const singleReceipt = printRep();
                            const data = [
                                ...singleReceipt,
                                ...singleReceipt,
                                ...singleReceipt
                            ];

                            return qz.print(config, data);
                        }).then(() => {
                            resolve();
                        }).catch(err => {
                            reject(err);
                        });
                    });
                }
                // function printReceipt() {
                //     return new Promise((resolve, reject) => {
                //         try {
                //             const receipt = printRep();  


                //             const blob = new Blob([receipt], { type: "text/plain;charset=utf-8" });
                //             const link = document.createElement("a");
                //             link.href = URL.createObjectURL(blob);
                //             link.download = "receipt_sample.txt";
                //             document.body.appendChild(link);
                //             link.click();
                //             document.body.removeChild(link);

                //             resolve("TXT file generated");
                //         } catch (err) {
                //             reject(err);
                //         }
                //     });
                // }


                function printRep() {
                    const studentName = document.getElementById('studentName').value.trim();
                    const studentNo = document.getElementById('searchStudent').value;
                    const transNo = document.getElementById('trans_no').value;
                    const amount = parseFloat(document.getElementById('amount').value);
                    const course = document.getElementById('course').value;
                    const date = new Date();
                    const time = date.toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    const formattedDate = date.toLocaleDateString();
                    const cashier = @json(auth()->user()->name);


                    const rows = document.querySelectorAll('#feesTableBody tr');
                    let items = '';
                    rows.forEach(row => {
                        const name = row.querySelector('.fee-name')?.textContent.trim() || '';
                        const amountText = row.querySelector('.fee-amount')?.textContent.replace('₱', '').trim() || '0';
                        const quantity = row.querySelector('.quantity-input')?.value || '0';
                        const totalText = row.querySelector('.fee-total')?.textContent.replace('₱', '').trim() || '0';

                        if (parseInt(quantity) > 0) {
                            items += `${name.slice(0,12).padEnd(12)} ${quantity.toString().padStart(2)} ${parseFloat(amountText).toFixed(0).padStart(6)} ${parseFloat(totalText).toFixed(0).padStart(7)}ª\x0A`;
                        }
                    });

                    const data = [
                        '\x1B\x40',
                        '\x1B\x61\x31',
                        'Infotech Dev\'t Systems Coll.\x0A',
                        'Natera-P. Timog, Ligao City\x0A\x0A',

                        `Trans#: ${transNo}\x0A`,
                        `Name  : ${studentName}\x0A`,
                        `No.   : ${studentNo}\x0A`,
                        `Course: ${course}\x0A`,

                        '--------------------------------\x0A',
                        'Description  QTY Price   Amount\x0A',
                        '--------------------------------\x0A',
                        items,
                        '--------------------------------\x0A',
                        `TOTAL:        ₱${amount.toFixed(2)}ª\x0A`,
                        '--------------------------------\x0A',
                        `Date: ${formattedDate}\x0A`,
                        `Time: ${time}\x0A`,
                        `Cashier: ${cashier}\x0A\x0A`,

                        '\x1B\x45\x0D',
                        '** THANK YOU FOR YOUR PAYMENT **\x0A',
                        '\x1B\x45\x0A',
                        '  Please keep this receipt for\x0A',
                        '        future reference.\x0A',

                        '\x0A\x0A\x0A\x0A\x0A\x0A\x0A',
                        '\x1B\x69',
                        '\x10\x14\x01\x00\x05'
                    ];

                    return data;
                    // const receipt = 
                    //         `  Infotech Dev't Systems Coll.
                    //            Natera-P. Timog, Ligao City

                    //         Trans#: ${transNo}
                    //         Name  : ${studentName}
                    //         No.   : ${studentNo}
                    //         Course: ${course}
                    //         --------------------------------
                    //         Description  QTY Price   Amount
                    //         --------------------------------
                    //         ${items}
                    //         --------------------------------
                    //         TOTAL:      ₱${amount.toFixed(2)}
                    //         --------------------------------
                    //         Date: ${formattedDate}
                    //         Time: ${time}
                    //         Cashier: ${cashier}

                    //         ** THANK YOU FOR YOUR PAYMENT **
                    //           Please keep this receipt for
                    //                 future reference.
                    //         `;

                    // return receipt;
                }





                function convertAmountToWords(amount) {
                    const ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
                    const teens = ["Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen",
                        "Nineteen"
                    ];
                    const tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

                    function numberToWords(num) {
                        let word = "";

                        if (num >= 100000) {
                            word += ones[Math.floor(num / 100000)] + " Hundred ";
                            num %= 100000;
                        }

                        if (num >= 20000) {
                            word += tens[Math.floor(num / 10000)] + " ";
                            num %= 10000;
                        } else if (num >= 10000) {
                            word += teens[Math.floor((num % 10000) / 1000)] + " Thousand ";
                            num %= 1000;
                        }

                        if (num >= 1000) {
                            word += ones[Math.floor(num / 1000)] + " Thousand ";
                            num %= 1000;
                        }

                        if (num >= 100) {
                            word += ones[Math.floor(num / 100)] + " Hundred ";
                            num %= 100;
                        }

                        if (num >= 20) {
                            word += tens[Math.floor(num / 10)] + " ";
                            num %= 10;
                        } else if (num >= 10) {
                            word += teens[num - 10] + " ";
                            num = 0;
                        }

                        if (num > 0) {
                            word += ones[num] + " ";
                        }

                        return word.trim();
                    }

                    let num = Math.floor(amount);
                    let cents = Math.round((amount - num) * 100);

                    let words = numberToWords(num);
                    if (cents > 0) {
                        words += ` and ${cents} Centavos`;
                    }

                    return words || "Zero";
                }

                // Function to set the hidden school year and semester values (you'll call this when setting up the modal)
                function setSchoolYearAndSemester(schoolYear, semester) {
                    document.getElementById('schoolYearInput').value = schoolYear;
                    document.getElementById('semesterInput').value = semester;
                }
            </script>


            <!-- Optional CSS -->
            <style>
                #printableReceipt {
                    font-size: 24px !important;
                    /* Base font size much larger */
                }

                #printableReceipt h3 {
                    text-align: center;
                    margin-bottom: 30px;
                    font-size: 28px !important;
                    font-weight: bold;
                }

                #printableReceipt p {
                    margin: 10px 0;
                    font-size: 24px !important;
                    line-height: 1.5;
                }

                #printableReceipt div {
                    font-size: 24px !important;
                    margin-bottom: 25px !important;
                }

                #receiptDate,
                #receiptStudentName,
                #receiptSchoolYear,
                #receiptRemarks,
                #receiptAmountWords,
                #receiptAmount {
                    font-size: 24px !important;
                }

                #receiptStudentName {
                    font-size: 26px !important;
                    font-weight: bold;
                }
            </style>

            <script>
                function inputChangeHandler() {
                    const searchInput = document.getElementById('searchStudent');
                    const suggestionsList = document.getElementById('searchSuggestions');
                    const studentNameInput = document.getElementById('studentName');
                    const student_lrn = document.getElementById('student_lrn');
                    const student_type = document.getElementById('student_type');
                    const course = document.getElementById('course');

                    const query = this.value;
                    console.log(query);
                    if (query.length >= 2) {
                        fetch(`/api/search?query=${query}`)
                            .then(response => response.json())
                            .then(data => {
                                suggestionsList.innerHTML = '';
                                data.forEach(student => {
                                    const suggestion = document.createElement('li');
                                    suggestion.className = 'list-group-item list-group-item-action';
                                    suggestion.textContent =
                                        `${student?.student_lrn ?? student?.student_id} - ${student.full_name}`;
                                    suggestion.addEventListener('click', function() {
                                        studentNameInput.value = student.full_name;
                                        course.value = student.course;
                                        if (student?.student_lrn) {
                                            student_type.value = '2';
                                            student_lrn.value = student?.student_lrn
                                            student_lrn.classList.remove('d-none');
                                            searchInput.classList.add('d-none');
                                            searchInput.value = null;
                                        } else {
                                            student_type.value = '1';
                                            searchInput.value = student?.student_id;
                                            student_lrn.value = null;
                                            searchInput.classList.remove('d-none');
                                            student_lrn.classList.add('d-none');
                                        }
                                        suggestionsList.innerHTML = '';
                                    });
                                    suggestionsList.appendChild(suggestion);
                                });
                            });
                    } else {
                        suggestionsList.innerHTML = '';
                    }
                }
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchStudent');
                    const suggestionsList = document.getElementById('searchSuggestions');
                    const studentNameInput = document.getElementById('studentName');
                    const student_lrn = document.getElementById('student_lrn');
                    const student_type = document.getElementById('student_type');
                    searchInput.addEventListener('input', inputChangeHandler);
                    student_lrn.addEventListener('input', inputChangeHandler);

                    // Hide suggestions when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!suggestionsList.contains(event.target) && event.target !== searchInput) {
                            suggestionsList.innerHTML = '';
                        }
                    });
                });
            </script>
            <script>
                function reprintReceipt(studentName, amount, remarks, paymentDate, orNumber, schoolYear, semester) {
                    const nameParts = studentName.trim().split(' ');
                    let formattedName = studentName;
                    if (nameParts.length >= 3) {
                        const lastName = nameParts.pop();
                        const firstName = nameParts.shift();
                        const middleInitial = nameParts.length > 0 ? nameParts[0].charAt(0).toUpperCase() + '.' : '';
                        formattedName = `${lastName.toUpperCase()}, ${firstName.toUpperCase()} ${middleInitial}`;
                    }

                    const amountValue = parseFloat(amount.replace(/,/g, ''));
                    const amountWords = convertAmountToWords(amountValue).toUpperCase() + ' PESOS ONLY';

                    function formatSemester(sem) {
                        sem = sem.toString().toUpperCase();
                        if (sem === '1' || sem === 'FIRST' || sem === '1ST') return '1ST';
                        if (sem === '2' || sem === 'SECOND' || sem === '2ND') return '2ND';
                        if (sem === '3' || sem === 'THIRD' || sem === '3RD') return '3RD';
                        return sem;
                    }

                    const formattedSemester = formatSemester(semester);
                    const formattedSemesterAndSY = `${formattedSemester} SY ${schoolYear}`;
                    const cashier = @json(auth()->user()->name);

                    const data = [
                        '\x1B\x40',
                        '\x1B\x61\x31',
                        'Infotech Dev\'t Systems Coll.\x0A',
                        'Natera-P. Timog, Ligao City\x0A\x0A',

                        `Trans#: ${orNumber}\x0A`,
                        `Name  : ${formattedName}\x0A`,

                        '--------------------------------\x0A',
                        'Description  QTY Price   Amount\x0A',
                        '--------------------------------\x0A',
                        remarks,
                        '--------------------------------\x0A',
                        `TOTAL:        ₱${amountValue.toFixed(2)}ª\x0A`,
                        '--------------------------------\x0A',
                        `Date: ${paymentDate}\x0A`,
                        `Cashier: ${cashier}\x0A\x0A`,

                        '\x1B\x45\x0D',
                        '** THANK YOU FOR YOUR PAYMENT **\x0A',
                        '\x1B\x45\x0A',
                        '  Please keep this receipt for\x0A',
                        '        future reference.\x0A',

                        '\x0A\x0A\x0A\x0A\x0A\x0A\x0A',
                        '\x1B\x69',
                        '\x10\x14\x01\x00\x05'
                    ];
                   reprintReceiptThermo(data);
                    
                }
                function reprintReceiptThermo(singleReceipt) {
                    return new Promise((resolve, reject) => {
                        if (typeof qz === 'undefined') {
                            alert("QZ Tray is not loaded.");
                            reject("QZ not loaded");
                            return;
                        }

                        qz.websocket.connect().then(() => {
                            qz.printers.find().then(printers => console.log(printers));
                            const config = qz.configs.create("Microsoft Print to PDF");


                            const data = [
                                ...singleReceipt,
                                ...singleReceipt,
                                ...singleReceipt
                            ];

                            return qz.print(config, data);
                        }).then(() => {
                            resolve();
                        }).catch(err => {
                            reject(err);
                        });
                    });
                }
                function convertAmountToWords(amount) {
                    const ones = ["", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine"];
                    const teens = ["Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eighteen",
                        "Nineteen"
                    ];
                    const tens = ["", "", "Twenty", "Thirty", "Forty", "Fifty", "Sixty", "Seventy", "Eighty", "Ninety"];

                    function numberToWords(num) {
                        let word = "";
                        if (num >= 100000) {
                            word += ones[Math.floor(num / 100000)] + " Hundred ";
                            num %= 100000;
                        }
                        if (num >= 20000) {
                            word += tens[Math.floor(num / 10000)] + " ";
                            num %= 10000;
                        } else if (num >= 10000) {
                            word += teens[Math.floor((num % 10000) / 1000)] + " Thousand ";
                            num %= 1000;
                        }
                        if (num >= 1000) {
                            word += ones[Math.floor(num / 1000)] + " Thousand ";
                            num %= 1000;
                        }
                        if (num >= 100) {
                            word += ones[Math.floor(num / 100)] + " Hundred ";
                            num %= 100;
                        }
                        if (num >= 20) {
                            word += tens[Math.floor(num / 10)] + " ";
                            num %= 10;
                        } else if (num >= 10) {
                            word += teens[num - 10] + " ";
                            num = 0;
                        }
                        if (num > 0) {
                            word += ones[num] + " ";
                        }
                        return word.trim();
                    }

                    let num = Math.floor(amount);
                    let cents = Math.round((amount - num) * 100);
                    let words = numberToWords(num);
                    if (cents > 0) {
                        words += ` and ${cents} Centavos`;
                    }
                    return words || "Zero";
                }
            </script>

            <script>
                const fees = @json($uniformFees);
                const tableBody = document.getElementById('feesTableBody');
                const grandTotalEl = document.getElementById('grand-total');
                let addedFees = {}; // to prevent duplicates

                document.getElementById('addFeeButton').addEventListener('click', function() {
                    const selectedId = document.getElementById('selectedUniform').value;

                    if (addedFees[selectedId]) return;

                    const fee = fees.find(f => f.id == selectedId);
                    if (!fee) return;

                    addedFees[selectedId] = true;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                                        <td class="fee-name">${fee.name}</td>
                                        <td>
                                            <input type="hidden" name="fees[${fee.id}][name]" value="${fee.name}">
                                            <input type="hidden" name="fees[${fee.id}][amount]" value="${fee.amount}">
                                            ₱<span class="fee-amount" data-fee-id="${fee.id}">${parseFloat(fee.amount).toFixed(2)}</span>
                                        </td>
                                        <td>
                                            <input type="number" min="0" name="fees[${fee.id}][quantity]" class="form-control quantity-input" data-fee-id="${fee.id}" value="0" style="width: 80px; margin: 0 auto;">
                                        </td>
                                        <td>
                                            <span class="fee-total" id="fee-total-${fee.id}">₱0.00</span>
                                        </td>
                                    `;

                    tableBody.appendChild(row);
                });


                document.addEventListener('input', function(e) {
                    if (e.target.classList.contains('quantity-input')) {
                        const feeId = e.target.dataset.feeId;
                        const amount = parseFloat(document.querySelector(`.fee-amount[data-fee-id="${feeId}"]`).innerText);
                        const quantity = parseInt(e.target.value) || 0;
                        const total = amount * quantity;

                        document.getElementById(`fee-total-${feeId}`).innerText = `₱${total.toFixed(2)}`;
                        updateGrandTotal();
                    }
                });

                function updateGrandTotal() {
                    let grandTotal = 0;
                    document.querySelectorAll('.fee-total').forEach(el => {
                        grandTotal += parseFloat(el.innerText.replace('₱', '')) || 0;
                    });
                    grandTotalEl.innerText = `₱${grandTotal.toFixed(2)}`;
                }
            </script>
        </div>

    </div>
    <!-- End Page Content -->

    @include('layouts.footer')

</div>
<!-- End of Content Wrapper -->
@endsection

@section('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('js/datatables.js') }}"></script>
<!-- DataTables Initialization Script -->
<script>
    $(document).ready(function() {
        $('#collegePaymentsTable').DataTable();
        $('#shsPaymentsTable').DataTable();
    });
</script>


@endsection
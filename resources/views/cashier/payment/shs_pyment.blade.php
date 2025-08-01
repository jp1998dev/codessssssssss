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
                <h1 class="h3 mb-0 text-gray-800">Manage Shs Student Billings</h1>

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
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="paymentTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="completed-tab" data-bs-toggle="tab" href="#payment" role="tab">Completed</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pending-tab" data-bs-toggle="tab" href="#pending" role="tab">Pending</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body tab-content" id="paymentTabsContent">
                        <div class="tab-pane fade show active" id="payment" role="tabpanel">
                            <div class="table-responsive">

                                <table class="table table-bordered" id="paymentsTable">
                                    <thead>
                                        <tr>
                                            <th>LRN</th>
                                            <th>Student Name</th>
                                            <th>Strand</th>
                                            <th>Amount</th>
                                            <th>Payment Date</th>
                                            <th>Type Of Payee</th>
                                            <th>OR Number</th>

                                            <th>Action</th> <!-- New column -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments->where('status', '==', 'completed') as $payment)
                                        <tr>
                                            <td style="text-align: center">{{ $payment->student->lrn_number }}</td>
                                            <td>{{$payment->student->first_name }} {{ $payment->student->last_name ?? 'No student found' }}</td>

                                            <td>{{ $payment->enrollment->strand }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y h:i A') }}
                                            </td>
                                            <td> {{ $payment->enrollment->type_of_payee }}</td>
                                            <td>{{ $payment->or_number }}</td>

                                            <td>

                                                <button class="btn btn-sm btn-primary reprint-btn"
                                                    data-student-number="{{ $payment->student->lrn_number }}"
                                                    data-name="{{ $payment->student->full_name ?? 'No student found' }}"
                                                    data-amount="{{ $payment->amount }}"
                                                    data-remarks="{{ $payment->remarks }}"
                                                    data-date="{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y') }}"
                                                    data-school-year="{{ $payment->enrollment->semester }} SY {{ $payment->school_year }}"
                                                    data-balance="{{ $payment->balance_due ?? 0 }}">
                                                    Reprint
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pending" role="tabpanel">
                            <div class="table-responsive">

                                <table class="table table-bordered" id="pendingTable">
                                    <thead>
                                        <tr>
                                            <th>LRN</th>
                                            <th>Student Name</th>
                                            <th>Strand</th>
                                            <th>Amount</th>
                                            <th>Payment Date</th>
                                            <th>Type Of Payee</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @ @foreach ($payments->where('status', '!=', 'completed') as $payment)
                                        <tr>
                                            <td style="text-align: center">{{ $payment->student->lrn_number }}</td>
                                            <td>{{$payment->student->first_name }} {{ $payment->student->last_name ?? 'No student found' }}</td>

                                            <td>{{ $payment->enrollment->strand }}</td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y h:i A') }}
                                            </td>
                                            <td>{{ $payment->enrollment->type_of_payee }}</td>
                                            <td>{{ $payment->status }} </td>
                                            <td>
                                                <button class="btn btn-primary"
                                                    @if($payment->status === 'pending') disabled @endif
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updatePaymentModal"
                                                    data-id="{{ $payment->payment_id }}">
                                                    Update
                                                </button>
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


            <!-- Update Payment Modal -->
            <div class="modal fade" id="updatePaymentModal" tabindex="-1" aria-labelledby="updatePaymentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('payment.shs.markAsCompleted') }}">
                        @csrf
                        <input type="hidden" name="payment_id" id="modal_payment_id">
                        <div class="modal-content shadow-lg rounded-3">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title fw-bold" id="updatePaymentModalLabel">Update Payment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body px-4 py-3">
                                <div class="mb-3">
                                    <label for="or_number" class="form-label">OR Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="or_number" id="or_number" required>
                                </div>
                                <div class="mb-3">
                                    <label for="remarks" class="form-label">Remarks</label>
                                    <textarea class="form-control" name="remarks" id="remarks" rows="3" placeholder="Optional remarks..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Mark as Completed</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <script>
                document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
                    button.addEventListener('click', function() {
                        const paymentId = this.getAttribute('data-id');
                        document.getElementById('modal_payment_id').value = paymentId;
                    });
                });
            </script>


            <div class="modal fade" id="newPaymentModal" tabindex="-1" aria-labelledby="newPaymentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('shspayment.store') }}" onsubmit="return handlePrintAndSubmit(event)">
                        @csrf
                        <div class="modal-content shadow-lg rounded-3">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title fw-bold" id="newPaymentModalLabel">New Payment Entry</h5>
                            </div>
                            <div class="modal-body px-4 py-3">
                                <input type="hidden" id="schoolYearInput" name="school_year">
                                <input type="hidden" id="semesterInput" name="semester">

                                <!-- Student Seasrch Section -->
                                <div class="border rounded p-3 mb-4">
                                    <h6 class="text-primary fw-semibold mb-3">Student Details</h6>
                                    <div class="mb-3 position-relative">
                                        <label for="searchStudent" class="form-label">Search Student</label>
                                        <input type="text" class="form-control" id="searchStudent" name="lrn_number"
                                            placeholder="Search by Student ID or Name">
                                        <ul id="searchSuggestions" class="list-group position-absolute w-100 mt-1"
                                            style="z-index: 1050;"></ul>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="studentName" class="form-label">Student Name</label>
                                            <input type="text" class="form-control" id="studentName" readonly required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="balanceDue" class="form-label">Balance Due</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="number" class="form-control" id="balanceDue" readonly required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Info Section -->
                                <div class="border rounded p-3 mb-4">
                                    <h6 class="text-success fw-semibold mb-3">Payment Details</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="amount" class="form-label">Amount</label>
                                            <div class="input-group">
                                                <span class="input-group-text">₱</span>
                                                <input type="number" class="form-control" id="amount" name="payment_amount" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="">
                                            <span class="me-3">Mode of Payment</span>

                                            <div class="col-md-6 d-flex align-items-center">
                                                <div class="form-check me-3">
                                                    <input class="form-check-input" type="radio" id="cash" name="payment_method" value="CASH">
                                                    <label class="form-check-label" for="cash">Cash</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" id="online" name="payment_method" value="ONLINE">
                                                    <label class="form-check-label" for="online">Online</label>
                                                </div>
                                            </div>
                                            <input type="text" name="ref_number" class="form-control mt-2 @error('ref_number') is-invalid @enderror"
                                                id="refNumber" placeholder="Reference Number (optional)" disabled>
                                        </div>
                                        <!-- <div class="mt-3">
                            <label for="remarks" class="form-label">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="2" placeholder="Add any notes..."></textarea>
                        </div> -->
                                        <div class="mt-3">
                                            <label for="orNumber" class="form-label">OR Number</label>
                                            <input type="text" class="form-control @error('or_number') is-invalid @enderror"
                                                id="orNumber" name="or_number" placeholder="Enter OR Number" >
                                            @error('or_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer bg-light">
                                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Submit Payment</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>

            <script>
                document.querySelectorAll('.reprint-btn').forEach(btn => {
                    const cashier = @json(auth()->user()->name);
                    btn.addEventListener('click', () => {
                        reprintReceipt({
                            student_number: btn.dataset.studentNumber,
                            name: btn.dataset.name,
                            amount: parseFloat(btn.dataset.amount),
                            remarks: btn.dataset.remarks,
                            date: btn.dataset.date,
                            schoolYear: btn.dataset.schoolYear,
                            balance: parseFloat(btn.dataset.balance),
                            c: cashier.name
                        });
                    });
                });
                document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        const refNumberInput = document.getElementById('refNumber');
                        const orNumberInput = document.getElementById('orNumber');
                        // const remarksInput = document.getElementById('remarks');
                        if (this.value === 'ONLINE') {
                            refNumberInput.disabled = false;
                            refNumberInput.required = true;
                            orNumberInput.disabled = true;
                            orNumberInput.required = false;
                            // remarksInput.disabled = true;
                            // remarksInput.required = false;
                        } else {
                            refNumberInput.disabled = true;
                            refNumberInput.required = false;
                            refNumberInput.value = '';
                            orNumberInput.disabled = false;
                            orNumberInput.required = true;
                            // remarksInput.disabled = false;
                            // remarksInput.required = true;
                        }
                    });
                });
            </script>

            <div id="printableReceipt" style="display: none;">
                <div
                    style="width: 100%; font-family: Arial, sans-serif; text-align: left; position: relative; font-size: larger; line-height: 1.8;">
                    <div style="text-align: left; font-size: 20px; margin-bottom: 50px;">
                        <span id="receiptDate"></span>
                    </div>
                    <div style="text-align: left; font-size: 22px; font-weight: bold; margin-bottom: 50px;">
                        <span id="receiptStudentName"></span>
                    </div>
                    <div style="text-align: left; font-size: 20px; margin-bottom: 20px;">
                        School Year: <span id="receiptSchoolYear"></span>
                    </div>
                    <div style="text-align: left; font-size: 20px; margin-bottom: 20px;">
                        Semester: <span id="receiptSemester"></span>
                    </div>
                    <div style="text-align: left; font-size: 20px; margin-bottom: 50px;">
                        <span id="receiptAmountWords"></span>
                    </div>
                    <div style="text-align: left; font-size: 20px; margin-bottom: 50px;">
                        <span id="receiptAmount"></span>
                    </div>
                    <div style="text-align: left; font-size: 20px; margin-bottom: 50px;">
                        <span id="receiptRemarks"></span>
                    </div>
                    <!-- Add this new div for remaining balance -->
                    <div style="text-align: left; font-size: 20px; margin-bottom: 50px;">
                        Remaining Balance: <span id="receiptBalanceDue"></span>
                    </div>
                </div>
            </div>




            <script>
                function handlePrintAndSubmit(event) {
                    event.preventDefault();
                     const payment_mehthod = document.querySelector('input[name="payment_method"]:checked').value;
                     if (payment_mehthod === "ONLINE") {
                        event.target.submit();
                        return false;
                    }
                    // Get form values
                    const studentName = document.getElementById('studentName').value.trim();
                    const amount = parseFloat(document.getElementById('amount').value);
                    const balanceDue = parseFloat(document.getElementById('balanceDue').value);
                    const orNumber = document.getElementById('orNumber').value.trim();

                    // Clear any existing error messages
                    const existingError = document.getElementById('js-error-alert');
                    if (existingError) existingError.remove();

                    // Basic validations
                    if (studentName === "") {
                        showErrorMessage('Student name cannot be blank.');
                        return false;
                    }

                    if (isNaN(amount) || amount <= 0) {
                        showErrorMessage('The payment amount must be greater than 0.');
                        return false;
                    }

                    if (amount > balanceDue) {
                        showErrorMessage('The payment amount cannot be greater than the current balance due.');
                        return false;
                    }

                    if (orNumber === "") {
                        showErrorMessage('OR Number cannot be blank.');
                        return false;
                    }

                    // Check for duplicate OR number
                    fetch(`/check-or-number?or_number=${encodeURIComponent(orNumber)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                showErrorMessage('This OR Number is already in use. Please enter a different number.');
                            } else {
                                printReceipt();
                                setTimeout(() => event.target.submit(), 800);
                            }
                        })
                        .catch(error => {
                            console.error('Error checking OR number:', error);
                            showErrorMessage('Error verifying OR number. Please try again.');
                        });

                    return false;
                }

                function showErrorMessage(message) {
                    const existing = document.getElementById('js-error-alert');
                    if (existing) existing.remove();

                    const alert = document.createElement('div');
                    alert.className = 'popup-alert fadeDownIn shadow rounded-lg p-4';
                    alert.id = 'js-error-alert';
                    alert.style.backgroundColor = '#dc3545';
                    alert.style.color = '#fff';
                    alert.innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-semibold fs-6">
                ${message}
                <i class="fas fa-exclamation-circle ms-1"></i>
            </span>
        </div>
    `;
                    document.body.appendChild(alert);

                    setTimeout(() => {
                        alert.classList.remove('fadeDownIn');
                        alert.classList.add('fadeOut');
                        setTimeout(() => alert.remove(), 400);
                    }, 3000);
                }

                async function printReceipt() {
                    function getDateInWords(date) {
                        const months = [
                            "January", "February", "March", "April", "May", "June",
                            "July", "August", "September", "October", "November", "December"
                        ];
                        const day = date.getDate();
                        const month = months[date.getMonth()];
                        const year = date.getFullYear();
                        return `${month} ${day}, ${year}`;
                    }

                    const currentDate = new Date();

                    const nameParts = document.getElementById('studentName').value.trim().split(' ');
                    let formattedName = document.getElementById('studentName').value;
                    if (nameParts.length >= 3) {
                        const lastName = nameParts.pop();
                        const firstName = nameParts.shift();
                        const middleInitial = nameParts.length > 0 ? nameParts[0].charAt(0).toUpperCase() + '.' : '';
                        formattedName = `${lastName.toUpperCase()}, ${firstName.toUpperCase()} ${middleInitial}`;
                    }

                    const amount = parseFloat(document.getElementById('amount').value);
                    const balanceDue = parseFloat(document.getElementById('balanceDue').value);
                    const remainingBalance = balanceDue - amount;
                    const remarks = document.getElementById('remarks').value;
                    const amountWords = convertAmountToWords(amount).toUpperCase() + ' PESOS ONLY';
                    const searchInput = document.getElementById('searchStudent').value;

                    const rawSchoolYear = document.getElementById('schoolYearInput')?.value || "N/A";
                    const rawSemester = document.getElementById('semesterInput')?.value || "N/A";

                    const formattedSemesterAndSY = `School Year ${rawSchoolYear}`;

                    const cashier = @json(auth()->user()->name);
                    await createCustomPDF({
                        student_number:searchInput,
                        name: formattedName,
                         amount : amount,
                        remarks : remarks ?? '',
                        date :getDateInWords(currentDate),
                        schoolYear : formattedSemesterAndSY,
                        balance : remainingBalance,
                    });
                    
                }

                //             function reprintReceipt(data) {
                //                 const studentNumber = data.student_number;
                //                 const amount = parseFloat(data.amount);
                //                 const remainingBalance = parseFloat(data.balance); // ✅ Use as-is
                //                 const amountWords = convertAmountToWords(amount).toUpperCase() + ' PESOS ONLY';

                //                 const nameParts = data.name.trim().split(' ');
                //                 let formattedName = data.name;
                //                 if (nameParts.length >= 3) {
                //                     const lastName = nameParts.pop();
                //                     const firstName = nameParts.shift();
                //                     const middleInitial = nameParts.length > 0 ? nameParts[0].charAt(0).toUpperCase() + '.' : '';
                //                     formattedName = `${lastName.toUpperCase()}, ${firstName.toUpperCase()} ${middleInitial}`;
                //                 }



                //                 const formattedSemesterAndSY = `School Year ${data.schoolYear}`;
                //                 

                //                 const printContent = `
                //                 <div class="date">${data.date}</div>
                //                  <div class="no">${studentNumber}</div>
                //                 <div class="name">${formattedName}</div>
                //                 <div class="sem"><h4>${formattedSemesterAndSY}</h4></div>
                //                 <div class="rem"><h4>${data.remarks}</h4></div>
                //                 <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                //                     <div class="word">${amountWords}</div>
                //                     <div class="amount">₱${amount.toFixed(2)}</div>
                //                 </div>
                //                 <div class="cashier">${cashier}</div>
                //                  <div class="rem-bal">${remainingBalance}</div>
                //             `;

                //                 const printWindow = window.open('', '', 'width=491,height=718');
                //                 printWindow.document.open();
                //                 printWindow.document.write(`
                //     <html>
                //         <head>
                //             <title>Reprint Receipt</title>
                //             <style>
                //                 body { font-family: Arial, sans-serif; font-size: 18px;   width:491px; height:719px; padding: 20px; line-height: 1.8; }
                //                 div { margin-bottom: 15px; }
                //                 .cashier{position:absolute; bottom:30px; right:110px;}
                //                 .rem-bal{position:absolute; bottom:110px; right:110px;  
                //             font-weight: bold; }
                //                 .sem{position:absolute; bottom:350px; left:50px;}
                //                 .rem{position:absolute; bottom:380px; left:50px;}
                //                 .amount{position:absolute; bottom:510px; left:50px;  font-size: 22px; 
                //             font-weight: bold; }
                //                 .word{position:absolute; bottom:560px; left:50px}
                //                 .name{position:absolute; top:200px; left:50px;  font-size: 22px; 
                //             font-weight: bold; }
                //                 .date{position:absolute; top:150px; left:50px;}
                //                 .no{position:absolute; top:150px; right:110px; }
                //             </style>
                //         </head>
                //         <body>
                //             ${printContent}
                //         </body>
                //     </html>
                // `);
                //                 printWindow.document.close();
                //                 printWindow.print();
                //             }
               async function createCustomPDF(data) {
                    const { jsPDF } = window.jspdf;
                    const cashier = @json(auth()->user()->name);
                    const inToPt = (inch) => inch * 72;
                    const pdfWidth = inToPt(6.5);
                    const pdfHeight = inToPt(8.9);

                    const pdf = new jsPDF({
                        unit: 'pt',
                        format: [pdfWidth, pdfHeight]
                    });

                    
                    const {
                        student_number = '',
                        name = '',
                        amount = 0,
                        remarks = '',
                        date = '',
                        schoolYear = '',
                        balance = 0,
                        
                    } = data;
                    const formattedSemesterAndSY = `School Year ${schoolYear}`
                    pdf.setFontSize(12);
                    pdf.setTextColor(0, 0, 0);

                   
                    pdf.text(String(student_number), pdfWidth - 100, 60);

                
                    pdf.text(String(date), 60, 100);

                    
                    pdf.setTextColor(255, 0, 0);
                    pdf.setFontSize(17);
                    pdf.text(String(name), 60, 150);

                    // pdf.setTextColor(0, 0, 0);
                    // pdf.text(String(formattedSemesterAndSY), 60, 180);

                   
                    pdf.text(convertAmountToWords(amount), 60, 220);

                  
                    pdf.setTextColor(0, 150, 0);
                    pdf.text('₱' + String(amount.toFixed(2)), 60, 240);

                    pdf.setTextColor(0, 0, 0);
                    pdf.setFontSize(17);
                    pdf.text(String(remarks), 60, 320);
                    pdf.text(String(formattedSemesterAndSY), 60, 340);

                
                    pdf.setTextColor(0, 150, 0);
                    pdf.setFontSize(17);
                    pdf.text(String(balance), pdfWidth - 110, pdfHeight - 140);

                    pdf.setTextColor(0, 0, 0);
                    pdf.setFontSize(12);
                    pdf.text(String(cashier), pdfWidth - 110, pdfHeight - 80);

                   
                    pdf.autoPrint();
                    window.open(pdf.output('bloburl'), '_blank');
                }

                async function reprintReceipt(data) {
                    await createCustomPDF(data);
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



            <style>
                #printableReceipt {
                    font-size: 24px !important;
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
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchStudent');
                    const suggestionsList = document.getElementById('searchSuggestions');
                    const studentNameInput = document.getElementById('studentName');
                    const balanceDueInput = document.getElementById('balanceDue');

                    // Hidden inputs
                    const schoolYearInput = document.getElementById('schoolYearInput');
                    const semesterInput = document.getElementById('semesterInput');

                    searchInput.addEventListener('input', function() {
                        const query = this.value;

                        if (query.length >= 2) {
                            fetch(`/api/search-shs-students?query=${query}`)
                                .then(response => response.json())
                                .then(data => {
                                    suggestionsList.innerHTML = '';
                                    data.forEach(student => {
                                        const suggestion = document.createElement('li');
                                        suggestion.className = 'list-group-item list-group-item-action';
                                        suggestion.innerHTML =
                                            `${student.student_id} - ${student.full_name} ` +
                                            `<span class="badge ${student.balance_due > 0 ? 'bg-danger' : 'bg-success'}">` +
                                            `${student.balance_due > 0 ? `₱${student.balance_due}` : 'No billing found'}` +
                                            `</span>`;

                                        suggestion.addEventListener('click', function() {
                                            // Set visible fields
                                            studentNameInput.value = student.full_name;
                                            balanceDueInput.value = student.balance_due;
                                            searchInput.value = student.student_lrn;
                                            suggestionsList.innerHTML = '';
                                            // Set hidden inputs directly from data
                                            schoolYearInput.value = student.school_year;
                                            semesterInput.value = student.semester;

                                            // Fetch semester and school_year
                                            fetch(`/get-semester-year/${student.student_id}`)
                                                .then(response => response.json())
                                                .then(info => {
                                                    if (!info.error) {
                                                        schoolYearInput.value = info
                                                            .school_year;
                                                        semesterInput.value = info.semester;
                                                    } else {
                                                        schoolYearInput.value = '';
                                                        semesterInput.value = '';
                                                        console.error(info.error);
                                                    }
                                                })
                                                .catch(error => {
                                                    console.error('Fetch error:', error);
                                                });
                                        });
                                        suggestionsList.appendChild(suggestion);
                                    });
                                });
                        } else {
                            suggestionsList.innerHTML = '';
                        }
                    });

                    // Hide suggestions when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!suggestionsList.contains(event.target) && event.target !== searchInput) {
                            suggestionsList.innerHTML = '';
                        }
                    });
                });
            </script>


        </div>

    </div>
    <!-- End Page Content -->

    @include('layouts.footer')

</div>
<div id="js-error-alert-container"></div>

<!-- End of Content Wrapper -->
@endsection

@section('scripts')
<script src="{{ asset('js/datatables.js') }}"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#paymentsTable').DataTable({
            responsive: true,
            pageLength: 10
        });
        $('#pendingTable').DataTable({
            responsive: true,
            pageLength: 10
        });
    });
</script>


@endsection
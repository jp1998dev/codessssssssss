@extends('layouts.main')

@section('tab_title', 'Dashboard')
@section('registrar_sidebar')
@include('registrar.registrar_sidebar')
@endsection

@section('content')

<div id="content-wrapper" class="d-flex flex-column">


    <div id="content">

        @include('layouts.topbar')


        <div class="container-fluid">
            @include('layouts.success-message')
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Senior High New Enrollment</h1>


                <button class="btn btn-primary" data-toggle="modal" data-target="#studentFormModal">
                    Open Admission Form
                </button>

            </div>
            <div class="modal fade" id="studentFormModal" tabindex="-1" aria-labelledby="studentFormModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable" style="padding: 20px;">
                    <div class="modal-content" style="max-height: 90vh; overflow-y: auto !important; width:100%; min-width: 1000px; padding: 20px;">

                        <div class="modal-header">
                            <h5 class="modal-title" id="studentFormModalLabel">Student Registration</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form id="studentForm" method="POST" action="{{ route('shs.store') }}">
                            @csrf


                            <div class="form-step form-step-active" id="step-1">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label>Track</label>
                                        <select name="track" class="form-select" required>
                                            <option value="">Select Track</option>
                                            <option value="1">General Academic Track</option>
                                            <option value="2">TVL</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Strand</label>
                                        <select class="form-select" name="strandID" required>
                                            <option value="">Select Strand</option>
                                            <option value="1">STEM</option>
                                            <option value="2">HUMSS</option>
                                            <option value="3">TVL-HE</option>
                                            <option value="4">TVL-ICT</option>
                                            <option value="5">ABM</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Section</label>
                                        <input type="text" class="form-control" name="section" placeholder="Enter section" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" name="lastName" placeholder="Enter last name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" name="firstName" placeholder="Enter first name" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" name="middleName" placeholder="Enter middle name">
                                    </div>

                                    <div class="col-md-4">
                                        <label>Gender</label>
                                        <select name="gender" class="form-select" required>
                                            <option value="">Select Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Civil Status</label>
                                        <input type="text" class="form-control" name="civilStatus" placeholder="Single, Married, etc.">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Birth Date</label>
                                        <input type="date" class="form-control" name="birthDate" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Birth Place</label>
                                        <input type="text" class="form-control" name="birthPlace" placeholder="Enter birth place">
                                    </div>
                                    <div class="col-md-4">
                                        <label>Address</label>
                                        <input type="text" class="form-control" name="address" placeholder="Enter complete address" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Mobile Number</label>
                                        <input type="text" class="form-control" name="mobileNumber" placeholder="09XXXXXXXXX" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label>Email Address</label>
                                        <input type="email" class="form-control" name="emailAddress" placeholder="example@email.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Secondary School Name</label>
                                        <input type="text" class="form-control" name="secondarySchoolName" placeholder="Enter school name">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Secondary School Address</label>
                                        <input type="text" class="form-control" name="secondarySchoolAddress" placeholder="Enter school address">
                                    </div>

                                    <div class="col-md-4">
                                        <label>LRN</label>
                                        <input type="text" class="form-control" name="lrn" placeholder="Enter LRN" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label>ESC Number</label>
                                        <input type="text" class="form-control" name="escNumber" placeholder="Enter ESC Number">
                                    </div>

                                    <div class="col-md-4">
                                        <label>School Classification</label>
                                        <select name="secondarySchoolClassification" class="form-select" required>
                                            <option value="">Select School Type</option>
                                            <option value="Private">Private</option>
                                            <option value="Public">Public</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Type Of Pee</label>
                                        <select name="type_of_pee" class="form-select" required>
                                            <option value="">Select Type</option>
                                            <option value="1">With Voucher</option>
                                            <option value="2">With ESC</option>
                                            <option value="3">No Voucher</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3 text-end">
                                    <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                                </div>
                            </div>


                            <div class="form-step" id="step-2">
                                <div class="row g-3">
                                    <div class="col-md-4"><label>Father's Name</label><input type="text" class="form-control" name="fathersName" placeholder="Enter father's full name" required></div>
                                    <div class="col-md-4"><label>Father's Address</label><input type="text" class="form-control" name="fathersAddress" placeholder="Enter father's address" required></div>
                                    <div class="col-md-4"><label>Father's Occupation</label><input type="text" class="form-control" name="fathersOccupation" placeholder="Enter father's occupation"></div>
                                    <div class="col-md-4"><label>Father's Contact Number</label><input type="text" class="form-control" name="fathersContactNumber" placeholder="09XXXXXXXXX"></div>

                                    <div class="col-md-4"><label>Mother's Name</label><input type="text" class="form-control" name="mothersName" placeholder="Enter mother's full name" required></div>
                                    <div class="col-md-4"><label>Mother's Address</label><input type="text" class="form-control" name="motherAddress" placeholder="Enter mother's address" required></div>
                                    <div class="col-md-4"><label>Mother's Occupation</label><input type="text" class="form-control" name="mothersOccupation" placeholder="Enter mother's occupation"></div>
                                    <div class="col-md-4"><label>Mother's Contact Number</label><input type="text" class="form-control" name="mothersContactNumber" placeholder="09XXXXXXXXX"></div>
                                </div>
                                <div class="mt-3 d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="prevStep()">Back</button>
                                    <button type="button" class="btn btn-primary" onclick="nextStep()">Next</button>
                                </div>
                            </div>

                            <div class="form-step" id="step-3">
                                <div class="row g-3">
                                    <div class="col-md-4"><label>Guardian Name</label><input type="text" class="form-control" name="guardianName" placeholder="Enter guardian's name" required></div>
                                    <div class="col-md-4"><label>Guardian Address</label><input type="text" class="form-control" name="guardianAddress" placeholder="Enter guardian's address" required></div>
                                    <div class="col-md-4"><label>Relationship</label><input type="text" class="form-control" name="relationship" placeholder="Ex: Aunt, Uncle, etc." required></div>
                                    <div class="col-md-4"><label>Guardian Contact No.</label><input type="text" class="form-control" name="guardianContactNo" placeholder="09XXXXXXXXX" required></div>

                                    <div class="col-md-4"><label>Government Assistance</label><input type="text" class="form-control" name="governmentAssistance" placeholder="Ex: 4Ps, Scholarship"></div>
                                    <div class="col-md-4"><label>Grade</label>
                                        <!-- <input type="text"  name="grade" placeholder="Enter grade level" required> -->
                                        <select name="grade" id="" class="form-select">
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4"><label>School Year</label><input type="text" class="form-control" name="schoolYearID" placeholder="Ex: 2025"></div>
                                    <div class="col-md-4"><label>Region</label><input type="text" class="form-control" name="region" placeholder="Enter region" required></div>
                                    <div class="col-md-4"><label>Division ID</label><input type="text" class="form-control" name="divisionID" placeholder="Enter division ID"></div>
                                    <div class="col-md-4"><label>Summer</label><input type="text" class="form-control" name="summer" placeholder="Summer class details"></div>
                                    <div class="col-md-4">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" name="voucherRecipient" id="voucherRecipient">
                                            <label class="form-check-label" for="voucherRecipient">Voucher Recipient</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4"><label>Year Graduated ID</label><input type="text" class="form-control" name="yearGraduatedID" placeholder="2025"></div>
                                    <div class="col-md-4"><label>Award ID</label><input type="text" class="form-control" name="awardID" placeholder="Enter award ID"></div>
                                    <div class="col-md-4"><label>Clearance</label><input type="text" class="form-control" name="clearance" placeholder="Enter clearance details"></div>
                                </div>
                                <div class="mt-3 d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary" onclick="prevStep()">Back</button>
                                    <!-- <button type="submit" class="btn btn-success">Save Student</button> -->
                                    <button type="button" class="btn btn-success" onclick="submitStudent()">Save Student</button>

                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>


            <style>
                .form-step {
                    display: none;
                    animation: fade 0.4s ease-in-out;
                }

                .form-step-active {
                    display: block;
                }

                @keyframes fade {
                    from {
                        opacity: 0;
                        transform: translateX(40px);
                    }

                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }

                .is-invalid {
                    border-color: #dc3545;
                }
            </style>

            <script>
                let currentStep = 1;

                function validateStep(step) {
                    let isValid = true;
                    const stepInputs = document.querySelectorAll(`#step-${step} input, #step-${step} select`);

                    stepInputs.forEach((input) => {
                        input.classList.remove('is-invalid');


                        if (input.hasAttribute('required') && !input.value.trim()) {
                            input.classList.add('is-invalid');
                            isValid = false;
                            return;
                        }

                        const name = input.name;

                        if ((name.toLowerCase().includes('contact') || name === 'mobileNumber') && input.value) {
                            if (!/^09\d{9}$/.test(input.value)) {
                                input.classList.add('is-invalid');
                                isValid = false;
                            }
                        }

                        if (input.type === 'email' && input.value) {
                            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (!emailPattern.test(input.value)) {
                                input.classList.add('is-invalid');
                                isValid = false;
                            }
                        }

                        if (name === 'birthDate' && input.value) {
                            const selectedDate = new Date(input.value);
                            const today = new Date();
                            if (selectedDate > today) {
                                input.classList.add('is-invalid');
                                isValid = false;
                            }
                        }

                        if (name === 'lrn' && input.value && !/^\d{12}$/.test(input.value)) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        }

                        if (name === 'escNumber' && input.value && !/^\d+$/.test(input.value)) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        }

                        if (name === 'schoolYearID' && input.value && !/^\d{4}$/.test(input.value)) {
                            input.classList.add('is-invalid');
                            isValid = false;
                        }
                    });

                    return isValid;
                }

                function nextStep() {
                    if (!validateStep(currentStep)) return;

                    document.querySelector(`#step-${currentStep}`).classList.remove('form-step-active');
                    currentStep++;
                    const nextStepDiv = document.querySelector(`#step-${currentStep}`);
                    if (nextStepDiv) nextStepDiv.classList.add('form-step-active');
                }

                function submitStudent() {
                    if (!validateStep(currentStep)) return;
                    const form = document.getElementById('studentForm');
                    form.submit();
                }


                function prevStep() {
                    document.querySelector(`#step-${currentStep}`).classList.remove('form-step-active');
                    currentStep--;
                    const prevStepDiv = document.querySelector(`#step-${currentStep}`);
                    if (prevStepDiv) prevStepDiv.classList.add('form-step-active');
                }
            </script>

            <div class="row justify-content-center mt-3">
                <div class="col-md-12">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="miscFees">
                                    <thead>
                                        <tr>
                                            <th>LRN</th>
                                            <th>Full Name</th>
                                            <th>Strand</th>
                                            <th>Admission Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($admissions as $admission)
                                        <tr>
                                            <td>{{ strtoupper($admission->lrn_number) }}</td>
                                            <td>{{ strtoupper($admission->first_name . ' ' . $admission->middle_name . ' ' . $admission->last_name) }}
                                            </td>
                                            <td>{{ $admission->enrollment->combination_label ?? 'N/A' }}</td>

                                            <td>{{ ucfirst($admission->enrollment?->status) ?? 'N/A' }}</td>


                                            <td class="text-center align-middle">
                                                @php
                                                $initialPayment = $admission->billing->initial_payment ?? 0;
                                                @endphp

                                                <div class="d-flex justify-content-center align-items-center gap-1" style="min-width: 150px;">

                                                    <button type="button" class="btn btn-info btn-sm position-relative"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#promoteModal{{ $admission->student_id }}"
                                                        title="Promote Student">
                                                        <i class="fas fa-user-graduate"></i>
                                                    </button>

                                                    <!-- View Button with Eye Icon -->
                                                    <button type="button"
                                                        class="btn btn-info btn-sm position-relative"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#studentModal{{ $admission->student_id }}"
                                                        title="View Student">
                                                        <i class="fas fa-eye"></i>

                                                        @if($initialPayment <= 0)
                                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"
                                                            data-bs-toggle="tooltip"
                                                            title="This student hasn't made an initial payment yet.">
                                                            Warning
                                                            </span>
                                                            @endif
                                                    </button>

                                                    <!-- Edit Button -->
                                                    <a href="{{ route('shs.edit', $admission->student_id) }}"
                                                        class="btn btn-warning btn-sm"
                                                        title="Edit Student">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <!-- Print COR Button -->
                                                    <a href="{{ route('admissions.printCOR', $admission->student_id) }}"
                                                        target="_blank" rel="noopener"
                                                        class="btn btn-primary btn-sm"
                                                        title="Print COR">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </div>

                                                <!-- Initialize Bootstrap tooltips -->
                                                <script>
                                                    document.addEventListener('DOMContentLoaded', function() {
                                                        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                                        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                                                            new bootstrap.Tooltip(tooltipTriggerEl);
                                                        });
                                                    });
                                                </script>
                                            </td>

                                        </tr>

                                        <!-- Promote Modal -->
                                        <div class="modal fade" id="promoteModal{{ $admission->student_id }}" tabindex="-1" aria-labelledby="promoteModalLabel{{ $admission->student_id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('shs.promote', $admission->student_id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="promoteModalLabel{{ $admission->student_id }}">Confirm Promotion</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to promote <strong>{{ $admission->full_name }}</strong> to Grade 12?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-info">Confirm</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Student Information Modal - Larger Size -->
                                        <div class="modal fade" id="studentModal{{ $admission->student_id }}" tabindex="-1"
                                            aria-labelledby="studentModalLabel{{ $admission->student_id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <!-- Changed to modal-lg for larger size -->
                                                <div class="modal-content">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title fw-bold" id="studentModalLabel{{ $admission->student_id }}">
                                                            <i class="bi bi-person-vcard me-2"></i>Student Details
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    @if($admission->billing)
                                                    {{-- Form starts here, targeting your update route --}}
                                                    <form method="POST" action="{{ route('billing.shs.updateInitialPayment', $admission->billing->id) }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="modal-body p-4"> <!-- Added more padding -->
                                                            <!-- Basic Information Section (unchanged, just display) -->
                                                            <div class="mb-4">
                                                                <h6 class="fw-bold text-primary mb-3">
                                                                    <i class="bi bi-info-circle me-2"></i>Basic Information
                                                                </h6>
                                                                <div class="row g-3">
                                                                    <!-- Display info only -->
                                                                    <div class="col-md-6">
                                                                        <div class="bg-light p-3 rounded">
                                                                            <p class="mb-1"><strong>LRN:</strong></p>
                                                                            <p class="text-dark">{{ $admission->lrn_number }}</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="bg-light p-3 rounded">
                                                                            <p class="mb-1"><strong>Full Name:</strong></p>
                                                                            <p class="text-dark">
                                                                                {{ Str::title("{$admission->last_name}, {$admission->first_name} {$admission->middle_name}") }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Other basic info here similarly... -->
                                                                </div>
                                                            </div>

                                                            <!-- Billing Information Section -->
                                                            @if ($admission->billing)
                                                            <div class="mt-4 pt-3 border-top">
                                                                <h6 class="fw-bold text-primary mb-3">
                                                                    <i class="bi bi-cash-coin me-2"></i>Financial Information
                                                                </h6>

                                                                <!-- Display all billing info readonly -->
                                                                <div class="row g-3 mb-3">
                                                                    <div class="col-md-6">
                                                                        <div class="bg-light p-3 rounded">
                                                                            <p class="mb-1"><strong>School Year:</strong></p>
                                                                            <p class="text-dark">{{ $admission->billing->school_year }}</p>
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="col-md-6">
                                                                        <div class="bg-light p-3 rounded">
                                                                            <p class="mb-1"><strong>Semester:</strong></p>
                                                                            <p class="text-dark">{{ $admission->billing->semester }}</p>
                                                                        </div>
                                                                    </div> -->
                                                                </div>

                                                                <div class="row g-3 mb-3">
                                                                    <div class="col-md-6">
                                                                        <div class="bg-light p-3 rounded">
                                                                            <p class="mb-1"><strong>Tuition Fee:</strong></p>
                                                                            <p class="text-dark">₱{{ number_format($admission->billing->tuition_fee, 2) }}</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="bg-light p-3 rounded">
                                                                            <p class="mb-1"><strong>Discount:</strong></p>
                                                                            <p class="text-dark">{{ $admission->billing->discount }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="alert alert-primary mt-3 mb-4">
                                                                    <div class="row g-2">
                                                                        <div class="col-md-6">
                                                                            <div class="p-2">
                                                                                <p class="mb-1"><strong>Total Assessment:</strong></p>
                                                                                <h5 class="fw-bold mb-0">
                                                                                    ₱{{ number_format($admission->billing->total_assessment, 2) }}
                                                                                </h5>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="p-2">
                                                                                <p class="mb-1"><strong>Balance Due:</strong></p>
                                                                                <h5 class="fw-bold mb-0">
                                                                                    ₱{{ number_format($admission->billing->balance_due, 2) }}
                                                                                </h5>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <h6 class="fw-bold mt-4 mb-3">
                                                                    <i class="bi bi-calendar-check me-2"></i>Payment Schedule
                                                                </h6>
                                                                <div class="row g-3">
                                                                    <!-- Full width Initial Payment -->
                                                                    <div class="col-12">
                                                                        <div class="mb-3">
                                                                            <label for="initial_payment{{ $admission->id }}" class="form-label"><strong>Initial Payment</strong></label>
                                                                            <input
                                                                                placeholder="0.00"
                                                                                type="number"
                                                                                name="initial_payment"
                                                                                step="0.01"
                                                                                min="0"
                                                                                class="form-control"
                                                                                id="initial_payment{{ $admission->id }}"
                                                                                value="{{ old('initial_payment', ($admission->billing->initial_payment ?? 0) > 0 ? $admission->billing->initial_payment : '') }}">
                                                                        </div>
                                                                    </div>




                                                                    <!-- Payment Status -->
                                                                    <div class="col-12">
                                                                        <div class="bg-light p-3 rounded">
                                                                            <p class="mb-1"><strong>Payment Status:</strong></p>
                                                                            <p>
                                                                                @php
                                                                                $initial = $admission->billing->initial_payment ?? 0;
                                                                                $balance = $admission->billing->balance_due ?? 0;
                                                                                @endphp

                                                                                @if ($balance == 0)
                                                                                <span class="badge bg-success p-2">Fully Paid</span>
                                                                                @elseif ($initial > 0 && $initial < $balance)
                                                                                    <span class="badge bg-warning p-2">Installment</span>
                                                                                    @else
                                                                                    <span class="badge bg-secondary p-2">—</span>
                                                                                    @endif
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            @else
                                                            <div class="alert alert-warning mt-4">
                                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                                No billing information available for this student.
                                                            </div>
                                                            @endif
                                                        </div>

                                                        <div class="modal-footer">
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="bi bi-save me-2"></i>Save Payment
                                                                </button>
                                                            </div>
                                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                                <i class="bi bi-x-lg me-2"></i> Close
                                                            </button>
                                                        </div>
                                                    </form>
                                                    @else
                                                    <p><em>No billing record found for this admission.</em></p>
                                                    @endif
                                                    {{-- Form ends here --}}
                                                </div>
                                            </div>
                                        </div>

                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>
    <!-- End of Main Content -->

    @include('layouts.footer')

</div>
<!-- End of Content Wrapper -->
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- REQUIRED for DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        const student = @json($students);
        console.log("student: ", student)
        $('#miscFees').DataTable({
            responsive: true,
            pageLength: 10
        });
    });
</script>


<!-- Font Awesome for icons (include in your head tag) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab navigation variables
        const formTabs = document.querySelectorAll('#formTabs .nav-link');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const progressBar = document.querySelector('.progress-bar');
        let currentTab = 0;

        // Admission info elements
        const admissionStatusRadios = document.querySelectorAll('input[name="admission_status"]');
        const transfereeFields = document.getElementById('transfereeFields');
        const manualCourseSelection = document.getElementById('manualCourseSelection');

        // Course selection elements
        const courseSelect = document.getElementById('course_ids');

        // Define required fields for each step (0-indexed)
        const requiredFields = [
            // Step 1: Personal Info
            ['last_name', 'first_name', 'contact_number', 'email', 'region', 'province', 'city', 'barangay'],

            // Step 2: Parents Info (optional)
            [],

            // Step 3: Other Personal Details
            ['gender', 'birthdate', 'citizenship'],

            // Step 4: Admission Info
            ['admission_status', 'major', 'previous_school'],

            // Step 5: Education History
            ['secondary_school', 'secondary_address']
        ];

        // Initialize UI
        updateTabs();
        if (manualCourseSelection) manualCourseSelection.style.display = 'none';
        if (transfereeFields) transfereeFields.style.display = 'none';

        // Tab navigation functions
        nextBtn.addEventListener('click', function() {
            if (validateCurrentStep(currentTab)) {
                currentTab++;
                updateTabs();
            }
        });

        prevBtn.addEventListener('click', function() {
            currentTab--;
            updateTabs();
        });

        function updateTabs() {
            formTabs.forEach((tab, index) => {
                const tabPane = document.querySelector(tab.getAttribute('href'));
                if (index === currentTab) {
                    tab.classList.add('active');
                    tabPane.classList.add('show', 'active');
                } else {
                    tab.classList.remove('active');
                    tabPane.classList.remove('show', 'active');
                }
            });

            prevBtn.disabled = currentTab === 0;
            nextBtn.style.display = currentTab === formTabs.length - 1 ? 'none' : 'block';
            submitBtn.style.display = currentTab === formTabs.length - 1 ? 'block' : 'none';

            const progress = ((currentTab + 1) / formTabs.length) * 100;
            progressBar.style.width = `${progress}%`;
            progressBar.textContent = `Step ${currentTab + 1} of ${formTabs.length}`;
            progressBar.setAttribute('aria-valuenow', progress);
        }

        // Admission status change handler
        admissionStatusRadios.forEach(radio => {
            if (radio.checked) {
                handleAdmissionStatusChange(radio.value);
            }

            radio.addEventListener('change', function() {
                handleAdmissionStatusChange(this.value);
            });
        });

        function handleAdmissionStatusChange(value) {
            if (transfereeFields) {
                transfereeFields.style.display = value === 'transferee' ? 'block' : 'none';
            }

            if (manualCourseSelection) {
                const showCourseSelection = (value === 'transferee' || value === 'returnee');
                manualCourseSelection.style.display = showCourseSelection ? 'block' : 'none';
            }
        }

        // Form submission handler
        document.getElementById('admissionForm').addEventListener('submit', function(e) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Submitting...';
            submitBtn.disabled = true;
        });

        function validateCurrentStep(step) {
            const fields = requiredFields[step];
            let isValid = true;
            let firstInvalidField = null;

            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    } else {
                        field.classList.remove('is-invalid');
                    }
                }
            });

            // Special validation for admission status in step 4
            if (step === 3) {
                const admissionStatusSelected = document.querySelector('input[name="admission_status"]:checked');
                if (!admissionStatusSelected && requiredFields[3].includes('admission_status')) {
                    isValid = false;
                    admissionStatusRadios.forEach(radio => {
                        radio.closest('.form-check').classList.add('is-invalid');
                    });
                } else {
                    admissionStatusRadios.forEach(radio => {
                        radio.closest('.form-check').classList.remove('is-invalid');
                    });
                }
            }

            if (!isValid) {
                alert('Please fill in all required fields before proceeding.');
                if (firstInvalidField) {
                    firstInvalidField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstInvalidField.focus();
                }
            }

            return isValid;
        }

        // Add input event listeners to clear validation errors when typing
        requiredFields.flat().forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            }
        });
    });
</script>
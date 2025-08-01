@extends('layouts.main')
<style>
    /* Style for the multi-select dropdown */
    #course_ids {
        min-height: 150px;
        padding: 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }

    #course_ids option {
        padding: 8px;
        margin: 2px 0;
        border-radius: 3px;
    }

    #course_ids option:hover {
        background-color: #f8f9fa;
    }

    #course_ids option:checked {
        background-color: #007bff;
        color: white;
    }

    /* Style for the selected courses list */
    #selectedCoursesList li {
        padding: 8px;
        background-color: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 5px;
    }

    .remove-course {
        padding: 0 5px;
        line-height: 1;
    }
</style>
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
                    <h1 class="h3 mb-0 text-gray-800">Existing Students</h1>


                    <button class="btn btn-primary" data-toggle="modal" data-target="#admissionFormModal">
                        Open Admission Form
                    </button>

                </div>
             <div class="modal fade" id="admissionFormModal" tabindex="-1" aria-labelledby="admissionFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('re_enroll_regular.store') }}" id="admissionForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Admission Form</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Progress Indicator -->
                    <div class="progress mb-4">
                        <div class="progress-bar" id="progressBar" role="progressbar" style="width: 50%;"
                            aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                            Step 1 of 2
                        </div>
                    </div>

                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" id="formTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="step1-tab" data-toggle="tab" href="#step1"
                                role="tab" aria-controls="step1" aria-selected="true">
                                <i class="fas fa-search mr-1"></i> Student Verification
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link disabled" id="step2-tab" data-toggle="tab" href="#step2"
                                role="tab" aria-controls="step2" aria-selected="false">
                                <i class="fas fa-graduation-cap mr-1"></i> Enrollment Details
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Step 1: Search for Student -->
                        <div class="tab-pane fade show active" id="step1" role="tabpanel"
                            aria-labelledby="step1-tab">
                            <div class="form-group">
                                <label for="student_search">Search Student (Name or Student ID) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <input type="text" id="student_search" name="student_search"
                                        class="form-control" placeholder="Enter student ID or name" autocomplete="off" />
                                </div>

                                <!-- Search results dropdown -->
                                <div id="searchResultsDropdown" class="dropdown-menu w-100" style="display: none; max-height: 300px; overflow-y: auto;">
                                    <!-- Search results will be inserted here -->
                                </div>

                                <!-- Loading indicator -->
                                <div id="searchLoading" class="text-center" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p>Searching student...</p>
                                </div>

                                <!-- Student info display -->
                                <div id="studentInfoContainer" style="display: none;">
                                    <div class="card mt-3">
                                        <div class="card-header bg-info text-white">
                                            <strong>Student Information</strong>
                                        </div>
                                        <div class="card-body">
                                            <div id="studentInfo"></div>
                                            <div id="enrollmentWarnings" class="mt-3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Enrollment Details -->
                        <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                            <input type="hidden" name="student_id" id="student_id_input">
                            
                            <!-- Student Type Selection -->
                            <div class="form-group mb-4">
                                <label>Student Type:</label>
                                <div class="form-check">
                                    <input class="form-check-input student-type" type="radio" name="student_type" id="regularType" value="regular" checked>
                                    <label class="form-check-label" for="regularType">Regular</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input student-type" type="radio" name="student_type" id="irregularType" value="irregular">
                                    <label class="form-check-label" for="irregularType">Irregular</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input student-type" type="radio" name="student_type" id="transfereeType" value="transferee">
                                    <label class="form-check-label" for="transfereeType">Transferee</label>
                                </div>
                            </div>

                            <!-- Regular Course Mapping (shown by default) -->
                            <div id="regularCourseMapping">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="course_mapping_id">Course Mapping <span class="text-danger">*</span></label>
                                        <select id="course_mapping_id" name="course_mapping_id" class="form-control" required>
                                            <option value="" selected disabled>Choose Mapping</option>
                                            @foreach ($courseMappings as $mapping)
                                                @if ($mapping->program && $mapping->yearLevel)
                                                    <option value="{{ $mapping->id }}"
                                                        data-program="{{ $mapping->program_id }}"
                                                        data-year="{{ $mapping->year_level_id }}"
                                                        data-semester="{{ $mapping->semester_id }}"
                                                        data-sy="{{ $mapping->effective_sy }}">
                                                        {{ $mapping->program->name }} -
                                                        {{ $mapping->yearLevel->name }}
                                                        ({{ $mapping->effective_sy }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        
                                        <div class="mt-2" style="display: none;">
                                            <small class="text-muted">Selected Mapping ID: <span id="displayMappingId">-</span></small>
                                        </div>
                                        
                                        <div id="totalUnitsContainer" class="alert alert-info mt-3 d-none" style="display: none;">
                                            Total Units: <strong id="totalUnitsValue"></strong>
                                        </div>
                                        
                                        <div id="tuitionFeeContainer" class="alert alert-success mt-2 d-none" style="display: none;">
                                            Tuition Fee: <strong id="tuitionFeeValue"></strong>
                                        </div>
                                        
                                        <div id="feeCalculationContainer" class="alert alert-secondary mt-2" style="display:none;">
                                            <small>Calculation: <span id="feeCalculationDetails"></span></small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="major">Major</label>
                                            <input type="text" id="major" name="major" class="form-control" placeholder="Enter major (if applicable)">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="scholarship">Scholarship</label>
                                            <select id="scholarship" name="scholarship" class="form-control">
                                                <option value="" selected disabled>Select Scholarship</option>
                                                @foreach ($scholarships as $scholarship)
                                                    <option value="{{ $scholarship->id }}">
                                                        {{ $scholarship->name }}
                                                        ({{ $scholarship->discount }}% Discount)
                                                    </option>
                                                @endforeach
                                                <option value="none">None</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Irregular/Transferee Course Selection (hidden by default) -->
                            <div id="irregularCourseSelection" style="display: none;">
                                <div class="card mb-3">
                                    <div class="card-header bg-primary text-white">
                                        <strong>Course Selection</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label>Search and Add Courses:</label>
                                            <input type="text" id="courseSearch" class="form-control" placeholder="Search courses...">
                                            <div id="courseSearchResults" class="list-group mt-1" style="display: none;"></div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label>Selected Courses:</label>
                                            <ul id="selectedCoursesList" class="list-group"></ul>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Transferee Fields (hidden by default) -->
                                <div id="transfereeFields" style="display: none;">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <strong>Transferee Information</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="previous_school">Previous School</label>
                                                <input type="text" id="previous_school" name="previous_school" class="form-control" placeholder="Enter previous school name">
                                            </div>
                                            <div class="form-group">
                                                <label for="reason_for_transfer">Reason for Transfer</label>
                                                <textarea id="reason_for_transfer" name="reason_for_transfer" class="form-control" rows="3" placeholder="Enter reason for transfer"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- LRN Field (hidden by default) -->
                                <div id="lrnField" style="display: none;">
                                    <div class="form-group">
                                        <label for="lrn">LRN (Learner Reference Number)</label>
                                        <input type="text" id="lrn" name="lrn" class="form-control" placeholder="Enter LRN">
                                    </div>
                                </div>
                                
                                <!-- Tuition Calculation -->
                                <div id="irregularTuitionDisplay" style="display: none;">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <strong>Tuition Calculation</strong>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p>Total Units: <span id="total_units_display">0</span></p>
                                                    <p>Total Tuition Fee: ₱<span id="tuition_fee_display">0.00</span></p>
                                                    <input type="hidden" id="tuition_fee_input" name="tuition_fee">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Miscellaneous Fees (hidden by default) -->
                              <div id="miscFeesSection" style="display: none;">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <strong>Miscellaneous Fees</strong>
                                        </div>
                                        <div class="card-body">
                                            <!-- Add this new row for the input fields -->
                                            <div class="row mb-3">
                                                <div class="col-md-5">
                                                    <input type="text" id="newMiscFeeName" class="form-control" placeholder="Fee Name">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" id="newMiscFeeAmount" class="form-control" placeholder="Amount" min="0" step="0.01">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" id="addMiscFeeBtn" class="btn btn-primary w-100">Add</button>
                                                </div>
                                            </div>
                                            
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Fee Name</th>
                                                        <th>Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="miscFeesList"></tbody>
                                            </table>
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <div>
                                                    <strong>Total Misc Fees: ₱<span id="misc_fees_total">0.00</span></strong>
                                                    <input type="hidden" id="misc_fees_input" name="misc_fees_total" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="prevBtn" style="display: none;">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </button>
                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                        <i class="fas fa-check mr-1"></i> Submit Enrollment
                    </button>
                    <button type="button" class="btn btn-primary" id="nextBtn" disabled>
                        Continue <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Close
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .search-result-item {
        padding: 8px 16px;
        cursor: pointer;
    }

    .search-result-item:hover {
        background-color: #f8f9fa;
    }

    #searchResultsDropdown {
        position: absolute;
        z-index: 1000;
        background: white;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        margin-top: -3px;
    }

    .dropdown-item {
        white-space: normal;
        word-wrap: break-word;
    }
</style>


                <div class="row justify-content-center mt-3">
                    <div class="col-md-12">
                        <div class="card shadow mb-4">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="miscFees">
                                        <thead>
                                            <tr>
                                                <th>Student No.</th>
                                                <th>Full Name</th>
                                                <th>Program</th>

                                                <th>Admission Status</th>
                                                <th>Email</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($admissions as $admission)
                                                <tr>
                                                    <td>{{ strtoupper($admission->student_id) }}</td>
                                                    <td>{{ strtoupper($admission->admission->full_name ?? 'N/A') }}</td>
                                                    <td>{{ $admission->courseMapping->combination_label ?? 'N/A' }}</td>
                                                    <td>{{ ucfirst($admission->status) }}</td>
                                                    <td>{{ $admission->admission->email ?? 'N/A' }}</td>
                                                  <td>
    @php
        $initialPayment = $admission->billing->initial_payment ?? 0;
        $studentId = $admission->student_id;
    @endphp

    <input 
        type="hidden" 
        name="initial_payment" 
        id="initial_payment{{ $admission->student_id }}"
        value="{{ old('initial_payment', ($billing->initial_payment ?? 0) > 0 ? $billing->initial_payment : '') }}">

    <!-- Button Row -->
    <div class="d-flex align-items-center gap-2 mt-2">
        <!-- View Button -->
        <div class="position-relative">
            <button type="button"
                class="btn btn-info btn-sm position-relative"
                data-bs-toggle="modal"
                data-bs-target="#studentModal{{ $studentId }}"
                id="viewBtn{{ $studentId }}"
                title="View Student">
                <i class="fas fa-eye"></i>

                <!-- Warning badge -->
                <span
                    id="warning-badge-{{ $studentId }}"
                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark d-none"
                    data-bs-toggle="tooltip"
                    title="This student hasn't made an initial payment yet.">
                    Warning
                </span>
            </button>
        </div>

        <!-- Edit Button -->
        <a href="{{ route('admissions.edit', $admission->student_id) }}"
           class="btn btn-warning btn-sm"
           title="Edit Student Details">
            <i class="fas fa-edit"></i>
        </a>

        <!-- Print Button -->
        <a href="{{ route('admissions.printCOR', $admission->student_id) }}"
           target="_blank" rel="noopener" class="btn btn-primary btn-sm"
           title="Print COR">
           <i class="fas fa-print"></i>
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            const input = document.getElementById('initial_payment{{ $studentId }}');
            const warningBadge = document.getElementById('warning-badge-{{ $studentId }}');

            function checkInitialPayment() {
                const value = parseFloat(input.value);
                if (isNaN(value) || value <= 0) {
                    warningBadge.classList.remove('d-none');
                }
            }

            checkInitialPayment();
            input.addEventListener('input', checkInitialPayment);
        });
    </script>
</td>

                                                </tr>
                                             <div class="modal fade" id="studentModal{{ $admission->student_id }}" tabindex="-1"
    aria-labelledby="studentModalLabel{{ $admission->student_id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="studentModalLabel{{ $admission->student_id }}">
                    <i class="bi bi-person-vcard me-2"></i>Student Billing Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            @php
            
                // Get current active school year
                $activeSchoolYear = \App\Models\SchoolYear::where('is_active', 1)->first();
                
                // Get billing information for this student in the active school year
                $billing = \App\Models\Billing::where('student_id', $admission->student_id)
                    ->where('school_year', $activeSchoolYear->name ?? null)
                    ->where('semester', $activeSchoolYear->semester ?? null)
                    ->first();
            @endphp

            @if($billing)
            <form method="POST" action="{{ route('billing.updateInitialPayment', $billing->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <!-- Basic Information Section -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Student ID:</strong></p>
                                    <p class="text-dark">{{ strtoupper($admission->student_id) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Full Name:</strong></p>
                                    <p class="text-dark">{{ strtoupper($admission->admission->full_name ?? 'N/A') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Program:</strong></p>
                                    <p class="text-dark">{{ $admission->courseMapping->combination_label ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Admission Status:</strong></p>
                                    <p class="text-dark">{{ ucfirst($admission->status) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing Information Section -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-cash-coin me-2"></i>Financial Information
                        </h6>

                        <!-- School Year and Semester -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>School Year:</strong></p>
                                    <p class="text-dark">{{ $activeSchoolYear->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Semester:</strong></p>
                                    <p class="text-dark">{{ $activeSchoolYear->semester ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Fee Breakdown -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Tuition Fee:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->tuition_fee, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Discount:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->discount, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Tuition After Discount:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->tuition_fee_discount, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Miscellaneous Fee:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->misc_fee, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Old Accounts Balance:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->old_accounts, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Assessment and Balance Due -->
                        <div class="alert alert-primary mt-3 mb-4">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="p-2">
                                        <p class="mb-1"><strong>Total Assessment:</strong></p>
                                        <h5 class="fw-bold mb-0">
                                            ₱{{ number_format($billing->total_assessment, 2) }}
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2">
                                        <p class="mb-1"><strong>Balance Due:</strong></p>
                                        <h5 class="fw-bold mb-0">
                                            ₱{{ number_format($billing->balance_due, 2) }}
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <h6 class="fw-bold mt-4 mb-3">
                            <i class="bi bi-calendar-check me-2"></i>Payment Information
                        </h6>
                        
                        <div class="row g-3">
                            <!-- Manual Old Balance Input -->
<div class="col-12">
    <div class="mb-3">
        <label for="manual_old_balance{{ $admission->student_id }}" class="form-label">
            <strong>Manual Old Balance</strong> 
            <small class="text-muted">(Optional)</small>
        </label>
        <input 
            placeholder="0.00" 
            type="number" 
            name="manual_old_balance" 
            step="0.01" 
            min="0"
            class="form-control"
            id="manual_old_balance{{ $admission->student_id }}"
            value="{{ old('manual_old_balance') }}">
        <small class="text-muted">Enter a value to manually set old balance. Leave empty for no change.</small>
    </div>
</div>
<input type="hidden" name="current_old_accounts" value="{{ $billing->old_accounts }}">


                            <!-- Initial Paysment Input -->
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="initial_payment{{ $admission->student_id }}" class="form-label"><strong>Initial Payment</strong></label>
                                    <input 
                                        placeholder="0.00" 
                                        type="number" 
                                        name="initial_payment" 
                                        step="0.01" 
                                        min="0"
                                        class="form-control"
                                        id="initial_payment{{ $admission->student_id }}"
                                        value="{{ old('initial_payment', ($billing->initial_payment ?? 0) > 0 ? $billing->initial_payment : '') }}">
                                </div>
                            </div>

                            <!-- Payment Schedule -->
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Prelims Due:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->prelims_due, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Midterms Due:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->midterms_due, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Pre-Finals Due:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->prefinals_due, 2) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Finals Due:</strong></p>
                                    <p class="text-dark">₱{{ number_format($billing->finals_due, 2) }}</p>
                                </div>
                            </div>

                            <!-- Payment Status -->
                            <div class="col-12">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Payment Status:</strong></p>
                                    <p>
                                        @if ($billing->is_full_payment)
                                            <span class="badge bg-success p-2">Fully Paid</span>
                                        @elseif ($billing->initial_payment > 0)
                                            <span class="badge bg-warning p-2">Partial Payment</span>
                                        @else
                                            <span class="badge bg-secondary p-2">No Payment Yet</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                <div class="modal-body p-4">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        No billing information found for the current active school year ({{ $activeSchoolYear->name ?? 'N/A' }} - {{ $activeSchoolYear->semester ?? 'N/A' }}).
                    </div>
                    
                    <!-- At least show basic student info -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="bi bi-info-circle me-2"></i>Basic Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Student ID:</strong></p>
                                    <p class="text-dark">{{ strtoupper($admission->student_id) }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Full Name:</strong></p>
                                    <p class="text-dark">{{ strtoupper($admission->admission->full_name ?? 'N/A') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Close
                    </button>
                </div>
            @endif
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


             
 <script>
    $(document).ready(function() {
    let searchTimeout;
    let selectedCourses = [];

    // ========== STUDENT SEARCH FUNCTIONALITY ==========
    $('#student_search').on('input', function() {
        clearTimeout(searchTimeout);
        $('#searchResultsDropdown').hide().empty();

        let query = $(this).val().trim();
        if (query.length < 2) return;

        $('#searchLoading').show();

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route('search.student') }}',
                method: 'GET',
                data: { query: query },
                success: function(data) {
                    $('#searchLoading').hide();
                    displaySearchResults(data);
                },
                error: function(xhr) {
                    $('#searchLoading').hide();
                    console.error('Search error:', xhr.responseText);
                }
            });
        }, 300);
    });

    function displaySearchResults(data) {
        const dropdown = $('#searchResultsDropdown');
        dropdown.empty();

        if (data.length === 0) {
            dropdown.append('<div class="dropdown-item">No students found</div>');
        } else {
            data.forEach(studentObj => {
                const student = studentObj.student;

                const item = $('<div>', {
                    class: 'dropdown-item search-result-item',
                    'data-student-id': student.student_id
                })
                .text(`${student.student_id} - ${student.first_name} ${student.last_name}`)
                .data('studentObj', studentObj);

                dropdown.append(item);
            });
        }

        dropdown.show();
    }

    $(document).on('click', '.search-result-item', function() {
        const studentObj = $(this).data('studentObj');
        const student = studentObj.student;

        $('#student_search').val(`${student.student_id} - ${student.first_name} ${student.last_name}`);
        $('#searchResultsDropdown').hide();
        $('#student_id_input').val(student.student_id);

        let html = `
            <p><strong>Student ID:</strong> ${student.student_id}</p>
            <p><strong>Name:</strong> ${student.first_name} ${student.middle_name || ''} ${student.last_name}</p>
        `;
        $('#studentInfo').html(html);

        let warnings = [];
        let canProceed = true;

        if (studentObj.already_enrolled) {
            warnings.push('<div class="alert alert-danger">Student is already enrolled for the current term.</div>');
            canProceed = false;
        }

        if (studentObj.has_failing_grades) {
            warnings.push('<div class="alert alert-danger">Student has failing grades.</div>');
            canProceed = false;
        }

        $('#enrollmentWarnings').html(warnings.join(''));
        $('#nextBtn').prop('disabled', !canProceed);
        $('#studentInfoContainer').show();
    });

    // ========== STUDENT TYPE HANDLING ==========
    $('.student-type').change(function() {
        const status = $(this).val();
        
        if (status === 'regular') {
            $('#regularCourseMapping').show();
            $('#irregularCourseSelection').hide();
            $('#transfereeFields').hide();
            $('#lrnField').show();
            $('#irregularTuitionDisplay').hide();
            $('#miscFeesSection').hide();
            $('#admissionForm').attr('action', '{{ route("re_enroll_regular.store") }}');
            $('#selectedCoursesList').empty();
        } else if (status === 'transferee') {
            $('#regularCourseMapping').show();
            $('#irregularCourseSelection').show();
            $('#transfereeFields').show();
            $('#lrnField').hide();
            $('#irregularTuitionDisplay').show();
            $('#miscFeesSection').show();
            $('#admissionForm').attr('action', '{{ route("admissions.store.transferee") }}');
            $('#selectedCoursesList').empty();
        } else if (status === 'irregular') {
            $('#regularCourseMapping').show();
            $('#irregularCourseSelection').show();
            $('#transfereeFields').hide();
            $('#lrnField').hide();
            $('#irregularTuitionDisplay').show();
            $('#miscFeesSection').show();
            $('#admissionForm').attr('action', '{{ route("re_enroll_irregular.store") }}');
            $('#selectedCoursesList').empty();

            if ($('#course_mapping_id').val()) {
                loadMappingCourses($('#course_mapping_id').val());
            }
        }
    });

    // ========== COURSE MAPPING HANDLING ==========
    $('#course_mapping_id').on('change', function() {
        const status = $('.student-type:checked').val();
        const mappingId = $(this).val();
        
        if ((status === 'irregular' || status === 'transferee') && mappingId) {
            loadMappingCourses(mappingId);
        } else {
            calculateRegularTuition(mappingId);
        }
    });

    // ========== COURSE LOADING ==========
    function loadMappingCourses(mappingId) {
        $.ajax({
            url: '{{ route("getMappingCourses") }}',
            type: 'POST',
            data: {
                mapping_id: mappingId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#selectedCoursesList').empty();
                $('#miscFeesList').empty();
                
                if (response.courses && response.courses.length > 0) {
                    response.courses.forEach(course => {
                        addCourseToSelection(
                            course.id, 
                            course.code, 
                            course.name, 
                            course.units,
                            false
                        );
                    });
                }
                
                if (response.misc_fees && response.misc_fees.length > 0) {
                    response.misc_fees.forEach(fee => {
                        addMiscFee(fee.id, fee.name, fee.amount, fee.is_required);
                    });
                }
                
                updateSelectedCoursesInput();
                recalculateIrregularTuition();
            }
        });
    }


   // ========== MISCELLANEOUS FEES HANDLING ==========
function addMiscFee(id, name, amount, isRequired) {
    const rowId = 'misc_fee_row_' + id;
    
    if ($('#' + rowId).length > 0) return;
    
    const row = `
    <tr id="${rowId}">
        <td>${name}</td>
        <td>
            <input type="number" class="form-control misc-fee-amount" 
                   name="misc_fees[${id}][amount]" 
                   value="${amount}" ${isRequired ? 'readonly' : ''}>
            <input type="hidden" name="misc_fees[${id}][name]" value="${name}">
            <input type="hidden" name="misc_fees[${id}][is_required]" value="${isRequired ? 1 : 0}">
        </td>
        <td>
            ${isRequired ? 
                '<span class="badge bg-primary">Required</span>' : 
                '<button type="button" class="btn btn-sm btn-danger remove-misc-fee">Remove</button>'}
        </td>
    </tr>
    `;
    
    $('#miscFeesList').append(row);
    updateMiscFeesTotal();
}

// Add new misc fee
$('#addMiscFeeBtn').click(function() {
    const name = $('#newMiscFeeName').val().trim();
    const amount = parseFloat($('#newMiscFeeAmount').val());
    
    if (!name || isNaN(amount)) {
        Swal.fire({
            title: 'Error',
            text: 'Please enter both fee name and amount',
            icon: 'error'
        });
        return;
    }
    
    const tempId = 'new_' + Date.now();
    addMiscFee(tempId, name, amount, false);
    
    // Clear the input fields
    $('#newMiscFeeName').val('');
    $('#newMiscFeeAmount').val('');
});

// Remove misc fee
$(document).on('click', '.remove-misc-fee', function() {
    $(this).closest('tr').remove();
    updateMiscFeesTotal();
});

// Update misc fees total
function updateMiscFeesTotal() {
    let total = 0;
    $('.misc-fee-amount').each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    $('#misc_fees_total').text(total.toFixed(2));
    $('#misc_fees_input').val(total);
    recalculateIrregularTuition();
}

// Misc fee amount change handler
$(document).on('change', '.misc-fee-amount', function() {
    updateMiscFeesTotal();
});

    // ========== COURSE SEARCH AND SELECTION ==========
    $('#courseSearch').on('input', function() {
        const searchTerm = $(this).val().trim();
        if (searchTerm.length >= 1) {
            $.ajax({
                url: '{{ route("courses.search") }}',
                method: 'GET',
                data: { 
                    query: searchTerm,
                    with_prerequisites: true
                },
                success: function(response) {
                    const resultsContainer = $('#courseSearchResults');
                    resultsContainer.empty();

                    if (response.length > 0) {
                        response.forEach(course => {
                            const prereqBadge = course.has_prerequisites 
                                ? '<span class="badge bg-warning float-end">Has Prereq</span>' 
                                : '';
                            resultsContainer.append(`
                                <a href="#" class="list-group-item list-group-item-action course-item" 
                                   data-id="${course.id}" 
                                   data-code="${course.code}" 
                                   data-title="${course.title}" 
                                   data-units="${course.units}"
                                   data-has-prereq="${course.has_prerequisites}">
                                    ${course.code} - ${course.title} (${course.units} units)
                                    ${prereqBadge}
                                </a>
                            `);
                        });
                        resultsContainer.show();
                    } else {
                        resultsContainer.hide();
                    }
                }
            });
        } else {
            $('#courseSearchResults').hide();
        }
    });

    // Add course handler
    $(document).on('click', '.course-item', function(e) {
        e.preventDefault();
        const courseId = $(this).data('id');
        const courseCode = $(this).data('code');
        const courseName = $(this).data('title');
        const courseUnits = $(this).data('units');
        const hasPrereq = $(this).data('has-prereq') == 1;

        if ($(`#selectedCourse_${courseId}`).length > 0) {
            Swal.fire({
                title: 'Course Already Added',
                text: 'This course is already in the selection list.',
                icon: 'info'
            });
            return;
        }

        if (hasPrereq) {
            $.ajax({
                url: '{{ route("courses.prerequisites") }}',
                method: 'POST',
                data: {
                    course_id: courseId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const prereqIds = response.prerequisites.map(pr => pr.id);
                    const selectedIds = $('#selectedCoursesList li').map(function() {
                        return $(this).data('id');
                    }).get();

                    const missingPrereqs = response.prerequisites.filter(pr => !selectedIds.includes(pr.id));

                    if (missingPrereqs.length === 0) {
                        addCourseToSelection(courseId, courseCode, courseName, courseUnits, false);
                    } else {
                        const missingList = missingPrereqs.map(pr => `<li>${pr.code} - ${pr.name}</li>`).join('');
                        Swal.fire({
                            title: 'Missing Prerequisites',
                            html: `<p><strong>${courseCode}</strong> requires the following prerequisites:</p>
                                   <ul>${missingList}</ul>
                                   <p>Do you want to override and add this course anyway?</p>`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, override',
                            cancelButtonText: 'Cancel',
                            customClass: {
                                confirmButton: 'btn btn-primary',
                                cancelButton: 'btn btn-secondary'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                addCourseToSelection(courseId, courseCode, courseName, courseUnits, true);
                            }
                        });
                    }
                }
            });
        } else {
            addCourseToSelection(courseId, courseCode, courseName, courseUnits, false);
        }
    });

    // Add course to selection list
    function addCourseToSelection(courseId, code, title, units, isOverridden) {
        $('#selectedCoursesList').append(`
            <li class="list-group-item d-flex justify-content-between align-items-center selected-course" 
                id="selectedCourse_${courseId}" 
                data-id="${courseId}">
                <div>
                    ${code} - ${title} (${units} units)
                    ${isOverridden ? '<div class="text-warning mt-1"><small>Prerequisite overridden</small></div>' : ''}
                </div>
                <button type="button" class="btn btn-sm btn-danger remove-course" data-id="${courseId}">
                    <i class="fas fa-times"></i>
                </button>
                <input type="hidden" name="courses[${courseId}][course_id]" value="${courseId}">
                <input type="hidden" name="courses[${courseId}][override_prereq]" value="${isOverridden ? 1 : 0}">
            </li>
        `);
        updateSelectedCoursesInput();
        $('#courseSearch').val('');
        $('#courseSearchResults').hide();
        recalculateIrregularTuition();
    }

    // Remove course handler
    $(document).on('click', '.remove-course', function() {
        const courseId = $(this).data('id');
        $(`#selectedCourse_${courseId}`).remove();
        updateSelectedCoursesInput();
        recalculateIrregularTuition();
    });

    // Update selected courses input
    function updateSelectedCoursesInput() {
        const selectedCourses = [];
        $('#selectedCoursesList li').each(function() {
            const courseId = $(this).find('input[name*="[course_id]"]').val();
            const override = $(this).find('input[name*="[override_prereq]"]').val();
            selectedCourses.push({ id: courseId, override_prereq: override });
        });
        $('#selected_courses_input').val(JSON.stringify(selectedCourses));
    }

    // ========== TUITION CALCULATION ==========
    // Regular tuition calculation
    function calculateRegularTuition(mappingId) {
        if (!mappingId) {
            $('#totalUnitsContainer').hide();
            $('#tuitionFeeContainer').hide();
            return;
        }

        $.ajax({
            url: '{{ route("getMappingUnits") }}',
            type: 'POST',
            data: {
                mapping_id: mappingId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                let totalUnits = response.total_units;
                let tuitionFee = response.tuition_fee;

                // Adjust for NSTP deductions
                if (response.courses && Array.isArray(response.courses)) {
                    let nstpUnitsToDeduct = 0;

                    response.courses.forEach(course => {
                        let rawName = `${course.code} ${course.name} ${course.description}`.toLowerCase();
                        rawName = rawName.replace(/- ?[a-z0-9 &()]+/g, '').trim();
                        const isNSTP = rawName.includes('national service training program') ||
                                       rawName.includes('civic welfare training service') ||
                                       rawName.includes('lts/cwts/rotc') ||
                                       rawName.includes('lts/rotc');

                        if (isNSTP) {
                            let units = parseFloat(course.units);
                            if (!isNaN(units)) {
                                nstpUnitsToDeduct += units / 2;
                            }
                        }
                    });

                    totalUnits -= nstpUnitsToDeduct;
                    let tuitionPerUnit = tuitionFee / response.total_units;
                    tuitionFee = totalUnits * tuitionPerUnit;
                }

                $('#totalUnitsValue').text(totalUnits.toFixed(2));
                $('#totalUnitsContainer').show();
                $('#tuitionFeeContainer').html('Tuition Fee: <strong>₱' + tuitionFee.toFixed(2) + '</strong>').show();
                $('#tuition_fee_input').val(tuitionFee);
            },
            error: function() {
                $('#totalUnitsContainer').hide();
                $('#tuitionFeeContainer').hide();
            }
        });
    }

    // Irregular tuition calculation
    function recalculateIrregularTuition() {
        const selectedCourses = [...document.querySelectorAll('.selected-course')];
        const courseIds = selectedCourses.map(el => el.dataset.id);
        const miscFeesTotal = parseFloat($('#misc_fees_input').val()) || 0;

        if (courseIds.length === 0) {
            $('#total_units_display').text('0');
            $('#tuition_fee_display').text(miscFeesTotal.toFixed(2));
            $('#tuition_fee_input').val(miscFeesTotal);
            return;
        }

        fetch('{{ route("calculate.irregular.tuition") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                course_ids: courseIds,
                misc_fees: miscFeesTotal
            })
        })
        .then(response => response.json())
        .then(data => {
            $('#total_units_display').text(data.total_units);
            $('#tuition_fee_display').text((parseFloat(data.tuition_fee) + miscFeesTotal).toFixed(2));
            $('#tuition_fee_input').val(parseFloat(data.tuition_fee) + miscFeesTotal);
        })
        .catch(error => {
            console.error('Error calculating tuition:', error);
        });
    }

    // ========== FORM NAVIGATION ==========
    $('#nextBtn').click(function() {
        const currentTab = $('.tab-pane.active');
        const nextTab = currentTab.next('.tab-pane');

        if (currentTab.attr('id') === 'step1') {
            if (!$('#student_id_input').val()) {
                alert('Please search and select a valid student first.');
                return;
            }

            currentTab.removeClass('show active');
            nextTab.addClass('show active');
            $('#step1-tab').removeClass('active').addClass('disabled');
            $('#step2-tab').addClass('active').removeClass('disabled');

            $('#progressBar').css('width', '100%').text('Step 2 of 2');
            $('#prevBtn').show();
            $('#nextBtn').hide();
            $('#submitBtn').show();
        }
    });

    $('#prevBtn').click(function() {
        const currentTab = $('.tab-pane.active');
        const prevTab = currentTab.prev('.tab-pane');

        currentTab.removeClass('show active');
        prevTab.addClass('show active');
        $('#step2-tab').removeClass('active').addClass('disabled');
        $('#step1-tab').addClass('active').removeClass('disabled');

        $('#progressBar').css('width', '50%').text('Step 1 of 2');
        $('#prevBtn').hide();
        $('#submitBtn').hide();
        $('#nextBtn').show();
    });

    $('#admissionForm').submit(function(e) {
        if (!$('#course_mapping_id').val()) {
            e.preventDefault();
            alert('Please select a course mapping before submitting.');
            return false;
        }
        return true;
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#student_search, #searchResultsDropdown').length) {
            $('#searchResultsDropdown').hide();
        }
    });
});
 </script>





            </div>


        </div>
        <!-- End of Main Content -->

        @include('layouts.footer')


    </div>
    <!-- End of Constent Wrapper -->
@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- REQUIRED for DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        $('#miscFees').DataTable({
            responsive: true,
            pageLength: 10
        });
    });
</script>



<script>
    document.getElementById('course_mapping_id').addEventListener('change', function() {
        const mappingId = this.value;

        if (!mappingId) {
            document.getElementById('tuition_fee').value = '';
            return;
        }

        fetch('{{ route('calculate.tuition.fee') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    course_mapping_id: mappingId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.tuition_fee !== undefined) {
                    document.getElementById('tuition_fee').value = data.tuition_fee;
                } else {
                    document.getElementById('tuition_fee').value = '';
                    console.error(data.error);
                }
            })
            .catch(error => {
                console.error('Error fetching tuition fee:', error);
            });
    });
</script>

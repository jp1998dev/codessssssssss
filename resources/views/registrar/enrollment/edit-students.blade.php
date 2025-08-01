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

@section('tab_title', 'Edit')
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
                <h1 class="h3 mb-0 text-gray-800">Edit Student Info</h1>

                <button class="btn btn-primary" data-toggle="modal" data-target="#admissionFormModal">
                    Download Report
                </button>
            </div>

            <!--  Edit Admisssion Form -->
            <form action="{{ route('admissions.update', $admission->student_id) }}" method="POST"
                class="needs-validation" novalidate>
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-12">
                        <!-- Form Navigation Tabs -->
                        <ul class="nav nav-tabs mb-4" id="admissionTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal"
                                    role="tab">Personal Info</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="education-tab" data-toggle="tab" href="#academic"
                                    role="tab">Acadamic</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="education-tab" data-toggle="tab" href="#education"
                                    role="tab">Education</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="family-tab" data-toggle="tab" href="#family"
                                    role="tab">Family</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="admissionTabsContent">
                            <!-- Personal Information Tab -->
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Personal Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="last_name" class="font-weight-bold">Last Name <span
                                                            class="text-danger">*</span></label>
                                                    <input value="{{ $admission->last_name }}" type="text"
                                                        name="last_name" id="last_name" class="form-control" required>
                                                    <div class="invalid-feedback">Please provide last name.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="first_name" class="font-weight-bold">First Name <span
                                                            class="text-danger">*</span></label>
                                                    <input value="{{ $admission->first_name }}" type="text"
                                                        name="first_name" id="first_name" class="form-control" required>
                                                    <div class="invalid-feedback">Please provide first name.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="middle_name">Middle Name</label>
                                                    <input value="{{ $admission->middle_name }}" type="text"
                                                        name="middle_name" id="middle_name" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="birthdate" class="font-weight-bold">Birthdate</label>
                                                    <input value="{{ $admission->birthdate }}" type="date"
                                                        name="birthdate" id="birthdate" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="birthplace">Birthplace</label>
                                                    <input value="{{ $admission->birthplace }}" type="text"
                                                        name="birthplace" id="birthplace" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="gender" class="font-weight-bold">Gender <span
                                                            class="text-danger">*</span></label>
                                                    <select name="gender" id="gender" class="form-control"
                                                        required>
                                                        <option value="">Select Gender</option>
                                                        <option value="Male"
                                                            {{ $admission->gender == 'Male' ? 'selected' : '' }}>Male
                                                        </option>
                                                        <option value="Female"
                                                            {{ $admission->gender == 'Female' ? 'selected' : '' }}>
                                                            Female</option>
                                                    </select>
                                                    <div class="invalid-feedback">Please select gender.</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="citizenship">Citizenship</label>
                                                    <input value="{{ $admission->citizenship }}" type="text"
                                                        name="citizenship" id="citizenship" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="religion">Religion</label>
                                                    <input value="{{ $admission->religion }}" type="text"
                                                        name="religion" id="religion" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="civil_status">Civil Status</label>
                                                    <input value="{{ $admission->civil_status }}" type="text"
                                                        name="civil_status" id="civil_status" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="contact_number" class="font-weight-bold">Contact
                                                        Number <span class="text-danger">*</span></label>
                                                    <input value="{{ $admission->contact_number }}" type="text"
                                                        name="contact_number" id="contact_number"
                                                        class="form-control" required>
                                                    <div class="invalid-feedback">Please provide contact number.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="email" class="font-weight-bold">Email <span
                                                            class="text-danger">*</span></label>
                                                    <input value="{{ $admission->email }}" type="email"
                                                        name="email" id="email" class="form-control" required>
                                                    <div class="invalid-feedback">Please provide valid email.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="academic" role="tabpanel">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Academic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Admission Status Radio Buttons -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <label class="form-label font-weight-bold">Admission Status <span class="text-danger">*</span></label>
                                                <div class="form-check">
                                                    <input class="form-check-input admission-status" type="radio" name="admission_status"
                                                        value="regular" id="regular" {{ $admission->admission_status === 'regular' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="regular">Regular Student</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input admission-status" type="radio" name="admission_status"
                                                        value="transferee" id="transferee" {{ $admission->admission_status === 'transferee' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="transferee">Transferee</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input admission-status" type="radio" name="admission_status"
                                                        value="irregular" id="irregular" {{ $admission->admission_status === 'irregular' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="irregular">Irregular</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Transferee Fields (hidden by default) -->
                                        <div class="row g-3 mt-2" id="transfereeFields" style="display: {{ $admission->admission_status === 'transferee' ? 'block' : 'none' }};">
                                            <div class="col-md-6">
                                                <label for="student_no">Student No. (if transferee)</label>
                                                <input type="text" id="student_no" name="student_no" class="form-control"
                                                    value="{{ $admission->student_no }}" placeholder="e.g., 2020-12345">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="admission_year">Year When Admitted</label>
                                                <input type="text" id="admission_year" name="admission_year" class="form-control"
                                                    value="{{ $admission->admission_year }}" placeholder="e.g., 2023">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="course_mapping_id" class="font-weight-bold">Program Mapping</label>
                                                    <select name="course_mapping_id" id="course_mapping_id" class="form-control">
                                                        <option value="">Select Program Mapping</option>
                                                        @foreach ($courseMappings as $groupKey => $mappings)
                                                        @php
                                                        $firstMapping = $mappings->first();
                                                        $programName = $firstMapping->program->name ?? 'N/A';
                                                        $yearLevel = $firstMapping->yearLevel->name ?? 'N/A';
                                                        $semester = $firstMapping->semester->name ?? 'N/A';
                                                        $effectiveSy = $firstMapping->effective_sy;
                                                        $mappingId = $firstMapping->id;
                                                        @endphp

                                                        <option value="{{ $mappingId }}"
                                                            {{ $admission->course_mapping_id == $mappingId ? 'selected' : '' }}>
                                                            {{ $programName }} - Year {{ $yearLevel }} -
                                                            {{ $semester }} Semester ({{ $effectiveSy }})
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <!-- Hidden field for current tuition fee -->
                                                    <input type="hidden" id="tuition_fee_input" name="tuition_fee" value="{{ $currentTuitionFee }}">

                                                    <div class="form-group mt-3">
                                                        <label for="scholarship_id" class="font-weight-bold">Scholarship</label>
                                                        <select name="scholarship" id="scholarship" class="form-control">
                                                            <option value="">No Scholarship</option>
                                                            @foreach ($scholarships as $scholarship)
                                                            <option value="{{ $scholarship->id }}"
                                                                {{ $admission->scholarship_id == $scholarship->id ? 'selected' : '' }}>
                                                                {{ $scholarship->name }}
                                                                ({{ $scholarship->discount }}%)
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div id="totalUnitsContainer" class="alert alert-info mt-3"
                                                        style="display:{{ $admission->admission_status === 'regular' ? 'block' : 'none' }};">
                                                        Total Units for Selected Mapping: <strong id="totalUnitsValue"></strong>
                                                    </div>
                                                    <div id="tuitionFeeContainer" class="alert alert-success mt-2"
                                                        style="display:{{ $admission->admission_status === 'regular' ? 'block' : 'none' }};"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Irregular/Transferee Course Selection (hidden by default) -->
                                        <div class="row" id="irregularCourseSelection" style="display: {{ in_array($admission->admission_status, ['irregular', 'transferee']) ? 'block' : 'none' }};">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="courseSearch">Search Courses</label>
                                                        <input type="text" id="courseSearch" class="form-control" placeholder="Start typing to search courses...">
                                                        <div id="courseSearchResults" class="list-group mt-2" style="display:none; max-height: 200px; overflow-y: auto;"></div>
                                                    </div>
                                                </div>

                                                <div class="row mt-3">
                                                    <div class="col-md-12">
                                                        <h5>Selected Courses:</h5>
                                                        <div id="selectedCoursesContainer">
                                                            <ul id="selectedCoursesList" class="list-group">
                                                                @if(in_array($admission->admission_status, ['irregular', 'transferee']) && $admission->courses)
                                                                @foreach($admission->courses as $course)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center selected-course"
                                                                    id="selectedCourse_{{ $course->id }}"
                                                                    data-id="{{ $course->id }}">
                                                                    <div>
                                                                        {{ $course->code }} - {{ $course->name }} ({{ $course->units }} units)
                                                                        @if($course->pivot->override_prereq)
                                                                        <div class="text-warning mt-1"><small>Prerequisite overridden</small></div>
                                                                        @endif
                                                                    </div>
                                                                    <button type="button" class="btn btn-sm btn-danger remove-course" data-id="{{ $course->id }}">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                    <input type="hidden" name="courses[{{ $course->id }}][course_id]" value="{{ $course->id }}">
                                                                    <input type="hidden" name="courses[{{ $course->id }}][override_prereq]" value="{{ $course->pivot->override_prereq ? 1 : 0 }}">
                                                                </li>
                                                                @endforeach
                                                                @endif
                                                            </ul>
                                                            <input type="hidden" id="selected_courses_input" name="selected_courses"
                                                                value="{{ (in_array($admission->admission_status, ['irregular', 'transferee']) && $admission->courses) ? $admission->courses->toJson() : '[]' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3" id="irregularTuitionDisplay" style="display: {{ in_array($admission->admission_status, ['irregular', 'transferee']) ? 'block' : 'none' }};">
                                            <p>Total Units: <span id="total_units_display">
                                                    @if(in_array($admission->admission_status, ['irregular', 'transferee']) && $admission->courses)
                                                    {{ $admission->courses->sum('units') }}
                                                    @else
                                                    0
                                                    @endif
                                                </span></p>
                                            <p>Total Tuition Fee: ₱<span id="tuition_fee_display">
                                                    @if(in_array($admission->admission_status, ['irregular', 'transferee']))
                                                    {{ number_format($currentTuitionFee, 2) }}
                                                    @else
                                                    0.00
                                                    @endif
                                                </span></p>
                                        </div>

                                        <!-- Misc Fees Section -->
                                        <div class="mt-3" id="miscFeesSection" style="display: {{ in_array($admission->admission_status, ['irregular', 'transferee']) ? 'block' : 'none' }};">
                                            <h5>Miscellaneous Fees</h5>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Fee Name</th>
                                                        <th>Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="miscFeesList">
                                                    @if(in_array($admission->admission_status, ['irregular', 'transferee']) && $admission->misc_fees)
                                                    @foreach($admission->misc_fees as $fee)
                                                    <tr id="misc_fee_row_{{ $fee->id }}">
                                                        <td>{{ $fee->name }}</td>
                                                        <td>
                                                            <input type="number" class="form-control misc-fee-amount"
                                                                name="misc_fees[{{ $fee->id }}][amount]"
                                                                value="{{ $fee->amount }}" {{ $fee->is_required ? 'readonly' : '' }}>
                                                            <input type="hidden" name="misc_fees[{{ $fee->id }}][name]" value="{{ $fee->name }}">
                                                            <input type="hidden" name="misc_fees[{{ $fee->id }}][is_required]" value="{{ $fee->is_required ? 1 : 0 }}">
                                                        </td>
                                                        <td>
                                                            @if($fee->is_required)
                                                            <span class="badge bg-primary">Required</span>
                                                            @else
                                                            <button type="button" class="btn btn-sm btn-danger remove-misc-fee">Remove</button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                    @endif
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Total Misc Fees</th>
                                                        <th id="misc_fees_total">
                                                            {{ in_array($admission->admission_status, ['irregular', 'transferee']) ? number_format($admission->miscFees?->sum('amount') ?? 0, 2) : '0.00' }}
                                                        </th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>

                                            <!-- Add new misc fee -->
                                            <div class="row mt-3">
                                                <div class="col-md-5">
                                                    <input type="text" id="newMiscFeeName" class="form-control" placeholder="Fee Name">
                                                </div>
                                                <div class="col-md-5">
                                                    <input type="number" id="newMiscFeeAmount" class="form-control" placeholder="Amount">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" id="addMiscFeeBtn" class="btn btn-primary">Add</button>
                                                </div>
                                            </div>

                                            <input type="hidden" name="misc_fees_total" id="misc_fees_input"
                                                value="{{ in_array($admission->admission_status, ['irregular', 'transferee']) ? $admission->misc_fees->sum('amount') : 0 }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                $(document).ready(function() {
                                    // ========== GLOBAL VARIABLES ==========
                                    let selectedCourses = [];

                                    // Initialize based on current admission status
                                    const currentStatus = $('.admission-status:checked').val();
                                    toggleAdmissionFields(currentStatus);

                                    // ========== ADMISSION STATUS HANDLING ==========
                                    $('.admission-status').change(function() {
                                        const status = $(this).val();
                                        toggleAdmissionFields(status);
                                    });

                                    function toggleAdmissionFields(status) {
                                        if (status === 'regular') {
                                            $('#regularCourseMapping').show();
                                            $('#irregularCourseSelection').hide();
                                            $('#transfereeFields').hide();
                                            $('#irregularTuitionDisplay').hide();
                                            $('#miscFeesSection').hide();

                                            // Trigger tuition calculation if mapping is already selected
                                            if ($('#course_mapping_id').val()) {
                                                calculateRegularTuition($('#course_mapping_id').val());
                                            }
                                        } else if (status === 'transferee') {
                                            $('#regularCourseMapping').show();
                                            $('#irregularCourseSelection').show();
                                            $('#transfereeFields').show();
                                            $('#irregularTuitionDisplay').show();
                                            $('#miscFeesSection').show();

                                            // Load courses if mapping is already selected
                                            if ($('#course_mapping_id').val()) {
                                                loadMappingCourses($('#course_mapping_id').val());
                                            }
                                        } else if (status === 'irregular') {
                                            $('#regularCourseMapping').show();
                                            $('#irregularCourseSelection').show();
                                            $('#transfereeFields').hide();
                                            $('#irregularTuitionDisplay').show();
                                            $('#miscFeesSection').show();

                                            // Load courses if mapping is already selected
                                            if ($('#course_mapping_id').val()) {
                                                loadMappingCourses($('#course_mapping_id').val());
                                            }
                                        }
                                    }

                                    // ========== COURSE MAPPING HANDLING ==========
                                    $('#course_mapping_id').on('change', function() {
                                        const status = $('.admission-status:checked').val();
                                        const mappingId = $(this).val();

                                        if (status === 'regular') {
                                            calculateRegularTuition(mappingId);
                                        } else if ((status === 'irregular' || status === 'transferee') && mappingId) {
                                            loadMappingCourses(mappingId);
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
                                            Swal.fire('Error', 'Please enter both name and amount', 'error');
                                            return;
                                        }

                                        const tempId = 'new_' + Date.now();
                                        addMiscFee(tempId, name, amount, false);

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
                                                            const prereqBadge = course.has_prerequisites ?
                                                                '<span class="badge bg-warning float-end">Has Prereq</span>' :
                                                                '';
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
                                            selectedCourses.push({
                                                id: courseId,
                                                override_prereq: override
                                            });
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

                                        // Default values if no courses
                                        let totalUnits = 0;
                                        let tuitionFee = miscFeesTotal;

                                        if (courseIds.length === 0) {
                                            $('#total_units_display').text(totalUnits);
                                            $('#tuition_fee_display').text(tuitionFee.toFixed(2));
                                            $('#tuition_fee_input').val(tuitionFee);
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
                                });
                            </script>

                            <!-- Education Information Tab -->
                            <div class="tab-pane fade" id="education" role="tabpanel">
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Education Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="student_no">Student Number</label>
                                                    <input value="{{ $admission->student_no }}" type="text"
                                                        name="student_no" id="student_no" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="lrn">LRN</label>
                                                    <input value="{{ $admission->lrn }}" type="text"
                                                        name="lrn" id="lrn" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="admission_status">Admission Status</label>
                                                    <input value="{{ $admission->admission_status }}" type="text"
                                                        name="admission_status" id="admission_status"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="admission_year">Admission Year</label>
                                                    <input value="{{ $admission->admission_year }}" type="number"
                                                        name="admission_year" id="admission_year"
                                                        class="form-control" min="1900" max="2100">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="major">Major</label>
                                                    <input value="{{ $admission->major }}" type="text"
                                                        name="major" id="major" class="form-control">
                                                </div>
                                            </div>
                                        </div>



                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="status" class="font-weight-bold">Status <span
                                                            class="text-danger">*</span></label>
                                                    <input value="{{ $admission->status }}" type="text"
                                                        name="status" id="status" class="form-control" required>
                                                    <div class="invalid-feedback">Please provide status.</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="honors">Honors</label>
                                                    <input value="{{ $admission->honors }}" type="text"
                                                        name="honors" id="honors" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <h5 class="mt-4 border-bottom pb-2">Previous Schools</h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="previous_school">Previous School</label>
                                                    <input value="{{ $admission->previous_school }}" type="text"
                                                        name="previous_school" id="previous_school"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="previous_school_address">Previous School
                                                        Address</label>
                                                    <input value="{{ $admission->previous_school_address }}"
                                                        type="text" name="previous_school_address"
                                                        id="previous_school_address" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="elementary_school">Elementary School</label>
                                                    <input value="{{ $admission->elementary_school }}" type="text"
                                                        name="elementary_school" id="elementary_school"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="elementary_address">Elementary Address</label>
                                                    <input value="{{ $admission->elementary_address }}"
                                                        type="text" name="elementary_address"
                                                        id="elementary_address" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="secondary_school">Secondary School</label>
                                                    <input value="{{ $admission->secondary_school }}" type="text"
                                                        name="secondary_school" id="secondary_school"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="secondary_address">Secondary Address</label>
                                                    <input value="{{ $admission->secondary_address }}" type="text"
                                                        name="secondary_address" id="secondary_address"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Family Information Tab -->
                            <div class="tab-pane fade" id="family" role="tabpanel">
                                <!-- Father's Information -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Father's Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="father_last_name">Last Name</label>
                                                    <input value="{{ $admission->father_last_name }}" type="text"
                                                        name="father_last_name" id="father_last_name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="father_first_name">First Name</label>
                                                    <input value="{{ $admission->father_first_name }}" type="text"
                                                        name="father_first_name" id="father_first_name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="father_middle_name">Middle Name</label>
                                                    <input value="{{ $admission->father_middle_name }}"
                                                        type="text" name="father_middle_name"
                                                        id="father_middle_name" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="father_contact">Contact Number</label>
                                                    <input value="{{ $admission->father_contact }}" type="text"
                                                        name="father_contact" id="father_contact"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="father_profession">Profession</label>
                                                    <input value="{{ $admission->father_profession }}" type="text"
                                                        name="father_profession" id="father_profession"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="father_industry">Industry</label>
                                                    <input value="{{ $admission->father_industry }}" type="text"
                                                        name="father_industry" id="father_industry"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mother's Information -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0">Mother's Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mother_last_name">Last Name</label>
                                                    <input value="{{ $admission->mother_last_name }}" type="text"
                                                        name="mother_last_name" id="mother_last_name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mother_first_name">First Name</label>
                                                    <input value="{{ $admission->mother_first_name }}" type="text"
                                                        name="mother_first_name" id="mother_first_name"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mother_middle_name">Middle Name</label>
                                                    <input value="{{ $admission->mother_middle_name }}"
                                                        type="text" name="mother_middle_name"
                                                        id="mother_middle_name" class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mother_contact">Contact Number</label>
                                                    <input value="{{ $admission->mother_contact }}" type="text"
                                                        name="mother_contact" id="mother_contact"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mother_profession">Profession</label>
                                                    <input value="{{ $admission->mother_profession }}" type="text"
                                                        name="mother_profession" id="mother_profession"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="mother_industry">Industry</label>
                                                    <input value="{{ $admission->mother_industry }}" type="text"
                                                        name="mother_industry" id="mother_industry"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-actions d-flex justify-content-between border-top pt-3">
                            <a href="{{ route('admissions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Add this script for form validation and tab persistence -->
            <script>
                // Form validation
                (function() {
                    'use strict';
                    window.addEventListener('load', function() {
                        var forms = document.getElementsByClassName('needs-validation');
                        var validation = Array.prototype.filter.call(forms, function(form) {
                            form.addEventListener('submit', function(event) {
                                if (form.checkValidity() === false) {
                                    event.preventDefault();
                                    event.stopPropagation();

                                    // Find the first invalid field and switch to its tab
                                    var invalidFields = form.querySelectorAll(':invalid');
                                    if (invalidFields.length > 0) {
                                        var field = invalidFields[0];
                                        var tabPane = field.closest('.tab-pane');
                                        if (tabPane) {
                                            var tabId = tabPane.id;
                                            $('.nav-tabs a[href="#' + tabId + '"]').tab('show');
                                        }
                                        field.focus();
                                    }
                                }
                                form.classList.add('was-validated');
                            }, false);
                        });
                    }, false);
                })();

                // Remember last active tab
                $(document).ready(function() {
                    // Check for saved tab in localStorage
                    var lastTab = localStorage.getItem('lastTab');
                    if (lastTab) {
                        $('.nav-tabs a[href="' + lastTab + '"]').tab('show');
                    }

                    // Save the latest tab when a new one is shown
                    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                        localStorage.setItem('lastTab', $(e.target).attr('href'));
                    });
                });
            </script>

            <!-- End of Edit Form -->


        </div>

    </div>
    <!-- End of Main Content -->

    @include('layouts.footer')

</div>
<!-- End of Content Wrapper -->

@endsection

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
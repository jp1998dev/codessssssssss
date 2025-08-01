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

                <!-- <button class="btn btn-primary" data-toggle="modal" data-target="#admissionFormModal">
                    Download Report
                </button> -->
            </div>

            <!-- ✏️ Edit Admission Form -->

            <form method="POST" action="{{ route('shs.update', $student->student_id) }}">
                @csrf
                @method('PUT')

                <div class="container py-4">
                    <h4 class="mb-4">Student Information</h4>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Track</label>
                            <select class="form-select" name="track" required>
                                <option value="1" {{ old('track', $enrollment->track ?? '') == '1' ? 'selected' : '' }}>Academic</option>
                                <option value="2" {{ old('track', $enrollment->track ?? '') == '2' ? 'selected' : '' }}>Technical-Vocational</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Strand</label>
                            <select class="form-select" name="strandID" required>
                                @foreach($strands as $strand)
                                <option value="{{ $strand->strand_id }}" {{ old('strandID', $enrollment->strand_id ?? '') == $strand->strand_id ? 'selected' : '' }}>
                                    {{ $strand->strand_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Section</label>
                            <input type="text" class="form-control" name="section" value="{{ old('section', $enrollment->section ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="lastName" value="{{ old('lastName', $student->last_name ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="firstName" value="{{ old('firstName', $student->first_name ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Middle Name</label>
                            <input type="text" class="form-control" name="middleName" value="{{ old('middleName', $student->middle_name ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select class="form-select" name="gender" required>
                                <option value="Male" {{ old('gender', $student->gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $student->gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Civil Status</label>
                            <input type="text" class="form-control" name="civilStatus" value="{{ old('civilStatus', $student->civil_status ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Birth Date</label>
                            <input type="date" class="form-control" name="birthDate" value="{{ old('birthDate', $student->birthday ?? '') }}" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Birth Place</label>
                            <input type="text" class="form-control" name="birthPlace" value="{{ old('birthPlace', $student->birthplace ?? '') }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-control" name="address" value="{{ old('address', $student->address ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" class="form-control" name="mobileNumber" value="{{ old('mobileNumber', $student->mobile_number ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="emailAddress" value="{{ old('emailAddress', $student->email_address ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">LRN</label>
                            <input type="text" class="form-control" name="lrn" value="{{ old('lrn', $student->lrn_number ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">ESC Number</label>
                            <input type="text" class="form-control" name="escNumber" value="{{ old('escNumber', $enrollment->esc_number ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary School Name</label>
                            <input type="text" class="form-control" name="secondarySchoolName" value="{{ old('secondarySchoolName', $student->secondary_school_name ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Secondary School Address</label>
                            <input type="text" class="form-control" name="secondarySchoolAddress" value="{{ old('secondarySchoolAddress', $student->secondary_address ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">School Classification</label>
                            <select class="form-select" name="secondarySchoolClassification" required>
                                <option value="Private" {{ old('secondarySchoolClassification', $student->classification ?? '') == 'Private' ? 'selected' : '' }}>Private</option>
                                <option value="Public" {{ old('secondarySchoolClassification', $student->classification ?? '') == 'Public' ? 'selected' : '' }}>Public</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type of Payee</label>
                            <select class="form-select" name="type_of_pee" required>
                                @foreach($typesOfPee as $pee)
                                <option value="{{ $pee->id }}" {{ old('type_of_pee', $enrollment->type_of_payee_id ?? '') == $pee->id ? 'selected' : '' }}>
                                    {{ $pee->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Parents & Guardian</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Father's Name</label>
                            <input type="text" class="form-control" name="fathersName" value="{{ old('fathersName', $student->father_name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Father's Address</label>
                            <input type="text" class="form-control" name="fathersAddress" value="{{ old('fathersAddress', $student->father_address ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Father's Occupation</label>
                            <input type="text" class="form-control" name="fathersOccupation" value="{{ old('fathersOccupation', $student->father_occupation ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Father's Contact</label>
                            <input type="text" class="form-control" name="fathersContactNumber" value="{{ old('fathersContactNumber', $student->father_contact ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Name</label>
                            <input type="text" class="form-control" name="mothersName" value="{{ old('mothersName', $student->mother_name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Address</label>
                            <input type="text" class="form-control" name="motherAddress" value="{{ old('motherAddress', $student->mother_address ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Occupation</label>
                            <input type="text" class="form-control" name="mothersOccupation" value="{{ old('mothersOccupation', $student->mother_occupation ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mother's Contact</label>
                            <input type="text" class="form-control" name="mothersContactNumber" value="{{ old('mothersContactNumber', $student->mother_contact ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guardian Name</label>
                            <input type="text" class="form-control" name="guardianName" value="{{ old('guardianName', $student->guardian_name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guardian Address</label>
                            <input type="text" class="form-control" name="guardianAddress" value="{{ old('guardianAddress', $student->guardian_address ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Relationship</label>
                            <input type="text" class="form-control" name="relationship" value="{{ old('relationship', $student->guardian_relationship ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Guardian Contact</label>
                            <input type="text" class="form-control" name="guardianContactNo" value="{{ old('guardianContactNo', $student->guardian_contact ?? '') }}" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Additional Fields</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Government Assistance</label>
                            <input type="text" class="form-control" name="governmentAssistance" value="{{ old('governmentAssistance', $enrollment->government_assistance ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Grade</label>
                            <input type="text" class="form-control" name="grade" value="{{ old('grade', $enrollment->grade_level ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Region</label>
                            <input type="text" class="form-control" name="region" value="{{ old('region', $student->region ?? '') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Division ID</label>
                            <input type="text" class="form-control" name="divisionID" value="{{ old('divisionID', $student->division_id ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Summer</label>
                            <input type="text" class="form-control" name="summer" value="{{ old('summer', $enrollment->summer ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Voucher Recipient</label><br>
                            <input class="form-check-input" type="checkbox" name="voucherRecipient" {{ old('voucherRecipient', $enrollment->voucher_recipient_checklist ?? false) ? 'checked' : '' }}>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Year Graduated</label>
                            <input type="text" class="form-control" name="yearGraduatedID" value="{{ old('yearGraduatedID', $student->year_graduated ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Award ID</label>
                            <input type="text" class="form-control" name="awardID" value="{{ old('awardID', $student->award_id ?? '') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Clearance</label>
                            <input type="text" class="form-control" name="clearance" value="{{ old('clearance', $student->checklist_clearance ?? '') }}">
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Student</button>
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
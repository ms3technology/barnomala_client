@extends('layouts.app')

@section('title', 'Online Apply')

@section('content')
@php
    $school = $formData['school'] ?? null;
    $schoolName = $school['name'] ?? $options['institute.branding.name'] ?? config('app.name', 'Barnomala');
    $schoolDomain = $school['domain_name'] ?? ($schoolContext['display'] ?? request()->getHost());
    $schoolShortCode = $school['short_code'] ?? null;
    $classOptions = collect($formData['classOptions'] ?? []);
    $selectedClassId = old('admission_class_id', $prefill['admission_class_id'] ?? null);
    $selectedClass = $classOptions->firstWhere('value', $selectedClassId);
    $showGroupField = (bool) ($selectedClass['has_groups'] ?? false);
    $selectedGroupOptions = collect($selectedClass['groups'] ?? []);
    if ($selectedGroupOptions->isEmpty()) {
        $selectedGroupOptions = collect($formData['groupOptions'] ?? []);
    }
    $lookupApplicants = collect($lookupResult['applicants'] ?? []);
    $lookupFound = (bool) ($lookupResult['found'] ?? false);
    $hasLookup = filled($lookupFilters['phone'] ?? null) && filled($lookupFilters['dob'] ?? null);
    $currentAcademicYear = $admissionYears[0] ?? now()->year;
    $prefill = $prefill ?? [];
    $prefillValue = static fn (string $key) => old($key, $prefill[$key] ?? null);
    $hasPrefill = !empty($prefill);
    $prefillClassId = $prefill['admission_class_id'] ?? null;
@endphp

<div class="app-container">

    <div class="page-header">
        <h1>Student Admission Portal</h1>
        <p>Complete your application in simple steps</p>
    </div>

    {{-- Section 1: Search Existing Applications --}}
    <div class="app-card" id="search-section">
        <div class="section-header">
            <div class="section-number">1</div>
            <div class="section-title">Already Applied? Search Your Application</div>
        </div>

        <form action="{{ route('apply.index') }}" method="GET" class="search-box">
            <div class="form-group">
                <label for="lookup-phone">Phone Number</label>
                <input
                    id="lookup-phone"
                    name="phone"
                    type="tel"
                    value="{{ old('phone', $lookupFilters['phone'] ?? ($prefill['phone'] ?? '')) }}"
                    placeholder="017xxxxxxxx"
                    pattern="[0-9]{11}"
                    class="form-control"
                >
            </div>
            <div class="form-group">
                <label for="lookup-dob">Date of Birth</label>
                <input
                    id="lookup-dob"
                    name="dob"
                    type="date"
                    value="{{ old('dob', $lookupFilters['dob'] ?? ($prefill['dob'] ?? '')) }}"
                    class="form-control"
                >
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Search
            </button>
        </form>

        @if($hasLookup)
            <div class="search-results">
                <div class="alert alert-{{ $lookupFound ? 'success' : 'info' }}">
                    {{ $lookupFound ? 'Matching applications were found.' : 'No matching applications were found.' }}
                    @if($lookupFound && $lookupApplicants->isNotEmpty())
                        ({{ $lookupApplicants->count() }} application(s))
                    @endif
                </div>

                @if($lookupError)
                    <div class="alert alert-error">{{ $lookupError }}</div>
                @endif

                @if($lookupApplicants->isNotEmpty())
                    <div class="class-grid">
                        @foreach($lookupApplicants as $application)
                            <div class="class-card">
                                <div class="class-name" style="font-size:14px;">App #{{ $application['application_no'] ?? 'N/A' }}</div>
                                <div class="class-fee" style="text-transform:uppercase;">
                                    {{ $application['application_status'] ?? 'pending' }}
                                </div>
                                <div class="app-meta">
                                    <div>Phone: {{ $application['phone'] ?? 'N/A' }}</div>
                                    <div>DOB: {{ $application['dob'] ?? 'N/A' }}</div>
                                </div>
                                @if($application['application_no'] ?? null)
                                    <a
                                        href="{{ route('apply.index', array_filter([
                                            'application_no' => $application['application_no'],
                                            'applicant_id' => $application['id'] ?? null,
                                            'phone' => $lookupFilters['phone'] ?? null,
                                            'dob' => $lookupFilters['dob'] ?? null,
                                        ])) }}"
                                        class="btn btn-outline"
                                    >
                                        <i class="fas fa-pen-to-square"></i> Select &amp; Edit
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif(! $lookupFound)
                    <div class="alert alert-info">No past application entries were returned for this lookup.</div>
                @endif
            </div>
        @endif
    </div>

    <div class="app-card form-section active" id="form-section">
        <div class="section-header">
            <div class="section-number" id="form-section-number">2</div>
            <div class="section-title">New / Update Application Form</div>
        </div>

        @if($hasPrefill)
            <div class="alert alert-info">
                <strong>Editing application:</strong> {{ $prefill['application_no'] ?? '' }} — submitting will update the record.
            </div>
        @endif

        <form action="{{ route('apply.submit') }}" method="POST" enctype="multipart/form-data" id="applicationForm">
            @csrf

            @if($hasPrefill && !empty($prefill['application_no']))
                <input type="hidden" name="application_no" value="{{ $prefill['application_no'] }}">
            @endif

            @if(!empty($selectedApplicantId))
                <input type="hidden" name="applicant_id" value="{{ $selectedApplicantId }}">
            @endif

            @foreach($schoolContext['payload'] as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            {{-- Academic Information --}}
            <div class="section-subtitle">Academic Information</div>
            <div class="row">
                <div>
                    <div class="form-group">
                        <label class="form-label" for="admission_year">Admission Year *</label>
                        <select id="admission_year" name="admission_year" required class="form-control">
                            <option value="">Select year</option>
                            @foreach($admissionYears as $year)
                                <option value="{{ $year }}" @selected((string) $prefillValue('admission_year') === (string) $year || (old('admission_year') === null && ! $hasPrefill && $currentAcademicYear == $year))>{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('admission_year')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="admission_class_id">Admission Class *</label>
                        <select id="admission_class_id" name="admission_class_id" required class="form-control">
                            <option value="">Select class</option>
                            @forelse($classOptions as $option)
                                <option
                                    value="{{ $option['value'] }}"
                                    data-has-groups="{{ !empty($option['has_groups']) ? '1' : '0' }}"
                                    data-groups='@json($option['groups'] ?? [])'
                                    @selected((string) $prefillValue('admission_class_id') === (string) $option['value'])
                                >{{ $option['label'] }}</option>
                            @empty
                                <option value="" disabled>No class data loaded</option>
                            @endforelse
                        </select>
                        @error('admission_class_id')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div id="group-field" class="{{ $showGroupField ? '' : 'hidden' }}">
                    <div class="form-group">
                        <label class="form-label" for="applying_group_id">Applying Group</label>
                        <select id="applying_group_id" name="applying_group_id" class="form-control" {{ $showGroupField ? '' : 'disabled' }}>
                            <option value="">Select group</option>
                            @forelse($selectedGroupOptions as $option)
                                <option value="{{ $option['value'] }}" @selected((string) $prefillValue('applying_group_id') === (string) $option['value'])>{{ $option['label'] }}</option>
                            @empty
                                <option value="" disabled>No group data loaded</option>
                            @endforelse
                        </select>
                        @error('applying_group_id')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Student Information --}}
            <div class="section-subtitle">Student Information</div>
            <div class="row">
                <div>
                    <div class="form-group">
                        <label class="form-label" for="full_name">Full Name *</label>
                        <input id="full_name" name="full_name" type="text" value="{{ $prefillValue('full_name') }}" required class="form-control">
                        @error('full_name')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="full_name_bn">Full Name Bangla</label>
                        <input id="full_name_bn" name="full_name_bn" type="text" value="{{ $prefillValue('full_name_bn') }}" class="form-control">
                        @error('full_name_bn')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="phone">Phone *</label>
                        <input id="phone" name="phone" type="text" value="{{ $prefillValue('phone') }}" required class="form-control">
                        @error('phone')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="dob">Date of Birth *</label>
                        <input id="dob" name="dob" type="date" value="{{ $prefillValue('dob') }}" required class="form-control">
                        @error('dob')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="gender">Gender</label>
                        <select id="gender" name="gender" class="form-control">
                            <option value="">Select</option>
                            @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" @selected((string) $prefillValue('gender') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gender')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="image">Applicant Photo</label>
                        <input id="image" name="image" type="file" accept="image/*" class="form-control">
                        @error('image')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Address --}}
            <div class="section-subtitle">Address</div>
            <div class="row">
                <div>
                    <div class="form-group">
                        <label class="form-label" for="present_address">Present Address</label>
                        <textarea id="present_address" name="present_address" rows="3" class="form-control">{{ $prefillValue('present_address') }}</textarea>
                        @error('present_address')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="permanent_address">Permanent Address</label>
                        <textarea id="permanent_address" name="permanent_address" rows="3" class="form-control">{{ $prefillValue('permanent_address') }}</textarea>
                        @error('permanent_address')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Guardian / Parent Information --}}
            <div class="section-subtitle">Guardian / Parent Information</div>
            <div class="row">
                <div>
                    <div class="form-group">
                        <label class="form-label" for="father_name">Father's Name *</label>
                        <input id="father_name" name="father_name" type="text" value="{{ $prefillValue('father_name') }}" required class="form-control">
                        @error('father_name')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="mother_name">Mother's Name *</label>
                        <input id="mother_name" name="mother_name" type="text" value="{{ $prefillValue('mother_name') }}" required class="form-control">
                        @error('mother_name')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="father_profession">Father Profession</label>
                        <input id="father_profession" name="father_profession" type="text" value="{{ $prefillValue('father_profession') }}" class="form-control">
                        @error('father_profession')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="mother_profession">Mother Profession</label>
                        <input id="mother_profession" name="mother_profession" type="text" value="{{ $prefillValue('mother_profession') }}" class="form-control">
                        @error('mother_profession')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="parent_annual_income">Parent Annual Income</label>
                        <input id="parent_annual_income" name="parent_annual_income" type="number" step="0.01" value="{{ $prefillValue('parent_annual_income') }}" class="form-control">
                        @error('parent_annual_income')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="father_nid">Father NID</label>
                        <input id="father_nid" name="father_nid" type="text" value="{{ $prefillValue('father_nid') }}" class="form-control">
                        @error('father_nid')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="mother_nid">Mother NID</label>
                        <input id="mother_nid" name="mother_nid" type="text" value="{{ $prefillValue('mother_nid') }}" class="form-control">
                        @error('mother_nid')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="guardian_name">Guardian Name</label>
                        <input id="guardian_name" name="guardian_name" type="text" value="{{ $prefillValue('guardian_name') }}" class="form-control">
                        @error('guardian_name')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="guardian_phone">Guardian Phone</label>
                        <input id="guardian_phone" name="guardian_phone" type="text" value="{{ $prefillValue('guardian_phone') }}" class="form-control">
                        @error('guardian_phone')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="guardian_nid">Guardian NID</label>
                        <input id="guardian_nid" name="guardian_nid" type="text" value="{{ $prefillValue('guardian_nid') }}" class="form-control">
                        @error('guardian_nid')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="emergency_phone">Emergency Phone</label>
                        <input id="emergency_phone" name="emergency_phone" type="text" value="{{ $prefillValue('emergency_phone') }}" class="form-control">
                        @error('emergency_phone')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Additional Info --}}
            <div class="section-subtitle">Additional Info</div>
            <div class="row">
                <div>
                    <div class="form-group">
                        <label class="form-label" for="religion_id">Religion</label>
                        <select id="religion_id" name="religion_id" class="form-control">
                            <option value="">Select religion</option>
                            @forelse($formData['religionOptions'] ?? [] as $option)
                                <option value="{{ $option['value'] }}" @selected((string) $prefillValue('religion_id') === (string) $option['value'])>{{ $option['label'] }}</option>
                            @empty
                                <option value="" disabled>No religion data loaded</option>
                            @endforelse
                        </select>
                        @error('religion_id')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ $prefillValue('email') }}" class="form-control">
                        @error('email')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="blood_group">Blood Group</label>
                        <input id="blood_group" name="blood_group" type="text" value="{{ $prefillValue('blood_group') }}" class="form-control">
                        @error('blood_group')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="nationality">Nationality</label>
                        <input id="nationality" name="nationality" type="text" value="{{ $prefillValue('nationality') }}" class="form-control">
                        @error('nationality')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="birth_reg_no">Birth Reg. No.</label>
                        <input id="birth_reg_no" name="birth_reg_no" type="text" value="{{ $prefillValue('birth_reg_no') }}" class="form-control">
                        @error('birth_reg_no')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Admission Details --}}
            <div class="section-subtitle">Admission Details</div>
            <div class="row">
                <div>
                    <div class="form-group">
                        <label class="form-label" for="admission_date">Admission Date</label>
                        <input id="admission_date" name="admission_date" type="date" value="{{ $prefillValue('admission_date') }}" class="form-control">
                        @error('admission_date')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="shift">Shift</label>
                        <input id="shift" name="shift" type="text" value="{{ $prefillValue('shift') }}" class="form-control">
                        @error('shift')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="tc_number">TC Number</label>
                        <input id="tc_number" name="tc_number" type="text" value="{{ $prefillValue('tc_number') }}" class="form-control">
                        @error('tc_number')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Previous Academic Info --}}
            <div class="section-subtitle">Previous Academic Info</div>
            <div class="row">
                <div>
                    <div class="form-group">
                        <label class="form-label" for="previous_school">Previous School</label>
                        <input id="previous_school" name="previous_school" type="text" value="{{ $prefillValue('previous_school') }}" class="form-control">
                        @error('previous_school')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="facilities_availed">Facilities Availed</label>
                        <input id="facilities_availed" name="facilities_availed" type="text" value="{{ $prefillValue('facilities_availed') }}" class="form-control">
                        @error('facilities_availed')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="ssc_roll">SSC Roll</label>
                        <input id="ssc_roll" name="ssc_roll" type="text" value="{{ $prefillValue('ssc_roll') }}" class="form-control">
                        @error('ssc_roll')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="ssc_reg_no">SSC Registration No.</label>
                        <input id="ssc_reg_no" name="ssc_reg_no" type="text" value="{{ $prefillValue('ssc_reg_no') }}" class="form-control">
                        @error('ssc_reg_no')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label" for="previous_gpa">Previous GPA</label>
                        <input id="previous_gpa" name="previous_gpa" type="number" step="0.01" value="{{ $prefillValue('previous_gpa') }}" class="form-control">
                        @error('previous_gpa')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Flags --}}
            <div class="section-subtitle">Flags</div>
            <div class="row">
                <div>
                    <div class="form-group">
                        <label class="checkbox-row">
                            <input type="checkbox" name="is_father_late" value="1" @checked($prefillValue('is_father_late'))>
                            Father is late
                        </label>
                        @error('is_father_late')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="checkbox-row">
                            <input type="checkbox" name="is_mother_late" value="1" @checked($prefillValue('is_mother_late'))>
                            Mother is late
                        </label>
                        @error('is_mother_late')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="checkbox-row">
                            <input type="checkbox" name="is_intellectual_disability" value="1" @checked($prefillValue('is_intellectual_disability'))>
                            Intellectual disability
                        </label>
                        @error('is_intellectual_disability')<p class="error-text">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="submit-row">
                <button type="submit" class="btn {{ $hasPrefill ? 'btn-warning' : 'btn-success' }}">
                    <i class="fas {{ $hasPrefill ? 'fa-arrows-rotate' : 'fa-paper-plane' }}"></i>
                    {{ $hasPrefill ? 'Update Application' : 'Submit Application' }}
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const classSelect = document.getElementById('admission_class_id');
        const groupField = document.getElementById('group-field');
        const groupSelect = document.getElementById('applying_group_id');

        if (!classSelect || !groupField || !groupSelect) {
            return;
        }

        const defaultGroupOptions = @json($formData['groupOptions'] ?? []);

        function renderOptions(options) {
            const fragment = document.createDocumentFragment();

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Select group';
            fragment.appendChild(placeholder);

            if (!Array.isArray(options) || options.length === 0) {
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.disabled = true;
                emptyOption.textContent = 'No group data loaded';
                fragment.appendChild(emptyOption);
            } else {
                options.forEach((option) => {
                    if (!option || option.value === undefined || option.value === null || option.label === undefined) {
                        return;
                    }

                    const opt = document.createElement('option');
                    opt.value = option.value;
                    opt.textContent = option.label;
                    if (String(option.value) === groupSelect.value) {
                        opt.selected = true;
                    }
                    fragment.appendChild(opt);
                });
            }

            groupSelect.innerHTML = '';
            groupSelect.appendChild(fragment);
        }

        function toggleGroupField() {
            const selectedOption = classSelect.options[classSelect.selectedIndex];
            const hasGroups = selectedOption?.dataset?.hasGroups === '1';
            const rawGroups = selectedOption?.dataset?.groups || '[]';

            let classGroups = defaultGroupOptions;
            try {
                const parsedGroups = JSON.parse(rawGroups);
                if (Array.isArray(parsedGroups) && parsedGroups.length > 0) {
                    classGroups = parsedGroups;
                }
            } catch (error) {
                classGroups = defaultGroupOptions;
            }

            if (hasGroups) {
                groupField.classList.remove('hidden');
                groupSelect.disabled = false;
                renderOptions(classGroups);
            } else {
                groupSelect.value = '';
                groupSelect.disabled = true;
                renderOptions([]);
                groupField.classList.add('hidden');
            }
        }

        classSelect.addEventListener('change', toggleGroupField);
        toggleGroupField();
    });
</script>
@endsection

<style>
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --bg-color: #f8fafc;
        --card-bg: #ffffff;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
    }


    .app-container {
        max-width: 1000px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .app-card {
        background: var(--card-bg);
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-bottom: 20px;
    }

    @media screen and (max-width: 600px) {
        .app-card {
            padding: 15px 10px;
        }
    }

    .page-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 26px;
        margin-bottom: 5px;
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }

    .section-number {
        background: var(--primary-color);
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
    }

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-main);
    }

    /* Search Section */
    .search-box {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    @media screen and (max-width: 600px) {
        .search-box {
            flex-direction: column;
        }        
    }

    .search-input {
        flex: 1;
        padding: 10px 14px;
        border: 2px solid var(--border-color);
        border-radius: 6px;
        font-size: 15px;
        transition: border-color 0.2s;
    }

    .search-input:focus {
        border-color: var(--primary-color);
        outline: none;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
        display: inline-block;
    }

    .btn-success {
        background: var(--success-color);
        color: white;
    }

    .btn-outline {
        background: transparent;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
    }

    /* Class Cards */
    .class-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }

    .class-card {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 20px 15px;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }

    .class-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(37, 99, 235, 0.2);
    }

    .class-card.selected {
        background: linear-gradient(135deg, var(--success-color), #059669);
    }

    .class-card.selected::after {
        content: '✓';
        position: absolute;
        top: 8px;
        right: 8px;
        width: 22px;
        height: 22px;
        background: white;
        color: var(--success-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .class-card.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .class-card.disabled.selected {
        opacity: 1;
    }

    .class-name {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .class-fee {
        font-size: 13px;
        opacity: 0.9;
    }

    /* Form Styles */
    .form-section {
        margin-bottom: 25px;
    }

    .form-section:not(.active) .section-subtitle,
    .form-section:not(.active) .row,
    .form-section:not(.active) .form-group,
    .form-section:not(.active) form > *:not(.section-subtitle):not(.alert) {
        display: none;
    }

    .form-section:not(.active)::after {
        content: 'Select a class above to fill the application form';
        display: block;
        text-align: center;
        padding: 30px 15px;
        color: var(--text-muted);
        font-size: 15px;
    }

    .form-section.active {
        display: block;
    }

    /* Completed step styling */
    .section-number {
        transition: all 0.3s ease;
    }

    .section-subtitle {
        margin-top: 25px;
        font-size: 15px;
        font-weight: 600;
        color: var(--secondary-color);
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--border-color);
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-label {
        display: block;
        font-weight: 500;
        margin-bottom: 5px;
        color: var(--text-main);
        font-size: 14px;
    }

    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    input.form-control:not([type="checkbox"]):not([type="radio"]), 
    select.form-control {
        height: 40px;
    }

    textarea.form-control {
        height: auto;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .alert {
        padding: 10px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
        font-weight: 500;
        font-size: 14px;
    }

    .alert-success {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .alert-info {
        background-color: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

   

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid var(--border-color);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

        
    .search-box {
        display: flex;
        gap: 10px;
        align-items: flex-center;
    }

    .form-group {
        position: relative;
        flex: 1;
    }

    .form-group input {
        width: 100%;
        padding: 12px 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: none;
        font-size: 14px;
    }

    .form-group label {
        position: absolute;
        top: -8px;
        left: 10px;
        background: #fff;
        padding: 0 4px;
        font-size: 12px;
        color: #555;
        pointer-events: none;
    }

    .btn {
        padding: 10px 16px;
        font-size: 14px;
    }

    .row {
        margin-bottom: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
    }

    .submit-row {
        margin-top: 30px;
    }
</style>
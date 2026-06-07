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

<section class="relative overflow-hidden py-10 sm:py-14">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900"></div>
    <div class="absolute -left-20 top-24 h-64 w-64 rounded-full bg-cyan-400/20 blur-3xl"></div>
    <div class="absolute right-0 top-40 h-80 w-80 rounded-full bg-amber-300/15 blur-3xl"></div>

    <div class="relative mx-auto max-w-[90%] px-4 sm:px-6 lg:px-8">
        <div class="">
            <div class="space-y-6">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-lg shadow-slate-950/5 sm:p-8">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <h2 class="mt-2 text-2xl font-black text-slate-950">Check existing applications</h2>
                    </div>

                    <form action="{{ route('apply.index') }}" method="GET" class="mt-6 grid gap-4 md:grid-cols-[1fr_1fr_auto]">
                        <div>
                            <label for="lookup-phone" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Phone</label>
                            <input
                                id="lookup-phone"
                                name="phone"
                                type="text"
                                value="{{ old('phone', $lookupFilters['phone'] ?? ($prefill['phone'] ?? '')) }}"
                                placeholder="01700000000"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white"
                            >
                        </div>
                        <div>
                            <label for="lookup-dob" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Date of Birth</label>
                            <input
                                id="lookup-dob"
                                name="dob"
                                type="date"
                                value="{{ old('dob', $lookupFilters['dob'] ?? ($prefill['dob'] ?? '')) }}"
                                class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white"
                            >
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3.5 text-sm font-bold text-white transition hover:bg-slate-800">
                                <i class="fas fa-search"></i>
                                Lookup
                            </button>
                        </div>
                    </form>

                    @if($hasLookup)
                        <div class="mt-6 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.3em] text-slate-500">Lookup Result</p>
                                    <p class="mt-1 text-sm text-slate-600">
                                        {{ $lookupFound ? 'Matching applications were found.' : 'No matching applications were found.' }}
                                    </p>
                                </div>
                                @if($lookupFound && $lookupApplicants->isNotEmpty())
                                    <span class="inline-flex w-fit rounded-full bg-emerald-100 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.25em] text-emerald-700">
                                        {{ $lookupApplicants->count() }} application(s)
                                    </span>
                                @endif
                            </div>

                            @if($lookupError)
                                <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                    {{ $lookupError }}
                                </div>
                            @endif

                            @if($lookupApplicants->isNotEmpty())
                                <div class="mt-4 grid gap-3 md:grid-cols-2">
                                    @foreach($lookupApplicants as $application)
                                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-slate-400">Application No</p>
                                                    <p class="mt-1 text-lg font-black text-slate-950">{{ $application['application_no'] ?? 'N/A' }}</p>
                                                </div>
                                                <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] {{ ($application['application_status'] ?? 'pending') === 'approved' ? 'bg-emerald-100 text-emerald-700' : (($application['application_status'] ?? 'pending') === 'rejected' ? 'bg-rose-100 text-rose-700' : 'bg-amber-100 text-amber-700') }}">
                                                    {{ $application['application_status'] ?? 'pending' }}
                                                </span>
                                            </div>
                                            <div class="mt-4 grid grid-cols-2 gap-3 text-sm text-slate-600">
                                                <div>
                                                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">Phone</p>
                                                    <p class="mt-1 font-medium text-slate-800">{{ $application['phone'] ?? 'N/A' }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">DOB</p>
                                                    <p class="mt-1 font-medium text-slate-800">{{ $application['dob'] ?? 'N/A' }}</p>
                                                </div>
                                                @if($application['id'] ?? null)
                                                    <div class="col-span-2">
                                                        <p class="text-xs font-bold uppercase tracking-[0.25em] text-slate-400">Applicant ID</p>
                                                        <p class="mt-1 font-medium text-slate-800">{{ $application['id'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($application['application_no'] ?? null)
                                                <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                                                    <a
                                                        href="{{ route('apply.index', array_filter([
                                                            'application_no' => $application['application_no'],
                                                            'applicant_id' => $application['id'] ?? null,
                                                            'phone' => $lookupFilters['phone'] ?? null,
                                                            'dob' => $lookupFilters['dob'] ?? null,
                                                        ])) }}"
                                                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-500"
                                                    >
                                                        <i class="fas fa-pen-to-square"></i>
                                                        Select &amp; Edit
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-6 text-sm text-slate-500">
                                    No past application entries were returned for this lookup.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="rounded-4xl border border-slate-200 bg-white p-6 shadow-lg shadow-slate-950/5 sm:p-8">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                        <h2 class="mt-2 text-2xl font-black text-slate-950">Submit a new application</h2>
                    </div>

                    <form action="{{ route('apply.submit') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-8">
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

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label for="admission_year" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Admission Year *</label>
                                <select id="admission_year" name="admission_year" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                    <option value="">Select year</option>
                                    @foreach($admissionYears as $year)
                                        <option value="{{ $year }}" @selected((string) $prefillValue('admission_year') === (string) $year || (old('admission_year') === null && ! $hasPrefill && $currentAcademicYear == $year))>{{ $year }}</option>
                                    @endforeach
                                </select>
                                @error('admission_year')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="admission_class_id" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Admission Class *</label>
                                <select id="admission_class_id" name="admission_class_id" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
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
                                @error('admission_class_id')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div id="group-field" class="{{ $showGroupField ? '' : 'hidden' }}">
                                <label for="applying_group_id" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Applying Group</label>
                                <select id="applying_group_id" name="applying_group_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white" {{ $showGroupField ? '' : 'disabled' }}>
                                    <option value="">Select group</option>
                                    @forelse($selectedGroupOptions as $option)
                                        <option value="{{ $option['value'] }}" @selected((string) $prefillValue('applying_group_id') === (string) $option['value'])>{{ $option['label'] }}</option>
                                    @empty
                                        <option value="" disabled>No group data loaded</option>
                                    @endforelse
                                </select>
                                @error('applying_group_id')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="full_name" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Full Name *</label>
                                <input id="full_name" name="full_name" type="text" value="{{ $prefillValue('full_name') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('full_name')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="full_name_bn" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Full Name Bangla</label>
                                <input id="full_name_bn" name="full_name_bn" type="text" value="{{ $prefillValue('full_name_bn') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('full_name_bn')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="phone" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Phone *</label>
                                <input id="phone" name="phone" type="text" value="{{ $prefillValue('phone') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('phone')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="dob" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Date of Birth *</label>
                                <input id="dob" name="dob" type="date" value="{{ $prefillValue('dob') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('dob')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="gender" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Gender</label>
                                <select id="gender" name="gender" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                    <option value="">Select</option>
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" @selected((string) $prefillValue('gender') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('gender')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="image" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Applicant Photo</label>
                                <input id="image" name="image" type="file" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition file:mr-4 file:rounded-xl file:border-0 file:bg-slate-950 file:px-4 file:py-2 file:text-sm file:font-bold file:text-white focus:border-slate-950 focus:bg-white">
                                @error('image')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="religion_id" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Religion</label>
                                <select id="religion_id" name="religion_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                    <option value="">Select religion</option>
                                    @forelse($formData['religionOptions'] ?? [] as $option)
                                        <option value="{{ $option['value'] }}" @selected((string) $prefillValue('religion_id') === (string) $option['value'])>{{ $option['label'] }}</option>
                                    @empty
                                        <option value="" disabled>No religion data loaded</option>
                                    @endforelse
                                </select>
                                @error('religion_id')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Email</label>
                                <input id="email" name="email" type="email" value="{{ $prefillValue('email') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('email')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label for="blood_group" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Blood Group</label>
                                <input id="blood_group" name="blood_group" type="text" value="{{ $prefillValue('blood_group') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('blood_group')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="nationality" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Nationality</label>
                                <input id="nationality" name="nationality" type="text" value="{{ $prefillValue('nationality') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('nationality')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="birth_reg_no" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Birth Reg. No.</label>
                                <input id="birth_reg_no" name="birth_reg_no" type="text" value="{{ $prefillValue('birth_reg_no') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('birth_reg_no')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="present_address" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Present Address</label>
                                <textarea id="present_address" name="present_address" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">{{ $prefillValue('present_address') }}</textarea>
                                @error('present_address')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="permanent_address" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Permanent Address</label>
                                <textarea id="permanent_address" name="permanent_address" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">{{ $prefillValue('permanent_address') }}</textarea>
                                @error('permanent_address')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="father_name" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Father's Name *</label>
                                <input id="father_name" name="father_name" type="text" value="{{ $prefillValue('father_name') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('father_name')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="mother_name" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Mother's Name *</label>
                                <input id="mother_name" name="mother_name" type="text" value="{{ $prefillValue('mother_name') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('mother_name')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label for="father_profession" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Father Profession</label>
                                <input id="father_profession" name="father_profession" type="text" value="{{ $prefillValue('father_profession') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('father_profession')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="mother_profession" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Mother Profession</label>
                                <input id="mother_profession" name="mother_profession" type="text" value="{{ $prefillValue('mother_profession') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('mother_profession')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="parent_annual_income" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Parent Annual Income</label>
                                <input id="parent_annual_income" name="parent_annual_income" type="number" step="0.01" value="{{ $prefillValue('parent_annual_income') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('parent_annual_income')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label for="father_nid" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Father NID</label>
                                <input id="father_nid" name="father_nid" type="text" value="{{ $prefillValue('father_nid') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('father_nid')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="mother_nid" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Mother NID</label>
                                <input id="mother_nid" name="mother_nid" type="text" value="{{ $prefillValue('mother_nid') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('mother_nid')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="guardian_name" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Guardian Name</label>
                                <input id="guardian_name" name="guardian_name" type="text" value="{{ $prefillValue('guardian_name') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('guardian_name')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label for="guardian_phone" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Guardian Phone</label>
                                <input id="guardian_phone" name="guardian_phone" type="text" value="{{ $prefillValue('guardian_phone') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('guardian_phone')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="guardian_nid" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Guardian NID</label>
                                <input id="guardian_nid" name="guardian_nid" type="text" value="{{ $prefillValue('guardian_nid') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('guardian_nid')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="emergency_phone" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Emergency Phone</label>
                                <input id="emergency_phone" name="emergency_phone" type="text" value="{{ $prefillValue('emergency_phone') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('emergency_phone')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="admission_date" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Admission Date</label>
                                <input id="admission_date" name="admission_date" type="date" value="{{ $prefillValue('admission_date') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('admission_date')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="shift" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Shift</label>
                                <input id="shift" name="shift" type="text" value="{{ $prefillValue('shift') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('shift')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label for="tc_number" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">TC Number</label>
                                <input id="tc_number" name="tc_number" type="text" value="{{ $prefillValue('tc_number') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('tc_number')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="previous_school" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Previous School</label>
                                <input id="previous_school" name="previous_school" type="text" value="{{ $prefillValue('previous_school') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('previous_school')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="facilities_availed" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Facilities Availed</label>
                                <input id="facilities_availed" name="facilities_availed" type="text" value="{{ $prefillValue('facilities_availed') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('facilities_availed')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="ssc_roll" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">SSC Roll</label>
                                <input id="ssc_roll" name="ssc_roll" type="text" value="{{ $prefillValue('ssc_roll') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('ssc_roll')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="ssc_reg_no" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">SSC Registration No.</label>
                                <input id="ssc_reg_no" name="ssc_reg_no" type="text" value="{{ $prefillValue('ssc_reg_no') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('ssc_reg_no')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="previous_gpa" class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Previous GPA</label>
                                <input id="previous_gpa" name="previous_gpa" type="number" step="0.01" value="{{ $prefillValue('previous_gpa') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-slate-900 outline-none transition focus:border-slate-950 focus:bg-white">
                                @error('previous_gpa')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Flags</label>
                                <div class="mt-2 grid gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4 sm:grid-cols-2">
                                    <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                                        <input type="checkbox" name="is_father_late" value="1" @checked($prefillValue('is_father_late')) class="h-4 w-4 rounded border-slate-300 text-slate-950 focus:ring-slate-950">
                                        Father is late
                                    </label>
                                    <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                                        <input type="checkbox" name="is_mother_late" value="1" @checked($prefillValue('is_mother_late')) class="h-4 w-4 rounded border-slate-300 text-slate-950 focus:ring-slate-950">
                                        Mother is late
                                    </label>
                                    <label class="flex items-center gap-3 text-sm font-medium text-slate-700 sm:col-span-2">
                                        <input type="checkbox" name="is_intellectual_disability" value="1" @checked($prefillValue('is_intellectual_disability')) class="h-4 w-4 rounded border-slate-300 text-slate-950 focus:ring-slate-950">
                                        Intellectual disability
                                    </label>
                                </div>
                                @error('is_father_late')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                                @error('is_mother_late')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                                @error('is_intellectual_disability')<p class="mt-2 text-sm font-medium text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs font-medium uppercase tracking-[0.25em] text-slate-400">
                                @if($hasPrefill)
                                    Editing application <span class="font-black text-slate-700">{{ $prefill['application_no'] ?? '' }}</span> — submitting will update the record.
                                @else
                                    Required fields are marked with *
                                @endif
                            </p>
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-bold text-white shadow-lg transition {{ $hasPrefill ? 'bg-amber-600 shadow-amber-600/20 hover:bg-amber-500' : 'bg-indigo-600 shadow-indigo-600/20 hover:bg-indigo-500' }}">
                                <i class="fas {{ $hasPrefill ? 'fa-arrows-rotate' : 'fa-paper-plane' }}"></i>
                                {{ $hasPrefill ? 'Update Application' : 'Submit Application' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

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

@extends('layouts.admin')

@section('title', 'Demographics Settings')

@push('header_actions')
<div class="flex items-center gap-3">
    <form action="{{ route('admin.transfer.lock') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="section" value="demographics">
        <input type="hidden" name="lock" value="{{ $isLocked ? '0' : '1' }}">
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md transition-all {{ $isLocked ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
            <i class="fas {{ $isLocked ? 'fa-lock' : 'fa-sync' }} mr-2"></i>
            {{ $isLocked ? 'Auto-Update Disabled' : 'Auto-Update Enabled' }}
        </button>
    </form>
    <button type="submit" form="demographics-form" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700">
        <i class="fas fa-save mr-2"></i>
        Save
    </button>
</div>
@endpush

@section('content')
<div class="space-y-4">
    <form id="demographics-form" action="{{ route('admin.demographics.update') }}" method="POST">
        @csrf
        
        {{-- Quick Stats Section --}}
        <div class="bg-white shadow rounded-lg overflow-hidden mb-4">
            <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-base font-bold text-gray-800">Institution Quick Stats</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Total Classes</label>
                        <input type="number" name="stats_classes" value="{{ $options['institute.stats.classes_count'] ?? 0 }}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Total Students</label>
                        <input type="number" name="stats_students" value="{{ $options['institute.stats.students_count'] ?? 0 }}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Total Teachers</label>
                        <input type="number" name="stats_teachers" value="{{ $options['institute.stats.teachers_count'] ?? 0 }}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Total Staffs</label>
                        <input type="number" name="stats_staffs" value="{{ $options['institute.stats.staffs_count'] ?? 0 }}" class="w-full px-3 py-2 text-sm rounded-md border border-gray-300 focus:ring-indigo-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Classes and Students --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-base font-bold text-gray-800">Classes</h3>
                    <button type="button" onclick="addRow('classes-rows', 'classes')" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                </div>
                <div class="p-4">
                    <div id="classes-rows" class="space-y-2">
                        @php $classes = $options['institute.demographics.classes'] ?? []; @endphp
                        @forelse($classes as $label => $value)
                            <div class="flex gap-2 items-center demographic-row">
                                <input type="text" name="classes[{{ $loop->index }}][label]" value="{{ $label }}" placeholder="Class" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <input type="number" name="classes[{{ $loop->index }}][value]" value="{{ $value }}" placeholder="Count" class="w-20 px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="button" onclick="this.closest('.demographic-row').remove()" class="text-red-500 hover:text-red-700 text-sm p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @empty
                            <p class="text-gray-400 text-xs italic empty-msg">No classes yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Gender Distribution --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-base font-bold text-gray-800">Gender</h3>
                    <button type="button" onclick="addRow('gender-rows', 'gender')" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                </div>
                <div class="p-4">
                    <div id="gender-rows" class="space-y-2">
                        @php $genders = $options['institute.demographics.gender'] ?? []; @endphp
                        @forelse($genders as $label => $value)
                            <div class="flex gap-2 items-center demographic-row">
                                <input type="text" name="gender[{{ $loop->index }}][label]" value="{{ $label }}" placeholder="Gender" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <input type="number" name="gender[{{ $loop->index }}][value]" value="{{ $value }}" placeholder="Count" class="w-20 px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="button" onclick="this.closest('.demographic-row').remove()" class="text-red-500 hover:text-red-700 text-sm p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @empty
                            <p class="text-gray-400 text-xs italic empty-msg">No data yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Religion Distribution --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-base font-bold text-gray-800">Religion</h3>
                    <button type="button" onclick="addRow('religion-rows', 'religion')" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">
                        <i class="fas fa-plus mr-1"></i> Add
                    </button>
                </div>
                <div class="p-4">
                    <div id="religion-rows" class="space-y-2">
                        @php $religions = $options['institute.demographics.religion'] ?? []; @endphp
                        @forelse($religions as $label => $value)
                            <div class="flex gap-2 items-center demographic-row">
                                <input type="text" name="religion[{{ $loop->index }}][label]" value="{{ $label }}" placeholder="Religion" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <input type="number" name="religion[{{ $loop->index }}][value]" value="{{ $value }}" placeholder="Count" class="w-20 px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="button" onclick="this.closest('.demographic-row').remove()" class="text-red-500 hover:text-red-700 text-sm p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        @empty
                            <p class="text-gray-400 text-xs italic empty-msg">No data yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function addRow(containerId, inputName) {
        const container = document.getElementById(containerId);
        const emptyMsg = container.querySelector('.empty-msg');
        if (emptyMsg) emptyMsg.remove();

        const index = container.querySelectorAll('.demographic-row').length;
        const div = document.createElement('div');
        div.className = 'flex gap-2 items-center demographic-row';
        div.innerHTML = `
            <input type="text" name="${inputName}[${index}][label]" placeholder="Label" class="flex-1 px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <input type="number" name="${inputName}[${index}][value]" placeholder="Value" class="w-20 px-3 py-2 text-sm rounded-md border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <button type="button" onclick="this.closest('.demographic-row').remove()" class="text-red-500 hover:text-red-700 text-sm p-1">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }
</script>
@endpush
@endsection

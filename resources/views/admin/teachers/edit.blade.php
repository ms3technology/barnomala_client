@extends('layouts.admin')

@section('title', 'Edit Teacher: ' . $teacher->teacher_name)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.teachers.index') }}" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Edit Teacher</h2>
            <p class="text-sm text-slate-500 mt-1 uppercase tracking-wider font-semibold">{{ $teacher->teacher_name }}</p>
        </div>
    </div>
</div>

<form action="{{ route('admin.teachers.update', $teacher) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Form Area -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-id-card text-indigo-500"></i>
                        Basic Identification
                    </h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Teacher Name <span class="text-red-500">*</span></label>
                            <input type="text" name="teacher_name" value="{{ old('teacher_name', $teacher->teacher_name) }}" required
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-800">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Designation <span class="text-red-500">*</span></label>
                            <input type="text" name="designation" value="{{ old('designation', $teacher->designation) }}" required
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-800">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Department</label>
                            <input type="text" name="department" value="{{ old('department', $teacher->department) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-800">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Teacher Code / ID</label>
                            <input type="text" name="teacher_code" value="{{ old('teacher_code', $teacher->teacher_code) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-phone-alt text-indigo-500"></i>
                        Contact & Status
                    </h3>
                </div>
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-800">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Official Email</label>
                            <input type="email" name="email" value="{{ old('email', $teacher->email) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-800">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Joining Date</label>
                            <input type="date" name="joining_date" value="{{ old('joining_date', $teacher->joining_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none font-medium text-slate-800">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700">Employment Status</label>
                            <div class="flex items-center gap-6 py-3">
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="status" value="1" {{ $teacher->status ? 'checked' : '' }} class="w-5 h-5 text-emerald-500 focus:ring-emerald-500/20">
                                    <span class="text-sm font-bold text-slate-600 group-hover:text-emerald-600">Active</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input type="radio" name="status" value="0" {{ !$teacher->status ? 'checked' : '' }} class="w-5 h-5 text-slate-400 focus:ring-slate-500/20 border-slate-300">
                                    <span class="text-sm font-bold text-slate-600 group-hover:text-slate-900">Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Area -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Photo Upload Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden group">
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2 text-sm uppercase tracking-wider">
                        Profile Image
                    </h3>
                </div>
                <div class="p-6">
                    <div id="image-dropzone" class="relative group cursor-pointer">
                        <input type="file" name="photo" id="photo-input" class="hidden" accept="image/*" onchange="previewImage(this)">
                        <div class="w-full aspect-square md:aspect-auto md:h-60 rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center gap-4 transition-all hover:bg-indigo-50 hover:border-indigo-200 group-hover:border-indigo-300" 
                             onclick="document.getElementById('photo-input').click()"
                             onkeyup="if(event.key === 'Enter') document.getElementById('photo-input').click()"
                             tabindex="0">
                            <div id="preview-container" class="{{ $teacher->photo ? 'flex' : 'hidden' }} absolute inset-0 rounded-xl bg-white overflow-hidden pointer-events-none">
                                <img id="preview-img" src="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : '' }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold uppercase tracking-widest bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full border border-white/20">Change Photo</span>
                                </div>
                            </div>
                            <div id="upload-prompt" class="flex flex-col items-center gap-3">
                                <div class="w-16 h-16 rounded-full bg-white shadow-md flex items-center justify-center text-indigo-500 transition-transform group-hover:scale-110">
                                    <i class="fas fa-camera text-2xl"></i>
                                </div>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Update Portrait</p>
                            </div>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-4 text-center leading-relaxed">
                        Recommended: Square format (1:1)<br>JPG, PNG up to 2MB
                    </p>
                </div>
            </div>

            <!-- Sorting/Priority Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-500 uppercase tracking-widest">Display Priority (Index)</label>
                        <input type="number" name="priority_index" value="{{ old('priority_index', $teacher->priority_index) }}" required
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold text-slate-800 outline-none transition-all">
                        <p class="text-[9px] text-slate-400">Lower numbers appear first in lists.</p>
                    </div>

                    <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-lg shadow-indigo-200 font-black uppercase tracking-widest text-sm transition-all transform hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3">
                        <i class="fas fa-save"></i>
                        Update Teacher
                    </button>
                    
                    <a href="{{ route('admin.teachers.index') }}" class="block w-full text-center py-4 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all">
                        Cancel
                    </a>

                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <p class="text-[10px] text-slate-400 text-center">Last Modified: {{ $teacher->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview-container');
    const img = document.getElementById('preview-img');
    const prompt = document.getElementById('upload-prompt');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
            preview.classList.add('flex');
            prompt.classList.add('opacity-0');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

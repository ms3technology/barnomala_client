<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pb-20 font-bn reveal">
    <div class="flex items-center gap-4 mb-8">
        <h2 class="text-2xl font-bold text-gray-900">সম্মানিত শিক্ষকবৃন্দ</h2>
        <div class="flex-1 h-px bg-gray-200"></div>
        <a href="{{ route('teachers.index') }}" class="text-indigo-600 text-sm font-bold hover:underline">সকল শিক্ষক</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-6">
        @foreach($teachers as $teacher)
            <a href="{{ route('teachers.show', $teacher->id) }}" class="block">
                <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-xl transition duration-300 border border-slate-100">
                    <div class="aspect-square overflow-hidden bg-slate-100">
                        @if($teacher->photo)
                            <img src="{{ $teacher->photo }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $teacher->teacher_name }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fas fa-user-tie text-4xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-3 text-center">
                        <h4 class="font-bold text-gray-900 text-xs md:text-sm mb-0.5 truncate">{{ $teacher->teacher_name }}</h4>
                        <p class="text-[10px] md:text-xs text-indigo-600 font-semibold truncate">{{ $teacher->designation }}</p>
                        <p class="text-[9px] text-gray-400 truncate">{{ $teacher->department }}</p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>

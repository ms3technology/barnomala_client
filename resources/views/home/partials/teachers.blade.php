<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16 pb-20 font-bn">
    <div class="flex items-center gap-4 mb-8">
        <h2 class="text-2xl font-bold text-gray-900">সম্মানিত শিক্ষকবৃন্দ</h2>
        <div class="flex-1 h-px bg-gray-200"></div>
        <a href="#" class="text-indigo-600 text-sm font-bold hover:underline">সকল শিক্ষক</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-6">
        @foreach($teachers as $teacher)
            <div class="bg-white rounded-xl shadow-md overflow-hidden group hover:shadow-xl transition duration-300">
                <div class="aspect-3/4 overflow-hidden">
                    <img src="{{ $teacher['teacherImg'] ?? $teacher->teacherImg }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $teacher['teacherName'] ?? $teacher->teacherName }}">
                </div>
                <div class="p-4 text-center">
                    <h4 class="font-bold text-gray-900 text-sm mb-1 truncate">{{ $teacher['teacherName'] ?? $teacher->teacherName }}</h4>
                    <p class="text-xs text-gray-500 truncate">{{ $teacher['teacherDesignation'] ?? $teacher->teacherDesignation }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>

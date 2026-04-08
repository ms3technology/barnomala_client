@extends('layouts.admin')

@section('title', 'Institution Settings')

@push('header_actions')
<button type="submit" form="settings-form" class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-bold rounded-lg shadow-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
    <i class="fas fa-save mr-2"></i>
    Save Settings
</button>
@endpush

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ activeTab: 'about' }">
    <div class="p-8 text-gray-900">
        <!-- Tabs Navigation -->
        <div class="flex space-x-1 bg-slate-100 p-1 rounded-xl mb-8">
            @foreach(['about', 'identity', 'contact'] as $category)
                @if(isset($options[$category]))
                    <button @click="activeTab = '{{ $category }}'"
                            :class="activeTab === '{{ $category }}' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-50'"
                            class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 uppercase tracking-wider">
                        {{ $category }}
                    </button>
                @endif
            @endforeach
        </div>

        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-12">
                @foreach(['about', 'identity', 'contact'] as $category)
                    @if(isset($options[$category]))
                    <div x-show="activeTab === '{{ $category }}'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="animate-fade-in">
                        
                        <div class="flex items-center space-x-4 mb-6">
                            <h2 class="text-xl font-bold text-slate-800 uppercase tracking-wide">{{ $category }}</h2>
                            <div class="flex-1 h-px bg-slate-200"></div>
                        </div>

                        <div class="w-full md:w-2/3 space-y-3">
                            @if ($category === 'about')
                                <div class="grid grid-cols-3 border border-slate-200 rounded-xl p-4 bg-slate-50/50 hover:bg-white transition-all duration-200">
                                    <label class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">
                                        About Us Image
                                    </label>

                                    <div class="col-span-2 space-y-4" x-data="{ photoName: null, photoPreview: null }">
                                        <div class="mt-2" x-show="! photoPreview">
                                            @php
                                                $aboutImage = \App\Models\Option::where('option_key', 'institute.about.image_json')->first();
                                                $imageUrl = $aboutImage ? (json_decode($aboutImage->option_value, true)['url'] ?? asset('images/about-us-1.png')) : asset('images/about-us-1.png');
                                            @endphp
                                            <img src="{{ $imageUrl }}" class="h-40 w-full object-cover rounded-xl shadow-sm border border-slate-200">
                                        </div>

                                        <div class="mt-2" x-show="photoPreview" style="display: none;">
                                            <span class="block h-40 w-full rounded-xl shadow-sm border border-slate-200 bg-cover bg-no-repeat bg-center"
                                                  x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                            </span>
                                        </div>

                                        <input type="file" class="hidden"
                                               name="about_image"
                                               x-ref="photo"
                                               x-on:change="
                                                    photoName = $refs.photo.files[0].name;
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => {
                                                        photoPreview = e.target.result;
                                                    };
                                                    reader.readAsDataURL($refs.photo.files[0]);
                                               ">

                                        <button type="button" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg font-bold text-xs text-slate-700 uppercase tracking-widest shadow-sm hover:text-slate-500 focus:outline-none focus:border-indigo-300 focus:ring focus:ring-indigo-200 active:text-slate-800 active:bg-slate-50 disabled:opacity-25 transition" 
                                                x-on:click.prevent="$refs.photo.click()">
                                            Select New Image
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @foreach($options[$category] as $option)
                                <div class="grid grid-cols-3 border border-slate-200 rounded-xl p-4 bg-slate-50/50 hover:bg-white transition-all duration-200">
                                    <label for="{{ str_replace('.', '_', $option->option_key) }}" class="block text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">
                                        {{ ucwords(str_replace(['institute', $category, '.', '_'], ' ', $option->option_key)) }}
                                    </label>

                                    <div class="col-span-2">
                                        @if($option->value_type === 'boolean')
                                            <select name="{{ str_replace('.', '_', $option->option_key) }}" id="{{ str_replace('.', '_', $option->option_key) }}" class="block w-full text-sm rounded-lg border border-slate-300 bg-white/80 backdrop-blur-sm px-4 py-2.5 text-slate-800 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 hover:border-slate-400">
                                                <option value="1" {{ $option->option_value == '1' ? 'selected' : '' }}>Enabled</option>
                                                <option value="0" {{ $option->option_value == '0' ? 'selected' : '' }}>Disabled</option>
                                            </select>
                                        @elseif($option->value_type === 'integer')
                                            <input type="number" 
                                                name="{{ str_replace('.', '_', $option->option_key) }}" 
                                                id="{{ str_replace('.', '_', $option->option_key) }}" 
                                                value="{{ $option->option_value }}"
                                                class="block w-full text-sm rounded-lg border border-slate-300 bg-white/80 backdrop-blur-sm px-4 py-2.5 text-slate-800 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 hover:border-slate-400">
                                        @elseif(str_contains($option->option_key, 'text') || str_contains($option->option_key, 'address'))
                                            <textarea name="{{ str_replace('.', '_', $option->option_key) }}" 
                                                    id="{{ str_replace('.', '_', $option->option_key) }}" 
                                                    rows="3" 
                                                    class="block w-full text-sm rounded-lg border border-slate-300 bg-white/80 backdrop-blur-sm px-4 py-2.5 text-slate-800 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 hover:border-slate-400">{{ $option->option_value }}</textarea>
                                        @else
                                            <input type="text" 
                                                name="{{ str_replace('.', '_', $option->option_key) }}" 
                                                id="{{ str_replace('.', '_', $option->option_key) }}" 
                                                value="{{ $option->option_value }}"
                                                class="block w-full text-sm rounded-lg border border-slate-300 bg-white/80 backdrop-blur-sm px-4 py-2.5 text-slate-800 placeholder-slate-400 shadow-sm transition-all duration-200 focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 hover:border-slate-400">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </form>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
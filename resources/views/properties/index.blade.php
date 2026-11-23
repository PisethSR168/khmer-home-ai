@extends('layouts.app')

@section('content')

<div class="bg-slate-100/60 border-b border-slate-200 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-extrabold text-slate-900">Explore Available Rentals in Siem Reap</h1>
        <p class="text-slate-500 text-sm mt-1">Browse verified rooms, garden studios, and Khmer villas in Sala Kamreuk, Wat Bo, Svay Dangkum, and Slor Kram.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ mobileFiltersOpen: false }">
    <div class="flex flex-col lg:flex-row gap-8">

        <!-- Mobile Filter Button -->
        <div class="lg:hidden flex items-center justify-between mb-4">
            <button @click="mobileFiltersOpen = !mobileFiltersOpen" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 shadow-sm">
                <i data-lucide="sliders-horizontal" class="w-4 h-4 text-emerald-600"></i> Filter Properties
            </button>
            <span class="text-xs text-slate-500 font-medium">{{ $properties->total() }} properties found</span>
        </div>

        <!-- Sidebar Filters -->
        <aside :class="mobileFiltersOpen ? 'block' : 'hidden lg:block'" class="w-full lg:w-72 shrink-0 space-y-6 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm h-fit">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="filter" class="w-4 h-4 text-emerald-600"></i> Filters
                </h3>
                <a href="{{ route('properties.index') }}" class="text-xs text-slate-400 hover:text-emerald-600">Reset All</a>
            </div>

            <form action="{{ route('properties.index') }}" method="GET" class="space-y-5 text-sm">
                <!-- Search Keyword -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Keyword</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Street, landmark, Wat Bo..."
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <!-- Siem Reap Communes Dropdown -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Siem Reap Commune</label>
                    <select name="district" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Siem Reap Areas</option>
                        <option value="Sala Kamreuk" {{ request('district') == 'Sala Kamreuk' ? 'selected' : '' }}>Sala Kamreuk (Artisan Hub)</option>
                        <option value="Wat Bo" {{ request('district') == 'Wat Bo' ? 'selected' : '' }}>Wat Bo (Cafes & River)</option>
                        <option value="Svay Dangkum" {{ request('district') == 'Svay Dangkum' ? 'selected' : '' }}>Svay Dangkum (Pub St)</option>
                        <option value="Slor Kram" {{ request('district') == 'Slor Kram' ? 'selected' : '' }}>Slor Kram (Angkor)</option>
                        <option value="Kork Chak" {{ request('district') == 'Kork Chak' ? 'selected' : '' }}>Kork Chak</option>
                        <option value="Chreav" {{ request('district') == 'Chreav' ? 'selected' : '' }}>Chreav (Countryside)</option>
                    </select>
                </div>

                <!-- Property Type -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Property Type</label>
                    <select name="property_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="">All Types</option>
                        <option value="studio" {{ request('property_type') == 'studio' ? 'selected' : '' }}>Garden Studio</option>
                        <option value="room" {{ request('property_type') == 'room' ? 'selected' : '' }}>Private Room</option>
                        <option value="apartment" {{ request('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="house" {{ request('property_type') == 'house' ? 'selected' : '' }}>Khmer Villa / House</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Price Range ($/mo)</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min $30" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max $100" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                    </div>
                    <div class="flex items-center gap-1.5 mt-2">
                        <a href="{{ route('properties.index', ['min_price' => 30, 'max_price' => 50]) }}" class="text-[11px] px-2 py-0.5 rounded bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 transition">$30 - $50</a>
                        <a href="{{ route('properties.index', ['min_price' => 50, 'max_price' => 80]) }}" class="text-[11px] px-2 py-0.5 rounded bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 transition">$50 - $80</a>
                        <a href="{{ route('properties.index', ['min_price' => 80, 'max_price' => 100]) }}" class="text-[11px] px-2 py-0.5 rounded bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-slate-600 transition">$80 - $100</a>
                    </div>
                </div>

                <!-- Bedrooms -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bedrooms</label>
                    <div class="grid grid-cols-4 gap-1.5 text-center">
                        @foreach([1, 2, 3, 4] as $bed)
                            <label class="cursor-pointer">
                                <input type="radio" name="bedrooms" value="{{ $bed }}" {{ request('bedrooms') == $bed ? 'checked' : '' }} class="peer sr-only">
                                <div class="py-1.5 rounded-lg border border-slate-200 peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 text-xs font-semibold">
                                    {{ $bed }}+
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Amenities Checklist -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Amenities</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->slug }}"
                                       {{ in_array($amenity->slug, (array) request('amenities', [])) ? 'checked' : '' }}
                                       class="rounded text-emerald-600 focus:ring-emerald-500">
                                <span>{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Boolean toggles -->
                <div class="space-y-2 pt-2 border-t border-slate-100 text-xs text-slate-700">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="wifi_included" value="1" {{ request('wifi_included') ? 'checked' : '' }} class="rounded text-emerald-600">
                        <span>Free Wi-Fi Included</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="pet_friendly" value="1" {{ request('pet_friendly') ? 'checked' : '' }} class="rounded text-emerald-600">
                        <span>Pet Friendly</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-sm transition">
                    Apply Filters
                </button>
            </form>
        </aside>

        <!-- Main Listings Content -->
        <div class="flex-1">

            <!-- Top Results Toolbar -->
            <div class="flex flex-col sm:flex-row items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
                <div class="text-sm text-slate-600">
                    Showing <strong class="text-slate-900">{{ $properties->firstItem() ?? 0 }}-{{ $properties->lastItem() ?? 0 }}</strong> of <strong class="text-slate-900">{{ $properties->total() }}</strong> verified rentals in Siem Reap
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-600">
                    <label for="sort-select" class="font-medium">Sort by:</label>
                    <form method="GET" action="{{ route('properties.index') }}" class="inline">
                        @foreach(request()->except('sort') as $k => $v)
                            @if(is_array($v))
                                @foreach($v as $subV)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $subV }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <select id="sort-select" name="sort" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-semibold focus:outline-none">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest Listings</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Property Cards Grid -->
            @if($properties->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($properties as $property)
                        <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-lg transition duration-200 flex flex-col justify-between group">
                            
                            <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
                                <img src="{{ $property->primary_image_url }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                
                                <span class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-900/80 backdrop-blur-md text-white">
                                    {{ ucfirst($property->property_type) }}
                                </span>

                                <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1 rounded-xl shadow-sm text-slate-900 font-extrabold text-base">
                                    ${{ number_format($property->price_per_month, 0) }}<span class="text-[11px] font-normal text-slate-500">/mo</span>
                                </div>

                                <div class="absolute bottom-3 right-3 bg-emerald-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-lg backdrop-blur-md">
                                    AI: {{ $property->ai_completeness_score }}%
                                </div>
                            </div>

                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mb-1">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                        <span class="truncate">{{ $property->khan_district }}, Siem Reap</span>
                                    </div>
                                    <h3 class="font-bold text-slate-900 text-sm line-clamp-1 group-hover:text-emerald-600 transition">
                                        <a href="{{ route('properties.show', $property->slug) }}">{{ $property->title }}</a>
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-2 mt-1">
                                        {{ $property->description }}
                                    </p>
                                </div>

                                <div class="mt-4 pt-3 border-t border-slate-100">
                                    <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                                        <span>{{ $property->bedrooms }} Bed • {{ $property->bathrooms }} Bath</span>
                                        <span>EDC: ${{ $property->electricity_cost_per_kwh ?? '0.22' }}/kWh</span>
                                    </div>
                                    <a href="{{ route('properties.show', $property->slug) }}" class="mt-3 w-full py-2 bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-semibold text-xs rounded-xl flex items-center justify-center gap-1 transition">
                                        View Room <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $properties->links() }}
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="search-x" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-bold text-slate-800 text-lg">No matching rooms in Siem Reap</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">Try broadening your price filters or removing specific commune restrictions.</p>
                    <a href="{{ route('properties.index') }}" class="mt-4 inline-block px-4 py-2 bg-emerald-600 text-white font-semibold text-xs rounded-lg">
                        Clear All Filters
                    </a>
                </div>
            @endif

        </div>
    </div>
</div>

@endsection

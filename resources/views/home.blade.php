@extends('layouts.app')

@section('content')

<!-- Hero Section with AI Search - Siem Reap Focus -->
<section class="relative bg-gradient-to-b from-emerald-50 via-teal-50/40 to-slate-50 pt-12 pb-20 overflow-hidden">
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-200/50 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-teal-200/40 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-sm mb-4">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-600"></i>
                Room SR • បន្ទប់ជួល សៀមរាប • AI Rental Platform
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Find Your Perfect <span class="bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">Room in Siem Reap</span>
            </h1>

            <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Discover clean, verified rooms and studios in Wat Bo, Sala Kamreuk, Svay Dangkum, and Chreav from <strong class="text-emerald-700 font-bold">$30 to $100 / month</strong>. Genuine EDC electricity rates ($0.20/kWh), high-speed Wi-Fi, and real room photography.
            </p>

            <!-- Natural Language AI Search Box -->
            <div class="mt-8 bg-white p-3 sm:p-4 rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-200 max-w-2xl mx-auto">
                <form action="{{ route('ai.search') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <i data-lucide="sparkles" class="w-5 h-5 text-emerald-500 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="q" placeholder="Try: Clean room in Wat Bo with private bathroom under $40..."
                               class="w-full pl-11 pr-4 py-3 bg-slate-50 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                    </div>
                    <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm rounded-xl flex items-center justify-center gap-2 shadow-sm transition">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>AI Search</span>
                    </button>
                </form>

                <!-- Siem Reap Prompt Chips -->
                <div class="mt-3 flex items-center gap-1.5 flex-wrap text-xs text-slate-500 justify-center">
                    <span class="font-medium">Siem Reap searches:</span>
                    <a href="{{ route('ai.search', ['q' => 'Clean room in Wat Bo under $40']) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 rounded-full transition">Wat Bo &lt; $40</a>
                    <a href="{{ route('ai.search', ['q' => 'Quiet worker room in Chreav $45']) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 rounded-full transition">Chreav $45/mo</a>
                    <a href="{{ route('ai.search', ['q' => 'Cozy room with hot shower in Svay Dangkum under $65']) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 rounded-full transition">Svay Dangkum &lt; $65</a>
                    <a href="{{ route('ai.search', ['q' => 'Air-conditioned room in Sala Kamreuk under $90']) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 rounded-full transition">Sala Kamreuk Aircon &lt; $90</a>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="mt-12 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto pt-6 border-t border-slate-200/80">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['total_listings'] }}+</div>
                    <div class="text-xs text-slate-500 font-medium">Siem Reap Rentals</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $stats['verified_landlords'] }}</div>
                    <div class="text-xs text-slate-500 font-medium">Verified Local Hosts</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-extrabold text-slate-900">{{ $stats['happy_renters'] }}+</div>
                    <div class="text-xs text-slate-500 font-medium">Viewings in Siem Reap</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600">${{ $stats['avg_price'] }}/mo</div>
                    <div class="text-xs text-slate-500 font-medium">Average Monthly Rent</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties Section -->
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
        <div>
            <div class="flex items-center gap-2 text-emerald-600 text-xs font-bold uppercase tracking-wider">
                <i data-lucide="award" class="w-4 h-4"></i>
                Hand-Picked in Siem Reap
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Featured Rooms & Studios ($30 – $100 / mo)</h2>
            <p class="text-slate-500 text-sm mt-1">Verified real room photos, low EDC electricity rates ($0.20/kWh), and secure parking across Siem Reap communes.</p>
        </div>
        <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:text-emerald-700 group">
            View All Siem Reap Rooms
            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"></i>
        </a>
    </div>

    <!-- Properties Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
        @foreach($featuredProperties as $property)
            <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col group">
                
                <!-- Image Container -->
                <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                    <img src="{{ $property->primary_image_url }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    
                    <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase tracking-wide bg-slate-900/80 backdrop-blur-md text-white">
                            {{ ucfirst($property->property_type) }}
                        </span>
                        @if($property->is_featured)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-white shadow-sm flex items-center gap-1">
                                <i data-lucide="star" class="w-3 h-3 fill-current"></i> Featured
                            </span>
                        @endif
                    </div>

                    <!-- Favorite Button -->
                    @auth
                        <form action="{{ route('favorites.toggle', $property->id) }}" method="POST" class="absolute top-3 right-3">
                            @csrf
                            <button type="submit" class="w-9 h-9 rounded-full bg-white/90 backdrop-blur-md flex items-center justify-center text-slate-700 hover:text-rose-500 hover:bg-white shadow-sm transition">
                                <i data-lucide="heart" class="w-4 h-4 {{ $property->isFavoritedBy(auth()->user()) ? 'text-rose-500 fill-current' : '' }}"></i>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/90 backdrop-blur-md flex items-center justify-center text-slate-700 hover:text-rose-500 hover:bg-white shadow-sm transition">
                            <i data-lucide="heart" class="w-4 h-4"></i>
                        </a>
                    @endauth

                    <!-- Price Tag Overlay -->
                    <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-xl shadow-md border border-slate-100 flex items-baseline gap-1">
                        <span class="text-lg font-black text-slate-900">${{ number_format($property->price_per_month, 0) }}</span>
                        <span class="text-xs text-slate-500 font-medium">/ month</span>
                    </div>

                    <!-- AI Completeness Score Badge -->
                    <div class="absolute bottom-3 right-3 bg-emerald-600/90 text-white text-[10px] font-bold px-2 py-1 rounded-lg backdrop-blur-md flex items-center gap-1">
                        <i data-lucide="sparkles" class="w-3 h-3"></i>
                        AI: {{ $property->ai_completeness_score }}%
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Location -->
                        <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium mb-1.5">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                            <span class="truncate">{{ $property->khan_district }}, Siem Reap</span>
                        </div>

                        <!-- Title -->
                        <h3 class="font-bold text-slate-900 text-base line-clamp-1 group-hover:text-emerald-600 transition">
                            <a href="{{ route('properties.show', $property->slug) }}">
                                {{ $property->title }}
                            </a>
                        </h3>

                        <p class="text-xs text-slate-500 line-clamp-2 mt-1.5 leading-relaxed">
                            {{ $property->description }}
                        </p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="grid grid-cols-3 gap-2 text-center text-xs text-slate-600 font-semibold mb-3">
                            <div class="bg-slate-50 py-1.5 rounded-lg flex items-center justify-center gap-1">
                                <i data-lucide="bed" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $property->bedrooms }} Bed</span>
                            </div>
                            <div class="bg-slate-50 py-1.5 rounded-lg flex items-center justify-center gap-1">
                                <i data-lucide="bath" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $property->bathrooms }} Bath</span>
                            </div>
                            <div class="bg-slate-50 py-1.5 rounded-lg flex items-center justify-center gap-1">
                                <i data-lucide="maximize" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>{{ $property->size_sqm ? round($property->size_sqm) . 'm²' : 'Studio' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-[11px] text-slate-500">
                                EDC: <strong class="text-slate-700">${{ $property->electricity_cost_per_kwh ?? '0.22' }}/kWh</strong>
                            </div>
                            <a href="{{ route('properties.show', $property->slug) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
                                View Details <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Popular Areas in Siem Reap -->
<section class="py-16 bg-slate-100/70 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-emerald-600 text-xs font-bold uppercase tracking-wider">Siem Reap Neighborhoods</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Explore Rooms by Commune</h2>
            <p class="text-slate-500 text-sm mt-1">Quiet leafy villages, digital nomad hubs, and lively riverfront zones in Krong Siem Reap.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($districts as $district)
                <a href="{{ route('properties.index', ['district' => $district['name']]) }}" class="group relative rounded-2xl overflow-hidden aspect-[4/5] shadow-sm hover:shadow-lg transition">
                    <img src="{{ $district['image'] }}" alt="{{ $district['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-900/30 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white">
                        <div class="text-xs text-emerald-400 font-semibold">{{ $district['khmer'] }}</div>
                        <div class="font-extrabold text-base leading-tight">{{ $district['name'] }}</div>
                        <div class="text-[11px] text-slate-300 mt-0.5">{{ $district['count'] }} listings</div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Why Siem Reap Living with AI -->
<section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-emerald-600 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="cpu" class="w-4 h-4"></i> Designed for Siem Reap Life
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-2 tracking-tight">
                Living in the City of Angkor Made Simple
            </h2>
            <p class="text-slate-600 text-sm sm:text-base mt-4 leading-relaxed">
                Whether you're moving to Siem Reap for remote work, teaching, or hospitality management, Khmer Home AI connects you directly to verified hosts without tourist markups.
            </p>

            <div class="mt-8 space-y-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <i data-lucide="wifi" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Verified Remote Work Wi-Fi</h4>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">High-speed fiber optic internet tested in our listings for smooth Zoom calls and remote engineering.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center shrink-0">
                        <i data-lucide="zap" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Official EDC Electricity Rates</h4>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">All listings disclose electricity rates ($0.20-$0.22/kWh). No hidden surcharges.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <i data-lucide="waves" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">Tropical Gardens & Swimming Pools</h4>
                        <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Filter specifically for pet-friendly properties, private balconies, and garden pools.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('ai.assistant') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold transition">
                    <i data-lucide="message-square" class="w-4 h-4 text-emerald-400"></i>
                    Ask the Siem Reap AI Concierge
                </a>
            </div>
        </div>

        <!-- Chat Simulation -->
        <div class="bg-gradient-to-tr from-slate-900 to-slate-800 p-6 sm:p-8 rounded-3xl text-white shadow-2xl relative">
            <div class="flex items-center justify-between pb-6 border-b border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-slate-950 font-bold">
                        <i data-lucide="bot" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm">Siem Reap Rental Concierge</h4>
                        <p class="text-xs text-emerald-400 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Grounded in Siem Reap Database
                        </p>
                    </div>
                </div>
            </div>

            <div class="py-6 space-y-4 text-xs">
                <div class="flex gap-2.5 max-w-[85%]">
                    <div class="w-6 h-6 rounded-full bg-slate-700 flex items-center justify-center text-[10px] shrink-0">User</div>
                    <div class="bg-slate-700/80 p-3 rounded-2xl rounded-tl-none text-slate-200">
                        "I'm a remote worker looking for a quiet studio in Sala Kamreuk with pool and good wifi under $300."
                    </div>
                </div>

                <div class="flex gap-2.5 max-w-[90%] ml-auto flex-row-reverse">
                    <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-[10px] shrink-0 text-slate-950">AI</div>
                    <div class="bg-emerald-600/30 border border-emerald-500/40 p-3.5 rounded-2xl rounded-tr-none text-slate-100 space-y-2">
                        <p>I found a verified listing in Sala Kamreuk matching your criteria:</p>
                        <div class="bg-slate-900/80 p-2.5 rounded-xl border border-slate-700 flex items-center gap-3">
                            <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=150&q=80" class="w-12 h-12 rounded-lg object-cover">
                            <div>
                                <div class="font-bold text-white">Boutique Garden Studio with Pool</div>
                                <div class="text-[11px] text-emerald-400 font-semibold">$280/mo • Sala Kamreuk</div>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-300">4 mins from Wat Bo village. Electricity is $0.22/kWh, Wi-Fi and pool access included. Would you like to request a viewing?</p>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-700 flex items-center justify-between text-xs text-slate-400">
                <span>Ask in English or Khmer</span>
                <a href="{{ route('ai.assistant') }}" class="text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1">
                    Open Full Assistant <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Landlord Call-to-Action Banner -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
    <div class="rounded-3xl bg-gradient-to-r from-emerald-700 via-teal-800 to-slate-900 p-8 sm:p-12 text-white relative overflow-hidden shadow-2xl">
        <div class="max-w-2xl relative z-10">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/20 text-emerald-200 mb-3">For Landlords in Siem Reap</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Have a Room, Studio, or Villa in Siem Reap to Rent?</h2>
            <p class="mt-3 text-slate-200 text-sm sm:text-base leading-relaxed">
                List your property on Khmer Home AI. Use our AI description assistant to attract quality long-term tenants, digital nomads, and expats.
            </p>
            <div class="mt-6 flex flex-wrap gap-4">
                @auth
                    @if(auth()->user()->isLandlord())
                        <a href="{{ route('landlord.properties.create') }}" class="px-6 py-3 bg-white text-emerald-800 hover:bg-slate-100 font-bold text-sm rounded-xl transition shadow-lg">
                            + List a Property in Siem Reap
                        </a>
                    @else
                        <a href="{{ route('properties.index') }}" class="px-6 py-3 bg-white text-emerald-800 hover:bg-slate-100 font-bold text-sm rounded-xl transition shadow-lg">
                            Browse Siem Reap Rooms
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-emerald-800 hover:bg-slate-100 font-bold text-sm rounded-xl transition shadow-lg">
                        Register as Siem Reap Landlord
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

@endsection

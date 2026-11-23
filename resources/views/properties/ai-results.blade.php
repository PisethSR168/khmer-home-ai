@extends('layouts.app')

@section('content')

<div class="bg-gradient-to-r from-emerald-900 to-slate-900 text-white py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2">
            <i data-lucide="sparkles" class="w-4 h-4"></i> AI Semantic Query Analysis
        </div>
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">"{{ $query }}"</h1>
        <p class="text-slate-300 text-sm mt-1">Our AI processed your natural language request and cross-referenced active Cambodian rental databases.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        <!-- AI Insight Box -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center gap-2 text-emerald-600 font-bold text-sm">
                    <i data-lucide="brain" class="w-5 h-5"></i> AI Extracted Criteria
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Target District</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $criteria['district'] ?? 'Any District in Phnom Penh' }}</span>
                    </div>

                    @if(!empty($criteria['max_price']))
                        <div>
                            <span class="text-slate-400 block font-medium">Budget Ceiling</span>
                            <span class="font-bold text-emerald-600 text-sm">${{ $criteria['max_price'] }} / month</span>
                        </div>
                    @endif

                    @if(!empty($criteria['property_type']))
                        <div>
                            <span class="text-slate-400 block font-medium">Property Style</span>
                            <span class="font-bold text-slate-800 text-sm">{{ ucfirst($criteria['property_type']) }}</span>
                        </div>
                    @endif

                    @if(!empty($criteria['amenity_slugs']))
                        <div>
                            <span class="text-slate-400 block font-medium">Desired Amenities</span>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @foreach($criteria['amenity_slugs'] as $slug)
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-semibold text-[11px]">{{ $slug }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600 leading-relaxed">
                    <strong>AI Note:</strong> {{ $explanation }}
                </div>

                <a href="{{ route('ai.assistant') }}" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-semibold flex items-center justify-center gap-1.5 transition">
                    <i data-lucide="message-square" class="w-3.5 h-3.5 text-emerald-400"></i> Continue in AI Chat
                </a>
            </div>
        </div>

        <!-- Matched Properties Grid -->
        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-bold text-slate-900">Found {{ $total }} Real Rental Matches</h2>
                <a href="{{ route('properties.index') }}" class="text-xs font-semibold text-emerald-600 hover:underline">View standard catalog</a>
            </div>

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
                            </div>

                            <div class="p-5 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mb-1">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                                        <span class="truncate">{{ $property->khan_district }}</span>
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
                                        <span>Electricity: ${{ $property->electricity_cost_per_kwh ?? '0.25' }}</span>
                                    </div>
                                    <a href="{{ route('properties.show', $property->slug) }}" class="mt-3 w-full py-2 bg-slate-50 hover:bg-emerald-50 hover:text-emerald-700 text-slate-700 font-semibold text-xs rounded-xl flex items-center justify-center gap-1 transition">
                                        View Details <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200">
                    <i data-lucide="bot" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                    <h3 class="font-bold text-slate-800 text-base">No precise match found for this AI query</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">Try asking in simpler terms or check our complete rental catalog.</p>
                    <a href="{{ route('properties.index') }}" class="mt-4 inline-block px-4 py-2 bg-emerald-600 text-white font-semibold text-xs rounded-lg">
                        Browse All Rooms
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection

@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ bookingModalOpen: false, activeImage: '{{ $property->primary_image_url }}' }">

    <!-- Breadcrumb & Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
        <nav class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Home</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-300"></i>
            <a href="{{ route('properties.index') }}" class="hover:text-emerald-600">Rentals</a>
            <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-300"></i>
            <span class="text-slate-800 font-medium truncate">{{ $property->khan_district }}</span>
        </nav>

        <div class="flex items-center gap-3">
            @auth
                <form action="{{ route('favorites.toggle', $property->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-700 hover:text-rose-600 hover:border-rose-200 transition">
                        <i data-lucide="heart" class="w-4 h-4 {{ $property->isFavoritedBy(auth()->user()) ? 'text-rose-500 fill-current' : '' }}"></i>
                        <span>{{ $property->isFavoritedBy(auth()->user()) ? 'Saved' : 'Save to Favorites' }}</span>
                    </button>
                </form>
            @endauth

            <button @click="bookingModalOpen = true" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                Request Viewing
            </button>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left 2 Cols: Gallery, Details, Amenities, Reviews -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Gallery -->
            <div class="space-y-3">
                <div class="aspect-[16/10] rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 relative">
                    <img :src="activeImage" alt="{{ $property->title }}" class="w-full h-full object-cover">
                    
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-900/80 backdrop-blur-md text-white">
                        {{ ucfirst($property->property_type) }}
                    </span>

                    <span class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-sm flex items-center gap-1">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> AI Quality: {{ $property->ai_completeness_score }}%
                    </span>
                </div>

                <!-- Thumbnails -->
                @if($property->images->count() > 1)
                    <div class="flex items-center gap-2 overflow-x-auto pb-2">
                        @foreach($property->images as $img)
                            <button @click="activeImage = '{{ $img->url }}'" class="w-20 h-16 rounded-xl overflow-hidden shrink-0 border-2 transition" :class="activeImage === '{{ $img->url }}' ? 'border-emerald-600 ring-2 ring-emerald-500/30' : 'border-transparent opacity-70 hover:opacity-100'">
                                <img src="{{ $img->url }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Property Headline -->
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold mb-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i>
                    <span>{{ $property->street_address }}, {{ $property->sangkat_commune }}, {{ $property->khan_district }}, {{ $property->city }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                    {{ $property->title }}
                </h1>
            </div>

            <!-- Specs Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white p-4 rounded-xl border border-slate-200 text-center">
                    <i data-lucide="bed" class="w-5 h-5 text-emerald-600 mx-auto mb-1"></i>
                    <div class="text-xs text-slate-400 font-medium">Bedrooms</div>
                    <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $property->bedrooms }} {{ Str::plural('Bed', $property->bedrooms) }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 text-center">
                    <i data-lucide="bath" class="w-5 h-5 text-emerald-600 mx-auto mb-1"></i>
                    <div class="text-xs text-slate-400 font-medium">Bathrooms</div>
                    <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $property->bathrooms }} {{ Str::plural('Bath', $property->bathrooms) }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 text-center">
                    <i data-lucide="maximize" class="w-5 h-5 text-emerald-600 mx-auto mb-1"></i>
                    <div class="text-xs text-slate-400 font-medium">Area Size</div>
                    <div class="font-bold text-slate-800 text-sm mt-0.5">{{ $property->size_sqm ? round($property->size_sqm) . ' m²' : 'N/A' }}</div>
                </div>
                <div class="bg-white p-4 rounded-xl border border-slate-200 text-center">
                    <i data-lucide="sofa" class="w-5 h-5 text-emerald-600 mx-auto mb-1"></i>
                    <div class="text-xs text-slate-400 font-medium">Furnished</div>
                    <div class="font-bold text-slate-800 text-sm mt-0.5">{{ ucfirst(str_replace('_', ' ', $property->furnished_status)) }}</div>
                </div>
            </div>

            <!-- Utility Rates Breakdown -->
            <div class="bg-emerald-50/70 border border-emerald-200 p-5 rounded-2xl">
                <h3 class="font-bold text-emerald-900 text-sm flex items-center gap-2 mb-3">
                    <i data-lucide="zap" class="w-4 h-4 text-emerald-600"></i>
                    Transparent Utility Rates (No Surprise Landlord Markups)
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block font-medium">Electricity (EDC)</span>
                        <strong class="text-emerald-900 text-sm">${{ $property->electricity_cost_per_kwh ?? '0.25' }} / kWh</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block font-medium">Water (PPWSA)</span>
                        <strong class="text-emerald-900 text-sm">${{ $property->water_cost_per_m3 ?? '0.40' }} / m³</strong>
                    </div>
                    <div>
                        <span class="text-slate-500 block font-medium">Wi-Fi Internet</span>
                        <strong class="text-emerald-900 text-sm">{{ $property->wifi_included ? 'Included in Rent' : 'Available / Separate' }}</strong>
                    </div>
                </div>
            </div>

            <!-- AI Summary Callout -->
            @if($property->ai_summary)
                <div class="bg-slate-900 text-white p-5 rounded-2xl shadow-md">
                    <div class="flex items-center gap-2 text-xs font-bold text-emerald-400 mb-2">
                        <i data-lucide="sparkles" class="w-4 h-4"></i> AI Property Analysis Summary
                    </div>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        "{{ $property->ai_summary }}"
                    </p>
                </div>
            @endif

            <!-- Description -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200">
                <h3 class="font-bold text-slate-900 text-base mb-3">About this Accommodation</h3>
                <div class="prose prose-slate text-sm leading-relaxed text-slate-600 whitespace-pre-line">
                    {{ $property->description }}
                </div>
            </div>

            <!-- Amenities Grid -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200">
                <h3 class="font-bold text-slate-900 text-base mb-4">Included Amenities & Facilities</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($property->amenities as $amenity)
                        <div class="flex items-center gap-2.5 p-2.5 rounded-xl bg-slate-50 text-xs font-semibold text-slate-700">
                            <i data-lucide="{{ $amenity->icon ?? 'check' }}" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                            <span>{{ $amenity->name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reviews -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-base">Tenant Reviews ({{ $property->reviews->count() }})</h3>
                    <div class="flex items-center gap-1 text-amber-500 font-bold text-sm">
                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                        <span>5.0</span>
                    </div>
                </div>

                @if($property->reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($property->reviews as $review)
                            <div class="border-t border-slate-100 pt-3">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-xs text-slate-800">{{ $review->user->name }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">No reviews yet for this listing. Be the first tenant to submit a review after viewing!</p>
                @endif
            </div>

        </div>

        <!-- Right Col: Price, Landlord Card, Book Viewing Action -->
        <div class="space-y-6">

            <!-- Pricing & Action Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm sticky top-24 space-y-5">
                <div class="border-b border-slate-100 pb-4">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Monthly Rent</div>
                    <div class="flex items-baseline gap-1 mt-1">
                        <span class="text-3xl font-black text-slate-900">${{ number_format($property->price_per_month, 0) }}</span>
                        <span class="text-xs text-slate-500 font-medium">/ month</span>
                    </div>
                    @if($property->deposit_amount)
                        <div class="text-xs text-slate-500 mt-1">Security Deposit: <strong>${{ number_format($property->deposit_amount, 0) }}</strong> (1 month)</div>
                    @endif
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Lease Term:</span>
                        <strong class="text-slate-800">6 - 12 Months</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Availability:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">
                            Available Now
                        </span>
                    </div>
                </div>

                <button @click="bookingModalOpen = true" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    Schedule Room Viewing
                </button>

                <!-- Landlord Card -->
                <div class="pt-5 border-t border-slate-100">
                    <div class="text-xs text-slate-400 font-semibold uppercase tracking-wider mb-3">Landlord Profile</div>
                    <div class="flex items-center gap-3">
                        <img src="{{ $property->landlord->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($property->landlord->name) . '&background=059669&color=fff' }}" class="w-12 h-12 rounded-full object-cover border border-slate-200">
                        <div>
                            <div class="font-bold text-slate-900 text-sm flex items-center gap-1.5">
                                {{ $property->landlord->name }}
                                <i data-lucide="badge-check" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <div class="text-[11px] text-slate-500">
                                {{ $property->landlord->landlordProfile->business_name ?? 'Verified Property Owner' }}
                            </div>
                            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5 flex items-center gap-1">
                                <i data-lucide="star" class="w-3 h-3 fill-current text-amber-400"></i>
                                {{ $property->landlord->landlordProfile->rating ?? '5.00' }} Rating (Verified)
                            </div>
                        </div>
                    </div>

                    @if($property->landlord->landlordProfile?->telegram_username)
                        <div class="mt-3 p-2.5 bg-sky-50 rounded-xl border border-sky-100 flex items-center justify-between text-xs text-sky-800">
                            <span class="flex items-center gap-1.5 font-medium">
                                <i data-lucide="send" class="w-3.5 h-3.5 text-sky-600"></i> Telegram:
                            </span>
                            <span class="font-bold">{{ $property->landlord->landlordProfile->telegram_username }}</span>
                        </div>
                    @endif
                </div>

                <div class="text-[11px] text-slate-400 text-center">
                    Protected by Khmer Home AI Renter Safety Guarantee
                </div>
            </div>

        </div>
    </div>

    <!-- AI Recommended Similar Properties Carousel -->
    @if(isset($recommendations) && $recommendations->count() > 0)
        <div class="mt-16 pt-10 border-t border-slate-200">
            <div class="flex items-center gap-2 text-emerald-600 text-xs font-bold uppercase tracking-wider mb-1">
                <i data-lucide="sparkles" class="w-4 h-4"></i> AI Matched Accommodations
            </div>
            <h2 class="text-xl font-extrabold text-slate-900 mb-6">Similar Rooms in {{ $property->khan_district }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($recommendations as $rec)
                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-md transition group">
                        <div class="aspect-[16/10] bg-slate-100 relative overflow-hidden">
                            <img src="{{ $rec->primary_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute bottom-2 left-2 bg-white/95 px-2 py-0.5 rounded-lg text-xs font-black text-slate-900">
                                ${{ number_format($rec->price_per_month, 0) }}/mo
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="text-[11px] text-slate-400">{{ $rec->khan_district }}</div>
                            <h4 class="font-bold text-xs text-slate-900 truncate mt-0.5 group-hover:text-emerald-600">
                                <a href="{{ route('properties.show', $rec->slug) }}">{{ $rec->title }}</a>
                            </h4>
                            <div class="text-[11px] text-slate-500 mt-2 flex items-center justify-between">
                                <span>{{ $rec->bedrooms }} Bed • {{ $rec->bathrooms }} Bath</span>
                                <a href="{{ route('properties.show', $rec->slug) }}" class="text-emerald-600 font-bold hover:underline">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Request Room Viewing Modal -->
    <div x-show="bookingModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="bookingModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in duration-200">
            <button @click="bookingModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <div class="flex items-center gap-2 text-emerald-600 font-bold text-xs uppercase tracking-wider mb-1">
                <i data-lucide="calendar" class="w-4 h-4"></i> Schedule Appointment
            </div>
            <h3 class="text-xl font-extrabold text-slate-900">Request a Room Viewing</h3>
            <p class="text-xs text-slate-500 mt-1">The landlord will receive your request and confirm the appointment.</p>

            @auth
                <form action="{{ route('viewings.request') }}" method="POST" class="mt-5 space-y-4 text-sm">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $property->id }}">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Preferred Date</label>
                        <input type="date" name="preferred_date" required min="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Preferred Time</label>
                        <select name="preferred_time" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="09:00 AM">Morning (09:00 AM)</option>
                            <option value="10:30 AM">Morning (10:30 AM)</option>
                            <option value="02:00 PM">Afternoon (02:00 PM)</option>
                            <option value="04:30 PM">Afternoon (04:30 PM)</option>
                            <option value="06:00 PM">Evening (06:00 PM)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Contact Phone Number (Telegram)</label>
                        <input type="text" name="contact_phone" value="{{ auth()->user()->phone ?? '+855 ' }}" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Note for Landlord (Optional)</label>
                        <textarea name="renter_note" rows="2" placeholder="e.g. I work nearby and would like to check the room Wi-Fi and parking."
                                  class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Confirm & Send Viewing Request
                    </button>
                </form>
            @else
                <div class="mt-6 p-6 bg-slate-50 rounded-xl text-center border border-slate-100">
                    <i data-lucide="lock" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                    <p class="text-xs text-slate-600 font-medium mb-4">Please log in or create an account to request a viewing.</p>
                    <a href="{{ route('login') }}" class="inline-block px-5 py-2.5 bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-sm">
                        Log In to Continue
                    </a>
                </div>
            @endauth
        </div>
    </div>

</div>

@endsection

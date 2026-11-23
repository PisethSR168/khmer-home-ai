@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Top Profile & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                    Verified Landlord Dashboard
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                {{ auth()->user()->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ auth()->user()->landlordProfile?->business_name ?? 'Property Management' }} • {{ auth()->user()->landlordProfile?->address ?? 'Phnom Penh' }}
            </p>
        </div>

        <a href="{{ route('landlord.properties.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
            <i data-lucide="plus" class="w-4 h-4"></i> Add New Rental Unit
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-400 font-semibold uppercase">Total Properties</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_properties'] }}</div>
            <div class="text-[11px] text-emerald-600 font-medium mt-1">{{ $stats['available_count'] }} available to rent</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-400 font-semibold uppercase">Pending Viewings</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $stats['pending_viewings'] }}</div>
            <a href="{{ route('landlord.viewings') }}" class="text-[11px] text-emerald-600 font-medium mt-1 hover:underline">Review requests &rarr;</a>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-400 font-semibold uppercase">Tenant Views</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_views'] }}</div>
            <div class="text-[11px] text-slate-500 font-medium mt-1">Across all listings</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-400 font-semibold uppercase">Landlord Rating</div>
            <div class="text-2xl font-black text-emerald-600 mt-1 flex items-center gap-1">
                <i data-lucide="star" class="w-5 h-5 fill-current text-amber-400"></i>
                {{ auth()->user()->landlordProfile?->rating ?? '5.00' }}
            </div>
            <div class="text-[11px] text-slate-500 font-medium mt-1">Verified on platform</div>
        </div>
    </div>

    <!-- Listings Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-10">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-base">Your Listed Properties</h3>
            <span class="text-xs text-slate-500">{{ $properties->count() }} total</span>
        </div>

        @if($properties->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-6 py-3">Property</th>
                            <th class="px-6 py-3">District</th>
                            <th class="px-6 py-3">Rent / Mo</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">AI Score</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($properties as $prop)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4 flex items-center gap-3">
                                    <img src="{{ $prop->primary_image_url }}" class="w-12 h-10 rounded-lg object-cover">
                                    <div>
                                        <a href="{{ route('properties.show', $prop->slug) }}" class="font-bold text-slate-900 hover:text-emerald-600">
                                            {{ $prop->title }}
                                        </a>
                                        <div class="text-[11px] text-slate-400">{{ ucfirst($prop->property_type) }} • {{ $prop->bedrooms }} Bed</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium">{{ $prop->khan_district }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">${{ number_format($prop->price_per_month, 0) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $prop->status === 'available' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $prop->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-bold text-emerald-600">{{ $prop->ai_completeness_score }}%</span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('properties.show', $prop->slug) }}" class="text-slate-600 hover:text-emerald-600 font-semibold">View</a>
                                    <form action="{{ route('landlord.properties.delete', $prop->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this listing?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center text-xs text-slate-500">
                You haven't listed any rental units yet.
                <div class="mt-3">
                    <a href="{{ route('landlord.properties.create') }}" class="inline-block px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg">Create First Listing</a>
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Viewing Requests -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900 text-base">Recent Viewing Appointments</h3>
            <a href="{{ route('landlord.viewings') }}" class="text-xs font-semibold text-emerald-600 hover:underline">View all</a>
        </div>

        @if($viewingRequests->count() > 0)
            <div class="space-y-3">
                @foreach($viewingRequests as $v)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="font-bold text-xs text-slate-900">{{ $v->property?->title }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                Renter: <strong>{{ $v->user?->name }}</strong> ({{ $v->contact_phone }}) • Date: <strong>{{ $v->preferred_date?->format('M d, Y') }}</strong> at <strong>{{ $v->preferred_time }}</strong>
                            </div>
                            @if($v->renter_note)
                                <div class="text-[11px] text-slate-600 mt-1 italic">"{{ $v->renter_note }}"</div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $v->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : ($v->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                                {{ $v->status }}
                            </span>

                            @if($v->status === 'pending')
                                <form action="{{ route('landlord.viewings.status', $v->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700">
                                        Confirm
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-slate-400">No active viewing appointments yet.</p>
        @endif
    </div>

</div>

@endsection

@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                Renter Account
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                Welcome, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Manage your favorite rental rooms and viewing appointments.</p>
        </div>

        <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
            <i data-lucide="search" class="w-4 h-4"></i> Browse More Rooms
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left 2 Cols: Viewing Appointments & Saved Favorites -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Viewing Appointments -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5 text-emerald-600"></i>
                    My Room Viewing Appointments ({{ $viewingRequests->count() }})
                </h3>

                @if($viewingRequests->count() > 0)
                    <div class="space-y-4">
                        @foreach($viewingRequests as $v)
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $v->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : ($v->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                                            {{ $v->status }}
                                        </span>
                                        <span class="text-xs text-slate-400">{{ $v->preferred_date?->format('M d, Y') }} at {{ $v->preferred_time }}</span>
                                    </div>

                                    <h4 class="font-bold text-xs sm:text-sm text-slate-900">
                                        <a href="{{ route('properties.show', $v->property->slug) }}" class="hover:text-emerald-600">
                                            {{ $v->property?->title }}
                                        </a>
                                    </h4>

                                    <div class="text-[11px] text-slate-500">
                                        Landlord: <strong>{{ $v->landlord?->name }}</strong> • Contact: {{ $v->contact_phone }}
                                    </div>
                                    @if($v->landlord_response)
                                        <div class="text-[11px] text-emerald-700 bg-emerald-50 p-2 rounded-lg mt-1">
                                            Landlord response: "{{ $v->landlord_response }}"
                                        </div>
                                    @endif
                                </div>

                                <a href="{{ route('properties.show', $v->property->slug) }}" class="px-4 py-2 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 text-center shrink-0">
                                    View Room
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">You haven't booked any viewing appointments yet.</p>
                @endif
            </div>

            <!-- Saved Favorites -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
                    <i data-lucide="heart" class="w-5 h-5 text-rose-500"></i>
                    Saved Favorite Accommodations ({{ $favorites->count() }})
                </h3>

                @if($favorites->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($favorites as $fav)
                            <div class="border border-slate-200 rounded-2xl overflow-hidden hover:shadow-md transition group">
                                <div class="aspect-[16/10] bg-slate-100 relative overflow-hidden">
                                    <img src="{{ $fav->primary_image_url }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                                    <div class="absolute bottom-2 left-2 bg-white/95 px-2.5 py-0.5 rounded-lg text-xs font-black text-slate-900">
                                        ${{ number_format($fav->price_per_month, 0) }}/mo
                                    </div>
                                </div>
                                <div class="p-3">
                                    <div class="text-[10px] text-slate-400 uppercase font-semibold">{{ $fav->khan_district }}</div>
                                    <h4 class="font-bold text-xs text-slate-900 truncate mt-0.5">
                                        <a href="{{ route('properties.show', $fav->slug) }}" class="hover:text-emerald-600">{{ $fav->title }}</a>
                                    </h4>
                                    <div class="mt-3 flex items-center justify-between text-xs">
                                        <a href="{{ route('properties.show', $fav->slug) }}" class="text-emerald-600 font-bold hover:underline">Details &rarr;</a>
                                        <form action="{{ route('favorites.toggle', $fav->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-rose-500 text-[11px] font-semibold hover:underline">Remove</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">You don't have any saved rooms yet. Browse our listings and click the heart icon to save!</p>
                @endif
            </div>

        </div>

        <!-- Right Col: Profile & System Notifications -->
        <div class="space-y-6">

            <!-- Profile Overview -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=059669&color=fff' }}" class="w-14 h-14 rounded-full object-cover border border-slate-200">
                    <div>
                        <h4 class="font-bold text-slate-900 text-sm">{{ auth()->user()->name }}</h4>
                        <div class="text-xs text-slate-400">{{ auth()->user()->email }}</div>
                        <div class="text-xs text-slate-600 mt-0.5">{{ auth()->user()->phone ?? 'No phone added' }}</div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h4 class="font-bold text-slate-900 text-sm mb-3 flex items-center gap-2">
                    <i data-lucide="bell" class="w-4 h-4 text-emerald-600"></i> Notifications
                </h4>

                @if($notifications->count() > 0)
                    <div class="space-y-3">
                        @foreach($notifications as $n)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                                <div class="font-bold text-slate-800">{{ $n->title }}</div>
                                <div class="text-slate-500 mt-0.5">{{ $n->message }}</div>
                                <div class="text-[10px] text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">No new notifications.</p>
                @endif
            </div>

        </div>

    </div>

</div>

@endsection

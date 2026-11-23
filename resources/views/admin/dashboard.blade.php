@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-8 border-b border-slate-200 gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-900 text-white">
                Admin Superuser
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">
                Khmer Home AI System Administration
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">Global oversight of rentals, users, AI operations, and landlord verifications.</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-400 font-semibold uppercase">Total Listings</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_properties'] }}</div>
            <div class="text-[11px] text-emerald-600 font-medium mt-1">{{ $stats['available_properties'] }} available</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-400 font-semibold uppercase">Registered Users</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_users'] }}</div>
            <div class="text-[11px] text-slate-500 font-medium mt-1">{{ $stats['landlords_count'] }} landlords, {{ $stats['renters_count'] }} renters</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-400 font-semibold uppercase">Viewing Appointments</div>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_viewings'] }}</div>
            <div class="text-[11px] text-amber-600 font-medium mt-1">{{ $stats['pending_viewings'] }} pending</div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="text-xs text-slate-400 font-semibold uppercase">AI Operations Logged</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $stats['ai_logs_count'] }}</div>
            <div class="text-[11px] text-slate-500 font-medium mt-1">NLP searches & chats</div>
        </div>
    </div>

    <div class="space-y-8">

        <!-- Landlord Verifications -->
        @if($pendingLandlords->count() > 0)
            <div class="bg-white rounded-2xl border border-amber-200 shadow-sm p-6 bg-amber-50/20">
                <h3 class="font-bold text-slate-900 text-base mb-4 flex items-center gap-2">
                    <i data-lucide="shield-alert" class="w-5 h-5 text-amber-500"></i>
                    Pending Landlord Verifications ({{ $pendingLandlords->count() }})
                </h3>

                <div class="divide-y divide-slate-100">
                    @foreach($pendingLandlords as $pl)
                        <div class="py-3 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-900">{{ $pl->user->name }} ({{ $pl->business_name }})</div>
                                <div class="text-[11px] text-slate-500">Address: {{ $pl->address }} • Telegram: {{ $pl->telegram_username }}</div>
                            </div>
                            <form action="{{ route('admin.landlords.verify', $pl->id) }}" method="POST" class="flex gap-2">
                                @csrf
                                <button type="submit" name="status" value="verified" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold">Verify</button>
                                <button type="submit" name="status" value="rejected" class="px-3 py-1 bg-slate-200 text-slate-700 rounded-lg text-xs font-bold">Reject</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Properties Moderation -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-base">All Property Listings</h3>
                <span class="text-xs text-slate-500">Live platform records</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="px-6 py-3">Property</th>
                            <th class="px-6 py-3">Landlord</th>
                            <th class="px-6 py-3">District</th>
                            <th class="px-6 py-3">Price</th>
                            <th class="px-6 py-3">Featured</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($properties as $prop)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    <a href="{{ route('properties.show', $prop->slug) }}" class="hover:text-emerald-600">
                                        {{ $prop->title }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">{{ $prop->landlord?->name }}</td>
                                <td class="px-6 py-4">{{ $prop->khan_district }}</td>
                                <td class="px-6 py-4 font-bold">${{ number_format($prop->price_per_month, 0) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $prop->is_featured ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $prop->is_featured ? 'Featured' : 'Standard' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.properties.featured', $prop->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-emerald-600 hover:underline">
                                            {{ $prop->is_featured ? 'Unfeature' : 'Make Featured' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-base">User Directory</h3>
                <span class="text-xs text-slate-500">Renters, Landlords, and Admins</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Role</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $u)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 font-bold text-slate-900">{{ $u->name }}</td>
                                <td class="px-6 py-4">{{ $u->email }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 text-slate-700">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $u->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                        {{ $u->is_active ? 'Active' : 'Suspended' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(!$u->isAdmin())
                                        <form action="{{ route('admin.users.toggle', $u->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold {{ $u->is_active ? 'text-rose-600' : 'text-emerald-600' }} hover:underline">
                                                {{ $u->is_active ? 'Suspend' : 'Activate' }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-xs">Superadmin</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection

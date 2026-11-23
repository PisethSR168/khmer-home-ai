@extends('layouts.app')

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <a href="{{ route('landlord.dashboard') }}" class="text-xs font-semibold text-emerald-600 hover:underline flex items-center gap-1 mb-2">
            &larr; Back to Landlord Dashboard
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Room Viewing Appointments</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">Review tenant viewing requests and update appointment confirmations.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($viewings->count() > 0)
            <div class="divide-y divide-slate-100">
                @foreach($viewings as $v)
                    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:bg-slate-50/50 transition">
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $v->status === 'confirmed' ? 'bg-emerald-100 text-emerald-800' : ($v->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                                    {{ $v->status }}
                                </span>
                                <span class="text-xs text-slate-400">Requested {{ $v->created_at->diffForHumans() }}</span>
                            </div>

                            <h3 class="font-bold text-slate-900 text-base">
                                <a href="{{ route('properties.show', $v->property->slug) }}" class="hover:text-emerald-600">
                                    {{ $v->property?->title }}
                                </a>
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-xs text-slate-600">
                                <div>Renter: <strong class="text-slate-800">{{ $v->user?->name }}</strong></div>
                                <div>Phone / Telegram: <strong class="text-slate-800">{{ $v->contact_phone }}</strong></div>
                                <div>Appointment Date: <strong class="text-slate-800">{{ $v->preferred_date?->format('l, F j, Y') }}</strong></div>
                                <div>Time: <strong class="text-slate-800">{{ $v->preferred_time }}</strong></div>
                            </div>

                            @if($v->renter_note)
                                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600 italic">
                                    "{{ $v->renter_note }}"
                                </div>
                            @endif
                        </div>

                        <!-- Action Form -->
                        <div class="shrink-0 flex items-center gap-2">
                            @if($v->status === 'pending')
                                <form action="{{ route('landlord.viewings.status', $v->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="confirmed">
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition">
                                        Confirm Viewing
                                    </button>
                                </form>

                                <form action="{{ route('landlord.viewings.status', $v->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-700 font-bold text-xs rounded-xl transition">
                                        Decline
                                    </button>
                                </form>
                            @elseif($v->status === 'confirmed')
                                <form action="{{ route('landlord.viewings.status', $v->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                                        Mark as Completed
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 border-t border-slate-100">
                {{ $viewings->links() }}
            </div>
        @else
            <div class="p-12 text-center text-xs text-slate-400">
                No room viewing appointments found.
            </div>
        @endif
    </div>
</div>

@endsection

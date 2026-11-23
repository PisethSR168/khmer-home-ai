@extends('layouts.app')

@section('content')

<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" x-data="{ selectedRole: 'renter' }">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
        <div class="text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Khmer Home AI Logo" class="h-20 w-auto mx-auto object-contain">
            <h2 class="mt-4 text-2xl font-extrabold text-slate-900">Create an Account</h2>
            <p class="mt-1 text-xs text-slate-500">Join the AI-powered rental community in Siem Reap • Room SR</p>
        </div>

        @if($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
            @csrf

            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">I am registering as:</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="renter" x-model="selectedRole" class="sr-only">
                        <div class="p-3 rounded-xl border text-center transition"
                             :class="selectedRole === 'renter' ? 'border-emerald-600 bg-emerald-50 text-emerald-800 font-bold' : 'border-slate-200 text-slate-600'">
                            <i data-lucide="user" class="w-4 h-4 mx-auto mb-1"></i>
                            <span class="text-xs">Renter / Tenant</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="landlord" x-model="selectedRole" class="sr-only">
                        <div class="p-3 rounded-xl border text-center transition"
                             :class="selectedRole === 'landlord' ? 'border-emerald-600 bg-emerald-50 text-emerald-800 font-bold' : 'border-slate-200 text-slate-600'">
                            <i data-lucide="building" class="w-4 h-4 mx-auto mb-1"></i>
                            <span class="text-xs">Property Landlord</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <!-- Business name if Landlord -->
            <div x-show="selectedRole === 'landlord'">
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Residence / Business Name</label>
                <input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="e.g. Tuol Kouk Serviced Studios"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Phone Number (Telegram)</label>
                <input type="text" name="phone" value="{{ old('phone', '+855 ') }}"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
                <input type="password" name="password" required minlength="6"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation" required minlength="6"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                Create Account
            </button>
        </form>

        <div class="text-center text-xs text-slate-500">
            Already have an account?
            <a href="{{ route('login') }}" class="text-emerald-600 font-bold hover:underline">Sign in</a>
        </div>
    </div>
</div>

@endsection

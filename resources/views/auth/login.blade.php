@extends('layouts.app')

@section('content')

<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
        <div class="text-center">
            <img src="{{ asset('images/logo.png') }}" alt="Khmer Home AI Logo" class="h-20 w-auto mx-auto object-contain">
            <h2 class="mt-4 text-2xl font-extrabold text-slate-900">Welcome Back</h2>
            <p class="mt-1 text-xs text-slate-500">Sign in to your Khmer Home AI account • Room SR</p>
        </div>

        @if($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded text-emerald-600">
                    <span>Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                Sign In
            </button>
        </form>

        <!-- Quick Demo Credentials Box -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-1.5">
            <div class="font-bold text-slate-800 text-[11px] uppercase tracking-wider mb-1">Demo Accounts (Password: <code>password</code>):</div>
            <div>• Landlord: <code>sokha.landlord@khmerhome.ai</code></div>
            <div>• Renter: <code>visal.renter@khmerhome.ai</code></div>
            <div>• Admin: <code>admin@khmerhome.ai</code></div>
        </div>

        <div class="text-center text-xs text-slate-500">
            Don't have an account yet?
            <a href="{{ route('register') }}" class="text-emerald-600 font-bold hover:underline">Create an account</a>
        </div>
    </div>
</div>

@endsection

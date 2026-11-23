<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Room SR - AI-Powered Room Rentals in Siem Reap • បន្ទប់ជួលសៀមរាប' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Kantumruy+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons CDN fallback & Vite -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', 'Kantumruy Pro', sans-serif;
        }
    </style>
</head>
<body class="flex min-h-full flex-col antialiased">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 text-white text-xs sm:text-sm py-2 px-4 text-center font-medium shadow-inner flex items-center justify-center gap-2">
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/30 text-white border border-emerald-400/40">
            Siem Reap Focus
        </span>
        <span>Looking for rooms in Siem Reap (ក្រុងសៀមរាប)? Try our <strong>AI Smart Room Finder</strong> or chat with our 24/7 Rental Concierge!</span>
    </div>

    <!-- Navigation Bar -->
    <header x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">

                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <img src="{{ asset('images/logo.png') }}" alt="Khmer Home AI - Room SR" class="h-11 sm:h-13 w-auto object-contain transition-transform group-hover:scale-105">
                        <div class="hidden sm:block">
                            <div class="font-extrabold text-base sm:text-lg tracking-tight text-slate-900 flex items-center gap-1 leading-tight">
                                Room<span class="text-sky-600">SR</span>
                                <span class="text-[9px] bg-sky-100 text-sky-800 font-bold px-1.5 py-0.5 rounded">Siem Reap</span>
                            </div>
                            <div class="text-[10px] text-slate-500 font-medium">បន្ទប់ជួល សៀមរាប • $30-$100/mo</div>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                    <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('home') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Home
                    </a>
                    <a href="{{ route('properties.index') }}" class="px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('properties.*') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Explore Rooms
                    </a>
                    <a href="{{ route('ai.assistant') }}" class="px-3 py-2 text-sm font-semibold rounded-lg flex items-center gap-1.5 {{ request()->routeIs('ai.assistant') ? 'text-emerald-600 bg-emerald-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        <i data-lucide="sparkles" class="w-4 h-4 text-emerald-500"></i>
                        AI Rental Assistant
                    </a>
                </nav>

                <!-- Actions / Auth -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        @if(auth()->user()->isLandlord())
                            <a href="{{ route('landlord.properties.create') }}" class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-2 rounded-lg transition">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i> List Property
                            </a>
                        @endif

                        <!-- User Profile Dropdown -->
                        <div class="relative" @click.away="userDropdownOpen = false">
                            <button @click="userDropdownOpen = !userDropdownOpen" class="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-100 transition">
                                <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=059669&color=fff' }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                <span class="text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                            </button>

                            <div x-show="userDropdownOpen" x-transition class="absolute right-0 mt-2 w-56 rounded-xl bg-white shadow-xl border border-slate-100 py-2 z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs text-slate-400 font-medium">Signed in as</p>
                                    <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-slate-100 text-slate-600">
                                        {{ auth()->user()->role }}
                                    </span>
                                </div>

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-500"></i> Admin Dashboard
                                    </a>
                                @elseif(auth()->user()->isLandlord())
                                    <a href="{{ route('landlord.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-slate-500"></i> Landlord Dashboard
                                    </a>
                                    <a href="{{ route('landlord.viewings') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i> Viewing Requests
                                    </a>
                                @else
                                    <a href="{{ route('renter.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                        <i data-lucide="heart" class="w-4 h-4 text-rose-500"></i> My Favorites & Viewings
                                    </a>
                                @endif

                                <div class="border-t border-slate-100 mt-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                        <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-slate-900 transition">
                            Log In
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-sm shadow-emerald-500/20 transition">
                            Create Account
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden border-b border-slate-200 bg-white px-4 pt-2 pb-6 space-y-3">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50">Home</a>
            <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-slate-50">Explore Rooms</a>
            <a href="{{ route('ai.assistant') }}" class="block px-3 py-2 rounded-md text-base font-medium text-emerald-600 hover:bg-emerald-50 flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4"></i> AI Rental Assistant
            </a>

            <div class="border-t border-slate-100 pt-3">
                @auth
                    <p class="text-xs text-slate-400 font-semibold px-3 uppercase tracking-wider mb-2">My Account ({{ auth()->user()->role }})</p>
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50">Admin Dashboard</a>
                    @elseif(auth()->user()->isLandlord())
                        <a href="{{ route('landlord.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50">Landlord Dashboard</a>
                        <a href="{{ route('landlord.properties.create') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-emerald-700 hover:bg-emerald-50">+ Post New Room</a>
                    @else
                        <a href="{{ route('renter.dashboard') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-slate-50">Favorites & Bookings</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-sm font-medium text-rose-600 hover:bg-rose-50">Sign Out</button>
                    </form>
                @else
                    <div class="flex flex-col gap-2 pt-1">
                        <a href="{{ route('login') }}" class="w-full text-center py-2 text-sm font-semibold border border-slate-200 rounded-lg text-slate-700">Log In</a>
                        <a href="{{ route('register') }}" class="w-full text-center py-2 text-sm font-semibold bg-emerald-600 text-white rounded-lg">Create Account</a>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 mt-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">

                <!-- Brand -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="Khmer Home AI Logo" class="h-14 w-auto bg-white rounded-xl p-1.5 object-contain shadow-sm">
                        <div>
                            <div class="text-white font-extrabold text-lg leading-tight">Room<span class="text-sky-400">SR</span></div>
                            <div class="text-[10px] text-slate-400">Find a Home. Trust What You See.</div>
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 max-w-sm">
                        Siem Reap's dedicated room and accommodation rental platform (បន្ទប់ជួល សៀមរាប). Powered by Google AI to provide transparent utility rates, verified hosts, and hassle-free room finding across Krong Siem Reap.
                    </p>
                    <div class="text-xs text-slate-500">
                        Supported by Laravel REST API, MySQL & Flutter Mobile Apps.
                    </div>
                </div>

                <!-- Popular Districts in Siem Reap -->
                <div>
                    <h3 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">Siem Reap Communes</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('properties.index', ['district' => 'Sala Kamreuk']) }}" class="hover:text-emerald-400 transition">Sala Kamreuk (Artisan Hub)</a></li>
                        <li><a href="{{ route('properties.index', ['district' => 'Wat Bo']) }}" class="hover:text-emerald-400 transition">Wat Bo Village (Cafes & Riverside)</a></li>
                        <li><a href="{{ route('properties.index', ['district' => 'Svay Dangkum']) }}" class="hover:text-emerald-400 transition">Svay Dangkum (Pub St / Center)</a></li>
                        <li><a href="{{ route('properties.index', ['district' => 'Slor Kram']) }}" class="hover:text-emerald-400 transition">Slor Kram (Angkor Conservation)</a></li>
                        <li><a href="{{ route('properties.index', ['district' => 'Kork Chak']) }}" class="hover:text-emerald-400 transition">Kork Chak (Gate to Angkor)</a></li>
                    </ul>
                </div>

                <!-- AI Services -->
                <div>
                    <h3 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">AI Features</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('ai.assistant') }}" class="hover:text-emerald-400 transition">Natural Language Room Search</a></li>
                        <li><a href="{{ route('ai.assistant') }}" class="hover:text-emerald-400 transition">24/7 AI Rental Assistant</a></li>
                        <li><a href="{{ route('properties.index') }}" class="hover:text-emerald-400 transition">Smart Recommendations</a></li>
                        <li><a href="{{ route('landlord.dashboard') }}" class="hover:text-emerald-400 transition">Listing Completeness Checks</a></li>
                    </ul>
                </div>

                <!-- Mobile App teaser -->
                <div>
                    <h3 class="text-white text-sm font-semibold uppercase tracking-wider mb-4">Khmer Home Mobile</h3>
                    <p class="text-xs text-slate-400 mb-3">Download our Flutter app for iOS and Android to search, chat, and book viewings on the go.</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-slate-800 text-white text-xs border border-slate-700">
                            <i data-lucide="smartphone" class="w-4 h-4 text-emerald-400"></i>
                            <div>
                                <div class="font-bold">Android & iOS</div>
                                <div class="text-[10px] text-slate-400">Powered by Flutter & Dart</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-12 pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; {{ date('Y') }} Room SR (បន្ទប់ជួល សៀមរាប). All rights reserved. Made for Siem Reap, Cambodia.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-slate-400">Terms of Service</a>
                    <a href="#" class="hover:text-slate-400">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-400">EDC Electricity Guide</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>

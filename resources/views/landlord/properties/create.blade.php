@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="propertyForm()">

    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('landlord.dashboard') }}" class="text-xs font-semibold text-emerald-600 hover:underline flex items-center gap-1 mb-2">
            &larr; Back to Landlord Dashboard
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900">List a New Rental Property</h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">Provide clear specifications and transparent utility rates to score high on AI listing completeness.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700">
            <strong>Please fix the errors below:</strong>
            <ul class="list-disc list-inside mt-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('landlord.properties.store') }}" method="POST" class="space-y-8 bg-white p-6 sm:p-10 rounded-3xl border border-slate-200 shadow-sm">
        @csrf

        <!-- 1. Basic Info -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-emerald-600"></i> Basic Information
            </h3>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Property Listing Title</label>
                <input type="text" name="title" x-model="form.title" required placeholder="e.g. Modern Studio with Balcony & High Speed Wi-Fi - Tuol Kouk"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Property Type</label>
                    <select name="property_type" x-model="form.property_type" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="room">Private Single Room</option>
                        <option value="studio">Studio Apartment</option>
                        <option value="apartment">Full Apartment</option>
                        <option value="condo">Condominium</option>
                        <option value="house">House / Villa</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Furnishing Status</label>
                    <select name="furnished_status" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        <option value="fully_furnished">Fully Furnished</option>
                        <option value="semi_furnished">Semi Furnished</option>
                        <option value="unfurnished">Unfurnished</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Location -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-emerald-600"></i> Location in Cambodia
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">City</label>
                    <input type="text" name="city" value="Siem Reap" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Commune / Area in Siem Reap</label>
                    <select name="khan_district" x-model="form.khan_district" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach($districts as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Commune (Sangkat)</label>
                    <input type="text" name="sangkat_commune" placeholder="e.g. BKK1, Tuek L'ak 1" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Street Address</label>
                    <input type="text" name="street_address" required placeholder="e.g. Street 288, near Central Market" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm">
                </div>
            </div>
        </div>

        <!-- 3. Pricing & Transparent Utilities -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="dollar-sign" class="w-4 h-4 text-emerald-600"></i> Pricing & Utilities
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Monthly Rent ($ USD)</label>
                    <input type="number" step="0.01" name="price_per_month" x-model="form.price_per_month" required placeholder="e.g. 250"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Security Deposit ($ USD)</label>
                    <input type="number" step="0.01" name="deposit_amount" placeholder="e.g. 250 (1 Month)"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl">
                <div>
                    <label class="block text-xs font-bold text-emerald-900 uppercase mb-1">Electricity Rate ($/kWh)</label>
                    <input type="number" step="0.01" name="electricity_cost_per_kwh" value="0.25" placeholder="0.25"
                           class="w-full px-4 py-2.5 bg-white border border-emerald-300 rounded-xl text-xs focus:outline-none">
                    <span class="text-[10px] text-emerald-700 mt-1 block">Government EDC rate is ~$0.18, private rate usually $0.20-$0.25</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-emerald-900 uppercase mb-1">Water Rate ($/m³)</label>
                    <input type="number" step="0.01" name="water_cost_per_m3" value="0.40" placeholder="0.40"
                           class="w-full px-4 py-2.5 bg-white border border-emerald-300 rounded-xl text-xs focus:outline-none">
                    <span class="text-[10px] text-emerald-700 mt-1 block">PPWSA standard is ~$0.35 - $0.50/m³</span>
                </div>
            </div>
        </div>

        <!-- 4. Specifications -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="home" class="w-4 h-4 text-emerald-600"></i> Room Specifications
            </h3>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bedrooms</label>
                    <input type="number" name="bedrooms" value="1" min="1" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bathrooms</label>
                    <input type="number" name="bathrooms" value="1" min="1" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Size (m²)</label>
                    <input type="number" step="0.1" name="size_sqm" placeholder="e.g. 35" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
            </div>
        </div>

        <!-- 5. Description & AI Assistance -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-emerald-600"></i> Property Description
                </h3>

                <!-- AI Generator Trigger -->
                <button type="button" @click="generateAiDescription()" :disabled="aiGenerating"
                        class="px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 disabled:opacity-50 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-emerald-400"></i>
                    <span x-text="aiGenerating ? 'AI Generating...' : 'Auto-Draft with AI'"></span>
                </button>
            </div>

            <div>
                <textarea name="description" x-model="form.description" rows="5" required
                          placeholder="Describe the room, furniture, views, neighborhood attractions, and lease terms..."
                          class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                <span class="text-[11px] text-slate-400 mt-1 block">Tip: Click 'Auto-Draft with AI' to automatically write an attractive description based on your title and location!</span>
            </div>
        </div>

        <!-- 6. Amenities -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="check-square" class="w-4 h-4 text-emerald-600"></i> Included Amenities
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @foreach($amenities as $amenity)
                    <label class="flex items-center gap-2 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-emerald-50 hover:border-emerald-200 transition">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-semibold text-slate-700">{{ $amenity->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <!-- 7. Photographs (URLs) -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-900 text-base border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="image" class="w-4 h-4 text-emerald-600"></i> Property Photographs
            </h3>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Image URLs (one per line)</label>
                <textarea name="image_urls" rows="3" placeholder="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267...&#10;https://images.unsplash.com/photo-1502672260266-1c1ef2d93688..."
                          class="w-full p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
                <span class="text-[11px] text-slate-400 mt-1 block">Leave empty to use high-resolution default room photography.</span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('landlord.dashboard') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-emerald-500/20 transition">
                Publish Rental Listing
            </button>
        </div>

    </form>
</div>

<script>
function propertyForm() {
    return {
        aiGenerating: false,
        form: {
            title: '',
            property_type: 'studio',
            khan_district: 'Sala Kamreuk',
            price_per_month: 250,
            description: ''
        },
        async generateAiDescription() {
            if (!this.form.title) {
                alert('Please enter a listing title first.');
                return;
            }
            this.aiGenerating = true;
            try {
                const res = await fetch('/api/v1/ai/generate-description', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        title: this.form.title,
                        property_type: this.form.property_type,
                        khan_district: this.form.khan_district,
                        price_per_month: this.form.price_per_month
                    })
                });
                const data = await res.json();
                if (data.success && data.data.description) {
                    this.form.description = data.data.description;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.aiGenerating = false;
            }
        }
    }
}
</script>

@endsection

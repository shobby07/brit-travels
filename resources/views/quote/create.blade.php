<x-layout
    title="Get a Free Coach Hire Quote"
    description="Request a free, no-obligation coach hire quotation from Brit Travels. Tell us about your trip and we'll price it up — usually the same day."
>
    <x-page-header
        eyebrow="Free Quotation"
        title="Get your free quote"
        subtitle="Tell us about your trip and we'll send a no-obligation quotation — usually the same day. The more detail you give, the more accurate the price."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site max-w-3xl">
            @if ($errors->any())
                <div class="mb-7 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700" role="alert">
                    Please fix the highlighted fields below.
                </div>
            @endif

            <form
                action="{{ route('quote.store') }}"
                method="POST"
                x-data="{ tripType: '{{ old('trip_type', 'one_way') }}' }"
                class="rounded-3xl border border-navy-100 bg-white p-8 shadow-xl shadow-navy-900/5 sm:p-12"
            >
                @csrf

                <h2 class="font-display text-xl font-semibold text-navy-950">Trip details</h2>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="trip_type" class="field-label">Trip type</label>
                        <select id="trip_type" name="trip_type" x-model="tripType" class="field-input">
                            <option value="one_way">One way</option>
                            <option value="round_trip">Round trip</option>
                        </select>
                        @error('trip_type')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="passengers" class="field-label">Number of passengers</label>
                        <input id="passengers" name="passengers" type="number" min="1" max="500" value="{{ old('passengers') }}" class="field-input">
                        @error('passengers')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pickup_location" class="field-label">Pickup location *</label>
                        <input id="pickup_location" name="pickup_location" type="text" required placeholder="e.g. London Victoria" value="{{ old('pickup_location') }}" class="field-input">
                        @error('pickup_location')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="dropoff_location" class="field-label">Drop-off location *</label>
                        <input id="dropoff_location" name="dropoff_location" type="text" required placeholder="e.g. Manchester City Centre" value="{{ old('dropoff_location') }}" class="field-input">
                        @error('dropoff_location')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pickup_date" class="field-label">Pickup date</label>
                        <input id="pickup_date" name="pickup_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('pickup_date') }}" class="field-input">
                        @error('pickup_date')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="pickup_time" class="field-label">Pickup time</label>
                        <input id="pickup_time" name="pickup_time" type="time" value="{{ old('pickup_time') }}" class="field-input">
                        @error('pickup_time')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <template x-if="tripType === 'round_trip'">
                        <div>
                            <label for="return_date" class="field-label">Return date</label>
                            <input id="return_date" name="return_date" type="date" min="{{ now()->toDateString() }}" value="{{ old('return_date') }}" class="field-input">
                            @error('return_date')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                    </template>
                    <template x-if="tripType === 'round_trip'">
                        <div>
                            <label for="return_time" class="field-label">Return time</label>
                            <input id="return_time" name="return_time" type="time" value="{{ old('return_time') }}" class="field-input">
                            @error('return_time')<p class="field-error">{{ $message }}</p>@enderror
                        </div>
                    </template>
                    <div class="sm:col-span-2">
                        <label for="coach_id" class="field-label">Preferred coach (optional)</label>
                        <select id="coach_id" name="coach_id" class="field-input">
                            <option value="">Let us recommend</option>
                            @foreach ($coaches as $coach)
                                <option value="{{ $coach->id }}" @selected(old('coach_id', $selectedCoach) == $coach->id)>{{ $coach->name }} ({{ $coach->seats }} seats)</option>
                            @endforeach
                        </select>
                        @error('coach_id')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <h2 class="mt-10 font-display text-xl font-semibold text-navy-950">Your details</h2>
                <div class="mt-6 grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="field-label">Full name *</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" class="field-input">
                        @error('name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="field-label">Email address *</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}" class="field-input">
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="field-label">Phone number *</label>
                        <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="field-input">
                        @error('phone')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="message" class="field-label">Anything else we should know?</label>
                        <textarea id="message" name="message" rows="4" placeholder="Multiple pickups, luggage, accessibility needs, flexible dates…" class="field-input">{{ old('message') }}</textarea>
                        @error('message')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                <button type="submit" class="btn-primary mt-9 w-full">Request My Free Quote</button>
                <p class="mt-4 text-center text-xs text-navy-400">No obligation, no spam — just a clear price for your journey.</p>
            </form>
        </div>
    </section>
</x-layout>

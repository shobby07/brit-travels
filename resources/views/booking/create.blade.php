<x-layout
    title="Book a Coach Online"
    description="Book a coach or minibus online with Brit Travels in under two minutes. One-way or round trip, 8 to 70 seats, professional driver included."
>
    <x-page-header
        eyebrow="Online Booking"
        title="Book your coach"
        subtitle="Two quick steps: tell us about your trip, then your contact details. We'll confirm availability by email — usually within a few hours."
    />

    <section class="py-16 sm:py-24">
        <div class="container-site max-w-3xl">
            @php
                $startStep = $errors->hasAny(['name', 'email', 'phone', 'notes']) ? 2 : 1;
            @endphp

            <div
                x-data="bookingForm({
                    step: {{ $startStep }},
                    tripType: '{{ old('trip_type', request('trip_type', 'one_way')) }}',
                })"
                class="rounded-3xl border border-navy-100 bg-white p-8 shadow-xl shadow-navy-900/5 sm:p-12"
            >
                {{-- Progress --}}
                <ol class="flex items-center gap-3" aria-label="Booking steps">
                    <li class="flex items-center gap-2.5">
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-full font-display text-sm font-bold transition-colors"
                            :class="step >= 1 ? 'bg-accent-400 text-navy-950' : 'bg-navy-100 text-navy-400'"
                        >1</span>
                        <span class="text-sm font-semibold" :class="step === 1 ? 'text-navy-950' : 'text-navy-400'">Trip Details</span>
                    </li>
                    <li class="h-px flex-1 bg-navy-100" aria-hidden="true"></li>
                    <li class="flex items-center gap-2.5">
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-full font-display text-sm font-bold transition-colors"
                            :class="step >= 2 ? 'bg-accent-400 text-navy-950' : 'bg-navy-100 text-navy-400'"
                        >2</span>
                        <span class="text-sm font-semibold" :class="step === 2 ? 'text-navy-950' : 'text-navy-400'">Your Details</span>
                    </li>
                </ol>

                @if ($errors->any())
                    <div class="mt-7 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700" role="alert">
                        Please fix the highlighted fields below.
                    </div>
                @endif

                <form action="{{ route('booking.store') }}" method="POST" class="mt-9">
                    @csrf

                    {{-- Step 1: Trip details --}}
                    <div x-show="step === 1" x-ref="step1">
                        <fieldset>
                            <legend class="field-label">Trip type *</legend>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 px-4 py-3.5 text-sm font-semibold transition-colors"
                                    :class="tripType === 'one_way' ? 'border-accent-400 bg-accent-50 text-navy-950' : 'border-navy-100 text-navy-500 hover:border-navy-200'"
                                >
                                    <input type="radio" name="trip_type" value="one_way" x-model="tripType" class="sr-only">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                                    One Way
                                </label>
                                <label
                                    class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border-2 px-4 py-3.5 text-sm font-semibold transition-colors"
                                    :class="tripType === 'round_trip' ? 'border-accent-400 bg-accent-50 text-navy-950' : 'border-navy-100 text-navy-500 hover:border-navy-200'"
                                >
                                    <input type="radio" name="trip_type" value="round_trip" x-model="tripType" class="sr-only">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                                    Round Trip
                                </label>
                            </div>
                            @error('trip_type')<p class="field-error">{{ $message }}</p>@enderror
                        </fieldset>

                        <div class="mt-6 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="pickup_location" class="field-label">Pickup location *</label>
                                <input id="pickup_location" name="pickup_location" type="text" required placeholder="e.g. London Victoria" value="{{ old('pickup_location', request('pickup_location')) }}" class="field-input">
                                @error('pickup_location')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="dropoff_location" class="field-label">Drop-off location *</label>
                                <input id="dropoff_location" name="dropoff_location" type="text" required placeholder="e.g. Manchester City Centre" value="{{ old('dropoff_location', request('dropoff_location')) }}" class="field-input">
                                @error('dropoff_location')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="pickup_date" class="field-label">Pickup date *</label>
                                <input id="pickup_date" name="pickup_date" type="date" required min="{{ now()->toDateString() }}" value="{{ old('pickup_date', request('pickup_date')) }}" class="field-input">
                                @error('pickup_date')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="pickup_time" class="field-label">Pickup time *</label>
                                <input id="pickup_time" name="pickup_time" type="time" required value="{{ old('pickup_time') }}" class="field-input">
                                @error('pickup_time')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                            <template x-if="tripType === 'round_trip'">
                                <div>
                                    <label for="return_date" class="field-label">Return date *</label>
                                    <input id="return_date" name="return_date" type="date" :required="tripType === 'round_trip'" min="{{ now()->toDateString() }}" value="{{ old('return_date') }}" class="field-input">
                                    @error('return_date')<p class="field-error">{{ $message }}</p>@enderror
                                </div>
                            </template>
                            <template x-if="tripType === 'round_trip'">
                                <div>
                                    <label for="return_time" class="field-label">Return time *</label>
                                    <input id="return_time" name="return_time" type="time" :required="tripType === 'round_trip'" value="{{ old('return_time') }}" class="field-input">
                                    @error('return_time')<p class="field-error">{{ $message }}</p>@enderror
                                </div>
                            </template>
                            <div>
                                <label for="passengers" class="field-label">Number of passengers *</label>
                                <input id="passengers" name="passengers" type="number" required min="1" max="500" value="{{ old('passengers') }}" class="field-input">
                                @error('passengers')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="coach_id" class="field-label">Preferred coach (optional)</label>
                                <select id="coach_id" name="coach_id" class="field-input">
                                    <option value="">Let us recommend</option>
                                    @foreach ($coaches as $coach)
                                        <option value="{{ $coach->id }}" @selected(old('coach_id', request('coach')) == $coach->id)>{{ $coach->name }} ({{ $coach->seats }} seats)</option>
                                    @endforeach
                                </select>
                                @error('coach_id')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <button type="button" @click="nextStep()" class="btn-primary mt-8 w-full">
                            Continue to Your Details
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>

                    {{-- Step 2: Personal details --}}
                    <div x-show="step === 2" x-cloak>
                        <div class="grid gap-5 sm:grid-cols-2">
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
                                <label for="notes" class="field-label">Notes for our team (optional)</label>
                                <textarea id="notes" name="notes" rows="4" placeholder="Luggage, accessibility needs, multiple pickups, special occasions…" class="field-input">{{ old('notes') }}</textarea>
                                @error('notes')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <button type="button" @click="step = 1" class="btn-secondary sm:w-auto">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                                Back
                            </button>
                            <button type="submit" class="btn-primary flex-1">Submit Booking Request</button>
                        </div>
                        <p class="mt-4 text-center text-xs text-navy-400">No payment is taken online. We'll confirm availability and arrange everything by email.</p>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingForm', ({ step, tripType }) => ({
                step,
                tripType,
                nextStep() {
                    const fields = this.$refs.step1.querySelectorAll('input, select');
                    for (const field of fields) {
                        if (!field.checkValidity()) {
                            field.reportValidity();
                            return;
                        }
                    }
                    this.step = 2;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },
            }));
        });
    </script>
</x-layout>

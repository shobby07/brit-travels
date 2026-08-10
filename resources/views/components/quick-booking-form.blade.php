@props(['pickup' => null])

{{--
    Three-field teaser that hands off to the full booking form. Submits as GET
    so the values arrive as query params on /book, where the real form reads
    them via request() and prefills step 1.
--}}
<form action="{{ route('booking.create') }}" method="GET" class="rounded-3xl border border-navy-100 bg-white p-6 shadow-xl shadow-navy-900/5 sm:p-7">
    <div class="space-y-4">
        <div>
            <label for="quick_pickup_location" class="field-label">Pickup address *</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-accent-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                </span>
                <input id="quick_pickup_location" name="pickup_location" type="text" required value="{{ $pickup }}" placeholder="Pickup address" class="field-input pl-10">
            </div>
        </div>

        <div>
            <label for="quick_dropoff_location" class="field-label">Drop-off address *</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-accent-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                </span>
                <input id="quick_dropoff_location" name="dropoff_location" type="text" required placeholder="Drop-off address" class="field-input pl-10">
            </div>
        </div>

        <div>
            <label for="quick_pickup_date" class="field-label">Pickup date *</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-accent-500">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                </span>
                <input id="quick_pickup_date" name="pickup_date" type="date" required min="{{ now()->toDateString() }}" class="field-input pl-10">
            </div>
        </div>
    </div>

    <button type="submit" class="btn-primary mt-6 w-full">
        Next
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
    </button>

    <p class="mt-3 text-center text-xs text-navy-400">Takes under two minutes. No payment is taken online.</p>
</form>

@props(['coach'])

<article class="group overflow-hidden rounded-3xl border border-navy-100 bg-white transition-all duration-300 hover:-translate-y-1.5 hover:border-navy-200 hover:shadow-xl hover:shadow-navy-900/10">
    <a href="{{ route('fleet.show', $coach) }}" class="block">
        <div class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-navy-800 to-navy-950">
            @if ($coach->image)
                <img
                    src="{{ str_starts_with($coach->image, 'http') ? $coach->image : asset('storage/'.$coach->image) }}"
                    alt="{{ $coach->name }} available for hire from Brit Travels"
                    loading="lazy"
                    width="640" height="400"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                >
            @else
                <div class="flex h-full w-full items-center justify-center p-10 text-navy-300 transition-transform duration-500 group-hover:scale-105">
                    <x-coach-illustration />
                </div>
            @endif
            <span class="absolute right-4 top-4 rounded-full bg-accent-400 px-3.5 py-1.5 text-xs font-bold text-navy-950">{{ $coach->seats }} seats</span>
        </div>
    </a>
    <div class="p-6">
        <h3 class="font-display text-xl font-semibold text-navy-950">
            <a href="{{ route('fleet.show', $coach) }}" class="transition-colors hover:text-accent-600">{{ $coach->name }}</a>
        </h3>
        @if ($coach->amenities)
            <ul class="mt-3 flex flex-wrap gap-1.5">
                @foreach (array_slice($coach->amenities, 0, 3) as $amenity)
                    <li class="rounded-full bg-navy-50 px-3 py-1 text-xs font-medium text-navy-700">{{ $amenity }}</li>
                @endforeach
                @if (count($coach->amenities) > 3)
                    <li class="rounded-full bg-navy-50 px-3 py-1 text-xs font-medium text-navy-500">+{{ count($coach->amenities) - 3 }} more</li>
                @endif
            </ul>
        @endif
        <div class="mt-5 flex items-center gap-3">
            <a href="{{ route('booking.create', ['coach' => $coach->id]) }}" class="btn-primary !px-5 !py-2.5 text-xs">Book Now</a>
            <a href="{{ route('quote.create', ['coach' => $coach->id]) }}" class="btn-secondary !px-5 !py-2.5 text-xs">Get a Quote</a>
        </div>
    </div>
</article>

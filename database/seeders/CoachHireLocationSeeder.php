<?php

namespace Database\Seeders;

use App\Models\CoachHireLocation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CoachHireLocationSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $heroAlts = $this->heroImageAltText();
        $credits = $this->heroPhotoCredits();

        foreach ($this->locations() as $index => $location) {
            $slug = Str::slug($location['name']);

            // Hero photo: sourced from Wikimedia Commons (CC BY-SA / CC0) and
            // self-hosted under public/images/hero, so it ships with the repo
            // rather than depending on a storage symlink. Only wire it up if the
            // file is actually present, so a checkout without the images falls
            // back cleanly to the gradient hero. The admin can replace any photo
            // from the panel (uploads go to the public storage disk instead).
            $credit = $credits[$slug] ?? null;
            $heroImage = null;
            if (isset($credit['image']) && is_file(public_path($credit['image']))) {
                $heroImage = $credit['image'];
            }

            CoachHireLocation::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $location['name'],
                    'hero_image' => $heroImage,
                    'hero_image_alt' => $credit['alt'] ?? ($heroAlts[$slug] ?? null),
                    'hero_image_credit' => $heroImage ? ($credit['credit_html'] ?? null) : null,
                    'meta_title' => $location['meta_title'],
                    'meta_description' => $location['meta_description'],
                    'intro_heading' => $location['intro_heading'],
                    'intro_content' => $location['intro_content'],
                    'why_choose_points' => $location['why_choose_points'],
                    'faqs' => $location['faqs'],
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );
        }
    }

    /**
     * Local filename, photo attribution and alt text recorded when the hero
     * images were fetched from Wikimedia Commons. Keyed by slug. The credit is
     * kept as a licensing record (see data/PHOTO_CREDITS.md) — it is no longer
     * painted over the hero. Returns [] if the file is missing.
     */
    private function heroPhotoCredits(): array
    {
        $path = database_path('seeders/data/hero-photo-credits.json');

        if (! is_file($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?: [];
    }

    /**
     * Descriptive, landmark-specific alt text for each city's hero photo.
     * Used as a fallback when no fetched-photo credit is available.
     */
    private function heroImageAltText(): array
    {
        return [
            'london' => 'Coach hire in central London near Tower Bridge and the River Thames',
            'glasgow' => 'Coach hire in Glasgow beside the River Clyde and the city centre',
            'edinburgh' => 'Coach hire in Edinburgh below the Old Town skyline and Edinburgh Castle',
            'bristol' => 'Coach hire in Bristol at the Clifton Suspension Bridge over the Avon Gorge',
            'southampton' => 'Coach hire in Southampton near the cruise port and waterfront',
            'swansea' => 'Coach hire in Swansea overlooking Swansea Bay and the Mumbles coastline',
            'leeds' => 'Coach hire in Leeds in the city centre beside the River Aire',
            'sheffield' => 'Coach hire in Sheffield on the edge of the Peak District',
            'newcastle-upon-tyne' => 'Coach hire in Newcastle upon Tyne beside the Tyne Bridge and Quayside',
        ];
    }

    /**
     * Each location has deliberately distinct copy — different landmarks,
     * use cases, structure and wording — to avoid duplicate-content issues.
     */
    private function locations(): array
    {
        return [
            [
                'name' => 'London',
                'meta_title' => 'Coach Hire London | Executive Coaches & Minibus Hire — Brit Travel',
                'meta_description' => 'Coach hire in London with professional drivers. Heathrow & Gatwick airport transfers, Wembley & Twickenham event travel, weddings and corporate shuttles. Free quote.',
                'intro_heading' => 'Coach Hire in London for Groups of Every Size',
                'intro_content' => "London moves fast, and getting a group through it — from the City to the West End, or out to Heathrow and Gatwick — takes a driver who knows the capital's roads, bus lanes and charging zones. Brit Travel provides chauffeured coaches and minibuses across all 32 boroughs, whether you're heading to a matinee in Theatreland, a match at Wembley or Twickenham, or a conference at the ExCeL.\n\nWe handle the logistics that make London group travel stressful: timed pickups around rush hour, coach-friendly drop-off points near pedestrianised areas, and drivers who won't be caught out by a closed road near Westminster. From an 8-seat executive minibus for a client roadshow to a 70-seat coach for a school trip to the museums in South Kensington, every vehicle comes with a licensed, DBS-checked driver.\n\nAirport transfers are one of our most requested London services — early-morning runs to Heathrow, Gatwick, Stansted, Luton and City with enough luggage space for the whole party. We also cover weddings across the Home Counties, corporate shuttles in Canary Wharf, and evening pickups after West End shows and O2 concerts.",
                'why_choose_points' => [
                    ['title' => 'Airport transfers done right', 'description' => 'Fixed pickup times for Heathrow, Gatwick, Stansted, Luton and London City, with room for cases, buggies and sports kit — no squeezing luggage onto laps.'],
                    ['title' => 'Drivers who know the capital', 'description' => 'Our drivers plan around the Congestion Charge and ULEZ zones, coach restrictions and one-way systems so your group arrives on schedule, not stuck circling for a drop-off.'],
                    ['title' => 'Event and stadium travel', 'description' => 'Group runs to Wembley, Twickenham, the O2, the Emirates and Tottenham Hotspur Stadium, with post-event pickups arranged in advance so nobody is left waiting in the cold.'],
                    ['title' => 'Corporate-ready service', 'description' => 'Executive minibuses for client visits, staff shuttles and roadshows across the City and Canary Wharf, invoiced simply for your finance team.'],
                ],
                'faqs' => [
                    ['question' => 'Can you pick up from a central London hotel?', 'answer' => "Yes. We arrange coach-friendly pickup points close to central hotels and will advise the nearest legal stopping point where the street itself can't take a coach — common around Covent Garden, Soho and the West End."],
                    ['question' => 'Do you cover all London airports for group transfers?', 'answer' => 'We cover Heathrow, Gatwick, Stansted, Luton and London City, including early-morning departures and late arrivals with meet-and-greet available on request.'],
                    ['question' => 'Are the Congestion Charge and ULEZ fees included?', 'answer' => 'Any applicable Congestion Charge or ULEZ fees are factored into your quotation up front, so there are no surprise add-ons after your trip.'],
                ],
            ],
            [
                'name' => 'Glasgow',
                'meta_title' => 'Coach Hire Glasgow | Minibus & Coach Hire with Driver — Brit Travel',
                'meta_description' => 'Coach and minibus hire in Glasgow. Hampden Park and OVO Hydro event travel, Glasgow Airport transfers, Loch Lomond day trips and distillery tours. Free same-day quote.',
                'intro_heading' => 'Coach Hire Across Glasgow and the West of Scotland',
                'intro_content' => "Glasgow is Scotland's events capital, and Brit Travel keeps groups moving to all of it — from a concert at the OVO Hydro to an international at Hampden Park or a graduation at the University of Glasgow. Our chauffeured coaches and minibuses cover the city centre, the West End and the wider Strathclyde area, with drivers who know the M8, the Clyde Tunnel and the match-day traffic around Celtic Park and Ibrox.\n\nBeyond the city, Glasgow is the natural launch point for the west of Scotland. We run day trips out to Loch Lomond and the Trossachs, distillery tours across Argyll, and longer touring itineraries up towards Oban and the Highlands. Whether it's a stag weekend, a golf party or a corporate away day, we'll match the right size of vehicle to your group.\n\nGlasgow Airport transfers are a core service, with early departures from hotels across the city and plenty of hold space for luggage and golf clubs. We also handle weddings across Ayrshire and Renfrewshire, and school and college trips throughout Greater Glasgow.",
                'why_choose_points' => [
                    ['title' => 'Match-day and concert travel', 'description' => 'Group transport to Hampden Park, the OVO Hydro, Celtic Park and Ibrox, with drivers who plan around road closures and know where a coach can actually set down.'],
                    ['title' => 'Loch Lomond and Highland day trips', 'description' => 'Comfortable coaches for scenic runs to Loch Lomond, the Trossachs and beyond, with drivers used to single-track roads and Highland routes.'],
                    ['title' => 'Glasgow Airport transfers', 'description' => 'Punctual pickups for early flights, with space for suitcases and golf bags, plus meet-and-greet on return if you need it.'],
                    ['title' => 'Whisky and touring itineraries', 'description' => 'Distillery tours and multi-day Scottish itineraries planned around your group, so the driver handles the miles while you enjoy the trip.'],
                ],
                'faqs' => [
                    ['question' => 'Do you run day trips from Glasgow to Loch Lomond?', 'answer' => 'Yes — Loch Lomond and the Trossachs are among our most popular Glasgow day trips, and we can build in stops at Luss, Balloch or Aberfoyle to suit your group.'],
                    ['question' => 'Can you handle match-day travel to Hampden or Ibrox?', 'answer' => "Absolutely. We arrange drop-offs at the nearest permitted point and confirm a post-match pickup location in advance so your group isn't hunting for the coach in the crowds."],
                    ['question' => 'Is Glasgow Airport within your standard coverage?', 'answer' => 'Yes, Glasgow Airport transfers are one of our core services, including early-morning departures and flights from Glasgow Prestwick on request.'],
                ],
            ],
            [
                'name' => 'Edinburgh',
                'meta_title' => 'Coach Hire Edinburgh | Festival, Rugby & Airport Coach Hire — Brit Travel',
                'meta_description' => 'Coach hire in Edinburgh with driver. Fringe Festival and Hogmanay group travel, Murrayfield rugby, Edinburgh Airport transfers and Old Town tours. Free quote.',
                'intro_heading' => 'Coach Hire in Edinburgh for Festivals, Rugby and Beyond',
                'intro_content' => "Few cities pack as much into the calendar as Edinburgh, and Brit Travel moves groups through all of it. During August the Fringe and International Festival turn the city centre into a maze of closures and crowds — our drivers know which streets a coach can still use and where a group can be set down within walking distance of the Royal Mile. Come winter, we handle Hogmanay parties and the Christmas markets on Princes Street.\n\nMurrayfield is a fixture in our diary through the Six Nations and autumn internationals, with group runs from hotels across the Lothians and post-match pickups arranged before kick-off. We also cover graduations, Edinburgh Castle and Old Town sightseeing tours, and corporate travel to the financial district and the EICC.\n\nEdinburgh makes a superb base for exploring, too. We run day trips out to the Scottish Borders, over the Forth bridges to Fife, and along the coast, as well as Edinburgh Airport transfers with room for luggage and an early start when your flight demands it.",
                'why_choose_points' => [
                    ['title' => 'Festival-season know-how', 'description' => 'During the Fringe and Hogmanay our drivers work around the city-centre road closures and crowds to get your group as close as the rules allow.'],
                    ['title' => 'Six Nations at Murrayfield', 'description' => "Rugby group travel with drop-off near the stadium and a pre-agreed pickup point, so the post-match scramble for transport isn't your problem."],
                    ['title' => 'Tours beyond the city', 'description' => 'Day trips to the Borders, Fife and the coast, plus Old Town and castle sightseeing runs with a driver who knows the one-way system.'],
                    ['title' => 'Edinburgh Airport transfers', 'description' => 'Reliable early-morning and late-night airport runs with full luggage capacity for the whole party.'],
                ],
                'faqs' => [
                    ['question' => 'Can you get a group close to the Royal Mile during the Fringe?', 'answer' => 'As close as the closures allow. We know the permitted coach routes and set-down points around the Old Town in August and will advise the nearest practical drop-off before the day.'],
                    ['question' => 'Do you cover rugby travel to Murrayfield?', 'answer' => 'Yes — Murrayfield is one of our busiest Edinburgh destinations during the Six Nations, with drop-off and a confirmed post-match pickup arranged in advance.'],
                    ['question' => 'How early can you collect for an Edinburgh Airport flight?', 'answer' => 'As early as you need. We regularly run pre-dawn transfers to Edinburgh Airport and plan the pickup time around your check-in.'],
                ],
            ],
            [
                'name' => 'Bristol',
                'meta_title' => 'Coach Hire Bristol | Minibus & Coach Hire with Driver — Brit Travel',
                'meta_description' => 'Coach and minibus hire in Bristol. Ashton Gate event travel, Bristol Airport transfers, Balloon Fiesta and West Country day trips. Free same-day quote from Brit Travel.',
                'intro_heading' => 'Coach Hire in Bristol and the South West',
                'intro_content' => "Bristol is a compact, hilly city with a harbour at its heart, and getting a group around it — over to Clifton and the Suspension Bridge, down to the waterfront, or out to Ashton Gate — is easier with a driver who knows its quirks. Brit Travel runs chauffeured coaches and minibuses across Bristol and the wider West of England, from Bath and the Mendips to the M4 and M5 corridors.\n\nThe city's events keep us busy: matchdays at Ashton Gate, gigs and shows in the harbourside venues, and the Bristol International Balloon Fiesta each summer, when Ashton Court fills and parking vanishes — exactly the kind of day a coach solves. We also cover the two universities for graduations and society trips, and corporate travel around Aztec West and the Temple Quarter.\n\nBristol Airport sits just south of the city, and airport transfers are one of our most requested services, with early pickups from across the region. Bristol also makes a fine base for West Country touring — Bath, Cheddar Gorge, Wells and the Cotswolds are all within easy reach for a day trip.",
                'why_choose_points' => [
                    ['title' => 'Balloon Fiesta and festival travel', 'description' => 'When Ashton Court and the harbourside fill up, a coach beats the parking scramble — we plan drop-offs and pickups around the crowds and road closures.'],
                    ['title' => 'Ashton Gate event days', 'description' => 'Group travel to matches and concerts at Ashton Gate, with a driver who knows the set-down points and the post-event traffic.'],
                    ['title' => 'West Country day trips', 'description' => 'Easy runs to Bath, Cheddar Gorge, Wells and the Cotswolds, all comfortable day trips from the centre of Bristol.'],
                    ['title' => 'Bristol Airport transfers', 'description' => 'Punctual transfers to and from Bristol Airport, with enough hold space for luggage and an early start when you need one.'],
                ],
                'faqs' => [
                    ['question' => 'Can a coach get up to Clifton and the Suspension Bridge?', 'answer' => 'Yes, though some Clifton streets are narrow — our drivers know the coach-friendly routes and the best set-down points for the bridge and observatory.'],
                    ['question' => 'Do you provide transport for the Balloon Fiesta?', 'answer' => 'We do — the Fiesta is one of our busiest Bristol dates, and group coach travel avoids the parking and traffic problems around Ashton Court entirely.'],
                    ['question' => 'Is Bristol Airport included in your coverage?', 'answer' => 'Yes. Bristol Airport transfers are a core service, with pickups from across Bristol, Bath and the surrounding area.'],
                ],
            ],
            [
                'name' => 'Southampton',
                'meta_title' => 'Coach Hire Southampton | Cruise Transfers & Coach Hire — Brit Travel',
                'meta_description' => 'Coach and minibus hire in Southampton. Cruise terminal transfers, St Mary\'s Stadium travel, New Forest day trips and airport runs. Free quote from Brit Travel.',
                'intro_heading' => 'Coach Hire in Southampton and Cruise Terminal Transfers',
                'intro_content' => "Southampton is one of Europe's busiest cruise ports, and cruise transfers are the service groups ask us for most. Brit Travel runs coaches and minibuses to all of the city's terminals — Ocean, Mayflower, Horizon and City — timed around your embarkation, with plenty of hold space for the mountain of luggage a cruise demands. We collect from across the south, from London and the Home Counties down to the coast.\n\nIn the city itself we cover matchdays at St Mary's Stadium, graduations at the University of Southampton and Solent University, and corporate travel around the business parks and the port. Our drivers know the ring road, the terminal access routes and where a coach can wait during a busy turnaround day.\n\nSouthampton is also the gateway to the New Forest and the Solent. We run day trips into the Forest and down to the coast at Lymington and Beaulieu, ferry connections for the Isle of Wight, and airport transfers to Southampton, Bournemouth, Heathrow and Gatwick.",
                'why_choose_points' => [
                    ['title' => 'Cruise terminal transfers', 'description' => "Timed runs to Southampton's Ocean, Mayflower, Horizon and City terminals, with luggage space for a full cruise wardrobe and a driver who knows the port."],
                    ['title' => 'New Forest day trips', 'description' => 'Comfortable coaches for days out in the New Forest, Beaulieu and along the Solent coast, straight from your door.'],
                    ['title' => 'St Mary\'s and university travel', 'description' => "Group transport for matchdays at St Mary's Stadium and for graduations and society trips at the city's universities."],
                    ['title' => 'Airport and ferry links', 'description' => 'Transfers to Southampton, Bournemouth, Heathrow and Gatwick airports, plus Isle of Wight ferry connections for the whole group.'],
                ],
                'faqs' => [
                    ['question' => 'Do you time cruise transfers around embarkation?', 'answer' => 'Always. We build the pickup time around your terminal\'s check-in window so the group arrives with time to spare, and we can arrange the return collection for your disembarkation day too.'],
                    ['question' => 'Which Southampton cruise terminals do you cover?', 'answer' => 'All of them — Ocean, Mayflower, Horizon and City — with drivers familiar with the access routes and waiting arrangements at each.'],
                    ['question' => 'Can you run day trips into the New Forest?', 'answer' => 'Yes, the New Forest is one of our most popular Southampton day trips, and we can include stops at Beaulieu, Lyndhurst or the coast to suit your group.'],
                ],
            ],
            [
                'name' => 'Swansea',
                'meta_title' => 'Coach Hire Swansea | Gower & Minibus Hire with Driver — Brit Travel',
                'meta_description' => 'Coach and minibus hire in Swansea. Gower Peninsula and Mumbles day trips, Swansea.com Stadium travel, weddings and Brecon Beacons tours. Free quote from Brit Travel.',
                'intro_heading' => 'Coach Hire in Swansea and Across the Gower',
                'intro_content' => "Swansea sits between a lively city centre and one of Britain's most beautiful coastlines, and Brit Travel covers both. Our chauffeured coaches and minibuses run throughout the city and out along the seafront to Mumbles, with drivers who know the roads down onto the Gower Peninsula — Britain's first Area of Outstanding Natural Beauty and home to the sweep of Rhossili Bay.\n\nIn the city we handle matchdays and concerts at the Swansea.com Stadium, graduations at Swansea University's Bay and Singleton campuses, and corporate travel around the SA1 waterfront. Weddings are a speciality, with guest shuttles between venues across Gower and the Vale of Glamorgan.\n\nSwansea is also a springboard for the wider region. We run day trips up into the Brecon Beacons, along the Heritage Coast, and across to Cardiff for events at the Principality Stadium, as well as airport transfers to Cardiff and Bristol for groups flying out.",
                'why_choose_points' => [
                    ['title' => 'Gower and coastal day trips', 'description' => 'Runs down to Mumbles, Three Cliffs and Rhossili, with drivers used to the narrow lanes and car-park pinch points of the Gower Peninsula.'],
                    ['title' => 'Wedding guest shuttles', 'description' => 'Smooth transport between ceremony and reception venues across Gower and the Vale, keeping guests together and on time.'],
                    ['title' => 'Stadium and university travel', 'description' => 'Group transport to the Swansea.com Stadium and both university campuses, for matchdays, concerts and graduations alike.'],
                    ['title' => 'Brecon Beacons and beyond', 'description' => 'Day trips into the Brecon Beacons and along the Heritage Coast, plus event travel across to Cardiff.'],
                ],
                'faqs' => [
                    ['question' => 'Can you get a coach down to Rhossili on the Gower?', 'answer' => "Yes, though the Gower lanes are narrow and car parks fill fast — our drivers know the routes and set-down points, and we'll advise the best plan for busy summer days."],
                    ['question' => 'Do you provide wedding transport around Swansea and Gower?', 'answer' => 'We do. Guest shuttles between Gower and Vale of Glamorgan venues are one of our most requested Swansea services, timed around your ceremony and reception.'],
                    ['question' => 'Can you run trips from Swansea to the Brecon Beacons?', 'answer' => "Yes — the Brecon Beacons make an excellent day trip from Swansea, and we can tailor the route and stops to your group's plans."],
                ],
            ],
            [
                'name' => 'Leeds',
                'meta_title' => 'Coach Hire Leeds | Minibus & Coach Hire with Driver — Brit Travel',
                'meta_description' => 'Coach and minibus hire in Leeds. Elland Road and First Direct Arena travel, Yorkshire Dales day trips, York races and airport transfers. Free quote from Brit Travel.',
                'intro_heading' => 'Coach Hire in Leeds and West Yorkshire',
                'intro_content' => "Leeds is the commercial heart of West Yorkshire, and Brit Travel keeps its groups moving — from a concert at the First Direct Arena to a matchday at Elland Road or a day at York Races. Our coaches and minibuses cover the city centre, the university quarter and the wider county, with drivers who know the loop road, the arena crowds and the shopping-day traffic around Trinity and the Corn Exchange.\n\nThe city's calendar keeps us busy with corporate travel around the financial district, graduations at the University of Leeds and Leeds Beckett, and party groups heading out for the night. Race-day trips to York and Wetherby are a Yorkshire tradition, and we handle them with pickups arranged around the first race.\n\nLeeds is also the gateway to the Yorkshire Dales. We run day trips up to Bolton Abbey, the Dales villages and the moors, along with Leeds Bradford Airport transfers for groups catching an early flight.",
                'why_choose_points' => [
                    ['title' => 'Arena and stadium travel', 'description' => 'Group runs to the First Direct Arena, Elland Road and Headingley, with drivers who plan around the crowds and confirm a pickup point in advance.'],
                    ['title' => 'Yorkshire Dales day trips', 'description' => 'Comfortable coaches for Bolton Abbey, the Dales villages and the moors, with drivers who know the country roads.'],
                    ['title' => 'Race-day travel to York and Wetherby', 'description' => "A Yorkshire day-out staple, with pickups timed around the first race and a driver waiting when the last one's run."],
                    ['title' => 'Leeds Bradford Airport transfers', 'description' => 'Early-morning airport runs with full luggage capacity, collecting from across Leeds and West Yorkshire.'],
                ],
                'faqs' => [
                    ['question' => 'Do you run race-day trips from Leeds to York?', 'answer' => 'Yes — York and Wetherby race days are among our most popular Leeds bookings, with the pickup timed around the first race and a confirmed collection point for afterwards.'],
                    ['question' => 'Can you get a group to the Yorkshire Dales for the day?', 'answer' => "Absolutely. Bolton Abbey, Grassington and the wider Dales are easy day trips from Leeds, and we'll build in the stops your group wants."],
                    ['question' => 'Is Leeds Bradford Airport within your coverage?', 'answer' => 'Yes, Leeds Bradford Airport transfers are a core service, with early-morning departures from across West Yorkshire.'],
                ],
            ],
            [
                'name' => 'Sheffield',
                'meta_title' => 'Coach Hire Sheffield | Peak District & Coach Hire with Driver — Brit Travel',
                'meta_description' => 'Coach and minibus hire in Sheffield. Peak District day trips, Bramall Lane and Utilita Arena travel, Meadowhall shopping and university trips. Free quote from Brit Travel.',
                'intro_heading' => 'Coach Hire in Sheffield and the Peak District Gateway',
                'intro_content' => "Sheffield is a city built across seven hills on the very edge of the Peak District, and that setting shapes the trips groups ask us for. Brit Travel runs coaches and minibuses throughout the city and straight out into the National Park — to Chatsworth, Castleton and the moors — with drivers used to the steep gradients and winding roads the Peak throws at you.\n\nIn the city we cover matchdays at Bramall Lane and Hillsborough, concerts at the Utilita Arena and Sheffield City Hall, and the World Snooker Championship each spring, when the Crucible draws crowds into the centre. The two universities keep us busy with graduations and society trips, and Meadowhall is a regular shopping-day destination.\n\nSheffield's position on the M1 also makes it a practical base for wider travel — corporate trips, airport transfers to Manchester, Leeds Bradford and East Midlands, and group outings across South Yorkshire and the Peak.",
                'why_choose_points' => [
                    ['title' => 'Peak District day trips', 'description' => "Runs out to Chatsworth, Castleton, Bakewell and the moors, with drivers confident on the Peak's steep, winding roads."],
                    ['title' => 'Matchday and arena travel', 'description' => 'Group transport to Bramall Lane, Hillsborough and the Utilita Arena, planned around the crowds and city-centre closures.'],
                    ['title' => 'Meadowhall and shopping trips', 'description' => "Easy group runs to Meadowhall and the city centre, with a driver handling the parking so you don't have to."],
                    ['title' => 'Airport transfers on the M1', 'description' => 'Transfers to Manchester, Leeds Bradford and East Midlands airports, with early pickups from across Sheffield.'],
                ],
                'faqs' => [
                    ['question' => 'Can you take a group into the Peak District for the day?', 'answer' => "Yes — the Peak District is right on Sheffield's doorstep, and Chatsworth, Bakewell and Castleton are among our most popular day trips, with drivers used to the terrain."],
                    ['question' => 'Do you cover matchday travel to Bramall Lane and Hillsborough?', 'answer' => 'We do, for both grounds, with drop-off at the nearest permitted point and a pickup location confirmed before kick-off.'],
                    ['question' => 'Which airports do you cover from Sheffield?', 'answer' => 'We regularly transfer Sheffield groups to Manchester, Leeds Bradford and East Midlands airports, including early-morning departures.'],
                ],
            ],
            [
                'name' => 'Newcastle upon Tyne',
                'meta_title' => 'Coach Hire Newcastle | Minibus & Coach Hire with Driver — Brit Travel',
                'meta_description' => "Coach and minibus hire in Newcastle upon Tyne. St James' Park and Quayside travel, stag and hen parties, Hadrian's Wall and Northumberland day trips. Free quote.",
                'intro_heading' => 'Coach Hire in Newcastle upon Tyne and the North East',
                'intro_content' => "Newcastle knows how to put on an occasion, and Brit Travel makes sure groups get to the heart of it. From a matchday at St James' Park to a night out on the Quayside or a celebration in the Bigg Market, our coaches and minibuses cover the city and the wider Tyneside, with drivers who know the bridges, the one-way system and where a coach can set a group down close to the action.\n\nStag and hen parties are a Newcastle speciality, and we handle the transport end of the weekend so nobody's counting heads at a taxi rank. We also cover concerts and shows at the Utilita Arena and the venues across the river in Gateshead, graduations at Newcastle and Northumbria universities, and corporate travel around the business parks.\n\nThe North East is made for touring, too. We run day trips out to Hadrian's Wall, up the Northumberland coast to Bamburgh and Alnwick, and down to Durham, along with Newcastle Airport transfers timed around your flight.",
                'why_choose_points' => [
                    ['title' => 'Stag and hen weekend transport', 'description' => "We take care of the group transport across a Newcastle weekend, from arrival to the Quayside and back, so nobody's left behind."],
                    ['title' => "St James' Park matchdays", 'description' => 'Group runs to St James\' Park with a driver who knows the city-centre traffic and where a coach can set down near the ground.'],
                    ['title' => 'Northumberland and Hadrian\'s Wall', 'description' => "Day trips up the coast to Bamburgh and Alnwick, out to Hadrian's Wall and down to Durham, all comfortable runs from the city."],
                    ['title' => 'Newcastle Airport transfers', 'description' => 'Punctual transfers to Newcastle Airport with room for luggage, collecting from across Tyneside.'],
                ],
                'faqs' => [
                    ['question' => 'Do you provide transport for stag and hen weekends?', 'answer' => "Yes — Newcastle stag and hen parties are one of our specialities, and we'll plan the group transport around your itinerary for the whole weekend."],
                    ['question' => "Can you get a group close to St James' Park on a matchday?", 'answer' => 'As close as the traffic management allows. Our drivers know the city-centre set-down points and will confirm a pickup spot for after the final whistle.'],
                    ['question' => "Can you run day trips to Hadrian's Wall or the Northumberland coast?", 'answer' => 'Absolutely. Hadrian\'s Wall, Bamburgh, Alnwick and the coast are all popular day trips from Newcastle, tailored to what your group wants to see.'],
                ],
            ],
        ];
    }
}

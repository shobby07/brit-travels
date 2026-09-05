/**
 * UK address autocomplete for the pickup / drop-off / via-stop fields, built on
 * the Places API (New) Autocomplete Data API.
 *
 * We drive our own dropdown rather than dropping in <gmp-place-autocomplete>
 * because that element replaces the <input>, which would break the `name`
 * attributes, `old()` repopulation and @error blocks the Blade forms rely on.
 * Here the original input is left untouched and only gains a listbox.
 *
 * No Place Details request is made: the suggestion text is the address we email,
 * so selecting a place costs nothing beyond the autocomplete call itself.
 *
 * Opt in per field with `data-place-autocomplete`, and add
 * `data-place-theme="dark"` on inputs sitting on a dark background.
 */

const KEY = document.querySelector('meta[name="google-maps-key"]')?.content?.trim();

const MIN_QUERY_LENGTH = 2;
const DEBOUNCE_MS = 150;
const CACHE_LIMIT = 50;

// Google's wordmark colours, for the attribution footer the Places terms
// require whenever suggestions are shown outside a Google map.
const GOOGLE_LETTERS = [
    ['G', '#4285F4'],
    ['o', '#EA4335'],
    ['o', '#FBBC05'],
    ['g', '#4285F4'],
    ['l', '#34A853'],
    ['e', '#EA4335'],
];

let libraryPromise = null;

/**
 * Loads the Maps JS API once, on demand, and resolves with the places library.
 * Callers can fire this eagerly on focus so the SDK is warm before the first
 * keystroke finishes debouncing.
 */
function loadPlacesLibrary() {
    if (libraryPromise) return libraryPromise;

    libraryPromise = new Promise((resolve, reject) => {
        const callbackName = '__britTravelMapsReady';

        window[callbackName] = () => {
            delete window[callbackName];
            google.maps.importLibrary('places').then(resolve, reject);
        };

        const params = new URLSearchParams({
            key: KEY,
            v: 'weekly',
            libraries: 'places',
            language: 'en-GB',
            region: 'GB',
            loading: 'async',
            callback: callbackName,
        });

        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?${params}`;
        script.async = true;
        script.onerror = () => reject(new Error('Google Maps JS API failed to load'));
        document.head.appendChild(script);
    });

    return libraryPromise;
}

/**
 * Renders a Places FormattableText into `parent`, wrapping the matched ranges in
 * <strong> so the typed prefix reads bold the way Google's own widget shows it.
 */
function appendFormatted(parent, formattable, matchClass) {
    const text = formattable?.text ?? '';
    const matches = formattable?.matches ?? [];

    if (! matches.length) {
        parent.append(text);
        return;
    }

    let cursor = 0;

    for (const match of matches) {
        const start = Math.max(match.startOffset ?? 0, cursor);
        const end = Math.min(match.endOffset ?? text.length, text.length);
        if (end <= start) continue;

        if (start > cursor) parent.append(text.slice(cursor, start));

        const strong = document.createElement('strong');
        strong.className = matchClass;
        strong.textContent = text.slice(start, end);
        parent.append(strong);

        cursor = end;
    }

    if (cursor < text.length) parent.append(text.slice(cursor));
}

function buildAttribution() {
    const footer = document.createElement('div');
    footer.className = 'places-dropdown__attribution';
    footer.append('powered by ');

    for (const [letter, colour] of GOOGLE_LETTERS) {
        const span = document.createElement('span');
        span.style.color = colour;
        span.textContent = letter;
        footer.append(span);
    }

    return footer;
}

let instanceCount = 0;

class PlaceAutocomplete {
    constructor(input) {
        this.input = input;
        this.id = `places-listbox-${++instanceCount}`;
        this.dark = input.dataset.placeTheme === 'dark';

        this.suggestions = [];
        this.activeIndex = -1;
        this.isOpen = false;
        this.cache = new Map();
        this.requestSeq = 0;
        this.debounceTimer = null;
        this.sessionToken = null;

        this.buildDropdown();
        this.bindInput();
    }

    buildDropdown() {
        // Parked on <body> in document coordinates so neither the hero's
        // backdrop-blur containing block nor any overflow-hidden ancestor can
        // clip it. Absolute (not fixed) means it scrolls with the page for free.
        this.list = document.createElement('ul');
        this.list.className = `places-dropdown${this.dark ? ' places-dropdown--dark' : ''}`;
        this.list.id = this.id;
        this.list.setAttribute('role', 'listbox');
        this.list.hidden = true;
        document.body.appendChild(this.list);

        this.input.setAttribute('role', 'combobox');
        this.input.setAttribute('aria-autocomplete', 'list');
        this.input.setAttribute('aria-expanded', 'false');
        this.input.setAttribute('aria-controls', this.id);
        this.input.setAttribute('autocomplete', 'off');
        this.input.setAttribute('autocapitalize', 'off');
        this.input.setAttribute('spellcheck', 'false');
    }

    bindInput() {
        // Warm the SDK the moment the field is touched, so the network cost of
        // loading it overlaps with the visitor typing rather than following it.
        const warm = () => loadPlacesLibrary().catch(() => {});
        this.input.addEventListener('focus', warm, { once: true });
        this.input.addEventListener('pointerenter', warm, { once: true });

        this.input.addEventListener('input', () => this.onInput());
        this.input.addEventListener('keydown', (event) => this.onKeydown(event));
        this.input.addEventListener('blur', () => {
            // Deferred so a click landing on an option still resolves first.
            setTimeout(() => this.close(), 120);
        });

        this.list.addEventListener('pointerdown', (event) => {
            // Keeps focus in the input so blur never races the click.
            event.preventDefault();
        });

        this.list.addEventListener('click', (event) => {
            const option = event.target.closest('[data-index]');
            if (option) this.select(Number(option.dataset.index));
        });
    }

    onInput() {
        const query = this.input.value.trim();

        clearTimeout(this.debounceTimer);

        if (query.length < MIN_QUERY_LENGTH) {
            this.close();
            return;
        }

        const cached = this.cache.get(query);
        if (cached) {
            // Backspacing into an already-seen query repaints with no network hop.
            this.render(cached);
            return;
        }

        this.debounceTimer = setTimeout(() => this.fetch(query), DEBOUNCE_MS);
    }

    async fetch(query) {
        // Monotonic sequence guard: a slow early response can never overwrite the
        // results of a later, faster one.
        const seq = ++this.requestSeq;

        let places;
        try {
            places = await loadPlacesLibrary();
        } catch {
            return; // No SDK: the field stays a plain text input.
        }

        if (seq !== this.requestSeq) return;

        this.sessionToken ??= new places.AutocompleteSessionToken();

        try {
            const { suggestions } = await places.AutocompleteSuggestion.fetchAutocompleteSuggestions({
                input: query,
                sessionToken: this.sessionToken,
                // Restricted to the UK — this is a UK coach operator, and a
                // smaller candidate set comes back faster. No type filter, so
                // street addresses appear alongside towns and landmarks.
                includedRegionCodes: ['gb'],
                language: 'en-GB',
                region: 'gb',
            });

            if (seq !== this.requestSeq) return;

            const predictions = suggestions
                .map((suggestion) => suggestion.placePrediction)
                .filter(Boolean);

            this.remember(query, predictions);
            this.render(predictions);
        } catch (error) {
            if (seq === this.requestSeq) this.close();
            console.error('Places autocomplete request failed', error);
        }
    }

    remember(query, predictions) {
        if (this.cache.size >= CACHE_LIMIT) {
            this.cache.delete(this.cache.keys().next().value);
        }
        this.cache.set(query, predictions);
    }

    render(predictions) {
        this.suggestions = predictions;
        this.activeIndex = -1;
        this.list.replaceChildren();

        if (! predictions.length) {
            this.close();
            return;
        }

        predictions.forEach((prediction, index) => {
            const option = document.createElement('li');
            option.className = 'places-dropdown__option';
            option.id = `${this.id}-option-${index}`;
            option.dataset.index = String(index);
            option.setAttribute('role', 'option');
            option.setAttribute('aria-selected', 'false');

            const pin = document.createElement('span');
            pin.className = 'places-dropdown__pin';
            pin.setAttribute('aria-hidden', 'true');
            pin.innerHTML =
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">' +
                '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>' +
                '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>' +
                '</svg>';

            const label = document.createElement('span');
            label.className = 'places-dropdown__label';

            const main = document.createElement('span');
            main.className = 'places-dropdown__main';
            appendFormatted(main, prediction.mainText ?? prediction.text, 'places-dropdown__match');
            label.append(main);

            if (prediction.secondaryText?.text) {
                const secondary = document.createElement('span');
                secondary.className = 'places-dropdown__secondary';
                appendFormatted(secondary, prediction.secondaryText, 'places-dropdown__match');
                label.append(' ', secondary);
            }

            option.append(pin, label);
            this.list.append(option);
        });

        this.list.append(buildAttribution());
        this.open();
    }

    open() {
        this.position();
        this.list.hidden = false;
        this.isOpen = true;
        this.input.setAttribute('aria-expanded', 'true');
    }

    close() {
        if (! this.isOpen) return;
        this.list.hidden = true;
        this.isOpen = false;
        this.activeIndex = -1;
        this.input.setAttribute('aria-expanded', 'false');
        this.input.removeAttribute('aria-activedescendant');
    }

    position() {
        const rect = this.input.getBoundingClientRect();
        this.list.style.top = `${rect.bottom + window.scrollY + 6}px`;
        this.list.style.left = `${rect.left + window.scrollX}px`;
        this.list.style.width = `${rect.width}px`;
    }

    onKeydown(event) {
        if (! this.isOpen) return;

        switch (event.key) {
            case 'ArrowDown':
                event.preventDefault();
                this.highlight(this.activeIndex + 1);
                break;
            case 'ArrowUp':
                event.preventDefault();
                this.highlight(this.activeIndex - 1);
                break;
            case 'Enter':
                if (this.activeIndex >= 0) {
                    event.preventDefault();
                    this.select(this.activeIndex);
                }
                break;
            case 'Escape':
                this.close();
                break;
            case 'Tab':
                this.close();
                break;
        }
    }

    highlight(index) {
        const count = this.suggestions.length;
        if (! count) return;

        const next = (index + count) % count;
        const options = this.list.querySelectorAll('[data-index]');

        options.forEach((option, i) => {
            const active = i === next;
            option.classList.toggle('is-active', active);
            option.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        this.activeIndex = next;
        this.input.setAttribute('aria-activedescendant', `${this.id}-option-${next}`);
        options[next].scrollIntoView({ block: 'nearest' });
    }

    select(index) {
        const prediction = this.suggestions[index];
        if (! prediction) return;

        this.input.value = prediction.text?.text ?? this.input.value;
        // A new session starts after each pick, per the Places session contract.
        this.sessionToken = null;
        this.close();

        // Let Alpine's x-model (via stops) and any validation pick up the change.
        this.input.dispatchEvent(new Event('input', { bubbles: true }));
        this.input.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

const attached = new WeakSet();

function attach(root = document) {
    root.querySelectorAll?.('input[data-place-autocomplete]').forEach((input) => {
        if (attached.has(input)) return;
        attached.add(input);
        new PlaceAutocomplete(input);
    });
}

if (KEY) {
    attach();

    // Via-stop rows are appended by Alpine after load, so watch for new fields.
    new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (node.nodeType !== Node.ELEMENT_NODE) continue;
                if (node.matches?.('input[data-place-autocomplete]')) {
                    if (! attached.has(node)) {
                        attached.add(node);
                        new PlaceAutocomplete(node);
                    }
                }
                attach(node);
            }
        }
    }).observe(document.body, { childList: true, subtree: true });
}

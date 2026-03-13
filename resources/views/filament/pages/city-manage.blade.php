<x-filament-panels::page>
    {{ $this->table }}
</x-filament-panels::page>

@script
<script>
    const apiKey = @js(\App\Models\Setting::get('google_places_api_key'));
    const countryCode = @js(auth()->user()->currentCountry?->iso_code ?? 'US');

    // Add CSS for Google Places autocomplete dropdown z-index
    const style = document.createElement('style');
    style.textContent = '.pac-container { z-index: 10000 !important; }';
    document.head.appendChild(style);

    if (apiKey) {
        if (window.google?.maps?.places) {
            setupObserver();
        } else {
            const script = document.createElement('script');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places`;
            script.async = true;
            script.onload = () => setupObserver();
            document.head.appendChild(script);
        }
    }

    function setupObserver() {
        const observer = new MutationObserver(() => {
            const input = document.querySelector('[data-field="city-name"]');
            if (input && !input._autocompleteInit) {
                input._autocompleteInit = true;
                initAutocomplete(input);
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    function initAutocomplete(input) {
        const autocomplete = new google.maps.places.Autocomplete(input, {
            types: ['(cities)'],
            componentRestrictions: { country: countryCode.toLowerCase() },
            fields: ['name', 'geometry', 'place_id', 'address_components'],
        });

        autocomplete.addListener('place_changed', () => {
            const place = autocomplete.getPlace();
            if (!place.geometry) return;

            const lat = place.geometry.location.lat();
            const lng = place.geometry.location.lng();
            const state = extractState(place.address_components);

            setFieldValue('[data-field="city-name"]', place.name);
            setFieldValue('[data-field="latitude"]', lat.toFixed(7));
            setFieldValue('[data-field="longitude"]', lng.toFixed(7));

            $wire.set('selectedState', state);
            $wire.set('selectedGooglePlaceId', place.place_id);
        });
    }

    function setFieldValue(selector, value) {
        const input = document.querySelector(selector);
        if (!input) return;

        // Try Alpine state first (Filament uses Alpine x-model with $wire.$entangle)
        const alpineEl = input.closest('[x-data]');
        if (alpineEl) {
            try {
                const data = Alpine.$data(alpineEl);
                if ('state' in data) {
                    data.state = value;
                    return;
                }
            } catch (e) {}
        }

        // Fallback: set value and dispatch events
        input.value = value;
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    function extractState(components) {
        if (!components) return null;
        const state = components.find(c => c.types.includes('administrative_area_level_1'));
        return state ? state.long_name : null;
    }
</script>
@endscript

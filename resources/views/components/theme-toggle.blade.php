<button type="button" {{ $attributes->merge(['class' => 'btn-icon']) }} x-data @click="$store.theme.cycle()"
    :aria-label="'Colour theme: ' + $store.theme.mode + '. Switch theme'" :title="'Theme: ' + $store.theme.mode">
    <span x-show="$store.theme.mode === 'light'"><x-icon name="sun" :size="18"/></span>
    <span x-show="$store.theme.mode === 'dark'" x-cloak><x-icon name="moon" :size="18"/></span>
    <span x-show="$store.theme.mode === 'system'" x-cloak><x-icon name="monitor" :size="18"/></span>
</button>

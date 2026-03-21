@props(['label', 'name', 'type' => 'text', 'value' => null, 'hint' => null, 'bag' => 'default', 'errorKey' => null])
@php
    $key = $errorKey ?? str_replace(['[', ']'], ['.', ''], $name);
    $error = $errors->getBag($bag)->first($key);
    $id = $attributes->get('id', 'f-'.str_replace(['[', ']', '.'], '-', $name));
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="label">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" {{ $attributes->except(['class', 'id'])->merge(['class' => 'input', 'rows' => 4]) }} @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>{{ old($key, $value) }}</textarea>
    @elseif ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->except(['class', 'id'])->merge(['class' => 'input']) }} @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
            {{ $slot }}
        </select>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type === 'password' ? '' : old($key, $value) }}"
            {{ $attributes->except(['class', 'id'])->merge(['class' => 'input']) }} @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif>
    @endif
    @if ($error)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm text-accent">{{ $error }}</p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-muted">{{ $hint }}</p>
    @endif
</div>

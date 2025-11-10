@props([
    'id' => 'historiaSearch',
    'name' => null,
    'placeholder' => 'Buscar historia clínica...',
    'showIcon' => true,
    'icon' => 'fas fa-search',
    'containerClass' => 'historia-search',
    'selectClass' => '',
    'mode' => 'select',
    'target' => null,
    'context' => null,
    'required' => false,
])

@php
    $labelClasses = trim($containerClass);
@endphp

<label for="{{ $id }}" {{ $attributes->merge(['class' => $labelClasses]) }}>
    @if($showIcon)
        <i class="{{ $icon }}" aria-hidden="true"></i>
    @endif
    <select
        id="{{ $id }}"
        @if($name) name="{{ $name }}" @endif
        class="historia-search__select js-historia-search {{ $selectClass }}"
        data-placeholder="{{ $placeholder }}"
        data-search-mode="{{ $mode }}"
        @if($target) data-search-target="{{ $target }}" @endif
        @if($context) data-search-context="{{ $context }}" @endif
        @if($required) required @endif
    >
        <option value="">{{ $placeholder }}</option>
    </select>
</label>

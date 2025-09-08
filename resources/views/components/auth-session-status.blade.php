@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-s text-green-600']) }}>
        {{ $status }}
    </div>
@endif

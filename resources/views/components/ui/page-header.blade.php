@props(['title' => '', 'subtitle' => null])
<div class="flex items-center justify-between gap-3 mb-4">
    <div>
        @if($subtitle)
            <p class="text-sm text-gray-500">{{ $subtitle }}</p>
        @endif
        <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
    </div>
    @isset($actions)
        <div class="flex items-center gap-2">{{ $actions }}</div>
    @endisset
</div>

@props(['label', 'value', 'icon', 'color' => 'indigo', 'trend' => null, 'trendLabel' => null])
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $value }}</p>
            @if($trend !== null)
                <p class="text-xs mt-2 {{ $trend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                    @if($trendLabel) <span class="text-gray-400">{{ $trendLabel }}</span> @endif
                </p>
            @endif
        </div>
        <div class="h-12 w-12 bg-{{ $color }}-100 rounded-lg flex items-center justify-center shrink-0">
            {!! $icon !!}
        </div>
    </div>
</div>

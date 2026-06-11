@props(['color' => 'gray'])
@php
    $map = [
        'green'  => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'red'    => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        'yellow' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'blue'   => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
        'purple' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
        'gray'   => 'bg-gray-100 text-gray-700 ring-gray-500/20',
    ];
@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $map[$color] ?? $map['gray'] }}">{{ $slot }}</span>

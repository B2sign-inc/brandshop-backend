@php
    $wrapBegin = $wrapBegin ?? '';
    $wrapEnd = $wrapEnd ?? '';
    $itemBegin = $itemBegin ?? '';
    $itemEnd = $itemEnd ?? '';
@endphp

{!! $wrapBegin !!}

@foreach ($categories as $category)
    {!! $itemBegin !!}

    {{ $prefix }} {{ $category->title }} @include('partials.delete-button', ['url' => route('admin.categories.destroy', $category)])

    {!! $itemEnd ?? '<br>' !!}
    @if ($category->children && $category->children->count() > 0)
        @include('admin.categories.category-loop', [
            'categories' => $category->children,
            'prefix' => $prefix . '-',
            'wrapBegin' => '',
            'wrapEnd' => '',
            'itemBegin' => $itemBegin,
            'itemEnd' => $itemEnd,
            ])
    @endif


@endforeach

{!! $wrapEnd !!}

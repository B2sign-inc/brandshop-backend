@php
    $wrapBegin = $wrapBegin ?? '';
    $wrapEnd = $wrapEnd ?? '';
    $itemBegin = $itemBegin ?? '';
    $itemEnd = $itemEnd ?? '';
@endphp

{!! $wrapBegin !!}

@foreach ($categories as $category)
    {!! $itemBegin !!}

    {{ $prefix }} {{ $category->title }}

    {!! $itemEnd ?? '<br>' !!}
    @if (!empty($category->children) && count($category->children) > 0)
        @include('admin.categories.b2sign-category-loop', [
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
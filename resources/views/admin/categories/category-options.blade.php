
@foreach($categories as $category)
<option value="{{ $category->id }}">{{ $prefix }}{{ $category->title }}</option>
@if ($category->children && $category->children->count() > 0)
    @include('admin.categories.category-options', [
        'categories' => $category->children,
        'prefix' => $prefix . '&nbsp;&nbsp;',
        ])
@endif
@endforeach
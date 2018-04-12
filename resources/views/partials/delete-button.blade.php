@php
    $deleteFormId = md5($url);
@endphp

<a href="{{ $url }}"
   data-delete-form-id="{{ $deleteFormId }}"
   class="{{ $class ?? 'text-danger' }} partial-delete" data-toggle="tooltip" title="Delete">
    <i class="fas fa-trash-alt"></i> {{ $text or '' }}
</a>
<form class="delete-form" id="{{ $deleteFormId }}" method="POST" action="{{ $url }}" style="display:none;">
    <input type="hidden" name="_method" value="DELETE">
    {{ csrf_field() }}
</form>
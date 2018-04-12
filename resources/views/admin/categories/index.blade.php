@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    Your Categories
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categories.resetAndSync') }}" method="post">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary">Reset And Sync</button>
                    </form>
                </div>
                <div class="panel-body">
                    @include('admin.categories.category-loop', [
                        'categories' => $categories,
                        'prefix' => '',
                        'wrapBegin' => '<ul class="list-group list-group-flush">',
                        'wrapEnd' => '</ul>',
                        'itemBegin' => '<li class="list-group-item">',
                        'itemEnd' => '</li>',
                    ])
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    New Categories
                </div>
                <div class="card-body">
                    <form method="post">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="parent">Parent</label>
                            <select class="form-control" name="parent" id="parent">
                                @include('admin.categories.category-options', [
                                    'categories' => $categories,
                                    'prefix' => '',
                                ])
                            </select>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Save">
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-4">
            <div class="card">
                <div class="card-header">
                    B2Sign Categories
                </div>
                <div class="card-body">
                    @include('admin.categories.b2sign-category-loop', [
                        'categories' => $b2signCategories,
                        'prefix' => '',
                        'wrapBegin' => '<ul class="list-group list-group-flush">',
                        'wrapEnd' => '</ul>',
                        'itemBegin' => '<li class="list-group-item">',
                        'itemEnd' => '</li>',
                    ])
                </div>
            </div>
        </div>
    </div>


@endsection

@section('js')
<script>
    (function () {

    })()
</script>
@endsection
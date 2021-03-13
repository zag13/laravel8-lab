@extends('layouts.main')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <form action="/api/test/search">
                <div class="input-group">
                    <input type="text" class="form-control h50" name="q" placeholder="关键字..." value="{{ $q }}">
                    <span class="input-group-btn"><button class="btn btn-default h50" type="submit" type="button"><span
                                class="glyphicon glyphicon-search"></span></button></span>
                </div>
            </form>
        </div>
    </div>
    @if($q)
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default list-panel search-results">
                    <div class="panel-heading">
                        <h3 class="panel-title ">
                            <i class="fa fa-search"></i> 关于 “<span class="highlight">{{ $q }}</span>” 的搜索结果,
                            共 {{ $paginator->total() }} 条
                        </h3>
                    </div>

                    <div class="panel-body ">
                        @foreach($paginator->items() as $post)
                            <div class="result">
                                <h3 class="title">
                                    @if ($post->highlight && $post->highlight->name)
                                        @foreach($post->highlight->name as $item)
                                            {!! $item !!}
                                        @endforeach
                                    @else
                                        {{ $post->name }}
                                    @endif
                                </h3>
                                <div class="info">
                                    <h5>
                                        @if ($post->highlight && $post->highlight->address)
                                            @foreach($post->highlight->address as $item)
                                                {!! $item !!}
                                            @endforeach
                                        @else
                                            {{ $post->address }}
                                        @endif
                                    </h5>
                                </div>
                                <div class="desc">
                                    @if ($post->highlight && $post->highlight->text)
                                        @foreach($post->highlight->text as $item)
                                            {!! $item !!}
                                        @endforeach
                                    @else
                                        {{ mb_substr($post->text, 0, 100) }}......
                                    @endif
                                </div>
                                <hr>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row text-center">
            <div class="col-md-12">
                <br>
                <h2>你会搜索到什么？</h2>
                <br>
            </div>
        </div>
    @endif
@endsection

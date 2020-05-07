@extends('default.master')

@section('content')
    <section>
        <div class="container">
            <h1>404</h1>
            <h2>Ops, ocorreu um erro...</h2>
            <h3>{{$erro}}</h3>
            <h5>Informação:</h5>
            <p>
                {!! dump($dump) !!}
            </p>
        </div>
    </section>
@stop
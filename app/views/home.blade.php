@extends('default.master')

@section('content')
    <section>
        <div class="container">
            <h1>Teste</h1>
            {!! dump(\abApp\Models\Usuario::load(1)) !!}
        </div>
    </section>
@stop
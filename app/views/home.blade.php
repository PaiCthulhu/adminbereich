@extends('default.master')

@section('content')
    <section>
        <div class="container">
            <h1>Teste</h1>
            {!! dump(\abApp\Models\Usuario::find(1)) !!}
        </div>
    </section>
@stop
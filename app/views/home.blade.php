@extends('default.master')

@section('content')
    <section>
        <div class="container">
            <h1>{{$usuario->email}}</h1>
        </div>
    </section>
@stop
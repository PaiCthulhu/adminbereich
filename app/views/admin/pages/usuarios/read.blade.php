@extends('admin.master')

@section('content')
    <section class="main">
        <div class="card full">
            <div class="card-header">
                <h5 class="card-title">Usuários</h5>
                <a href="{{PATH.DS.'admin'.DS.'usuarios'.DS.'add'}}" class="btn btn-sm btn-secondary float-right">&plus; Novo</a>
            </div>
            <div class="card-body">
                @each('admin.pages.usuarios.row', $usuarios, 'usuario')
            </div>
        </div>
    </section>
@stop
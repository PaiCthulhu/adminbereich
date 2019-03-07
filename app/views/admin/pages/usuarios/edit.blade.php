@extends('admin.master')

@section('content')
    <section class="main">
        <div class="card full">
            <div class="card-header">
                <h5 class="card-title">@include('default.icon',['i'=>'user','t'=>'s']) &nbsp;Editar Usuário #{{$usuario->usuario_id}} - {{$usuario->nome}}</h5>
                <a href="{{PATH}}/admin/usuarios" class="float-right"><< Voltar</a>
            </div>
            <div class="card-body">
                <form action="{{PATH}}/admin/usuarios/update" method="POST">
                    <input type="hidden" id="id" name="usuario_id" value="{{$usuario->usuario_id}}" />
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" class="form-control" value="{{$usuario->nome}}" />
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuário:</label>
                        <input type="text" id="usuario" name="username" class="form-control" value="{{$usuario->username}}" />
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{$usuario->email}}" />
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" autocomplete="off" placeholder="Somente digite a senha se quiser alterar a atual" />
                    </div>
                    <div class="form-group text-center">
                        <button name="_edit" class="btn btn-primary" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop
@extends('admin.master')

@section('content')
    <section class="main">
        <div class="card full">
            <div class="card-header">
                <h5 class="card-title">@include('default.icon',['i'=>'user','t'=>'s']) &nbsp;Editar Usuário #{{$usuario->id_usuario}} - {{$usuario->nome}}</h5>
                <a href="{{PATH.DS.'admin'.DS.'usuarios'}}" class="float-right"><< Voltar</a>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="name" class="form-control" value="{{$usuario->nome}}" />
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuário:</label>
                        <input type="text" id="usuario" name="user" class="form-control" value="{{$usuario->username}}" />
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="mail" class="form-control" value="{{$usuario->email}}" />
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="pswd" class="form-control" autocomplete="off" placeholder="Somente digite a senha se quiser alterar a atual" />
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-primary" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop
@extends('admin.master')

@section('content')
    <section class="main">
        <div class="card full">
            <div class="card-header">
                <h5 class="card-title">@include('default.icon',['i'=>'user-plus','t'=>'s']) &nbsp;Adicionar Usuário</h5>
                <a href="{{PATH.DS.'admin'.DS.'usuarios'}}" class="float-right"><< Voltar</a>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="name" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuário:</label>
                        <input type="text" id="usuario" name="user" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="mail" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="pswd" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group text-center">
                        <button class="btn btn-primary" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop
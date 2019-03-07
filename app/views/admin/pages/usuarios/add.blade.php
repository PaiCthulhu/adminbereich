@extends('admin.master')

@section('content')
    <section class="main">
        <div class="card full">
            <div class="card-header">
                <h5 class="card-title">@include('default.icon',['i'=>'user-plus','t'=>'s']) &nbsp;Adicionar Usuário</h5>
                <a href="{{PATH}}/admin/usuarios" class="float-right"><< Voltar</a>
            </div>
            <div class="card-body">
                <form action="{{PATH}}/admin/usuarios/save" method="POST">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="nome" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="usuario">Usuário:</label>
                        <input type="text" id="usuario" name="username" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group text-center">
                        <button name="_save" class="btn btn-primary" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop
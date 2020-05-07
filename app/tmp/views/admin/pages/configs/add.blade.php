@extends('admin.master')

@section('content')
    <section class="main">
        <div class="card full">
            <div class="card-header">
                <h5 class="card-title">@include('default.icon',['i'=>'cog','t'=>'s']) &nbsp;Adicionar Configuração</h5>
                <a href="{{PATH}}/admin/configs" class="float-right"><< Voltar</a>
            </div>
            <div class="card-body">
                <form action="{{PATH}}/admin/configs/save" method="POST">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" id="nome" name="label" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="key">Código:</label>
                        <input type="text" id="key" name="key" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="valor">Valor:</label>
                        <input type="text" id="valor" name="val" class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group text-center">
                        <button name="_save" class="btn btn-primary" type="submit">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@stop
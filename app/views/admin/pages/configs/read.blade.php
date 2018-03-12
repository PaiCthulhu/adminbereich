@extends('admin.master')

@section('content')
    <section class="main">
        <div class="card full">
            <div class="card-header">
                <h5 class="card-title">@include('default.icon',['i'=>'cogs','t'=>'s']) &nbsp;Configurações</h5>
                @if (Auth::hasPerm('configs_add'))
                    <a href="{{PATH.DS.'admin'.DS.'configs'.DS.'add'}}" class="btn btn-sm btn-secondary float-right">&plus; Novo</a>
                @endif
            </div>
            <div class="card-body">
                <table class="table-view row">
                    <thead>
                    <tr>
                        <td>Título</td>
                        <td>Valor</td>
                        <td></td>
                    </tr>
                    </thead>
                    <tbody>
                    @each('admin.pages.configs.row', $configs, 'config')
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <script src="{{PATH}}/public/js/admin/admDeleteConfirmation.js"></script>
@stop
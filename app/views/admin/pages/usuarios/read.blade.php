@extends('admin.master')

@section('content')
    <section class="main">
        <div class="card full">
            <div class="card-header">
                <h5 class="card-title">@include('default.icon',['i'=>'users','t'=>'s']) &nbsp;Usuários</h5>
                @if (\AdmBereich\Auth::hasPerm('users_add'))
                    <a href="{{PATH.'/admin/usuarios/add'}}" class="btn btn-sm btn-secondary float-right">&plus; Novo</a>
                @endif
            </div>
            <div class="card-body">
                <table class="table-view row">
                    <thead>
                        <tr>
                            <td></td>
                            <td>Nome</td>
                            <td>Usuário</td>
                            <td>E-mail</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                    @each('admin.pages.usuarios.row', $usuarios, 'usuario')
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <script src="{{PATH}}/public/js/admin/admDeleteConfirmation.js"></script>
@stop
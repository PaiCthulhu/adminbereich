@extends('admin.master')

@section('content')
    <section id="login">
        <div class="container full-size">
            <div class="row full-size justify-content-center">
                <div class="col-4 align-self-center card">
                    <div class="card-header">
                        <h1>@include('default.icon', ['i'=>"sign-in-alt",'t'=>"s"]) Área Administrativa</h1>
                    </div>
                    <div class="card-body">
                        @if (\AdmBereich\Session::has('login_error'))
                            <div class="alert alert-danger" role="alert">
                                {{\AdmBereich\Session::get('login_error')}}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <p class="text-center">
                            Faça login para acessar o painel
                        </p>
                        <form method="post" action="{{PATH}}/admin/login">
                            <div class="form-group">
                                <input name="user" type="text" class="form-control" placeholder="login" />
                            </div>
                            <div class="form-group">
                                <input name="pswd" type="password" class="form-control" placeholder="senha" />
                            </div>
                            <div class="form-group text-center">
                                <input type="submit" value="Entrar" class="btn" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop
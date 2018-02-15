<tr data-id="{{$usuario->id_usuario}}">
    <td>
        <div class="avatar text-center">
            <img src="https://www.gravatar.com/avatar/{{md5(strtolower(trim($usuario->email)))}}?s=36&d=mm" />
        </div>
    </td>
    <td>
        {{$usuario->nome}}
    </td>
    <td>
        {{$usuario->username}}
    </td>
    <td>
        {{$usuario->email}}
    </td>
    <td>
        <div class="row-controls">
            <a class="btn btn-sm btn-outline-secondary" href="{{PATH}}/admin/usuarios/edit/{{$usuario->id_usuario}}" title="Editar">
                @include('default.icon',['i'=>'edit','t'=>'r'])
            </a>
            <a class="btn btn-sm btn-outline-secondary" href="{{PATH}}/admin/usuarios/delete/{{$usuario->id_usuario}}" title="Deletar">
                @include('default.icon',['i'=>'trash-alt','t'=>'r'])
            </a>
        </div>
    </td>
</tr>
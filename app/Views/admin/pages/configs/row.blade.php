<tr data-id="{{$config->config_id}}">
    <td>
        {{$config->label}}
    </td>
    <td>
        {{$config->val}}
    </td>
    <td>
        <div class="row-controls">
            <a class="btn btn-sm btn-outline-secondary" href="{{PATH}}/admin/configs/edit/{{$config->config_id}}" title="Editar">
                @include('default.icon',['i'=>'edit','t'=>'r'])
            </a>
            <a class="btn btn-sm btn-outline-secondary adm-delete" href="{{PATH}}/admin/configs/delete/{{$config->config_id}}" title="Deletar">
                @include('default.icon',['i'=>'trash-alt','t'=>'r'])
            </a>
        </div>
    </td>
</tr>
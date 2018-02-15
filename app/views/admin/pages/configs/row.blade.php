<tr data-id="{{$config->id}}">
    <td>
        {{$config->label}}
    </td>
    <td>
        {{$config->val}}
    </td>
    <td>
        <div class="row-controls">
            <a class="btn btn-sm btn-outline-secondary" href="{{PATH}}/admin/configs/edit/{{$config->id}}" title="Editar">
                @include('default.icon',['i'=>'edit','t'=>'r'])
            </a>
            <a class="btn btn-sm btn-outline-secondary" href="{{PATH}}/admin/configs/delete/{{$config->id}}" title="Deletar">
                @include('default.icon',['i'=>'trash-alt','t'=>'r'])
            </a>
        </div>
    </td>
</tr>
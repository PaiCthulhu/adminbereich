@if (isset($option['type']) && $option['type'] == 'cat')
    <li style="padding-right: 12px;">
        <div class="menu-cat" data-title="{{$option['title']}}"></div>
    </li>
@else
    <li>
        <a class="menu-opt{{preg_match("/{$option['page']}/", $_page) == 1 ?' active':''}}" href="{{PATH.DS.$option['path']}}">
            @include('default.icon',['i'=>$option['icon'],'t'=>$option['icon_type']]) {{$option['title']}}
        </a>
    </li>
@endif
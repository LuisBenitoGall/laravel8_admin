<div class="ibox-content">
    <ul>
        @php
        $allChecked = true;
        $seeAllChecked = false;
        @endphp

        @foreach($permissions as $row)
            <li class="mb-2">
                <label class="custom-chk-rdb" rel="tooltip">
                    <input type="checkbox" name="permissions[]" value="{{ $row->id }}" data-permission="{{ $row->name }}" @if(in_array($row->id, $role_permissions)) checked @endif class="chkPermission functionality-{{ $functionality->slug }}">
                    <span class="chk chksmall" style="top: 3px;"></span>
                </label>

                {{ $row->name }}
            </li>  

            @php
            if(!in_array($row->id, $role_permissions)) $allChecked = false;
            @endphp 
        @endforeach

        <hr>
        <li>
            {{-- Seleccionar todos los permisos: sólo para Super Admin --}}
            {{-- @if($seeAllChecked && Auth::user()->hasRole('Super Admin')) --}}
                @php
                $all_checked = $allChecked? 'checked':'';
                @endphp

                <label class="custom-chk-rdb" rel="tooltip">
                    <input type="checkbox" class="chkFunc" data-functionality="{{ $functionality->slug }}" {{ $all_checked }}>
                    <span class="chk chksmall" style="top: 3px;"></span>
                </label>

                <b>{!! trans('textos.todos_permisos_funcionalidad') !!}</b>
            {{-- @endif --}}
        </li>
    </ul>
</div>

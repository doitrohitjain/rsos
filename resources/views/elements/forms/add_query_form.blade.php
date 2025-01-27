<div class="">  
    @php $counter = 1; @endphp
    <script>
        var options=[];
    </script>
    @foreach(@$formFields as $k => $v)
        @if(@$v['fld'] == 'sql' || @$v['fld'] == 'url' || @$v['fld'] == 'role')
		
            <div class="input-field col s12" did="{{ $v['fld'] }}">
        @elseif(@$v['fld'] == 'role')
            <div class="input-field col s4" did="{{ $v['fld'] }}">
        @else
            <div class="input-field col s4" did="{{ $v['fld'] }}">
        @endif
        @php 
            $lbl=@$v['lbl'];
            $starSymbol=null; 
            if( @$v['is_mandatory'] == true ){
                $starSymbol = Config::get('global.starMark');
                $v['placeholder'] = @$v['placeholder'];
            }
        @endphp
        @if(@$v['input_type'] == 'text')
            @php 
            $placeholder = "". @$v['placeholder']; 
            $fld=@$v['fld'] @endphp
            <h8>{!!Form::label($fld, $lbl) !!} @php echo $starSymbol; @endphp</h8>
            <input type="text" value="{{ @$master[$fld] }}"  name="{{ $fld }}" class="form-control {{ @$v['class'] }} "    autocomplete="off" id="{{ $fld }}" placeholder="Enter {{ $placeholder }}">
        @elseif(@$v['input_type'] == 'textarea')
            @php $placeholder = "Enter ". @$v['placeholder']; $fld= @$v['fld'] @endphp
            <h8>{!!Form::label($fld, $lbl) !!} @php echo $starSymbol; @endphp</h8>
            <textarea value="{{ @$master[$fld] }}"  name="{{ $fld }}" class="form-control" . {{ $fld }}   autocomplete="off" id="{{ $fld }}" rows="5" cols="100" style="height: 10rem;" placeholder="Enter {{ $placeholder }}"></textarea>
        @elseif(@$v['input_type'] == 'select')
            @php  $placeholder = "Select ". @$v['placeholder']; $fld=@$v['fld'] @endphp
			<input type="hidden" value="3"  name="type">
        @php 
            $$fld = $v['options'];
        @endphp 
            <script>
                k = "{{ $v['fld'] }}"; 
                options[k] = "{{ @$$fld  }}";
            </script>
            <h8>{!!Form::label($fld, $lbl) !!}@php echo $starSymbol; @endphp </h8>

            @if(@$v['is_multiple'] == true)
                {!! Form::select($fld. '[]',@$v['options'],@$devloperadminrole, ['class' => 'select2 browser-default form-control center-align ' . $fld  , 'multiple' => 'multiple','id'=>$fld, ]) !!} 
            @else
                {!! Form::select($fld,@$v['options'],@$master->$fld, ['class' => 'select2 browser-default form-control center-align ' . $fld  ,'id'=>$fld, 'placeholder' => $placeholder]) !!}
            @endif
        @endif 
            @include('elements.field_error')
    </div>   
        @php $counter++; @endphp
    @endforeach  
</div>
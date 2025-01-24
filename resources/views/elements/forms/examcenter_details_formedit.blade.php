 
<h4 class="card-title"></h4>
<div class="row">
    <div class="col s12"> 
        <div class="col s12">
            
                <div class="row">
                    @php $counter = 1; @endphp
                    <script>
                        var options=[];
                    </script>
                    @foreach(@$formFields as $k => $v)
                        @if($counter%200==0)
                            </div><div class="row">
                        @endif
                        
                        @if(@$v['fld'] == "cent_name")
                            <div class="input-field col m9 s12">
                        @elseif(@$v['fld'] == "cent_add1" || @$v['fld'] == "cent_add2")
                            <div class="input-field col m6 s12">
                        @else
                            <div class="input-field col m3 s12">
                        @endif
                        @php 
                            $lbl=@$v['lbl']; 
                            
                            if(@$v['is_mandatory'] == true){
                                $starSymbol = Config::get('global.starMark');
                                $v['placeholder'] = @$v['placeholder'] . "*";
                            }
                        @endphp
                        @if(@$v['input_type'] == 'text')
                            @php    
                                $placeholder = "". @$v['placeholder']; $fld=@$v['fld'] @endphp
                                <input type="text" value="{{ @$v['default_value'] }}"  name="{{ $fld }}" class="form-control " . $fld   autocomplete="off" id="{{ $fld }}" placeholder="Enter {{ $placeholder }}">
                                
                        @elseif(@$v['input_type'] == 'select')
                            @php  $placeholder = "Select ". @$v['placeholder']; $fld=@$v['fld'] @endphp
                            @php 
                                $$fld = $v['options'];
                            @endphp

                            <script>
                                k = "{{ $v['fld'] }}"; 
                                options[k] = "{{ @$$fld  }}";
                            </script>
                            {!! Form::select($fld,@$v['options'],@$v['default_value'], ['class' => 'select2 browser-default form-control center-align ' . $fld  ,'id'=>$fld, 'placeholder' => $placeholder]) !!}
                        @endif 
                         @include('elements.field_error')
                    </div>   
                        @php $counter++; @endphp
                    @endforeach 
                </div> 
                 
            
            
        </div> 
    </div>
</div>
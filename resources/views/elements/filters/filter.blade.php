<div class="col-md-3 right">
    @foreach(@$exportBtn as $k => $v)
        @if($v['status'] == true)
            <a href="{{ route($v['url']) }}" class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange">
                {{ $v['label'] }}
            </a>    
        @endif
    @endforeach
</div> 
<h4 class="card-title">Search</h4>

<div class="row">
    <div class="col s12"> 
        <div class="col s12">
            <form id="yourFormId">
                <div class="row">
                    @php $counter = 1; @endphp
                    <script>
                        var options=[];
                    </script>
                    @foreach(@$filters as $k => $v)
                        @if($counter%5==0)
                            </div><div class="row">
                        @endif
                        @if(@$v['input_type'] == 'text')
                            @php $lbl=@$v['lbl']; $placeholder = "". @$v['placeholder']; $fld=@$v['fld'] @endphp
                            <div class="input-field col m3 s12">
                                <input type="text"  name="{{ $fld }}" class="form-control " . $fld   autocomplete="off" id="{{ $fld }}" placeholder="Enter {{ $placeholder }}">
                            </div>
                        @elseif(@$v['input_type'] == 'select') 
                            <div class="input-field col m3 s12">
                                @php $lbl=@$v['lbl']; $placeholder = "Select ". @$v['placeholder']; $fld=@$v['fld'] @endphp
                                @php 
                                    $$fld = $v['options'];
                                @endphp

                                <script>
                                    k = "{{ $v['fld'] }}"; 
                                    options[k] = "{{ @$$fld  }}";
                                </script>

                                {!! Form::select($fld,@$v['options'],null, ['class' => 'select2 browser-default form-control center-align ' . $fld  ,'id'=>$fld, 'placeholder' => $placeholder]) !!}
                            </div>   
                        @endif 
                        @php $counter++; @endphp
                    @endforeach 
                </div> 
                
                <div class="col-md-3 right">
                    <button type="button" name="filter" id="submitFilterBtn" class="btn btn-primary">Filter</button>
                    <button class="btn cyan waves-effect waves-light right" type="reset">Reset
                      </button>
                </div>
            </form>
        </div> 
    </div>
</div>


@php 
$query_array = app('request')->query();	
$showPopup = "";
if(@$query_array){
    $showPopup = "active";
}
@endphp

<ul class="row collapsible tempslide-out-search-div" id="slide-out" data-collapsible="menu-accordion">
    <li class="{{ @$showPopup }} bold"><a class="collapsible-header waves-effect waves-cyan " href="JavaScript:void(0)">
        <!-- @php 
        $yesno = ['20' => '20','40' => '40',];
        $currentURL = URL::current();
        @endphp -->
        <div class="col s12 m12 l8">
            <spanclass="card-title" style="font-size: 16px;">Search(खोज)</span>
        </div>
        <div class="col s12 m12 l4">
            <span style="font-size: 14px;">Click here to search(खोजने के लिए यहां क्लिक करें)</span>
        </div>
    </a>
      <div class="collapsible-body">
            <div class="col-md-3 right">
                @foreach(@$exportBtn as $k => $v)
                    @if($v['status'] == true)
                        <a href="{{ route($v['url']) }}" class="btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange">
                            {{ $v['label'] }}
                        </a>    
                    @endif
                @endforeach
            </div> 
            <div class="row ">
                <div class="col s12 card card-default "> 
                    <div class="col s12 ">
                        <form id="yourFormId">

                        <!--    <div class="row">
                                @php $fld='page'; @endphp
                        <div class="input-field">
                        {!! Form::select($fld,@$yesno,null, ['class' => 'select2 browser-default form-control center-align', 'id'=>'course11']) !!}
                        </div>
                        </div> -->
                                @php $counter = 0; @endphp
                                <script>
                                    var options=[];
                                </script>
                                @foreach(@$filters as $k => $v)
                                    @if($counter%4==0)
                                        </div><div class="row" >
                                    @endif
                                    @if(@$v['input_type'] == 'datetime')
                                        @php $lbl=@$v['lbl']; $placeholder = "". @$v['placeholder']; $fld=@$v['fld'] @endphp
                                        <div class="input-field col m3 s12">
                                            <input type="text"  name="{{ $fld }}" value="@php echo request()->get($fld); @endphp" class="form-control  customdatetime" .  . $fld   autocomplete="off" id="{{ $fld }}" placeholder="Enter {{ $placeholder }}">
                                        </div>
                                    @elseif(@$v['input_type'] == 'datetime-local') 
                                    @php $lbl=@$v['lbl']; $placeholder = "". @$v['placeholder']; $fld=@$v['fld'] @endphp
                                        <div class="input-field col m3 s12">
                                            <input type="{{@$v['input_type']}}"  name="{{ $fld }}" class='datetimepicker1'   value="@php echo request()->get($fld); @endphp" class="form-control " .  . $fld   autocomplete="off" id="{{ $fld }}" placeholder="Enter {{ $placeholder }}">
                                            <span style='color:;'>{{ $lbl }} click on <i class="material-icons dp48">perm_contact_calendar</i></span>
                                        </div>
                                    @elseif(@$v['input_type'] == 'text')
                                        @php $lbl=@$v['lbl']; $placeholder = "". @$v['placeholder']; $fld=@$v['fld'] @endphp
                                        <div class="input-field col m3 s12">
                                            <input type="text" name="{{ $fld }}" value="@php echo request()->get($fld); @endphp" class="form-control " . $fld   autocomplete="off" id="{{ $fld }}" placeholder="Enter {{ $placeholder }}">
                                        </div>
                                        @error($fld)
                                            <span class="invalid-feedback" role="alert" style="color:red;">
                                            <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
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
                                            {!! Form::select($fld,@$v['options'],request()->get($fld), ['class' => 'select2 browser-default form-control center-align ' . $fld  ,'id'=>$fld, 'placeholder' => $placeholder]) !!}
                                        @include('elements.field_error')
                                        </div>   
                                    @endif 
                                    @php $counter++; @endphp
                                @endforeach 
                                @if(@$sortingField)
                                    @include('elements.sorting')
                                @endif
                            </div> 
                            </br>
                            <div class="col-md-3 right">
                                <button type="submit" name="filter" id="submitFilterBtn" class="btn btn-primary">Filter</button>
                                @php
                                    $route = Route::current()->getActionName();
                                    if(Route::current()->action['as'] == "examiner_mapping_practical_list"){
                                        $paramName1 = Route::current()->parameters()['practical_user_id'];
                                        @endphp 
                                        <a href="{{ action($route, $paramName1) }}" class="btn btn-primary">Reset</a>
                                        @php 
                                    }else{
                                        @endphp
                                            <a href="{{ action($route) }}" class="btn btn-primary">Reset</a> 
                                        @php 
                                    }
                                @endphp
                                
                            </div></br></br></br>
                            </form>
						 </div> 
                    </div> 
                </div>
            </div>
        </div>
    </li>
</ul>
 

<script>
    $(function() {
        $( ".customdatetime").datepicker({
            dateFormat : "dd-mm-yy",
            multidate: 2,
            maxdate: '+14Y',
        });
    }); 
    $('.slide-out-search-div').parent().parent().removeClass('card');
   //$(function () {
       //$(function () {
           //$('.datetimepicker1').datetimepicker();
       //});
   // });

//   jQuery(function() {
//     jQuery('#course11').change(function() {
//         this.form.submit();
//     });
// });
    
</script>

  

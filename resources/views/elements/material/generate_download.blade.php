<div id="tap-target" class="card card-tabs">
    <h4 class="header">&nbsp;<span style="color:blue;"> Generate Exam Center {{ $title }} </span></h4>
    <div class="card-content">
        {!! Form::open(array('route' => $path,'method'=>'POST')) !!}
        <div class="row">
        <div class="col m3 s12">
            @php $lbl='पाठ्यक्रम(Course)'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
            <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
            <div class="input-field">
            {!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align course2 admtypes','placeholder' => $placeholder,'id'=>'course2']) !!}
            @include('elements.field_error')
            </div>
            </div>

            <div class="col m3 s12">
            @php $lbl='स्ट्रीम(Stream)'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
            <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
            <div class="input-field">
            {!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'stream2']) !!}
            @include('elements.field_error')
        </div>
        </div>
            <div class="col m3 s12">
            @php $lbl='ज़िला(District)'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
            <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
            <div class="input-field">
            {!! Form::select($fld,@$district_list,null, ['class' => 'select2 browser-default form-control center-align district2','placeholder' => $placeholder,'id'=>'district2' ]) !!}

            @include('elements.field_error')
            </div>
            </div>
            <div class="col m3 s12">
            @php $lbl='परीक्षा केंद्र(Exam Center)'; $placeholder = "Select ". $lbl; $fld='ecenter'; @endphp
            <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
            <div class="input-field">
            {!! Form::select($fld,@$empty,null, ['class' => 'select2 browser-default form-control center-align examcenter2','placeholder' => $placeholder ,'id'=>'subjectstype2']) !!}

            @include('elements.field_error')
            </div>
            </div>
			
    </div>
    
    </div>
    <br>
    <div class="row">
        <div class="col m10 s12 mb-2">
            <button class="btn warning waves-effect waves-light right" type="submit" name="action"> Generate
            </button>
        </div>
        <div class="col m2 s12 mb-2">
            <a href="{{route(Route::currentRouteName())}}" class="btn cyan waves-effect waves-light right">Reset </a>
        </div>
    </div> 
    {{ Form::close() }}
</div> 

@extends('layouts.default')
@section('content')
 <!-- BEGIN: SideNav-->
  <!-- BEGIN: Page Main-->
    <div id="main">
      <div class="row">
    <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Update District</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Form</a>
                  </li>
                  <li class="breadcrumb-item active">Update District
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
		<div class="col s12">
        <div class="container">
        <div class="seaction">
        	<div class="card">
			<div class="card-content">
	 <h6><a href="{{route('districts.index')}}" class="btn btn-xs btn-info right">Back</a></h6>
      <h6>Update District<h6>
	 </div>
  </div>
<!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
          <h4 class="card-title">Update District</h4>
           {{ Form::open(['route' => ['districts.update', $districts->id]]) }}
           {!! Form::token() !!}
           {{ method_field('PUT') }}
            <div class="row">
              <div class="input-field col m6 s12">
                @php $lbl="राज्य(State)"; $fld='state_id'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				{!! Form::select('state_id',@$state,@$districts->state_id,['class' => 'form-control', 'placeholder' => 'Please Select State']) !!}
               @error('state_id')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
              <div class="input-field col m6 s12">
                @php $lbl="डिवीजन (Division)"; $fld='division_id'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>{!!Form::text('division_id',@$districts->division_id,['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!}
                @error('division_id')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
            </div>
			      <div class="row">
              <div class="input-field col m6 s12">
             @php $lbl="कोड(Code)"; $fld='code'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
              {!! Form::text('code',@$districts->code,['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!} 
              @error('code')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
              <div class="input-field col m6 s12">
             @php $lbl="नाम(Name)"; $fld='name'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
              {!! Form::text('name',@$districts->name,['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!}
              @error('name')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
            </div>
			 <div class="row">
              <div class="input-field col m6 s12">
               @php $lbl="हिंदी में नाम(Name in hindi)"; $fld='name_mangal'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
              {!! Form::text('name_mangal',@$districts->name_mangal,['type'=>'text','class'=>'form-control','autocomplete'=>'off']); !!}
              @error('name_mangal')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
            </div>
			 <div class="row">
			   <div class="col m10 s12 mb-3">
                      <button class="btn cyan waves-effect waves-light right" type="reset">
                        <i class="material-icons right">clear</i>Reset
                      </button>
                    </div>
                <div class="col m2 s12 mb-3">
				  <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>
		{{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
@endsection




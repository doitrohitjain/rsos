@extends('layouts.default')
@section('content')
    <div id="main">
      <div class="row">
	  <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Form Layouts</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Form</a>
                  </li>
                  <li class="breadcrumb-item active">Form Layouts
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
	 <h6><a href="{{route('subjects.index')}}" class="btn btn-xs btn-info right">Back</a></h6>
      <h6>Form Layouts<h6>
	 </div>
  </div>
<!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
          <h4 class="card-title">Form Subjects</h4>
		  {!! Form::model($subject, ['method' => 'PATCH','route' => ['subjects.update', $subject->id],'id' => $model]) !!}
		  	<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
            <div class="row">
	        <div class="col m3 s12">
				@php $lbl="Subject Real Name"; $fld='real_name'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">{!!Form::text($fld,@$subject->real_name,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
				@include('elements.field_error')	
				</div>
			</div>
			<div class="col m3 s12">
				@php $lbl="Subject Name"; $fld='name'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">{!!Form::text($fld,@$subject->name,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
				@include('elements.field_error')	
				</div>
			</div>
			<div class="col m3 s12">
			 @php $lbl="Subject Code"; $fld='subject_code'; @endphp 
			<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
			<div class="input-field">{!!Form::text($fld,@$subject->subject_code,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
			 @include('elements.field_error')	
				</div>
			</div>
			<div class="col m3 s12">
			@php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
			<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
			<div class="input-field">
			{!! Form::select($fld,@$course,$subject->course, ['class' => 'select2 browser-default form-control center-align ','placeholder' => $placeholder,]) !!}
			@include('elements.field_error')
			</div>
		</div>
		 </div>
		   <div class="row">
		    <div class="col m3 s12">
				@php $lbl="Theory Max Marks"; $fld='theory_max_marks'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">{!!Form::text($fld,@$subject->theory_max_marks,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
				@include('elements.field_error')	
				</div>
			</div>
			 <div class="col m3 s12">
				@php $lbl="Theory Min Marks"; $fld='theory_min_marks'; @endphp 
				<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">{!!Form::text($fld,@$subject->theory_min_marks,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
				@include('elements.field_error')	
				</div>
			</div>
		<div class="col m3 s12">
				@php $lbl="Sessional max Marks"; $fld='sessional_max_marks'; @endphp 
				<span class="small_lable ">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">{!!Form::text($fld,@$subject->sessional_max_marks,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
				@include('elements.field_error')	
				</div>
			</div>
			<div class="col m3 s12">
			 @php $lbl="Sessional Min Marks"; $fld='sessional_min_marks'; @endphp 
			<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
			<div class="input-field">{!!Form::text($fld,@$subject->sessional_min_marks,['type'=>'text','class'=>'form-control num','autocomplete'=>'off','placeholder' => $lbl]); !!}
			 @include('elements.field_error')	
				</div>
			</div>
		 </div>
		 <div class="col m3 s12">
			@php $lbl='Subject Type'; $placeholder = "Select ". $lbl; $fld='subject_type'; @endphp
			<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
			<div class="input-field">
			{!! Form::select($fld,@$subjecttype,@$subject->subject_type, ['class' => 'select2 browser-default form-control center-align ','placeholder' => $placeholder,]) !!}
			@include('elements.field_error')
			</div>
		</div>
		 <div class="col m3 s12">
			@php $lbl='Practical Type'; $placeholder = "Select ". $lbl; $fld='practical_type'; @endphp
			<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
			<div class="input-field">
			{!! Form::select($fld,@$yes_no,@$subject->practical_type, ['class' => 'select2 browser-default form-control center-align changetype','placeholder' => $placeholder,]) !!}
			@include('elements.field_error')
			</div>
		</div>
		 <div class="row">
		 @if($subject->practical_type == 1)
			<div class="col m3 s12 ">
				@php $lbl="Practical Max Marks"; $fld='practical_max_marks'; @endphp 
				<span class="small_lable ">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">{!!Form::text($fld,@$subject->practical_max_marks,['type'=>'text','class'=>'form-control num ','autocomplete'=>'off','placeholder' => $lbl]); !!}
				@include('elements.field_error')	
				</div>
			</div>
			<div class="col m3 s12 " >
			 @php $lbl="Practical Min Marks"; $fld='practical_min_marks'; @endphp 
			<span class="small_lable ">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
			<div class="input-field">{!!Form::text($fld,@$subject->practical_min_marks,['type'=>'text','class'=>'form-control num ','autocomplete'=>'off','placeholder' => $lbl]); !!}
			 @include('elements.field_error')	
				</div>
			</div>
		 @endif
			<div class="col m3 s12 show">
				@php $lbl="Practical Max Marks"; $fld='practical_max_marks'; @endphp 
				<span class="small_lable show">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
				<div class="input-field">{!!Form::text($fld,@$subject->practical_max_marks,['type'=>'text','class'=>'form-control num show','autocomplete'=>'off','placeholder' => $lbl]); !!}
				@include('elements.field_error')	
				</div>
			</div>
			<div class="col m3 s12 show" >
			 @php $lbl="Practical Min Marks"; $fld='practical_min_marks'; @endphp 
			<span class="small_lable show">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
			<div class="input-field">{!!Form::text($fld,@$subject->practical_min_marks,['type'=>'text','class'=>'form-control num show','autocomplete'=>'off','placeholder' => $lbl]); !!}
			 @include('elements.field_error')	
				</div>
			</div>
		 </div>
		    @if($subject->course==12)
            <div class="row">
				<div class="col m3 s12">
					@php $lbl="Is Science Faculty"; $placeholder = "Select ". $lbl; $fld='is_science_faculty'; @endphp 
					<span class="small_lable">@php echo $lbl  @endphp </span>
					<div class="input-field">
						{!! Form::select($fld,@$yes_no,@$subject->$fld,['class' => 'select2 browser-default form-control center-align ','placeholder' => $placeholder,]) !!}
						@include('elements.field_error')	
					</div>
				</div>
				<div class="col m3 s12">
					@php $lbl="Is Commerce Faculty"; $placeholder = "Select ". $lbl; $fld='is_commerce_faculty'; @endphp 
					<span class="small_lable">@php echo $lbl ; @endphp  </span>
					<div class="input-field">{!! Form::select($fld,@$yes_no,@$subject->$fld, ['class' => 'select2 browser-default form-control center-align ','placeholder' => $placeholder,]) !!}
				    @include('elements.field_error')	
				</div>
			</div>
			<div class="col m3 s12">
			 @php $lbl="Is Arts Faculty";$placeholder = "Select ". $lbl; $fld='is_arts_faculty'; @endphp 
			<span class="small_lable">@php echo $lbl ; @endphp </span>
			<div class="input-field">
			{!! Form::select($fld,@$yes_no,@$subject->$fld, ['class' => 'select2 browser-default form-control center-align ','placeholder' => $placeholder,]) !!}
			@include('elements.field_error')
			 	
				</div>
			</div>
			
			<div class="col m3 s12">
			 @php $lbl="Is Agricultre Faculty";$placeholder = "Select ". $lbl; $fld='is_agricultre_faculty'; @endphp 
			<span class="small_lable">@php echo $lbl ; @endphp </span>
			<div class="input-field">
			{!! Form::select($fld,@$yes_no,@$subject->$fld, ['class' => 'select2 browser-default form-control center-align ','placeholder' => $placeholder,]) !!}
			@include('elements.field_error')
			 	
				</div>
			</div>
			
			<div class="col m3 s12 l12">
			@php $lbl='Is Common Faculty(If more than one faculty) (सामान्य संकाय है (एक से अधिक संकाय का))'; $placeholder = "Select ". $lbl; $fld='is_allow_faculty'; @endphp
			<span class="small_lable">@php echo $lbl ; @endphp </span>
			<div class="input-field">
			{!! Form::select($fld,@$yes_no,@$subject->$fld, ['class' => 'select2 browser-default form-control center-align ','placeholder' => $placeholder,]) !!}
			@include('elements.field_error')
			</div>
		</div>
		@endif
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
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/subjects_details.js') !!}"></script> 
@endsection
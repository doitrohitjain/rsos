@extends('layouts.default')
@section('content')
@php $role_id = Session::get('role_id');
$ai_code = Session::get('ai_code');@endphp
<div id="main">
    <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
        <div class="container">
            <div class="row">
				<div class="col s12 m6 l6">
					<h5 class="breadcrumbs-title mt-0 mb-0"><span>Books Requirement</span></h5>
				</div>
				<div class="col s12 m6 l6 right-align-md">
					<ol class="breadcrumbs mb-0">
						<li class="breadcrumb-item"><a href="index-2.html">Home</a>
						</li>
						<li class="breadcrumb-item active">Books Requirement 
						</li>
					</ol>
				</div>
            </div>
        </div>
    </div>
	<div class="container">
        <div class="seaction">
			<div class="card">
				<div class="card-content">
					<h6>
						<a href="{{route('booklisting')}}" class="btn btn-xs btn-info right gradient-45deg-deep-orange-orange">
							Back
						</a>
					</h6>
					<h6>Books Requirement</h6>
				</div>
			</div>
        </div>
    </div>
	<div class="row">
		<div class="col s12">
			<div id="html-validations" class="card card-tabs">
				<div class="card-content">
					<h6>Books Requirement  <h6></br>
					<div id="html-view-validations">
						{!! Form::open(array('route' => 'bookadd','method'=>'POST','id' => $model)) !!}
							<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
							<input type="hidden" name='medium' value='' id='medium'>
							<div class="row">
								@if($role_id == 71 || $role_id == 86)
									<div class="col m3 s12">
										@php $lbl='AI code(विद्यालय का नाम कोड)'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!! Form::select($fld,$aiCenters,null,['class' => 'select2 browser-default form-control aicode allsubjectcoursemedium checkcombinationexists','placeholder' => $placeholder,]) !!}
											@include('elements.field_error')
										</div>
									</div>
								@elseif($role_id == 59)
									<div class="col m3 s12">
										@php $lbl='AI code(विद्यालय का नाम कोड)'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
										<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
										<div class="input-field">
											{!!Form::text($fld,$ai_code,['type'=>'text','class'=>'form-control aicode','autocomplete'=>'off','placeholder' => $lbl,'readonly'=>'readonly']); !!}
											@include('elements.field_error')
										</div>
									</div>
								@endif
								<div class="col m3 s12">
									@php $lbl='Course(पाठ्यक्रम)'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
										{!! Form::select($fld,$course,null,['class' => 'select2 browser-default form-control course_id  allsubjectcoursemedium courses checkcombinationexists','placeholder' => $placeholder,]) !!}
										@include('elements.field_error')
									</div>
								</div>
								<div class="col m3 s12">
									@php $lbl='Subject(विषय)'; $placeholder = "Select ". $lbl; $fld='subject_id'; @endphp
									<span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
									<div class="input-field">
										{!! Form::select($fld,@$subject,null,['class' => 'select2 browser-default form-control subject_id allsubjectcoursemedium subjects checkcombinationexists','placeholder' => $placeholder,]) !!}
										@include('elements.field_error')
									</div>
								</div>
								<div class="col m3 s12">
									@php $lbl='Volume(वॉल्यूम)'; $placeholder = "Select ". $lbl; $fld='subject_volume_id'; @endphp
									<span class="small_lable">@php echo $lbl.Config::get('global.starMark') ; @endphp </span>
									<div class="input-field">
										{!! Form::select($fld,$book_publication_volumes,null,['class' => 'select2 browser-default form-control volume  checkcombinationexists','id'=>'volumes','placeholder' => $placeholder,]) !!}
										@include('elements.field_error')
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col m4 s12 hindienrollmentcount">
									@php $lbl="Hindi Enrollment Student Count(हिन्दी नामांकन छात्र संख्या)"; $fld='hindi_auto_student_count'; @endphp 
									<span class="small_lable" id ="enrollmentstudentcount">@php echo $lbl  @endphp </span>@php echo Config::get('global.starMark')@endphp
									<div class="input-field">
										{!!Form::text($fld,null,['type'=>'text','class'=>'form-control rxpectedenrollmentcount num hindiautostudentcount',"onkeyup"=>"hindirequiredbookcountcalculate()",'autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>8,'minLength'=>1,'readonly'=>'readonly']); !!}
										@include('elements.field_error')	
									</div>
								</div>
								<div class="col m4 s12 hindilastyearbook">
									@php $lbl="Hindi Last Year Book Stock Count(हिंदी पिछले वर्ष की पुस्तक स्टॉक संख्या)"; $fld='hindi_last_year_book_stock_count'; @endphp 
									<span class="small_lable" id ="lastyearbookcount">@php echo $lbl; @endphp </span>@php echo Config::get('global.starMark')@endphp
									<div class="input-field">
										{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','id'=>'hindilastyearstockcount','autocomplete'=>'off','placeholder' => $lbl,"onkeyup"=>"hindirequiredbookcountcalculate()",'maxlength'=>8,'minLength'=>1]); !!}
										@include('elements.field_error')	
									</div>
								</div>
								<div class="col m4 s12 hindirequiredbook">
									@php $lbl="Hindi Required Book Count (हिंदी वर्तमान सत्र में शुद्ध मांग)"; $fld='hindi_required_book_count'; @endphp 
									<span class="small_lable" id="requiredbookcount">@php echo $lbl ; @endphp </span>@php echo Config::get('global.starMark')@endphp
									<div class="input-field">
										{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num hindirequiredbookcount','autocomplete'=>'off','placeholder' => $lbl,"onkeyup"=>"hindirequiredbookcountcalculate()",'maxlength'=>8,'minLength'=>1,'readonly'=>'readonly']); !!}
										@include('elements.field_error')	
									</div>
								</div>
							</div>
							<div class="row " id="englishBox">
								<div class="col m4 s12 englishenrollmentcount" >
									@php $lbl="English Enrollment Student Count (अंग्रेजी नामांकन छात्र संख्या)"; $fld='english_auto_student_count'; @endphp 
									<span class="small_lable ">@php echo $lbl; @endphp </span>@php echo Config::get('global.starMark')@endphp
									<div class="input-field">
										{!!Form::text($fld,null,['type'=>'text','class'=>'form-control rxpectedenrollmentcount num englishautostudentcount',"onkeyup"=>"englishrequiredbookcountcalculate()",'autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>8,'minLength'=>1,'readonly'=>'readonly']); !!}
										@include('elements.field_error')	
									</div>
								</div>
								<div class="col m4 s12 englishlastyearbookstock">
									@php $lbl="English Last Year Book Stock Count(अंग्रेजी पिछले वर्ष की पुस्तक स्टॉक संख्या)"; $fld='english_last_year_book_stock_count'; @endphp 
									<span class="small_lable">@php echo $lbl; @endphp </span>@php echo Config::get('global.starMark')@endphp
									<div class="input-field">
										{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num','id'=>'englishlastyearstockcount','autocomplete'=>'off','onkeyup'=>"englishrequiredbookcountcalculate()",'placeholder' => $lbl,'maxlength'=>8,'minLength'=>1]); !!}
										@include('elements.field_error')	
									</div>
								</div>
								<div class="col m4 s12 englishrequiredbook">
									@php $lbl="English Required Book Count(अंग्रेजी वर्तमान सत्र में शुद्ध मांग)"; $fld='english_required_book_count'; @endphp 
									<span class="small_lable">@php echo $lbl  @endphp </span>@php echo Config::get('global.starMark')@endphp
									<div class="input-field">
										{!!Form::text($fld,null,['type'=>'text','class'=>'form-control num  enlgishrequiredbookcount','onkeyup'=>"englishrequiredbookcountcalculate()",'autocomplete'=>'off','placeholder' => $lbl,'maxlength'=>8,'minLength'=>1,'readonly'=>'readonly']); !!}
										@include('elements.field_error')	
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col m10 s12 mb-3">
									<a href="{{ route('bookadd') }}" class="btn cyan waves-effect waves-light right"> <i class="material-icons right">clear</i>Reset</a>
								</div>
								<div class="col m2 s12 mb-3">
									<button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
										<i class="material-icons right">send</i>
									</button>
								</div>
							</div>
						{!! Form::close() !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('customjs')
<script src="{!! asset('public/app-assets/js/bladejs/books_requrement_details.js') !!}"></script> 
@endsection

@php 
	use App\Component\CustomComponent;
	$custom_component_obj = new CustomComponent;
	$isStudent = $custom_component_obj->_getIsStudentLogin();
	$role_id = @Session::get('role_id');
@endphp

@if($role_id == Config::get("global.aicenter_id"))
	<div class="card">
		<div id="card-stats2" class="row">
		
			<div class="card-content">
				<h6><a href="{{route('supplementary_student_applications',['exam_month' => 1])}}" class="btn btn-xs btn-info left mb-2 mr-1" >Back</a></h6>
 
				@if($role_id == Config::get("global.aicenter_id"))
					@if(@$suppData->is_aicenter_verify == 1 || @$suppData->is_aicenter_verify == 4 )
						<h6><a data-url="{{ route('supp_doc_mark_verfication',[Crypt::encrypt($suppData->id),2]) }}" class="btn btn-xs btn-success right mb-2 mr-1 confirms" data-type="2" style="background: linear-gradient(45deg,#12e471,#219243)!important;">Mark Approved</a></h6>
						<h6><a data-url="{{ route('supp_doc_mark_rejected_verfication',[Crypt::encrypt($suppData->id),3]) }}" class="btn btn-xs btn-red right mb-2 mr-1 confirms" data-type="3" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">Mark Rejected</a></h6>
					@endif
				@endif  
			</div>
		</div>
	</div>
@elseif($role_id == Config::get("global.examination_department"))
	<div class="card">
		<div id="card-stats2" class="row">
			<div class="card-content">
				<h6><a href="{{route('supplementary_student_applications',['exam_month' => 1])}}" class="btn btn-xs btn-info left mb-2 mr-1" >Back</a></h6> 
				@if($role_id == Config::get("global.examination_department"))
					@if(@$suppData->is_department_verify == 1 || @$suppData->is_department_verify == 4 )
						<h6><a data-url="{{ route('supp_doc_mark_verfication',[Crypt::encrypt($suppData->id),2]) }}" class="btn btn-xs btn-success right mb-2 mr-1 confirms" data-type="2" style="background: linear-gradient(45deg,#12e471,#219243)!important;">Mark Approved</a></h6>
						<h6><a data-url="{{ route('supp_doc_mark_rejected_verfication',[Crypt::encrypt($suppData->id),3]) }}" class="btn btn-xs btn-red right mb-2 mr-1 confirms" data-type="3" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">Mark Rejected</a></h6>
					@endif
				@endif

			</div>
		</div>
	</div>
@endif 

@if($role_id != Config::get("global.aicenter_id"))
<div class="card-content"> 
	<div id="card-stats" class="row">
        @php
			
			$menus =  array(
				'supp_find_enrollment' => "Search Enrollment",
				'supp_subjects_details' => "Personal Details", //here also save subjects and docuemnt.
				'supp_fees_details' => "Fees",
				'supp_preview_details' => "Preview"
			); 
			if(@$isStudent && $isStudent == true){
				unset($menus['supp_find_enrollment']);
			} 
		@endphp
		 
		@foreach($menus as $link => $label)
			@if(@$isStudent && $isStudent == true)
				<div class="col s12 m4 xl112 center divsection {{ request()->routeIs($link) ? 'activedivsec' : '' }} ">
			@else
				<div class="col s12 m3 xl112 center divsection {{ request()->routeIs($link) ? 'activedivsec' : '' }} ">
			@endif
				<div class="input-field sessional-link-tabs" >
					@if($link == "find_enrollment")
						<a data-target=""  href="{{ route($link) }}">
					@else
						@if(@$student_id)
							<a data-target=""  href="{{ route($link,Crypt::encrypt($student_id)) }}">
						@else
							<a data-target=""  href="javascript:void(none);">	
						@endif
						
					@endif
						<span class="dropdown-title3" data-i18n="Persoanl">
							@php echo @$label @endphp
						</span>  
					</a>
				</div>  
			</div> 
		@endforeach 
 
        </div>
    </div>
	
	<style>
		.activedivsec {
			background-color: #14b4fc !important; 
		} 
		.activedivsec > .input-field > a { 
			font-size: 30px;
			color: white !important;
		} 
		.divsection > .input-field > a { 
			color: #464a5e !important;
			font-size: 18px;
			font-weight: 700;
		} 

		#card-stats {
			padding-top: 0px !important;
		} 
	</style>
@else
@endif
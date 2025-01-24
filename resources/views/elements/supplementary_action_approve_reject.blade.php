@php 
	use App\Component\CustomComponent;
	$custom_component_obj = new CustomComponent;
	$isStudent = $custom_component_obj->_getIsStudentLogin();
	$showStatus = $custom_component_obj->isCurrentRoleVerificationAllowOrNot();
	$role_id = @Session::get('role_id'); 
@endphp

@if($showStatus && $role_id == Config::get("global.aicenter_id"))
	@can('aicenter_supp_document_verification')
		<div class="cardold">
			<div id="card-stats2" class="row">
				<div class="card-contentold">
					<!--<h6><a href="{{route('supplementary_student_applications',['exam_month' => 1])}}" class="btn btn-xs btn-info left mb-2 mr-1" >Back</a></h6>-->
					
					@if($role_id == Config::get("global.aicenter_id"))
						@if((@$suppData->is_aicenter_verify == 1 || @$suppData->is_aicenter_verify == 4 ) && !empty(@@$masterrecord->locksubmitted_date) &&  !empty(@$masterrecord->locksubmitted_date) && @$masterrecord->fee_paid_amount >= 0)
							<h6><a data-url="{{ route('supp_doc_mark_verfication',[Crypt::encrypt($suppData->id),2]) }}" class="btn btn-xs btn-success right mb-2 mr-1 confirms" data-type="2" style="background: linear-gradient(45deg,#12e471,#219243)!important;">Mark Approved</a></h6>
							<h6><a data-url="{{ route('supp_doc_mark_rejected_verfication',[Crypt::encrypt($suppData->id),3]) }}" class="btn btn-xs btn-red right mb-2 mr-1 confirms" data-type="3" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">Mark Rejected</a></h6>
						@endif
					@endif  
				</div>
			</div>
		</div>
	@endcan
@elseif($showStatus && $role_id == Config::get("global.examination_department"))
	@can('department_supp_document_verification')
		<div class="cardold">
			<div id="card-stats2" class="row">
				<div class="card-contentold">
					<!--<h6><a href="{{route('supplementary_student_applications',['exam_month' => 1])}}" class="btn btn-xs btn-info left mb-2 mr-1" >Back</a></h6> -->
					@if($role_id == Config::get("global.examination_department"))  
					@if( (@$suppData->is_aicenter_verify == 2 && @$suppData->is_department_verify == 1 || @$suppData->is_department_verify == 4 ) &&  !empty(@$masterrecord->locksubmitted_date) && @$masterrecord->fee_paid_amount >= 0)  
							<h6><a data-url="{{ route('supp_doc_mark_verfication',[Crypt::encrypt($suppData->id),2]) }}" class="btn btn-xs btn-success right mb-2 mr-1 confirms" data-type="2" style="background: linear-gradient(45deg,#12e471,#219243)!important;">Mark Approved</a></h6>
							<h6><a data-url="{{ route('supp_doc_mark_rejected_verfication',[Crypt::encrypt($suppData->id),3]) }}" class="btn btn-xs btn-red right mb-2 mr-1 confirms" data-type="3" style="background: linear-gradient(45deg,#e42b12,#923821)!important;">Mark Rejected</a></h6>
						@endif
					@endif

				</div>
			</div>
		</div>
	@endcan
@else
	@if($role_id != Config::get("global.student"))
		<div class="cardold">
			<div id="card-stats2" class="row">
				<div class="card-contentold">	
					<!--<h6><a href="{{route('supplementary_student_applications',['exam_month' => 1])}}" class="btn btn-xs btn-info left mb-2 mr-1" >Back</a></h6> -->
				</div>
			</div>
		</div>
	@endif 
@endif 

<script>
	$('.confirms').on('click', function (event) {
		event.preventDefault();
		const url = $(this).attr('data-url');
		const type = $(this).attr('data-type');
		msg = "Are you sure you want to mark Rejected the student?";    if(type == 2){
			msg = "Are you sure you want to mark Approved the student?";
		}
		swal({
			title: 'Are you sure?',
			text: msg,
			icon: 'info',
			buttons: ["Cancel", "Yes!"],
		}).then(function(value) {
			if (value) {
				window.location.href = url;
			}
		});
	});
</script>


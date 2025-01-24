<div class="">
	<div class="">   
		<ul class="collapsible "> <!-- popout -->
			@php $kCounter = 0 ; @endphp
			@foreach(@$arrVerifications as $fld => $v)
				@if(@$role_id == config("global.super_admin_id"))
					@php 				
						$verifierStatus = "verifier_" . $fld . "_is_verify";
						$verifierStatusRemarks = "verifier_" . $fld . "_is_verify_remarks";
					@endphp
				@endif 
				<li>
					<div class="collapsible-header">
						<i class="material-icons">details</i> <span style="font-size:18px;">{{ @$v['lbl'] }}</span> 
						<span class="right" style="float:right;font-size:14px;margin-left:1%;">
							<span class="chip lighten-5 green green-text hide" id="{{ $fld }}_approved" >Approved</span>
							<span class="chip lighten-5 red red-text hide" id="{{ $fld }}_rejected">Rejected</span>
							<span class="chip lighten-5 blue blue-text hidenot" id="{{ $fld }}_pending">Pending</span>
						</span> 
						<span class="right" style="float:right;font-size:14px;margin-left:2%;">
							@php  $color = "blue"; @endphp
							@if(@$verifierStatus && @$documentVerifierVerifications->$verifierStatus)
								@if(@$verifierStatus && $documentVerifierVerifications->$verifierStatus == 1)
									@php  $color = "green"; @endphp
								@elseif(@$verifierStatus && $documentVerifierVerifications->$verifierStatus == 2)
									@php  $color = "red"; @endphp 
								@endif
							@endif  
							<span class="lighten-5 {{ $color }} {{ $color }}-text" id="">
								@if(@$verifierStatus && @$documentVerifierVerifications->$verifierStatus)
									<sup>{{ @$fresh_student_doc_update_status[@$documentVerifierVerifications->$verifierStatus] }} By Verifier</sup>
								@endif 
							</span>
						</span> 
					</div>
					<div class="collapsible-body"> 
						<span>
							@if(@$verifierStatus && @$documentVerifierVerifications->$verifierStatus == 2)
							@php
								$modelContent = '<center><h4> ' . @$documentVerifierVerifications->$verifierStatusRemarks . ' </h4></center>';
							@endphp
								<span class="waves-effect waves-light  modal-trigger modalCls" style="color:blue;float:right;" data-content="{{ $modelContent }}">
									<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/> Verifier Rejection Reason
								</span>
							@endif
							@php $fileName = 'elements.verification.' . @$v['file']; @endphp
							@include(@$fileName,['fld' => @$fld])
						</span>
					</div>
				</li>
				@php $kCounter++; @endphp
			@endforeach
			<li>				
				<div class="collapsible-header">
					<i class="material-icons">details</i> <span style="font-size:18px;">Documents Details(दस्तावेज़ विवरण)</span> 
					<span class="right" style="float:right;font-size:14px;margin-left:1%;">
						@php  $color = "red"; $docStatus = "Not Approved"; @endphp	
						@if(@$studentdata->is_doc_rejected)
							@if($studentdata->is_doc_rejected != 1)
								@php  $color = "green"; $docStatus = "Approved"; @endphp
							@endif
						@endif
						<span class="lighten-5 {{ $color }} {{ $color }}-text" id="">
							 <sup>{{ @$docStatus }} By Verifier</sup>
						</span> 
					</span>
					
				</div>
				<div class="collapsible-body">
					<span> 
						<div class="" style="margin-top:-4%;"> 
							<h5>Documents Verification</h5>
							<div class="col m12 s12 mb-1">
								<span class="" style="float:left !important;color:blue;font-size:18px;">
								Note: Please verify each document separately.If any document mark is rejected then please enter the reason of rejection. 
								(नोट: कृपया प्रत्येक दस्तावेज़ को अलग से सत्यापित करें। यदि कोई दस्तावेज़ चिह्न अस्वीकार कर दिया गया है तो कृपया अस्वीकृति का कारण दर्ज करें।)
								</span>
							</div>
							@include('elements.verify_document_input') 
						</div>  
					</span>
				</div>
			</li>
		</ul>  
	</div>   
</div>   
 
<nav class="white" id="horizontal-nav">
	<div class="nav-wrapper">
		<ul class="left hide-on-med-and-down2" id="ul-horizontal-nav" data-menu="menu-navigation">
			
			@php
				$menus =  array(
					'persoanl_details' => "Personal",
					'address_details' => "Address",
					'bank_details' => "Bank",
					'document_details' => "Documents",
					'admission_subject_details' => "Subjects",
					// 'toc_subject_details' =>  "TOC Subjects",
				);
				
				if(isset($isItiStudent) && $isItiStudent!=true){
					$menus['toc_subject_details'] =  "TOC Subjects";
				}
				 
				$menus['exam_subject_details'] = "Exam Subjects";
				$menus['fee_details'] = "Fees";
				
				if(@$isLockAndSubmit == 1){
					$menus['view_details'] =  "View";
				}else{
					$menus['preview_details'] =  "Preview";
				}
				
				
			@endphp

			@foreach($menus as $link => $label)
				<li>
					<a data-target="" class="{{ request()->routeIs($link) ? 'active' : '' }}" href="{{ route($link,Crypt::encrypt($student_id)) }}">
						<span class="dropdown-title3" data-i18n="Persoanl">
							@php echo @$label @endphp
						</span>  
					</a>
				</li>  
			@endforeach 
		</ul>
	</div>
</nav>
 
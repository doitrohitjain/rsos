@php 
	use App\Component\CustomComponent;
	$custom_component_obj = new CustomComponent;
	$isStudent = $custom_component_obj->_getIsStudentLogin();
	$role_id = @Session::get('role_id'); 
	$minwidth = "50%";
@endphp

<nav class="white" id="horizontal-nav"> 
	<div class="nav-wrapper">
		<ul class="left hide-on-med-and-down2" id="ul-horizontal-nav" data-menu="menu-navigation">
			
			@php
				$menus =  array(
					'reval_find_enrollment' => "Search Enrollment",
					'reval_subjects_details' => "Reval Subject Details",
					'reval_preview_details' => "Reval Preview"
				); 
				unset($menus['reval_find_enrollment']);
				$minwidth = "50%";
				if(@$isStudent && $isStudent == true){
					
				}else{
					
				}   
				 
				$menus['reval_subjects_details'] = "Reval Subject Details";
				
				if(@$isLockAndSubmit == 1){
					$menus['reval_preview_details'] =  "Reval View";
				}else{
					$menus['reval_preview_details'] =  "Reval Preview";
				}
				
			 
			@endphp

			@foreach($menus as $link => $label)
				@if($link == "reval_subjects_details")
					<li style="min-width:{{ @$minwidth}}">
					<a data-target="" class="{{ request()->routeIs($link) ? 'active' : '' }}" href="{{ route($link,Crypt::encrypt($student_id)) }}">  
				@elseif($link == "reval_preview_details")
					<li style="min-width:{{ @$minwidth}}">
					@if(@$reval_id)
						
						<a data-target="" class="{{ request()->routeIs($link) ? 'active' : '' }}" href="{{ route($link,Crypt::encrypt(@$reval_id)) }}">  
					@else
						<a data-target="" class="{{ request()->routeIs($link) ? 'active' : '' }}" href="javacript:void(none);">  
					
						@endif
				@endif
				
						<span class="dropdown-title" data-i18n="Subjects">
							@php echo @$label @endphp
						</span>  
					</a>
				</li>  
			@endforeach 
		</ul>
	</div>
</nav>
 
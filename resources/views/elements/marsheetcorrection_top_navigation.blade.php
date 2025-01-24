@php 
	use App\Component\CustomComponent;
	$custom_component_obj = new CustomComponent;
	$isStudent = $custom_component_obj->_getIsStudentLogin();
	$role_id = @Session::get('role_id'); 
	$minwidth = "50%";
@endphp

<nav class="white" id="horizontal-nav" > 
	<div class="nav-wrapper">
		<ul class="left hide-on-med-and-down2" id="ul-horizontal-nav" data-menu="menu-navigation">
			
			@php
				$menus =  array(
					'reval_find_enrollment' => "Search Enrollment",
					'marksheetCorreaction' => "Reval Subject Details",
					'corr_marksheet_previews' => "Reval Preview"
				); 
				unset($menus['reval_find_enrollment']);
				$minwidth = "50%";
				if(@$isStudent && $isStudent == true){
					
				}else{
					
				}   
				 
				$menus['marksheetCorreaction'] = "Student Details";
				
				if(@$isLockAndSubmit == 1){
					$menus['corr_marksheet_previews'] =  "Student View";
				}else{
					$menus['corr_marksheet_previews'] =  "Student Preview";
				}
				
			 
			@endphp
			@foreach($menus as $link => $label)
				@if($link == "marksheetCorreaction")
					<li style="min-width:{{ @$minwidth}}">
					<a data-target="" class="{{ request()->routeIs($link) ? 'active' : '' }}" href="{{ route($link,Crypt::encrypt(@$student_id)) }}">  
				@elseif($link == "corr_marksheet_previews")
					<li style="min-width:{{ @$minwidth}}">
					@if(@$mmr_id)
						<a data-target="" class="{{ request()->routeIs($link) ? 'active' : '' }}" href="{{ route($link,Crypt::encrypt(@$mmr_id)) }}">  
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
 
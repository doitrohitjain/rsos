	<div class="card-content"> 
        <div id="card-stats" class="row">
        @php
			$menus =  array(
				'prepare_find_enrollment' => "Search By Enrollment",
				'prepare_marks_details' => "Exam Subjects Marks",
				'prepare_marks_preview_details' => "Preview"
			); 
		@endphp
		
		@foreach($menus as $link => $label)
			<div class="col s12 m3 xl4 center divsection {{ request()->routeIs($link) ? 'activedivsec' : '' }} ">
				<div class="input-field sessional-link-tabs" >
					@if($link == "prepare_find_enrollment")
						<a data-target=""  href="{{ route($link) }}">
					@else
						<a data-target=""  href="{{ route($link,Crypt::encrypt($student_id)) }}">
					@endif
						<span class="dropdown-title3 {{ request()->routeIs($link) ? 'activedivsectext' : '' }}" data-i18n="Persoanl" >
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
		background: linear-gradient(45deg,#d500f9,#ffa000)!important;
		
	} 
	.activedivsectext{
		color: white !important;
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
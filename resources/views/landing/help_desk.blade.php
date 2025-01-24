<style>
@media screen and (min-width: 480px) { 
	logocss{
		align:center
	}
}
</style>
@php 
	$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));  
@endphp

@extends('layouts.default')
@section('content')
 
<div id="main">
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="seaction">
					<div class="card card-content">
						<div id="view-available-colors">
							<div id="cards-extended">
								<div class="card">
									<?php  
										$lists = array();
										$APP_URL = Config::get("global.APP_URL");
										$studentFreshFormFillingVidPath = $APP_URL .'public/manual/student_applicaton_form_filling_video.mp4';
										
										 
										$modelContentVideo = '<p><video class="popuptext" style="max-width: -webkit-fill-available;" controls>
												<source src="'. @$studentFreshFormFillingVidPath . '" type="video/mp4">
											</video>
										</p>';
										 
										$role_id = Session::get('role_id');
										if(@$role_id == config("global.student")){
											$filePath = route('download',encrypt('/manual/student_applicaton_form_filling.pdf'));
											//$lists[] = array('lbl_text' =>' राजस्थान  स्टेट ओपन स्कूल छात्र आवेदन पत्र भरने का मैनुअल पीडीएफ प्रारूप में।(Student Application Form Filling Manual in PDF format.)',  'btnText' => 'PDF Manual', 'is_new' => true, 'route' => $filePath ); 
											$lists[] = array('lbl_text' =>' राजस्थान  स्टेट ओपन स्कूल छात्र आवेदन पत्र भरने का मैनुअल वीडियो प्रारूप में।(Student Application Form Filling Manual in Video format.)',  'btnText' => 'Video Manual', 'is_new' => true, 'target' => '_blank', 'is_quick_view_video' => 'modelContentVideo','route' => $studentFreshFormFillingVidPath );
										}else if(@$role_id == config("global.developer_admin")){
											$filePath = route('download',encrypt('/manual/student_applicaton_form_filling.pdf'));
											//$lists[] = array('lbl_text' =>' राजस्थान  स्टेट ओपन स्कूल छात्र आवेदन पत्र भरने का मैनुअल पीडीएफ प्रारूप में।(Student Application Form Filling Manual in PDF format.)',  'btnText' => 'PDF Manual', 'is_new' => true, 'route' => $filePath ); 
											
											$lists[] = array('lbl_text' =>' राजस्थान  स्टेट ओपन स्कूल छात्र आवेदन पत्र भरने का मैनुअल वीडियो प्रारूप में।(Student Application Form Filling Manual in Video format.) ',  'btnText' => 'Video Manual', 'is_new' => true, 'target' => '_blank', 'is_quick_view_video' => 'modelContentVideo', 'route' => $studentFreshFormFillingVidPath);
										}  
									?>  
									
									
									  
									 <table class="table collection waves-color-demo"> 
										@if(@$lists)
											@foreach(@$lists as $k => $item) 
												<tr>
													<td  width="75%" style="line-height: 25px;color:#0e0e88;font-size:20px;">
														<span class="language-markup" style="color: #0e0e88">
															 <!--{{ @$k+1 }}.  -->  
															{{ @$k+1 }}. @if(@$item['color'])
																<span style="color:#0e0e88">
																	{{ @$item['lbl_text'] }} 
																</span>
															@else
																{{ @$item['lbl_text'] }} 
															@endif
															@if(@$item['is_new'])
																<img class="" src="{{asset('public/app-assets/images/new.jpg')}}" height="20" alt="materialize logo"/>
															@endif
														</span>
													</td>
													<td width="25%" style="text-align: center;">
														@php $temp = @$item['is_quick_view_video']; @endphp 
														@if(@$item['is_quick_view_video'])
															&nbsp; <span class="waves-effect waves-light  modal-trigger modalCls waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content" style="color:blue;" data-content="{{ $$temp }}">Video Quick View</span>
														@else													
															<a href="{{ @$item['route'] }}" target="{{  @$item['target'] }}" class="waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content">
																{{ @$item['btnText'] }}
															</a>
														@endif
													</td>
												</tr>
											@endforeach
										@else
											<tr>
												<td>
													<center>No Record Found</center>
												</td>
											</tr>
										@endif
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
		 
 
       
@endsection 

 

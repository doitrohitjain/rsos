@extends('layouts.default')
@php 
	use App\Helper\CustomHelper;
@endphp
  
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							@foreach($breadcrumbs as $v)
								<li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
							@endforeach 
						</ol>
					</div>
				</div>
			</div>
        </div>
		 
        <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<p class="caption mb-0">
										<h6>
											@if(@$subject_id_for_link && @$subject_id_for_link > 0)
												@php $subjectName = @$subject_list[$subject_id_for_link]; @endphp
												<span style=""><a href="{{ route('reval_generate_template',@$subject_id_for_link) }}" class="btn btn-xs btn-info pull-left waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Download {{ @$subjectName }} Seprate Template</a></span>
												
												<span style=""><a href="{{ route('downloadrevalgeneratetemplateExl',@$subject_id_for_link) }}" class="btn btn-xs btn-info pull-left waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Download {{ @$subjectName }} Consolidated Excel</a></span>
											@else 
												<span style=""><a href="{{ route('reval_generate_template',@$subject_id_for_link) }}" class="btn btn-xs btn-info pull-left waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Download Consolidated Template</a></span>
											
											   <span style=""><a href="{{ route('downloadrevalgeneratetemplateExl',@$subject_id_for_link) }}" class="btn btn-xs btn-info pull-left waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Download Consolidated Excel</a></span>
											@endif 
											
											@if(@$subject_id_for_link && @$subject_id_for_link > 0)
												@php $subjectName = @$subject_list[$subject_id_for_link]; @endphp
												<span style=""><a href="{{ route('reval_generate_pdf_obtained_marks',@$subject_id_for_link) }}" class="btn btn-xs btn-info waves-effect waves-light btn gradient-45deg-purple-deep-orange gradient-shadow  white-text">Download {{ @$subjectName }} Seprate Obtained Marks</a></span>
											@else 
												<span style=""><a href="{{ route('reval_generate_pdf_obtained_marks',@$subject_id_for_link) }}" class="btn btn-xs btn-info waves-effect waves-light btn gradient-45deg-purple-deep-orange gradient-shadow  white-text">Download Consolidated Obtained Marks</a></span>
											@endif
											<div class="row">&nbsp;</div>
											<span style=""><a href="{{ route('reval_obtained_marks') }}" class="btn btn-xs btn-info pull-left">Reval Marks Obtained Enteries</a></span>
										</h6>
									</p>
									@include('elements.filters.search_filter')
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row"> 
										@if(@$master)
											 @php $counter = 0; @endphp
												<table>
													<tr>
														<th>Sr.No.</th>
														<th>Enrollment</th>
														<th>Student Fixcode</th>
														<th>Center Fixcode</th>
														<th>Sub Code</th>
														<th>Marks on the Answer sheet before Reval</th>
														<th style="text-decoration:underline;">Marks on the Answer sheet after Reval</th>
														<th style="text-decoration:underline;">Remarks</th>
														<th>Theory Marks in result before Reval</th>
														<th>Theory Marks in the result after Reval</th>
														<th>Sessional Marks in the Result</th>
														<th>Practical Marks in the Result</th>
														<th>Total Marks before Reval</th>
														<th>Total Marks after Reval</th>
														<th>Final Result before Reval</th>
														<th>Final Result after Reval</th>
													</tr>
													
													@php 
														foreach($master as $k => $v){ $counter++;
															@endphp
															<tr>
																<td>
																	{{ @$counter }}
																</td>
																<td>{{ @$v->enrollment }}</td>
																<td>{{ @$v->studentfixcode }}</td>
																<td>{{ @$v->centerfixcode }}</td>
																<td>{{ @$subjectCodes[$v->subject_id] }}</td>						
																<td>{{ @$v->marks_on_answer_book_before_reval }}</td>
																<td>{{ @$v->theory_marks_in_reval }}</td>
																<td>
																	@php $v->reval_type_of_mistake = CustomHelper::extractFirstNumberOfWords(@$v->reval_type_of_mistake,5);
																	@endphp
																	{{ @$v->reval_type_of_mistake }}
																</td>
																<td>{{ @$v->final_theory_marks }}</td>
																<td>{{ @$v->final_theory_marks_after_reval }}</td> 
																<td>@if( @$v->sessional_marks == 999)
																		@php $v->sessional_marks = 0; @endphp
																	@endif
																	{{ @$v->sessional_marks }}</td>
																<td>
																	@if( @$v->final_practical_marks == 999)
																		@php $v->final_practical_marks = 0; @endphp
																	@endif
																	{{ @$v->final_practical_marks }}
																</td>
																<td>{{ @$v->total_marks }}</td>
																<td>{{ @$v->total_marks_after_reval }}</td>				
																<td> 
																	@php $rr = "N/A";
																	@endphp 
																	@if(isset($resultsyntax[@$v->final_result]))
																		@php $rr = $resultsyntax[@$v->final_result]; @endphp 
																	@endif
																	{{ @$rr }}
																</td> 
																<td> 
																	@php $rr = @$v->final_result_after_reval;
																	@endphp 
																	@if(isset($resultsyntax[@$v->final_result_after_reval]))
																		@php $rr = $resultsyntax[@$v->final_result_after_reval]; @endphp 
																	@endif
																	{{ @$rr }}
																</td> 	
															</tr>
													@php 	} 
													@endphp
												</table>
												{{ $master->withQueryString()->links('elements.paginater') }} 
											@else 
												<tbody><tr><td colspan="20" class="center text-red">Data Not Found</td></tr></tbody>
											@endif 
									</div>
								</div>
							</div>
						</div>
					</div>
				</div> 
          <div class="content-overlay"></div>
        </div>
		</div>
    </div>
</div> 
@endsection

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/reval/reval_marks_listing.js') !!}"></script> 
@endsection 



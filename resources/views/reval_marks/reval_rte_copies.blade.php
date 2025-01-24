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
											<!-- buttons -->
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
									<div class="scroll"> 
										@if(@$master)
											 @php $counter = 0; @endphp
												{!! Form::model($model, ['route' => ['reval_obtained_marks'],'id' => $model]) !!}
														
												<table class="table" >
													<tr style="">
														<th>Sr.No.</th>
														<th>Enrollment</th>
														<th>Student Fixcode</th>
														<th>Center Fixcode</th>
														<th>Sub Code</th>
														<th style="text-align:center;text-decoration:underline;min-width:200px;">RTE Status</th>
														<th>Marks on the Answer sheet before Reval</th>
														<th >Marks on the Answer sheet after Reval</th>
														<th  width="40%">Remarks</th>
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
																<td>{{ @$counter }}
																<input type="hidden" value="{{ Crypt::encrypt(@$v->student_allotment_marks_id) }}" id="student_allotment_marks_id_{{ $k }}" />
																<input type="hidden" value="{{ Crypt::encrypt(@$v->reval_student_subjects_id) }}" id="reval_student_subjects_id_{{ $k }}" />
																</td>
																<td>{{ @$v->enrollment }}</td>
																<td>{{ @$v->studentfixcode }}</td>
																<td>{{ @$v->centerfixcode }}</td>
																<td id='subject_id{{ $k }}'>{{ @$subjectCodes[$v->subject_id] }}</td>
																<td id='reval_rte_status{{ $k }}'>
																	@php $oldValFld = "reval_rte_status"; 
																	$fld='reval_rte_status[' . $k .']'; 
																	if(@$v->$oldValFld && $v->$oldValFld ){ @endphp

																	@php  }else{ @endphp
																	@php $v->$oldValFld = 1; @endphp
																	@php  }  @endphp

																	@if(@$v->reval_rte_status && 	@$v->reval_rte_status == 2)
																		<span styel="color:green;">{{ @$reval_rte_status[@$v->reval_rte_status] }}
																			</span>
																	@else
																		@php $placeholder="Reval RTI status"; @endphp
																		{!! Form::select($fld,@$reval_rte_status,$v->$oldValFld,['item'=>'reval_rte_status','key'=>$k, 'class'=>'select3 form-control revalRteStatusCls reval_rte_status_cls',
																			'id'=>'reval_rte_status_id_' . $k ,'autocomplete'=>'off'
																			//,'placeholder' => $placeholder
																		]); 
																			!!} 
																			<span id='span_reval_rte_status_id_{{ $k }}' .   style="color:green;"></span>

																			@include('elements.field_error')
																	@endif
																</td> 

																<td id='marks_on_answer_book_before_reval{{ $k }}'>{{ @$v->marks_on_answer_book_before_reval }}</td>
																<td id='reval_final_theory_marks{{ $k }}'>
																	{{ @$v->reval_final_theory_marks }}
																</td> 
																
																<td id='reval_type_of_mistake{{ $k }}'>
																	@php $v->reval_type_of_mistake = CustomHelper::extractFirstNumberOfWords(@$v->reval_type_of_mistake,5);
																	@endphp
																	{{ @$v->reval_type_of_mistake }}
																</td>
																
																<td id='final_theory_marks{{ $k }}'> {{ @$v->final_theory_marks }}</td>
																<td id='final_theory_marks_after_reval{{ $k }}'>{{ @$v->final_theory_marks_after_reval }}</td> 
																<td id='sessional_marks{{ $k }}'>
																	@if( @$v->sessional_marks == 999)
																		@php $v->sessional_marks = 0; @endphp
																	@endif
																{{ @$v->sessional_marks }}</td>
																<td id='final_practical_marks{{ $k }}'>
																	@if( @$v->final_practical_marks == 999)
																		@php $v->final_practical_marks = 0; @endphp
																	@endif
																	{{ @$v->final_practical_marks }}
																</td>
																<td id='total_marks{{ $k }}'>{{ @$v->total_marks }}</td>
																<td id='total_marks_after_reval{{ $k }}'>{{ @$v->total_marks_after_reval }}</td>				
																<td id='final_result{{ $k }}'> 
																	@php $rr = "N/A"; @endphp 
																	@if(isset($resultsyntax[@$v->final_result]))
																		@php $rr = $resultsyntax[@$v->final_result]; @endphp 
																	@endif
																	{{ @$rr }}
																</td>
																<td id='final_result_after_reval{{ $k }}'> 
																	@php $rr = "N/A"; @endphp 
																	@if(isset($resultsyntax[@$v->final_result_after_reval]))
																		@php $rr = $resultsyntax[@$v->final_result_after_reval]; @endphp 
																	@endif
																	{{ @$rr }}
																</td>			
															</tr>
													@php 	} 
													@endphp
												</table>
												{{ Form::close() }}
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
	<script src="{!! asset('public/app-assets/js/bladejs/reval/reval_rte_copies.js') !!}"></script> 
@endsection 



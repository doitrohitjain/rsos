@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
       <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
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
	@if(empty($data))
	<div class="col s12">
		<div class="container"> 
			<div class="section section-data-tables"> 
				<div class="row">
					<div class="col s12">
						<div class="card">
							<div class="card-content">
								@include('elements.filters.update_result_filter')
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	@endif
	@if(isset($data) && !empty($data))
	@php
	    $subjecdata=$subjectcount;
	@endphp
	@if(in_array("addsubject",$permissions))
	@if(isset($studentdata) && !empty($studentdata) && $studentdata->adm_type != '5' && $subjecdata < '7')
	<div class="row">
        <div class="col s12">
            <div class="container">
                <div class="seaction">
                    <h6><span style='color:red;'><b></b></span></h6>
                    <div class="card">
                        <div class="card-content">
						<h6><a href="{{ route('addsubject',Crypt::encrypt($studentdata->enrollment)) }}" class="btn btn-xs btn-info right">Add Subjects</a></h6>  
                            <h6>Add Subject</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	@endif
	@endif
		<div class="section section-data-tables"> 
			<div class="row">
				<div class="col s12">
					<div class="card">
					<div class="card-content">
                         <h5><b>Update Subjects Marks</b></h5>
							<div class="row"> 
								<table class="responsive-table">
									<thead>
										<tr>
											<th>S/R</th>
											<th>Student_id</th>
											<th>Enrollment</th>
											<th>Stream</th>
											<th>Course</th>
											<th>Subjects</th>
											<th>Exam_Year</th>
											<th>Exam_Month</th>
											<th>is_Deleted</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody> 
										@php $count=1; @endphp
										@if($data->count(0))
											@foreach($data as $data)
												<tr>
													<td>{{ $count }}</td>
													<td>{{@$data->student_id}}</td>
													<td>{{@$data->enrollment}}</td>
													<td>{{@$data->stream}}</td>
													<td>{{@$data->course}}</td>
													<td>{{@$subjects[@$data->subject_id]}}</td>
													<td>{{ @$exam_year_session[@$data->exam_year] }}</td>
									                <td>{{ @$exam_month_session[@$data->exam_month] }}</td>
													@if(in_array("delete_subjects",$permissions))
													@if(!empty(@$data->deleted_at))
														@if($subjecdata=='7')
															<td>
															<button type="button" class="btn btn-primary"  value="{{@$data->id}}">yes
																</button>
															</td>
														@else
														<td>
																<button1 type="button1" class="btn btn-primary" id="changestatus_{{@$data->id}}" class="changestatus" value="{{@$data->id}}">yes
																</button1>
															</td>
														@endif

												    @else
													    <td>
															<button1 type="button1" class="btn btn-primary" id="changestatus_{{@$data->id}}" class="changestatus" value="{{@$data->id}}">NO
															</button1>
														</td>
													@endif
													@endif
													@if(@$data->deleted_at==Null)
													<td>
														@if(in_array("edit_subjects_marks",$permissions))
														<div class="invoice-action">
															<a href=" {{ route('editresult',Crypt::encrypt($data->id)) }}" class="invoice-action-edit">
															<i class="material-icons">edit</i></a>
														</div>
														@endif
													</td>
													@endif
												</tr>
											@php  $count++; @endphp
											@endforeach  
										@else
											<tr>
												<td colspan="10" class="text-center text-primary">There are no data.</td>
											</tr>
										@endif         
									</tbody>
								</table>
							</div>
							&nbsp;&nbsp;  
							
							<div class="row">	
								<div class="input-field md-4">
								@if(in_array("update_final_marks",$permissions))	
								@if($data2==0)
								<a href="{{ route('finalupdate',Crypt::encrypt(@$data->enrollment)) }}"class="btn cyan waves-effect waves-light right show_confirm" style="background: linear-gradient(45deg,#303f9f,#7b1fa2);width:30%">Update Final Result</a>
							    @else
								<span class="right" style='color:red ;width:35%'><h5 style='color: red;width:100%;font-size:22px'><b> Note:- Please update Subject Result.</b></h5></span>
								@endif

								@endif
							</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script >
			$(document).ready(function() {
				$('button1').on('click',function(event){
				var id=$(this).attr('id').replace(/[^\d.]/g,'');
				$.ajax({
						url: "{{ route('updatevalue') }}",
						type: "get",
						data: {'id': id},
                        success: function(data){
                            console.log(data.success)
							location.reload();
                        }
					});
				});
			});
		</script> 
	@endif
</div>
</div>
@endsection 
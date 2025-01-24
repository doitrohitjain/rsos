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
	@if(isset($data) && !empty($data))
		<div class="section section-data-tables"> 
			<div class="row">
				<div class="col s12">
					<div class="card">
						<div class="card-content">
							<div class="row"> 
								<table class="responsive-table">
									<thead>
										<tr>
											<th>S/R</th>
											<th>Student_id</th>
											<th>Enrollment</th>
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
													<td>{{@$data->course}}</td>
													<td>{{@$data->Subject->name}}</td>
													<td>{{ @$exam_year_session[@$data->exam_year] }}</td>
									                <td>{{ @$exam_month_session[@$data->exam_month] }}</td>
													@if(!empty(@$data->deleted_at))
													    <td>
															<button type="button" class="btn btn-primary" id="changestatus" value="{{@$data->id}}">yes
															</button>
														</td>
												    @else
													    <td>
															<button type="button" class="btn btn-primary" id="changestatus" value="{{@$data->id}}">NO
															</button>
														</td>
													@endif
													@if(@$data->deleted_at==Null)
													<td>
														<div class="invoice-action">
															<a href=" {{ route('editresult',Crypt::encrypt($data->id)) }}" class="invoice-action-edit">
															<i class="material-icons">edit</i></a>
														</div>
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
						</div>
					</div>
				</div>
			</div>
		</div>
		<script >
			$(document).ready(function() {
				$('#changestatus').on('click',function(){
				var id=this.value;
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



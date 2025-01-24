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
	@if(isset($finalResult) && !empty($finalResult))
		@php 
			$item = "masters"; $masters = $finalResult[$item];
			$genders = $masters['gender'];
			$courses = $masters['course'];
			$states = $masters['states'];
			$districts = $masters['districts'];
			$blocks = $masters['blocks']; 
		@endphp  
		<div class="section section-data-tables"> 
			<div class="row">
				<div class="col s12">
					<div class="card">
						<div class="card-content">
							<div class="row"> 
								<table class="responsive-table">
									<thead>
										<tr>
											<th>#</th>
											<th>Type</th>  
											<th>District</th> 
											<th>Block</th> 
											<th>Student Id</th> 
											<th>Enrollment</th> 
											<th>Name</th> 
											<th>Father's Name</th> 
											<th>Mother's Name</th> 
											<th>DOB</th> 
											<th>Gender</th> 
											<th>Course</th> 
											<th>Total Marks</th> 
											<th>Percentage</th>  
											<th>Final Result</th>  
											<th>Rank</th>  
										</tr>
									</thead>
									@php $count=0; $item="state"; $last_total_marks=null;@endphp  
									<tbody>  
										@if(!empty($finalResult[$item]))
											@foreach(@$finalResult[$item] as $k1 => $data1)
												@foreach(@$data1 as $k2 => $data2) 
													@php $rank=0; @endphp
													@foreach(@$data2 as $k3 => $data3)
														<tr>
															<td> @php echo ++$count; @endphp </td>
															<td> @php echo strtoupper($item); @endphp </td>
															<td> @php $fld = "district_id";echo @$districts[$data3->$fld]; @endphp </td>
															<td> @php $fld = "block_id";echo @$blocks[$data3->$fld]; @endphp </td>
															<td> @php $fld = "student_id";echo @$data3->$fld; @endphp </td>
															<td> @php $fld = "enrollment";echo @$data3->$fld; @endphp </td>
															<td> @php $fld = "name";echo @$data3->$fld; @endphp </td>
															<td> @php $fld = "father_name";echo @$data3->$fld; @endphp </td>
															<td> @php $fld = "mother_name";echo @$data3->$fld; @endphp </td>
															<td> @php $fld = "dob";echo @$data3->$fld; @endphp </td>
															<td> @php $fld = "gender_id";echo @$genders[$data3->$fld]; @endphp </td>
															<td> @php $fld = "course";echo @$courses[$data3->$fld]; @endphp </td>
															<td> @php $fld = "total_marks";echo @$data3->$fld; @endphp </td>
															<td> @php $fld = "percent_marks";echo @$data3->$fld; @endphp </td>
															<td> @php $fld = "final_result";echo @$data3->$fld; @endphp </td>
															<td>
																@php
																	if(!empty($last_total_marks)){
																		if($last_total_marks == $data3->total_marks){

																		}else{
																			$rank++;
																		}
																	}else{
																		$rank++;
																	} 
																	$last_total_marks = $data3->total_marks;
																	echo $rank; 
																@endphp 
															</td>
														</tr>  
													@endforeach 
												@endforeach  
											@endforeach 
										@endif 
									</tbody>
									@php $count=0; $item="district"; @endphp  
									<tbody>  
										@if(!empty($finalResult[$item]))
											@foreach(@$finalResult[$item] as $k1 => $data1)
												@foreach(@$data1 as $k2 => $data2)   
													@foreach(@$data2 as $k3 => $districtsArr)
														@php $rank=0; @endphp
														@foreach(@$districtsArr as $k3 => $data3)
															<tr>
																<td> @php echo ++$count; @endphp </td>
																<td> @php echo strtoupper($item); @endphp </td>
																<td> @php $fld = "district_id";echo @$districts[$data3->$fld]; @endphp </td>
																<td> @php $fld = "block_id";echo @$blocks[$data3->$fld]; @endphp
																<td> @php $fld = "student_id";echo @$data3->$fld; @endphp </td>
																<td> @php $fld = "enrollment";echo @$data3->$fld; @endphp </td>
																<td> @php $fld = "name";echo @$data3->$fld; @endphp </td>
																<td> @php $fld = "father_name";echo @$data3->$fld; @endphp </td>
																<td> @php $fld = "mother_name";echo @$data3->$fld; @endphp </td>
																<td> @php $fld = "dob";echo @$data3->$fld; @endphp </td>
																<td> @php $fld = "gender_id";echo @$genders[$data3->$fld]; @endphp </td>
																<td> @php $fld = "course";echo @$courses[$data3->$fld]; @endphp </td>
																<td> @php $fld = "total_marks";echo @$data3->$fld; @endphp </td>
																<td> @php $fld = "percent_marks";echo @$data3->$fld; @endphp </td>
																<td> @php $fld = "final_result";echo @$data3->$fld; @endphp </td>
																<td>
																	@php
																		if(!empty($last_total_marks)){
																			if($last_total_marks == $data3->total_marks){

																			}else{
																				$rank++;
																			}
																		}else{
																			$rank++;
																		} 
																		$last_total_marks = $data3->total_marks;
																		echo $rank; 
																	@endphp 
																</td>
															</tr>  
														@endforeach 	
													@endforeach   
												@endforeach  
											@endforeach  
										@endif
									</tbody>

									@php $count=0; $item="block"; @endphp  
									<tbody>  
										@if(!empty($finalResult[$item]))
											@foreach(@$finalResult[$item] as $k1 => $data1)
												@foreach(@$data1 as $k2 => $data2)   
													@foreach(@$data2 as $k3 => $districtsArr)
														@foreach(@$districtsArr as $k3 => $blockArr)
															@php $rank=0; @endphp
															@foreach(@$blockArr as $k3 => $data3)
																<tr>
																	<td> @php echo ++$count; @endphp </td>
																	<td> @php echo strtoupper($item); @endphp </td>
																	<td> @php $fld = "district_id";echo @$districts[$data3->$fld]; @endphp </td>
																	<td> @php $fld = "block_id";echo @$blocks[$data3->$fld]; @endphp
																	<td> @php $fld = "student_id";echo @$data3->$fld; @endphp </td>
																	<td> @php $fld = "enrollment";echo @$data3->$fld; @endphp </td>
																	<td> @php $fld = "name";echo @$data3->$fld; @endphp </td>
																	<td> @php $fld = "father_name";echo @$data3->$fld; @endphp </td>
																	<td> @php $fld = "mother_name";echo @$data3->$fld; @endphp </td>
																	<td> @php $fld = "dob";echo @$data3->$fld; @endphp </td>
																	<td> @php $fld = "gender_id";echo @$genders[$data3->$fld]; @endphp </td>
																	<td> @php $fld = "course";echo @$courses[$data3->$fld]; @endphp </td>
																	<td> @php $fld = "total_marks";echo @$data3->$fld; @endphp </td>
																	<td> @php $fld = "percent_marks";echo @$data3->$fld; @endphp </td>
																	<td> @php $fld = "final_result";echo @$data3->$fld; @endphp </td>
																	<td>
																		@php
																			if(!empty($last_total_marks)){
																				if($last_total_marks == $data3->total_marks){

																				}else{
																					$rank++;
																				}
																			}else{
																				$rank++;
																			} 
																			$last_total_marks = $data3->total_marks;
																			echo $rank; 
																		@endphp 
																	</td>
																</tr>  
															@endforeach 	
														@endforeach   
													@endforeach   
												@endforeach  
											@endforeach  
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



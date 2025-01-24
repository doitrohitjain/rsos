@extends('layouts.default')
@section('content')
<style>
	.alignclass{
		text-align: center;
	}
</style>

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
		
        {{-- <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									@include('elements.filters.search_filter')
								</div>
							</div>
						</div>
					</div>
				</div> --}}
				
				<div class="row">
					<div class="col s12">
						<div class="container">
							<div class="seaction">
								<div class="card">
									<div class="card-content">  
										<div class="red-text">
											Note: If you need to update the student absent or nr then you should first click on the button(Update ABSENT/NR) and then update the details.
											<br>(यदि आपको छात्र अनुपस्थित या एनआर को अपडेट करने की आवश्यकता है तो आपको पहले बटन पर क्लिक करना चाहिए (Update ABSENT/NR) और फिर विवरण अपडेट करें।)
											<a href="javascript:void(none);" class="gradient-45deg-amber-amber btn btn-xs right update_absent_nr">
												Update ABSENT/NR 
											</a>
										</div> 
									</div>
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
									
										<table>
										
											<tbody>
												<tr >
													<th >Examination Center</th>
													<td >{{@$examiner_list[@$data->examcenter_detail_id]}}</td>
													<th >Course</th>
													<td >{{@$course[@$data->course_id]}}</td>
													<th >Subject</th>
													<td >{{@$subjects[@$data->subject_id]}}</td>
													<th >Examiner SSO ID</th>
													<td >{{@$userdetails->ssoid}}</td>
												</tr>
												<tr class='alignclass'>
													<th>Examiner Name</th>
													<td>{{@$userdetails->name}}</td>
													<th>Total Students Appearing</th>
													<td>{{@$data->total_students_appearing}}</td>	
													<th>Total Copies of the subject</th>
													<td >{{@$data->total_copies_of_subject}}</td>
													<th >Total Absent</th>
													<td >{{@$data->total_absent}}</td>
												</tr>
												
											    <tr class='alignclass'>
												    <th >Total NR</th>
													<td >{{@$data->total_nr}}</td>
													<th>Maximum Marks</th>
													<td >{{@$getMaxMarks->theory_max_marks}}</td>
												</tr>
											</tbody>
										</table>
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
										{{ Form::open(['url'=>url()->current(),'id'=>'marks_submissions_id']) }}
										{!! Form::token() !!}
										{{ method_field('POST') }}
											<table>
												<thead>
													<tr>
														<th >SR</th>
														<th style='text-align:center'>Fictitious Code</th>
														<th style='text-align:left;display:none' class="absent_nr_div">Is Absent Mark</th>
														<th style='text-align:left;display:none' class="absent_nr_div">Is NR Mark</th>
														<th>Marks Scored by the student</th>
													</tr>
												</thead>
												<tbody>
													
													@php Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation',
													'value'=>'1']); 
													@endphp
													<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
													@if($result) 
														@php 
														if($result->currentPage()!=1){
															$i= $defaultPageLimit * ($result->currentPage()-1)+1; 
														} else {
															$i=1;
														}
														@endphp
														@foreach(@$result as $k=>$data)
															<tr>
																<td>{{ $i }}</td>
																<td style='text-align:center'>{{ @$data->fixcode }}</td>
																<td style='text-align:left;display:none' class="absent_nr_div">
																<label>
																	<?php
																	$is_check_class = '';
																	if(@$data->theory_absent=='1'){
																		$is_check_class= 'checked="checked"';
																	}
																	?>
																	<input type='checkbox' id="theory_absent_<?php echo $i; ?>" name='data[{{ $k }}][theory_absent]' class="theory_absent theory_absent_<?php echo $i; ?>  check_absent_marks"  <?php echo $is_check_class; ?> /><span></span>
																</label>
																</td>
																
																<td style='text-align:left;display:none' class="absent_nr_div">
																	<label>
																		<?php
																		$is_check_class = '';
																		if(@$data->theory_absent=='2'){
																			$is_check_class= 'checked="checked"';
																		}
																		?>
																		<input type='checkbox' id="theory_absent_nr_<?php echo $i; ?>" name='data[{{ $k }}][theory_absent_nr]' class="theory_absent_nr theory_absent_nr_<?php echo $i; ?>  check_absent_marks"  <?php echo $is_check_class; ?> /><span></span>
																		</label>
																</td>
																
																<td>
																	@if(@$data->theory_absent=='1')
																		<?php echo "<span class='final_theory_marks_span red-text' >Absent</span>";
																		  $minmarks=0; 
																		$data->final_theory_marks ='';
																		 ?>
																		<input type='text' id="theorymarks<?php echo $i; ?>" name='data[{{ $k }}][final_theory_marks]' class="test1 num final_theory_marks final_theory_marks_<?php echo $i; ?>  check_absent_marks"  value='{{  $data->final_theory_marks; }}' readonly  >
																		
																		<input type='hidden' name='data[{{ $k }}][student_allotment_marks_id]' class='student_allotment_marks_id' value='{{ Crypt::encrypt(@$data->id) }}' maxlength="3" readonly>
																		
																		@elseif(@$data->theory_absent=='2')
																		<?php echo "<span class='final_theory_marks_span blue-text'>NR</span>";
																		$minmarks=0; 
																		$data->final_theory_marks ='';
																		?>
																		
																		<input type='text' id="theorymarks<?php echo $i; ?>" name='data[{{ $k }}][final_theory_marks]' class="num final_theory_marks final_theory_marks_<?php echo $i; ?>  check_absent_marks" value='{{  $data->final_theory_marks; }}' readonly >
																	
																		<input type='hidden' name='data[{{ $k }}][student_allotment_marks_id]' class='student_allotment_marks_id' value='{{  Crypt::encrypt(@$data->id) }}' maxlength="3" readonly>
																		
																		@else
																		<input type='text' id="theorymarks<?php echo $i; ?>" name='data[{{ $k }}][final_theory_marks]' class="num final_theory_marks  final_theory_marks_<?php echo $i; ?>  check_absent_marks" value='{{  $data->final_theory_marks; }}' maxlength="3"  required>
																	
																		<input type='hidden' name='data[{{ $k }}][student_allotment_marks_id]' class='student_allotment_marks_id' class='num' value='{{ Crypt::encrypt(@$data->id)   }}'>
																		@endif
																</td>
															</tr>
														@php  $i++; @endphp
														@endforeach
													@endif
												</tbody>
											</table>
											
											<div class="card">
												<div class="row">
													<input type='hidden' name='last_page_id' class="last_page_id" value='{{ Crypt::encrypt($result->lastPage()) }}'></td>
													<input type='hidden' name='current_page_id' class="current_page_id" value='{{ Crypt::encrypt($result->currentPage()) }}'></td>
													<input type='hidden' name='max_marks' class="max_marks" value='{{ Crypt::encrypt(@$getMaxMarks->theory_max_marks) }}'>
													<input type='hidden' name='min_marks' class="min_marks" value='0'>
												
													<div class="col m9 s12 mb-3" style="margin-top:1%">
														<button class="btn cyan waves-effect waves-light right" type="reset">
														<i class="material-icons right">clear</i>Reset
														</button>
													</div>
													<?php if($result->lastPage()==$result->currentPage()){ ?>
														<div class="col m3 s12 mb-3" style="margin-top:1%">
															<button class="btn cyan waves-effect waves-light gradient-45deg-deep-orange-orange right" type="submit" name="action">preview
															<i class="material-icons right">send</i>
														</button>
													</div>
													<?php } else { ?>
														<div class="col m3 s12 mb-3" style="margin-top:1%">
															<button class="btn cyan waves-effect waves-light gradient-45deg-green-teal right" type="submit" name="action">Submit & Next
															<i class="material-icons right">send</i>
														</button>
													</div>
													<?php } ?>
												</div>
											</div>
											{{ $result->links('elements.paginater') }}
											{{ Form::close() }}
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
	<script>
		min_marks = '0';
		max_marks = '<?php echo $maxmarks ?>';
	</script>
    <script src="{!! asset('public/app-assets/js/bladejs/theory_marks_submissions.js') !!}"></script> 
@endsection 
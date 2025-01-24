@extends('layouts.default')
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
								<h6>{{ $title }}<h6>
							<h6>
								<span class="z-depth-2" style="color:red;font-size:16px;line-height: 1.6;">
									{{ @$specialNoteRemarks[0] }}
								</span>
								@can('practical_examiner_add')
								<a href="{{route('practicalexamineradd')}}" class="btn btn-xs btn-info right hide">Add Practical Examiner</a>
								@endcan
								
							</h6>
							
							</div>
							</div>
						</div>
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									@include('elements.filters.search_filter')
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
							<div class="row"> 
								<table>
									<thead>
										<tr>
											<th>#</th>
											<!-- <th>Academic Year</th> -->
											<th>DEO Name</th>
											<th>Exam Center Name</th>
											<th>Course</th>
											<!-- <th>Stream</th> -->
											<th>Subject</th>
											<th>Is Lock & Submitted</th>
											<th>Is Signed PDF Uploaded</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										@php  $i= 1; @endphp
										@foreach(@$master as $data)
										<tr>
											<td>{{ $i; }}</td>
											<!-- <td><?php if(isset($exam_year_arr[@$current_exam_year]) ){ echo $exam_year_arr[@$current_exam_year]; }else { echo "-"; } ?></td> -->
											<!-- <td><?php if(isset($exam_year_arr[@$current_exam_year]) && isset($exam_month_arr[@$current_exam_month])){ echo $exam_year_arr[@$current_exam_year]."/".$exam_month_arr[@$current_exam_month]; }else { echo "-"; } ?></td> -->
											<td>{{ @$data->name }}</td> 
											<td><?php if(isset($examcenter_list[@$data->examcenter_detail_id])){ echo $examcenter_list[@$data->examcenter_detail_id]; }else { echo "-"; } ?></td>
											<td>{{ @$data->course }}th</td>
											<!-- <td>{{ @$data->stream }}</td> -->
											<td><?php if(isset($subject_list[$data->subject_id])){ echo $subject_list[$data->subject_id]; }else { echo "-"; } ?></td>
											
											<td>
												@php if($data->is_lock_submit=='1'){ @endphp
													<i class="material-icons dp48" style="color: green;">lock_outline</i> 
												@php } else { @endphp 
													<i class="material-icons dp48" style="color: red;">lock_open</i>
												@php } @endphp 
											</td>
											
											<td>
												@php if($data->document!=''){ @endphp
													<i class="material-icons dp48" style="color: green;">check</i>
												@php } else { @endphp 
													<i class="material-icons dp48" style="color: red;">close</i>
												@php } @endphp 
											</td>
											
											<td class="width-20" >
												@php $unsignedPdf = "UnSigned PDF"; @endphp
												@php if($data->document=='' && $data->is_lock_submit!=1){ @endphp
												<a href="{{ route('old_add_marks',Crypt::encrypt($data->id)) }}" class="btn btn-xs btn-info invoice-action-view" title="Click here to Entry Marks.">+ Practical Marks Entry</a>
												@php } else  if($data->document==''){ @endphp
													@if($role_id == config("global.developer_admin"))
														<a href="{{route('practicalMarksSubmissionPdf',Crypt::encrypt($data->id))}}" class="btn btn-xs btn-info left" title="Click here to Upload UnSigned PDF.">
															{{ $unsignedPdf }}
														</a>
													@endif
												<a href="{{ route('examiner_marks_docupload',Crypt::encrypt($data->id)) }}" class="btn btn-xs orange invoice-action-view" >+Document</a>
												@php  } else  if($data->is_lock_submit!=1){ @endphp
												<a href="{{ route('add_marks',Crypt::encrypt($data->id)) }}" class="btn btn-xs red invoice-action-view">+ Lock & Submit</a>
												@php } else {
													$path = "public/" . @$practicalDocumentPath . @$data->id . "/" . @$data->document;
												@endphp 
												
												@if($role_id == config("global.developer_admin"))
													<a href="{{route('practicalMarksSubmissionPdf',Crypt::encrypt($data->id))}}" class="btn btn-xs btn-info left" title="Click here to Download UnSigned PDF.">
														{{ $unsignedPdf }}
													</a>
												@endif


												<a href="{{ $path }}" 
												class="btn btn-xs green left" download title="Click here to Download Signed PDF.">Signed PDF</a>
												@php }  @endphp
											</td>
										</tr>
										@php  $i++; @endphp
										@endforeach
									</tbody>
								</table>
								{{-- $master->links('elements.paginater') --}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div> 
		<div class="content-overlay"></div>
		
    </div>
</div>
<style>
.width-10 {
	width:10%
}
.width-20 {
	width:20%
}
.width-30 {
	width:30%
}
.width-50 {
	width:50%
}
.width-90 {
	width:90%
}

</style> 
@endsection

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/practical/practicals.js') !!}"></script> 
@endsection 
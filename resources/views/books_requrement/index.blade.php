@extends('layouts.default')
@section('content')
<style>
      .text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
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
        <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
				<div class="row">
				<div class="col s12">
					<div class="card">
					<div class="card-content">
					 @can('add_books_requrement')
					 <h6><a href="{{route('bookadd')}}" class="btn btn-xs btn-info right mb-2 mr-1">ADD Books Requirement</a></h6>
					  @endcan
					<h6>Books Requirement Details<h6>
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
	
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row"> 
									<table class="responsive-table">
									<thead>
									<tr>
									<th>SR.NO</th>
									<th>Ai Code</th>
									<th>Course</th>
									<th>Subject</th>
									<th>Volume</th>
									<th>Hindi Enrollment Student Count</th>
									<th>English Enrollment Student Count</th>
									<th>Hindi Last Year Book Stock Count</th>
									<th>English Last Year Book Stock Count</th>
									<th>Hindi Required Book Count</th>
									<th>English Required Book Count</th>
									<th>Action</th>
									</tr>
									</thead>
									<tbody>
									@php $i =($data->currentpage()-1)* $data->perpage() + 1; @endphp
										@foreach ($data as $key => $getdata)
											<tr>
											<td>{{ $i }}</td>
											<td>{{@$getdata->ai_code }}</td>
											<td>{{@$getdata->course }}</td>
											<td>{{@$subject_list[@$getdata->subject_id] }}</td>
											<td>{{@$book_publication_volumes[@$getdata->subject_volume_id]}}</td>
											<td>{{@$getdata->hindi_auto_student_count }}</td>
											<td>{{@$getdata->english_auto_student_count }}</td>
											<td>{{@$getdata->hindi_last_year_book_stock_count }}</td>
											<td>{{@$getdata->english_last_year_book_stock_count }}</td>
											<td>{{@$getdata->hindi_required_book_count }}</td>
											<td>{{@$getdata->english_required_book_count }}</td>
											
											<td>
												<div class="invoice-action">
													@can('edit_books_requrement')
														<a href="{{ route('bookedit',Crypt::encrypt($getdata->id)) }}" class="invoice-action-edit">
														<i class="material-icons tooltipped" data-position="bottom" data-tooltip="Are You Edit">edit</i></a>
													@endcan
													@can('delete_books_requrement') 
														<a href="{{ route('bookdelete',Crypt::encrypt($getdata->id)) }}" class="invoice-action-delete delete-confirm">
														<i class="material-icons tooltipped" data-position="bottom" data-tooltip="Are you sure?<br>This record and it`s details will be deleted!">delete</i></a>
													@endcan
												</div>
											</td>
											  @php  $i++; @endphp
										@endforeach 
                                    	
									</tfoot>
									</table>
									{{ $data->withQueryString()->links('elements.paginater') }}
									</div>
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
@section('customjs')
<script>
$('.delete-confirm').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be deleted!',
        icon: 'warning',
        buttons: ["Cancel", "Yes"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});

$(document).ready(function() {
    $('.course').on('change',function(){
        var courseid = this.value;
        $(".subject_id").html('<option value="">Select Subject</option>');
    $.ajax({
              url: config.routes.getsubjects,
              type: "get",
              data: {'id': courseid},
              dataType : 'json',
              success: function (result){
                $(".subject_id").html('<option value="">Select Subject</option>');
                $.each(result,function(key,value){
                  $('.subject_id').append('<option value="' + key + '">' +value+'</option>');
                  $(".subject_id").trigger('contentChanged');
                });	
              },
            });
       
       });
});
</script>

@endsection 




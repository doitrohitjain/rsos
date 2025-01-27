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
						<div class="card-content right" style="padding:0px !important;left-margin:5px !important">
							@can('deo_add')
								<a href="{{route('deocreate')}}" class="btn btn-xs btn-info">ADD {{ $title }}</a>
							@endcan
							@can('deo_listing')
							<a href="{{route('examiner_mapping_list')}}" style="left-margin:5px !important" class="btn btn-xs btn-info">Examiner Mapped List</a>
							@endcan
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
										<table>
											<thead>
												<tr>
													<th>Sr. No.</th>
													<!--<th>ID</th>-->
													<th>District</th>
													<th>Name</th>
													<th>SSOID</th>
													<th>Email</th>
													<!--<th>Roles</th>-->
													<!-- <th>Session Year</th>
													<th>Session month</th> -->
													<th>Action</th>
												</tr>
											</thead>
											
											<tbody>
												<?php $srno =1; ?>
												@foreach ($data as $key => $user)
												<tr id ="row1">
													<td>
														{{ $srno; }}
													</td>
													<!--<td>
														{{ $user->id }}
													</td>-->
													<td>{{ @$user->district_name }}</td>
													<td>{{ @$user->name }}</td>
													<td>{{ @$user->ssoid }}</td>
													<td>{{ @$user->email }}</td>
													<!--<td>
														@php 
															$roles = $user->getRoleNames()->toArray();
															$roles = implode(" ,",$roles);
														@endphp 
														<label class="badge badge-success">@php echo $roles;  @endphp </label>
													</td>-->
													<!-- <td>{{ @$exam_year_session[@$user->exam_year] }}</td>
													<td>{{ @$exam_month_session[@$user->exam_month] }}</td> -->
													<td>
														<div class="invoice-action">
															<!--<a href="{{ route('deoshow',Crypt::encrypt($user->id)) }}" class="invoice-action-view">
															<i class="material-icons">remove_red_eye</i>
															</a>-->
															
															@can('deo_edit')
																<a href="{{ route('deoedit',Crypt::encrypt($user->id)) }}" class="invoice-action-edit" title="Click here to Edit."> 
																<i class="material-icons">edit</i>
																</a>
															@endcan
															
															@can('deo_delete')
																<a href="javascript:void(0)" class="invoice-action-delete deleteItem" data-id="{{ Crypt::encrypt(@$user->id) }}">
																<i class="material-icons" title="Click here to Delete.">delete</i></a>
																</a>
															@endcan
														</div>
													</td>
												</tr>
												<?php $srno++; ?>
												@endforeach  
											</tbody>
										</table>
										{{ $data->links('elements.paginater') }}
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
	<script src="{!! asset('public/app-assets/js/bladejs/practicalexaminer_details222.js') !!}"></script> 
  	<script>
		$('body').on('click', '.deleteItem', function (){ 
			item_id = $(this).data("id");
			event.preventDefault();
			swal({
				title: 'Are you sure?',
				text: 'This record and it`s details will be permanantly deleted!',
				icon: 'warning',
				buttons: ["Cancel", "Yes!"],
			}).then(function(value) {
				if (value) {
						$.ajax({
							headers: {
								'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
							},

							type: "POST",
							dataType: "json",
							data: {"id":item_id},
							url: "{{ route('deo_destroy') }}",
							success: function (data) { 
								toastr.success(data.success);
								window.location.reload();
							},
							error: function (data) {
								toastr.error(data.success);
								console.log('Error:', data);
							}

						});
					
				}

			});
		});
	</script>


@endsection 
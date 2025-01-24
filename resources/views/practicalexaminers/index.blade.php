@extends('layouts.default')
@section('content')
@php 
$role_id = Session::get('role_id'); 
use App\Component\PracticalCustomComponent;
@endphp

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
								<div class="card-content right" style="padding:0px !important;margin-left:5px !important">
									@can('examiner_mapping_add')
									<a href="{{route('practicalexamineradd')}}" class="btn btn-xs btn-info left">ADD Practical Examiner</a>
									@endcan
									
									<a href="{{route('examiner_mapping_list')}}" style="margin-left:5px !important" class="btn btn-xs btn-info right">Examiner Mapped List</a>
									
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
												<th width="5%">No</th>
												<th width="10%">SSOID</th>
												<th width="10%">Email</th>
												<!--<th>Roles</th>-->
												<!-- <th>Session Year/Month</th> -->
												<th width="20%">School Name</th>
												<th width="15%">Deo Name</th>
												<th width="40%"><center>Action</center></th>
											</tr>
										</thead>
										
										<tbody>
											@php $i=1; 
											// @dd($data); 
											if(isset($data) && !empty($data)){ @endphp
											@foreach (@$data as $key => $user)
											@php // @dd($user); @endphp
											<tr id ="row1">
												<td width="5%">{{ $i }}</td>
												<td width="10%">{{ $user->ssoid }}</td>
												<td width="10%">{{ $user->email }}</td>
												<!--<td>
													@php  
														// $roles = $user->getRoleNames()->toArray();
														// $roles = implode(" ,",@$roles);
														$roles = 'Practical Examiner';
													@endphp 
													<label class="badge badge-success">@php echo $roles;  @endphp </label>
												</td>-->
												<!-- <td>{{ @$exam_year_session[$user->exam_year] }}/{{ @$exam_month_session[$user->exam_month] }}</td> -->
												<td width="20%">{{ @$user->college_name }}</td>
												<td width="15%" >
													@php 
														$practical_custom_component_obj = new PracticalCustomComponent();
														$deo_data = $practical_custom_component_obj->getDeoDataById(@$user->user_deo_id);
														echo @$deo_data[0]['name'];														
													@endphp
												</td>
												<td width="40%"><center>
													<div class="invoice-action">
													   @can('examiner_mapping_view')
														<a href="{{ route('examiner_mapping_practical_list',Crypt::encrypt($user->id)) }}" class="invoice-action-view">
															<i class="material-icons" title="Click here to View.">remove_red_eye</i>
														</a> 
														@endcan
														@php 
															//dd($user);
															$deo = Config::get("global.deo");
															$developer_admin = Config::get("global.developer_admin");
														@endphp


														@can('examiner_mapping_add')
															<a href="{{ route('examiner_mapping',Crypt::encrypt($user->id)) }}"  
																data-id="{{ Crypt::encrypt(@$user->id) }}" style="line-height:18px !important;color: rgba(255, 255, 255, 0.901961);" title="Click here to Map Examiner.">
																<i class="btn btn-default" title="Click here to View Students List.">Map Examiner</i>
															</a> 
														@endcan

														@can('examiner_mapping_edit')
															<a href="{{ route('practicalexamineredit',Crypt::encrypt($user->id)) }}" class="invoice-action-edit">
																<i class="material-icons" title="Click here to Edit.">edit</i>
															</a>
														@endcan
														@can('examiner_mapping_delete')
															<a href="javascript:void(0)" class="invoice-action-delete deleteItem" data-id="{{ Crypt::encrypt(@$user->id) }}" data-deoid="{{ Crypt::encrypt(@$user->user_deo_id) }}" >
																<i class="material-icons" title="Click here to Delete.">delete</i>
															</a> 
														@endcan
													</div>
												    </center>
												</td>
											</tr>
											@php $i++; @endphp
											@endforeach  
											@php } else { @endphp
											<tr><td>Record Not Found</td></tr>
											@php }  @endphp
											</tbody>
										</table>
									@php if(isset($data) && !empty($data)){ @endphp
									{{ $data->links('elements.paginater') }}
									@php } @endphp
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
			deoid = $(this).data("deoid");
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
							data: {"id":item_id,"deoId":deoid},
							url: "{{ route('practicalexaminer_destroy') }}",
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
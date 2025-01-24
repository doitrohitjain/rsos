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
					 <h6><a href="{{route('users.create')}}" class="btn btn-xs btn-info right mb-2 mr-1">ADD Users</a></h6>
					 @can('all_delete_users')
					 <h6><a href="{{route('deleteusers')}}" class="btn btn-xs btn-info right mb-2 mr-1">All Delete Users</a></h6>
					 @endcan
					<h6>Users Details<h6>
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
									<th>No</th>
									<th>UserName</th>
									<th>SSOID</th>
									<th>Email</th>
									<th>Roles</th>
									<th>Session Year</th>
									<th>Session month</th>
									<th>Ai Center Name</th>
									<th>Ai code </th>
									<th>Action</th>
									</tr>
									</thead>
									<tbody>
									@foreach ($data as $key => $user)
									<tr>
									<td>
									{{ $user->id }}
									</td>
									<td >{{ $user->username }}</td>
									<td >{{ $user->ssoid }}</td>
									<td >{{ $user->email }}</td>
									<td>
										@php 
											$roles = $user->getRoleNames()->toArray();
											$roles = implode(" ,",$roles);
										@endphp 
									<label class="badge badge-success">@php echo $roles;  @endphp </label>
									
									</td>
									<td>{{ @$exam_year_session[$user->exam_year] }}</td>
									<td>{{ @$exam_month_session[$user->exam_month] }}</td>
									<td>{{ @$aiCenters[$user->ai_code] }}</td>
									<td>{{ @$user->ai_code }}</td>
									<td>
									 <div class="invoice-action">
									<a href="{{ route('users.show',$user->id) }}" class="invoice-action-view">
									<i class="material-icons">remove_red_eye</i>
									</a>
									<a href="{{ route('users.edit',$user->id) }}" class="invoice-action-edit">
									<i class="material-icons">edit</i>
									</a>
									<a href="{{ route('usersdelete',$user->id) }}" class="invoice-action-delete delete-confirm">
									<i class="material-icons">delete</i></a>
									
									</div>
									</td>
									</tr>
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
        text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
        buttons: ["Cancel", "Yes!"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});
</script>
@endsection 




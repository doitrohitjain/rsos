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
					 @can('add_aicenter')
					 <h6><a href="{{route('aicenterusers.create')}}" class="btn btn-xs btn-info right mb-2 mr-1">ADD AiCenter</a></h6>
					  @endcan
					<h6>AI Center Details<h6>
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
									<th style="width:5%;">SR.NO</th>
									<th style="width:10%;">Ai Code</th>
									<th style="width:45%;">College Name</th>
									<th style="width:5%;">District</th>
									<th style="width:5%;">Block</th>
									<th style="width:5%;">New District</th>
									<th style="width:5%;">New Block</th>

									@if(@$allowonlyotherrole==false)
										<th style="width:10%;">Principal Name</th>
										<th style="width:10%;">Nodal Officer Name</th>
										<th style="width:10%;">SSOID</th>
									
									@endif
										<th style="width:10%;">Action</th>
									</tr>
									</thead>
									<tbody>
									@php $i = 1;@endphp
									@foreach ($data as $key => $user)
									<tr> 
									@if($user->active==0)
										<td style="width:5%;color:red;" >{{ @$i++ }}</td>
										<td style="width:45%;color:red;"> {{ @$user->ai_code }}</td>
										<td style="width:45%;color:red;"> {{ @$user->college_name }}</td>
										<td style="width:5%;color:red;">{{ @$district_list[$user->district_id] }}</td>
										<td style="width:5%;color:red;">{{ @$block_list[$user->block_id] }}</td>
										
										<td style="width:5%;">{{ @$district_list[$user->temp_district_id] }}</td>
										<td style="width:5%;">{{ @$block_list[$user->temp_block_id] }}</td>
										@if(@$allowonlyotherrole==false)
											<td style="width:10%;color:red;">{{ @$user->principal_name }}</td>
											<td style="width:10%;color:red;" >{{ @$user->nodal_officer_name }}</td>
											<td style="width:10%;color:red;">{{ @$user->ssoid }}</td>
										@endif	

									@else
										<td style="width:5%;" >{{ @$i++ }}</td>
										<td style="width:45%;"> {{ @$user->ai_code }}</td>
										<td style="width:45%;"> {{ @$user->college_name }}</td>
										<td style="width:5%;">{{ @$district_list[$user->district_id] }}</td>
										<td style="width:5%;">{{ @$block_list[$user->block_id] }}</td>
										<td style="width:5%;">{{ @$district_list[$user->temp_district_id] }}</td>
										<td style="width:5%;">{{ @$block_list[$user->temp_block_id] }}</td>
										@if(@$allowonlyotherrole==false)
											<td style="width:10%;">{{ @$user->principal_name }}</td>
											<td style="width:10%;" >{{ @$user->nodal_officer_name }}</td>
											<td style="width:10%;">{{ @$user->ssoid }}</td>
										@endif
									@endif 
									<td>
									 <div class="invoice-action">
									@can('edit_aicenter')
									
									<a href="{{ route('aicenterusers.edit',$user->id) }}" class="invoice-action-edit">
								
									<i class="material-icons tooltipped" data-position="bottom" data-tooltip="Click For Update">edit</i>
									
									</a>
									@endcan
									@can('delete_aicenter_ssoid') 
									@if(!empty($user->ssoid))
									<a href="{{ route('aicenterdelete',$user->id) }}" class="invoice-action-delete delete-confirm">
									<i class="material-icons tooltipped" data-position="bottom" data-tooltip="Are you sure?<br>This record and it`s details will be deleted!">settings_applications</i></a>
									@endif
									@endcan
									</div>
									<div class="invoice-action">
									@can('aicenter_active')
									@if($user->active == 1)
									<a href="{{ route('aicenterusersactive',[$user->id,0]) }}" class="invoice-action-edit lock-confirm">
									<i class="material-icons tooltipped" data-position="bottom" data-tooltip="Click for Inactive">lock</i>
									@elseif($user->active == 0)
									<a href="{{ route('aicenterusersactive',[$user->id,1,]) }}" class="invoice-action-edit lock-confirms">
									<i class="material-icons tooltipped" data-position="bottom" data-tooltip="Click for Active">lock_open</i>
									</a>
									@endif
									@endcan
									@can('export_pdf')
									@if(!empty($user->ssoid))
									<a href="{{ route('letter_twelve_generate_report_pdf',@$user->ai_code)  }}" class="btn btn-lg btn cyan waves-effect waves-light border-round gradient-45deg-purple-deep-orange" style='margin:10px'>
									Generate&nbsp;Pdf</a>
									@endif
									@endcan
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
        text: 'This record and it`s details will be deleted!',
        icon: 'warning',
        buttons: ["Cancel", "Yes"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});
$('.lock-confirm').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: 'AI CENTER WILL BE INACTIVE',
        icon: 'warning',
        buttons: ["Cancel", "Yes"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});
$('.lock-confirms').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: 'AI CENTER WILL BE ACTIVE',
        icon: 'warning',
        buttons: ["Cancel", "Yes"],
    }).then(function(value) {
        if (value) {
            window.location.href = url;
        }
    });
});
$(document).ready(function(){
 // Add Class
 $('.edit').click(function(){
  $(this).addClass('editMode');
 });

 // Save data
 $(".edit").focusout(function(){
  $(this).removeClass("editMode");
  var id = this.id;
  var split_id = id.split("_");
  var value = $(this).text();
  var name = $(this).attr("name");
  $.ajax({
   type: 'post',
   url:"{{ route('livetableupdate') }}",
   headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
   data: { id:id, value:value, name:name },
   success:function(response){
     if(response == 1){
		swal({
		title: 'Record successfully updated',
		icon: 'success',
		 showConfirmButton: false,
		timer: 1200
		})
     }else{
        console.log("Not saved.");
     }
   }
  });
 
 });

});
</script>
<style>
a[title]:hover::after {
  content: attr(title);
  position: absolute;
  top: -100%;
  left: 80px;
  font_size:25px;
}
.edit{
 width: 100%;
 height: 30px;
}
.editMode{
 border: 2px solid black;
}

</style>
@endsection 




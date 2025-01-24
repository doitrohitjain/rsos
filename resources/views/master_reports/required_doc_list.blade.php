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
				<div class="row">
					<div class="col s12">
						<div class="container">
							<div class="seaction">
								<div class="card">
									<div class="card-content">
										<div class="blue-text"> 
										<h6>{{ $title }}
											<a href="{{route('add_doc_req')}}" class="btn btn-xs btn-info right gradient-45deg-deep-purple-blue">Add Document Required</a>
											</h6>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
        <div class="col s12">
            <div class="container"> 
                <div class="section section-data-tables"> 
                    @if(!empty($filters))
                        <div class="row">
                                <div class="col s12">
                                    <div class="card">
                                        <div class="card-content">
                                            @include('elements.filters.search_filter')
                                        </div>
                                    </div>
                                </div>
                            </div>
                       
                    @endif
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
                                                <th>Sr.No</th>
                                                <th>Admission Type</th>
                                                <th>Course</th>
                                                <th>Verification Label</th>
                                                <th>Filed Id</th>
                                                <th>Filed Name</th>
                                                <th>Form Filled table</th>
                                                <th>Form Filled Ref</th>
                                                <th>status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                    <tbody>
                                    @foreach ($verficationmasterdata as $key => $verifications)
                                        <tr>
                                            <td>
                                                {{ $key + 1 }}
                                            </td>
                                            <td>{{@$adm_type[@$verifications->adm_type]}}</td>
                                            <td>{{@$course[@$verifications->course]}}</td>
                                            <td>{{@$verfication_label[@$verifications->main_document_id]}}</td>
                                            <td>{{@$verifications->field_id}}</td>
                                            <td>
                                                {!! @$verifications->field_name !!}
                                            </td>
                                            <td >{{@$verifications->form_filled_tbl}}</td>
                                            <td >{{@$verifications->form_filled_ref}}</td>
                                            <td >{{@$status[@$verifications->status]}}</td>
                                            <td>
                                                <div class="invoice-action"> 
                                                    <a href="{{ route('edit_doc_req',Crypt::encrypt(@$verifications->id)) }}" class="invoice-action-edit" title="Click here to Edit.">
                                                        <i class="material-icons">edit</i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach  
                                    </tfoot>
                                    </table>
                                    {{ $verficationmasterdata->withQueryString()->links('elements.paginater') }}
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
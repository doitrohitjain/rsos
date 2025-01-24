@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="../public/app-assets/images/gallery/breadcrumb-bg.jpg">
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
							   {{ Form::open(array('route' => 'logslisting')) }}
								<table border="0" cellspacing="5" cellpadding="5">
								<tbody>
								<tr class="col s12">
								<td><input type="text" id="table_name" name="table_name"  placeholder="Table Name"></td>
								<td><input type="text" id="data" name="data"  placeholder="ALL Data"></td>
								
								<td class="col-md-3 right">
								<button class="btn btn-primary" type="submit" name="action">Fitter </button>
								@php
								$route = Route::current()->getActionName();
								@endphp
								<a href="{{ action($route) }}" class="btn btn-primary">Reset</a></td>
								 </tr>
								</tbody></table>
							   {!! Form::close() !!}
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
									 <table id="roleTable">
                <thead>
					<tr>
					<th>ID</th>
					<th>Log Date</th>
					<th>Table Name</th>
					<th>All Data</th>
					</tr>
                </thead>
                <tbody>
                 @foreach ($master as $value)
                            <tr>
							<td>
							{{ $value->id }}
							</td>
							<td>{{ $value->log_date }}</td>
							<td>{{ $value->table_name }}</td>
							<td>{{ $value->data }}</td>
					        <td>
							
					      </td>
                         </tr>
                         @endforeach  
                        </tfoot>
                        </table>
			               {{ $master->withQueryString()->links('elements.paginater') }}
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











@extends('layouts.default')
 @section('content')
 <div id="main">
      <div class="row">
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
				</div>
        <div class="col s12">
          <div class="container">
            <div class="section section-data-tables">
  <!-- Page Length Options -->

    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <div class="row">
		<br><br>
		<table id="roleTable">
                <thead>
					<tr>
						<th style="text-align:center">Sr.no</th>
						<th style="text-align:center">Subjects</th>
						<th style="text-align:center">Subject wise Student Count</th>
						<th style="text-align:center">Subject wise supplementary Student Count</th>
					</tr>
                </thead>
                <tbody>
                	@php @$i=1; @endphp 
                @foreach ($final_data as $data)
			
                   <tr>
									   		<td style="text-align:center">{{ @$i }}</td>
											<td style="text-align:center">{{ @$subject_list[$data['subject_id']] }}</td>
											<td style="text-align:center">{{ @$data['studentData'] }}</td>
											<td style="text-align:center">{{ @$data['SuppStudentData'] }}</td>

										</tr>
										 @php @$i++ @endphp
                   @endforeach  
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- END RIGHT SIDEBAR NAV -->

          </div>
          <div class="content-overlay"></div>
        </div>
      </div>
    </div>
@endsection
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/role_details.js') !!}"></script> 
@endsection 
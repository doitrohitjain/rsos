@extends('layouts.default')
@section('content')
	<div id="main">
		<div class="row">
			<div class="col s12">
				<div class="container">
					<div class="seaction">
						<div class="col s12 m12 l12">
							<div class="card"> 
								@include('elements.supplementary_top_navigation')
							</div>
						</div>
						<div class="col s12 m12 l12">
							<div id="responsive-table" class="card card card-default scrollspy">
								<div class="card-content">
									<h4 class="card-fee-title">{{ $page_title; }} </h4>
									{{ Form::open(['route' => [request()->route()->getAction()['as'], $estudent_id], 'id' => $model]) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									<div class="row">
										<div class="col s1"></div>
										<div class="col s12">
											<table class="responsive-table">
												<thead></thead>
												<tbody>
													<tr>
														<td style ="font-size:16px;font-weight: bold;color:black;">@php $fld="Fees Details"; echo "<b>". ucwords(str_replace("_", " ", $fld)) . "</b>";  @endphp</td>
														<td style="font-size:16px;font-weight: bold;color:black;"><b>RS.</b></td>
													</tr>
													<tr>
														<td style ="font-size:16px;font-weight: bold;color:black;">@php $fld="subject_change_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
														<td>{{  @$master[$fld]}}</td>
													</tr>
													<tr>
														<td style ="font-size:16px;font-weight: bold;color:black;">@php $fld="exam_subject_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
														<td>{{  @$master[$fld]}}</td>
													</tr>
													<tr>
														<td style ="font-size:16px;font-weight: bold;color:black;">@php $fld="practical_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
														<td>{{  @$master[$fld]}}</td>
													</tr>
													<tr>
														<td style ="font-size:16px;font-weight: bold;color:black;">@php $fld="late_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
														<td>{{  @$master[$fld]}}</td>
													</tr>
													<tr>
														<td style ="font-size:16px;font-weight: bold;color:black;">@php $fld="forward_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
														<td>{{  @$master[$fld]}}</td>
													</tr>
													<tr>
														<td style ="font-size:16px;font-weight: bold;color:black;">@php $fld="online_services_fees"; echo ucwords(str_replace("_", " ", $fld)); @endphp</td>
														<td>{{  @$master[$fld]}}</td>
													</tr>
												</tbody>
											</table>
										<br>
										<span style ='margin-left:75%;' class="chip lighten-5 red red-text"> 
											TOLAL : &nbsp;{{ @$master[final_fees]}}</span>
										</div>
										<div class="row">
											<div class="col m11 s12 mb-3"><br><br>
												<button class="btn cyan waves-effect waves-light right " type="submit" name="action"> Save & Continue
												</button>
											</div>
										</div>
										{{ Form::close() }}
								</div>  
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.card .card-content .card-fee-title {
    line-height: 32px;
    display: block;
    margin-bottom: 8px;
    font-size: 18px;
    font-weight: bold;
}
</style>
@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/supp_fees_details.js') !!}"></script> 
@endsection





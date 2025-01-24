@extends('layouts.defaultwithguest')
@section('content')
 <div id="login-page">
	<div class="row">
        <div class="col s12">
			<div class="container">
				<div class="section section-data-tables">
					<div class="card">
						<div class="card-content">
							<p class="caption mb-0">
								<h6>
								<span>{{ @$title }}</span>
									
								</h6>
							</p>
						</div>
					</div>
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
									<div class="row ">
										<div class="">
										    <form method="post" action="{{ route('select_aicenter') }}" id='new_term_condition'>
                                                @php $fld='locksumbitted'; @endphp 
                                                @if(empty(@$masterrecord->$fld))
                                                <div class="card-content invoice-print-area">
                                                    <div class="row">
                                                        <div class="col m12 s12">
														@php $lbl='Declaration(घोषणा)'; $fld='locksumbitted'; @endphp
														    <p class="mb-1">
																<label>
																	@if(@$boards)
																		<div class="row"> 
																			<div class="col s12">
																			RSOS Approved Boards(आरएसओएस अनुमोदित बोर्ड)<br><br>
																			</div>
																			<div class="col s12 cardSection">
																				@php  $counter = 1; @endphp
																				@foreach(@$boards as $key => $board)
																					<div class="col s6">
																						{{ $counter++ }}. {{ @$board }}
																					</div>
																				@endforeach 
																			</div>
																			<div class="col s12">
																				<br>
																			</div>
																		</div>
																	@endif 
																</label>
																<label>
																	<br>
																	{{ Form::checkbox($fld, null) }}
																	<span class="cardSectionnot">
																		<p>
																			
																		</p>
																	</span>
																</label>
																<br><br> 
																@include('elements.field_error')
															</p> 
															<p>
															माध्यमिक एवं उच्च माध्यमिक पाठ्यक्रम हेतु अनिवार्य प्रवेश योग्यताएँ
																			<span class="">
																			@php
																				$modelContent = '<center>'. @$newtermconditions[1].'</center>';
																			@endphp
																			<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}" style="font-size:18px;color:black;"><b>Read More</b></span>
																			</span>
															</p>
															<p>
																<center>
																	@if(!empty($egetssoid))
																	<input type="hidden" name="ssoid" value="{{$egetssoid}}"/>
																	@endif
																	<br>
																	<button class="btn cyan waves-effect waves-light " type="submit" name="action"> Accept<i class="material-icons right">send</i>Continue	
																	</button>
																	<a href="{{ route('landing') }}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Cancel</a>
																</center>
															</p>
														</form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div> 
									</div>

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
@endsection 
<style>
	.cardSection{
		overflow-y:scroll;
		max-height: 200px;
	}
</style>
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/new_term_conditions.js') !!}"></script> 
@endsection 
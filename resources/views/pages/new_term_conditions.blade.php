@extends('layouts.defaultwithguest')
@section('content')
<style>
.b{
   color:#080808;
}
.accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}
.active, .accordion:hover {
  background-color: #ccc;
}
.panel {
  padding: 0 18px;
  display: none;
  background-color: white;
  overflow: hidden;
}
</style>
 <div id="login-page" style="width:100%;">
	<div class="row" style="width:100%;">
        <div class="col s12">
			<div class="container">
				<div class="section section-data-tables">
					<div class="card">
						<div class="card-content">
							<p class="caption mb-0">
								<h6>
									<span>Terms & Conditions(नियम एवं शर्तें)</span> 
								</h6>
							</p>
						</div>
					</div>
					<div class="row" style="width:100%;">
						<div class="col s12">
							<div class="card" >
								<div class="card-content2">
									<div class="row ">
										<div class="">
										    <form method="post" action="{{ route('select_aicenter') }}" id='new_term_condition'>
                                                @php $fld='locksumbitted'; @endphp 
                                                @if(empty(@$masterrecord->$fld))
                                                <div class="">
                                                    <div class="row" style="width:100%;">
                                                        <div class="col m12 s12">
														@php $lbl='Declaration(घोषणा)'; $fld='locksumbitted'; @endphp
														    <p class="mb-3">
																<label >
																	@if(@$boards)
																		
																		<div style="width:100%;"> 
																				<div class="col" style="width:100%;">
																			    <div class="accordion" style="width: 97.5%; margin-left: 1.2%;">RSOS Approved Boards(आरएसओएस अनुमोदित बोर्ड) 
																					</div>
																					<div class="panel">
																						<p>
																							@if(@$boards)
																								@php  $counter = 1; @endphp
																								@foreach(@$boards as $key => $board)
																									{{ $counter++ }}. {{ @$board }}</br>
																								@endforeach 
																							@endif 
																						</p>
																					</div>

																				
																				<div class="col" style="width:100%;">
																					<span class="">{!! $newtermconditions[1] !!}</span>
																				</div>
																			<p>
																					<center>
																					@if(!empty($egetssoid))
																					<input type="hidden" name="ssoid" value="{{$egetssoid}}"/>
																					@endif
																					<br>
																					<button class="btn cyan waves-effect waves-light " type="submit" name="action"><i class="material-icons right">send</i>Continue	
																					</button>
																					<a href="{{ route('landing') }}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text ">Cancel</a>
																					</center>
																				</p>
																			</div>
																		</div>
																	@endif 
																</label>
																
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
	<script>
                                                        var acc = document.getElementsByClassName("accordion");
                                                        var i;
                                                        for (i = 0; i < acc.length; i++) {
                                                          acc[i].addEventListener("click", function() {
                                                            this.classList.toggle("active");
                                                            var panel = this.nextElementSibling;
                                                            if (panel.style.display === "block") {
                                                              panel.style.display = "none";
                                                            } else {
                                                              panel.style.display = "block";
                                                            }
                                                          });
                                                        }
                                                        </script>
@endsection 
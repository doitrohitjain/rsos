@extends('layouts.default') 
@section('content') 
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="col s12 m12 l12">
                        <div class="card"> @include('elements.prepare_sessional_top_navigation') </div>
                    </div>
                    <div class="col s12 m12 l12" style="background-color: #4f505245;'">
                        <div id="Form-advance" class="card card card-default scrollspy">
                            <div class="card-content">
                                <h5 class="card-title2">{{ $page_title; }}
									<span>
										<a href="{{ route('prepare_find_enrollment') }}" class="waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content">Search More Enrollment</a>
									</span>	
								</h5>
								
								@php $fld='locksumbitted'; @endphp 
								@if(!empty(@$master->$fld))
									<h4><a class="btn btn-success" style ="margin-left: 80%;" href="{{ route('generate_student_pdf',Crypt::encrypt(@$master->student_id)) }}">Download PDF</a></h4>
								@endif
								<div class="col x25 m12 s12"> @foreach($master as $secotionFld => $values) 
									@php if($values['data'] != null){   @endphp 
									<div class="card">
                                        <div class="card-content invoice-print-area">											
											@php echo $values['seciontLabel'];  @endphp 
											<div class="divider mb-3 mt-3"></div>
                                            <div class="row">
                                                <div class="col m12 s12">
													<table><tr> 
														@php $counter = 0; @endphp 
														@php if($values['data'] != null){ @endphp 
														@foreach(@$values['data'] as $fld => $lbl) 
														@php $showTr = false;
														if($counter%2 === 0){ $showTr = true; } 
														if($showTr){  echo "</tr><tr>"; } 
														@endphp 
														<td width="20%">@php echo $lbl['label']; @endphp </td>
														<td width="5%"> @php echo " : "; @endphp </td>
														<td width="20%"> @php echo $lbl['value']; @endphp </td>
														@php $counter++; @endphp 
														@endforeach 
														@php } @endphp 
														</tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="divider mb-3 mt-3"></div>
                                    </div>
									@php } @endphp 
									
									@endforeach  
								</div>
                            </div>
                            <div class="row"></div>
							<br>
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
	<script src="{!! asset('public/app-assets/js/bladejs/prepare_sessional/marks_preview_details.js') !!}"></script> 
@endsection 

@extends('layouts.default')
@section('content')
<div id="main">
	<div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
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
	</div>
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="seaction">
                    <h6><span style='color:red;'><b>{{$pagetitle}}</b></span></h6>
                    <div class="card">
                        <div class="card-content">
                            <h6><a href="{{ route('printduplicatemarksheetcertificate',Crypt::encrypt(@$exam_result->enrollment)) }}" class="btn btn-xs btn-info right">Download Marksheet And Certificate</a></h6>  
                            <h6>Print Marksheet</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::open(['url'=>url()->current(),'id'=>'finalupdate']) }}
    {!! Form::token() !!}
    {{ method_field('PUT') }}
    <input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
        <div class="section section-data-tables"> 
            <div class="row">
                <div class="col s12">
                    <div class="card">
                        <div class="card-content">
                            <div class="row"> 
                                <table class="responsive-table">
                                <h6>Marksheet Details</h6>
                                    <thead>
                                        <tr>
                                            <th>Enrollment</th>
                                            <th>Subject Code</th>
                                            <th>Final Theory Marks</th>
                                            <th>Final Practical Marks</th>
                                            <th>Sessional Marks</th>
                                           <!-- <th>Sessional Marks<br>Reil Result 20</th>-->
                                            <th>Final Sessional Marks</th>
                                            <th>Total Marks</th>
                                            <th>Final Result</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        @if(!empty(@$data))
                                            @foreach($data as $data)
                                                <tr>
                                                    <td>{{@$data->enrollment}}</td>
                                                    <td>{{@$subjects[@$data->subject_id]}}</td>
                                                    <!-- <td>{{@$data->Subject->name}}</td> -->
                                                    <td>{{@$data->final_theory_marks}}</td>
                                                    <td>{{@$data->final_practical_marks}}</td>
                                                    <td>{{@$data->sessional_marks}}</td>
                                                    <!-- <td>{{@$data->sessional_marks_reil_result_20}}</td> -->
                                                    <td>{{@$data->sessional_marks_reil_result}}</td>
                                                    <td>{{@$data->total_marks}}</td>
                                                    <td>{{@$data->final_result}}</td>
                                                </tr>   
                                            @endforeach 
                                        @else
											<tr>
                                                <td colspan="6">There is no data.</td>
                                            </tr>
										@endif  
                                       <tr>
                                        <td colspan="10"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"></td>
                                            <td><b>Total Marks</b></td>
                                            <td><b>Final Result</b></td> 
                                            <td><b>Percentage</b></td> 
                                        </tr>
                                        @if(!empty($exam_result))
                                        <tr style="font-weight:bolder;color:black;">
                                            <td colspan="5"></td>
                                            <td><b>Previous Result</b></td>
                                            <td><b>{{$exam_result->total_marks}}</b></td>
                                            <td><b>{{$exam_result->final_result}}</b></td>
                                            <td><b>{{$exam_result->percent_marks}}</b></td>
                                            
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="6"></td>
                                            <td  >@php  $fld='total_marks'; @endphp
                                                {!!Form::text($fld,@$exam_result->$fld,['type'=>'text','class'=>'form-control col-sm-2 num','autocomplete'=>'off','size'=>'1','required'=>'true']); !!}
                                                @include('elements.field_error')
                                            </td>
                                            <td>@php  $fld='final_result';
                                            if($exam_result->$fld=='PASS'){
                                                $finalresultdata=1;
                                            }else{
                                                $finalresultdata=0;
                                            }    
                                            @endphp
                                                
                                            {!!Form::select($fld,$finalresults,@$finalresultdata,['type'=>'Select','class'=>'form-control','autocomplete'=>'off','placeholder'=>'Select Final Result','required'=>'true']); !!}
											
                                            <!-- {!!Form::text($fld,@$exam_result->$fld,['type'=>'text','class'=>'form-control col-sm-2 ','autocomplete'=>'off','size'=>'1','required'=>'true']); !!} -->
                                                @include('elements.field_error')
                                            </td>
                                            <td>@php  $fld='percent_marks'; @endphp
                                                {!!Form::text($fld,@$exam_result->$fld,['type'=>'text','class'=>'form-control col-sm-2 ','autocomplete'=>'off','size'=>'1','required'=>'true']); !!}
                                                 @include('elements.field_error')
                                            </td>
                                            
                                        </tr>
                                        @else
                                        <tr>
                                            <td colspan="7"></td>
                                            <td class="text-center"><b>There are no data.</b></td>
                                        </tr>
                                    @endif 
                                    </tbody>
                                </table>
                                
                                </div>
                               
                            </div>
                            <div class="row">
                            <div class="row">
                            <div class="col m2 s10 mb-3 pl-3">
										<button class="btn cyan waves-effect waves-light left gradient-45deg-deep-orange-orange" type="reset">Reset
                                    </button>
										</div>	
                            <div class="col m1 s12 mb-3">
									
                            <button class="btn cyan waves-effect waves-light left show_confirm" style="background: linear-gradient(45deg,#303f9f,#7b1fa2);" type="submit" name="action">Submit
											</button>
										</div>
										
									</div>		
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
    {{ Form::close() }}
</div>				
@endsection
@section('customjs')
    <script src="{!! asset('public/app-assets/js/bladejs/finalupdate.js') !!}"></script> 
@endsection 
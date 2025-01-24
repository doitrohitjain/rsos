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
                <div class="card">
                    <div class="card-content">
                        <h6><a href="{{route('theory')}}" class="btn btn-xs btn-info right">Download Marksheet</a></h6> 
                        <h6>Print Marksheet</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    {{ Form::open(['url'=>url()->current(),'id'=>'form_id']) }}
    {!! Form::token() !!}
    {{ method_field('PUT') }}
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
                                            <th>Subject<br>Code</th>
                                            <th>Final<br>Theory<br>Marks</th>
                                            <th>Final<br>practical<br>Marks</th>
                                            <th>Sessional<br>Marks</th>
                                            <th>Sessional Marks<br>Reil Result 20</th>
                                            <th>Sessional Marks<br>Reil Result</th>
                                            <th>Total Marks</th>
                                            <th>Final Result</th>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        @if(!empty(@$data))
                                            @foreach($data as $data)
                                                <tr>
                                                    <td>{{@$data->enrollment}}</td>
                                                    <td>{{@$data->Subject->name}}</td>
                                                    <td>{{@$data->final_theory_marks}}</td>
                                                    <td>{{@$data->final_practical_marks}}</td>
                                                    <td>{{@$data->sessional_marks}}</td>
                                                    <td>{{@$data->sessional_marks_reil_result_20}}</td>
                                                    <td>{{@$data->sessional_marks_reil_result}}</td>
                                                    <td>{{@$data->total_marks}}</td>
                                                    <td>{{@$data->final_result}}</td>
                                                </tr>   
                                            @endforeach 
                                        @else
											<tr>
                                                <td colspan="6">ther is no data.</td>
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
                                            <td>@php  $fld='final_result'; @endphp
                                                {!!Form::text($fld,@$exam_result->$fld,['type'=>'text','class'=>'form-control col-sm-2 ','autocomplete'=>'off','size'=>'1','required'=>'true']); !!}
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
                                <div class="col m2 s12 mb-3">
                                    <button class="btn cyan waves-effect waves-light left" type="submit" name="action">Save 
                                        <i class="material-icons right"></i>
                                    </button>
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
    <script src="{!! asset('public/app-assets/js/bladejs/update_filter_details.js') !!}"></script> 
@endsection 
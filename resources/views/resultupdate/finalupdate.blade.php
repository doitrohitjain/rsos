@extends('layouts.default')
@section('content')
    <?php
        $addtinal_subjects1=unserialize(@$exam_result->additional_subjects);
        $addtinal_subjects=array();
        if(!empty($addtinal_subjects1)){
        $addtinal_subjects=array_keys(@$addtinal_subjects1);
    }
    ?>
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
                            <h6><a href="{{ route('updateindex',Crypt::encrypt(@$exam_result->enrollment)) }}" class="btn btn-xs btn-info right">BacK</a></h6><h6></h6>  
                            @if(in_array("print_marksheets_permissions",$permissions))
                                <h6><a href="{{ route('printduplicatemarksheetcertificate',Crypt::encrypt(@$exam_result->enrollment)) }}" class="btn btn-xs btn-info right">Download Marksheet And Certificate</a></h6>  
                                <h6>Print Marksheet</h6>
                            @endif   
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
                                    <table class="responsive-table" >
                                        <h6>Marksheet Details</h6>
                                        <thead>
                                            <tr >
                                                <th >Enrollment</th>
                                                <th>Subject Code</th>
                                                <th>Final Theory Marks</th>
                                                <th>Final Practical Marks</th>
                                            <!-- <th>Sessional Marks<br>Reil Result 20</th>-->
                                                <th>Final Sessional Marks</th>
                                                <th>Total Marks</th>
                                                <th>Final Result</th>
                                            </tr>
                                        </thead>
                                        <tbody> 
                                            @if(!empty(@$data))
                                                @foreach($data as $datas)
                                                    <tr>
                                                        <td>{{@$datas->enrollment}}</td>
                                                        <td>{{@$subjects[@$datas->subject_id]}}</td>
                                                        <td>{{@$datas->final_theory_marks}}</td>
                                                        <td>{{@$datas->final_practical_marks}}</td>
                                                        <td>{{@$datas->sessional_marks_reil_result}}</td>
                                                        <td>{{@$datas->total_marks}}</td>
                                                        <td>{{@$datas->final_result}}</td>
                                                    </tr>   
                                                @endforeach 
                                            @else
                                                <tr>
                                                    <td colspan="6">There is no data.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
</br>                               @if(in_array("show_additional_subject",$permissions))
                                    @php
                                    $datacount=count(@$data);
                                    @endphp 
                                    @if($datacount>5)
                                        <table class="responsive-table">
                                        <thead>
                                        <tr><th colspan='1'>Additional Subjects</th></tr>
                                        </thead>
                                            <tbody>
    
                                            <tr >
                                            <td colspan='6'>
                                                @if(!empty(@$data))
                                                    @foreach($data as $datas)
                                                            @if(in_array(@$datas->subject_id,$addtinal_subjects))
                                                            <label>
                                                            &nbsp;
                                                                &nbsp;
                                                                <?php
                                                                    $is_check_class= 'checked="checked"';
                                                                    $subjectsdata=array("$datas->subject_id"=>"final_result");
                                                                ?>
                                                                <input type='checkbox' id="theory_absent_nr_" name='additional_subjects[{{ $datas->subject_id }}]' value="{{@$resultsyntax[$datas->final_result];}}"   <?php echo $is_check_class; ?> />
                                                                <span style='font-size:15px;box-sizing:unset;'>{{@$subjects[@$datas->subject_id]}}</span>
                                                            
                                                            </label'>     &nbsp;
                                                            @else
                                                            &nbsp;
                                                                <label>
                                                                &nbsp;
                                                                &nbsp;
                                                                    <!-- <span style='font-size:15px'>{{@$subjects[@$datas->subject_id]}}</span> -->
                                                                <input type='checkbox'  name='additional_subjects[{{ $datas->subject_id }}] ' value="{{ @$resultsyntax[$datas->final_result]; }}" />
                                                                <span style='font-size:15px;box-sizing: unset;'>{{@$subjects[@$datas->subject_id]}}</span>
                                                                </label>
                                                            @endif 
                                                    @endforeach 
                                                </td>   
                                                </tr> 

                                                @else
                                                    <tr>
                                                        <td colspan="6">There is no data.</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                        <br>
                                    @endif
                                    @endif
                                    <table class="responsive-table" >
                                        <tbody>
                                            <tr>
                                                <td width="70%" colspan="2">&nbsp;</td>
                                                <td width="10%"><b>Total Marks</b></td>
                                                <td width="10%"><b>Final Result</b></td> 
                                                <td width="10%"><b>Percentage</b></td> 
                                            </tr>
                                            @if(!empty($exam_result)) 
                                                <tr style="font-weight:bolder;color:black;">
                                                    <td width="40%">&nbsp;</td>
                                                    <td width="20%" ><b>Previous Result</b></td>
                                                    <td width="10%"><b>{{$exam_result->total_marks}}</b></td>
                                                    <td width="10%"><b>{{$exam_result->final_result}}</b></td>
                                                    <td width="10%"><b>{{$exam_result->percent_marks}}</b></td>
                                                </tr>
                                                @if(in_array("show_final_result",$permissions))
                                                <tr>
                                                    <td width="70%" colspan="2">&nbsp;</td>
                                                    <td width="10%">
                                                        @php  $fld='total_marks'; @endphp
                                                        {!!Form::text($fld,@$exam_result->$fld,['type'=>'text','class'=>'form-control col-sm-2 num','autocomplete'=>'off','size'=>'1','required'=>'true']); !!}
                                                        @include('elements.field_error')
                                                    </td>
                                                    <td width="10%">
                                                        @php  $fld='final_result';
                                                        if($exam_result->$fld=='PASS'){
                                                            $finalresultdata=1;
                                                        }else if($exam_result->$fld=='RWH'){
				                                           $finalresultdata=2;
			                                            }else{
                                                            $finalresultdata=0;
                                                        }    
                                                        @endphp
                                                    
                                                        {!!Form::select($fld,$finalresults,@$finalresultdata,['type'=>'Select','class'=>'form-control','autocomplete'=>'off','placeholder'=>'Select Final Result','required'=>'true']); !!}
                                                        @include('elements.field_error')
                                                    </td>
                                                    <td width="10%">@php  $fld='percent_marks'; @endphp
                                                        {!!Form::text($fld,@$exam_result->$fld,['type'=>'text','class'=>'form-control col-sm-2 ','autocomplete'=>'off','size'=>'1','required'=>'true']); !!}
                                                        @include('elements.field_error')
                                                    </td>
                                                
                                                </tr>
                                                @endif
                                                
                                            @else
                                                <tr>
                                                    <td class="text-center" colspan="20"><b>There are no data.</b></td>
                                                </tr>
                                            @endif 

                                            
                                        </tbody>
                                    </table>
									@if(!empty($exam_result)) 
										
                                    <table>
									<tr style="font-weight:bolder;color:black;">
										<td width="30%" ><b>Remarks</b></td>
										<td width="70%">
											@php  $fld='remarks'; @endphp
											{!! Form::textarea($fld,@$exam_result->$fld,['class'=>'form-control', 'rows' => 10, 'cols' => 40]) !!}
											<!-- {!!Form::text($fld,@$exam_result->$fld,['type'=>'textarea','class'=>'form-control  num','autocomplete'=>'off','required'=>'true']); !!} -->
											@include('elements.field_error')
										</td>
										</tr>
                                    </table>
									@endif 

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
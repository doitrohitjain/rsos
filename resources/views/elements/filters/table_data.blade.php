@php use App\Helper\CustomHelper; 
	$role_id = Session::get('role_id');
 @endphp
<div > 
    <table class="responsive-table"> 
        <thead>
            <tr>
				@php
					$isCustomFieldWidth = true;
					if($isCustomFieldWidth){
						$width = "20%";
					}
				@endphp
				
                @foreach($tableData as $key => $tableTh)  
                    <th class="" style="white-space: nowrap;width: 1%;">{{ $tableTh['lbl'] }}</th> 
                @endforeach 
            </tr>
        </thead>
        @if(count($master) > 0)
            @php $counter = 0; @endphp
            @foreach ($master as $key => $value)
                {{-- @dd($tableData); --}}
				<tbody>
                    <tr> 
						@foreach($tableData as $key => $tableTh)
							@php 
                                $fld=$tableTh['fld'];
                            @endphp
							@if($fld != 'action')
							<td class="word-break2"> 
								@php 
                                    if(@$tableTh['report_type'] == 'sessional' && @$tableTh['vertical_type'] == true) {
                                        $exam_subjects = explode(",",$value['subjects']);
                                        $exam_subjects_marks = explode(",",$value['subject_marks']);
                                        $subjectDetails = "-";
                                        if(@$tableTh['options'][@$exam_subjects[@$tableTh['subject_key']]]){
                                            $subjectDetails = @$tableTh['options'][@$exam_subjects[@$tableTh['subject_key']]]. "(" . @$exam_subjects_marks[@$tableTh['subject_key']] . ")";
                                        }
                                        echo $subjectDetails;
                                    }else if($fld == 'srno'){
                                       echo $master->firstItem() + $counter;
                                    }else{
                                       
                                        $textLabel = null;
                                        if(@$tableTh['fld_url']){
                                            //$tableTh['fld_url'] = strrchr( $tableTh['fld_url'], '?');
                                            $tempUrlFinal = $tempUrl  = explode("#", $tableTh['fld_url']);
											
                                            foreach($tempUrl as $k =>  $v){
												
                                                if($k >0 && $k%2 != 0){ 
                                                    if($v == 'id' || $v == 'enrollment' || $v == 'student_id'){
														$tempUrlFinal[$k] =  Crypt::encrypt($value[$v]);
                                                    }else{
                                                        $tempUrlFinal[$k] =  $value[$v];
                                                    }
                                                }
                                            }
                                            $tempUrlFinal = implode("",$tempUrlFinal); 
											
											if(@$tableTh['notExtraCls']){
												
												$textLabel .=  "<a href='" . $tempUrlFinal . "' class=''>";
												
											}else{
												$textLabel .=  "<a href='" . $tempUrlFinal . "' class='btn  btn-success'>";
											}
											
                                        }
										
										// dd($tableTh['input_type']);
										if(@$tableTh['input_type'] == 'select'){
                                            // dd($tableTh['options']);
											@$color=null;
                                            if(@$tableTh['options'][$value[$fld]]){
												if(@$tableTh['options'][$value[$fld]]=='Rejected'){
												$color="color:red";
												}elseif(@$tableTh['options'][$value[$fld]]=='Approved'){
													$color="color:green";
												}
                                                $textLabel .=  "<span style=".$color.">".$tableTh['options'][$value[$fld]]."</span>";
												
												
                                            }else{
                                                $textLabel .= "N/A";
                                            }
                                           }elseif(@$tableTh['input_type'] == 'date'){
											$textLabel .=  date('d-m-Y h:i A', strtotime(@$value[$fld]));;
										   }else{ 
                                            
                                            if(@$value[$fld]){
                                                $textLabel .=  $value[$fld];
												
                                            }else if($value[$fld] == 0){
                                                $textLabel .=  $value[$fld];
                                            } 
                                        }

										if(@$tableTh['fld_url']){
											
                                            $textLabel .= "</a>";
                                        }
                                        
                                        echo $textLabel;
                                    } 
                                @endphp  
                            </td> 
							@endif
                        @endforeach

						@if(@$actions)
								 
                            <td>
								@foreach($actions as $key => $action)
                                    @php
										if(@$action['extraCondition'] && $action['extraCondition'] == "student_applications"){
											$stream =$value->stream;
											$gender_id =$value->gender_id;
											$student_id = $value->id;
											$isContinue = false;
											if($role_id == 59){
												$isDateOpenOrClosed = CustomHelper::_checkPaymentAllowedOrNotHelper($stream,$gender_id);
												$isDateOpenOrClosed = json_decode($isDateOpenOrClosed,true);
												$isLocked = CustomHelper::_CheckStudentFormLockAndSubmittHelper($student_id);
												
												if($isDateOpenOrClosed['status'] == false){
													if(!$isLocked){
														$isContinue = true;
													}
												} 
												if($isContinue){
													continue;
												}
											}
											
											
										}
                                        $textLabel = null;
										if(@$action['fld_url']){
                                            //$tableTh['fld_url'] = strrchr( $tableTh['fld_url'], '?');
                                            $tempUrlFinal = $tempUrl  = explode("#", $action['fld_url']); 
											foreach($tempUrl as $k =>  $v){
                                                if($k >0 && $k%2 != 0){ 
                                                    if($v == 'id' || $v == 'enrollment' || $v == 'student_id'){
														$tempUrlFinal[$k] =  Crypt::encrypt($value[$v]);
                                                    }else{
                                                        $tempUrlFinal[$k] =  $value[$v];
                                                    }
                                                }
                                            }
                                            $class = null;
                                            if(@$action['class']){
                                               $class=@$action['class'];
                                            }  
                                            $tempUrlFinal = implode("",$tempUrlFinal);
                                            // echo $tempUrlFinal;
                                            $textLabel .=  "<a href='" . $tempUrlFinal . "' class='" . $class . "'>";
                                                 
                                        } 
										$textLabel .=  @$action['icon']; 

                                        if(@$action['fld_url']){
                                            $textLabel .= "</a>";
                                        }
                                        $statusIsExamCenter = true;
                                        $school_id = $value['id'];
                                        if(@$action['use_helper'] && $action['use_helper'] == "checkAlredaySchoolMapForExamCenter" ){
                                            $statusIsExamCenter = CustomHelper::checkAlredaySchoolMapForExamCenter($school_id);
                                            if(@$statusIsExamCenter > 0){
                                                echo '<span class="btn cyan waves-effect waves-light border-round gradient-45deg-green-teal">Already!</span>';
                                            }else{
                                                echo $textLabel;
                                            }
                                        }else{
                                            echo $textLabel;
                                        }
                                        
                                    @endphp 
                                @endforeach 
                            </td>
                        @endif 
					</tr>
                </tbody>  
                @php $counter++; @endphp 
            @endforeach
		@else 
			<tbody><tr><td colspan="20" class="center text-red">Data Not Found</td></tr></tbody>
		@endif
	</table>
    {{ $master->withQueryString()->links('elements.paginater') }}
</div>

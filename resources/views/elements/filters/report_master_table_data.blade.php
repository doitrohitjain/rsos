@php use App\Helper\CustomHelper;  @endphp
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
				<tbody>
                    <tr>
						
						@foreach($tableData as $key => $tableTh)
							@php 
                                $fld=$tableTh['fld'];
                            @endphp
							@if($fld != 'action')
							<td class="word-break2"> 
								@php 
                                    if(@$tableTh['customCheck']){
                                        $allowedRoles = null;
                                        if($fld == 'role_id'){
                                            foreach(@$value->rolehaspermission as $k => $p){
                                                $allowedRoles[] = @$roles[$p->role_id];
                                            }
                                        }
                                        if(!empty($allowedRoles)){
                                            $strOrg = $str = implode(",",$allowedRoles);
                                            if(strlen($str) > 20){
                                                $str = substr($str, 0, 20) . " ...";
                                            }
                                            echo "<span title='$strOrg'>$str</span>";
                                        }
                                        
                                    }else{
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
                                                        $tempUrlFinal[$k] =  $value[$v];
                                                    }
                                                }
                                                $tempUrlFinal = implode("",$tempUrlFinal);  
                                                $textLabel .=  "<a href='" . $tempUrlFinal . "' class='btn  btn-success'>";
                                            }
                                            
                                            
                                            if(@$tableTh['input_type'] == 'select'){
                                                if(@$tableTh['options'][$value[$fld]]){
                                                    $textLabel .=  $tableTh['options'][$value[$fld]];
                                                }
                                            }else{ 
                                                if(@$value[$fld]){
                                                    $textLabel .=  @$value[$fld];
                                                }else if(@$value[$fld] && $value[$fld] == 0){
                                                    $textLabel .=  @$value[$fld];
                                                } 
                                            }

                                            if(@$tableTh['fld_url']){
                                                $textLabel .= "</a>";
                                            }
                                            
                                            $strOrg = $str = $textLabel;
                                            if(strlen($str) > 20){
                                                $str = substr($str, 0, 20) . " ...";
                                            } 
                                            echo "<span title='$strOrg'>$str</span>";
                                        } 
                                    }
                                @endphp  
                            </td> 
							@endif
                        @endforeach

						@if(@$actions)
                            <td>
								@foreach($actions as $key => $action)
                                    @php  
                                        $textLabel = null;
                                        if(@$action['customCheck'] && @$action['fld_url']){
                                            $fld = "is_sql"; 
                                            if(@$value[$fld] == 1){
                                                $fld = "id";
                                                $class = "";
                                                $tempUrlFinal = Crypt::encrypt($value[$fld]);
                                                echo $textLabel =  "<a target='_blank' href='export/" . $tempUrlFinal . "' class='" . $class . "'>";
                                                @endphp 
                                                <img title="Click here to download excel." height="35%" width="35%" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAACcklEQVR4nGNgGAWjYBSMgmEJDOsNpfRqDX11aw0bdGsNN+vWGj5jGIzAvt6eRb9GX1uvxiBOt8Zwol6t0W69WsM3erVG/9HxQLuVQb3Uilev2thGt9YoDeRY3RrDI3o1ht+xOXbAPaBboSsIdmyNUb5ujdEi3Rqjq3o1Rn+JdeyAe0CPAofiwj7rA8jC3uv9jwxpD/isDyA99kY9UDsaA/8HXRJCBjCH4QOjHtBDCr3Obd0ooVO+qgpF3q7d6f+XH1/g8j07+gZXDBjWm/6/9eI23PC7r+7+N6gzgcsvPb4cLnfn5Z3/Rg1mg68YTZmfjhJCuUsKwOIevT7/f/7+CRb79+/f/+R5aYO3Hth7bT/cA5ceXwaLbbmwFS62+cKWwZ2Jvfp8//+AhjYIlKws///3318w+9P3T/8dO10Gtwf0ao3+zzowB27Jx28f4ez2LZ1Doxg1b7b+//LjSxTLrj+78d+gHpGpB20e0Ks1+m/VaofhgWfvn/03a7IaGh5YfXot3OGvP7+Bs2funz34k1Di3BRwUQkC3399/5+3tBBuIago9Z0QOHg9YNJo8f/uq3twC+YcnAcWP3r7GFzs2J0Tg9cDMw/MRil9bNscwOKhUyPgRSkIlK6sGHx5IGhy6P9ff37hbOvsvLwLJV+AMvqg8YBBncn/8w8vwB0IKoFMmyxRHTch4P+fv3/gahYfWzr4kpAehXjUAwMdA4OmItMb9UDAaAwMqSTkvd7/MMOIG9zFNbyuW2tkDJsLGNTD6+RMcOjVGnWAZmMG7QQHCYBRq95QRbfOOFS31rBNr8Zom16N4XNSDBgFo2AUjAKGIQEAkqNB3aFhJ4wAAAAASUVORK5CYII=" >
                                                @php
                                                echo "</a>";
                                            }else if(@$value[$fld] == 2){
                                                $fld = "url";
                                                $class = "";
                                                $tempUrlFinal = $value[$fld];
												$url = route('landing') . "/" . $tempUrlFinal; 
                                                echo $textLabel =  "<a target='_blank' href='" . $url . "' class='" . $class . "'><i class='material-icons' title='click here to open in a link.'>open_in_new</i></a>";
                                            } 
                                        }else if(@$action['fld_url']){
                                            //$tableTh['fld_url'] = strrchr( $tableTh['fld_url'], '?');
                                            $tempUrlFinal = $tempUrl  = explode("#", $action['fld_url']); 
                                            foreach($tempUrl as $k =>  $v){
                                                if($k >0 && $k%2 != 0){ 
                                                    if($v == 'id' || $v == 'enrollment' || $v == 'student_id'){
                                                        $tempUrlFinal[$k] =  Crypt::encrypt(@$value[$v]);
                                                    }else{
                                                        $tempUrlFinal[$k] =  $value[$v];
                                                    }
                                                }
                                            }
                                            $class = null;
                                            if(@$action['class']){
                                                $class=@$action['class'];
                                            }  

                                            $school_id = Crypt::encrypt(@$value['id']);
                                            
                                            $tempUrlFinal = implode("",$tempUrlFinal); 

                                            if(@$action['fld'] && $action['fld'] == 'delete'){
                                                $class .= ' deleteItem ';
                                                $tempUrlFinal = "javascript:void(0);";
                                            }  
                                            
                                            
                                            $textLabel .=  "<a href='" . $tempUrlFinal . "' data-id='" . $school_id . "'class='" . $class . "'>";
                                            $textLabel .=  @$action['icon']; 

                                            if(@$action['fld_url']){
                                                $textLabel .= "</a>";
                                            }
                                            $statusIsExamCenter = true;
                                           
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


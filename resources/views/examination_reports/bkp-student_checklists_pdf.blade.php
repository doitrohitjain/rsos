<div id="main2">
    <div class="row2">
        <div class="col2 22s12">
            <div class="container2">
                <div class="">
                    <table id="checklist" style="width:100%;margin-top:-1%;">
                        <tbody style="font-size:14px;">
                            <tr style="border-bottom:2px solid black;">
                                <td class="tdcls" colspan="2">
                                    <div><b>RAJASTHAN STATE OPEN SCHOOL,JAIPUR</b></div>
                                </td>		
                                <td class="tdcls" colspan="3">
                                    <div><b>Registration Checklist(1st)[Total : <?= count($master); ?>]</b></div>
                                </td>		
                                <td class="tdcls" colspan="1" >
                                    <div><b><?php echo $ai_code; ?>/<?php echo $course; ?>th</b></div>
                                </td>		
                            </tr>
                            <tr style="border-bottom:2px solid black;margin-bottom:2px;">
                                <td class="tdcls">
                                    <div>S.No.</div>
                                    <div>ENR. No</div>
                                    <div>App. Type</div>
                                    <div>TOC Board/Year</div>
                                    <div>Roll No.</div>			
                                </td>
                                <td class="tdcls2">
                                    <div>Name</div>
                                    <div>Father's Name</div>
                                    <div>Mother's Name</div>			
                                </td>
                                <td class="tdcls">
                                    <div>DOB/Age</div>
                                    <div>Gender/Category</div>
                                    <div>Medium/Course</div>
                                </td>
                                <td class="tdcls2">
                                    <div>Subject / Add. Subjects</div>
                                    <div>Exam Subjects</div>
                                    <div>Board/Year Pass  
                                        @if($course == 10)
                                            @php echo 'X'; @endphp 
                                        @else
                                        @php  echo 'XII'; @endphp 
                                        @endif
                                    </div>
                                    <div>Fee [AI/RSOS/Difference] </div>
                                    <div>Error Parameters</div>		
                                </td>
                                <td class="tdcls">		
                                    <div>Signature</div>			
                                </td>
                                <td class="tdcls" style="text-align: center;">
                                    <div>Photograph</div>			
                                </td>
                            </tr>
                            
                            <?php if(@$master){ $counter = 0;?>
                                <?php foreach($master as $key => $student){
                                    // dd($student->id);	
                                    $student_id =$student->id; 
                                    $counter++;
                                ?>
                                    <tr>
                                        <td class="tdcls">
                                            <div><?= $counter; ?></div>
                                            <div>
                                                @php 
                                                    echo $student->enrollment; 
                                                @endphp
                                            </div>
                                            <!--<div>&nbsp;</div>-->
                                            <div><?php 
                                                    echo (@$boards[$student->application->board])?$boards[$student->application->board]:''; 								
                                                    if(@$student->toc->year_fail && $student->toc->year_fail > 0){
                                                        echo '/'.$rsos_years[$student->toc->year_fail]; 
                                                    }

                                                    if(@$student->toc->year_pass && $student->toc->year_pass > 0){
                                                        echo '/'.$rsos_years[$student->toc->year_pass]; 
                                                    }  	
                                                ?>
                                            </div>
                                            <div><?php
                                            
                                            echo @$student->toc->roll_no;?></div>
                                            <!--<div>&nbsp;</div>-->
                                        </td>
                                        <td class="tdcls2">
                                            <div><?php echo $student->name; ?></div>
                                            <div><?php echo $student->father_name; ?></div>
                                            <div><?php echo $student->mother_name; ?></div>
                                            <?php 
                                            if(isset($student->application->toc) && $student->application->toc == 1){
                                                    
                                                    foreach($student['tocmarks'] as $tmkey=>$tmval){							
                                                    echo "<div>";	
                                                    if(isset($subjects[$tmval['TocMark']['subject']] )){
                                                        echo $subjects[$tmval['TocMark']['subject']];
                                                    
                                                ?>
                                                
                                                <?php echo '/';?>
                                                <?php echo $tmval['TocMark']['theory']; ?>
                                                <?php echo '/';?>
                                                <?php echo $tmval['TocMark']['practical']; ?>
                                                <?php echo '/';?>
                                                <?php echo $tmval['TocMark']['total_marks']; ?>
                                                <?php } 
                                                    echo "</div>";
                                                    }
                                                }?>
                                        
                                        </td>
                                        <td class="tdcls">
                                            <div>
                                                <?php 
                                                    echo $student['Application']['dob']; 
                                                ?>
                                            </div>
                                            <div>
                                                <?php 
                                                    echo isset($genders[$student['Application']['gender_id']])?$genders[$student['Application']['gender_id']]:''; 
                                                    echo '/'; 
                                                    echo isset($category[$student['Application']['category_a']])?$category[$student['Application']['category_a']]:''; 
                                                ?>
                                            </div>
                                            <div><?php 
                                                    echo isset($midiums[$student['Application']['midium']])?$midiums[$student['Application']['midium']]:''; 
                                                    echo '/'; 
                                                    echo $student['Application']['course'];
                                                ?>
                                            </div>
                                            <?php 
                                            /*if(isset($student['Application']['toc']) && $student['Application']['toc'] == 1){
                                                    
                                                    foreach($student['tocmarks'] as $tmkey=>$tmval){
                                                    if($tmkey < 3)
                                                        continue;
                                                    echo "<div>";	
                                                    if(isset($subjectNames[$tmval['TocMark']['subject']] )){
                                                        echo $subjectNames[$tmval['TocMark']['subject']];
                                                    
                                                ?>
                                                
                                                <?php echo '/';?>
                                                <?php echo $tmval['TocMark']['theory']; ?>
                                                <?php echo '/';?>
                                                <?php echo $tmval['TocMark']['practical']; ?>
                                                <?php echo '/';?>
                                                <?php echo $tmval['TocMark']['total_marks']; ?>
                                                <?php } 
                                                    echo "</div>";
                                                    }
                                                }*/?>
                                            
                                        </td>
                                        <td class="tdcls2">
                                            <div>
                                                <?php
                                                    $subModel = $student['AdmSubjectType'];
                                                    if(isset($student[$subModel][$subModel]) && !empty($student[$subModel][$subModel])){														
                                                        for($count = 1 ; $count <= 7;$count++){
                                                            $tempSub = 'subject'.$count;
                                                            if(isset($student[$subModel][$subModel][$tempSub]) && isset($subjects[$student[$subModel][$subModel][$tempSub]])){
                                                                echo $subjects[$student[$subModel][$subModel][$tempSub]] . ' ';											
                                                            }
                                                        }
                                                    }else{
                                                        echo 'No';
                                                    }
                                                ?>
                                            </div>
                                            <div>
                                            <?php
                                            
                                            if(isset($student['ExamSubject']) && count($student['ExamSubject']) > 0){
                                                foreach($student['ExamSubject'] as $eskey=>$esval){
                                                    echo (isset($subjects[$esval])) ? $subjects[$esval]." " : "";
                                                }
                                            }?>
                                            </div>
                                            <div>
                                                <?php 
                                                if($course == 12){
                                                    /*echo (isset($boards[$student['Toc']['board']]) && !empty($student['Toc']['board']))?$boards[$student['Toc']['board']]:''; 								
                                                    if( isset($boards[$student['Toc']['board']]) && isset($rsos_years[$student['Application']['year_pass']])){
                                                        echo '/'; 
                                                    }*/
                                                    echo (isset($rsos_years[$student['Application']['year_pass']])) ? $rsos_years[$student['Application']['year_pass']]:'';
                                                }
                                                ?>
                                            </div>
                                            <div>
                                                <?php
                                                    $subFeeModel = $student['FeeModelType'];
                                                    if(isset($student[$subFeeModel][$subFeeModel]) && !empty($student[$subFeeModel][$subFeeModel])){
                                                        if(isset($student['Student']['adm_type']) && $student['Student']['adm_type'] == 2)
                                                        {
                                                            echo $student[$subFeeModel][$subFeeModel]['total_fees'];
                                                        }else{
                                                            echo $student[$subFeeModel][$subFeeModel]['total'];
                                                        }
                                                        //. '/'.($student[$subFeeModel][$subFeeModel]['total']-50)."/".'50';
                                                        
                                                    }
                                                ?>
                                            </div>					
                                            <!--<div>&nbsp;</div>
                                            <div>&nbsp;</div>-->
                                        </td> 
                                        <td class="tdcls" style="text-align: center;">
                                            <div> 
                                                <img alt="100%x200" width="70" src="<?php echo url('public/'.$studentDocumentPath .'/' . $student_id .'/' . @$student->document->signature ) ;?>" class="img-rounded"  style="max-height:70px;"/>
                                            </div>  
                                            <!--<div>&nbsp;</div>-->
                                        </td>
                                        <td class="tdcls" style="text-align: center;">
                                            <div> 
                                                <img alt="100%x200" src="<?php echo url('public/'.$studentDocumentPath  .'/' . $student_id . '/' . @$student->document->photograph ) ;?>" width="100" class="img-rounded"  style="max-height:70px;">
                                            </div>  
                                            <!--<div>&nbsp;</div>-->
                                        </td>
                                    </tr>
                                    <tr  style="border-bottom:2px solid black;"><td colspan=5><div style="width:100%;"><b>Address: </b>
                                                
                                                <?php 
                                                    if(isset($student['Address']['address1']) && !empty($student['Address']['address1'])){
                                                        echo $student['Address']['address1'] . ',';
                                                    }
                                                    if(isset($student['Address']['address2']) && !empty($student['Address']['address2'])){
                                                        echo $student['Address']['address2'] . ',';
                                                    }
                                                    if(isset($student['Address']['address3']) && !empty($student['Address']['address3'])){
                                                        echo $student['Address']['address3'] . ',';
                                                    }
                                                    if(isset($student['Address']['tehsil_name']) && !empty($student['Address']['tehsil_name'])){
                                                        echo $student['Address']['tehsil_name'] . ',';
                                                    }
                                                    if(isset($student['Address']['city_name']) && !empty($student['Address']['city_name'])){
                                                        echo $student['Address']['city_name'] . ',';
                                                    }
                                                    if(isset($student['Address']['district_name']) && !empty($student['Address']['district_name'])){
                                                        echo $student['Address']['district_name'] . ',';
                                                    }
                                                    if(isset($student['Address']['state_name']) && !empty($student['Address']['state_name'])){
                                                        echo $student['Address']['state_name'] . '-';
                                                    }
                                                    if(isset($student['Address']['pincode']) && !empty($student['Address']['pincode'])){
                                                        echo $student['Address']['pincode'] . '';
                                                    }								
                                                ?>
                                            </div></td></tr>
                                <?php } ?>
                            <?php } ?>
                            </tbody>

                    </table>

                    <style> 
                        td{
                            padding-top:3px;
                            padding-bottom:3px;
                            vertical-align:top;
                        }
                        div{
                            padding-top:3px;
                            padding-bottom:3px;
                            vertical-align:top;
                        }
                        .tdcls{
                            width:18%;
                            text-align:left;
                        }
                        .tdcls{
                            width:18%;
                            text-align:left;
                        }
                        .tdcls2{
                            width:22%;
                            text-align:left;
                        }
                            
                    </style>
                </div>
            </div>
        </div>
    </div>
</div>
</div>  
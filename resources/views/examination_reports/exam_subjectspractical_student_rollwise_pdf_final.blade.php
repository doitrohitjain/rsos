<style>
	.vertical { 
		border-left: 1px solid black; 
		height: 899999450px; 
		position:absolute; 
		left: 50%; 
	} 
	.valign{
		border-left: 1px dashed black;
		height: 875px;
	}
	.head{
		vertical-align:top;
	}
	
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th 
	{
		border-top:0px solid lightgray;
		padding:5px;

	}
	.table {  
		margin-bottom: 0px;
	}
	
	.new-page {
		page-break-after: always;
	}

	.tablebordered {
  border: 1px solid lightgray;
  border-collapse: collapse;
}

.font{
 font-family: Arial, sans-serif;
 }

</style> 

@php $finalData = $dataSaveItem; @endphp
@foreach($finalData as $sk => $subjectscount)

@php $srCounterFirst=0;@endphp
@php $srCounterSecond=0;@endphp
@foreach(@$subjectscount as $students) 

<table class="table" width="98%" style="margin-top:-1%">
			<tr>
				<td width="50%"> 
								<table class="table font" width="100%"  >
									<tr>
										<td colspan="2" style="text-align:left;margin-bottom:-25px;">
											<b>RSOS Copy</b>
										</td>
									</tr>
									<tr>
										<td style="width:2%;text-align:center;">
											<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 60px;  border-radius: 10px"/>
										</td> 
										<td style="width:98%;text-align: center; font-size:14;">
											<p><b>RAJASTHAN STATE OPEN SCHOOL</br>
									
									CENTERWISE SIGNATURE ATTENDANCE ROLL</br>
									Examination(<?php if (isset($exam_session[$stream])) {echo $exam_session[$stream];
           } ?></b>)</p>
										</td> 
									</tr>
								</table>
								<table class="table  font" width="100%" style="margin-top:-10px;" > 
									<tr>
										<td colspan=2>
											<p style="font-size:14px;">Center code: {{ @$masterData['cent_code']}}</p>
										</td>
										<td colspan=2>
											<p style="font-size:14px;">Center Name: {{ @$masterData['cent_name']}}</p>
										</td> 
									</tr>
										 
									<tr>
										<td colspan="6" style="margin-top:-15px;">
											<p style="margin-top:-14px">Subject: 
												{{@$subjectList[$sk]}} [Practical] 
											</p>
										</td> 
									</tr> 
								</table>
								
								<table class="table table-responsive tablebordered font">
										<tr>
										<th class="tablebordered" width="5%" style="font-size:12px;">Sr.No</th>
										<th class="tablebordered" width="15%" style="font-size:12px;">Roll No.</th>
										<th class="tablebordered"width="35%" style="font-size:12px;">Name</th>
										<th class="tablebordered" width="20%" style="font-size:12px;">Answer Book S.No.</th>
										<th class="tablebordered" width="15%" style="font-size:12px;">Signature</th>
									</tr>
										
									@php
									$lsno=1; $countStudents = count($students); @endphp
									@foreach(@$students as $student) 
									@php $srCounterFirst++;@endphp
											
				
											<tr>

												<td class="tablebordered" width="5%" style="font-size:12px;">{{$srCounterFirst}}</td> 
												<td class="tablebordered" width="15%" style="font-size:12px;">
													{{@$student['enrollment']}}
												</td>
												<td  class="tablebordered" width="35%" style="font-size:12px; text-transform: uppercase;">
													{{@$student['name']}}
												</td> 				
												<td  class="tablebordered" width="20%" style="font-size:12px;vertical-align: bottom">....................</td>
												<td class="tablebordered"  width="15%" style="font-size:12px;vertical-align: bottom">....................</td>
												
											</tr>
									
									@endforeach 
								
								</table>
								<table  class="table table-responsive tablebordered sign head font"  >
							
									<tr>
									<td class="tablebordered" width="49%" style="font-size:12px;">Important Note: ROLL NO. OF ABSENTEES SHOULD BE CLEARLY  ENCIRCLED WITH RED INK.</td>
									</tr>
									<tr>
									<td  class="tablebordered"width="49%" style="font-size:12px;">TOTAL NUMBER OF CANDIDATE:&nbsp;{{ $countStudents; }}</td>
									</tr>
									<tr>
									<td class="tablebordered" width="49%" style="font-size:12px;">TOTAL NUMBER OF CANDIDATE(PRESENT):________________</td>
									</tr>
									<tr>
									<td class="tablebordered" width="49%" style="font-size:12px;">TOTAL NUMBER OF CANDIDATE(ABSENT):_________________</td>
									</tr>
									<tr>
									<td class="tablebordered" width="49%" style="font-size:12px;">NUMBER OF UFM CASES:___________SIGNATURE __________</td>
									</tr>
									<tr>
									<td class="tablebordered" width="49%" style="font-size:12px;">NAME OF INVIGILATOR:__________SIGNATURE ____________</td>
									</tr>
									<tr>
									<td class="tablebordered" width="49%" style="font-size:12px;">NAME OF SUPERVISOR:___________SIGNATURE ____________</td>
									</tr>
									<tr>
									<td class="tablebordered" width="49%" style="font-size:12px;">NAME OF CENTRE SUPTD.:_________SIGNATURE & SEAL ________</td>
									</tr>
								</table> 
									<div style = "display:block; clear:both; page-break-after:always;"></div>
				</td>
			
				<td width="2%">
					<!-- <div class="valign">&nbsp;</div> -->
					<div class = "vertical"></div> 
					
				</td>
				
				<td width="50%">
					  
					<table class="table head font" width="100%"  >
									<tr>
										<td colspan="2" style="text-align:right;margin-bottom:-25px;">
											<b>Center Copy</b>
										</td>
									</tr>
									<tr>
										<td style="width:2%;text-align:center;">
											<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 60px;  border-radius: 10px">
										</td> 
									<td style="width:98%;text-align: center; font-size:14;">
											<p><b>RAJASTHAN STATE OPEN SCHOOL</br>
									
									CENTERWISE SIGNATURE ATTENDANCE ROLL</br>
									Examination(<?php if (isset($exam_session[$stream])) {echo $exam_session[$stream];
           } ?></b>)</p>
										</td> 
									</tr>
								</table>
								<table class="table head font" width="100%" style="margin-top:-10px;" > 
									<tr>
										<td colspan=2>
											<p style="font-size:14px;">Center code: {{ @$masterData['cent_code']}}</p>
										</td>
										<td colspan=2>
											<p style="font-size:14px;">Center Name: {{ @$masterData['cent_name']}}</p>
										</td>

									</tr>
										 
									<tr>
										<td colspan="6" style="margin-top:-15px;">
											<p style="margin-top:-14px">Subject: 
												{{-- {{ $subjectList[$subjectscount['subject_id']]}} [Practical]  --}}
												{{@$subjectList[$sk]}} [Practical] 
											</p>
										</td> 
									</tr> 
								</table>
								
								<table class="table table-responsive  tablebordered font">
										<tr>
										<th class="tablebordered" width="5%" style="font-size:12px;">Sr.No</th>
										<th class="tablebordered" width="15%" style="font-size:12px;">Roll No.</th>
										<th class="tablebordered"width="35%" style="font-size:12px;">Name</th>
										<th class="tablebordered" width="20%" style="font-size:12px;">Answer Book S.No.</th>
										<th class="tablebordered" width="15%" style="font-size:12px;">Signature</th>
									</tr>
										
										
									@php
									$lsno=1; $countStudents = count($students); @endphp
									@foreach(@$students as $student) 
									@php $srCounterSecond++;@endphp	
										<tr>
												<td class="tablebordered" width="5%" style="font-size:12px;">{{$srCounterSecond}}</td> 
												<td class="tablebordered" width="15%" style="font-size:12px;">
													{{@$student['enrollment']}}
												</td>
												<td  class="tablebordered" width="35%" style="font-size:12px; text-transform: uppercase;">
													{{@$student['name']}}
												</td> 				
												<td  class="tablebordered" width="20%" style="font-size:12px;vertical-align: bottom">....................</td>
												<td class="tablebordered"  width="15%" style="font-size:12px;vertical-align: bottom">....................</td>
												
											</tr>
                                
										@endforeach 
						      
								</table>
								
							<table  class="table table-responsive table-bordered sign head tablebordered font" s >
						
								<tr>
								<td class="tablebordered" width="49%" style="font-size:12px;">Important Note: ROLL NO. OF ABSENTEES SHOULD BE CLEARLY  ENCIRCLED WITH RED INK.</td>
								</tr>
								<tr>
								<td  class="tablebordered"width="49%" style="font-size:12px;">TOTAL NUMBER OF CANDIDATE:&nbsp; {{ $countStudents; }}</td>
								</tr>
								<tr>
								<td class="tablebordered" width="49%" style="font-size:12px;">TOTAL NUMBER OF CANDIDATE(PRESENT):________________</td>
								</tr>
								<tr>
								<td class="tablebordered" width="49%" style="font-size:12px;">TOTAL NUMBER OF CANDIDATE(ABSENT):_________________</td>
								</tr>
								<tr>
								<td class="tablebordered" width="49%" style="font-size:12px;">NUMBER OF UFM CASES:___________SIGNATURE __________</td>
								</tr>
								<tr>
								<td class="tablebordered" width="49%" style="font-size:12px;">NAME OF INVIGILATOR:__________SIGNATURE ____________</td>
								</tr>
								<tr>
								<td class="tablebordered" width="49%" style="font-size:12px;">NAME OF SUPERVISOR:___________SIGNATURE ____________</td>
								</tr>
								<tr>
								<td class="tablebordered" width="49%" style="font-size:12px;">NAME OF CENTRE SUPTD.:_________SIGNATURE & SEAL ________</td>
								</tr>
							</table>  
							<div style = "display:block; clear:both; page-break-after:always;"></div>
				</td> 
			</tr>
		</table>
			
	@endforeach
	@endforeach

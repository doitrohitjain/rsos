<?php 
use App\Component\ThoeryCustomComponent;
$secratory_signature=config::get('global.secratory_sign');
$director_signature=config::get('global.director_sign');
 $replaceText = "Duplicate";
//  $replaceText = "Revised";

$passfail = null; 

 if(isset($final_result['final_result']) && $final_result['final_result'] != ""){
	$passfail = $final_result['final_result']; 	 
 }
 
if($passfail == 'PASS' || $passfail == 'PASS' || $passfail == "pass"){ ?> 

<style>
     *{
        font-family: Arial;
      }
    
      .bigfontset3{
	font-size:18px;
	font-weight:bold;
    }
    .bigfontset{
	font-size:20px;
	font-weight:bold;
}


</style>
<table width="100%" style="margin-top:0px;">
	 
	<tr>
	<td style="padding-top:0px;margin-top:0px;">
		<table width="100%" style="margin-left:5%;">
			<tr>
				<td style="vertical-align:top;" width="33%" class="bigfont">
<div style="height:30px;">
<div class="bigfontset" style="position:absolute;margin-top:-7px;"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- Serial No: --> 
	
{{@$serial_number}}</div>
</div>
					<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<!-- AI No. --> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="bigfontset"> {{@$student['ai_code']}}</span></div>
				</td>
				<td width="30%"></td>
				<td align="center" style="width:37%;">
				<div style="width:100%">
					<div style="width:100%;font-size:16px;font-weight:bold;">
							<span style="width:100%;"><!-- Revised -->{{@$marksheet_type}} &nbsp;&nbsp;<?php echo date('d-m-Y');?></span> 
						</div>
					<div style="background: red1;width:100%;">
						<div class="mb-2" style='width:40px;margin-left:35px;'><?php echo $barcode_img; ?></div>	
						<div class="mb-2" style='width:60px;margin-left:20px;'>{{@$student['enrollment']}}</div>
						
					 </div>
				</div>				
						</td>
			</tr>
			<!--
			   <tr>
				<td width="33%" style="vertical-align:top;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="bigfontset"> {{@$student['ai_code']}}</span></td>
				<td><span class="bigfontset"><br>{{@$marksheet_type}} <br> <?php //ho date('d-m-Y');?></span></td>
			</tr>-->
		</table>
		</td>
	</tr>
	<tr><td>
		<table width="100%">
			<tr>
				<td colspan="3"><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/></td></tr>
			</tr>
			<tr>
				<td colspan="3"></td>
			</tr>
			 
		</table>
	</td>
	</tr>
	<tr>
		<td><table width="100%" style="margin-top:2%;">
			<tr>
				<td width="90%">
					<table width="100%">
						<tr>
							<td>
							
									<div class="bigfontset" style="width:100%;border:0px solid blue;height:60px;">
										<div style="float:left;width:33%;"><span style="visibility:hidden;">&nbsp;</span></div>
										<div style="float:left;width:65%;"><br>
										<span>{{@$student['name']}}</span>
										</div> 
									</div>
									<div class="bigfontset" style="width:100%;border:0px solid blue;height:60px;">
										<div style="float:left;width:33%;"><span style="visibility:hidden;">&nbsp;</span></div>
										<div style="float:left;width:65%;">
										<br>
																				
										<span>{{@$student['father_name']}}</span>
										</div> 
									</div>
									 <div style="width:100%;height:130px;">&nbsp;</div>
									
									
									<div class="bigfontset" style="width:100%;border:0px solid blue;height:80px;">
										<div style="float:left;width:15%;"><span style="visibility:hidden;">&nbsp;</span></div>
										<div style="float:left;width:85%;">
										<br>
										<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{@$student['display_exam_month_year']}}<!-- APR-MAY 2020 --></span>
										</div> 
										
									</div>
									<div class="bigfontset" style="width:100%;border:0px solid blue;height:25px;">										
										<div style="float:left;width:70%;margin-left:30%;border:0px solid blue;">
										<span>{{@$student['enrollment']}}</span>
										</div> 
									</div>
									  
									 <div style="width:100%;height:150px;">&nbsp;</div>
									<div class="bigfontset" style="width:100%;border:0px solid blue;height:70px;">
										<div style="float:left;width:72%;"><span style="visibility:hidden;">&nbsp;</span></div>
										<div style="float:left;width:28%;">
										<br>
										<br>
										<span>{{@$student['dob']}}</span>
										</div> 
									</div>
									
									<div class="bigfontset"  style="width:100%;border:0px solid blue;height:105px;margin-top:20px;">
										<div style="float:left;width:15%;"><span style="visibility:hidden;">&nbsp;</span></div>
										<div style="float:left;width:85%;">
										<br>													
										<span>{{@$dobInWords}}</span>
										</div> 
									</div>
									   
							</td>
						</tr>
						 
					</table>
				</td>
				
			</tr>
		</table></td>
	</tr>
<tr><td style="height:45px;">&nbsp;</td></tr>
<tr>
		<td style="padding-top:0px;"><table width="100%" style="text-align:center;">
			<tfoot>
				<tr>
					<td width="40%"><!-- JAIPUR --></td>
					<td width="60%"><img src="{{asset('public/app-assets/images/favicon/MarksheetsecretarySign.png')}}" style="width: 70px;" align="center"/><br></td>
				</tr>
			</tfoot>
		  </table> 
		  </td>
	</tr>
	<tr>
		<td><table width="100%" style="text-align:center;">
			<tfoot>
				<tr>
					<td width="45%"  class="bigfontset3" style="padding-top:30px;"><!-- Date : -->&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>{{@$resultDate}} 
					</b><br></td>
					<!--<td width="55%" ><b>RAMCHANDRA SINGH BAGARIA</b><br></td> -->
					<td width="55%"><b>{{strtoupper(@$secratory_signature)}}</b><!-- Secretary RSOS --><br><br></td> 
				</tr>
			</tfoot>
		  </table> 
		  </td>
	</tr>
</table>
<?php }else{
    echo "Student is not Passed.";
} ?>







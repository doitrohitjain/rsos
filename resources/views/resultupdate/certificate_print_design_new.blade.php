<?php 
use App\Component\ThoeryCustomComponent;
 $replaceText = "Duplicate";
//  $replaceText = "Revised";

$passfail = null; 

 if(isset($final_result['final_result']) && $final_result['final_result'] != ""){
	$passfail = $final_result['final_result']; 	 
 }
 
if($passfail == 'PASS' || $passfail == 'PASS' || $passfail == "pass"){ ?> 

<style>
	body{
		margin:0px 0px 0px 0px !important;
	}
     *{
        font-family: Arial;
		
      }
	  .fontbody{
		font-size:28px !important;
		/*font-family: Bradley Hand ITC;*/
		/*font-family: MV Boli;	*/
		font-family:Papyrus;
		
		font-weight:bold;
	  }
	  .bigfont{
	font-size:20px;	
    }
      .bigfontset3{
	font-size:18px;
	font-weight:bold;
    }
    .bigfontset{
	font-size:20px;
	font-weight:bold;	
	}
	.bigfontsetduplicate{
	font-size:16px;
	font-weight:bold;	
	}
	.bigfontdata{
	font-size:18px;
	font-weight:bold;	
	}
	.bigfontsetmigration{
	font-size:28px;
	font-weight:bold;
	font-family: Arial;
	color:#00003f;
	}
	.bigfontsetmigrationenglish{
	font-size:24px;
	font-weight:bold;
	font-family: Arial;
	color:#00003f;

	}
	.maintbl{
		margin:0px 0px 0px 0px;
		border:2px solid #b82e2e;
		height:100%;
	}


</style>
<table class="maintbl" width="100%" style="page-break-after:always;z-index:-999;background-image:url('http://10.68.181.175:8080/lrsos/public/app-assets/images/watermark.png');">
	 
	<tr>
	<td style="padding-top:0px;">
	<img src="{{asset('public/app-assets/images/martopl.png')}}" style="position:absolute;z-index:9;margin:-5px 0px 0px 0px !important;" width="150px" height="130px"  align="left"/>	
	<table width="77%" style="float:left;margin-top:4%;margin-left:8%;z-index:1;">
			<tr>
				<td width="47%" class="bigfont">Serial No.:<!-- Serial No: --> 
				<span class="bigfontset">{{@$serial_number}}</span></td>
				<td width="30%" rowspan="2" style="vertical-align:top;"><img src="{{asset('public/app-assets/images/favicon/markadministrator.png')}}" style="position:absolute;margin-top:-40px;"  align="center"/></td>
				<td> 
					<table style="margin:2% 0% 0% 60%;">
					<tr><td class="mb-2" style='width:40px;'><?php echo $barcode_img; ?></td></tr>	
					<tr><td class="mb-2" style='width:40px;text-align:center;margin-left: 17px;'>{{@$student['enrollment']}}</td></tr>
					
					</table> 
				</td>
			</tr>
			<tr>
               
				<td width="47%" class="bigfont" style="vertical-align:top;"><!-- AI No. -->AI No.: <span class="bigfontset"> {{@$student['ai_code']}}</span></td>
				<td >
				<table style="margin:0% 0% 0% 60%;width:150px;">
				<tr><td class="mb-2 bigfontsetduplicate" style='width:100px;text-align:center;'>{{@$marksheet_type}}</td></tr>	
				<tr><td class="mb-2 bigfontsetduplicate" style='width:200px;text-align:center;'><?php echo date('d-m-Y');?></td></tr>
				
				</table>				  
				</td>
			</tr>
		</table>
		<img src="{{asset('public/app-assets/images/martopr.png')}}" style="float:left;position:absolute;z-index:9;margin:-5px 0px 0px 0px !important;" width="150px" height="130px"  align="left"/>	
		</td>
	</tr>
	<tr><td>
		<table width="100%">
			<tr>
				<td colspan="3" style="text-align:center;">
				<img src="{{asset('public/app-assets/images/marksheet_name.png')}}" style="width:85%;" align="center"/>
				</td>
			</tr>			
			<tr><td class="bigfontsetmigration" colspan="3" style="text-align:center;">राजस्थान सरकार <span class="bigfontsetmigrationenglish">GOVT. OF RAJASTHAN</span></td></tr>
			<tr><td class="bigfontsetmigration" colspan="3" style="text-align:center;color:#8e242d;font-size:32px;">माइग्रेशन एवं स्थानान्तरण प्रमाण पत्र</td></tr>
			<tr><td class="bigfontsetmigration" colspan="3" style="text-align:center;color:#8e242d;font-size:26px;">Migration Cum Transfer Certificate</td></tr>
		</table>
	</td>
	</tr>
	<tr>
		<td><table width="100%">
			<tr>
				<td width="90%">
					<table width="100%" style="margin-left:1%;">
						<tr>
							<td style="width:33%;height:50px;"><span class="fontbody" >Shri/Smt./Kumari</span></td>
							<td style="width:65%;" class="bigfontdata"><span>{{@$student['name']}}</span></td>	
						</tr>
						<tr>
							<td style="width:33%;height:50px;"><span class="fontbody" >Son/Daughter of</span></td>
							<td style="width:65%;" class="bigfontdata"><span>{{@$student['father_name']}}</span></td>	
						</tr>
						<tr>
							<td colspan=2 style="width:100%;height:50px;"><span class="fontbody" >(Father/Mother/Guardian) has passed the Secondary Examination</span></td>
								
						</tr>
						<tr>
							<td colspan=2 style="width:100%;height:50px;"><span class="fontbody" >of Rajasthan State Open School,</span></td>
								
						</tr>
						<tr>
							<td style="width:33%;height:50px;"><span class="fontbody" >In the Year</span></td>
							<td style="width:65%;" class="bigfontdata"><span>{{@$student['display_exam_month_year']}}</span></td>	
						</tr>
						<tr>
							<td style="width:33%;height:50px;"><span class="fontbody" >With Enrollment No.</span></td>
							<td style="width:65%;" class="bigfontdata"><span>{{@$student['enrollment']}}</span></td>	
						</tr>
						<tr>
							<td colspan=2 style="width:100%;height:50px;"><span class="fontbody" style='text-align:justify;' >This institution has no objection to his/her joining any recognized</span></td>
								
						</tr>
						<tr>
							<td colspan=2 style="width:100%;height:50px;"><span class="fontbody" >college/institution or taking examination of any University or Board</span></td>
								
						</tr>
						<tr>
							<td colspan=2 style="width:100%;height:50px;"><span class="fontbody" >established by law.</span></td>
								
						</tr>
						<tr>
							<td colspan=2 style="width:100%;height:50px;"><span class="fontbody" >His/Her date of birth as per record submitted is</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="bigfontdata">{{@$student['dob']}}</span></td>
								
						</tr>
						<tr>
							<td style="width:33%;height:50px;"><span class="fontbody" >in words</span></td>
							<td style="width:65%;" class="bigfontdata"><span>{{@$dobInWords}}</span></td>	
						</tr>
						
					</table>
				</td>
				
			</tr>
		</table></td>
	</tr>
	<?php /*<tr>
		<td style="padding-top:20px;"><table width="100%" style="text-align:center;">
			<tfoot>
				<tr>
					<td width="40%"><!-- JAIPUR --></td>
					<td width="60%"><br></td>
				</tr>
			</tfoot>
		  </table> 
		  </td>
	</tr>*/?>
	<tr>
		<td>
		<img src="{{asset('public/app-assets/images/marbotl.png')}}" style="position:absolute;z-index:9;rotate:360deg;margin:0px 0px 0px 0px;" width="150px" height="130px"  align="left"/>	
		<table width="71%" style="text-align:center;float:left;margin:2% 0% 0% 14%;">
			<tfoot>
				<tr>
					<td width="55%"  class="bigfontset3" style="text-align:left;padding:40px 0px 0px 10%;"><!-- Date : --><span style='font-size:24px;'>JAIPUR</span><br>दिनांक/<span style='font-size:24px;'>Date:</span> <b>{{@$resultDate}} 
					</b><br></td>
					<!--<td width="55%" ><b>RAMCHANDRA SINGH BAGARIA</b><br></td> -->
					<td width="45%" style="text-align:center;padding-top:28px;"><img src="{{asset('public/app-assets/images/favicon/signatureinpdf1.png')}}" style="width: 70px;" align="center"/><br/><b>RAJENDRA KUMAR SHARMA</b><!-- Secretary RSOS --><br><span style='font-size:18px;'>Secretary(RSOS)</span><br></td> 
				</tr>	
			</tfoot>
		  </table> 
		  <img src="{{asset('public/app-assets/images/marbotr.png')}}" style="float:eft;position:absolute;z-index:9;margin:0px 0px 0px 0px;" width="150px" height="130px"  align="left"/>
		  </td>
	</tr>
</table>
<?php }else{
    echo "Student is not Passed.";
} ?>







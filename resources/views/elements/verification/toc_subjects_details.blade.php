

@if($masterDetails->application->toc != 0 || !empty(@$masterDetails->tocdetils->student_id))
	@if(@$masterDetails->adm_type != 5)
		<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">टीओसी बोर्ड विवरण</a>(TOC Board Details) :</span></span></div> 
				@php $fp = ''; 
				if($masterDetails->adm_type == 1 || $masterDetails->adm_type == 2){
				$fp = 'failing';
				}else{
				$fp = 'passing';
				}
				$streamval = 'stream'.$masterDetails->stream;
				$isyearof = 'is_year_of_'.$fp.'_required_'.$streamval;
				@endphp
				@php
				$fld = "is_year_of_passing_required_stream1";
				if(@$passfailyers->$fld){
				$lblName = "Falling";$fldYearPassFailName="year_fail";
				
				} 
				$fld = "is_year_of_passing_required_stream2";
				if(@$passfailyers->$fld){
				$lblName = "Falling";$fldYearPassFailName="year_fail";
				}
				$fld = "is_year_of_failing_required_stream1";
				if(@$passfailyers->$fld){
				$lblName = "Passing";$fldYearPassFailName="year_pass";
				}
				$fld = "is_year_of_failing_required_stream2";
				if(@$passfailyers->$fld){
				$lblName = "Passing";$fldYearPassFailName="year_pass";
				} 
				@endphp

			<table border="1" style="width:100%;">
				<tbody>
					<tr>
					   @if(@$masterDetails->adm_type == 1 || @$masterDetails->adm_type == 2 || @$masterDetails->adm_type == 4 )
						<!--<td><span class="font"><span class='customStrong'>&nbsp;Whether you are applying for Transfer of Credit</span></span></td> -->
						<td><span class="font"><span class='customStrong'>&nbsp;Name Of Board</span></span></td>
					
						<td><span class="font"><span class='customStrong'>&nbsp;Years of <?php echo @$lblName; ?></span></span></td> 
						<td><span class="font"><span class='customStrong'>&nbsp;Roll No.</span></span></td>
						@endif

					</tr>
					<tr> 
					 @if(@$masterDetails->adm_type == 1 || @$masterDetails->adm_type == 2 || @$masterDetails->adm_type == 4 )
						<!--<td class="font">&nbsp;{{@$yesno[@$masterDetails->application->toc]}}</td>-->
					 @endif
						<td class="font">&nbsp;{{@$getBoardList[@$masterDetails->tocdetils->board]}}</td>
						
						@if(!empty($masterDetails->tocdetils->year_fail))
						<td class="font">&nbsp;{{@$tocpassfail[@$masterDetails->tocdetils->year_fail]}}</td>
						@else
						<td class="font">&nbsp;{{@$tocpassyear[@$masterDetails->tocdetils->year_pass]}}</td>
						@endif
						<td class="font">&nbsp;{{@$masterDetails->tocdetils->roll_no}}</td>
					</tr>
				</tbody>
			</table>
		@endif

		<div><span class="fonthead"><span class='customStrong'><a style="font-size:14px;">टीओसी विषय विवरण</a>( TOC Subjects Details):</span></span></div>
		<table border="1" style="width:100%;">
			<tbody>
				<!--<tr>
					<td colspan="5"><span class="font"><span class='customStrong'>क्रेडिट&nbsp;स्थानांतरण(Transfer of Credit) : 
					@if(@$masterDetails->application->toc==1) Yes  @else  No @endif </span></span></td>
				</tr>-->
				@if(!empty($master_subject_details['tocSubjectDetails']))
				<tr>
			<td style="width:20%;">Subjects Name(Code)</td>
			<td style="width:20%;">Theory Marks</td>
			<td style="width:20%;">Practical Marks</td>
			<td style="width:20%;">Total Marks</td>
		   </tr>
		   @foreach(@$master_subject_details['tocSubjectDetails'] as $key => $values)
		   <tr>
					<td><span class="font">&nbsp;{{ @$values['subject_id'] }}</span></td>
					<td><span class="font">&nbsp;{{ @$values['theory'] }}</span></td>
					<td><span class="font">&nbsp;{{ @$values['practical'] }}</span></td>
					<td><span class="font">&nbsp;{{ @$values['total_marks'] }}</span></td>
					</tr>
					@endforeach
				@endif
			</tbody>
		</table>
@else
	TOC : No
@endif
@include('elements.verification.other_then_document_status_update')
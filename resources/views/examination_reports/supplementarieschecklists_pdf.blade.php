<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
      .tdlast {
        vertical-align: text-top !important;
        width: 20% !important;
        font-size: 12px !important;
      }

      tr {
        width: 20% !important;
        font-size: 12px !important;
      }

      table.bottomBorder {
        border-collapse: collapse;
      }

      table.bottomBorder td,
      table.bottomBorder th {
        border-bottom: 2px solid black;
        padding: 5px;
        text-align: left;
      }

      .font {
        font-family: Arial, sans-serif;
      }
    </style>
  </head>
  <body> @php header( 'Content-Type: text/html; charset=utf-8' ); use App\Helper\CustomHelper; @endphp <table style="width:100%;">
      <tbody>
        <tr>
          <td style="width: 35%;font-size:16px;font-weight: bold;" class="font">
            <strong>RAJASTHAN STATE OPEN SCHOOL,JAIPUR</strong>
          </td>
          <td style="width: 55%;font-size:16px;font-weight: bold;" class="font">
            <strong>Online Supplementary Registration Checklist (1st)<!-- <?php if(isset($stream) && $stream ==1){
		echo "(1st)"; } else { echo "(2nd)";  } ?> -->
		[ <?php if(isset($stream) && $stream == 1){
		echo "March-May"; } else { echo "October-November";  } ?>  <?php echo  substr(@$admission_sessions[$current_admission_session_id], 0, 4); ?>  ] </strong>
          </td>
          <td style="width: 10%;font-size:16px;text-align:right;font-weight: bold;" class="font">
            <strong>&nbsp;&nbsp;&nbsp; <?php echo $aicode; ?>/ <?php echo $course; ?>th </strong>
          </td>
        </tr>
      </tbody>
    </table>
    <hr />

    <table style="width:100%;text-align:left;border-color: black;" class="font">
      <thead>
        <tr>
          <th style="font-size:16px;">S.No.</th>
          <th style="font-size:16px;" >ENR. No</th>
          <th style="font-size:16px;" >Name of Candidate</th>
          <th style="font-size:16px;" >Examination Subjects</th>
          <th style="font-size:16px;" >Mobile Number</th>
          <th style="font-size:16px;" >Fee Status</th>
        </tr>
      </thead>
      <td colspan="6">
        <hr>
      </td>
      </tr>
      <tr>
        <td style="font-size:16px;" colspan="3">
          <b> @if($course == 10) COURSE : 10th | SECONDARY @elseif($course == 12) COURSE : 12th | SENIOR SECONDARY @endif</b>
        </td>
        <td colspan="3"></td>
      </tr>
      <tbody> @php $i=1; @endphp @foreach($master as $skey => $student) <tr>
          <td style="font-size:16px;">{{ $i }} </td>
          <td style="font-size:16px;">{{ @$student['enrollment'] }} </td>
          <td style="font-size:16px;">{{ @$student['name'] }} </td>
          <td style="font-size:16px;"> <?php @$paperCodes = NULL;
									if(isset($student['exam_subjects']) && !empty($student['exam_subjects'])){
										foreach (@$student['exam_subjects'] as $key => $subject){
												$suppFlag = false;
												@$result = CustomHelper::getStudentResult($student['enrollment'], $subject['id']);
											 @$paperCodes = NULL;
											  if(@$student['exam_subjects'][$key])
												{
												   if(in_array($subject['id'] ,$practicalsubjects12) &&  $result == 888 ){
														@$paperCodes = 'T,P';
													} else if (!in_array($subject['id'] ,$practicalsubjects12) &&  $result == 888 ){
														@$paperCodes = 'T';
													} else if (in_array($subject['id'] ,$practicalsubjects12)  && $result == 666 ){
														@$paperCodes = 'P';
													} else if (!in_array($subject['id'] ,$practicalsubjects12)  && $result == 777 ){
														$paperCodes = 'T';
													} 
												    else if (in_array($subject['id'] ,$practicalsubjects12)  && $result == 777 ){
														$paperCodes = 'T';
													}
													else if (in_array($subject['id'] ,$practicalsubjects12)){
														$paperCodes = 'T,P';
													}
													else if (!in_array($subject['id'] ,$practicalsubjects12)){
														$paperCodes = 'T';
													}
													else if ($result == 'P' ){
														@$paperCodes = 'ERROR';
													} 

												}
												else {
														@$paperCodes = 'ERROR';
													 } 
										 ?> <?php echo  @$key . '[' . @$paperCodes .']'; ?> <?php } }?> </td>
          <td style="font-size:16px;">{{ @$student['mobile'] }} </td>
          <td style="font-size:16px;">FEE RECEIVED</td>
        </tr> @php $i++; @endphp @endforeach </tbody>
    </table>
  </body>
  <html>




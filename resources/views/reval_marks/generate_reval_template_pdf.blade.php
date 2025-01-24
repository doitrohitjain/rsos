@extends('layouts.pdf')
@section('content')


<style type="text/css">
.box {
    border: 1px solid #000000;
    display: inline-block;
    padding: 10px 60px;
	text-align:center;
}

td, th {
	text-align:center;
}
.boxmistake {
    border: 1px solid #000000;
    display: inline-block;
    padding: 10px 120px;
	text-align:center;
}
@media print
{    
    .noprint, .noprint *
    {
        display: none !important;
    }
}
table, th, td{
  border: 1px solid #D3D4D9;
  border-collapse: collapse;
  font-size:14px;
  font-family:Arial;
  
}
 .cc51 {
  font-size: 24px;
  white-space: nowrap;
  text-align: center;
  vertical-align: middle;
}
.cc55 {
  display: inline-block;
  vertical-align: middle;
}
.font{
 font-size:14px;
 font-family:Arial;
 font-weight:normal;  
 }
 .fonthead{
 font-size:12px;
 font-family:Arial;
 font-weight:bold;  
 }
</style>

<style type="text/css">
			table{
				 border-collapse: collapse;
				border-spacing: 0;
				font-family: arial, sans-serif;
			}
			.cc51 {
			  white-space: nowrap;
			  text-align: center;
			  vertical-align: middle;
			}
			.cc55 {
			  display: inline-block;
			  vertical-align: middle;
			}
			.font{
				font-family: Arial, sans-serif;
			}
			#evalhead tr td div{
				text-align: center;

			}
			tr{
			    padding-top:10px;
				border:0.2px solid rgba(2, 2, 2, 0.377);
			}
			
			.new-page {
				page-break-before: always;
			}
			.centerLabel{	
				font-size: 20px;
			}
			fieldset.scheduler-border {
			border: 1px #ccc solid !important;
			padding: 0 1em 1em !important;
			margin: 0 0 0 0 !important;
			-webkit-box-shadow: 0px 0px 0px 0px #000;
			box-shadow: 0px 0px 0px 0px #000;
			}
			legend.scheduler-border {
			font-size: 14px !important;
			font-weight: bold !important;
			text-align: left !important;
			width: auto;
			padding: 0 5px;
			border-top: none;
			border-bottom: none;
			}
			.fieldsetLable-newll {
				color: #0B614B;
				font-weight: bold;
				font-size: 100%;
				font-family: Cambria;
				float: none;
			}
			.pad, .box-title{
			margin-top:10px;
			}
			.page-header {
				padding-bottom:0px !important;
				margin:0px !important;
			}

			label {
				margin-bottom: 0px !important; 
			}
			.text-right {
				text-align: right;
				margin-top: -3%;
				margin-right: 7%;
			}
			.row{
			font-size:16px;
			}
			#signaturetbl tr td{height:25px;}
			h4,h5{text-align:center;}
			/*#TF_Table_Personal th{
			background-color:#ededed;
			}*/
			.bothBorder{ 
				border-bottom: 1px solid #000; 
				border-top: 1px solid #000 !important; 
			}
			.botomBorder{ 
				border-bottom: 2px solid #000 !important; 
			}

		</style>
		
</head>
<body style="margin-top:-10%,margin-bottom:0%">
	
	@if(@$finalArr)
		@php $outerCount = 1; @endphp
		@foreach($finalArr as $subject_id => $subejctData)
			@if($outerCount > 1)
				<div class="new-page">&nbsp;</div>
			@endif
			<table style="width:100%;"> 
				<thead>
					<td colspan="15">
						<div class="cc51"> 
							<div class="cc55">
								<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 45px; height: 40px; border-radius: 10px" alt="image upload button"/>
							</div>  
							RSOS Revaluation - Exam Session {{@$exam_month_master[@$reval_exam_month]}} {{@$admission_sessions[@$reval_exam_year]}} class {{ @$v->course }} of prepare by examiner details 
						</div>  
					</td>
				</thead> 
				<thead> 
					<th>Sr. No.</th>
					<th>Enrollment</th>
					<th>Student Fixcode</th>
					<th>Center Fixcode</th>
					<th>Sub Code</th>
					<th>Marks On Answer Book before reval</th>
					<th>Marks On Answer Book after reval</th>
					<th>Remarks</th>
					<th>Theory Marks in Result</th>
					<th>Sessional Marks in Result</th>
					<th>Practical Marks in Result</th>
					<th>Total Marks in Result</th>
					<th>Final Result in Result</th>
				</thead>
				@php $counter = 1; @endphp
				@foreach($subejctData as $k => $v)
					@php $class = ""; @endphp
					@if($counter > 1 && ($counter % $revalMarksDefaultPageLimit === 1))
						@php $class = "new-page"; @endphp
						</table>
						<table style="width:100%;"> 
							<thead>
								<td colspan="15">
									<div class="cc51"> 
										<div class="cc55">
											<img src="{{asset('public/app-assets/images/favicon/administrator.png')}}" style="width: 45px; height: 40px; border-radius: 10px" alt="image upload button"/>
										</div>  
										RSOS Revaluation - Exam Session {{@$exam_month_master[@$reval_exam_month]}} {{@$admission_sessions[@$reval_exam_year]}} class {{ @$v->course }} of prepare by examiner details 
									</div>  
								</td>
							</thead> 
							<thead>
								<th>Sr. No.</th>
								<th>Enrollment</th>
								<th>Student Fixcode</th>
								<th>Center Fixcode</th>
								<th>Sub Code</th>
								<th>Marks On Answer Book before reval</th>
								<th>Marks On Answer Book after reval</th>
								<th>Remarks</th>
								<th>Theory Marks in Result</th>
								<th>Sessional Marks in Result</th>
								<th>Practical Marks in Result</th>
								<th>Total Marks in Result</th>
								<th>Final Result in Result</th>
							</thead>
							<div class="new-page">&nbsp;</div>
					@endif
					<tr> 
						<td>{{ @$counter }}</td> 
						<td>{{ @$v->enrollment }}</td>
						<td>{{ @$v->studentfixcode }}</td>
						<td>{{ @$v->centerfixcode }}</td>
						<td>{{ @$subjectCodes[$v->subject_id] }}</td>						
						<td>{{ @$v->marks_on_answer_book_before_reval }}</td>
						<td>
							<span class="box"></span>
						</td>						
						<td>
							<span class="boxmistake"></span> 
						</td> 
						<td>{{ @$v->final_theory_marks }}</td>
						<td>@if( @$v->sessional_marks == 999)
								@php $v->sessional_marks = 0; @endphp
							@endif
							{{ @$v->sessional_marks }}</td>
						<td>
							@if( @$v->final_practical_marks == 999)
								@php $v->final_practical_marks = 0; @endphp
							@endif
							{{ @$v->final_practical_marks }}
						</td>
						<td>{{ @$v->total_marks }}</td>
						<td> 
							@php $rr = @$v->final_result_after_reval; @endphp 
							@if(isset($resultsyntax[@$v->final_result]))
								@php $rr = $resultsyntax[@$v->final_result]; @endphp 
							@endif
							{{ @$rr }}
						</td>
					</tr>
					@php $counter++; @endphp
				@endforeach 
			</table>
			@php $outerCount++; @endphp
		@endforeach
	@endif  
	
	<style>
		ul {
			list-style: none;
			margin-left: -30px;
		}

		ul li:before {
			content: '✓';
		}
	</style>
</body>
@endsection
@section('customjs')

@endsection
@extends('layouts.default')
@section('content')
@php 
	use App\Helper\CustomHelper;
	$permissions = CustomHelper::roleandpermission();
	$getdatamaster1 = CustomHelper::getdatamaster();
	$getdatamastersexam = CustomHelper::getdatamastersexam();
	$selected_session = CustomHelper::_get_selected_sessions();
	$role_id = Session::get('role_id');
	$changerole = CustomHelper::_changerole();
	$resultCount = 0;
	if(@$changerole){
		$resultCount = count($changerole); 
	} 
@endphp
<style>
.table1 {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  background-color: MediumSeaGreen  ;
  border-radius: 12px;
  
}
.table2 {
  font-family: arial, sans-serif;
   width: 100%;
  background-color: MediumSeaGreen  ;
   border-radius: 12px;
}

 th {
  border: 1px solid ;
  text-align: left;
  padding: 8px;
  color: white;
  font-weight: bold;

}

 td {
  border: 1px solid;
  color: white;
   font-weight: bold;
}
 a {
  color: white;
}

</style>
<div id="main">
	<div class="row">
    <div class="col s12">
			<div class="container"> 
				<div class="section section-data-tables"> 
					<div class="row">
						<div class="col s12">
							<div class="card">
								<div class="card-content">
								  @if(in_array("all_Material",$permissions))
								 <h5><center><b>Material Download</b></h5></center><br><br>
							     @endif
									<table class="table1">
									<tr>
									@if(in_array("aicodenominalnraicenter",$permissions) || in_array("aicodehallticket",$permissions))
									<th width="28%" height="50"><center>AI Center Material</center></th>
									@endif
									@if(in_array("aicodenominalnr",$permissions))
									<th width="28%" height="50"><center>Exam Center Material</center></th>
								    @endif
									@if(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions)
||in_array("examination_report_supplementarychecklists",$permissions))
									<th width="44%" height="50"><center>Rsos Material</center></th>
									 @endif
									</tr>
									</table><br>
											<table class="table2">
	<tr>
@if(in_array("aicodenominalnraicenter",$permissions))
    <td width="28%" height="50%" ><a href="{{route('nominalnrgenerateview')}}" class="">&nbsp;Nominal Roll </a></td>
@elseif(in_array("aicodenominalnraicenter",$permissions) || in_array("aicodehallticket",$permissions))
 <td width="28%" height="50%" ></td>
@endif

@if(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%"><a href="{{route('single_exam_center_nominal_roll_pdf_view')}}">&nbsp;Nominal Roll</a></td>
@elseif(in_array("aicodenominalnr",$permissions))
 <td width="28%" height="50%" ></td>
@endif
@if(in_array("examinationmaterial",$permissions))
    <td width="22%" height="50%"style="text-align: center;">&nbsp;Examination Material</td>
@elseif(in_array("examinationmaterial",$permissions) || in_array("examination_reportexamcenterwise",$permissions))
 <td width="28%" height="50%" ></td>
@endif
@if(in_array("admissionmaterial",$permissions))
	<td width="22%" height="50%" style="text-align: center;">&nbsp;Admission Material</td>
@elseif(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions)
||in_array("examination_report_supplementarychecklists",$permissions))
 <td width="28%" height="50%" ></td>
@endif
  </tr>
  
  <tr>
   @if(in_array("aicodehallticket",$permissions))
    <td width="28%" height="50%" ><a href="{{route('hallticketbulkviews')}}">&nbsp;Hall Tickets</a></td>
	@elseif(in_array("aicodenominalnraicenter",$permissions) || in_array("aicodehallticket",$permissions))
	<td width="28%" height="50%" ></td>
	@endif

    @if(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%">&nbsp;<a href="{{route('single_exam_center_attendance_roll_pdf_view')}}">Attendance Sheets</a></td>
    @elseif(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%" ></td>
    @endif
	@if(in_array("examination_reportexamcenterwise",$permissions))
    <td width="22%" height="50%" ><a href="{{route('reportexamcenterwise')}}">&nbsp;Big strength Nominal Roll</a></td>
@elseif(in_array("examinationmaterial",$permissions) || in_array("examination_reportexamcenterwise",$permissions))
 <td width="28%" height="50%" ></td>
	@endif
	@if(in_array("examination_report_studentchecklists",$permissions))
	<td width="22%" height="50%"><a href="{{route('studentchecklists')}}">&nbsp;Student Checklist</a></td>
@elseif(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions) 
||in_array("examination_report_supplementarychecklists",$permissions))
 <td width="28%" height="50%" ></td>
 @endif
  </tr>
  
  <tr>
  @if(in_array("aicodenominalnraicenter",$permissions)||in_array("aicodehallticket",$permissions))
   <td width="28%" height="50%" ></td>
@endif

@if(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%"><a href="{{route('single_exam_center_theorynominal_roll_pdf_view')}}">&nbsp;Theory Roll</a></td>
@elseif(in_array("aicodenominalnr",$permissions))
 <td width="28%" height="50%" ></td>
@endif
    <td width="22%" height="50%" >&nbsp;Board Nominal Roll</td>
	@if(in_array("examination_report_supplementarychecklists",$permissions))
	<td width="22%" height="50%" ><a href="{{route('SupplementaryChecklists')}}">&nbsp;Supplementary Checklist</a></td>
@elseif(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions) ||in_array("examination_report_supplementarychecklists",$permissions))
 <td width="28%" height="50%" ></td>
@endif
  </tr>
  
   <tr>
    @if(in_array("aicodenominalnraicenter",$permissions)|| in_array("aicodehallticket",$permissions))
   <td width="28%" height="50%" ></td>
@endif
@if(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%"><a href="{{route('single_exam_center_practicalnominal_roll_pdf_view')}}">&nbsp;Practical Roll</a></td>
@elseif(in_array("aicodenominalnr",$permissions))
 <td width="28%" height="50%" ></td>
@endif
    <td width="22%" height="50%" >&nbsp;Aicenter Wise Nominal Roll</td>
	@if(in_array("examination_report_tocchecklists",$permissions))
	<td width="22%" height="50%" ><a href="{{route('tocChecklists')}}">&nbsp;Toc Checklist</a></td>
@elseif(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions)
||in_array("examination_report_supplementarychecklists",$permissions))
 <td width="28%" height="50%" ></td>
@endif
  </tr>
  
   <tr>
    @if(in_array("aicodenominalnraicenter",$permissions) || in_array("aicodehallticket",$permissions))
   <td width="28%" height="50%" ></td>
@endif
@if(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%"><a href="{{route('single_exam_center_practicalsignaturenominal_roll_pdf_view')}}">&nbsp;Theory Signatare Roll</a></td>
@elseif(in_array("aicodenominalnr",$permissions))
 <td width="28%" height="50%" ></td>
@endif
    <td width="22%" height="50%">&nbsp;Stickers</td>
	@if(in_array("examination_report_allcenterwisetocchecklist",$permissions))
	<td width="22%" height="50%" ><a href="{{route('allcenterwisetocchecklist')}}">&nbsp;District wise Checklist</a></td>
@elseif(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions) ||
in_array("examination_report_allcenterwisetocchecklist",$permissions)
||in_array("examination_report_supplementarychecklists",$permissions))
 <td width="28%" height="50%" ></td>
@endif
  </tr>
  
   <tr>
   @if(in_array("aicodenominalnraicenter",$permissions)||in_array("aicodehallticket",$permissions))
   <td width="28%" height="50%" ></td>
@endif
@if(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%"><a href="{{route('single_exam_center_theorysignaturenominal_roll_pdf_view')}}">&nbsp;Practical Signatare Roll</a></td>
@elseif(in_array("aicodenominalnr",$permissions))
 <td width="28%" height="50%" ></td>
@endif
    <td width="22%" height="50%" >&nbsp;Student Fixcode</td>
@if(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions) ||
in_array("examination_report_allcenterwisetocchecklist",$permissions)
||in_array("examination_report_supplementarychecklists",$permissions))
 <td width="28%" height="50%" ></td>
@endif
  </tr>
  
  <tr>
    @if(in_array("aicodenominalnraicenter",$permissions)||in_array("aicodehallticket",$permissions))
   <td width="28%" height="50%" ></td>
@endif
@if(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%"></td>
@endif
    <td width="22%" height="50%" >&nbsp;Examcenter Fixcode</td>
@if(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions) ||
in_array("examination_report_allcenterwisetocchecklist",$permissions)
||in_array("examination_report_supplementarychecklists",$permissions))
 <td width="28%" height="50%" ></td>
@endif
  </tr>
  
  <tr>
 @if(in_array("aicodenominalnraicenter",$permissions)||in_array("aicodehallticket",$permissions))
   <td width="28%" height="50%" ></td>
@endif
@if(in_array("aicodenominalnr",$permissions))
    <td width="28%" height="50%"></td>
@endif
    <td width="22%" height="50%" >&nbsp;marksheet printing</td>
	@if(in_array("admissionmaterial",$permissions) ||
 in_array("examination_report_studentchecklists",$permissions) ||
 in_array("examination_report_tocchecklists",$permissions) 
|| in_array("examination_report_allcenterwisetocchecklist",$permissions) ||
in_array("examination_report_allcenterwisetocchecklist",$permissions)
||in_array("examination_report_supplementarychecklists",$permissions))
 <td width="28%" height="50%" ></td>
@endif
  </tr>
  
  </table>
								</div>
							</div>
						</div>
					</div>
				</div> 
          <div class="content-overlay"></div>
        </div>
		</div>
    </div>
</div> 
@endsection

@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/timetable_details_delete.js') !!}"></script> 
@endsection 



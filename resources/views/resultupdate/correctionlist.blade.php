@extends('layouts.default')
@section('content')
<style>
.tablestye {
	border:.1px solid rgba(0, 0, 0, 0.5);
}
.tableth{

  font-size:20px
}
</style>
<div id="main">
	<div class="row">
		<div id="breadcrumbs-wrapper" data-image="{{ asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ @$title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							@foreach($breadcrumbs as $v)
								<li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
							@endforeach 
						</ol>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col s12">
			<div class="container">
				<div class="section">
					<div class="col s12 m12 l12">
						<div class="card">  
							@include('elements.marsheetcorrection_top_navigation')
						</div>
					</div>
				</div> 
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-content invoice-print-area"> 
		<span style="font-weight: bold;color:#000000;">Fees Details</span>
			<div class="row">
				<div class="col m12">
					<table>
						<tr>
						  <td width="25%" style="font-size:16px;color:blue;">Revised/Duplicate (Marksheet and Migration)
						  </td>
						  <td width="1%"> @php echo " : "; @endphp </td>
						  <td width="6%"style="font-size:16px;color:blue;">{{@$marksheet_migartion_fees['3']  }} Rs. </td>			
						<td width="16%"style="font-size:16px;color:blue;">
						  Revised/Duplicate Marksheet
						  </td>
						  <td width="1%"> @php echo " : "; @endphp </td>
						  <td width="6%" style="font-size:16px;color:blue;">{{@$marksheet_migartion_fees['1']}} Rs.</td>
						  <td width="16%"style="font-size:16px;color:blue;">
						 Revised/Duplicate Migration
						  </td>
						  <td width="1%"> @php echo " : "; @endphp </td>
						  <td width="1%" style="font-size:16px;color:blue;">{{@$marksheet_migartion_fees['2']}} Rs.</td>	
						</tr>
					</table>
				</div>
			</div>	
		</div>
	</div>
	{{ Form::open(['url'=>url()->current(),'id'=>'correctiondata','enctype' => 'multipart/form-data']) }}
	{!! Form::token() !!}
	{{ method_field('PUT') }}
	@php  
		$hideDiv='';
        $hideDiv2  =  '';		
		if(empty($alreadyData)){ 
			$hideDiv='style="display:none;"'; 
		}
       if(!empty($alreadyData) && $alreadyData->marksheet_type == 2){
		   
		   $hideDiv2='style="display:none;"'; 
	   }  
	@endphp
		<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
		<div class="section section-data-tables"> 
			<div class="row">
				<div class="col s12">
					<div class="card">
						<div class="card-content">
							<span style="font-weight: bold;color:#000000;font-size:20px;">संशोधित/डुप्लीकेट मार्कशीट/माइग्रेशन (Revised/Duplicate Marksheet/Migration)</span><br><br>
							<div class="row">
								<div class="input-field col s6 document">
									@php $lbl='Marksheet Type'; 
									$fld='marksheet_type';
									@endphp
									
									<p >{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</p>{!!Form::select($fld,@$marsheet_type,@$alreadyData->marksheet_type,['type'=>'Select','class' => 'select2 browser-default form-control center-align ','id'=>"$fld",'placeholder'=>"select $lbl"]); !!}
								</div>	
								<div class="input-field col s6 document">
								
									@php $lbl='Document Type'; 
									$fld='document_type';
									$value = '';
									if(@$finalresultstatus == true){
										$disabled ='disabled';
										$value  = 1;
										
									}
									if(@$alreadyData->$fld){
										$value = @$alreadyData->$fld;
										
									}
									@endphp
									<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
										{!!Form::select($fld,@$marksheet_print_option,@$value,['type'=>'Select','class' => 'select2 browser-default form-control center-align ','id'=>"$fld",'placeholder'=>"select $lbl",@$disabled]); !!}
									<input type="hidden" name='{{$fld}}' value="1" >
										
								</div>									
							</div>			
						</div>
					</div>
					<!--<div class='card'>
							<div class="card-content">
								<table class="responsive-table revised "  <?php echo $hideDiv; ?>>
									<tbody> 
										@if(!empty(@$student_data))
											<tr >
												<th style="font-weight:bold;">Ai Code</th>
												<td>{{@$student_data->ai_code}}</td>
												<th style="font-weight:bold;">Enrollment</th>
												<td>{{@$student_data->enrollment}}</td>
												<th style="font-weight:bold;">Exam Result</th>
												<td>{{@$final_results->final_result}}</td>
												
											</tr>
										@endif
									</tbody>
								</table>
							</div>
					</div>-->
					<div class='card revised' <?php echo $hideDiv; ?>>
						<div class="card-content">
							<table class="responsive-table " >
								<thead>
									<tr>
										<th class="tableth">Details</th>
										<th class="tableth">Application Filled Details</th>
										<th class="duplicate tableth" <?php echo $hideDiv2; ?>>Correct Details</th>
									</tr>	
								</thead>
								<tbody> 
									@if(!empty(@$student_data))
										<tr>
											<td>Name</td>
											<td>{{$student_data->name}}</td>
											<td class="duplicate" <?php echo $hideDiv2; ?>>
											@php  $fld='name'; @endphp	{!!Form::text($fld,$student_data2->$fld,['type'=>'text','class'=>'form-control col-sm-2  uppercase','autocomplete'=>'off','size'=>'1','required'=>'true']); !!}
											@include('elements.field_error')
											<input type="hidden" name='field[]' value="{{$fld}}" >
											</td>
										</tr>
										<tr>
											@php $lbl='Date of birth'; $fld='dob';
											$dobFormat = @$student_data2->$fld;
											$dobFormat = date("M d, Y",strtotime(@$dobFormat));
											
											@endphp
											<td >Date Of Birth</td>
											<td>{{date("d-m-Y",strtotime(@$student_data->$fld))}}</td>
											<td class="duplicate" <?php  echo $hideDiv2; ?>>{!!Form::text($fld,$dobFormat,['type'=>'text','class'=>'form-control datepicker',"readonly"=>true,'size'=>'1','autocomplete'=>'off','required'=>true]); !!}
											@include('elements.field_error')
											<input type="hidden" name='field[]' value="{{$fld}}" >
											</td>
										</tr>
										<tr>
											<td >Father Name</td>
											<td>{{$student_data->father_name}}</td>
											<td class="duplicate" <?php echo $hideDiv2; ?>>
											@php  $fld='father_name'; @endphp
											{!!Form::text($fld,$student_data2->$fld,['type'=>'text','class'=>'form-control  uppercase','autocomplete'=>'off','size'=>'1','required'=>'true']); !!}
											@include('elements.field_error')
											<input type="hidden" name='field[]' value="{{$fld}}" >
											</td>
										</tr>
										<tr>
											<td >Mother Name</td>
											<td>{{$student_data->mother_name}}</td>
											<td class="duplicate" <?php echo  $hideDiv2; ?>>
											@php  $fld='mother_name'; @endphp
											{!!Form::text($fld,$student_data2->$fld,['type'=>'text','class'=>'form-control  uppercase','autocomplete'=>'off','size'=>'1','required'=>'true']); !!}
											@include('elements.field_error')
											<input type="hidden" name='field[]' value="{{$fld}}" >
											</td>
										</tr>
									@endif
								</tbody>
							</table>
							<div class="row revised" <?php echo $hideDiv; ?>>
								<div class="col m6 s12 file-field input-field">
									<div class="btn float-right">
										<span>
											@php $lbl ="support Document";
											$fld ="support_document2";
											echo $lbl; $fldInputType ="support_document"; $fldInput = "support_document";@endphp
										</span>
										{!!Form::file($fld,['type'=>'file',"data-type" => "i", "data-formId" =>"Documentupload", "id" => $fld, 'class'=>'form-control inputfile','autocomplete'=>'off']); !!}
										<input type="hidden" name='support_document' value="{{@$alreadyData->id}}" id='support_document'>
									</div>
									<div class="file-path-wrapper">
										<span style="text-align:center;">
											<input class="file-path validate" type="text" value ="@php echo $lbl; @endphp" disabled>
											@include('elements.field_error')
										</span>
									</div>
								</div>
								<div class="input-field col m6 s10 mb-3">
									@if(!empty(@$alreadyData->support_document))
									@php  
										$filePath = route('download', Crypt::encrypt('/'.$studentDocumentPath . "/" . @$alreadyData->support_document)); 
									@endphp			
									<a href="{{ $filePath }} " target="_blank" class='btn btn-ghost-info'>
									   <i class="fa fa-download">download</i>
									</a> 
								@endif
								</div>
								<div class="row revised" <?php echo $hideDiv; ?>>
									<div class="col m10 s11 mb-3">
										<button class="btn cyan waves-effect waves-light right gradient-45deg-deep-orange-orange float-right" type="reset">Reset
										</button>
									</div>
									<div class="col m2 s11 mb-3">
										<button class="btn cyan waves-effect waves-light  show_confirm right" style="background: linear-gradient(45deg,#303f9f,#7b1fa2);" type="submit" name="action">Submit
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>	
			</div>	
		</div>	
	{{ Form::close() }}
</div>				
@endsection
@section('customjs')
   <script src="{!! asset('public/app-assets/js/bladejs/correctionMarskeet.js') !!}"></script> 
@endsection 
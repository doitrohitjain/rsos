@extends('layouts.default')
@section('content')
<!-- BEGIN: Page Main-->
<div id="main">
<div class="row">
		
    <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
        <!-- Search for small screen-->
        <div class="container">
            <div class="row">
                <div>
                    <h5 style="color:white"><span>{{ $title }}</span></h5>
                </div>
            </div>
        </div>
    </div>
	
     <div class="col s12">
        <div class="container">
            <div class="seaction">
			
			<!--  Start Enrollment Wise Generate & Download -->
			<!-- 1 form -->
				@can('bulk_documents1')
				<div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
                                <div id="html-view-validations"> 
								<?php $document_type_dropdown_arr = array('1'=>'Marksheet','3'=>'Certificate'); ?>
								<h5 style="color:green">1. Enrollment Wise Marksheet/Certificate Generate & Download</h5>
								{{ Form::open(['route' => [request()->route()->getAction()['as']],'method'=>'POST', 'id' => 'downloadBulkDocumentForm1','autocomplete'=>'off']) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
                                    <div class="row">
                                        <div class="input-field col s6">
                                            @php $lbl='नामांकन (Enrollment)'; $fld='enrollment'; @endphp
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
											{!!Form::text($fld,@$practical_examiner_data->$fld,['type'=>'text','class'=>'enrollment form-control','autocomplete'=>'off']); !!}
                                            @include('elements.field_error')
                                        </div>
										<div class="input-field col s6"> 
                                            @php $lbl='दस्तावेज़  प्रकार (Document Type)'; $fld='document_type'; @endphp
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$document_type_dropdown_arr, @$master->$fld, ['class' => 'document_type form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="row">
											<div class="col m10 s12 mb-3">
												<input type="hidden" name="formType" value="1" required>
                                                <button class="btn cyan waves-effect waves-light right submit_disabled" type="submit" name="action">Generate & Download
                                                <i class="material-icons right">send</i>
                                                </button>
                                            </div>
											<div class="col m2 s12 mb-3">
                                                <a href="{{ route('downloadBulkDocument') }}" class="btn cyan waves-effect waves-light right reset">Reset</a>
                                            </div>
                                        </div>
										
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				@endcan
				<!--  End Enrollment Wise Generate & Download -->
				
				<!-- 2 form -->
				<?php $document_type_dropdown_arr = array('1'=>'Marksheet-Pass','2'=>'Marksheet-Fail','3'=>'Certificate','4'=>'STR'); ?>
				<!--  AI Center Wise Download -->
				@can('bulk_documents2')
				<div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
                                <div id="html-view-validations">
								<h5 style="color:green">2. AI Center Wise Marksheet/Certificate/STR Download</h5>
								
								{{ Form::open(['route' => [request()->route()->getAction()['as']],'method'=>'POST', 'id' => 'downloadBulkDocumentForm2','autocomplete'=>'off']) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
                                    <div class="row">
                                       <div class="input-field col s12"> 
                                            @php $lbl='AI केंद्र (AI Center)'; $fld='ai_code'; 
											$aicenter_dropdown_download_arr->prepend('Select Ai Center','');
											
											@endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld.'[]',$aicenter_dropdown_download_arr, @$master->$fld, ['class' => 'ai_code form-control select2 browser-default center-align','autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div> 
										
										<div class="input-field col s4"> 
                                            @php $lbl='पाठ्यक्रम (Course)';  $placeholder = "Select ". $lbl; $fld='course'; @endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$course_dropdown_arr, @$master->$fld, ['class' => 'course form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="input-field col s4"> 
                                            @php $lbl='धारा (Stream)';  $placeholder = "Select ". $lbl; $fld='stream'; @endphp 
                                           <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$stream_dropdown_arr, @$master->$fld, ['class' => 'stream form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="input-field col s4"> 
                                            @php $lbl='दस्तावेज़  प्रकार (Document Type)'; $fld='document_type'; @endphp
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$document_type_dropdown_arr, @$master->$fld, ['class' => 'document_type form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="row">
											<div class="col m10 s12 mb-3">
												<input type="hidden" name="formType" value="2" required>
                                                <button class="btn cyan waves-effect waves-light right submit_disabled" type="submit" name="action">Download
                                                <i class="material-icons right">send</i>
                                                </button>
                                            </div>
											<div class="col m2 s12 mb-3">
                                                <a href="{{ route('downloadBulkDocument') }}" class="btn cyan waves-effect waves-light right reset">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				@endcan
				<!--  End AI Center Wise Download -->
				
				<!-- 3 form -->
				<!--  Start AI Center Wise Generate -->
				@can('bulk_documents3')
				<div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
                                <div id="html-view-validations">
								<h5 style="color:blue">3. AI Center Wise Marksheet/Certificate/STR Generate</h5>
								
								{{ Form::open(['route' => [request()->route()->getAction()['as']],'method'=>'POST', 'id' => 'downloadBulkDocumentForm3','autocomplete'=>'off']) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
                                    <div class="row">
                                       <div class="input-field col s12"> 
                                            @php $lbl='AI केंद्र (AI Center)'; $fld='ai_code'; @endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld.'[]',$aicenter_dropdown_arr, @$master->$fld, ['multiple'=>'multiple','class' => 'ai_code form-control select2 browser-default center-align','autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div> 
										
										<div class="input-field col s4"> 
                                            @php $lbl='पाठ्यक्रम (Course)';  $placeholder = "Select ". $lbl; $fld='course'; @endphp 
                                           <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$course_dropdown_arr, @$master->$fld, ['class' => 'course form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="input-field col s4"> 
                                            @php $lbl='धारा (Stream)';  $placeholder = "Select ". $lbl; $fld='stream'; @endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$stream_dropdown_arr, @$master->$fld, ['class' => 'stream form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="input-field col s4"> 
                                            @php $lbl='दस्तावेज़  प्रकार (Document Type)'; $fld='document_type'; @endphp
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$document_type_dropdown_arr, @$master->$fld, ['class' => 'document_type form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="row">
											<div class="col m10 s12 mb-3">
												<input type="hidden" name="formType" value="3" required>
                                                <button class="btn cyan waves-effect waves-light right submit_disabled" type="submit" name="action">Generate
                                                <i class="material-icons right">send</i>
                                                </button>
                                            </div>
											<div class="col m2 s12 mb-3">
                                                <a href="{{ route('downloadBulkDocument') }}" class="btn cyan waves-effect waves-light right reset">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				@endcan
				<!--  End AI Center Wise Generate -->
				
				<!-- 4 form -->
				<!--  Start Bulk Download -->
				@can('bulk_documents4')
				<div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
                                <div id="html-view-validations"> 
								<h5 style="color:green">4. Bulk Marksheet/Certificate/STR Download</h5>

								{{ Form::open(['route' => [request()->route()->getAction()['as']],'method'=>'POST', 'id' => 'downloadBulkDocumentForm4','autocomplete'=>'off']) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
                                    <div class="row">
                                       <div class="input-field col s4"> 
                                            @php $lbl='पाठ्यक्रम (Course)';  $placeholder = "Select ". $lbl; $fld='course'; @endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$course_dropdown_arr, @$master->$fld, ['class' => 'course form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="input-field col s4"> 
                                            @php $lbl='धारा (Stream)';  $placeholder = "Select ". $lbl; $fld='stream'; @endphp 
											<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$stream_dropdown_arr, @$master->$fld, ['class' => 'stream form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="input-field col s4"> 
                                            @php $lbl='दस्तावेज़  प्रकार (Document Type)'; $fld='document_type'; @endphp
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$document_type_dropdown_arr, @$master->$fld, ['class' => 'document_type form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="row">
											<div class="col m10 s12 mb-3">
												<input type="hidden" name="formType" value="4" required>
                                                <button class="btn cyan waves-effect waves-light right submit_disabled" type="submit" name="action">Download
                                                <i class="material-icons right">send</i>
                                                </button>
                                            </div>
											<div class="col m2 s12 mb-3">
                                                <a href="{{ route('downloadBulkDocument') }}" class="btn cyan waves-effect waves-light right reset">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>	
				@endcan
				<!--  End Bulk Download -->
				
				<!-- 5 form -->
				<!--  Start Bulk Generate -->
				@can('bulk_documents5')
			
				<div class="row">
                    <div class="col s12">
                        <div id="html-validations" class="card card-tabs">
                            <div class="card-content">
                                <div id="html-view-validations"> 
								<h5 style="color:blue">5. Bulk Marksheet/Certificate/STR Generate</h5>

								{{ Form::open(['route' => [request()->route()->getAction()['as']],'method'=>'POST', 'id' => 'downloadBulkDocumentForm5','autocomplete'=>'off']) }}
									{!! Form::token() !!}
									{!! method_field('PUT') !!}
									{!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
									<input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
                                    <div class="row">
                                       <div class="input-field col s4"> 
                                            @php $lbl='पाठ्यक्रम (Course)';  $placeholder = "Select ". $lbl; $fld='course'; @endphp 
											<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$course_dropdown_arr, @$master->$fld, ['class' => 'course form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="input-field col s4"> 
                                            @php $lbl='धारा (Stream)';  $placeholder = "Select ". $lbl; $fld='stream'; @endphp 
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$stream_dropdown_arr, @$master->$fld, ['class' => 'stream form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="input-field col s4"> 
                                            @php $lbl='दस्तावेज़  प्रकार (Document Type)'; $fld='document_type'; @endphp
                                            <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                            {!! Form::select($fld,$document_type_dropdown_arr, @$master->$fld, ['class' => 'document_type form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                            @include('elements.field_error') 
                                        </div>
										
										<div class="row">
											<div class="col m10 s12 mb-3">
												<input type="hidden" name="formType" value="5" required>
                                                <button class="btn cyan waves-effect waves-light right submit_disabled" type="submit" name="action">Generate
                                                <i class="material-icons right">send</i>
                                                </button>
                                            </div>
											<div class="col m2 s12 mb-3">
                                                <a href="{{ route('downloadBulkDocument') }}" class="btn cyan waves-effect waves-light right reset">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				@endcan
				<!--  End Bulk Generate -->




                <!-- 5 form -->
				<!--  Start Bulk Generate -->
				
			@can('bulk_documents6')
            <div class="row">
                <div class="col s12">
                    <div id="html-validations" class="card card-tabs">
                        <div class="card-content">
                            <div id="html-view-validations"> 
                            <h5 style="color:blue">6. Temp Custom Bulk Marksheet/Certificate/STR Generate</h5>

                            {{ Form::open(['route' => [request()->route()->getAction()['as']],'method'=>'POST', 'id' => 'downloadBulkDocumentForm5','autocomplete'=>'off']) }}
                                {!! Form::token() !!}
                                {!! method_field('PUT') !!}
                                {!! Form::hidden('ajax_validation',null,['type'=>'text','id'=>'ajax_validation','value'=>'1']); !!}
                                <input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
                                <div class="row">
                                   <div class="input-field col s4"> 
                                        @php $lbl='पाठ्यक्रम (Course)';  $placeholder = "Select ". $lbl; $fld='course'; @endphp 
                                        <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                        {!! Form::select($fld,$course_dropdown_arr, @$master->$fld, ['class' => 'course form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                        @include('elements.field_error') 
                                    </div>
                                    
                                    <div class="input-field col s4"> 
                                        @php $lbl='धारा (Stream)';  $placeholder = "Select ". $lbl; $fld='stream'; @endphp 
                                        <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                        {!! Form::select($fld,$stream_dropdown_arr, @$master->$fld, ['class' => 'stream form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                        @include('elements.field_error') 
                                    </div>
                                    
                                    <div class="input-field col s4"> 
                                        @php $lbl='दस्तावेज़  प्रकार (Document Type)'; $fld='document_type'; @endphp
                                        <h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
                                        {!! Form::select($fld,$document_type_dropdown_arr, @$master->$fld, ['class' => 'document_type form-control  select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off','required'=>'required']) !!}
                                        @include('elements.field_error') 
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col m10 s12 mb-3">
                                            <input type="hidden" name="formType" value="6" required>
                                            <button class="btn cyan waves-effect waves-light right submit_disabled" type="submit" name="action">Generate
                                            <i class="material-icons right">send</i>
                                            </button>
                                        </div>
                                        <div class="col m2 s12 mb-3">
                                            <a href="{{ route('downloadBulkDocument') }}" class="btn cyan waves-effect waves-light right reset">Reset</a>
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           @endcan
            <!--  End Bulk Generate -->
				
            </div>
        </div>
    </div>
</div>
    </div>

@endsection
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/download_bulk_document.js') !!}"></script> 
@endsection 

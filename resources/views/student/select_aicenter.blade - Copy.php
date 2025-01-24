@extends('layouts.guest')
@section('content')
<div class="">
  <div class="container"><div id="login-page" class="row login-page-fit-size">
     <div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
      <div id="basic-form" class="card card card-default scrollspy">
        <div class="card-content">
          <span class="card-title2" style="font-size:20px;font-weight:700;">छात्र पंजीकरण(Student Registration)</span>
          <form method="post" action="{{ route('self_registration') }}" id='self_re'>
              <input type="hidden" value="{{ $action_type}}" name ="action_type"/>
              <input type="hidden" name='ajaxRequest' value='1' id='ajaxRequest'>
			  @if(!empty($ssoid))
			   <input type="hidden" name='ssoid' value={{$ssoid}} readonly>
		      @endif
            {!! Form::token() !!}
              <div class="row">
                <div class="input-field col s12">
                    <span class="tehsil_id_section"> 
                    @php $lbl='चयन जिला (Select District):'; $placeholder = "Select ". $lbl; $fld='district_id'; @endphp
                    <h8>@php echo $lbl.Config::get('global.starMark');@endphp </h8>
					@php
                    	$modelContent = '<center><h4>Select the district to find AI Center.(एआई केंद्र खोजने के लिए जिले का चयन करें।)</h4></center>';
                  	@endphp
                  	<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span> 
                    {!! Form::select($fld,$district_list,null, ['id' => $fld,'class' => 'form-control district_id select2 browser-default center-align location','placeholder' =>$lbl,'autocomplete'=>'off']) !!}
                    @include('elements.field_error')
                    </span>
                </div>
              </div>
              <div class="row">
                <div class="input-field col s12">
                  <span class="tehsil_id_section" >
                  @php $lbl='चयन ब्लॉक (Select Block):'; $placeholder = "Select ". $lbl; $fld='block_id'; @endphp
                  
                   
                  <h8>@php echo $lbl.Config::get('global.starMark') ; @endphp </h8>

					@php
                    	$modelContent = '<center><h4>Select the block to find AI Center.(एआई केंद्र खोजने के लिए ब्लॉक का चयन करें।)</h4></center>';
                  	@endphp
                  	<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>

                  {!! Form::select($fld,$blockdetails,null, ['id' => $fld,'class' => 'form-control block_id select2 browser-default center-align location','placeholder' =>$lbl,'autocomplete'=>'off','required'=>false]) !!}
                  @include('elements.field_error')
                </span>
                  
                </div>
              </div>
              <div class="row">
                <div class="input-field col s12">
                  <span class="tehsil_id_section" >
                  @php $lbl='एआई केंद्र (AI Centre):'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
                  
                  <!-- @php $toolTipInfo= '<span  class="tooltipped customTooltip" data-position="right" data-tooltip="Select the AI Center.(एआई केंद्र का चयन करें।)"><i class="material-icons mr-2"> info_outline </i></span>'; @endphp -->
                  <h8>@php echo $lbl.Config::get('global.starMark') ; @endphp </h8>

					@php
						$modelContent = '<center><h4>Select the AI Center.(एआई केंद्र का चयन करें।)</h4></center>';
					@endphp
					<span class="waves-effect waves-light  modal-trigger modalCls" data-content="{{ $modelContent }}"><i class="material-icons mr-2"> info_outline </i></span>

				
                  {!! Form::select($fld,$blockdetails,null, ['id' => $fld,'class' => 'form-control aicode select2 browser-default center-align aicenter','placeholder' =>$lbl,'autocomplete'=>'off','required'=>false]) !!}
                  @include('elements.field_error')
                  </span>
                </div>
              </div>
              <div class="input-field col s12 hide" id="aiCenterMsg">
                <span class="get_Aicnetervalue">
                </span>
              </div>
            </div>
            
            <div class="row">
			 <div class="step-actions right">
										  <div class="row">
											<div class="col m5 s12 mb-3">
											  <a href="{{ route('landing') }}" class="btn btn-xs btn-info waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text " style="">Cancel</a> 
											</div>
											<div class="col m1 s12 mb-3">
											  <button class="btn cyan waves-effect waves-light" type="submit" name="action">Continue
											  <i class="material-icons right">send</i>
											</button>
											</div>
										  </div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
	@endsection 
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/select_aicenter.js') !!}"></script> 
@endsection 
<style>
    .get_Aicnetervalue{
      font-size:20px;
      font-weight:700;
      color:blue;
    }
  </style>
<div>
	<fieldset>
		<legend>पत्राचार का पता (Correspondence Address)</legend>
		<div class="row">
			<div class="input-field col s12 l12">
				<label style="color:blue;">
					@php
						$fld='is_both_same';
                        $checked=false; 
                        if(@$master->$fld==1){
							$checked=true;
                        }
					@endphp
					{{ Form::checkbox($fld,null,$checked,array('id'=>'is_both_sameadress')) }}
				   <span style="color:blue;">
					   @php 
							echo $lbl='यदि स्थायी और पत्राचार पता समान है तो कृपया चेकबॉक्स की जांच करें। (if the permanent and correspondence address are same please check the checkbox.)';
						@endphp  
				   </span>
              </label>
			</div>
		</div>
		<br><br>
		<div class="row">
			<div class="input-field col m6 s12">
				@php $lbl='पता पंक्ति 1 (Address Line 1)'; $fld='current_address1'; @endphp
				<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
				{!!Form::text($fld,@$master->$fld,['class'=>'currentInput currentCheckBrowserIssue form-control','autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
			</div>
			<div class="input-field col m6 s12">
				@php $lbl='पता पंक्ति 2 (Address Line 2)'; $fld='current_address2'; @endphp
				<h8>{!!Form::label($fld, $lbl) !!}</h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput checkBrowserIssue2 form-control','maxlength'=>70,'minLength'=>2,'autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
			</div>
		</div>

		<div class="row">
			<div class="input-field col m6 s12">
				@php $lbl='पता पंक्ति 3 (Address Line 3)'; $fld='current_address3'; @endphp
				<h8>{!!Form::label($fld, $lbl) !!}</h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput checkBrowserIssue3 form-control','maxlength'=>70,'minLength'=>2,'autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
			</div>
			<div class="input-field col m6 s12">
				@php $lbl='चयन राज्य (State)'; $placeholder = "Select ". $lbl; $fld='current_state_id'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!! Form::select($fld,$state_list,@$master->$fld, ['id' => $fld,'class' => 'form-control currentInput current_state_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off', 'id' => $fld]) !!}
				{{-- {!!  Form::label($fld, $lbl) !!} --}}
				@include('elements.field_error')
			</div>
		</div>

		<div class="row">
			<div class="input-field col m6 s12">
				@php $lbl='चयन जिला (District):'; $placeholder = "Select ". $lbl; $fld='current_district_id'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!! Form::select($fld,$district_list,@$master->$fld, ['id' => $fld,'class' => 'form-control currentInput current_district_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off', 'id' => $fld]) !!}
				{{-- {!!  Form::label($fld, $lbl) !!} --}}
				@include('elements.field_error')
			</div>
			
			<div class="input-field col m6 s12"> 
				@php  if(!empty($master) && $master->state_id==6){  @endphp
				<span class="current_tehsil_id_section" >
				@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='current_tehsil_id'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!! Form::select($fld,$tehsil_list, @$master->$fld, ['id' => $fld,'class' => 'form-control currentInput current_tehsil_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off', 'id' => $fld]) !!}
				@include('elements.field_error')
				</span>
				
				<span class="current_tehsil_name_section"  style="display:none" >
				@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='current_tehsil_name'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','id' => $fld,'class'=>'currentInput form-control current_tehsil_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
				</span>
				
				@php  } else if(!empty($master) && $master->state_id !=6){ @endphp
				<span class="current_tehsil_id_section" style="display:none" >
				@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='current_tehsil_id'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!! Form::select($fld,$tehsil_list, @$master->$fld, ['class' => 'form-control currentInput current_tehsil_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off', 'id' => $fld]) !!}
				@include('elements.field_error')
				</span>
				
				<span class="current_tehsil_name_section" >
				@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='current_tehsil_name'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput form-control current_tehsil_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off']); !!}
				@include('elements.field_error')
				</span>
				
				@php  } else { @endphp
				<span class="current_tehsil_id_section" >
				@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='current_tehsil_id'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!! Form::select($fld,$tehsil_list, @$master->$fld, ['class' => 'form-control currentInput current_tehsil_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off', 'id' => $fld]) !!}
				@include('elements.field_error')
				</span>
				
				<span class="current_tehsil_name_section"  style="display:none" >
				@php $lbl='चयन  तहसील (Tehsil):'; $placeholder = "Select ". $lbl; $fld='current_tehsil_name'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput form-control current_tehsil_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off']); !!}
				@include('elements.field_error')
				</span>
				@php } @endphp
			</div>
			
		</div>
		<div class="row">
			<div class="input-field col m6 s12">
				@php  if(!empty($master) && $master->state_id==6){  @endphp
				<span class="current_block_id_section" >
				@php $lbl='चयन ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='current_block_id'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!! Form::select($fld,$block_list,@$master->$fld, ['class' => 'form-control currentInput current_block_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off', 'id' => $fld]) !!}
				@include('elements.field_error')
				</span>
				
				<span class="current_block_name_section" style="display:none" >
				@php $lbl='चयन  ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='current_block_name'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput form-control current_block_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
				</span>
				
				@php } else if(!empty($master) && $master->state_id !=6){  @endphp
				<span class="current_block_id_section" style="display:none" >
				@php $lbl='चयन ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='current_block_id'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!! Form::select($fld,$block_list, @$master->$fld, ['class' => 'form-control currentInput current_block_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off', 'id' => $fld]) !!}
				@include('elements.field_error')
				</span>
				
				<span class="current_block_name_section" >
				@php $lbl='चयन  ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='current_block_name'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput form-control current_block_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
				</span>
				
				@php  } else {  @endphp
				<span class="current_block_id_section" >
				@php $lbl='चयन ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='current_block_id'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!! Form::select($fld,$block_list, @$master->$fld, ['class' => 'form-control currentInput current_block_id select2 browser-default center-align','placeholder' =>$lbl,'autocomplete'=>'off', 'id' => $fld]) !!}
				@include('elements.field_error')
				</span>
				
				<span class="current_block_name_section" style="display:none" >
				@php $lbl='चयन  ब्लॉक (Block):'; $placeholder = "Select ". $lbl; $fld='current_block_name'; @endphp
				<h8>@php echo $lbl.Config::get('global.starMark'); @endphp </h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput form-control current_block_name','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
				</span>
				@php  }  @endphp
				
			</div>
			
			<div class="input-field col m6 s12">
				@php $lbl='चयन शहर/गाँव (City/Village)'; $fld='current_city_name'; @endphp
				<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput txtOnly form-control','maxlength'=>30,'minLength'=>2, 'autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
			</div>
		</div>
		<div class="row">
			<div class="input-field col m6 s12">
				@php $lbl='पिन कोड (Pincode)'; $fld='current_pincode'; @endphp
				<h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>
				{!!Form::text($fld,@$master->$fld,['type'=>'text','class'=>'currentInput form-control num','maxlength'=>6,'minLength'=>6,'autocomplete'=>'off', 'id' => $fld]); !!}
				@include('elements.field_error')
			</div>
		</div> 
	</fieldset>
</div>
	

 


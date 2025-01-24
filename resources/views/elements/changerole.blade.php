@php use App\Helper\CustomHelper; 
	$changerole = CustomHelper::_changerole(); 
	$selected_role = CustomHelper::_getCurrentLoginRole(); 	
@endphp
{{ Form::open(['id' => "roleChangeForm"]) }}
{!! Form::token() !!}
{!! method_field('PUT') !!}
        <div class="center">
			@php $lbl='Swtich Role'; $placeholder = "Select ". $lbl; $fld='current_change_role'; @endphp
			{!! Form::select($fld,@$changerole, @$selected_role, ['class' => 'form-control current_change_role select2 select2a browser-default center-align','placeholder' =>$lbl]) !!}
			@include('elements.field_error')
		</div>
{{ Form::close() }} 
<script src="{!! asset('public/app-assets/js/bladejs/current_change_role.js') !!}"></script> 
 


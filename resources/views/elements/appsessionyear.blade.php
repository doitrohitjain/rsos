@php use App\Helper\CustomHelper; 
	$admission_sessions = CustomHelper::_get_admission_sessions();
	$selected_session = CustomHelper::_get_selected_sessions(); 
	
	
@endphp
{{ Form::open(['id' => "sessionForm"]) }}
{!! Form::token() !!}
{!! method_field('PUT') !!}
        <div class="center">
			@php $lbl='सत्र चयन (Session)'; $placeholder = "Select ". $lbl; $fld='current_session'; @endphp
			{!! Form::select($fld,@$admission_sessions->toArray(),@$selected_session, ['class' => 'form-control current_session select2 select2a browser-default center-align','placeholder' =>$lbl]) !!}
			@include('elements.field_error')
		</div>
		<!-- <li class="active bold"><a class=""><span>&nbsp;&nbsp;&nbsp;&nbsp;Selected Session : {{ @$admission_sessions[$selected_session] }}</span></a>
		</li> -->
{{ Form::close() }} 
<script src="{!! asset('public/app-assets/js/bladejs/current_session.js') !!}"></script> 
 



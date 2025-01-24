
<div class="row">
	<div class="input-field col s4">
	
	{{ Form::open(['url'=>url()->current()])  }}
								{!! Form::token() !!}
								{{ method_field('get')  }}
	@php 


      	$paginatevalue = Session::get('defaultPageLimit');
		if(@$paginatevalue){
			$defaultvalue = $paginatevalue;
		}else{
			$defaultvalue = config("global.defaultPageLimit");
		}
		echo config("global.defaultPageLimit");
		@endphp

		@php $lbl='Limit'; $fld='pagevalue'; @endphp
		{{-- <h8>{!!Form::label($fld, $lbl) !!}@php echo Config::get('global.starMark'); @endphp</h8>	 --}}
		{!!Form::select($fld,$pagalimit,$defaultvalue,['type'=>'text','class'=>'form-control num paginator','autocomplete'=>'off' ,'placeholder'=>'Limit']); !!}
		@include('elements.field_error')
		{{ Form::close() }}
	</div> 
</div>
<script>
$(document).ready(function() {
    $('.paginator').on('change',function(){
        var pagntorvalue = this.value;
		$.ajax({
				url: config.routes.setPagntorValue,
				type: "get",
				data: {'value': pagntorvalue},
				dataType : 'json',
				success: function (result){
					location.reload();
					// console.log(result);
					// return false;
				},
			});
		
		});
	});
</script>


			
								 

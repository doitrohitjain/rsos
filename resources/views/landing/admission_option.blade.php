@extends('layouts.logindefault')
@section('content')

<div class="container"><div id="login-page" class="row">
	<div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
   	<div class="row">
		<div class="input-field col s12">
			<h5 class="ml-4">Select the correct login authority</h5>
		  </div>
		</div>
		
	  <div id="view-radio-buttons">
		  {{ Form::open(['route' => [request()->route()->getAction()['as']], 'class' => "login-form"]) }}
		  {!! Form::token() !!}
		   @foreach(@$result as $results)
		  
			<p class="mb-1">
			 <label>
			   <input name="option_id" type="radio" value="{{@$results->option_id}}">
			   <span style="font-weight: bold;font-size:20px;">{{@$results->option_val}}</span>
			 </label>
		   </p>
		   @endforeach<br>
		   <div class="row">
				
			   	<!-- <div class="col m2 s12 mb-3">
				 <button class="btn cyan waves-effect waves-light " type="submit" name="action">Submit
				   <i class="material-icons right">send</i>
				 </button>
			   </div>
			   <div class="col m2 s12 mb-3">
				 <a href="{{ route('landing') }}" class="btn btn-xs btn-info right">BacK</a></h6>
</div>
			 </div> -->
			 <div class="col m s12 mb-3">
				 <a href="javascript:void(none);" class="btn btn-xs btn-warning center backcomingsoon">BacK</a>
			 <button class="btn cyan waves-effect waves-light  btn btn-xs btn-warning " type="submit" name="action">Submit
				   <i class="material-icons right">send</i>
				 </button>

				 </div>
		{{ Form::close() }}
	   </div>
	   
	   
	   
	   
	</div>
  </div>
		  </div>
@endsection


@section('customjs')
<!-- <script>
$('.backcomingsoon').on('click', function (event) {
    event.preventDefault();
    const url = $(this).attr('href');
    swal({
        title: 'Are you sure?',
        text: '',
        icon: 'warning',
        buttons: ["Cancel", "Yes!"],
    }).then(function(value) {
        if (value) {
            // window.location.href = url;
        }
    });
});
</script> -->
@endsection

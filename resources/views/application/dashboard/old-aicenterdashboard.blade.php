@extends('layouts.logindefault')
@section('content')


<!-- Radio Buttons -->
<div class="row">
  <div class="col s12">
    <div id="radio-buttons" class="card card-tabs">
      <div class="card-content">
        <div class="card-title">
          <div class="row">
            <div class="col s12 m6 l10">
              <h4 class="card-title">select  login</h4>
            </div>
           
          </div>
        </div>
        <div id="view-radio-buttons">
           {{ Form::open(['route' => [request()->route()->getAction()['as']]]) }}
           {!! Form::token() !!}
	        @foreach($result as $results)
             <p class="mb-1">
              <label>
                <input name="role" type="radio" value="{{@$results->role_id}}">
                <span>Radio-1</span>
              </label>
            </p>
			@endforeach<br>
			<div class="row">
                <div class="col m2 s12 mb-3">
				  <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>
         {{ Form::close() }}
        </div>
   
      </div>
    </div>
  </div>
</div>
@endsection


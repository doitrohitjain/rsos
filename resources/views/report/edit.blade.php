@extends('layouts.default')
@section('content')
   <div id="main">
      <div class="row">
	     <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Form</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Form</a>
                  </li>
                  <li class="breadcrumb-item active">Form
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
		<div class="col s12">
        <div class="container">
        <div class="seaction">
		<div class="card">
			<div class="card-content">
	 <h6><a href="{{ route('reports.index') }}" class="btn btn-xs btn-info right">Back</a></h6>
      <h6>Queries Editer <h6>
	 </div>
  </div>
<!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
          <h4 class="card-title">Queries Editer</h4>
		  {{ Form::open(['route' => ['reports.update', $masterquerieexcel->id]]) }}
          {!! Form::token() !!}
		  {{ method_field('PUT') }}
            <div class="row">
            </div>

            <div class="input-field col m12 s12">
              {!! Form::select('is_link',$yesno, @$masterquerieexcel->is_link, ['class' => 'form-control', 'placeholder' => 'Please Select']) !!}
              {!!Form::label('Is Link', 'Is Link') !!}
              @error('is_link')
                <span class="invalid-feedback" role="alert" style="color:red;">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>

			 <div class="input-field col m12 s12">
			  {!! Form::select('status',$yesno, @$masterquerieexcel->status, ['class' => 'form-control', 'placeholder' => 'Please Select']) !!}
			   {!!Form::label('Status', 'Status') !!}
				@error('status')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
			  <div class="input-field col m12 s12">
			  {!! Form::select('pdf',$yesno, @$masterquerieexcel->pdf, ['class' => 'form-control', 'placeholder' => 'Please Select']) !!}
			   {!!Form::label('pdf', 'PDF') !!}
				@error('pdf')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
			  <div class="input-field col m12 s12">
			   {!! Form::select('excel',@$yesno,@$masterquerieexcel->excel,['class' => 'form-control', 'placeholder' => 'Please Select']) !!}
			   {!!Form::label('excel', 'Excel') !!}
				@error('excel')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
			  <div class="input-field col m12 s12">
              {!!Form::label('Title', 'Title') !!}
              {!!Form::text('title',@$masterquerieexcel->title,['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
				@error('title')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
              <div class="input-field col m12 s12">
                {!!Form::label('Link Text', 'Link Text') !!}
                {!!Form::text('link_text',@$masterquerieexcel->link_text,['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
                @error('link_text')
                  <span class="invalid-feedback" role="alert" style="color:red;">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>
              <div class="input-field col m12 s12">
					{!!Form::label('Serial Number', 'Serial Number') !!}
					{!!Form::text('serial_number',@$masterquerieexcel->serial_number,['type'=>'text','class'=>'form-control num', 'autocomplete'=>'off']); !!}
					@error('serial_number')
						<span class="invalid-feedback" role="alert" style="color:red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			   <div class="input-field col m12 s12">
				{!! Form::textarea('text',@$masterquerieexcel->text, array('class'=>'form-control', 
                    'rows' => 50, 'cols' => 10, 'style' => 'height:20rem;','placeholder'=>'Queries Editer comment')) !!}
					@error('text')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
			   </div>
			    <div class="row">
			   <div class="col m10 s12 mb-3">
                      <button class="btn cyan waves-effect waves-light right" type="reset">
                        <i class="material-icons right">clear</i>Reset
                      </button>
                    </div>
                <div class="col m2 s12 mb-3">
				  <button class="btn cyan waves-effect waves-light right" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>
        </div>
		 {{ Form::close() }}
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
@endsection








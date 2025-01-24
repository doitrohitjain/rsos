@extends('layouts.default')
@section('content')
    <div id="main">
      <div class="row">
	      <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Add Document</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  
                  </li>
                  <li class="breadcrumb-item active">Add Document
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
	 <h6><a href="{{ route('alldocumentlist') }}" class="btn btn-xs btn-info right">Back</a></h6>
      <h6> Add Document<h6>
	 </div>
  </div>
<!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
          <!-- <h4 class="card-title">Form Document</h4> -->
           <form action="{{ route('alldocumentstore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
            </div>
				<div class="input-field col m12 s12">
					{!! Form::select('is_link',@$yesno, null, ['class' => 'form-control', 'placeholder' => 'Please Select']) !!}
					{!!Form::label('Is Link', 'Is Link') !!}
					@error('is_link')
						<span class="invalid-feedback" role="alert" style="color:red;">
						<strong>{{ $message }}</strong>
					</span>
					@enderror
				</div>
				<div class="input-field col m12 s12">
					{!! Form::select('status',$yesno, null, ['class' => 'form-control', 'placeholder' => 'Please Select']) !!}
					{!!Form::label('Status', 'Status') !!}
					@error('status')
						<span class="invalid-feedback" role="alert" style="color:red;">
						<strong>{{ $message }}</strong>
					</span>
					@enderror
				</div>
			  <div class="input-field col m12 s12">
			  {!! Form::select('doc_type',$documentype, null, ['class' => 'form-control', 'placeholder' => 'Please Select']) !!}
			   {!!Form::label('DOC Type', 'DOC Type') !!}
				@error('doc_type')
                <span class="invalid-feedback" role="alert" style="color:red;">
                      <strong>{{ $message }}</strong>
                  </span>
               @enderror
              </div>
			  	<div class="input-field col m12 s12">
					{!!Form::label('Title', 'Title') !!}
					{!!Form::text('title',old('title'),['type'=>'text','class'=>'form-control', 'autocomplete'=>'off']); !!}
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
					{!!Form::text('serial_number',null,['type'=>'text','class'=>'form-control num', 'autocomplete'=>'off']); !!}
					@error('serial_number')
						<span class="invalid-feedback" role="alert" style="color:red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>

				<div class="input-field col m12 s12">
					{!!Form::label('Text', 'Text') !!}
					{!! Form::textarea('text',null, array('class'=>'form-control', 
                    'rows' => 20, 'cols' => 5, 'style' => 'height:10rem;')) !!}
					@error('text')
						<span class="invalid-feedback" role="alert" style="color:red;">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			   <div class="input-field col m12 s12">
				 <div class="file-field input-field">
        <div class="btn">
        <span>Documents File upload</span>
        <input type="file" name="document">
      </div>
      <div class="file-path-wrapper">
        <input class="file-path validate" type="text" disabled>
      </div>
    </div>
       @error('document')
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
              </form>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
@endsection







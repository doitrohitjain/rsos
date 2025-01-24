@extends('layouts.default')
@section('content')
    <div id="main">
      <div class="row">
        <div id="breadcrumbs-wrapper" data-image="{{asset('public/app-assets/images/gallery/breadcrumb-bg.jpg')}}">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Form Layouts</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                 <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item"><a href="#">Form</a>
                  </li>
                  <li class="breadcrumb-item active">Form Layouts
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
   <h6><a href="{{ route('supp_masterstudent') }}" class="btn btn-xs btn-info right">Back</a></h6>
      <h6>Form Supplementary Student Delete Reasons <h6>
   </div>
  </div>
     
<!-- Form Advance -->
    <div class="col s12 m12 l12">
      <div id="Form-advance" class="card card card-default scrollspy">
        <div class="card-content">
          <h4 class="card-title">Form Supplementary Student Delete Reasons</h4>
      {{ Form::open(['route' => ['suppstudentrejectdelete', $student_id]]) }}
      {!! Form::token() !!}
      {{ method_field('PUT') }}
            <div class="row">
            </div>
        <div class="input-field col m12 s12">
          {!! Form::select('deleted_reason',@$student_delete_reasons, @$alldocumentedit->is_link, ['class' => 'select2 browser-default form-control', 'placeholder' => 'Please Select Deleted Reason']) !!}
        
          @error('deleted_reason')
            <span class="invalid-feedback" role="alert" style="color:red;">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
        <div class="input-field col m12 s12">
          {!! Form::textarea('remarks',null, array('class'=>'form-control', 
                    'rows' => 20, 'cols' => 5, 'style' => 'height:10rem;', 'placeholder' => 'TEXT')) !!}
          @error('remarks')
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
              {{ Form::close() }}
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
@endsection







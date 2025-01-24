@extends('layouts.default')
@section('content')

<div id="main">
	<div class="row">
	  <div id="breadcrumbs-wrapper" data-image="../public/app-assets/images/gallery/breadcrumb-bg.jpg">
			<!-- Search for small screen-->
			<div class="container">
				<div class="row">
					<div class="col s12 m6 l6">
						<h5 class="breadcrumbs-title mt-0 mb-0"><span>{{ $title }}</span></h5>
					</div>
					<div class="col s12 m6 l6 right-align-md">
						<ol class="breadcrumbs mb-0"> 
							@foreach($breadcrumbs as $v)
								<li class="breadcrumb-item"><a href="{{ $v['url'] }}">{{ $v['label'] }}</a></li>
							@endforeach 
						</ol>
					</div>
				</div>
			</div>
        </div>
        <br>
            <br>
      <div id="tap-target" class="card card-tabs">
        <div class="card-content">
             {!! Form::open(array('route' => 'downloadhallticketbulviewpdfenrollment','method'=>'POST')) !!}
            <div class="row">
              <div class="row">
             @if($user_role == $developeradminrole || $user_role == $superadminrole)
        		<div class="col m3 s12">
                @php $lbl='Ai code'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$aiCenters,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'ai_code']) !!}
                @include('elements.field_error')
            </div>
            </div>
            @elseif($user_role == $aicenterrole)
                @php $lbl='Ai code'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
               {!!Form::hidden($fld,@$ai_codes,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
            @endif

             <div class="col m3 s12">
                @php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'course']) !!}
                @include('elements.field_error')
            </div>
            </div>
             <div class="col m3 s12">
                @php $lbl='Stream'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'stream']) !!}
                @include('elements.field_error')
            </div>
            </div>
            <div class="col m3 s12">
                @php $lbl='enrollment'; $fld='enrollment'; @endphp 
                <span class="small_lable">@php echo $lbl; @endphp </span>
                <div class="input-field">
                {!!Form::text($fld,null,['type'=>'text','class'=>'form-control','autocomplete'=>'off','placeholder' => $lbl]); !!}
                @include('elements.field_error')    
                </div>
            </div>
          </div>
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-2">
            <button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Submit
            </button>
          </div>
          <div class="col m2 s12 mb-2">
            <a href="{{route('studentchecklists')}}" class="btn cyan waves-effect waves-light right">Reset </a>
          </div>
        </div> 
   {{ Form::close() }}
      </div>
  </div>
   @if($user_role == $developeradminrole)
		<div id="tap-target" class="card card-tabs">
           <div class="card-content">
           	<div class="row">
            </div>
            <div class="row">
              <div class="col s12 center">
			    <a href="{{ route('hall_ticket_bulk_downloads_all',[10,1,0]) }}" class="btn mt-2"> Board NR Hall Ticket 10 stream-1 Generate</a>
                &nbsp;
                <a href="{{ route('hall_ticket_bulk_downloads_all',[12,1,0]) }}" class="btn mt-2"> Board NR Hall Ticket 12 stream-1 Generate</a>
				</div>
				 <div class="col s12 center">
			    <a href="{{ route('hall_ticket_bulk_downloads_all',[10,2,0]) }}" class="btn mt-2"> Board NR Hall Ticket 10 stream-2 Generate</a>
                &nbsp;
                <a href="{{ route('hall_ticket_bulk_downloads_all',[12,2,0]) }}" class="btn mt-2"> Board NR Hall Ticket 12 stream-2 Generate</a>
				</div>
              </div>
            </div>
        </div>
        @endif
      </div>
	</div>
</div>
@endsection

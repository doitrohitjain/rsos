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
	<div class="col s12 m12">
      <div id="Form-advance" class="card card card-default">
		<h4 class="header">&nbsp;<span style="color:green;">Download Student Checklist Ai Code Wise </span></h4>
        <div class="card-content">
             {!! Form::open(array('route' => 'getDownloadtocchecklistsingleaicode','method'=>'POST')) !!}
              <div class="row">
              <div class="row">
              <div class="col m3 s12">
                @php $lbl='Ai code'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$aiCenters,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                @include('elements.field_error')
				<input type="hidden" name="type" value="33">
            </div>
            </div>
             <div class="col m3 s12">
                @php $lbl='Course'; $placeholder = "Select ". $lbl; $fld='course'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$courses,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                @include('elements.field_error')
            </div>
            </div>
             <div class="col m3 s12">
                @php $lbl='Stream'; $placeholder = "Select ". $lbl; $fld='stream'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$stream_id,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                @include('elements.field_error')
            </div>
            </div>
			<div class="col m3 s12">
                @php $lbl='Late Fees'; $placeholder = "Select ". $lbl; $fld='late_fee'; @endphp
                <span class="small_lable">@php echo $lbl  @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$latefees,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                @include('elements.field_error')
            </div>
            </div>
			
          </div>
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-3">
            <button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Download
            </button>
          </div>
          <div class="col m2 s12 mb-3">
            <a href="{{route('studentchecklists')}}" class="btn cyan waves-effect waves-light right">Reset </a>
          </div>
        </div> 
   {{ Form::close() }}
      </div>
  </div>
      <div id="tap-target" class="card card-tabs">
	  <h4 class="header">&nbsp;<span style="color:blue;"> Generate Student Checklist Ai Code Wise </span></h4>
        <div class="card-content">
             {!! Form::open(array('route' => 'downloadstudentchecklistsPdf1','method'=>'POST')) !!}
			 @csrf
            <div class="row">
              <div class="row">
            <div class="col m3 s12">
                @php $lbl='Ai code'; $placeholder = "Select ". $lbl; $fld='ai_code'; @endphp
                <span class="small_lable">@php echo $lbl .Config::get('global.starMark'); @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$aiCenters,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder,'id'=>'ai_code']) !!}
                @include('elements.field_error')
            </div>
            </div>
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
                @php $lbl='Late Fees'; $placeholder = "Select ". $lbl; $fld='late_fee'; @endphp
                <span class="small_lable">@php echo $lbl; @endphp </span>
                <div class="input-field">
                {!! Form::select($fld,@$latefees,null,['class' => 'select2 browser-default form-control center-align allstreamcourseadmtype admtype admtypes','placeholder' => $placeholder]) !!}
                @include('elements.field_error')
            </div>
            </div>
          </div>
        </div><br>
        <div class="row">
           <div class="col m10 s12 mb-3">
            <button class="btn cyan waves-effect waves-light right  " type="submit" name="action"> Generate
            </button>
          </div>
          <div class="col m2 s12 mb-3">
            <a href="{{route('studentchecklists')}}" class="btn cyan waves-effect waves-light right">Reset </a>
          </div>
        </div> 
   {{ Form::close() }}
      </div>
  </div>
         	
<div id="tap-target" class="card card-tabs">
        <div class="card-content">
		 <h4 class="header">&nbsp;<span style="color:green;">Bulk Download Student Checklist </span></h4>
            <div class="row">
              <div class="col s12 center">
            
                <a href="{{ route('getDownloadtocchecklistzipdownload',[10,1,33]) }}" class="btn mt-2">Download Student Checklist(Stream1-10th)</a>
                &nbsp;
                  <a href="{{ route('getDownloadtocchecklistzipdownload',[10,2,33]) }}" class="btn mt-2" >Download Student Checklist(Stream2-10th)</a>
                &nbsp;
                </div>
                <div class="col s12 center">
                 <a href="{{ route('getDownloadtocchecklistzipdownload',[12,1,33]) }}" class="btn mt-2">Download Student Checklist(Stream1-12th)</a>
                &nbsp;
                 <a href="{{ route('getDownloadtocchecklistzipdownload',[12,2,33]) }}" class="btn mt-2" >Download Student Checklist(Stream2-12th)</a>
              </div>
            </div>
        </div>
      </div>
  <div id="tap-target" class="card card-tabs">
        <div class="card-content">
		<h4 class="header">&nbsp;<span style="color:blue;">Bulk Generate Student Checklist</span></h4>
            <div class="row">
              <div class="col s12 left"> 
				 @php $counter = 0; @endphp
				@foreach(@$exportBtnArr as $k => $v)
					@php
					$counter++;
					$title = @$v['title']; @endphp
					
				
				@if($counter == 1 || $counter == 5 || $counter == 9  || $counter == 13  )
					<fieldset>
					<legend><b><h5>
					Generate Student Checklist (Stream : {{ @$v['stream'] }} - Course: {{ @$v['course'] }}<sup>th</sup>) 
							</b></h5>	</legend>
				@endif
						<form method="post" action="{{ route(@$exportBtn[0]['url'])}}">
							@CSRF
							<input type="hidden" name="course" value="{{ @$v['course'] }}"/>
							<input type="hidden" name="stream" value="{{ @$v['stream'] }}"/>
							<input type="hidden" name="ai_code" value="{{ @$v['ai_code'] }}"/>
							<input type="hidden" name="late_fee" value="{{ @$v['latefee'] }}"/>
							<div class="col m6 s12 mb-2">
							<button class="btn cyan waves-effect waves-left " type="submit" name="action" title="{{ @$title }}"> 
								Generate Student Checklist  
								@if($v['latefee'] !== null && $v['latefee'] >= 0)
									(  late fee- {{ @$v['latefee'] }}  )
								@else
									ALL
								@endif
							</button>
							</div>
						</form>
				@if($counter %4 == 0) </fieldset>	@endif
			   @endforeach
			   
			   
              </div>
            </div>
        </div>
      </div>



   
      

        
        <!--<div class="section section-data-tables"> 
            <div class="row">
            	<div class="col s12">
            		<div class="card">
            			<div class="card-content">
            				
            			</div>
            		</div>
            	</div>
            </div>
            </div>
            <div class="section section-data-tables"> 
            <div class="row">
            	<div class="col s12">
            		<div class="card">
            			<div class="card-content">
            				<div class="row"> 
            					
            				</div>
            			</div>
            		</div>
            	</div>
            </div>
            </div>-->

            <!-- @foreach(@$aiCenters as $k => $v) 
                @if(@$k)  
                    <table>
                        <tr>
                            <td><span class="btn mt-2">{{ $k }}</span></td>   
                            <td><a href="{{ route('downloadstudentchecklistsPdf',[10,1,$k,0]) }}" class="btn mt-2">Stream1 - 10th</a></td>   
                            <td><a href="{{ route('downloadstudentchecklistsPdf',[10,2,$k,0]) }}" class="btn mt-2" > Stream2 - 10th</a></td>   
                            <td> <a href="{{ route('downloadstudentchecklistsPdf',[12,1,$k,0]) }}" class="btn mt-2"> Stream1 - 12th</a></td>   
                            <td><a href="{{ route('downloadstudentchecklistsPdf',[12,2,$k,0]) }}" class="btn mt-2" > Stream2 - 12th</a></td>   
                        </tr>
                    </table>  
                @endif
            @endforeach -->

        <div class="content-overlay"></div>
    </div>
</div>
</div>
</div> 
@endsection
@section('customjs')
<script src="{!! asset('public/app-assets/js/bladejs/reporting_student_application.js') !!}"></script> 
@endsection
@extends('layouts.default')
@section('content')
 <div id="main">
      <div class="row">
        <div id="breadcrumbs-wrapper" data-image="public/app-assets/images/gallery/breadcrumb-bg.jpg">
          <!-- Search for small screen-->
          <div class="container">
            <div class="row">
              <div class="col s12 m6 l6">
                <h5 class="breadcrumbs-title mt-0 mb-0"><span>Master Document</span></h5>
              </div>
              <div class="col s12 m6 l6 right-align-md">
                <ol class="breadcrumbs mb-0">
                  <li class="breadcrumb-item"><a href="index-2.html">Home</a>
                  </li>
                  <li class="breadcrumb-item active">Master Document 
                  </li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <div class="col s12">
          <div class="container">
            <div class="section section-data-tables">
			        <!-- <div class="card">
			          <div class="card-content">
                  @php
                    $route = Route::current()->getActionName();
                  @endphp
	                <h6><a href="{{route('createalldocument')}}" class="btn btn-xs btn-info right">ADD Master Document</a></h6>
                  <h6>Master Document <h6>
	              </div>
              </div> -->

  <!-- Page Length Options -->
  	<div class="card">
			<div class="card-content">
		<table border="0" cellspacing="5" cellpadding="5">
        <tbody>
			<tr>
        <td>Serial No.</td>
        <td><input type="text" id="serial_number" name="serial_number"></td>
			  <td>Text</td>
        <td><input type="text" id="text" name="text"></td>
        <td>Status</td>
        <td><input type="text" id="status" name="status"></td>
			</tr>
      <tr>
        <td>Title</td>
        <td><input type="text" id="title" name="title"></td>
        <td>Doc Type</td>
        <td><input type="text" id="doctype" name="doctype"></td>
        <td>Link</td>
        <td><input type="text" id="link" name="link"></td>
			</tr>
    </tbody></table><br>
                          <div class="step-actions ">
										  <div class="row">
											<div class="col m7 s12 mb-1">
											  <a href="{{ route('listings') }}" class="green btn submitBtnCls right submitconfirms" style="">Report Master</a> 
											</div>
											<div class="col m3 s12 mb-1">
											 <a href="{{route('createalldocument')}}" class="btn btn-xs btn-info right waves-effect waves-teal btn gradient-45deg-deep-orange-orange white-text secondary-content" >Add Master Document</a>
											</div>
											<div class="col m2 s12 mb-2">
											  <a href="{{ action($route) }}" class="waves-effect waves dark btn btn-primary next-step">Reset</a> 
											</div>
										  </div>
										</div>
										</div>
  </div>
  <div class="row">
    <div class="col s12">
      <div class="card">
        <div class="card-content">
          <div class="row">
                <table id="reportstable">
                <thead>
                  <tr>
                    <th>Serial No.</th>
					<th>Text</th>
					<th>Portal URL</th>
					<th>Status</th>
					<th>Title</th>
					<th>Doc Type</th>
					<th>Link Text</th>
					<th>Link</th>
					
          <!-- <th>Document</th> -->
          <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($masteralldocumentlist as $masteralldocumentlists)
                  <tr id ="row1">
				    <td>{{@$masteralldocumentlists->serial_number}}</td>
            <td class="word-break">{{@$masteralldocumentlists->text}} </td>
			<td class="word-break"> 
				@php $path = "downloaddocument\\"  . @$masteralldocumentlists->document; @endphp 
				{{ @$path }}
				
			</td>
				    <td>{{ ucfirst((@$masteralldocumentlists->status == 1) ? 'active' : 'disable')}}</td>
            <td>{{@$masteralldocumentlists->title}}</td>
            <td>{{@$documentype[@$masteralldocumentlists->doc_type]}}</td>
            <td>{{@$masteralldocumentlists->link_text}}</td>
            <td>{{ucfirst(@$yesno[@$masteralldocumentlists->is_link])}}</td>
            
            <td><a href="{{ route('downloaddocument',$masteralldocumentlists->document) }}" class="">
                  <i class="material-icons" title="Click here to Download.">arrow_downward</i></a>
                <a href="{{ route('alldocumentedit',Crypt::encrypt($masteralldocumentlists->id)) }}" class="">
                  <i class="material-icons" title="Click here to Edit.">edit</i></a>
                  <a href="javascript:void(0)" class="invoice-action-delete deleteProduct" data-id="{{ Crypt::encrypt($masteralldocumentlists->id)  }}">
							<i class="material-icons" title="Click here to Delete.">delete</i></a>
                </td>
                
                    </tr>
                   @endforeach  
                </tfoot>
              </table>
            </div>
          </div>
      </div>
    </div>
  </div>
<!-- END RIGHT SIDEBAR NAV -->

          </div>
        </div>
      </div>
@endsection
@section('customjs')
	<script src="{!! asset('public/app-assets/js/bladejs/alldocument_details.js') !!}"></script> 
@endsection 





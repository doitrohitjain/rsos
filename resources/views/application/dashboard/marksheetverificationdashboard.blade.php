@extends('layouts.default')
@section('content')
@include('elements.dashboard_ui_notifications')
<div id="main">
    <div class="row">
        <div class="col s12">
		<div class="section section-data-tables"> 
				<div class="row">
				<div class="col s12">
					<div class="card">
					<div class="card-content">
					<h4><center><b>Welcome {{@Auth::user()->ssoid; }}</b></center><h4>
					</div>
					</div>
					</div>
					</div>
				</div>
        </div>
    </div>
</div>
@endsection 
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Reports</h1>

    <!-- Form to Generate Report -->
    <form action="{{ route('reports.downloadExcel') }}" method="POST" class="mb-4" id="myForm">
        @csrf
        <div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="start_date">Start Date <span class="mandatory">*</span></label>
					<input type="date" name="start_date" id="start_date" class="form-control date" required>
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label for="end_date">End Date <span class="mandatory">*</span></label>
					<input type="date" name="end_date" id="end_date" class="form-control end_date" required>
				</div>
			</div> 
			@if(auth()->user()->role == 'admin')
				<div class="col-md-3">
					<div class="form-group">
						<label for="email">Staff Name</label>
						<input type="text" name="name" id="name" class="form-control name">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<label for="email">Email</label>
						<input type="text" name="email" id="email" class="form-control email">
					</div>
				</div>
			@endif
				<div class="col-md-4">
					<div class="form-group">
						<br>
						<button type="submit" class="btn btn-primary">Export Report</button>
					</div>
				</div>
			
		</div>
    </form> 
</div>
@endsection
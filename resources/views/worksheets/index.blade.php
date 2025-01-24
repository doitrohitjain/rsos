@extends('layouts.app')

@section('content')
<div class="container">
    <h1>
		@if(Auth::user()->role == 'admin')
		@else
			My
		@endif
			Daily Worksheets
		</h1>

    <!-- Form to Add Worksheet -->
    <fieldset class="border p-4 rounded">
		<legend class="w-auto px-3 text-primary"></legend>
		<form action="{{ route('worksheets.store') }}" method="POST" class="mb-4" id="myForm">
			@csrf
			
			<div class="row">
				<div class="col-md-3">
					<div class="form-group">
						<label for="date">Date <span class="mandatory">*</span></label>
						<input type="date" name="date" id="date" class="form-control date" required>
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label for="status">Status <span class="mandatory">*</span></label>
						 <select id="status" name="status" class="form-control" required>
						  <option value="pending">Pending</option>
						  <option value="completed">Completed</option>
						</select>
					</div>
				</div>
				<div class="col-md-7">
					<div class="form-group">
						<label for="task">Task <span class="mandatory">*</span></label>
						<input type="text" name="task" id="task" class="form-control" placeholder="Task Name" required>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<label for="description">In-depth Task Explanation <span class="mandatory">*</span></label>
				<textarea name="description" id="description" class="form-control editor-class" placeholder="Task Description" ></textarea>
			</div>
			
			<br>
			<button type="submit" class="btn btn-success addmytask">Add My Task</button>
		</form>
	</fieldset>
	<br>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sr.No.</th> 
				@if(Auth::user()->role == 'admin')
					<th>Staff</th>
				@endif
                <th>Task</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
			@if(@$worksheets && count($worksheets) > 0)
				@foreach ($worksheets as $worksheet)
				<tr>
					<td>{{ $loop->iteration }}</td>
					@if(Auth::user()->role == 'admin')
						<td>
							{{ @$userNameList[$worksheet->user_id] }} 
							({{ @$userEmailList[$worksheet->user_id] }})
						</td>
					@endif
					<td>{{ $worksheet->task }}</td>
					<td>{{ $worksheet->date }}</td>
					<td>{{ ucfirst($worksheet->status) }}</td>
					<td>
						<a href="{{ route('worksheets.show', Crypt::encrypt($worksheet->id)) }}" class="btn btn-info">View</a>
						<a href="{{ route('worksheets.edit', Crypt::encrypt($worksheet->id)) }}" class="btn btn-warning">Update</a>
						<form action="{{ route('worksheets.destroy', Crypt::encrypt($worksheet->id)) }}" method="POST" style="display:inline-block;">
							@csrf
							@method('DELETE')
							<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
						</form>
					</td>
				</tr>
				
				@endforeach
				<tr>
					<td colspan="10">
						{{ $worksheets->withQueryString()->links('elements.paginater') }}
					</td>
				</tr>
			@else
				<tr>
					<td colspan="10">
						<center>No Task Found</center>
					</td>
				</tr>
			@endif
        </tbody>
		
    </table>
</div>
@endsection



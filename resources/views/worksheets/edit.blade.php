@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Update Worksheet</h1>
    <form action="{{ route('worksheets.update', Crypt::encrypt($worksheet->id)) }}" method="POST">
        @csrf
        @method('PUT')
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="date">Date <span class="mandatory">*</span></label>
					<input type="date" name="date" id="date" class="form-control date" value="{{ old('date', $worksheet->date) }}" required>
				</div>
			</div>
			<div class="col-md-2">
				 <div class="form-group">
					<label for="status" class="form-label">Status</label>
					<select name="status" id="status" class="form-control" required>
						<option value="pending" {{ $worksheet->status === 'pending' ? 'selected' : '' }}>Pending</option>
						<option value="completed" {{ $worksheet->status === 'completed' ? 'selected' : '' }}>Completed</option>
					</select>
				</div>
			</div>
			<div class="col-md-7">
				<div class="form-group">
					<label for="task">Task <span class="mandatory">*</span></label>
					<input type="text" name="task" id="task" class="form-control" value="{{ old('task', $worksheet->task) }}" required>
				</div>
			</div>
		</div>
		
        <div class="mb-3">
            <label for="description">In-depth Task Explanation <span class="mandatory">*</span></label>
			<textarea name="description" id="description" class="form-control editor-class">{{ old('description', $worksheet->description) }}</textarea>
        </div>
		
       

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection

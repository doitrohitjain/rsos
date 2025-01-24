@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Worksheet Details</h1>
    <p><strong>Task Date:</strong> {{ $worksheet->date }}</p>
    <p><strong>Task Name:</strong> {{ $worksheet->task }}</p>
    <p>
		
		<strong>Description: </strong> 
		{{ $worksheet->description }}
		<div id="rawHtmlContent"></div> 
	</p>
    <p><strong>Status:</strong> {{ ucfirst($worksheet->status) }}</p>
    <a href="{{ route('worksheets.edit', Crypt::encrypt($worksheet->id)) }}" class="btn btn-primary">Update</a>
    <form action="{{ route('worksheets.destroy', Crypt::encrypt($worksheet->id)) }}" method="POST" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
    </form>
</div>
@endsection
 
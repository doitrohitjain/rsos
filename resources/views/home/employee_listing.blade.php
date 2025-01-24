@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Staff</h1>
 
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sr.No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ ucfirst($user->role) }}</td>
                <td>  
					<form action="{{ route('home.destroy', Crypt::encrypt($user->id)) }}" method="POST" style="display:inline-block;">
						@csrf
						@method('DELETE')
						<button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
					</form>
				</td>
            </tr>
            @endforeach
			<tr>
				<td colspan="10">
					{{ $users->withQueryString()->links('elements.paginater') }}
				</td>
			</tr>
			
        </tbody>
    </table>
</div>
@endsection

 
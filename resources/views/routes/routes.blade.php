<!DOCTYPE html>
<html>
<head>
    <title>Routes List</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Routes List</h1>
    <table>
        <thead>
            <tr>
                <th>URI</th>
                <th>Name</th>
                <th>Method</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($routes as $route)
                <tr>
                    <td>{{ $route['uri'] }}</td>
                    <td>{{ $route['name'] ?? '-' }}</td>
                    <td>{{ $route['method'] }}</td>
                    <td>{{ $route['action'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

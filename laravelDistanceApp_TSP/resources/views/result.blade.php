<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distance Matrix Result</title>
</head>
<body>
    <h1>Distance Matrix</h1>
    @if (!empty($distanceMatrix) && !empty($places))
        <table border="1">
            <tr>
                <th></th>
                @foreach ($places as $place)
                    <th>{{ $place }}</th>
                @endforeach
            </tr>
            @foreach ($distanceMatrix as $i => $row)
                <tr>
                    <th>{{ $places[$i] }}</th>
                    @foreach ($row as $distance)
                        <td>{{ $distance }}</td>
                    @endforeach
                </tr>
            @endforeach
        </table>
        <form method="POST" action="{{ route('save.distance') }}">
            @csrf
            <button type="submit">Save as CSV</button>
        </form>
    @else
        <p>No data to display.</p>
    @endif
    <form action="{{ url('/calculate-tsp') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="file" name="locations" required>
    <button type="submit">Calculate TSP</button>
</form>
</body>
</html>

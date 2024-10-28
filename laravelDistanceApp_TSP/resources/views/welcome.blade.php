<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distance Matrix Calculator</title>
</head>
<body>
    <form action="{{ route('calculate.distance') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="city_country">City, Country:</label>
        <input type="text" id="city_country" name="city_country" required>

        <label for="locations_file">Location File:</label>
        <input type="file" id="locations_file" name="locations_file" required>

        <button type="submit">Calculate Distances</button>
    </form>
</body>
</html>

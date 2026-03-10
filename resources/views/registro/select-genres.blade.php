<!DOCTYPE html>
<html>
<head>
    <title>Select Movie Genres</title>
</head>
<body>

<h2>Select the movie genres you like</h2>

<form method="POST" action="/save-genres">
    @csrf

    <label>
        <input type="checkbox" name="genres[]" value="action">
        Action
    </label>
    <br>

    <label>
        <input type="checkbox" name="genres[]" value="comedy">
        Comedy
    </label>
    <br>

    <label>
        <input type="checkbox" name="genres[]" value="drama">
        Drama
    </label>
    <br>

    <label>
        <input type="checkbox" name="genres[]" value="horror">
        Horror
    </label>
    <br>

    <label>
        <input type="checkbox" name="genres[]" value="sci-fi">
        Sci-Fi
    </label>
    <br><br>

    <button type="submit">Save Preferences</button>

</form>

</body>
</html>
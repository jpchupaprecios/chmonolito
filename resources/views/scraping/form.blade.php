<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scraping Amazon</title>
</head>
<body>
<h1 style="color: red">Scraping Amazon</h1>
<form method="POST" action="/scraping">
    @csrf
    <div>
        <label for="query">Buscar:</label>
        <input type="text" id="query" name="query" required>
    </div>
    <div>
        <label for="page">Página:</label>
        <input type="number" id="page" name="page" value="1" min="1">
    </div>
    <button type="submit">Iniciar Scraping</button>
</form>
</body>
</html>

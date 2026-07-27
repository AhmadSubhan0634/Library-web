<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Book</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .message { padding: 10px; margin: 20px 0; }
        .success { color: #155724; }
        .error { color: #721c24; }
    </style>
</head>
<body>
    <div class="message <?= strpos($message, 'success') !== false ? 'success' : 'error' ?>">
        <?= htmlspecialchars($message) ?>
    </div>

    <p><a href="/books">Back to Book List</a></p>
</body>
</html>
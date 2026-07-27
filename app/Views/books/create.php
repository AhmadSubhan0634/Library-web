<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 100px; }
        .error { color: #cc0000; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Add New Book</h1>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/books/store">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" required>
        </div>
        <div class="form-group">
            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" required>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required>
        </div>
        <div class="form-group">
            <label for="year">Year:</label>
            <input type="number" id="year" name="year" required>
        </div>
        <div class="form-group">
            <button type="submit">Add Book</button>
            <a href="/books">Cancel</a>
        </div>
    </form>
</body>
</html>
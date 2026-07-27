<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 100px; }
        .error { color: #cc0000; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Edit Book</h1>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="/books/update">
        <input type="hidden" name="isbn" value="<?= htmlspecialchars($book->getIsbn()) ?>">

        <div class="form-group">
            <label for="isbn_display">ISBN:</label>
            <input type="text" id="isbn_display" value="<?= htmlspecialchars($book->getIsbn()) ?>" readonly>
            <small>ISBN cannot be changed</small>
        </div>
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="<?= htmlspecialchars($book->getTitle()) ?>" required>
        </div>
        <div class="form-group">
            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?= htmlspecialchars($book->getAuthor()) ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <input type="text" id="category" name="category" value="<?= htmlspecialchars($book->getCategory()) ?>" required>
        </div>
        <div class="form-group">
            <label for="year">Year:</label>
            <input type="number" id="year" name="year" value="<?= htmlspecialchars($book->getYear()) ?>" required>
        </div>
        <div class="form-group">
            <button type="submit">Update Book</button>
            <a href="/books">Cancel</a>
        </div>
    </form>
</body>
</html>
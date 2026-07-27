<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .detail-row { padding: 5px 0; }
        .label { font-weight: bold; display: inline-block; width: 100px; }
    </style>
</head>
<body>
    <h1>Book Details</h1>

    <div class="detail-row">
        <span class="label">Title:</span>
        <?= htmlspecialchars($book->getTitle()) ?>
    </div>
    <div class="detail-row">
        <span class="label">Author:</span>
        <?= htmlspecialchars($book->getAuthor()) ?>
    </div>
    <div class="detail-row">
        <span class="label">ISBN:</span>
        <?= htmlspecialchars($book->getIsbn()) ?>
    </div>
    <div class="detail-row">
        <span class="label">Category:</span>
        <?= htmlspecialchars($book->getCategory()) ?>
    </div>
    <div class="detail-row">
        <span class="label">Year:</span>
        <?= htmlspecialchars($book->getYear()) ?>
    </div>

    <p>
        <a href="/books">Back to List</a>
        <a href="/books/edit?isbn=<?= urlencode($book->getIsbn()) ?>">Edit Book</a>
    </p>
</body>
</html>
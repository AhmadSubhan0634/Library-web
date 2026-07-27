<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; max-width: 800px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>Books</h1>
    <p><a href="/books/create">Add New Book</a></p>

    <?php if (empty($books)): ?>
        <p>No books found.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>ISBN</th>
                <th>Category</th>
                <th>Year</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= htmlspecialchars($book->getTitle()) ?></td>
                    <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                    <td><?= htmlspecialchars($book->getIsbn()) ?></td>
                    <td><?= htmlspecialchars($book->getCategory()) ?></td>
                    <td><?= htmlspecialchars($book->getYear()) ?></td>
                    <td>
                        <a href="/books/show?isbn=<?= urlencode($book->getIsbn()) ?>">View</a>
                        <a href="/books/edit?isbn=<?= urlencode($book->getIsbn()) ?>">Edit</a>
                        <form method="POST" action="/books/delete" style="display: inline;">
                            <input type="hidden" name="isbn" value="<?= htmlspecialchars($book->getIsbn()) ?>">
                            <button type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</body>
</html>
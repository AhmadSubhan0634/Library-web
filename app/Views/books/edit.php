<?php $title = 'Edit Book'; include __DIR__ . '/../layouts/header.php'; ?>

<h1>Edit Book</h1>
<form method="POST" action="/books/update" class="col-md-6">
    <input type="hidden" name="isbn" value="<?= htmlspecialchars($book->getIsbn()) ?>">
    <div class="mb-3">
        <label class="form-label">ISBN</label>
        <input type="text" class="form-control" value="<?= htmlspecialchars($book->getIsbn()) ?>" readonly>
    </div>
    <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($book->getTitle()) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Author</label>
        <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($book->getAuthor()) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($book->getCategory()) ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Year</label>
        <input type="number" name="year" class="form-control" value="<?= htmlspecialchars($book->getYear()) ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Book</button>
    <a href="/books" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

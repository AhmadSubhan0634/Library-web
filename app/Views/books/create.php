<?php $title = 'Add New Book'; include __DIR__ . '/../layouts/header.php'; ?>

<h1>Add New Book</h1>
<form method="POST" action="/books/store" class="col-md-6">
    <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Author</label>
        <input type="text" name="author" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">ISBN</label>
        <input type="text" name="isbn" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Year</label>
        <input type="number" name="year" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Add Book</button>
    <a href="/books" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

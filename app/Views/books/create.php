<?php $title = 'Add New Book'; include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        margin: 0;
        min-height: 100vh;
        background-image: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
                           url('https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
</style>

<div class="container d-flex align-items-center justify-content-center py-5" style="min-height: 90vh;">
    <div class="card shadow" style="max-width: 550px; width: 100%; background-color: rgba(255,255,255,0.9); backdrop-filter: blur(4px);">
        <div class="card-body p-4">
            <h1 class="h3 mb-4">Add New Book</h1>
            <form method="POST" action="/books/store">
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
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
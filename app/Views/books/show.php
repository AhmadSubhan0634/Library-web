<?php $title = 'Book Details'; include __DIR__ . '/../layouts/header.php'; ?>

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

<div class="container d-flex align-items-center justify-content-center" style="min-height: 90vh;">
    <div class="card shadow" style="max-width: 500px; width: 100%; background-color: rgba(255,255,255,0.9); backdrop-filter: blur(4px);">
        <div class="card-body p-4">
            <h1 class="h3 mb-3">Book Details</h1>
            <h5 class="card-title"><?= htmlspecialchars($book->getTitle()) ?></h5>
            <p class="card-text">
                <strong>Author:</strong> <?= htmlspecialchars($book->getAuthor()) ?><br>
                <strong>ISBN:</strong> <?= htmlspecialchars($book->getIsbn()) ?><br>
                <strong>Category:</strong> <?= htmlspecialchars($book->getCategory()) ?><br>
                <strong>Year:</strong> <?= htmlspecialchars($book->getYear()) ?>
            </p>
            <a href="/books" class="btn btn-secondary">Back to List</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/books/edit?isbn=<?= urlencode($book->getIsbn()) ?>" class="btn btn-primary">Edit Book</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
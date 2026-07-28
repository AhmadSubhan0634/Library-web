<?php $title = 'Book Details'; include __DIR__ . '/../layouts/header.php'; ?>

<h1>Book Details</h1>
<div class="card" style="max-width: 500px;">
    <div class="card-body">
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

<?php include __DIR__ . '/../layouts/footer.php'; ?>

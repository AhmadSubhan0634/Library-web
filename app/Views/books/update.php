<?php $title = 'Update Book Result'; include __DIR__ . '/../layouts/header.php'; ?>

<div class="alert <?= strpos($message, 'success') !== false ? 'alert-success' : 'alert-danger' ?>">
    <?= htmlspecialchars($message) ?>
</div>
<a href="/books" class="btn btn-secondary">Back to Book List</a>
<a href="/books/edit?isbn=<?= urlencode($isbn ?? '') ?>" class="btn btn-primary">Edit Again</a>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

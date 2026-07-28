<?php $title = 'Error'; include __DIR__ . '/../layouts/header.php'; ?>

<div class="alert alert-danger" role="alert">
    <?= htmlspecialchars($message) ?>
</div>
<a href="/books" class="btn btn-secondary">Back to Book List</a>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

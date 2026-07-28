<?php $title = '500 Server Error'; include __DIR__ . '/../layouts/header.php'; ?>

<div class="text-center py-5">
    <h1 class="display-1 fw-bold">500</h1>
    <p class="fs-4">Something went wrong on our end.</p>
    <p class="text-muted"><?= htmlspecialchars($message ?? 'Please try again later.') ?></p>
    <a href="/books" class="btn btn-primary">Back to Books</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<?php $title = 'Home'; include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        margin: 0;
        min-height: 100vh;
        background-image: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.45)),
            url('https://images.unsplash.com/photo-1568667256549-094345857637?w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
</style>

<div class="container d-flex align-items-center justify-content-center text-white" style="min-height: 90vh;">
    <div class="text-center">
        <h1 class="display-5 fw-bold">Welcome to the Library System</h1>
        <p class="fs-4">Browse, search, and manage the book collection.</p>
        <a href="/books" class="btn btn-primary btn-lg">View Books</a>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
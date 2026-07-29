<?php $title = 'Login'; include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        margin: 0;
        min-height: 100vh;
        background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                           url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
</style>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 90vh;">
    <div class="bg-white bg-opacity-75 rounded-4 p-4 p-md-5 shadow" style="backdrop-filter: blur(4px); max-width: 420px; width: 100%;">

        <h1 class="mb-4">Login</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Log In</button>
        </form>

        <p class="mt-3 mb-0">
            <a href="/books">Continue browsing as guest</a> |
            <a href="/register">Need an account? Register</a>
        </p>

    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
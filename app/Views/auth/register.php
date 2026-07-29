<?php $title = 'Register'; include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        margin: 0;
        min-height: 100vh;
        background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                           url('https://images.unsplash.com/photo-1568667256549-094345857637?w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
</style>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 90vh;">
    <div class="bg-white bg-opacity-75 rounded-4 p-4 p-md-5 shadow" style="backdrop-filter: blur(4px); max-width: 420px; width: 100%;">

        <h1 class="mb-4">Register</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/register">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" minlength="8" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" minlength="8" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="mt-3 mb-0"><a href="/login">Already have an account? Log in</a></p>

    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
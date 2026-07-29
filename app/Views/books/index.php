<?php $title = 'Books'; include __DIR__ . '/../layouts/header.php'; ?>

<style>
    body {
        margin: 0;
        min-height: 100vh;
        background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                           url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=1920&q=80');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
</style>

<div class="container py-5">
    <div class="bg-white bg-opacity-75 rounded-4 p-4 p-md-5 shadow" style="backdrop-filter: blur(4px);">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1>Books</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/books/create" class="btn btn-success">Add New Book</a>
            <?php else: ?>
                <a href="/login" class="btn btn-outline-primary">Log in to add or edit books</a>
            <?php endif; ?>
        </div>

        <form method="GET" action="/books" class="row g-2 mb-3">
            <div class="col-auto flex-grow-1">
                <input type="text" name="search" class="form-control" placeholder="Search by title, author, or ISBN"
                       value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="/books" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if (!empty($search)): ?>
            <p class="text-muted">Showing results for "<?= htmlspecialchars($search) ?>"</p>
        <?php endif; ?>

        <?php if (empty($books)): ?>
            <p class="text-muted fst-italic">No books found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped align-middle bg-white">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th>Year</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?= htmlspecialchars($book->getTitle()) ?></td>
                                <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                                <td><?= htmlspecialchars($book->getIsbn()) ?></td>
                                <td><?= htmlspecialchars($book->getCategory()) ?></td>
                                <td><?= htmlspecialchars($book->getYear()) ?></td>
                                <td>
                                    <a href="/books/show?isbn=<?= urlencode($book->getIsbn()) ?>" class="btn btn-sm btn-outline-secondary">View</a>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a href="/books/edit?isbn=<?= urlencode($book->getIsbn()) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="/books/delete" class="d-inline">
                                            <input type="hidden" name="isbn" value="<?= htmlspecialchars($book->getIsbn()) ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav aria-label="Book pages">
                    <ul class="pagination">
                        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="/books?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search ?? '') ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                                <a class="page-link" href="/books?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="/books?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search ?? '') ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
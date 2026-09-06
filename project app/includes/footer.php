<!-- Main page content ends here -->
</main>
<!-- Shared application footer -->
<footer class="app-footer">
    <span>&copy; <?= date('Y') ?> <?= e(hotel_settings()['hotel_name']) ?></span>
    <span>Hotel Booking Management System</span>
</footer>
<script>
// Confirm destructive actions before a form is submitted.
    document.querySelectorAll('[data-confirm]').forEach((element) => {
    element.addEventListener('click', (event) => {
    if (!confirm(element.dataset.confirm)) {
    event.preventDefault();
    }
    });
    });

// Expand or collapse the sidebar.
document.getElementById('menuToggle')?.addEventListener('click', () => {
    document.body.classList.toggle('sidebar-collapsed');
    });

    // Add a compact live search field to each data table.
    document.querySelectorAll('.panel table').forEach((table) => {
    const search = document.createElement('input');
    search.className = 'table-search';
    search.placeholder = 'Search this table...';
    table.before(search);

    search.addEventListener('input', () => {
    table.querySelectorAll('tbody tr').forEach((row) => {
    row.hidden = !row.textContent.toLowerCase().includes(search.value.toLowerCase());
    });
    });
    });

    // Let success and error messages disappear after a few seconds.
    setTimeout(() => document.querySelector('.alert')?.remove(), 4500);
</script>
</body>
</html>

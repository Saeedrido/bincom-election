
    <footer class="app-footer">
        &copy; <?= date('Y') ?> Bincom Election Management System. All rights reserved.
    </footer>
</main>

<!-- sidebar overlay close on click -->
<script>
    document.getElementById('sidebarOverlay')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.remove('mobile-open');
        this.classList.remove('show');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</div> <!-- .app -->
</body>
</html>

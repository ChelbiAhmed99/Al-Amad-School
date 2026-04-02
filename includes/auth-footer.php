    <script>
    // Universal reveal trigger for all auth pages
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.reveal, .reveal-right').forEach((el, i) => {
            setTimeout(() => el.classList.add('visible'), i * 120);
        });
    });
    </script>
    <script src="../assets/js/theme-switcher.js"></script>
</body>
</html>

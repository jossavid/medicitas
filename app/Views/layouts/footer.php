<?php if (\App\Core\Auth::check()): ?>
        </section>
        <footer class="app-footer">MediCitas</footer>
    </main>
</div>
<?php else: ?>
</main>
<?php endif; ?>
<script src="<?= url('/assets/js/app.js') ?>"></script>
</body>
</html>

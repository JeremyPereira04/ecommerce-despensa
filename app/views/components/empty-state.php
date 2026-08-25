<div class="empty-state">
    <div class="empty-state__icon" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="M4 7h16l-1 13H5L4 7Z"/><path d="M9 10V6a3 3 0 0 1 6 0v4"/></svg>
    </div>
    <h2><?= e($emptyTitle ?? 'Todavía no hay contenido') ?></h2>
    <p><?= e($emptyText ?? 'Volvé a consultar más tarde.') ?></p>
    <?php if (!empty($emptyActionUrl) && !empty($emptyActionLabel)): ?>
        <a class="btn btn-primary" href="<?= e($emptyActionUrl) ?>"><?= e($emptyActionLabel) ?></a>
    <?php endif; ?>
</div>

<div class="admin-page-actions">
    <div><span class="admin-kicker">Organización</span><h2>Categorías</h2><p>Organizá el catálogo real de la tienda.</p></div>
    <button class="admin-primary-button" type="button" data-category-create data-bs-toggle="modal" data-bs-target="#categoryModal">+ Nueva categoría</button>
</div>

<section class="admin-category-grid">
    <?php foreach ($adminCategories as $category): ?>
        <?php $isActive = in_array($category['activo'], [true, 1, '1', 't'], true); ?>
        <article class="admin-panel admin-category-card">
            <img class="admin-category-card__image" src="<?= e(category_image($category['imagen'] ?? null)) ?>" alt="Imagen de <?= e($category['nombre']) ?>" width="320" height="210" data-image-fallback="<?= e(asset('assets/images/categories/category-placeholder.svg')) ?>">
            <div class="admin-category-card__body">
                <span class="admin-status"><?= $isActive ? 'Activa' : 'Inactiva' ?></span>
                <h3><?= e($category['nombre']) ?></h3>
                <p><?= e($category['descripcion'] ?? 'Sin descripción') ?></p>
                <small><?= (int) $category['productos_count'] ?> productos</small>
            </div>
            <div class="admin-row-actions">
                <button type="button" data-category-edit data-id="<?= (int) $category['id_categoria'] ?>" data-name="<?= e($category['nombre']) ?>" data-description="<?= e($category['descripcion'] ?? '') ?>" data-image-url="<?= e(category_image($category['imagen'] ?? null)) ?>" data-active="<?= $isActive ? '1' : '0' ?>" data-bs-toggle="modal" data-bs-target="#categoryModal">Editar</button>
                <form method="post" action="<?= e(url('admin-category-toggle', ['id' => $category['id_categoria']])) ?>"><?= csrf_input() ?><button type="submit"><?= $isActive ? 'Desactivar' : 'Activar' ?></button></form>
            </div>
        </article>
    <?php endforeach; ?>
</section>

<?php if (!$adminCategories): ?><p class="admin-empty">Todavía no hay categorías.</p><?php endif; ?>

<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" action="<?= e(url('admin-category-save')) ?>" data-category-form data-create-action="<?= e(url('admin-category-save')) ?>" data-update-action="<?= e(url('admin-category-update')) ?>">
                <?= csrf_input() ?>
                <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
                <div class="modal-header"><h2 class="modal-title fs-5" id="categoryModalTitle" data-category-modal-title>Nueva categoría</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
                <div class="modal-body category-form-grid">
                    <div>
                        <label class="admin-field"><span>Nombre *</span><input name="nombre" class="form-control" maxlength="100" required></label>
                        <label class="admin-field mt-3"><span>Descripción</span><textarea name="descripcion" maxlength="1000" class="form-control" rows="4"></textarea></label>
                        <label class="admin-check mt-3"><input name="activo" type="checkbox" value="1" checked><span>Categoría activa</span></label>
                    </div>
                    <div class="category-image-field">
                        <span class="category-image-field__label">Imagen representativa</span>
                        <div class="admin-image-preview admin-image-preview--category" data-category-image-preview><img src="<?= e(asset('assets/images/categories/category-placeholder.svg')) ?>" alt="Vista previa de la categoría" width="320" height="240"></div>
                        <label class="admin-upload">Seleccionar imagen<input name="imagen" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-category-image-input data-max-size="2097152"></label>
                        <small>JPG, JPEG, PNG o WebP; máximo 2 MB. Al editar, dejá este campo vacío para conservar la imagen actual.</small>
                        <p class="category-image-error" data-category-image-error role="alert" aria-live="polite"></p>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="admin-secondary-button" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="admin-primary-button">Guardar categoría</button></div>
            </form>
        </div>
    </div>
</div>

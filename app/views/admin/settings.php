<?php $advertisements=$advertisements??[]; ?>
<div class="admin-page-actions"><div><span class="admin-kicker">Portada</span><h2>Carrusel de publicidades</h2><p>Subí banners horizontales y elegí cuáles se muestran automáticamente en el menú principal.</p></div><a class="admin-secondary-button" href="<?=e(url('home'))?>" target="_blank" rel="noopener">Ver portada</a></div>

<form class="admin-form-layout advertisement-create-form" method="post" enctype="multipart/form-data" action="<?=e(url('admin-advertisement-save'))?>" data-advertisement-form>
    <?=csrf_input()?>
    <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
    <section class="admin-panel admin-form-main">
        <h3>Nueva publicidad</h3>
        <label class="admin-field admin-field--wide"><span>Descripción accesible *</span><input class="form-control" name="texto_alternativo" maxlength="180" required placeholder="Ej.: Promoción de jugos naturales Watts"><small>Esta descripción ayuda a personas que usan lectores de pantalla.</small></label>
        <label class="admin-field"><span>Orden</span><input class="form-control" name="orden" type="number" min="0" max="999" value="<?=count($advertisements)*10?>"><small>Los números menores aparecen primero.</small></label>
        <label class="admin-check mt-4"><input name="activo" type="checkbox" value="1" checked><span>Publicar inmediatamente</span></label>
        <div class="advertisement-guidance"><strong>Dimensión recomendada: 1920 × 650 px</strong><span>JPG, JPEG, PNG o WebP · máximo 5 MB · formato horizontal.</span></div>
    </section>
    <aside class="admin-panel admin-image-panel">
        <h3>Imagen del banner</h3>
        <div class="admin-advertisement-preview" data-advertisement-preview><img src="<?=e(advertisement_image(null))?>" alt="Vista previa de la publicidad" width="1920" height="650"></div>
        <label class="admin-upload">Seleccionar imagen<input name="imagen" type="file" required accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-advertisement-input data-max-size="5242880"></label>
        <p class="category-image-error" data-advertisement-error role="alert" aria-live="polite"></p>
        <button class="admin-primary-button" type="submit"><i class="bi bi-plus-lg"></i> Agregar publicidad</button>
    </aside>
</form>

<section class="admin-panel advertisement-library" aria-labelledby="advertisement-library-title">
    <div class="advertisement-library__heading"><div><span class="admin-kicker">Biblioteca</span><h3 id="advertisement-library-title">Publicidades cargadas</h3></div><span><?=count($advertisements)?> banner<?=count($advertisements)===1?'':'s'?></span></div>
    <?php if($advertisements): ?>
        <div class="advertisement-admin-grid">
            <?php foreach($advertisements as $item): $active=in_array($item['activo']??false,[true,1,'1','t'],true); ?>
                <article class="advertisement-admin-card">
                    <img src="<?=e(advertisement_image($item['imagen']??null))?>" alt="<?=e($item['texto_alternativo']??'Publicidad')?>" width="1920" height="650" loading="lazy" data-image-fallback="<?=e(asset('assets/images/advertising/advertising-placeholder.svg'))?>">
                    <div class="advertisement-admin-card__body"><div><strong><?=e($item['texto_alternativo']??'Publicidad')?></strong><small>Orden <?=e((string)($item['orden']??0))?></small></div><span class="admin-status <?=$active?'status-entregado':'status-cancelado'?>"><?=$active?'Activa':'Inactiva'?></span></div>
                    <div class="advertisement-admin-card__actions">
                        <form method="post" action="<?=e(url('admin-advertisement-toggle',['id'=>$item['id_publicidad']]))?>"><?=csrf_input()?><button class="admin-secondary-button" type="submit"><i class="bi <?=$active?'bi-eye-slash':'bi-eye'?>"></i> <?=$active?'Ocultar':'Mostrar'?></button></form>
                        <form method="post" action="<?=e(url('admin-advertisement-delete',['id'=>$item['id_publicidad']]))?>"><?=csrf_input()?><button class="admin-danger-button" type="submit" data-confirm="¿Eliminar esta publicidad del carrusel?"><i class="bi bi-trash"></i> Eliminar</button></form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php else: ?><p class="inline-empty">Todavía no hay publicidades. Agregá la primera con el formulario superior.</p><?php endif; ?>
</section>

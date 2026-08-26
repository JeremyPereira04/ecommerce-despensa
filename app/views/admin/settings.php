<?php
$contact=$GLOBALS['appConfig']['contact']??[];
$advertisement=$advertisement??null;
$advertisementActive=is_array($advertisement)&&in_array($advertisement['activo']??false,[true,1,'1','t'],true);
?>
<div class="admin-page-actions"><div><span class="admin-kicker">Portada</span><h2>Publicidad principal</h2><p>Administrá la imagen destacada que aparece al ingresar a la tienda.</p></div><a class="admin-secondary-button" href="<?=e(url('home'))?>" target="_blank" rel="noopener">Ver portada</a></div>

<form class="admin-form-layout" method="post" enctype="multipart/form-data" action="<?=e(url('admin-advertisement-save'))?>" data-advertisement-form>
    <?=csrf_input()?>
    <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
    <section class="admin-panel admin-form-main">
        <h3>Datos de la publicidad</h3>
        <label class="admin-field admin-field--wide"><span>Descripción accesible *</span><input class="form-control" name="texto_alternativo" maxlength="180" required value="<?=e($advertisement['texto_alternativo']??'Promoción principal de Despensa Para Todos')?>"><small>Describí brevemente el contenido de la imagen para personas que usan lectores de pantalla.</small></label>
        <label class="admin-check mt-4"><input name="activo" type="checkbox" value="1" <?=$advertisement===null||$advertisementActive?'checked':''?>><span>Mostrar publicidad en el menú principal</span></label>
        <div class="advertisement-guidance"><strong>Dimensión recomendada: 1920 × 720 px</strong><span>Se aceptan imágenes horizontales JPG, JPEG, PNG o WebP, de hasta 5 MB.</span></div>
    </section>
    <aside class="admin-panel admin-image-panel">
        <h3>Imagen publicitaria</h3>
        <div class="admin-advertisement-preview" data-advertisement-preview><img src="<?=e(advertisement_image($advertisement['imagen']??null))?>" alt="Vista previa de la publicidad" width="1920" height="720" data-image-fallback="<?=e(asset('assets/images/advertising/advertising-placeholder.svg'))?>"></div>
        <label class="admin-upload">Seleccionar imagen<input name="imagen" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-advertisement-input data-max-size="5242880"></label>
        <small><?=empty($advertisement['imagen'])?'Seleccioná una imagen para publicar.':'Dejá el campo vacío para conservar la imagen actual.'?></small>
        <p class="category-image-error" data-advertisement-error role="alert" aria-live="polite"></p>
        <button class="admin-primary-button" type="submit">Guardar publicidad</button>
    </aside>
</form>

<section class="admin-panel settings-contact-summary"><h3>Información del negocio</h3><p><strong>Despensa Para Todos</strong><br><?=e($contact['phone_display']??'')?> · <?=e($contact['email']??'')?><br><?=e($contact['location']??'')?></p><small>Estos datos continúan administrándose desde la configuración local del proyecto.</small></section>

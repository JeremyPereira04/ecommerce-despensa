<?php
$allowedAlertTypes = ['success', 'danger', 'warning', 'info'];
$alertType = in_array($message['type'] ?? '', $allowedAlertTypes, true) ? $message['type'] : 'info';
?>
<div class="alert alert-<?= e($alertType) ?> alert-dismissible fade show app-alert" role="status">
    <span><?= e($message['message'] ?? '') ?></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar mensaje"></button>
</div>

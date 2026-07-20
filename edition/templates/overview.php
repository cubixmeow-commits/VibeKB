<?php
/**
 * Reusable publication template: overview
 * Presentation differences are handled via .template-overview styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

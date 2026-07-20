<?php
/**
 * Reusable publication template: editorial
 * Presentation differences are handled via .template-editorial styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

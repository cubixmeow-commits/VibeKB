<?php
/**
 * Reusable publication template: mental-models
 * Presentation differences are handled via .template-mental-models styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

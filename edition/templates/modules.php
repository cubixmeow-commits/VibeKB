<?php
/**
 * Reusable publication template: modules
 * Presentation differences are handled via .template-modules styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

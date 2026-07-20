<?php
/**
 * Reusable publication template: debugging
 * Presentation differences are handled via .template-debugging styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

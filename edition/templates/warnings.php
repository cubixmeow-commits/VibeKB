<?php
/**
 * Reusable publication template: warnings
 * Presentation differences are handled via .template-warnings styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

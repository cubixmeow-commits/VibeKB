<?php
/**
 * Reusable publication template: risks
 * Presentation differences are handled via .template-risks styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

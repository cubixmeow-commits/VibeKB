<?php
/**
 * Reusable publication template: decisions
 * Presentation differences are handled via .template-decisions styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

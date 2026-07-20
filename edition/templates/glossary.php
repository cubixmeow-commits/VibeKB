<?php
/**
 * Reusable publication template: glossary
 * Presentation differences are handled via .template-glossary styles.
 */
if (isset($item) && is_array($item)) {
    require __DIR__ . '/article.php';
} else {
    require __DIR__ . '/collection.php';
}

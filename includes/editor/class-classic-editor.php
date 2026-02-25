<?php
namespace OrgaPressLite\Editor;

if (!defined('ABSPATH')) {
    exit;
}

class ClassicEditor
{
    public static function init(): void
    {
        // Gutenberg deaktivieren
        add_filter('use_block_editor_for_post', '__return_false', 100);
        add_filter('use_block_editor_for_post_type', '__return_false', 100);
        add_filter('gutenberg_can_edit_post_type', '__return_false', 100);
        
        // Block-Widgets deaktivieren
        add_filter('use_widgets_block_editor', '__return_false');

        // Block-Styles im Frontend entfernen
        add_action('wp_enqueue_scripts', [self::class, 'remove_block_styles'], 100);
    }

    public static function remove_block_styles(): void
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('wc-block-style'); 
    }
}

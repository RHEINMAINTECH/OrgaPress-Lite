<?php
namespace OrgaPressLite\Media;

if (!defined('ABSPATH')) {
exit;
}

class MediaFolders
{
const TAXONOMY = 'orgapress_media_folder';
const CAPABILITY = 'upload_files';
const DEFAULT_FOLDERS = ['Marketing', 'Intern', 'Presse'];

public static function init(): void
{
    add_action('init', [self::class, 'register_taxonomy']);
    add_action('init', [self::class, 'ensure_default_folders']);
    add_filter('ajax_query_attachments_args', [self::class, 'filter_media_library']);
    add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
    add_filter('attachment_fields_to_edit', [self::class, 'attachment_field'], 10, 2);
    add_filter('attachment_fields_to_save', [self::class, 'save_attachment_field'], 10, 2);
    add_filter('bulk_actions-upload', [self::class, 'register_bulk_action']);
    add_filter('handle_bulk_actions-upload', [self::class, 'handle_bulk_action'], 10, 3);
    add_action('wp_ajax_get_terms', [self::class, 'ajax_get_terms']);
}

public static function ajax_get_terms(): void
{
    check_ajax_referer('query-attachments', 'nonce');
    
    $taxonomy = sanitize_text_field($_POST['taxonomy'] ?? '');
    if ($taxonomy !== self::TAXONOMY) {
        wp_send_json_error();
    }

    $terms = get_terms([
        'taxonomy' => self::TAXONOMY,
        'hide_empty' => false,
    ]);

    wp_send_json($terms);
}

public static function register_taxonomy(): void
{
    register_taxonomy(
        self::TAXONOMY,
        'attachment',
        [
            'labels' => [
                'name' => __('Medienordner', 'orgapress-lite'),
                'singular_name' => __('Medienordner', 'orgapress-lite'),
            ],
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'rewrite' => false,
            'capabilities' => [
                'manage_terms' => self::CAPABILITY,
                'edit_terms'   => self::CAPABILITY,
                'delete_terms' => self::CAPABILITY,
                'assign_terms' => self::CAPABILITY,
            ],
        ]
    );
}

public static function ensure_default_folders(): void
{
    foreach (self::DEFAULT_FOLDERS as $folder) {
        if (!term_exists($folder, self::TAXONOMY)) {
            wp_insert_term($folder, self::TAXONOMY);
        }
    }
}

public static function enqueue_assets(): void
{
    if (!current_user_can(self::CAPABILITY)) {
        return;
    }

    wp_enqueue_script(
        'orgapress-media-folders',
        ORGAPRESS_LITE_URL . 'includes/media/media-folders.js',
        ['media-views', 'jquery', 'wp-i18n'],
        ORGAPRESS_LITE_VERSION,
        true
    );

    wp_enqueue_script(
        'orgapress-media-folders-tree',
        ORGAPRESS_LITE_URL . 'includes/media/media-folders-tree.js',
        ['orgapress-media-folders', 'media-views', 'jquery', 'wp-i18n'],
        ORGAPRESS_LITE_VERSION,
        true
    );

    wp_enqueue_script(
        'orgapress-media-folders-dnd',
        ORGAPRESS_LITE_URL . 'includes/media/media-folders-dnd.js',
        ['orgapress-media-folders-tree', 'media-views', 'jquery'],
        ORGAPRESS_LITE_VERSION,
        true
    );

    wp_enqueue_style(
        'orgapress-media-folders',
        ORGAPRESS_LITE_URL . 'includes/media/media-folders.css',
        [],
        ORGAPRESS_LITE_VERSION
    );

    wp_localize_script(
        'orgapress-media-folders',
        'OrgaPressMedia',
        [
            'taxonomy' => self::TAXONOMY,
        ]
    );
}

public static function filter_media_library(array $query): array
{
    if (!current_user_can(self::CAPABILITY)) {
        return $query;
    }

    if (!empty($_REQUEST[self::TAXONOMY])) {
        $query['tax_query'] = [
            [
                'taxonomy' => self::TAXONOMY,
                'field' => 'term_id',
                'terms' => (int) $_REQUEST[self::TAXONOMY],
            ],
        ];
    }

    return $query;
}

public static function attachment_field(array $fields, \WP_Post $post): array
{
    if (!current_user_can(self::CAPABILITY)) {
        return $fields;
    }

    $terms = wp_get_object_terms($post->ID, self::TAXONOMY, ['fields' => 'ids']);
    $current = $terms ? (int) $terms[0] : 0;

    $dropdown = wp_dropdown_categories([
        'taxonomy' => self::TAXONOMY,
        'hide_empty' => false,
        'name' => self::TAXONOMY,
        'selected' => $current,
        'show_option_none' => __('Kein Ordner', 'orgapress-lite'),
        'echo' => false,
    ]);

    $fields[self::TAXONOMY] = [
        'label' => __('Medienordner', 'orgapress-lite'),
        'input' => 'html',
        'html'  => $dropdown,
    ];

    return $fields;
}

public static function save_attachment_field(array $post, array $attachment): array
{
    if (!current_user_can(self::CAPABILITY)) {
        return $post;
    }

    if (isset($attachment[self::TAXONOMY])) {
        wp_set_object_terms(
            (int) $post['ID'],
            (int) $attachment[self::TAXONOMY],
            self::TAXONOMY,
            false
        );
    }

    return $post;
}

public static function register_bulk_action(array $actions): array
{
    $actions['orgapress_assign_folder'] = __('Medienordner zuweisen', 'orgapress-lite');
    return $actions;
}

public static function handle_bulk_action(string $redirect, string $action, array $ids): string
{
    if ($action !== 'orgapress_assign_folder') {
        return $redirect;
    }

    if (!current_user_can(self::CAPABILITY)) {
        return $redirect;
    }

    $default = term_exists(self::DEFAULT_FOLDERS[0], self::TAXONOMY);
    if (!$default || empty($default['term_id'])) {
        return $redirect;
    }

    foreach ($ids as $id) {
        wp_set_object_terms((int) $id, (int) $default['term_id'], self::TAXONOMY, false);
    }

    return add_query_arg('orgapress_bulk_assigned', count($ids), $redirect);
}

}

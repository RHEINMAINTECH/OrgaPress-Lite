<?php
namespace OrgaPressLite\SEO;

if (!defined('ABSPATH')) {
    exit;
}

class SEO
{
    public static function init(): void
    {
        add_action('add_meta_boxes', [self::class, 'meta_box']);
        add_action('save_post', [self::class, 'save']);
        add_action('wp_head', [self::class, 'output'], 1);
    }

    public static function meta_box(): void
    {
        add_meta_box(
            'orgapress_seo',
            __('SEO Einstellungen', 'orgapress-lite'),
            [self::class, 'render'],
            ['post', 'page'],
            'normal',
            'default'
        );
    }

    public static function render($post): void
    {
        if (!current_user_can('edit_post', $post->ID)) {
            return;
        }

        wp_nonce_field('orgapress_seo_save', 'orgapress_seo_nonce');

        $title = get_post_meta($post->ID, '_orgapress_meta_title', true);
        $desc  = get_post_meta($post->ID, '_orgapress_meta_desc', true);
        ?>

    <p>
        <label for="orgapress_meta_title"><?php esc_html_e('Meta Title', 'orgapress-lite'); ?></label>
        <input
            type="text"
            id="orgapress_meta_title"
            name="orgapress_meta_title"
            value="<?php echo esc_attr($title); ?>"
            style="width:100%;"
        />
    </p>
    <p>
        <label for="orgapress_meta_desc"><?php esc_html_e('Meta Description', 'orgapress-lite'); ?></label>
        <textarea
            id="orgapress_meta_desc"
            name="orgapress_meta_desc"
            style="width:100%;"
        ><?php echo esc_textarea($desc); ?></textarea>
    </p>
    <?php
}

public static function save(int $post_id): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (
        !isset($_POST['orgapress_seo_nonce']) ||
        !wp_verify_nonce($_POST['orgapress_seo_nonce'], 'orgapress_seo_save')
    ) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['orgapress_meta_title'])) {
        update_post_meta(
            $post_id,
            '_orgapress_meta_title',
            sanitize_text_field(wp_unslash($_POST['orgapress_meta_title']))
        );
    }

    if (isset($_POST['orgapress_meta_desc'])) {
        update_post_meta(
            $post_id,
            '_orgapress_meta_desc',
            sanitize_textarea_field(wp_unslash($_POST['orgapress_meta_desc']))
        );
    }
}

public static function output(): void
{
    if (!is_singular()) {
        return;
    }

    global $post;

    if (!$post) {
        return;
    }

    $title = get_post_meta($post->ID, '_orgapress_meta_title', true);
    $desc  = get_post_meta($post->ID, '_orgapress_meta_desc', true);

    if (!empty($title)) {
        echo '<title>' . esc_html($title) . '</title>' . PHP_EOL;
    }

    if (!empty($desc)) {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . PHP_EOL;
    }
}

}

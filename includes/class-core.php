<?php
namespace OrgaPressLite;

if (!defined('ABSPATH')) {
exit;
}

class Core
{
    public static function activate(): void
    {
        self::includes();
        \OrgaPressLite\Media\MediaFolders::register_taxonomy();
        \OrgaPressLite\Media\MediaFolders::ensure_default_folders();
        \OrgaPressLite\Privacy\PrivacyAudit::maybe_create_table();
        \OrgaPressLite\Roles\Roles::register_roles();
        
        // Automatische Installation von Abhängigkeiten & Cleanup
        \OrgaPressLite\DependencyManager::install_required_plugins();
        \OrgaPressLite\DependencyManager::remove_unwanted_plugins();

        flush_rewrite_rules();
    }

    public static function init(): void
    {
        self::load_textdomain();
        self::includes();
    }

    private static function load_textdomain(): void
    {
        load_plugin_textdomain(
            'orgapress-lite',
            false,
            dirname(plugin_basename(__FILE__), 2) . '/languages'
        );
    }

private static function includes(): void
{
    require_once ORGAPRESS_LITE_PATH . 'includes/class-dependency-manager.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/admin/class-menu.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/editor/class-classic-editor.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/settings/class-smtp.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/privacy/class-cookie-banner.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/privacy/class-privacy-pro.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/privacy/class-privacy-audit.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/privacy/class-privacy-audit-viewer.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/security/class-security.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/roles/class-roles.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/seo/class-seo.php';
    require_once ORGAPRESS_LITE_PATH . 'includes/media/class-media-folders.php';

    Admin\Menu::init();
    Editor\ClassicEditor::init();
    Settings\SMTP::init();
    Privacy\CookieBanner::init();
    Privacy\PrivacyPro::init();
    Privacy\PrivacyAudit::init();
    Privacy\PrivacyAuditViewer::init();
    Security\Security::init();
    SEO\SEO::init();
    Media\MediaFolders::init();
}

}

<?php
namespace OrgaPressLite\Roles;

if (!defined('ABSPATH')) {
exit;
}

class Roles
{
public static function init(): void
{
add_action('init', [self::class, 'register_roles']);
}

public static function register_roles(): void
{
    // Rollen sollten nur einmal registriert werden
    if (get_role('orgapress_manager')) {
        return;
    }

    add_role(
        'orgapress_manager',
        __('OrgaPress Manager', 'orgapress-lite'),
        [
            'read'           => true,
            'edit_posts'     => true,
            'edit_pages'     => true,
            'manage_options' => true,
        ]
    );
}

}

<?php
/**
 * Plugin Name: Toasted Marshmallow Mode
 * Plugin URI: https://github.com/jcjason12108-alt/Toasted-Marshmallow-Mode-WordPress-Plugin-
 * Description: Adds a Coming Soon / Maintenance splash screen with customizable logo and text.
 * Version: 1.9.1
 * Author: Jason Cox
 * Author URI: https://jasoncox.cloud
 * Requires at least: 5.8
 * Tested up to: 7.0
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: toasted-marshmallow-mode
 */

defined('ABSPATH') || exit;

if (!defined('TOASTED_MARSHMALLOW_VERSION')) {
    define('TOASTED_MARSHMALLOW_VERSION', '1.9.1');
}

require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';

$toasted_marshmallow_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
    'https://github.com/jcjason12108-alt/Toasted-Marshmallow-Mode-WordPress-Plugin-',
    __FILE__,
    'Toasted'
);
$toasted_marshmallow_update_checker->setBranch('main');

add_filter(
    $toasted_marshmallow_update_checker->getUniqueName('vcs_update_detection_strategies'),
    static function (array $strategies): array {
        return isset($strategies['branch']) ? ['branch' => $strategies['branch']] : $strategies;
    }
);

$toasted_marshmallow_github_token = defined('PLUGIN_UPDATE_GITHUB_TOKEN')
    ? PLUGIN_UPDATE_GITHUB_TOKEN
    : getenv('PLUGIN_UPDATE_GITHUB_TOKEN');

if (!empty($toasted_marshmallow_github_token)) {
    $toasted_marshmallow_update_checker->setAuthentication($toasted_marshmallow_github_token);
}

// ========== FRONTEND LOGIC ==========
add_action('template_redirect', function () {
    if (current_user_can('manage_options')) {
        return;
    }

    $mode = get_option('toasted_marshmallow_mode', 'coming_soon');
    if ($mode === 'disabled') {
        return;
    }

    if ($mode === 'maintenance') {
        status_header(503);
        header('Retry-After: 3600');
    }

    $logo_url = get_option('toasted_marshmallow_logo', 'https://jasoncox.cloud/wp-content/uploads/2025/08/cropped-55C968AA-F132-483E-AFC9-B79720A27193.png');
    $tagline = get_option('toasted_marshmallow_tagline', 'Site Under Construction');
    $subtext = get_option('toasted_marshmallow_subtext', "We're building something gooey and golden.<br>Please check back soon!");
    $mode_label = $mode === 'maintenance' ? 'Maintenance Mode' : 'Coming Soon';
    $http_code = $mode === 'maintenance' ? '503' : '200';
    $bg_color = $mode === 'maintenance' ? '#2a1206' : '#1a1a1a';
    $title = sprintf(
        /* translators: %s is the active public site mode label. */
        __('Toasted Marshmallow - %s', 'toasted-marshmallow-mode'),
        $mode_label
    );

    $title = esc_html($title);
    $mode_label = esc_html($mode_label);
    $logo_url = esc_url($logo_url);
    $tagline = esc_html($tagline);
    $subtext = wp_kses_post($subtext);
    $bg_color = esc_attr($bg_color);
    $language_attributes = get_language_attributes();

    echo <<<HTML
<!DOCTYPE html>
<html {$language_attributes}>
<head>
  <meta charset="UTF-8">
  <title>{$title}</title>
  <style>
    body {
      background: {$bg_color};
      color: #ffffff;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      height: 100vh;
      margin: 0;
      padding: 40px 20px 0;
      text-align: center;
    }
    .mode-header {
      font-size: 2.2rem;
      font-weight: bold;
      margin-bottom: 40px;
    }
    img.logo {
      max-width: 300px;
      height: auto;
      margin-bottom: 30px;
    }
    h1 {
      font-size: 3em;
      margin-bottom: 0.4em;
    }
    p {
      font-size: 1.3em;
      color: #dddddd;
    }
  </style>
</head>
<body>
  <div class="mode-header">{$mode_label} - {$http_code}</div>
  <div>
    <img class="logo" src="{$logo_url}" alt="Toasted Marshmallow Logo">
    <h1>{$tagline}</h1>
    <p>{$subtext}</p>
  </div>
</body>
</html>
HTML;

    exit;
});


// ========== SETTINGS PAGE ==========
add_action('admin_menu', function () {
    add_options_page(
        'Toasted Marshmallow Mode Settings',
        'Toasted Marshmallow',
        'manage_options',
        'toasted-marshmallow-mode',
        'toasted_marshmallow_settings_page'
    );
});

add_action('admin_init', function () {
    register_setting(
        'toasted_marshmallow_settings',
        'toasted_marshmallow_mode',
        [
            'sanitize_callback' => 'toasted_marshmallow_sanitize_mode',
        ]
    );
    register_setting(
        'toasted_marshmallow_settings',
        'toasted_marshmallow_logo',
        [
            'sanitize_callback' => 'esc_url_raw',
        ]
    );
    register_setting(
        'toasted_marshmallow_settings',
        'toasted_marshmallow_tagline',
        [
            'sanitize_callback' => 'sanitize_text_field',
        ]
    );
    register_setting(
        'toasted_marshmallow_settings',
        'toasted_marshmallow_subtext',
        [
            'sanitize_callback' => 'wp_kses_post',
        ]
    );
});

add_filter('option_page_capability_toasted_marshmallow_settings', function () {
    return 'manage_options';
});

if (!function_exists('toasted_marshmallow_sanitize_mode')) {
    function toasted_marshmallow_sanitize_mode($mode) {
        $allowed_modes = [
            'coming_soon',
            'maintenance',
            'disabled',
        ];

        return in_array($mode, $allowed_modes, true) ? $mode : 'coming_soon';
    }
}

if (!function_exists('toasted_marshmallow_settings_page')) {
    function toasted_marshmallow_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have permission to access this page.', 'toasted-marshmallow-mode'));
        }

        $logo = get_option('toasted_marshmallow_logo', 'https://jasoncox.cloud/wp-content/uploads/2025/08/cropped-55C968AA-F132-483E-AFC9-B79720A27193.png');
        $tagline = get_option('toasted_marshmallow_tagline', 'Site Under Construction');
        $subtext = get_option('toasted_marshmallow_subtext', "We're building something gooey and golden.<br>Please check back soon!");
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Toasted Marshmallow Mode', 'toasted-marshmallow-mode'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('toasted_marshmallow_settings'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Mode', 'toasted-marshmallow-mode'); ?></th>
                        <td>
                            <select name="toasted_marshmallow_mode">
                                <option value="coming_soon" <?php selected(get_option('toasted_marshmallow_mode', 'coming_soon'), 'coming_soon'); ?>>
                                    <?php esc_html_e('Coming Soon (200 OK)', 'toasted-marshmallow-mode'); ?>
                                </option>
                                <option value="maintenance" <?php selected(get_option('toasted_marshmallow_mode', 'coming_soon'), 'maintenance'); ?>>
                                    <?php esc_html_e('Maintenance (503)', 'toasted-marshmallow-mode'); ?>
                                </option>
                                <option value="disabled" <?php selected(get_option('toasted_marshmallow_mode', 'coming_soon'), 'disabled'); ?>>
                                    <?php esc_html_e('Disabled', 'toasted-marshmallow-mode'); ?>
                                </option>
                            </select>
                            <p class="description"><?php esc_html_e('Choose which mode to show visitors.', 'toasted-marshmallow-mode'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Logo URL', 'toasted-marshmallow-mode'); ?></th>
                        <td>
                            <input type="url" name="toasted_marshmallow_logo" value="<?php echo esc_attr($logo); ?>" style="width: 100%;">
                            <p class="description"><?php esc_html_e('Paste the full URL to your logo image (e.g., from Media Library).', 'toasted-marshmallow-mode'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Main Tagline (H1)', 'toasted-marshmallow-mode'); ?></th>
                        <td>
                            <input type="text" name="toasted_marshmallow_tagline" value="<?php echo esc_attr($tagline); ?>" style="width: 100%;">
                            <p class="description"><?php esc_html_e('Example: Site Under Construction', 'toasted-marshmallow-mode'); ?></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php esc_html_e('Subtext (Paragraph)', 'toasted-marshmallow-mode'); ?></th>
                        <td>
                            <textarea name="toasted_marshmallow_subtext" rows="4" style="width: 100%;"><?php echo esc_textarea($subtext); ?></textarea>
                            <p class="description"><?php esc_html_e('You can include simple HTML (like <br> tags).', 'toasted-marshmallow-mode'); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(__('Save Changes', 'toasted-marshmallow-mode')); ?>
            </form>
        </div>
        <?php
    }
}

// ========== DEFAULT SETTINGS ON FIRST INSTALL ==========
register_activation_hook(__FILE__, function () {
    $defaults = [
        'toasted_marshmallow_mode'    => 'coming_soon',
        'toasted_marshmallow_logo'    => 'https://jasoncox.cloud/wp-content/uploads/2025/08/cropped-55C968AA-F132-483E-AFC9-B79720A27193.png',
        'toasted_marshmallow_tagline' => 'Site Under Construction',
        'toasted_marshmallow_subtext' => "We're building something gooey and golden.<br>Please check back soon!",
    ];

    foreach ($defaults as $key => $value) {
        if (get_option($key) === false) {
            add_option($key, $value);
        }
    }
});

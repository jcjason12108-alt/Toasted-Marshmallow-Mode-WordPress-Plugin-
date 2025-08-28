<?php
/**
 * Plugin Name: Toasted Marshmallow Mode
 * Plugin URI: https://jasoncox.cloud/maintenance-coming-soon-plugin/
 * Description: Adds a Coming Soon / Maintenance splash screen with customizable logo and text.
 * Version: 1.8
 * Author: Jason Cox
 * Author URI: https://jasoncox.cloud
 */

// ========== FRONTEND LOGIC ==========
add_action('template_redirect', function () {
    if (current_user_can('manage_options')) return;

    $mode = get_option('toasted_marshmallow_mode', 'coming_soon');
    if ($mode === 'disabled') return;

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

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Toasted Marshmallow – {$mode_label}</title>
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
  <div class="mode-header">{$mode_label} — {$http_code}</div>
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
    register_setting('toasted_marshmallow_settings', 'toasted_marshmallow_mode');
    register_setting('toasted_marshmallow_settings', 'toasted_marshmallow_logo');
    register_setting('toasted_marshmallow_settings', 'toasted_marshmallow_tagline');
    register_setting('toasted_marshmallow_settings', 'toasted_marshmallow_subtext');
});

function toasted_marshmallow_settings_page() {
    $logo = get_option('toasted_marshmallow_logo', 'https://jasoncox.cloud/wp-content/uploads/2025/08/cropped-55C968AA-F132-483E-AFC9-B79720A27193.png');
    $tagline = get_option('toasted_marshmallow_tagline', 'Site Under Construction');
    $subtext = get_option('toasted_marshmallow_subtext', "We're building something gooey and golden.<br>Please check back soon!");
    ?>
    <div class="wrap">
        <h1>Toasted Marshmallow Mode</h1>
        <form method="post" action="options.php">
            <?php settings_fields('toasted_marshmallow_settings'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Mode</th>
                    <td>
                        <select name="toasted_marshmallow_mode">
                            <option value="coming_soon" <?php selected(get_option('toasted_marshmallow_mode', 'coming_soon'), 'coming_soon'); ?>>
                                Coming Soon (200 OK)
                            </option>
                            <option value="maintenance" <?php selected(get_option('toasted_marshmallow_mode', 'coming_soon'), 'maintenance'); ?>>
                                Maintenance (503)
                            </option>
                            <option value="disabled" <?php selected(get_option('toasted_marshmallow_mode', 'coming_soon'), 'disabled'); ?>>
                                Disabled
                            </option>
                        </select>
                        <p class="description">Choose which mode to show visitors.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Logo URL</th>
                    <td>
                        <input type="text" name="toasted_marshmallow_logo" value="<?php echo esc_attr($logo); ?>" style="width: 100%;">
                        <p class="description">Paste the full URL to your logo image (e.g., from Media Library).</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Main Tagline (H1)</th>
                    <td>
                        <input type="text" name="toasted_marshmallow_tagline" value="<?php echo esc_attr($tagline); ?>" style="width: 100%;">
                        <p class="description">Example: Site Under Construction</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Subtext (Paragraph)</th>
                    <td>
                        <textarea name="toasted_marshmallow_subtext" rows="4" style="width: 100%;"><?php echo esc_textarea($subtext); ?></textarea>
                        <p class="description">You can include simple HTML (like &lt;br&gt; tags).</p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Changes'); ?>
        </form>
    </div>
    <?php
}

// ========== DEFAULT SETTINGS ON FIRST INSTALL ==========
register_activation_hook(__FILE__, function () {
    $defaults = [
        'toasted_marshmallow_mode'    => 'coming_soon',
        'toasted_marshmallow_logo'    => 'https://jasoncox.cloud/wp-content/uploads/2025/08/cropped-55C968AA-F132-483E-AFC9-B79720A27193.png',
        'toasted_marshmallow_tagline' => 'Site Under Construction',
        'toasted_marshmallow_subtext' => "We're building something gooey and golden.<br>Please check back soon!"
    ];

    foreach ($defaults as $key => $value) {
        if (get_option($key) === false) {
            add_option($key, $value);
        }
    }
});
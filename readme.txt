=== Toasted Marshmallow Mode ===
Contributors: Jason Cox
Tags: maintenance mode, coming soon, splash screen
Requires at least: 5.8
Tested up to: 6.9.4
Requires PHP: 7.4
Stable tag: 1.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a Coming Soon / Maintenance splash screen with customizable logo and text.

== Description ==

Toasted Marshmallow Mode lets administrators show either a Coming Soon page with a 200 response or a Maintenance Mode page with a 503 response while keeping the site available to users who can manage options.

== Installation ==

1. Upload the Toasted folder to `/wp-content/plugins/`.
2. Activate Toasted Marshmallow Mode from the Plugins screen.
3. Configure the plugin from Settings > Toasted Marshmallow.

== Changelog ==

= 1.9 =
* Added GitHub-based automatic updates through Plugin Update Checker.
* Added branch-only update checks for the main branch.
* Added optional GitHub token support through the `PLUGIN_UPDATE_GITHUB_TOKEN` constant or environment variable.
* Updated WordPress compatibility to 6.9.4.
* Added setting sanitization, escaped frontend output, and complete plugin/readme metadata.

= 1.8 =
* Existing Coming Soon and Maintenance Mode functionality.

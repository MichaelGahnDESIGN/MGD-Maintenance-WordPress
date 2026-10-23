=== MGD Maintenance Studio ===
Contributors: michaelgahndesign
Tags: maintenance, coming soon
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 0.5.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Design maintenance pages with previews, controlled access and error pages.

== Description ==

Design maintenance pages with previews, controlled access and error pages.

Features:

* Design separate maintenance layouts with local logos and background images.
* Review drafts in a protected preview before publishing.
* Configure role and IP access rules and legal-page access.
* Use a 503 response and crawler hints while maintenance is active.
* Keep optional error pages separate from the maintenance state.

This edition uses native WordPress.org updates. There is no GitHub self-updater or external plugin installer. Updates become available after approval and publication in the directory.

Documentation and source: https://github.com/MichaelGahnDESIGN/MGD-Maintenance-WordPress



== Installation ==

1. Upload the plugin folder or install the approved directory release from Plugins > Add New.
2. Activate the plugin and review its settings.
3. Test the configured features before enabling them on a production website.

== Frequently Asked Questions ==

= How are updates delivered? =
WordPress checks the official directory. This edition has no configurable update repository.

= Does it send analytics? =
No analytics or telemetry service is included. WordPress communicates with WordPress.org for native updates. Support links open only when clicked.

== Screenshots ==

1. Maintenance dashboard with status control and links to design and error pages.

== Changelog ==

= 0.5.5 =
* Render standalone maintenance resources through native WordPress asset handles.
* Document narrowly scoped read-only nonce exceptions and the required cache integration constant.
* Prepared a WordPress.org candidate with native updates, local assets and reviewed public credits.

== Upgrade Notice ==

= 0.5.5 =
Submission candidate. Do not activate alongside the GitHub edition. Back up the database before changing distribution channels.

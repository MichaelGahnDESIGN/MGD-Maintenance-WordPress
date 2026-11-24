<?php
namespace MGMS;
if ( ! defined( 'ABSPATH' ) ) {
	exit; }
$design_fields = Schema::fields();
$groups = array(
	'Layout und Raum' => array( 'layout', 'container', 'align', 'width', 'padding', 'padding_tablet', 'padding_mobile', 'radius', 'border', 'shadow' ),
	'Farben' => array( 'background', 'text', 'accent', 'button', 'button_text', 'card', 'border_color' ),
	'Markenbereich' => array( 'brand_presentation', 'brand', 'logo', 'logo_alt', 'logo_width', 'logo_link' ),
	'Fläche, Hintergrund und Gimmick' => array( 'background_image', 'background_fit', 'background_position', 'image_effect', 'gimmick_image', 'gimmick_position', 'gimmick_motion' ),
	'Effekte' => array( 'effect', 'effect_intensity' ),
	'Typografie und Texte' => array( 'tagline', 'font', 'font_file' ),
	'Feinschliff' => array( 'animation', 'credit', 'credit_url' ),
);
?>
<p class="mgms-panel-intro">Wähle einen Ausgangspunkt und passe ihn rechts in der Vorschau an. Farben, Logo und Layout bleiben bis zur ausdrücklichen Veröffentlichung im Entwurf.</p>
<div class="mgms-design-presets" role="group" aria-label="Gestaltungsvorlagen">
<?php foreach ( Admin::presets() as $key => $preset ) : ?>
<button type="button" class="mgms-preset-card" data-preset="<?php echo esc_attr( $key ); ?>" aria-pressed="<?php echo $s['preset'] === $key ? 'true' : 'false'; ?>">
<span class="mgms-preset-art mgms-preset-art--<?php echo esc_attr( $key ); ?>" aria-hidden="true"><span></span><span></span><span></span></span>
<strong><?php echo esc_html( $preset['name'] ); ?></strong><small><?php echo esc_html( $design_fields['layout'][4][ $preset['values']['layout'] ] ?? '' ); ?></small><em><?php echo esc_html( ! empty( $preset['pro'] ) ? 'Pro-Szene' : ( array( 'editorial' => 'Redaktionell mit klarer Lesespalte', 'centered' => 'Zentrierte Fokusansicht', 'split' => 'Geteilte Inhalte mit Infokarte', 'poster' => 'Großes Statement als Poster', 'cinema' => 'Bild als Bühne, Text wie ein Filmtitel', 'atelier' => 'Bildhälfte links, ruhige Textseite rechts', 'swiss' => 'Riesige Überschrift, klares Raster', 'glass' => 'Dunkle Glaskarte über Leuchtflächen', 'bloom' => 'Runde Karte, schwebende Farben' )[ $preset['values']['layout'] ] ?? '' ) ); ?></em>
</button>
<?php endforeach; ?>
<?php
// Schloss-Karten für Pro-Szenen ohne Paket
foreach ( Pro_Store::CATALOG as $mgms_key => $mgms_info ) :
	if ( Pro_Store::has_scene( $mgms_key ) ) {
		continue;
	}
	?>
<a class="mgms-preset-card mgms-preset-card--locked" href="<?php echo esc_url( \Mgd\Maintenance\Kit\Platform\WordPress\Tools::url( \Mgd\Maintenance\Kit\Platform\WordPress\Tools::PAGE_LICENSE ) ); ?>">
<span class="mgms-preset-art mgms-preset-art--pro-<?php echo esc_attr( $mgms_key ); ?>" aria-hidden="true"><span></span><span></span><span></span></span>
<strong><?php echo esc_html( $mgms_info[0] ); ?> <span class="mgms-pro-badge">PRO</span></strong><small><?php echo esc_html( $mgms_info[1] ); ?></small><em>Mit Pro-Lizenz freischalten</em>
</a>
<?php endforeach; ?>
</div>
<div class="mgms-design-tools"><button type="button" class="button" id="undo-preset" disabled>Vorherige Auswahl zurückholen</button><span id="contrast-hint" role="status"></span></div>
<?php foreach ( $groups as $heading => $keys ) : ?>
<div class="mgms-design-group"><h3><?php echo esc_html( $heading ); ?></h3><div class="mgms-design-fields">
<?php foreach ( $keys as $key ) : Admin::field( $key, $design_fields[ $key ], $s ); endforeach; ?>
</div></div>
<?php endforeach; ?>

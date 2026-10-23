<?php
namespace MGMS;
if ( ! defined( 'ABSPATH' ) ) {
	exit; }
$design_fields = Schema::fields();
$groups = array(
	'Layout und Raum' => array( 'layout', 'container', 'align', 'width', 'padding', 'padding_tablet', 'padding_mobile', 'radius', 'border', 'shadow' ),
	'Farben' => array( 'background', 'text', 'accent', 'button', 'button_text', 'card', 'border_color' ),
	'Logo und Hintergrund' => array( 'logo', 'logo_alt', 'logo_width', 'logo_link', 'background_image' ),
	'Typografie und Marke' => array( 'brand', 'tagline', 'font', 'font_file' ),
	'Feinschliff' => array( 'animation', 'credit', 'credit_url' ),
);
?>
<p class="mgms-panel-intro">Wähle einen Ausgangspunkt und passe ihn rechts in der Vorschau an. Farben, Logo und Layout bleiben bis zur ausdrücklichen Veröffentlichung im Entwurf.</p>
<div class="mgms-design-presets" role="group" aria-label="Gestaltungsvorlagen">
<?php foreach ( Admin::presets() as $key => $preset ) : ?>
<button type="button" class="mgms-preset-card" data-preset="<?php echo esc_attr( $key ); ?>" aria-pressed="<?php echo $s['preset'] === $key ? 'true' : 'false'; ?>">
<span class="mgms-preset-art mgms-preset-art--<?php echo esc_attr( $key ); ?>" aria-hidden="true"><span></span><span></span><span></span></span>
<strong><?php echo esc_html( $preset['name'] ); ?></strong><small><?php echo esc_html( $design_fields['layout'][4][ $preset['values']['layout'] ] ); ?></small>
</button>
<?php endforeach; ?>
</div>
<div class="mgms-design-tools"><button type="button" class="button" id="undo-preset" disabled>Vorherige Auswahl zurückholen</button><span id="contrast-hint" role="status"></span></div>
<?php foreach ( $groups as $heading => $keys ) : ?>
<div class="mgms-design-group"><h3><?php echo esc_html( $heading ); ?></h3><div class="mgms-design-fields">
<?php foreach ( $keys as $key ) : Admin::field( $key, $design_fields[ $key ], $s ); endforeach; ?>
</div></div>
<?php endforeach; ?>

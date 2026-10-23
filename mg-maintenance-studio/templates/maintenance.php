<?php
namespace MGMS;

// phpcs:disable WordPress.WP.EnqueuedResources -- Eigenständiges Dokument ohne Theme-Hooks; nur eigene lokale Assets ausgeben.

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php echo esc_attr( get_option( 'blog_charset' ) ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow, noarchive">
<title><?php echo esc_html( $s['title'] ); ?></title>
<link rel="stylesheet" href="<?php echo esc_url( MGMS_URL . 'assets/frontend.css?ver=' . MGMS_VERSION ); ?>">
<?php
if ( $font ) :
	?>
	<style>@font-face{font-family:MGMSLocal;src:url(<?php echo esc_url( $font ); ?>) format('woff2');font-display:swap}.mgms h1{font-family:MGMSLocal,system-ui,sans-serif}</style><?php endif; ?>
<script defer src="<?php echo esc_url( MGMS_URL . 'assets/frontend.js?ver=' . MGMS_VERSION ); ?>"></script>
<?php
if ( $preview ) :
	?>
	<script defer src="<?php echo esc_url( MGMS_URL . 'assets/preview.js?ver=' . MGMS_VERSION ); ?>"></script><?php endif; ?>
</head>
<body class="mgms mgms-layout-<?php echo esc_attr( in_array( $s['layout'], array( 'editorial', 'centered', 'split', 'poster' ), true ) ? $s['layout'] : 'editorial' ); ?> <?php echo $s['animation'] ? 'animate' : ''; ?>" style="<?php echo esc_attr( Renderer::variables( $s ) ); ?>" data-backdrop="<?php echo $s['modal_backdrop'] ? '1' : '0'; ?>">
<a class="skip" href="#main">Zum Inhalt</a>
<?php
if ( $background ) :
	?>
	<img class="background-image" src="<?php echo esc_url( $background ); ?>" alt=""><?php endif; ?>
<?php
if ( $preview ) :
	?>
	<div class="preview-label">Geschützte Vorschau · Entwurf · nicht veröffentlicht</div><?php endif; ?>
<div class="page-shell">
<header class="brand"><span data-live="brand"><?php echo esc_html( $s['brand'] ); ?></span></header>
<main id="main" tabindex="-1" class="content <?php echo 'card' === $s['container'] ? 'is-card' : ''; ?> <?php echo $s['shadow'] ? 'has-shadow' : ''; ?>">
<div class="mgms-logo-slot">
<?php
if ( $logo ) :
	?>
	<?php
	if ( $s['logo_link'] ) :
		?>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php endif; ?><img class="logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $s['logo_alt'] ); ?>">
	<?php
	if ( $s['logo_link'] ) :
		?>
	</a><?php endif; ?><?php endif; ?>
</div>
<p class="eyebrow" data-live="tagline"><?php echo esc_html( $s['tagline'] ); ?></p>
<div class="message-grid"><div class="message-copy">
<h1 data-live="title"><?php echo esc_html( $s['title'] ); ?></h1>
<p class="description" data-live="description"><?php echo esc_html( $s['description'] ); ?></p>
</div><div class="message-details">
<p class="notice" data-live="notice"><?php echo esc_html( $s['notice'] ); ?></p>
<?php
if ( $s['countdown'] ) :
	?>
	<p class="return-time">Geplante Rückkehr: <time datetime="<?php echo esc_attr( $s['countdown'] ); ?>"><?php echo esc_html( wp_date( 'd.m.Y H:i T', strtotime( $s['countdown'] ) ) ); ?></time></p><p class="countdown" data-until="<?php echo esc_attr( $s['countdown'] ); ?>" aria-label="Verbleibende Zeit"></p><?php endif; ?>
<?php
if ( $s['contact_url'] ) :
	?>
	<a class="contact-button" href="<?php echo esc_url( $s['contact_url'], array( 'http', 'https', 'mailto', 'tel' ) ); ?>"><?php echo esc_html( $s['contact_label'] ); ?></a><?php endif; ?>
<div class="contact-details">
<?php
if ( $s['email'] ) :
	?>
	<a href="<?php echo esc_url( 'mailto:' . $s['email'] ); ?>"><?php echo esc_html( $s['email'] ); ?></a><?php endif; ?>
<?php
if ( $s['phone'] ) :
	?>
	<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^+0-9]/', '', $s['phone'] ) ); ?>"><?php echo esc_html( $s['phone'] ); ?></a><?php endif; ?>
</div>
<p data-live="newsletter"><?php echo esc_html( $s['newsletter'] ); ?></p>
</div></div>
</main>
<footer class="site-footer"><nav aria-label="Rechtliche Hinweise">
<?php
foreach ( $legal as $index => $row ) :
	?>
	<?php
	if ( $row['footer'] ) :
		?>
	<a href="<?php echo esc_url( $row['page'] ? get_permalink( $row['page'] ) : '#legal-' . $index ); ?>"
		<?php
		if ( $row['modal'] && ! $row['page'] ) :
			?>
	data-modal="legal-<?php echo (int) $index; ?>"<?php endif; ?>><?php echo esc_html( $row['title'] ); ?></a><?php endif; ?><?php endforeach; ?>
</nav>
<?php
if ( $s['credit'] ) :
	?>
	<p class="credit">
	<?php
	if ( $s['credit_url'] ) :
		?>
	<a href="<?php echo esc_url( $s['credit_url'] ); ?>"><?php endif; ?>supported by Michael Gahn DESIGN
	<?php
	if ( $s['credit_url'] ) :
		?>
	</a><?php endif; ?></p><?php endif; ?>
</footer>
<div class="legal-sections">
<?php
foreach ( $legal as $index => $row ) :
	?>
	<?php
	if ( ! $row['page'] ) :
		?>
	<section class="legal" id="legal-<?php echo (int) $index; ?>" aria-labelledby="legal-title-<?php echo (int) $index; ?>"><h2 id="legal-title-<?php echo (int) $index; ?>"><?php echo esc_html( $row['title'] ); ?></h2><div class="legal-content"><?php echo wp_kses_post( wpautop( $row['content'] ) ); ?></div><a class="legal-back" href="#main">Zurück zum Inhalt</a></section><?php endif; ?><?php endforeach; ?>
</div>
</div>
</body></html>

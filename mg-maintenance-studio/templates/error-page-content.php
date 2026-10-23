<?php
namespace MGMS;
if ( ! defined( 'ABSPATH' ) ) {
	exit; }
wp_enqueue_style( 'mgms-error-page', MGMS_URL . 'assets/error-page.css', array(), MGMS_VERSION );
$mgms_style = '--page-bg:' . $s['background'] . ';--page-accent:' . $s['accent'] . ';--page-text:' . $s['text'];
?><!doctype html>
<html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo( 'charset' ); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title><?php echo esc_html( $code . ' · ' . $s['title'] ); ?></title><?php wp_print_styles( 'mgms-error-page' ); ?></head>
<body class="mgms-error mgms-error--<?php echo esc_attr( $s['template'] ); ?>" style="<?php echo esc_attr( $mgms_style ); ?>">
<main class="mgms-error__layout"><div class="mgms-error__brand"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></div><div class="mgms-error__content"><p class="mgms-error__code"><?php echo esc_html( $code ); ?></p><h1><?php echo esc_html( $s['title'] ); ?></h1><p class="mgms-error__message"><?php echo esc_html( $s['message'] ); ?></p><div class="mgms-error__actions"><a class="mgms-error__button" href="<?php echo esc_url( $home ); ?>"><?php echo esc_html( $s['button'] ); ?></a><?php if ( '404' === $code && $s['search'] ) : ?><form role="search" method="get" action="<?php echo esc_url( $home ); ?>"><label class="screen-reader-text" for="mgms-error-search">Website durchsuchen</label><input id="mgms-error-search" type="search" name="s" placeholder="Website durchsuchen"><button type="submit">Suchen</button></form><?php endif; ?></div></div><p class="mgms-error__footer"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></p></main>
</body></html>

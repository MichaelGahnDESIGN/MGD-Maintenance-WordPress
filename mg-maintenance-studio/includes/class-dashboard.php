<?php
namespace MGMS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }

final class Dashboard {
	public static function page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$live = Settings::get();
		$draft = Settings::get( true );
		$enabled = (bool) get_option( Settings::STATE );
		$errors = Error_Pages::get();
		$updates = array( 'enabled' => true );
		$layouts = Schema::fields()['layout'][4];
		require MGMS_DIR . 'templates/dashboard.php';
	}
}

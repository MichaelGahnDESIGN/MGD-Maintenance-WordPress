<?php
namespace MGMS;

use Mgd\Maintenance\Kit\License\ProContentClient;
use Mgd\Maintenance\Kit\Platform\WordPress\Setup;

if ( ! defined( 'ABSPATH' ) ) {
	exit; }
// Pro-Paket vom Server holen - täglich per Cron + nach Lizenz-Eingabe im Admin
final class Pro_Sync {
	public const PRODUCT = 'mgd-maintenance-wordpress';
	public const CRON    = 'mgms_pro_sync';
	public const LOCK    = 'mgms_pro_sync_lock';
	public const NOTICE  = 'mgms_pro_too_old';
	// öffentlicher Schlüssel fest eingebaut (kid => Ed25519 public key), nie vom Server übernehmen
	public const KEYS = array( 'b0e52763fa05333a' => 'tKS+FLEtNAzlu8Me1bce5Q604j8e7APywmWPZF5tGRo=' );

	public static function register(): void {
		add_action( self::CRON, array( self::class, 'run' ) );
		add_action( 'admin_init', array( self::class, 'maybe_run' ), 20 );
		if ( ! wp_next_scheduled( self::CRON ) ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', self::CRON );
		}
	}
	public static function unschedule(): void {
		wp_clear_scheduled_hook( self::CRON );
	}
	// im Admin höchstens alle 10 Min, nur Admins, nur wenn nötig
	public static function maybe_run(): void {
		if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) || get_transient( self::LOCK ) ) {
			return;
		}
		if ( Pro_Store::valid() && ! Pro_Store::needs_refresh() && self::license()->has( Pro_Store::ENTITLEMENT ) ) {
			return;
		}
		set_transient( self::LOCK, 1, 10 * MINUTE_IN_SECONDS );
		self::run();
	}
	public static function license(): \Mgd\Maintenance\Kit\License\LicenseClient {
		return Setup::license( self::PRODUCT, 'wordpress', true );
	}
	/** Ergebnis-Status zum Testen/Anzeigen */
	public static function run(): string {
		try {
			$license = self::license();
			// kein Schlüssel oder Lizenz ohne Pro (nach Gnadenfrist) > Pro weg
			if ( ! $license->hasToken() || ! $license->has( Pro_Store::ENTITLEMENT ) ) {
				if ( Pro_Store::data() ) {
					Pro_Store::clear();
				}
				return 'no_license';
			}
			if ( Pro_Store::valid() && ! Pro_Store::needs_refresh() ) {
				return 'fresh';
			}
			$r = Setup::proContent( self::PRODUCT, 'wordpress', self::KEYS, true )->fetch( Pro_Store::PRODUCT, Pro_Store::PACKAGE, MGMS_VERSION );
			return self::apply( $r );
		} catch ( \Throwable ) {
			return 'error';
		}
	}
	/** Antwort vom Baukasten umsetzen */
	public static function apply( array $r ): string {
		$status = (string) ( $r['status'] ?? '' );
		if ( ProContentClient::OK === $status ) {
			$saved = Pro_Store::save(
				(array) ( $r['contents'] ?? array() ),
				array(
					'iat'       => (int) ( $r['iat'] ?? 0 ),
					'exp'       => (int) ( $r['exp'] ?? 0 ),
					'version'   => (string) ( $r['version'] ?? '' ),
					'watermark' => (string) ( $r['watermark'] ?? '' ),
				)
			);
			delete_option( self::NOTICE );
			return true === $saved ? 'saved' : 'invalid';
		}
		if ( ProContentClient::DELETE === $status ) {
			Pro_Store::clear();
			return 'deleted';
		}
		if ( ProContentClient::TOO_OLD === $status ) {
			update_option( self::NOTICE, (string) ( $r['min_plugin_version'] ?? '' ), false );
		}
		// keep / not_found / off: vorhandenes Paket bleibt bis exp
		return $status ?: 'keep';
	}
}

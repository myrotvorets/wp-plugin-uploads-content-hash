<?php

namespace Myrotvorets\WordPress\UploadsContentHash;

use WildWolf\Utils\Singleton;

/**
 * @psalm-type UploadedFile = array{name: string, type: string, tmp_name: string, size: int, error: int}
 */
class Plugin {
	use Singleton;

	/**
	 * @codeCoverageIgnore
	 */
	private function __construct() {
		add_action( 'init', [ $this, 'init' ] );
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function init(): void {
		add_filter( 'wp_handle_upload_prefilter', [ $this, 'upload_prefilter' ] );
		add_filter( 'wp_handle_sideload_prefilter', [ $this, 'upload_prefilter' ] );
	}

	/**
	 * @psalm-param UploadedFile $file
	 * @psalm-return UploadedFile
	 */
	public function upload_prefilter( array $file ): array {
		if ( file_exists( $file['tmp_name'] ) ) {
			$hash = md5_file( $file['tmp_name'] );
			if ( false !== $hash ) {
				$info = pathinfo( $file['name'] );
				$ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];
				$name = basename( $file['name'], $ext );

				$file['name'] = $name . '.' . substr( $hash, 0, 6 ) . $ext;
			}
		}

		return $file;
	}
}

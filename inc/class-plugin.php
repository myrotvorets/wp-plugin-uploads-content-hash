<?php

namespace Myrotvorets\WordPress\UploadsContentHash;

use WildWolf\Utils\Singleton;

/**
 * @psalm-type UploadedFile = array{name: string, type: string, tmp_name: string, size: int, error: int}
 * @psalm-type PostData = array{post_name: string, post_title: string}
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
		add_filter( 'wp_insert_attachment_data', [ $this, 'wp_insert_attachment_data' ] );
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

	/**
	 * @psalm-param PostData $data
	 * @psalm-return PostData
	 */
	public function wp_insert_attachment_data( array $data ): array {
		$name  = $data['post_name'];
		$title = $data['post_title'];

		$matches = [];
		if ( preg_match( '/\\.([0-9a-f]{6})$/', $title, $matches ) && substr( $name, -7 ) === "-{$matches[1]}" ) {
			$data['post_name']  = substr( $name, 0, -7 );
			$data['post_title'] = substr( $title, 0, -7 );
		}

		return $data;
	}
}

<?php

use Myrotvorets\WordPress\UploadsContentHash\Plugin;

/**
 * @psalm-import UploadedFile from Plugin
 */
class Test_Plugin extends WP_UnitTestCase {
	/**
	 * @covers Myrotvorets\WordPress\UploadsContentHash\Plugin::upload_prefilter
	 * @dataProvider data_upload_prefilter
	 * @psalm-param UploadedFile $input
	 * @psalm-param UploadedFile $expected
	 */
	public function test_upload_prefilter( array $input, array $expected ): void {
		$instance = Plugin::instance();

		$actual = $instance->upload_prefilter( $input );
		self::assertEquals( $expected, $actual );
	}

	/**
	 * @psalm-return iterable<string, array{UploadedFile, UploadedFile}>
	 */
	public function data_upload_prefilter(): iterable {
		return [
			'real file'         => [
				[
					'name'     => 'test.txt',
					'type'     => 'text/plain',
					'tmp_name' => __DIR__ . '/fixtures/test.txt',
					'size'     => filesize( __DIR__ . '/fixtures/test.txt' ),
					'error'    => 0,
				],
				[
					'name'     => 'test.e5e13f.txt',
					'type'     => 'text/plain',
					'tmp_name' => __DIR__ . '/fixtures/test.txt',
					'size'     => filesize( __DIR__ . '/fixtures/test.txt' ),
					'error'    => 0,
				],
			],
			'non-existing file' => [
				[
					'name'     => 'does-not-exist.txt',
					'type'     => 'text/plain',
					'tmp_name' => __DIR__ . '/fixtures/does-not-exist.txt',
					'size'     => 0,
					'error'    => 0,
				],
				[
					'name'     => 'does-not-exist.txt',
					'type'     => 'text/plain',
					'tmp_name' => __DIR__ . '/fixtures/does-not-exist.txt',
					'size'     => 0,
					'error'    => 0,
				],
			],
		];
	}
}

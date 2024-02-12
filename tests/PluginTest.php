<?php

use Myrotvorets\WordPress\UploadsContentHash\Plugin;

/**
 * @psalm-import UploadedFile from Plugin
 * @psalm-import PostData from Plugin
 */
class PluginTest extends WP_UnitTestCase {
	/**
	 * @covers Myrotvorets\WordPress\UploadsContentHash\Plugin::upload_prefilter
	 * @dataProvider data_upload_prefilter
	 * @psalm-param UploadedFile $input
	 * @psalm-param UploadedFile $expected
	 */
	public function test_upload_prefilter( array $input, array $expected ): void {
		$instance = Plugin::instance();

		$actual = $instance->upload_prefilter( $input );
		static::assertEquals( $expected, $actual );
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

	/**
	 * @covers Myrotvorets\WordPress\UploadsContentHash\Plugin::wp_insert_attachment_data
	 * @dataProvider data_wp_insert_attachment_data
	 * @psalm-param PostData $input
	 * @psalm-param Post $expected
	 */
	public function test_wp_insert_attachment_data( array $input, array $expected ): void {
		$instance = Plugin::instance();

		$actual = $instance->wp_insert_attachment_data( $input );
		static::assertEquals( $expected, $actual );
	}

	/**
	 * @psalm-return iterable<string, array{PostData, PostData}>
	 */
	public function data_wp_insert_attachment_data(): iterable {
		return [
			'name and title with hash'             => [
				[
					'post_name'  => 'test-abcdef',
					'post_title' => 'test.abcdef',
				],
				[
					'post_name'  => 'test',
					'post_title' => 'test',
				],
			],
			'name and title without hash'          => [
				[
					'post_name'  => 'test',
					'post_title' => 'test',
				],
				[
					'post_name'  => 'test',
					'post_title' => 'test',
				],
			],
			'name and title with mismatching hash' => [
				[
					'post_name'  => 'test-abcdef',
					'post_title' => 'test.012345',
				],
				[
					'post_name'  => 'test-abcdef',
					'post_title' => 'test.012345',
				],
			],
			'name and title with incorrect hash'   => [
				[
					'post_name'  => 'test-zabcdef',
					'post_title' => 'test.zabcdef',
				],
				[
					'post_name'  => 'test-zabcdef',
					'post_title' => 'test.zabcdef',
				],
			],
		];
	}
}

<?php

declare( strict_types = 1 );

use Amnesty\Get_Image_Data;

if ( ! function_exists( 'render_hero_block' ) ) {
	/**
	 * Render a hero block
	 *
	 * @param array  $attributes the block attributes
	 * @param string $content    the block inner content
	 *
	 * @package Amnesty\Blocks
	 *
	 * @return string
	 */
	function render_hero_block( array $attributes = [], string $content = '' ): string {
		$attrs = wp_parse_args(
			$attributes,
			[
				'align'            => '',
				'background'       => '',
				'className'        => 'wp-block-amnesty-core-hero',
				'content'          => '',
				'ctaLink'          => '',
				'ctaText'          => '',
				'featuredVideoId'  => 0,
				'hideImageCaption' => true,
				'hideImageCredit'  => false,
				'imageID'          => 0,
				'title'            => '',
				'type'             => 'image',
			]
		);

		// image attribute takes precedence over the featured image
		$image_id = $attrs['imageID'];
		if ( ! $image_id ) {
			$image_id = get_post_thumbnail_id();
		}

		$image = new Get_Image_Data( (int) $image_id );
		$video = new Get_Image_Data( (int) $attrs['featuredVideoId'] );

		$video_output = '';
		// If the block has a featured video, get the video URL
		if ( $attrs['featuredVideoId'] && 'video' === $attrs['type'] ) {
			// $video_output used in hero.php view
			$video_output .= sprintf(
				'<div class="hero-videoContainer">
					<video class="hero-video" autoplay loop muted>
						<source src="%s">
					</video>
				</div>',
				esc_url( wp_get_attachment_url( $attrs['featuredVideoId'] ) ),
			);
		}

		// Build output for the image/video caption and credit
		// $media_meta_output used in hero.php view
		// Reverse the boolean value of the arguments to match the value of the arguments in the function
		$media_meta_output  = $image->metadata( ! $attrs['hideImageCaption'], ! $attrs['hideImageCredit'], 'image' );
		$media_meta_output .= $video->metadata( ! $attrs['hideImageCaption'], ! $attrs['hideImageCredit'], 'video' );

		$inner_blocks = '';
		// If inner blocks are present, build the inner blocks
		if ( $content ) {
			// $inner_blocks used in hero.php view
			$inner_blocks .= sprintf(
				'<div class="donation">%s</div>',
				wp_kses_post( $content )
			);
		}

		spaceless();
		require realpath( __DIR__ . '/views/hero.php' );
		return endspaceless( false );
	}
}

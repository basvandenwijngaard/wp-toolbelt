<?php
/**
 * Social sharing links.
 *
 * @package toolbelt
 */

// Don't display in the WordPress admin.
if ( is_admin() ) {
	return;
}


/**
 * Add social sharing buttons to the post content.
 *
 * @param string $content The post content to append the sharing option to.
 *
 * @return string The post content with the sharing options appended.
 */
function toolbelt_social_sharing( $content ) {

	$post_types = apply_filters(
		'toolbelt_social_sharing_post_types',
		array( 'post' )
	);

	/**
	 * Not a singular post type of any sort so let's quit.
	 */
	if ( ! is_singular( $post_types ) ) {
		return $content;
	}

	// If a password is required then don't add this stuff.
	if ( post_password_required() ) {
		return $content;
	}

	/**
	 * Check a filter to see if we should quit.
	 *
	 * By default will return if it's not a blog post.
	 * You can change this with the filter.
	 */
	if ( ! apply_filters( 'toolbelt_display_social_sharing', TRUE ) ) {
		return $content;
	}

	toolbelt_styles( 'social-sharing' );

	$html = '';

	// Get the canonical link for the blog post.
	// Fallback to the permalink.
	$canonical = wp_get_canonical_url();

	if ( ! $canonical ) {
		$canonical = get_permalink();
	}

	/**
	 * Let's build it ourselves from the server information.
	 */
	if ( ! $canonical ) {

		$server = wp_unslash( $_SERVER );

		if ( is_array( $server ) ) {

			$https = 'http';
			if ( isset( $server[ 'HTTPS' ] ) ) {
				if ( 'on' === $server[ 'HTTPS' ] ) {
					$https = 'https';
				}
			}

			/**
			 * Ignore input sanitization since the generated url will be escaped
			 * immediately after.
			 */
			if ( isset( $server[ 'HTTP_HOST' ] ) && isset( $server[ 'REQUEST_URI' ] ) ) {
				$canonical = $https . '://' . $server[ 'HTTP_HOST' ] . $server[ 'REQUEST_URI' ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			}
		}
	}

	/**
	 * There's no url so let's quit.
	 *
	 * This can happen in some places such as buddypress where some pages are
	 * created virtually and don't have a linkable url.
	 */
	if ( empty( $canonical ) ) {
		return $content;
	}

	/**
	 * Escape the url. Particularly important for urls that have been generated
	 * from the $_SERVER properties.
	 */
	$canonical = esc_url( (string) $canonical );

	// Display a list of social networks.
	$networks = toolbelt_social_networks();

	foreach ( $networks as $slug => $network ) {

		$url  = sprintf( $network[ 'url' ], rawurlencode( $canonical ) );
		$html .= sprintf(
			'<a href="%1$s" title="%2$s" class="%3$s" target="_blank">%4$s %5$s</a>' . "\n",
			esc_url( $url ),
			esc_attr( $network[ 'title' ] ),
			'toolbelt_' . esc_attr( $slug ),
			file_get_contents( TOOLBELT_PATH . 'svg/' . $slug . '.svg' ),
			esc_html( $network[ 'label' ] )
		);

	}

	return $content . '<section class="toolbelt_social_share toolbelt-social-share">' . $html . '</section>';

}

add_filter( 'the_content', 'toolbelt_social_sharing', 99 );


/**
 * Get a list of social networks and their sharing links.
 *
 * @return array<mixed>
 */
function toolbelt_social_networks() {

	$_default_networks = 'facebook|twitter|linkedin|whatsapp|pinterest|pocket|wallabag|email';

	$desired_networks = explode( '|', apply_filters( 'wp_toolbelt_social_networks', $_default_networks ) );

	$networks = array(
		'facebook'  => array(
			'title' => esc_html__( 'Share on Facebook', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Share this', 'Facebook button label', 'wp-toolbelt' ),
			'url'   => 'https://facebook.com/sharer/sharer.php?u=%s',
		),
		'twitter'   => array(
			'title' => esc_html__( 'Tweet on Twitter', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Tweet this', 'Twitter button label', 'wp-toolbelt' ),
			'url'   => 'https://twitter.com/intent/tweet?url=%s',
		),
		'linkedin'  => array(
			'title' => esc_html__( 'Share on LinkedIn', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Share this', 'LinkedIn button label', 'wp-toolbelt' ),
			'url'   => 'https://www.linkedin.com/shareArticle?mini=true&url=%s',
		),
		/**
		 * Share Whatsapp
		 *
		 * According to the documentation the link should be:
		 * 'https://wa.me/?text=%s'
		 * This link works on desktop but not on mobile.
		 *
		 * The docs can be seen here:
		 * https://faq.whatsapp.com/en/android/26000030/
		 */
		'whatsapp'  => array(
			'title' => esc_html__( 'Share on WhatsApp', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Share this', 'WhatsApp button label', 'wp-toolbelt' ),
			'url'   => 'https://api.whatsapp.com/send?text=%s',
		),
		'pinterest' => array(
			'title' => esc_html__( 'Pin on Pinterest', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Pin this', 'Pinterest button label', 'wp-toolbelt' ),
			'url'   => 'https://pinterest.com/pin/create/button/?url=%s',
		),
		'pocket'    => array(
			'title' => esc_html__( 'Save to Pocket', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Save this', 'Pocket button label', 'wp-toolbelt' ),
			'url'   => 'https://getpocket.com/save?url=%s',
		),
		'wallabag'  => array(
			'title' => esc_html__( 'Save to Wallabag', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Save this', 'Wallabag button label', 'wp-toolbelt' ),
			'url'   => 'https://app.wallabag.it/bookmarklet?url=%s',
		),
		'reddit'    => array(
			'title' => esc_html__( 'Share on Reddit', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Share this', 'Reddit button label', 'wp-toolbelt' ),
			'url'   => 'https://reddit.com/submit?url=%s',
		),
		'email'     => array(
			'title' => esc_html__( 'Send via Email', 'wp-toolbelt' ),
			'label' => esc_html_x( 'Send this', 'Email button label', 'wp-toolbelt' ),
			'url'   => ' mailto:somebody@example.com?body=%s',
		),

	);

	$output = array();

	foreach ( $desired_networks as $item ) {
		if ( array_key_exists( $item, $networks ) ) {
			$output[ $item ] = $networks[ $item ];
		}
	}

	return $output;

}

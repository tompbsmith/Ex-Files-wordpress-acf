<?php
/**
 * ACF integration: local JSON, options page, editor tweaks.
 *
 * @package exfiles
 */

defined( 'ABSPATH' ) || exit;

// Field groups live in /acf-json so they're version-controlled with the theme.
add_filter(
	'acf/settings/save_json',
	function () {
		return EXF_DIR . '/acf-json';
	}
);
add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		$paths[] = EXF_DIR . '/acf-json';
		return array_unique( $paths );
	}
);

// Site Settings options page (footer, socials, contact recipient).
add_action(
	'acf/init',
	function () {
		if ( function_exists( 'acf_add_options_page' ) ) {
			acf_add_options_page(
				array(
					'page_title' => __( 'Site Settings', 'exfiles' ),
					'menu_title' => __( 'Site Settings', 'exfiles' ),
					'menu_slug'  => 'exf-site-settings',
					'capability' => 'edit_theme_options',
					'icon_url'   => 'dashicons-admin-generic',
					'position'   => 59,
					'redirect'   => false,
				)
			);
		}
	}
);

// Pages built from sections use the classic screen so the sections are front and centre.
add_filter(
	'use_block_editor_for_post',
	function ( $use, $post ) {
		return ( $post && exf_is_sections_page( $post->ID ) ) ? false : $use;
	},
	10,
	2
);

// Show each section's heading in the collapsed layout bar.
add_filter(
	'acf/fields/flexible_content/layout_title/name=sections',
	function ( $title ) {
		$heading = get_sub_field( 'heading' );
		if ( $heading ) {
			$title .= ': <span style="font-weight:400">' . esc_html( wp_trim_words( preg_replace( '/\s+/', ' ', $heading ), 8 ) ) . '</span>';
		}
		if ( get_sub_field( 'hide_section' ) ) {
			$title .= ' <em style="opacity:.7">(' . esc_html__( 'hidden', 'exfiles' ) . ')</em>';
		}
		return $title;
	}
);

add_action(
	'admin_notices',
	function () {
		if ( exf_acf_ready() || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p>' . wp_kses_post(
			__( '<strong>The Ex Files theme</strong> needs <strong>ACF Pro</strong> or the free <strong>Secure Custom Fields</strong> plugin (both include Flexible Content) to edit the homepage sections.', 'exfiles' )
		) . '</p></div>';
	}
);

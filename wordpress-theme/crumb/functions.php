<?php
/**
 * Crumb theme functions.
 *
 * @package Crumb
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access.
}

define( 'CRUMB_VERSION', '1.0.0' );

/**
 * Theme setup: features, menus, translations.
 */
function crumb_setup() {
	load_theme_textdomain( 'crumb', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'crumb' ),
			'footer'  => __( 'Footer menu', 'crumb' ),
		)
	);

	add_image_size( 'crumb-card', 720, 450, true );
}
add_action( 'after_setup_theme', 'crumb_setup' );

/**
 * Content width for embeds and images.
 */
function crumb_content_width() {
	$GLOBALS['content_width'] = 760;
}
add_action( 'after_setup_theme', 'crumb_content_width', 0 );

/**
 * Footer widget area.
 */
function crumb_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer', 'crumb' ),
			'id'            => 'footer-1',
			'description'   => __( 'Widgets shown in the footer (opening hours, address, etc.).', 'crumb' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'crumb_widgets_init' );

/**
 * Styles and scripts. The menu script is tiny and loaded in the footer.
 */
function crumb_assets() {
	wp_enqueue_style( 'crumb-style', get_stylesheet_uri(), array(), CRUMB_VERSION );

	wp_register_script( 'crumb-navigation', '', array(), CRUMB_VERSION, true );
	wp_enqueue_script( 'crumb-navigation' );
	wp_add_inline_script(
		'crumb-navigation',
		"(function(){var n=document.getElementById('site-navigation');if(!n)return;var b=n.querySelector('.menu-toggle');if(!b)return;b.addEventListener('click',function(){var o=n.classList.toggle('toggled');b.setAttribute('aria-expanded',o?'true':'false');});})();"
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'crumb_assets' );

/**
 * Shorter, cleaner excerpts on archive pages.
 *
 * @param int $length Default length.
 * @return int
 */
function crumb_excerpt_length( $length ) {
	return is_admin() ? $length : 28;
}
add_filter( 'excerpt_length', 'crumb_excerpt_length' );

/**
 * Prints the post date and author, escaped.
 */
function crumb_posted_on() {
	printf(
		/* translators: 1: post date, 2: author name. */
		esc_html__( 'Posted on %1$s by %2$s', 'crumb' ),
		'<time datetime="' . esc_attr( get_the_date( DATE_W3C ) ) . '">' . esc_html( get_the_date() ) . '</time>',
		esc_html( get_the_author() )
	);
}

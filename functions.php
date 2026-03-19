<?php
/**
 * Wicked Evolutions Theme Functions
 *
 * @package wickedevolutions
 * @version 0.2.0
 */

/**
 * Template routing — main site (blog) vs knowledge subsite (docs).
 *
 * Main site (blog_id 1): posts use single-post-blog, categories use category-blog.
 * Knowledge subsite: posts use single-post (3-column with sidebar + TOC).
 */
add_filter( 'single_template_hierarchy', function ( $templates ) {
    $post = get_queried_object();
    if ( ! $post ) {
        return $templates;
    }

    if ( get_current_blog_id() === 1 ) {
        array_unshift( $templates, 'single-post-blog' );
    }

    return $templates;
} );

add_filter( 'category_template_hierarchy', function ( $templates ) {
    if ( get_current_blog_id() === 1 ) {
        array_unshift( $templates, 'category-blog' );
    }
    return $templates;
} );

/**
 * Enqueue theme assets.
 */
add_action( 'wp_enqueue_scripts', function () {
    // Theme effects CSS — all sites
    wp_enqueue_style(
        'we-theme',
        get_theme_file_uri( 'assets/css/theme.css' ),
        [],
        '0.2.0'
    );

    // Blog CSS — main site only
    if ( get_current_blog_id() === 1 ) {
        wp_enqueue_style(
            'we-blog',
            get_theme_file_uri( 'assets/css/blog.css' ),
            [ 'we-theme' ],
            '0.2.0'
        );
    }

    // Theme toggle — all sites, in <head> so preference applies before paint
    wp_enqueue_script(
        'we-theme-toggle',
        get_theme_file_uri( 'assets/js/theme-toggle.js' ),
        [],
        '0.2.0',
        false
    );

    // TOC — knowledge subsite only (has TOC rail in single-post template)
    if ( get_current_blog_id() !== 1 ) {
        wp_enqueue_script(
            'we-toc',
            get_theme_file_uri( 'assets/js/toc.js' ),
            [],
            '0.2.0',
            true
        );
    }
} );

/**
 * Editor styles — load theme.css in the block editor too.
 */
add_action( 'after_setup_theme', function () {
    add_editor_style( 'assets/css/theme.css' );
} );

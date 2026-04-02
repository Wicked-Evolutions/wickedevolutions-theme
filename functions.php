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

add_filter( 'frontpage_template_hierarchy', function ( $templates ) {
    if ( get_current_blog_id() === 4 ) {
        array_unshift( $templates, 'front-page-knowledge' );
    }
    return $templates;
} );

/**
 * Header template part routing — swap slug based on subsite.
 *
 * Intercepts the template-part block before render and replaces the "header"
 * slug with the subsite-specific header part.
 */
add_filter( 'render_block_data', function ( $parsed_block ) {
    if ( 'core/template-part' !== $parsed_block['blockName'] ) {
        return $parsed_block;
    }

    if ( empty( $parsed_block['attrs']['slug'] ) || 'header' !== $parsed_block['attrs']['slug'] ) {
        return $parsed_block;
    }

    $map = array(
        1 => 'header-main',
        4 => 'header-knowledge',
        2 => 'header-community',
    );

    $blog_id = get_current_blog_id();

    if ( isset( $map[ $blog_id ] ) ) {
        $parsed_block['attrs']['slug'] = $map[ $blog_id ];
    }

    return $parsed_block;
} );

/**
 * Auto-assign style variation per subsite.
 *
 * Uses wp_theme_json_data_theme filter to merge variation data
 * on top of theme.json for specific subsites.
 */
add_filter( 'wp_theme_json_data_theme', function ( $theme_json ) {
    $variations = array(
        4 => 'knowledge',
    );

    $blog_id = get_current_blog_id();

    if ( ! isset( $variations[ $blog_id ] ) ) {
        return $theme_json;
    }

    $file = get_stylesheet_directory() . '/styles/' . $variations[ $blog_id ] . '.json';

    if ( ! file_exists( $file ) ) {
        return $theme_json;
    }

    $data = json_decode( file_get_contents( $file ), true );

    if ( ! is_array( $data ) ) {
        return $theme_json;
    }

    return $theme_json->update_with( $data );
}, 10 );

/**
 * Per-subsite default theme via data-default-theme attribute on <html>.
 * Knowledge (blog_id 4) defaults to light; all others default to dark.
 */
add_filter( 'language_attributes', function ( $output ) {
    $defaults = array(
        4 => 'light',
    );
    $blog_id       = get_current_blog_id();
    $default_theme = $defaults[ $blog_id ] ?? 'dark';
    return $output . ' data-default-theme="' . $default_theme . '"';
} );

/**
 * Add subsite body class for per-subsite CSS scoping.
 */
add_filter( 'body_class', function ( $classes ) {
    $classes[] = 'subsite-' . get_current_blog_id();
    return $classes;
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
        filemtime( get_theme_file_path( 'assets/css/theme.css' ) )
    );

    // Blog CSS — main site only
    if ( get_current_blog_id() === 1 ) {
        wp_enqueue_style(
            'we-blog',
            get_theme_file_uri( 'assets/css/blog.css' ),
            [ 'we-theme' ],
            filemtime( get_theme_file_path( 'assets/css/blog.css' ) )
        );
    }

    // Theme toggle — all sites, in <head> so preference applies before paint
    wp_enqueue_script(
        'we-theme-toggle',
        get_theme_file_uri( 'assets/js/theme-toggle.js' ),
        [],
        filemtime( get_theme_file_path( 'assets/js/theme-toggle.js' ) ),
        false
    );

    // TOC — knowledge subsite only (has TOC rail in single-post template)
    if ( get_current_blog_id() !== 1 ) {
        wp_enqueue_script(
            'we-toc',
            get_theme_file_uri( 'assets/js/toc.js' ),
            [],
            filemtime( get_theme_file_path( 'assets/js/toc.js' ) ),
            true
        );
    }
} );

/**
 * Theme setup — editor styles + navigation menu locations.
 */
add_action( 'after_setup_theme', function () {
    add_editor_style( 'assets/css/theme.css' );

    register_nav_menus( [
        'topbar'   => 'Header Topbar Navigation',
        'tabstrip' => 'Header Tabstrip Navigation',
        'footer'   => 'Footer Navigation',
    ] );
} );

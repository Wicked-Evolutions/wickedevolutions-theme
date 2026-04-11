<?php
/**
 * Wicked Evolutions Theme Functions
 *
 * @package wickedevolutions
 * @version 0.3.0
 *
 * The theme is install-aware via theme_mods. See THEME-CONFIG.md.
 * No knowledge of the WE multisite is hardcoded — every capability
 * is opt-in via a theme_mod with a zero-config default.
 */

/**
 * Coerce a theme_mod string value to a boolean.
 * Treats '1', 'true', 'on', 'yes' as true; everything else as false.
 * Necessary because theme_mods set via the abilities API are stored
 * as strings — the literal "false" string would otherwise be truthy.
 */
function we_theme_mod_bool( $name ) {
    return filter_var( get_theme_mod( $name, false ), FILTER_VALIDATE_BOOLEAN );
}

add_filter( 'single_template_hierarchy', function ( $templates ) {
    if ( we_theme_mod_bool( 'we_enable_blog_templates' ) ) {
        array_unshift( $templates, 'single-post-blog' );
    }
    return $templates;
} );

add_filter( 'category_template_hierarchy', function ( $templates ) {
    if ( we_theme_mod_bool( 'we_enable_blog_templates' ) ) {
        array_unshift( $templates, 'category-blog' );
    }
    return $templates;
} );

add_filter( 'frontpage_template_hierarchy', function ( $templates ) {
    if ( we_theme_mod_bool( 'we_enable_knowledge_frontpage' ) ) {
        array_unshift( $templates, 'front-page-knowledge' );
    }
    return $templates;
} );

add_filter( 'wp_theme_json_data_theme', function ( $theme_json ) {
    $variation = get_theme_mod( 'we_style_variation', '' );

    if ( $variation === '' ) {
        return $theme_json;
    }

    $file = get_stylesheet_directory() . '/styles/' . $variation . '.json';

    if ( ! file_exists( $file ) ) {
        return $theme_json;
    }

    $data = json_decode( file_get_contents( $file ), true );

    if ( ! is_array( $data ) ) {
        return $theme_json;
    }

    return $theme_json->update_with( $data );
}, 10 );

add_filter( 'language_attributes', function ( $output ) {
    $scheme = get_theme_mod( 'we_default_color_scheme', 'dark' );

    if ( $scheme === '' ) {
        return $output;
    }

    return $output . ' data-default-theme="' . esc_attr( $scheme ) . '"';
} );

add_filter( 'body_class', function ( $classes ) {
    $scope = get_theme_mod( 'we_body_class', '' );

    if ( $scope !== '' ) {
        $classes[] = 'we-site-' . sanitize_html_class( $scope );
    }

    return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'we-theme',
        get_theme_file_uri( 'assets/css/theme.css' ),
        array(),
        filemtime( get_theme_file_path( 'assets/css/theme.css' ) )
    );

    if ( we_theme_mod_bool( 'we_enable_blog_css' ) ) {
        wp_enqueue_style(
            'we-blog',
            get_theme_file_uri( 'assets/css/blog.css' ),
            array( 'we-theme' ),
            filemtime( get_theme_file_path( 'assets/css/blog.css' ) )
        );
    }

    wp_enqueue_script(
        'we-theme-toggle',
        get_theme_file_uri( 'assets/js/theme-toggle.js' ),
        array(),
        filemtime( get_theme_file_path( 'assets/js/theme-toggle.js' ) ),
        false
    );

    if ( we_theme_mod_bool( 'we_enable_toc' ) ) {
        wp_enqueue_script(
            'we-toc',
            get_theme_file_uri( 'assets/js/toc.js' ),
            array(),
            filemtime( get_theme_file_path( 'assets/js/toc.js' ) ),
            true
        );
    }
} );

add_action( 'after_setup_theme', function () {
    add_editor_style( 'assets/css/theme.css' );

    register_nav_menus( array(
        'topbar'            => 'Header Topbar Navigation',
        'tabstrip'          => 'Header Tabstrip Navigation',
        'footer'            => 'Footer Navigation',
        'sidebar-pages'     => 'Sidebar: Pages',
        'sidebar-started'   => 'Sidebar: Getting Started',
        'sidebar-products'  => 'Sidebar: Products',
        'sidebar-concepts'  => 'Sidebar: Core Concepts',
        'sidebar-agents'    => 'Sidebar: For AI Agents',
        'sidebar-policy'    => 'Sidebar: Policy',
        'sidebar-reference' => 'Sidebar: Reference Library',
    ) );
} );

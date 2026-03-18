<?php
/**
 * Wicked Evolutions Theme Functions
 *
 * @package wickedevolutions
 */

// Category-based single post template resolution
add_filter( 'single_template_hierarchy', function ( $templates ) {
    $post = get_queried_object();
    if ( ! $post ) {
        return $templates;
    }

    $category_templates = [
        'skills',
        'modules',
        'agents',
        'workflows',
        'protocols',
        'courses',
        'changelog',
    ];

    foreach ( $category_templates as $cat ) {
        if ( has_category( $cat, $post ) ) {
            array_unshift( $templates, "single-post-{$cat}" );
            break;
        }
    }

    return $templates;
} );

// Self-hosted fonts + custom assets
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'we-custom',
        get_theme_file_uri( 'assets/css/custom.css' ),
        [],
        '0.1.0'
    );
    wp_enqueue_script(
        'we-toc',
        get_theme_file_uri( 'assets/js/toc.js' ),
        [],
        '0.1.0',
        true
    );
} );

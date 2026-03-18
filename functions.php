<?php
/**
 * Wicked Evolutions Theme Functions
 *
 * @package wickedevolutions
 */

/**
 * Template routing — main site (blog) vs knowledge subsite (docs).
 *
 * Main site (blog_id 1): posts use single-post-blog, categories use category-blog.
 * Knowledge subsite (blog_id 4): posts use single-post (3-column with sidebar+TOC),
 * with category-specific variants for docs categories.
 */
add_filter( 'single_template_hierarchy', function ( $templates ) {
    $post = get_queried_object();
    if ( ! $post ) {
        return $templates;
    }

    // Main site — use the blog single post template
    if ( get_current_blog_id() === 1 ) {
        array_unshift( $templates, 'single-post-blog' );
        return $templates;
    }

    // Knowledge subsite — category-specific doc templates
    $category_templates = [
        'skills', 'modules', 'agents', 'workflows',
        'protocols', 'courses', 'changelog',
    ];

    foreach ( $category_templates as $cat ) {
        if ( has_category( $cat, $post ) ) {
            array_unshift( $templates, "single-post-{$cat}" );
            break;
        }
    }

    return $templates;
} );

// Main site categories use the blog category archive template
add_filter( 'category_template_hierarchy', function ( $templates ) {
    if ( get_current_blog_id() === 1 ) {
        array_unshift( $templates, 'category-blog' );
    }
    return $templates;
} );

// Self-hosted fonts + custom assets
add_action( 'wp_enqueue_scripts', function () {
    // Custom CSS — all sites
    wp_enqueue_style(
        'we-custom',
        get_theme_file_uri( 'assets/css/custom.css' ),
        [],
        '0.2.0'
    );

    // Blog CSS — main site only
    if ( get_current_blog_id() === 1 ) {
        wp_enqueue_style(
            'we-blog',
            get_theme_file_uri( 'assets/css/blog.css' ),
            [ 'we-custom' ],
            '0.1.0'
        );
    }

    // TOC script — knowledge subsite only (has TOC rail)
    if ( get_current_blog_id() !== 1 ) {
        wp_enqueue_script(
            'we-toc',
            get_theme_file_uri( 'assets/js/toc.js' ),
            [],
            '0.1.0',
            true
        );
    }
} );

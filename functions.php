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

/**
 * Dynamic sidebar menu routing — gated by we_enable_dynamic_sidebar theme_mod.
 */
add_action( 'init', function () {
    if ( ! we_theme_mod_bool( 'we_enable_dynamic_sidebar' ) ) {
        return;
    }

    register_post_meta( 'page', 'sidebar_menu', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'integer',
    ] );

    register_term_meta( 'category', 'sidebar_menu', [
        'show_in_rest' => true,
        'single'       => true,
        'type'         => 'integer',
    ] );
} );

function we_sidebar_menu_dropdown( $field_name, $selected_id ) {
    $menus = wp_get_nav_menus();
    ?>
    <select name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $field_name ); ?>">
        <option value=""><?php esc_html_e( '— No sidebar menu —' ); ?></option>
        <?php foreach ( $menus as $menu ) : ?>
            <option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $selected_id, $menu->term_id ); ?>>
                <?php echo esc_html( $menu->name ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php
}

add_action( 'category_edit_form_fields', function ( $term ) {
    if ( ! we_theme_mod_bool( 'we_enable_dynamic_sidebar' ) ) {
        return;
    }
    $value = (int) get_term_meta( $term->term_id, 'sidebar_menu', true );
    ?>
    <tr class="form-field">
        <th><label for="sidebar_menu">Sidebar Menu</label></th>
        <td>
            <?php we_sidebar_menu_dropdown( 'sidebar_menu', $value ); ?>
            <p class="description">Select a menu to display in the sidebar for posts in this category.</p>
        </td>
    </tr>
    <?php
} );

add_action( 'category_add_form_fields', function () {
    if ( ! we_theme_mod_bool( 'we_enable_dynamic_sidebar' ) ) {
        return;
    }
    ?>
    <div class="form-field">
        <label for="sidebar_menu">Sidebar Menu</label>
        <?php we_sidebar_menu_dropdown( 'sidebar_menu', 0 ); ?>
        <p class="description">Select a menu to display in the sidebar for posts in this category.</p>
    </div>
    <?php
} );

add_action( 'edited_category', function ( $term_id ) {
    if ( ! we_theme_mod_bool( 'we_enable_dynamic_sidebar' ) ) {
        return;
    }
    if ( isset( $_POST['sidebar_menu'] ) ) {
        update_term_meta( $term_id, 'sidebar_menu', absint( $_POST['sidebar_menu'] ) );
    }
} );

add_action( 'created_category', function ( $term_id ) {
    if ( ! we_theme_mod_bool( 'we_enable_dynamic_sidebar' ) ) {
        return;
    }
    if ( isset( $_POST['sidebar_menu'] ) ) {
        update_term_meta( $term_id, 'sidebar_menu', absint( $_POST['sidebar_menu'] ) );
    }
} );

add_action( 'add_meta_boxes', function () {
    if ( ! we_theme_mod_bool( 'we_enable_dynamic_sidebar' ) ) {
        return;
    }
    add_meta_box(
        'we_sidebar_menu',
        'Sidebar Menu',
        function ( $post ) {
            wp_nonce_field( 'we_sidebar_menu', 'we_sidebar_menu_nonce' );
            $value = (int) get_post_meta( $post->ID, 'sidebar_menu', true );
            we_sidebar_menu_dropdown( 'sidebar_menu', $value );
            echo '<p class="description">Select a menu to display in the sidebar for this page.</p>';
        },
        'page',
        'side'
    );
} );

add_action( 'save_post_page', function ( $post_id ) {
    if ( ! we_theme_mod_bool( 'we_enable_dynamic_sidebar' ) ) {
        return;
    }
    if ( ! isset( $_POST['we_sidebar_menu_nonce'] ) || ! wp_verify_nonce( $_POST['we_sidebar_menu_nonce'], 'we_sidebar_menu' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( isset( $_POST['sidebar_menu'] ) ) {
        update_post_meta( $post_id, 'sidebar_menu', absint( $_POST['sidebar_menu'] ) );
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

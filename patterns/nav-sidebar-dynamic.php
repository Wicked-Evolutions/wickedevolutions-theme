<?php
/**
 * Title: Sidebar Dynamic Navigation
 * Slug: wickedevolutions/nav-sidebar-dynamic
 * Categories: hidden
 * Inserter: false
 */

if ( ! we_theme_mod_bool( 'we_enable_dynamic_sidebar' ) ) {
    return;
}

$sidebar_menu_id = 0;

if ( is_singular( 'post' ) ) {
    $categories = get_the_category();
    if ( $categories ) {
        $sidebar_menu_id = (int) get_term_meta( $categories[0]->term_id, 'sidebar_menu', true );
    }
} elseif ( is_page() ) {
    $sidebar_menu_id = (int) get_post_meta( get_the_ID(), 'sidebar_menu', true );
}

if ( $sidebar_menu_id && wp_get_nav_menu_object( $sidebar_menu_id ) ) :
?>
<!-- wp:group {"style":{"spacing":{"padding":{"left":"0","right":"0"},"margin":{"bottom":"0"}}},"className":"we-nav-section we-nav-dynamic"} -->
<div class="wp-block-group we-nav-section we-nav-dynamic" style="margin-bottom:0;padding-right:0;padding-left:0">
<?php wp_nav_menu( [
    'menu'            => $sidebar_menu_id,
    'container'       => 'nav',
    'container_class' => 'we-nav-sidebar',
    'menu_class'      => 'we-nav-sidebar-list',
    'depth'           => 2,
    'fallback_cb'     => false,
] ); ?>
</div>
<!-- /wp:group -->
<?php endif; ?>

<?php
/**
 * Title: Sidebar Pages Navigation
 * Slug: wickedevolutions/nav-sidebar-pages
 * Categories: hidden
 * Inserter: false
 */
?>
<?php if ( has_nav_menu( 'sidebar-pages' ) ) : ?>
<!-- wp:group {"style":{"spacing":{"padding":{"left":"0","right":"0"},"margin":{"bottom":"0"}}},"className":"we-nav-section"} -->
<div class="wp-block-group we-nav-section" style="margin-bottom:0;padding-right:0;padding-left:0">
<!-- wp:heading {"level":6,"style":{"typography":{"fontSize":"0.75rem","fontWeight":"700","letterSpacing":"0.08em","textTransform":"uppercase"}},"textColor":"muted","fontFamily":"jetbrains-mono","className":"we-nav-label"} -->
<h6 class="wp-block-heading we-nav-label has-muted-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.75rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase">Pages</h6>
<!-- /wp:heading -->
<?php
wp_nav_menu( [
	'theme_location'  => 'sidebar-pages',
	'container'       => 'nav',
	'container_class' => 'we-nav-sidebar',
	'menu_class'      => 'we-nav-sidebar-list',
	'depth'           => 1,
	'fallback_cb'     => false,
] );
?>
</div>
<!-- /wp:group -->
<?php endif; ?>

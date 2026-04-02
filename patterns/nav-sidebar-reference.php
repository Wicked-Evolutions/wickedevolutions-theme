<?php
/**
 * Title: Sidebar Reference Library Navigation
 * Slug: wickedevolutions/nav-sidebar-reference
 * Categories: hidden
 * Inserter: false
 */
?>
<?php if ( has_nav_menu( 'sidebar-reference' ) ) : ?>
<!-- wp:group {"style":{"spacing":{"padding":{"left":"0","right":"0"},"margin":{"bottom":"0","top":"12px"}},"border":{"top":{"width":"1px","color":"var:custom|border|subtle"}}},"className":"we-nav-section we-nav-reference"} -->
<div class="wp-block-group we-nav-section we-nav-reference" style="border-top-color:var(--wp--custom--border--subtle);border-top-width:1px;margin-top:12px;margin-bottom:0;padding-right:0;padding-left:0">
<!-- wp:heading {"level":6,"style":{"typography":{"fontSize":"0.75rem","fontWeight":"700","letterSpacing":"0.08em","textTransform":"uppercase"},"spacing":{"margin":{"top":"16px"}}},"textColor":"muted","fontFamily":"jetbrains-mono","className":"we-nav-label"} -->
<h6 class="wp-block-heading we-nav-label has-muted-color has-text-color has-jetbrains-mono-font-family" style="font-size:0.75rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;margin-top:16px">Reference Library</h6>
<!-- /wp:heading -->
<?php
wp_nav_menu( [
	'theme_location'  => 'sidebar-reference',
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

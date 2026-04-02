<?php
/**
 * Title: Topbar Navigation
 * Slug: wickedevolutions/nav-topbar
 * Categories: hidden
 * Inserter: false
 */
?>
<?php
wp_nav_menu( [
	'theme_location'  => 'topbar',
	'container'       => 'nav',
	'container_class' => 'we-nav-topbar',
	'menu_class'      => 'we-nav-topbar-list',
	'depth'           => 1,
	'fallback_cb'     => false,
] );
?>

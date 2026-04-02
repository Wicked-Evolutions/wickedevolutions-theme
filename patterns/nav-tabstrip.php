<?php
/**
 * Title: Tabstrip Navigation
 * Slug: wickedevolutions/nav-tabstrip
 * Categories: hidden
 * Inserter: false
 */
?>
<?php
wp_nav_menu( [
	'theme_location'  => 'tabstrip',
	'container'       => 'nav',
	'container_class' => 'we-nav-tabstrip',
	'menu_class'      => 'we-nav-tabstrip-list',
	'depth'           => 1,
	'fallback_cb'     => false,
] );
?>

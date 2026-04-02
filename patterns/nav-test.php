<?php
/**
 * Title: Nav Test
 * Slug: wickedevolutions/nav-test
 * Categories: hidden
 * Inserter: false
 */
?>
<nav class="we-nav-test">
<?php
wp_nav_menu( [
	'theme_location' => 'primary',
	'container'      => false,
	'fallback_cb'    => function () {
		echo '<p>No menu assigned to this location.</p>';
	},
] );
?>
</nav>

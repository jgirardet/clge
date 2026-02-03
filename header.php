<?php

/**
 * This template is used for the website header
 *
 * @package Clge
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<title>Collège Lyonnais des Généralistes Enseigants</title>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<a class="screen-reader-text skip-link" href="#content"><?php esc_html_e('Skip to content', 'clge'); ?></a>

	<div class="header-search-block bg-graphite hidden" id="search-container">
		<?php get_search_form(); ?>
	</div> <!-- /header-search-block -->

	<header class="header section small-padding bg-dark bg-image" style="background-image: url(<?php echo esc_url(get_header_image()); ?>);" role="banner">
		<a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
			<div class="cover"></div>
		</a>
		<div class="header-inner section-inner">
		</div> <!-- /header-inner -->
	</header> <!-- /header -->

	<div class="header-mobile">
		<a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
			<img src="<?php echo get_template_directory_uri() . '/images/logo-petit-ecran-sans-nom.png'; ?>" alt="" class="logo-petit-ecran">
		</a>
		<h1 class="title-small-screen">Collège Lyonnais des Généralistes Enseignants</h1>

		<div id="actions">
			<a class="fa fa-search search-icon" href="#"></a>
			<a class="fa fa-bars" aria-hidden="true" id="menu-icon"></a>
		</div>
	</div>

	<div class="navigation section no-padding bg-dark">
		<nav id="site-navigation" class="navigation-inner section-inner clear" role="navigation">
			<div class="main-navigation">
				<?php
				wp_nav_menu(array(
					'container' => '',
					'theme_location' => 'menu-1',
					'menu_id' => 'primary-menu',
				));
				?>
			</div>
			<div id="search-icon-large-screen"><a class="fa fa-search search-icon" style="padding-top: 20px; padding-left: 20px;" href="#"></a></div>




		</nav> <!-- /navigation-inner -->
	</div> <!-- /navigation -->
	<h4 class="site-description">
		Le Collège lyonnais des généralistes enseignants (CLGE) est l’association regroupant les MSU et les tuteurs rattachés à l’université de Lyon et organise la formation des maîtres de stage et aide à la promotion et au rayonnement de la médecine générale.
	</h4>
	<div class="wrapper section medium-padding clear">
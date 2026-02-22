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
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">
	<title>Collège Lyonnais des Généralistes Enseigants</title>
	<script src="https://unpkg.com/htmx.org@2.0.2" integrity="sha384-Y7hw+L/jvKeWIRRkqWYfPcvVxHzVzn5REgzbawhxAuQGwX1XWe70vji+VSeHOThJ" crossorigin="anonymous"></script>
	<script src="https://unpkg.com/hyperscript.org@0.9.14"></script>
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

	<div id="entete-footer">

		<div class="entete-footer-container">
			<h4>Adhérer au CLGE</h4>
		</div>


		<div class="entete-footer-container" id="phrase-entete-container">
			<h4 class="phrase-entete">
				Le CLGE (Collège Lyonnais des Généralistes Enseignants) est l’association regroupant les MSU et les tuteurs rattachés à l’université de Lyon et organise la formation des maîtres de stage et aide à la promotion et au rayonnement de la médecine générale.
			</h4>
		</div>

		<div class="entete-footer-container"
			id="newsletter-container">
			<form id="newsletter-form"
				hx-post="/wp-admin/admin-ajax.php"
				hx-target="#newsletter-container"
				hx-swap="innerHTML"
				_="on submit
					set #newsletter-submit-button.disabled to true
					set #newsletter-submit-button.style.opacity to '0.6'
				">
				<input type="hidden" name="action" value="send_newsletter">
				<label for="newsletter-email">
					Abonnez-vous à la newsletter
				</label>
				<div id="newsletter-inputs-container">
					<input type="email" id="newsletter-email" name="newsletter_email"
						placeholder="Votre adresse email" required
						_="on input
       set #newsletter-submit-button.disabled to !me.validity.valid">
					<button
						id="newsletter-submit-button"
						type="submit">
						<i class="fa fa-paper-plane"></i>
					</button>
				</div>
			</form>
			<div id="newsletter-response" style="margin-top: 10px;"></div>
		</div>




	</div>

	<div class="wrapper section medium-padding clear">
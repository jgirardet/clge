<?php

add_filter( 'typekit_add_font_category_rules', function( $category_rules ) {

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'body',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '18px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.site-title',
		array(
			array( 'property' => 'font-family', 'value' => '"Pacifico", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '2.25em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.site-description',
		array(
			array( 'property' => 'font-size', 'value' => '1.1em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.search-icon:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '27px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.header-search-block .search-field',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '2.5em' ),
			array( 'property' => 'font-weight', 'value' => '300' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.main-navigation',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.9em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.main-navigation li:before',
		array(
			array( 'property' => 'font-size', 'value' => '16px' ),
			array( 'property' => 'font-weight', 'value' => '300' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.main-navigation li > a',
		array(
			array( 'property' => 'font-size', 'value' => '.85em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.main-navigation ul ul a',
		array(
			array( 'property' => 'font-size', 'value' => '1.0em' ),
			array( 'property' => 'font-weight', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.media-caption',
		array(
			array( 'property' => 'font-size', 'value' => '.8em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-header .post-title',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.25em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-header .post-title em',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content h1,
		.post-content h2,
		.post-content h3,
		.post-content h4,
		.post-content h5,
		.post-content h6',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content h1 em,
		.post-content h2 em,
		.post-content h3 em',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content h1',
		array(
			array( 'property' => 'font-size', 'value' => '2em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content h2',
		array(
			array( 'property' => 'font-size', 'value' => '1.75em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content h3',
		array(
			array( 'property' => 'font-size', 'value' => '1.5em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content h4',
		array(
			array( 'property' => 'font-size', 'value' => '1.25em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content h5',
		array(
			array( 'property' => 'font-size', 'value' => '1em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content h6',
		array(
			array( 'property' => 'font-size', 'value' => '.9em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.post-content blockquote',
		array(
			array( 'property' => 'font-size', 'value' => '1em' ),
			array( 'property' => 'font-weight', 'value' => '300' ),
			array( 'property' => 'font-weight', 'value' => 'normal' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content blockquote p',
		array(
			array( 'property' => 'font-size', 'value' => '1.2em' ),
			array( 'property' => 'font-weight', 'value' => '300' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content cite',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.9rem' ),
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content blockquote cite em',
		array(
			array( 'property' => 'font-style', 'value' => 'italic' ),
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'em,
		q,
		.tag-archive-meta p',
		array(
			array( 'property' => 'font-style', 'value' => 'italic' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content em strong,
		.post-content strong em',
		array(
			array( 'property' => 'font-style', 'value' => 'italic' ),
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content big',
		array(
			array( 'property' => 'font-size', 'value' => '1.25em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'code,
		kbd,
		pre',
		array(
			array( 'property' => 'font-family', 'value' => '"Menlo", Monaco, monospace' ),
			array( 'property' => 'font-size', 'value' => '.85em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content dl dt',
		array(
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content pre',
		array(
			array( 'property' => 'font-size', 'value' => '.8em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content .gallery-caption,
		.post-content .wp-caption-text',
		array(
			array( 'property' => 'font-size', 'value' => '.8em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content .gallery-caption',
		array(
			array( 'property' => 'font-size', 'value' => '16px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.gallery-caption',
		array(
			array( 'property' => 'font-family', 'value' => '"Noto Sans", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '12px' ),
			array( 'property' => 'font-size', 'value' => '1.2rem' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content th',
		array(
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content fieldset legend,
		.widget-content fieldset legend',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.9rem' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content label',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1rem' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content input,
		.post-content textarea,
		.widget-content input,
		.widget-content textarea',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content input[type="email"],
		.post-content input[type="password"],
		.post-content input[type="tel"],
		.post-content input[type="text"],
		.post-content input[type="url"],
		.post-content textarea,
		.widget-content input[type="email"],
		.widget-content input[type="password"],
		.widget-content input[type="tel"],
		.widget-content input[type="text"],
		.widget-content input[type="url"],
		.widget-content textarea',
		array(
			array( 'property' => 'font-size', 'value' => '.85em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-content input[type="button"],
		.post-content input[type="reset"],
		.post-content input[type="submit"],
		.widget-content input[type="button"],
		.widget-content input[type="reset"],
		.widget-content input[type="submit"]',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.9rem' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.format-gallery .flex-direction-nav a:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '35px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.page .mejs-container.mejs-audio .mejs-controls .mejs-mute button:before,
		.page .mejs-container.mejs-audio .mejs-controls .mejs-playpause-button button:before,
		.page .mejs-container.mejs-audio .mejs-controls .mejs-unmute button:before,
		.page .mejs-container.mejs-audio .mejs-controls .mejs-volume-button button:before,
		.post .mejs-container.mejs-audio .mejs-controls .mejs-mute button:before,
		.post .mejs-container.mejs-audio .mejs-controls .mejs-playpause-button button:before,
		.post .mejs-container.mejs-audio .mejs-controls .mejs-unmute button:before,
		.post .mejs-container.mejs-audio .mejs-controls .mejs-volume-button button:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '20px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.posts .post-content h1',
		array(
			array( 'property' => 'font-size', 'value' => '1.6em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.posts .post-content h2',
		array(
			array( 'property' => 'font-size', 'value' => '1.5em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.posts .post-content h3',
		array(
			array( 'property' => 'font-size', 'value' => '1.3em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.posts .post-content h4',
		array(
			array( 'property' => 'font-size', 'value' => '1.2em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.posts .post-content h5',
		array(
			array( 'property' => 'font-size', 'value' => '1.1em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.posts .post-content h6',
		array(
			array( 'property' => 'font-size', 'value' => '0.8em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .post-content address,
		.posts .post-content dd,
		.posts .post-content dt,
		.posts .post-content li,
		.posts .post-content p,
		.posts .post-content td,
		.posts .post-content th',
		array(
			array( 'property' => 'font-size', 'value' => '.95em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .post-content dt',
		array(
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .post-meta a',
		array(
			array( 'property' => 'font-size', 'value' => '.8em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.posts .post-meta a.post-date i.fa',
		array(
			array( 'property' => 'font-size', 'value' => '1.4em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.posts .post-meta a.post-comments:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '1.3em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.posts .post-meta a.post-edit-link i.fa',
		array(
			array( 'property' => 'font-size', 'value' => '1.4em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .format-image .post-content .image-caption',
		array(
			array( 'property' => 'font-weight', 'value' => '600' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .format-link .post-content p',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.2em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .format-link .post-content a.more-link',
		array(
			array( 'property' => 'font-size', 'value' => '0.8em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text', // oddball, since h2 displays under body text
		'.posts .format-link .link-header h2',
		array(
			array( 'property' => 'font-size', 'value' => 'inherit' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.posts .format-link .link-header h2 a',
		array(
			array( 'property' => 'font-size', 'value' => '.8em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.posts .format-link .link-header h2 a i.fa',
		array(
			array( 'property' => 'font-size', 'value' => '1.2em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .format-quote .post-content blockquote',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.4em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.posts .format-quote .post-content blockquote > *',
		array(
			array( 'property' => 'font-size', 'value' => 'inherit' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .format-quote .post-content cite',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '0.6em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .format-status .post-content p',
		array(
			array( 'property' => 'font-family', 'value' => '"Pacifico", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.2em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts .format-status .post-content a.more-link',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '0.8em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.single .post-title',
		array(
			array( 'property' => 'font-size', 'value' => '2.25em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.single .format-aside .post-content',
		array(
			array( 'property' => 'font-size', 'value' => '1.2em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.single .format-quote .post-content blockquote',
		array(
			array( 'property' => 'font-size', 'value' => '1.75em' ),
			array( 'property' => 'font-weight', 'value' => '300' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.single .format-quote .post-content blockquote > *',
		array(
			array( 'property' => 'font-size', 'value' => 'inherit' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.single .format-quote .post-content cite',
		array(
			array( 'property' => 'font-size', 'value' => '0.6em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.single .format-status .post-content',
		array(
			array( 'property' => 'font-family', 'value' => '"Pacifico", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.4em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.post-navigation a,
		.single .post-meta > a,
		.single .post-meta p,
		.single .post-meta time',
		array(
			array( 'property' => 'font-size', 'value' => '.85em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.single .post-meta p.post-categories i.fa,
		.single .post-meta p.post-tags i.fa,
		.single .post-meta time i.fa',
		array(
			array( 'property' => 'font-size', 'value' => '1.3em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.post-navigation .nav-next a:before,
		.post-navigation .nav-previous a:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '1.3em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.single .post-edit-link i.fa',
		array(
			array( 'property' => 'font-size', 'value' => '1.3em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.entry-author .author-title',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.1em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.entry-author .author-bio',
		array(
			array( 'property' => 'font-size', 'value' => '.875em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.nocomments',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comments-title-container,
		#content #menu_widget caption,
		#infinite-footer .container',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.comments-title',
		array(
			array( 'property' => 'font-size', 'value' => '1.75em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.add-comment-title',
		array(
			array( 'property' => 'font-size', 'value' => '1rem' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.comment.bypostauthor:after,
		.comment-reply-link:before,
		.comment-edit-link:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '16px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-author .fn',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '18px' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-metadata,
		.comment-metadata a',
		array(
			array( 'property' => 'font-size', 'value' => '16px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.comment-content h1,
		.comment-content h2,
		.comment-content h3,
		.comment-content h4,
		.comment-content h5,
		.comment-content h6',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.2em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-content dl dt',
		array(
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-content big',
		array(
			array( 'property' => 'font-size', 'value' => '1.25em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-content th',
		array(
			array( 'property' => 'font-weight', 'value' => 'bold' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-nav-below',
		array(
			array( 'property' => 'font-size', 'value' => '.9em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.pingbacks-title',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.25em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.pingbacklist .pingback a',
		array(
			array( 'property' => 'font-size', 'value' => '15px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.comment-reply-title',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.75em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-reply-title #cancel-comment-reply-link',
		array(
			array( 'property' => 'font-size', 'value' => '.9rem' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.comment-form input[type="email"],
		.comment-form input[type="text"],
		.comment-form input[type="url"],
		.comment-form textarea,
		div#respond textarea',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.comment-form-author:before,
		.comment-form-email:before,
		.comment-form-url:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '28px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.form-submit #submit',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.9em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.author-info h4',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.5em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.author-info h5',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.page-title h5',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.9em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.page-title h3',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.75em' ),
			array( 'property' => 'font-weight', 'value' => '400' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.posts-navigation a,
		#infinite-handle span,
		#wp-calendar,
		#content #menu_widget td,
		#content #menu_widget th,
		.post-content table,
		.posts .format-image .post-content,
		.comment-content,
		.comment-content table,
		.comment-notes,
		.form-allowed-tags,
		.logged-in-as',
		array(
			array( 'property' => 'font-size', 'value' => '.9em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.sidebar .widget-content',
		array(
			array( 'property' => 'font-size', 'value' => '.85em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.widget-title',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.9em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget-title label',
		array(
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.widget-content ul li:before',
		array(
			array( 'property' => 'font-size', 'value' => '18px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.tagcloud a,
		.widget_tag_cloud a,
		.wp_widget_tag_cloud a',
		array(
			array( 'property' => 'font-size', 'value' => '.8em !important' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.footer .search-field,
		.wrapper .search-field',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.9em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.footer .search-form label:before,
		.wrapper .search-form label:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '27px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.sidebar .search-form label:before',
		array(
			array( 'property' => 'font-size', 'value' => '23px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#wp-calendar caption',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1rem' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#wp-calendar thead th,
		#wp-calendar tfoot a',
		array(
			array( 'property' => 'font-size', 'value' => '.9em' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#content #menu_widget .updated-at',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '0.9em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'placeholder',
		'.error404 .widget_recent_entries li:before',
		array(
			array( 'property' => 'font-size', 'value' => '18px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'headings',
		'.footer .widget-title',
		array(
			array( 'property' => 'font-size', 'value' => '.79em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.footer .widget-content',
		array(
			array( 'property' => 'font-size', 'value' => '.95em' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'#infinite-footer .blog-info a',
		array(
			array( 'property' => 'font-size', 'value' => '13px' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.credits p',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto Slab", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '.8rem' ),
			array( 'property' => 'font-weight', 'value' => '700' ),
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'body-text',
		'.main-navigation li > a,
		.main-navigation li > a',
		array(
			array( 'property' => 'font-family', 'value' => '"Roboto", sans-serif' ),
			array( 'property' => 'font-size', 'value' => '1.0em' ),
			array( 'property' => 'font-weight', 'value' => 'normal' ),
		),
		array(
			'(max-width: 1440px)',
		)
	);

	TypekitTheme::add_font_category_rule( $category_rules, 'none',
		'.nav-toggle:before',
		array(
			array( 'property' => 'font-family', 'value' => '"FontAwesome"' ),
			array( 'property' => 'font-size', 'value' => '25px' ),
		),
		array(
			'(max-width: 1440px)',
		)
	);

	return $category_rules;
} );

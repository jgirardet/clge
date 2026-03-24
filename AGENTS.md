# AGENTS.md - Clge WordPress Theme

## Project Overview

Clge is a WordPress theme - a dynamic, grid-based theme for curators. It displays posts, videos, images, galleries, quotes, and links. The theme is based on Baskerville 2 by Anders Norén and uses WordPress core from composer.

## Build/Lint/Test Commands

### Dependencies

```bash
# Install WordPress core dependency
composer install
```

### Running the Theme

This is a WordPress theme - there's no separate build process. To test:

1. **Local WordPress Development**: Copy this theme folder to a local WordPress installation's `wp-content/themes/clge/` directory
2. **Activate**: Go to Appearance > Themes in WordPress admin and activate "Clge"
3. **Development**: Edit PHP/CSS/JS files directly; changes appear on refresh

### Linting (Not Configured)

No automated linting is currently set up. If you want to add it:

- **PHP**: Consider adding `phpcs` with WordPress rules: `composer require --dev squizlabs/php_codesniffer:^3.7`
- **PHP Fixer**: Add `pint` or PHP-CS-Fixer: `composer require --dev laravel/pint`
- **CSS/JS**: Not linted - consider adding `npm install` with `stylelint` and `eslint` if needed

### Testing (Not Configured)

No automated tests exist. Test manually by:

1. Installing theme in local WordPress
2. Checking: homepage, single posts, pages, archives, widgets, responsive layouts
3. Testing in multiple browsers (Chrome, Firefox, Safari)

## Code Style Guidelines

### General Philosophy

This is a WordPress theme. Follow WordPress PHP Coding Standards: https://developer.wordpress.org/coding-standards/wordpress-coding-standards/

### Formatting

- **Indentation**: Use tabs, not spaces
- **Line endings**: Unix (LF)
- **No trailing whitespace**
- **Braces**: Use BSD/Allman style (opening brace on own line for functions, same line for control structures)
- **Space after keywords**: `if ( $condition )` not `if($condition)`
- **Space around operators**: `$a = $b` not `$a=$b`

### Naming Conventions

- **Functions**: `snake_case` with prefix (e.g., `clge_setup()`, `clge_create_event()`)
- **Classes**: `PascalCase` (e.g., `Clge_Admin_Page`)
- **Variables**: `snake_case` (e.g., `$table_name`, `$insert_data`)
- **Constants**: `UPPER_SNAKE_CASE` (e.g., `CLGE_VERSION`)
- **Hooks (filters/actions)**: `lowercase_with_underscores` (e.g., `after_setup_theme`)

### Functions

```php
// Use function_exists guard for all theme functions
if ( ! function_exists( 'clge_function_name' ) ) :
function clge_function_name( $param ) {
    // code
}
endif;
```

### SQL Queries

- Always use `$wpdb->prepare()` for variable data to prevent SQL injection
- Use proper format specifiers: `%s` for string, `%d` for integer
- Prefix tables with `$wpdb->prefix`

```php
$table_name = $wpdb->prefix . 'clge_cal_events';
$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );
```

### Security

- Use `sanitize_email()`, `sanitize_text_field()`, `esc_html()`, `esc_url()` for output
- Use `wp_nonce_field()` and `wp_verify_nonce()` for form submissions
- Use `defined('ABSPATH')` guard on all include files
- Never commit secrets or API keys

### Template Tags

- Use WordPress template tags: `get_template_part()`, `get_the_ID()`, etc.
- Wrap translatable strings with `__( 'text', 'clge' )` or `_e( 'text', 'clge' )`
- Use escape functions: `esc_html_e()`, `esc_attr_e()`, `esc_url()`

### CSS/JS Enqueueing

Enqueue scripts and styles properly:

```php
function clge_scripts() {
    wp_enqueue_style( 'clge-style', get_stylesheet_uri() );
    wp_enqueue_script( 'clge-script', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'clge_scripts' );
```

### File Organization

```
clge/
├── functions.php          # Theme setup, hooks, main logic
├── inc/
│   ├── custom-header.php  # Custom header functionality
│   ├── template-tags.php  # Template functions
│   ├── extras.php         # Additional functions
│   ├── jetpack.php        # Jetpack integration
│   ├── database.php        # Custom database operations
│   └── clge-admin-page.php # Admin interface
├── templates/             # Custom page templates
├── js/                    # JavaScript files
├── css/                   # (optional additional CSS)
└── style.css             # Main stylesheet with theme header
```

### Git Practices

- Commit messages should be clear and descriptive
- Keep commits atomic (one feature/fix per commit)
- Do not commit: vendor/, node_modules/, .DS_Store, wp-config.php
- The .gitignore handles most exclusions

## Working with This Theme

### Adding New Features

1. Create new functions in appropriate `inc/` files or `functions.php`
2. Add template files in root or `templates/` directory
3. Add CSS to `style.css` or create new stylesheet
4. Test in browser after changes

### Database Operations

When adding custom tables (as in `inc/database.php`):

- Create table on theme activation: `add_action( 'after_switch_theme', 'callback' )`
- Use `dbDelta()` for table creation
- Always prepare queries with `$wpdb->prepare()`

### AJAX Handling

The theme registers AJAX endpoints:

```php
add_action('wp_ajax_send_newsletter', 'handle_newsletter_submission');
add_action('wp_ajax_nopriv_send_newsletter', 'handle_newsletter_submission');
```

### Translations

The theme is translation-ready. Add strings using:

```php
esc_html__( 'Text', 'clge' )
esc_attr_e( 'Text', 'clge' )
```

Update `.pot` file for new translations: `wp i18n make-pot . languages/clge.pot`
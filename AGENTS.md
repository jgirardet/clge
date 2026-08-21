# AGENTS.md - Clge WordPress Theme

## Project Overview

Clge is a WordPress theme - a dynamic, grid-based theme for curators. It displays posts, videos, images, galleries, quotes, and links. The theme is based on Baskerville 2 by Anders Norén and uses WordPress core from composer.

### Key Features
- **Event Management System**: Custom calendar with `clge_cal_events` table for managing events with dates, locations, and descriptions
- **Newsletter Subscription**: AJAX-powered newsletter signup with email validation
- **CNGE Formations Integration**: HTML parsing and automatic event creation from CNGE formation tables
- **Nextcloud Integration**: CalDAV calendar integration with event synchronization, encryption, and debug tools
- **Custom Admin Interface**: Dedicated admin page for event management
- **Multi-language Support**: 25+ translation files (fr_FR, en, es_ES, de_DE, etc.)
- **Responsive Grid Layout**: Masonry-based post display
- **Post Formats Support**: aside, audio, chat, gallery, image, link, quote, status, video

## Build/Lint/Test Commands

### Dependencies

```bash
# Install WordPress core dependency
composer install

# Install Node.js dependencies (for development)
npm install
```

### JavaScript Dependencies
The theme uses the following front-end libraries (bundled in repo):
- **Masonry**: Grid layout library (included with WordPress core)
- **FlexSlider**: jQuery slider for galleries
- **imagesloaded**: Detect when images have loaded
- **Font Awesome 4.3.0**: Icon library
- **jQuery**: Included with WordPress core

### Running the Theme

1. **Local WordPress Development**: Copy this theme folder to a local WordPress installation's `wp-content/themes/clge/` directory
2. **Activate**: Go to Appearance > Themes in WordPress admin and activate "Clge"
3. **Development**: Edit PHP/CSS/JS files directly; changes appear on refresh

### Database Setup
On theme activation, the custom table `wp_clge_cal_events` is automatically created with:
- Event dates (debut, fin)
- Event details (nom, abrev, alias, description)
- Location (lieu_physique, url)
- Type flag (evt_clge)

Migration functions automatically add new columns (alias, description) if missing.

### Linting (Recommended Setup)

```bash
# PHP Code Sniffer with WordPress standards
composer require --dev squizlabs/php_codesniffer
./vendor/bin/phpcs --standard=WordPress-Core .

# PHP Code Beauty Fixer (PSR12 compatible with WordPress)
composer require --dev php-cs-fixer/php-cs-fixer
./vendor/bin/php-cs-fixer fix --rules=@PSR12

# PHPStan for static analysis
composer require --dev phpstan/phpstan
./vendor/bin/phpstan analyse --level=5
```

### Testing (Manual)
No automated tests exist. Test manually by:

1. **Frontend**: homepage, single posts, pages, archives, widgets, responsive layouts
2. **Admin**: Event management interface, newsletter signup, custom admin page, Nextcloud settings
3. **Browser Compatibility**: Chrome, Firefox, Safari, Edge
4. **Functionality**:
   - Event CRUD operations
   - Newsletter subscription form
   - Shortcode `[clge_cal_events]`
   - Nextcloud calendar synchronization
   - All post format displays
   - Custom templates

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
- **Maximum line length**: 80-120 characters (soft limit)

### Naming Conventions

- **Functions**: `snake_case` with prefix (e.g., `clge_setup()`, `clge_create_event()`, `clge_get_all_events()`)
- **Classes**: `PascalCase` (e.g., `Clge_Admin_Page`, `Clge_Nextcloud_API`)
- **Variables**: `snake_case` (e.g., `$table_name`, `$insert_data`, `$event`)
- **Constants**: `UPPER_SNAKE_CASE` (e.g., `CLGE_VERSION`)
- **Hooks (filters/actions)**: `lowercase_with_underscores` (e.g., `after_setup_theme`)
- **Database tables**: Prefix with `$wpdb->prefix` + theme slug (e.g., `$wpdb->prefix . 'clge_cal_events'`)

### Functions

```php
// Use function_exists guard for all theme functions
if ( ! function_exists( 'clge_function_name' ) ) :
function clge_function_name( $param ) {
    // code
}
endif;
```

For AJAX handlers, always check capabilities:
```php
function clge_ajax_handler() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( -1 );
    }
    // Handle request
}
```

### SQL Queries

- Always use `$wpdb->prepare()` for variable data to prevent SQL injection
- Use proper format specifiers: `%s` for string, `%d` for integer, `%f` for float
- Prefix tables with `$wpdb->prefix`
- Use `dbDelta()` for table creation and updates

```php
$table_name = $wpdb->prefix . 'clge_cal_events';
$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $id ) );

// For INSERT with multiple formats
$wpdb->insert(
    $table_name,
    [
        'nom' => sanitize_text_field( $name ),
        'debut' => $start_date,
        'evt_clge' => (int) $is_clge
    ],
    ['%s', '%s', '%d']
);
```

### Security

- Use `sanitize_email()` for email fields
- Use `sanitize_text_field()` for short text inputs
- Use `sanitize_textarea_field()` for long text
- Use `esc_html()` for HTML output
- Use `esc_attr()` for HTML attributes
- Use `esc_url()` for URLs in HTML
- Use `esc_url_raw()` for URLs in database
- Use `wp_nonce_field()` and `wp_verify_nonce()` / `check_admin_referer()` for form submissions
- Use `defined('ABSPATH')` guard on all include files
- Never commit secrets, API keys, or passwords
- Validate and sanitize all user input before processing

### Template Tags

- Use WordPress template tags: `get_template_part()`, `get_the_ID()`, `the_title()`, etc.
- Wrap translatable strings with `__('text', 'clge')` or `_e('text', 'clge')`
- Use escape functions: `esc_html_e()`, `esc_attr_e()`, `esc_url()`
- Always escape output, never trust user data

### CSS/JS Enqueueing

Enqueue scripts and styles properly:

```php
function clge_scripts() {
    // Main stylesheet
    wp_enqueue_style('clge-style', get_stylesheet_uri());

    // Font Awesome
    wp_enqueue_style(
        'fontawesome',
        get_template_directory_uri() . '/fontawesome/font-awesome.css',
        [],
        '4.3.0'
    );

    // RTL styles if needed
    if (is_rtl()) {
        wp_enqueue_style('clge-rtl', get_template_directory_uri() . '/rtl.css');
    }

    // Editor styles
    add_editor_style('editor-style.css');

    // Block editor styles
    add_action('enqueue_block_editor_assets', function() {
        wp_enqueue_style(
            'clge-block-editor-style',
            get_theme_file_uri('/editor-blocks.css')
        );
    });

    // JavaScript
    wp_enqueue_script(
        'clge-skip-link-focus-fix',
        get_template_directory_uri() . '/js/skip-link-focus-fix.js',
        [],
        '20151215',
        true
    );

    wp_enqueue_script(
        'clge-flexslider',
        get_template_directory_uri() . '/js/flexslider.js',
        ['jquery'],
        '',
        true
    );

    wp_enqueue_script(
        'clge-global',
        get_template_directory_uri() . '/js/global.js',
        ['jquery', 'masonry'],
        '',
        true
    );

    // Load imagesloaded if not already loaded by WordPress core
    wp_enqueue_script(
        'imagesloaded',
        get_template_directory_uri() . '/js/imagesloaded.pkgd.js',
        ['jquery'],
        '',
        true
    );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'clge_scripts');
```

**Note**: The theme uses the following WordPress core scripts:
- `jquery` (included with WordPress)
- `masonry` (included with WordPress)
- `comment-reply` (conditional)

### File Organization

```
clge/
├── functions.php              # Theme setup, hooks, main logic
├── style.css                  # Main stylesheet with theme header
├── rtl.css                    # Right-to-left styles
├── editor-style.css           # Editor styles
├── editor-blocks.css          # Block editor styles
├── content-aside.php          # Aside post format template
├── content-audio.php          # Audio post format template
├── content-author.php         # Author content template
├── content-gallery.php        # Gallery post format template
├── content-image.php          # Image post format template
├── content-link.php           # Link post format template
├── content-none.php           # No content template
├── content-page.php           # Page content template
├── content-post.php           # Standard post template (content.php)
├── content-quote.php          # Quote post format template
├── content-search.php         # Search results template
├── content-status.php         # Status post format template
├── content-video.php          # Video post format template
├── 404.php                    # 404 error page
├── archive.php                # Archive pages
├── comments.php               # Comments template
├── footer.php                 # Footer template
├── header.php                 # Header template
├── index.php                  # Main index template
├── page.php                   # Single page template
├── readme.txt                 # WordPress.org readme file
├── search.php                 # Search results page
├── sidebar.php                # Sidebar template
├── single.php                 # Single post template
├── TODO.md                    # Development tasks and notes
├── composer.json              # PHP dependencies configuration
├── .gitignore                 # Git ignore rules
├── screenshot.png             # Theme screenshot
├── fontawesome/
│   └── font-awesome.css       # Font Awesome 4.3.0 icon library
├── images/                    # Theme images
├── js/
│   ├── global.js              # Main theme JavaScript (Masonry, event handlers)
│   ├── flexslider.js          # FlexSlider jQuery plugin
│   ├── imagesloaded.pkgd.js   # ImagesLoaded library
│   └── skip-link-focus-fix.js # Accessibility focus fix
├── languages/                 # Translation files (25+ languages)
│   ├── clge.pot               # Template for new translations
│   ├── fr_FR.po/mo            # French translations
│   ├── es_ES.po/mo            # Spanish translations
│   ├── de_DE.po/mo            # German translations
│   └── ...
├── templates/
│   ├── contributors-page.php  # Authors/contributors grid template
│   ├── clge-calendrier.php    # Calendar admin interface template
│   ├── all_events.php         # Events list table template (AJAX)
│   ├── clge-debug-page.php    # Debug page template for Nextcloud integration
│   ├── clge-nextcloud-settings.php # Nextcloud settings page template
│   ├── full-width-page.php    # Full-width page template
│   ├── landing.php            # Admin landing page template
│   └── no-sidebar-page.php    # No sidebar page template
└── inc/
    ├── custom-header.php      # Custom header functionality
    ├── template-tags.php      # Template functions and tags
    ├── extras.php             # Additional theme functions
    ├── jetpack.php            # Jetpack plugin integration
    ├── jetpack-fonts.php      # Jetpack font handling
    ├── updater.php            # Theme updater for WordPress.com
    └── clge/
        ├── admin-page.php      # Admin interface for CLGE
        ├── cngeformations.php   # CNGE formations HTML parser and importer
        ├── database.php         # Custom database operations (events CRUD)
        ├── shortcodes.php       # Custom shortcodes ([clge_cal_events])
        └── nextcloud/
            ├── parsers.php              # iCalendar and date parsing functions
            ├── class-nextcloud.php       # Main Nextcloud integration class
            ├── class-nextcloud-api.php    # Nextcloud API client
            ├── class-nextcloud-calendars.php # Nextcloud calendar management
            ├── class-nextcloud-events.php  # Nextcloud event handling
            ├── class-nextcloud-settings.php # Nextcloud settings management
            ├── class-nextcloud-ui.php     # Nextcloud UI components
            ├── class-nextcloud-debug.php   # Debug utilities for Nextcloud
            └── class-nextcloud-encryption.php # Encryption utilities (AES-256)
```

## Working with This Theme

### Adding New Features

1. **PHP Functions**: Create new functions in appropriate `inc/` or `inc/clge/` files or `functions.php`
2. **Template Files**: Add template files in root or `templates/` directory
3. **CSS**: Add styles to `style.css` or create new stylesheet in root
4. **JavaScript**: Add scripts to `js/` directory and enqueue properly
5. **Translations**: Add strings using translation functions, update `.pot` file

### Database Operations

When adding custom tables (as in `inc/clge/database.php`):

- Create table on theme activation: `add_action( 'after_switch_theme', 'callback' )`
- Use `dbDelta()` for table creation (idempotent)
- Always prepare queries with `$wpdb->prepare()`
- Add migration functions for schema changes
- Prefix table names with `$wpdb->prefix`

Example table creation:
```php
function clge_create_custom_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'clge_custom_table';
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        value text DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    );";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_switch_theme', 'clge_create_custom_table');
```

### AJAX Handling

The theme registers multiple AJAX endpoints for event management, newsletter, and Nextcloud integration:

**Newsletter** (in functions.php):
```php
add_action('wp_ajax_send_newsletter', 'handle_newsletter_submission');
add_action('wp_ajax_nopriv_send_newsletter', 'handle_newsletter_submission');
```

**Event Management** (in inc/clge/admin-page.php):
```php
// Display interfaces
add_action('wp_ajax_clge_calendrier', 'hx_clge_calendrier');
add_action('wp_ajax_clge_all_events', 'all_events_list');

// Event CRUD
add_action('wp_ajax_clge_add_event', 'hx_add_event');
add_action('wp_ajax_clge_add_cnge_formation', 'hx_add_cnge_formation');
add_action('wp_ajax_clge_delete_event', 'hx_delete_event');
add_action('wp_ajax_clge_update_event', 'hx_update_event');
```

**Nextcloud Integration** (in inc/clge/nextcloud/class-nextcloud-*.php):
```php
// Calendar synchronization
add_action('wp_ajax_clge_nextcloud_sync_calendars', 'Clge_Nextcloud_Calendars::sync_calendars');
add_action('wp_ajax_clge_nextcloud_test_connection', 'Clge_Nextcloud_API::test_connection');
add_action('wp_ajax_clge_nextcloud_save_settings', 'Clge_Nextcloud_Settings::save_settings');
```

AJAX handler pattern:
```php
function clge_custom_ajax_handler() {
    // Verify nonce
    check_ajax_referer('clge_custom_action', 'nonce');
    
    // Verify capability
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(['message' => __('Unauthorized', 'clge')]);
    }
    
    // Process request
    $result = do_something($_POST);
    
    // Return JSON response
    if ($result) {
        wp_send_json_success(['message' => __('Success', 'clge')]);
    } else {
        wp_send_json_error(['message' => __('Error', 'clge')]);
    }
}
add_action('wp_ajax_clge_custom_action', 'clge_custom_ajax_handler');
```

### Translations

The theme is translation-ready. Add strings using:

```php
// For output with escaping
esc_html__( 'Text', 'clge' )
esc_attr__( 'Text', 'clge' )

// For direct output with escaping
esc_html_e( 'Text', 'clge' )
esc_attr_e( 'Text', 'clge' )

// For non-escaped text (use with caution)
__( 'Text', 'clge' )
_e( 'Text', 'clge' )
```

Update `.pot` file for new translations:
```bash
wp i18n make-pot . languages/clge.pot
```

To create new translation files:
```bash
# Create .po file from .pot
msginit -i languages/clge.pot -o languages/xx_XX.po -l xx_XX

# Compile .po to .mo
msgfmt languages/xx_XX.po -o languages/xx_XX.mo
```

## Custom Features

### Event Management System

The theme includes a complete event management system for managing CLGE calendar events.

**Database Table**: `$wpdb->prefix . 'clge_cal_events'`

| Column | Type | Description |
|--------|------|-------------|
| id | mediumint(9) | Auto-increment primary key |
| debut | datetime | Event start date/time |
| fin | datetime | Event end date/time |
| nom | varchar(255) | Event name |
| abrev | varchar(50) | Short abbreviation |
| alias | varchar(50) | Display alias (optional) |
| description | text | Event description (optional) |
| lieu_physique | varchar(255) | Physical location |
| url | varchar(255) | Event URL |
| evt_clge | bool | Flag: 1 = CLGE event, 0 = Formation |

**Functions** (in `inc/clge/database.php`):
- `clge_create_cal_events_table()`: Creates table on theme activation
- `clge_migrate_add_alias_description()`: Adds alias/description columns if missing
- `clge_create_event($data)`: Add new event (accepts DateTime objects or strings)
- `clge_get_all_events()`: Get all events (returns objects with DateTime for dates)
- `clge_get_event($id)`: Get single event by ID
- `clge_update_event($id, $data)`: Update event
- `clge_delete_event($id)`: Delete event

**Shortcode**: `[clge_cal_events]`
- Displays upcoming events (future dates only)
- Shows maximum 7 events on front page
- Shows all upcoming events on other pages
- Displays event dates, location, name, and description
- CLGE events (evt_clge=0) show "Formation:" prefix
- Uses alias if available, otherwise nom
- Events open in new tab if evt_clge=0

**Styling**: Shortcode includes inline CSS for event boxes with:
- Flexbox layout
- Date formatting in French (jour mois)
- Color coding: dates in blue (#1b6db5), formation name in orange (#f29816)
- Hover effects and shadows

### Newsletter Subscription

**Handler**: `handle_newsletter_submission()` in functions.php:338-358

**AJAX Endpoints**:
```php
add_action('wp_ajax_send_newsletter', 'handle_newsletter_submission');
add_action('wp_ajax_nopriv_send_newsletter', 'handle_newsletter_submission');
```

**Processing**:
1. Validates email with `sanitize_email()`
2. Sends email to: `contact@clge.fr`
3. Subject: "Nouvel abonnement à la newsletter"
4. Message includes the submitted email address
5. Returns HTML success/error message

**Security Notes**:
- Email is sanitized before use
- Headers include charset: `Content-Type: text/plain; charset=UTF-8`
- Uses `wp_mail()` for consistent email sending
- Nonce protection should be added to the form

**TODO**: Consider making the recipient email configurable via theme options instead of hardcoded.

### CNGE Formations Integration

**Module**: `inc/clge/cngeformations.php`

Parses HTML tables from CNGE (College National des Generalistes Enseignants) website to extract formation information and create events automatically.

**Constants**:
- `MoisVersNumero`: French month names to numbers mapping
- `ReDateFull`: Regex for full date format (dd/mm/yy HH:MM - dd/mm/yy HH:MM)
- `ReHeure`: Regex for time format (HH:MM - HH:MM)

**Main Function**: `parseFormationsTable(DOMElement $table, string $currentMois, string $annee)`
- Parses HTML table rows
- Extracts day, time, abbreviation, name, URL, and physical location
- Handles two date formats:
  1. Full format: dd/mm/yy HH:MM|dd/mm/yy HH:MM
  2. Time only: HH:MM|HH:MM (uses current month/year from context)
- Returns array of event objects with DateTime for debut/fin

**AJAX Endpoint**: `clge_add_cnge_formation`
1. Receives base64-encoded JSON data
2. Decodes and parses the formation data
3. Creates event using `clge_create_event()`
4. Returns updated events list via `all_events_list()`

**Usage**: Triggered from admin interface to import formations from external source.

### Nextcloud Integration

**Module**: `inc/clge/nextcloud/`

Complete Nextcloud CalDAV integration for calendar synchronization with CLGE events.

**Main Class**: `Clge_Nextcloud` (`class-nextcloud.php`)
- Entry point for Nextcloud integration
- Loads all dependencies and registers WordPress hooks
- Requires `SECURE_AUTH_KEY` to be defined in wp-config.php (minimum 32 characters for AES-256 encryption)

**Core Components**:

1. **API Client** (`class-nextcloud-api.php`)
   - Handles HTTP requests to Nextcloud server
   - Manages authentication (Basic Auth, Bearer tokens)
   - Provides methods for testing connection and server capabilities
   - Implements rate limiting and error handling

2. **Calendar Management** (`class-nextcloud-calendars.php`)
   - Lists available Nextcloud calendars
   - Syncs events from Nextcloud to local `clge_cal_events` table
   - Handles calendar color and display properties
   - Manages calendar subscriptions

3. **Event Handling** (`class-nextcloud-events.php`)
   - Parses iCalendar (ICS) event data
   - Maps Nextcloud events to CLGE event format
   - Handles event creation, updates, and deletion
   - Manages event recurrence and exceptions

4. **Settings Management** (`class-nextcloud-settings.php`)
   - Stores Nextcloud server URL, username, password (encrypted)
   - Manages synchronization settings and frequency
   - Provides admin interface for configuration
   - Validates connection settings before saving

5. **Encryption** (`class-nextcloud-encryption.php`)
   - Implements AES-256-CBC encryption for sensitive data
   - Uses WordPress `SECURE_AUTH_KEY` as encryption key
   - Encrypts Nextcloud password before storage
   - Decrypts password for API requests

6. **UI Components** (`class-nextcloud-ui.php`)
   - Renders settings form in WordPress admin
   - Displays connection status and sync information
   - Provides manual sync button and logs
   - Handles AJAX requests for sync operations

7. **Debug Utilities** (`class-nextcloud-debug.php`)
   - Logs API requests and responses
   - Provides debug page at `/wp-admin/admin.php?page=clge-debug`
   - Tests connection and displays server information
   - Logs synchronization errors

8. **Parsers** (`parsers.php`)
   - `clge_parse_icalendar_date()`: Parses iCalendar date strings (DATE, DATE-TIME, UTC)
   - `clge_parse_icalendar_content()`: Parses full iCalendar (ICS) content
   - `clge_parse_mailto()`: Extracts email addresses from mailto: links
   - Supports multiple date formats: YYYYMMDD, YYYYMMDDTHHMMSS, YYYY-MM-DD, etc.
   - Handles timezone conversion and day-only dates

**AJAX Endpoints**:
```php
// Settings and connection
add_action('wp_ajax_clge_nextcloud_save_settings', 'Clge_Nextcloud_Settings::save_settings');
add_action('wp_ajax_clge_nextcloud_test_connection', 'Clge_Nextcloud_API::test_connection');

// Calendar operations
add_action('wp_ajax_clge_nextcloud_sync_calendars', 'Clge_Nextcloud_Calendars::sync_calendars');
add_action('wp_ajax_clge_nextcloud_get_calendars', 'Clge_Nextcloud_Calendars::get_calendars');

// Debug
add_action('wp_ajax_clge_nextcloud_debug_info', 'Clge_Nextcloud_Debug::get_debug_info');
```

**Templates**:
- `templates/clge-nextcloud-settings.php`: Settings page with connection form
- `templates/clge-debug-page.php`: Debug information and connection testing

**Security**:
- Password encrypted using AES-256-CBC before storage
- Requires `manage_options` capability for all admin operations
- All AJAX requests use nonce verification
- Connection settings validated before use
- Sensitive data never logged in plain text

**Requirements**:
- PHP 7.4+
- OpenSSL extension for encryption
- cURL extension for HTTP requests
- WordPress 5.0+
- Nextcloud server with CalDAV enabled

### Admin Interface

**Page**: "CLGE" in WordPress admin menu (position 20, icon: dashicons-calendar-alt)

**Setup** (in `inc/clge/admin-page.php`):
```php
add_action('admin_menu', 'clge_add_admin_page');
```

**Main Template**: `templates/landing.php`
- Includes calendar interface and event management
- Uses AJAX for dynamic content loading
- Tabbed interface for different sections

**AJAX Endpoints**:
- `clge_calendrier`: Display calendar interface template
- `clge_all_events`: Display events list table
- `clge_add_event`: Add event manually (with nonce: clge_add_event)
- `clge_add_cnge_formation`: Add CNGE formation event (with nonce: clge_add_cnge_formation)
- `clge_delete_event`: Delete event (with nonce: clge_delete_event)
- `clge_update_event`: Update event (with nonce: clge_update_event)

All endpoints call `wp_die()` after output to prevent further WordPress processing.

## Git Practices

- **Commit messages**: Use [Conventional Commits](https://www.conventionalcommits.org/) format
  - `feat: add new feature`
  - `fix: correct a bug`
  - `docs: update documentation`
  - `refactor: code refactoring`
  - `chore: maintenance tasks`
  - `style: formatting changes`
  - `test: add tests`
- **Keep commits atomic**: One feature/fix per commit
- **Branch naming**: Use `feature/xxx`, `fix/xxx`, `docs/xxx`, `refactor/xxx` prefixes
- **Pull requests**: Required for main branch changes
- **Rebase instead of merge**: For feature branches to keep history clean
- **Do not commit**:
  - `vendor/` (Composer dependencies)
  - `node_modules/` (npm dependencies)
  - `.DS_Store` (macOS)
  - `wp-config.php` (WordPress configuration)
  - `*.sql` (database dumps)
  - `*.log` (log files)
  - IDE files (`.idea/`, `.vscode/`, `*.swp`, `*.swo`)
  - `.env` files (environment variables)
  - `composer.lock` (optional: can be committed for reproducibility)
  - `package-lock.json` (optional: can be committed for reproducibility)
- The `.gitignore` handles most exclusions

## Security Notes

### Data Sanitization
All custom database operations use proper sanitization:
- `sanitize_text_field()` for short text inputs (nom, abrev, alias, lieu_physique)
- `sanitize_textarea_field()` for long text (description)
- `sanitize_email()` for email addresses
- `esc_url_raw()` for URLs stored in database
- `esc_url()` for URLs output in HTML
- `(int)` casting for integer values (evt_clge, id)

### Output Escaping
Always escape output based on context:
- `esc_html()` for HTML content
- `esc_attr()` for HTML attributes
- `esc_url()` for URLs in HTML
- `wp_kses_post()` for HTML with allowed tags
- `wp_kses()` for custom allowed HTML

### Nonce Verification
All form submissions and AJAX requests must use nonces:
```php
// In form
wp_nonce_field('action_name', 'nonce_field_name');

// In AJAX handler
if (!check_ajax_referer('action_name', 'nonce_field_name')) {
    wp_send_json_error(['message' => __('Security check failed', 'clge')]);
}

// For admin form submissions
if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'action_name')) {
    wp_die(__('Security check failed', 'clge'));
}
```

### Database Security
- Always use `$wpdb->prepare()` with proper format specifiers
- Prefix all custom tables with `$wpdb->prefix`
- Use `dbDelta()` for table creation (safe and idempotent)
- Never use raw SQL queries with user input
- Use proper format specifiers: `%s`, `%d`, `%f`

### Sensitive Data
- **Never** commit: API keys, passwords, database credentials, secret tokens
- The newsletter recipient email (`contact@clge.fr`) is currently hardcoded in functions.php:342
- **Recommended**: Make configurable via theme options:
  ```php
  // In admin settings
  register_setting('clge_options', 'clge_newsletter_email');
  
  // In newsletter handler
  $to = get_option('clge_newsletter_email', 'contact@clge.fr');
  ```
- Nextcloud password is encrypted using AES-256 before storage in database

### File Security
- All include files must have `defined('ABSPATH')` guard
- Never allow direct file access
- Use `exit` or `wp_die()` after direct access check

Example:
```php
if (!defined('ABSPATH')) {
    exit; // or wp_die(__('Direct access forbidden', 'clge'));
}
```

## Additional Notes

### Template Hierarchy
The theme supports the following WordPress template hierarchy:
- `front-page.php` (if exists)
- `home.php` (if exists)
- `index.php`
- `single-{post-type}.php`
- `single.php`
- `page-{slug}.php`
- `page-{id}.php`
- `page.php`
- `archive-{post-type}.php`
- `archive.php`
- `taxonomy-{taxonomy}.php`
- `category-{slug}.php`
- `category.php`
- `tag-{slug}.php`
- `tag.php`
- `author-{nicename}.php`
- `author.php`
- `search.php`
- `404.php`

### Post Formats
The theme supports all WordPress post formats with custom templates:
- Standard: `content.php`
- Aside: `content-aside.php`
- Audio: `content-audio.php`
- Gallery: `content-gallery.php`
- Image: `content-image.php`
- Link: `content-link.php`
- Quote: `content-quote.php`
- Status: `content-status.php`
- Video: `content-video.php`

### Widget Areas
Four widget areas are registered:
- `sidebar-1`: Sidebar (right column)
- `sidebar-2`: Footer 1
- `sidebar-3`: Footer 2
- `sidebar-4`: Footer 3

### Theme Support
The theme declares support for:
- Automatic feed links
- Title tag
- Post thumbnails
- HTML5 (search-form, comment-form, comment-list, gallery, caption)
- Custom background
- Customize selective refresh widgets
- Custom logo (1200x300, flex width/height)
- Post formats (all)
- Editor styles
- Translation ready (text domain: 'clge')

### Image Sizes
- `clge-post-image`: 1400px width, unlimited height
- `clge-post-thumbnail`: 600px width, unlimited height

### Content Width
Global content width: 736px (filterable via `clge_content_width`)
Full-width template: 1120px

<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
    add_action( 'init', array( $this, 'register_taxonomies' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'setup_assets' ) );
		parent::__construct();
  }
  
  public function setup_assets() {
    wp_enqueue_script('main-js', get_stylesheet_directory_uri() . '/dist/main.js');
  }


	/** This is where you can register custom post types. */
	public function register_post_types() {
    include get_stylesheet_directory() . '/includes/cpt-project.php';
	}
	/** This is where you can register custom taxonomies. */
	public function register_taxonomies() {

  }
  


	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = new Timber\Menu();
    $context['site']  = $this;
    
    if(function_exists('get_cart_contents_count')) {
    //$context['cart_count'] = !is_cart() ? WC()->cart->get_cart_contents_count() : 0;
    }

		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
	}

	/** This Would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then returned 'foo bar!'.
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
		$twig->addFilter( new Twig\TwigFilter( 'myfoo', array( $this, 'myfoo' ) ) );
		return $twig;
	}

}

new StarterSite();



function timber_set_product( $post ) {
  global $product;

  // if ( is_woocommerce() ) {
      $product = wc_get_product( $post->ID );
  // }
}

remove_action( 'woocommerce_after_single_product_summary',
'woocommerce_output_related_products', 20 );
remove_action( 'woocommerce_before_shop_loop_item_title',
'woocommerce_template_loop_product_thumbnail' );

/**
 * Custom Events List Shortcode - Vertical list grouped by month
 * Usage: [events_list show_past="no" limit="20"]
 */
function tof_events_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_past' => 'no',
        'limit' => -1,
    ), $atts);

    // Query Tickera events
    $args = array(
        'post_type' => 'tc_events',
        'posts_per_page' => $atts['limit'],
        'meta_key' => 'event_date_time',
        'orderby' => 'meta_value',
        'order' => 'ASC',
        'post_status' => 'publish',
    );

    // Filter out past events if requested
    if ($atts['show_past'] === 'no') {
        $args['meta_query'] = array(
            array(
                'key' => 'event_date_time',
                'value' => current_time('Y-m-d H:i'),
                'compare' => '>=',
                'type' => 'DATETIME',
            ),
        );
    }

    $events = get_posts($args);

    if (empty($events)) {
        return '<p class="events-list__empty">No upcoming events scheduled.</p>';
    }

    // Group events by month
    $grouped_events = array();
    foreach ($events as $event) {
        $event_date = get_post_meta($event->ID, 'event_date_time', true);
        if ($event_date) {
            $month_key = date('Y-m', strtotime($event_date));
            $month_label = date('F Y', strtotime($event_date));
            if (!isset($grouped_events[$month_key])) {
                $grouped_events[$month_key] = array(
                    'label' => $month_label,
                    'events' => array(),
                );
            }
            $grouped_events[$month_key]['events'][] = $event;
        }
    }

    // Build output
    $output = '<div class="events-list">';

    foreach ($grouped_events as $month_key => $month_data) {
        $output .= '<div class="events-list__month">';
        $output .= '<h3 class="events-list__month-title">' . esc_html($month_data['label']) . '</h3>';
        $output .= '<div class="events-list__items">';

        foreach ($month_data['events'] as $event) {
            $event_date = get_post_meta($event->ID, 'event_date_time', true);
            $event_end_date = get_post_meta($event->ID, 'event_end_date_time', true);
            $event_location = get_post_meta($event->ID, 'event_location', true);
            $event_logo = get_post_meta($event->ID, 'event_logo_file_url', true);
            $presentation_page = get_post_meta($event->ID, 'event_presentation_page', true);
            $event_url = !empty($presentation_page) && is_numeric($presentation_page)
                ? get_permalink($presentation_page)
                : get_permalink($event->ID);

            $day = date('d', strtotime($event_date));
            $day_name = date('D', strtotime($event_date));
            $time = date('g:i A', strtotime($event_date));

            $output .= '<div class="events-list__item">';
            $output .= '<div class="events-list__date">';
            $output .= '<span class="events-list__day">' . $day . '</span>';
            $output .= '<span class="events-list__day-name">' . $day_name . '</span>';
            $output .= '</div>';
            $output .= '<div class="events-list__content">';
            $output .= '<h4 class="events-list__title"><a href="' . esc_url($event_url) . '">' . esc_html($event->post_title) . '</a></h4>';
            $output .= '<div class="events-list__meta">';
            $output .= '<span class="events-list__time">' . $time . '</span>';
            if (!empty($event_location)) {
                $output .= '<span class="events-list__location">' . esc_html($event_location) . '</span>';
            }
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';
        }

        $output .= '</div>'; // .events-list__items
        $output .= '</div>'; // .events-list__month
    }

    $output .= '</div>'; // .events-list

    return $output;
}
add_shortcode('events_list', 'tof_events_list_shortcode');

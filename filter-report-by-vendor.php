<?php
/**
 * Plugin Name: Filter Report By Vendor
 *
 * @package WooCommerce\Admin
 */


/**
 * Make the currency settings available to the javascript client using
 * AssetDataRegistry, available in WooCommerce 3.9.
 *
 * The add_currency_settings function is a most basic example, but below is
 * a more elaborate example of how one might use AssetDataRegistry in classes.
 *
```php
<?php

class MyClassWithAssetData {
private $asset_data_registry;
public function __construct( Automattic\WooCommerce\Blocks\AssetDataRegistry $asset_data_registry ) {
$this->asset_data_registry = $asset_data_registry;
}

protected function some_method_adding_assets() {
$this->asset_data_registry->add( 'myData', [ 'foo' => 'bar' ] );
}
}

// somewhere in the extensions bootstrap
class Bootstrap {
protected $container;
public function __construct( Automattic\WooCommerce\Blocks\Container $container ) {
$this->container = $container;
$this->container->register( MyClassWithAssetData::class, function( $blocks_container ) => {
return new MyClassWithAssetData( $blocks_container->get( Automattic\WooCommerce\Blocks\AssetDataRegistry::class ) );
} );
}
}

// now anywhere MyClassWithAssetData is instantiated it will automatically be
// constructed with the AssetDataRegistry
```
 */
function add_vendor_settings() {
    $vendors = [
        [
            'label' => 'All vendors',
            'value' => "all"

        ]
    ];

    $args = [
        'role'    => 'wcfm_vendor',
        'orderby' => 'user_nicename',
        'order'   => 'ASC'
    ];

    $users = get_users( $args );

    foreach($users as $user) {
        $vendors[] = [
            'label' => wcfm_get_vendor_store_name( $user->ID ),
            'value' => $user->ID,
        ];
    }

    $data_registry = Automattic\WooCommerce\Blocks\Package::container()->get(
        Automattic\WooCommerce\Blocks\Assets\AssetDataRegistry::class
    );

    $data_registry->add( 'vendors', $vendors );
}



/**
 * Register the JS.
 */
function add_extension_register_script() {
	if ( ! class_exists( 'Automattic\WooCommerce\Admin\Loader' ) || ! \Automattic\WooCommerce\Admin\Loader::is_admin_or_embed_page() ) {
		return;
	}

    add_vendor_settings();


    $script_path       = '/build/index.js';
	$script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
	$script_asset      = file_exists( $script_asset_path )
		? require( $script_asset_path )
		: array( 'dependencies' => array(), 'version' => filemtime( $script_path ) );
	$script_url = plugins_url( $script_path, __FILE__ );

	wp_register_script(
		'filter-report-by-vendor',
		$script_url,
		$script_asset['dependencies'],
		$script_asset['version'],
		true
	);

	wp_register_style(
		'filter-report-by-vendor',
		plugins_url( '/build/index.css', __FILE__ ),
		// Add any dependencies styles may have, such as wp-components.
		array(),
		filemtime( dirname( __FILE__ ) . '/build/index.css' )
	);

	wp_enqueue_script( 'filter-report-by-vendor' );
	wp_enqueue_style( 'filter-report-by-vendor' );
}

add_action( 'admin_enqueue_scripts', 'add_extension_register_script' );


function apply_vendor_arg( $args ) {
    $vendor = 'all';

    // phpcs:disable WordPress.Security.NonceVerification.Recommended
    if ( isset( $_GET['vendor'] ) ) {
        $vendor = sanitize_text_field( wp_unslash( $_GET['vendor'] ) );
    }
    // phpcs:enable

    $args['vendor'] = $vendor;

    return $args;
}

add_filter( 'woocommerce_analytics_orders_query_args', 'apply_vendor_arg' );
add_filter( 'woocommerce_analytics_orders_stats_query_args', 'apply_vendor_arg' );

function add_join_subquery( $clauses ) {
    global $wpdb;

    $clauses[] = "JOIN {$wpdb->prefix}woocommerce_order_items order_item ON {$wpdb->prefix}posts.ID = order_item.order_id";
    $clauses[] = "JOIN {$wpdb->prefix}woocommerce_order_itemmeta order_itemmeta ON order_item.order_id = order_itemmeta.order_item_id";

    return $clauses;
}

add_filter( 'woocommerce_analytics_clauses_join_orders_subquery', 'add_join_subquery' );
add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total', 'add_join_subquery' );
add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval', 'add_join_subquery' );


function add_where_subquery( $clauses ) {
    $vendor = 'all';

    if ( isset( $_GET['vendor'] ) ) {
        $vendor = sanitize_text_field( wp_unslash( $_GET['vendor'] ) );
    }

    if($vendor !== 'all') {

        $clauses[] = "AND order_itemmeta.meta_key = '_vendor_id' AND order_itemmeta.meta_value = '{$vendor}'";
    } else {
        $clauses[] = "AND order_itemmeta.meta_key = '_vendor_id'";
    }
    return $clauses;
}

add_filter( 'woocommerce_analytics_clauses_where_orders_subquery', 'add_where_subquery' );
add_filter( 'woocommerce_analytics_clauses_where_orders_stats_total', 'add_where_subquery' );
add_filter( 'woocommerce_analytics_clauses_where_orders_stats_interval', 'add_where_subquery' );


function add_select_subquery( $clauses ) {
    $clauses[] = ', order_itemmeta.meta_value AS vendor';

    return $clauses;
}

add_filter( 'woocommerce_analytics_clauses_select_orders_subquery', 'add_select_subquery' );
add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total', 'add_select_subquery' );
add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', 'add_select_subquery' );

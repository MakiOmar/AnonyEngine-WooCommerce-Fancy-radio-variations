<?php
/**
 * Plugin Name: WooCommerce Fancy radio variations
 * Description: Variations Radio Buttons for WooCommerce. Let your customers choose product variations using popup radio buttons instead of dropdowns.
 * Version:     1.0.0
 * Author:      Mohammad Omar
 * Author URI:  http://8manos.com
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * WC requires at least: 3.0
 * WC tested up to:      7.7.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Check if WooCommerce is active
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

	class WC_Radio_Buttons {
		// plugin version
		const VERSION = '2.0.0';

		private $plugin_path;
		private $plugin_url;

		public function __construct() {
			add_filter( 'woocommerce_locate_template', array( $this, 'locate_template' ), 10, 3 );

			//js scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ), 999 );
		}

		public function get_plugin_path() {

			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = plugin_dir_path( __FILE__ );
		}

		public function get_plugin_url() {

			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = plugin_dir_url( __FILE__ );
		}

		public function locate_template( $template, $template_name, $template_path ) {
			global $woocommerce;

			$_template = $template;

			if ( ! $template_path ) {
				$template_path = $woocommerce->template_url;
			}

			$plugin_path = $this->get_plugin_path() . 'templates/';

			// Look within passed path within the theme - this is priority
			$template = locate_template( array(
				$template_path . $template_name,
				$template_name
			) );

			// Modification: Get the template from this plugin, if it exists
			if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
				$template = $plugin_path . $template_name;
			}

			// Use default template
			if ( ! $template ) {
				$template = $_template;
			}

			return $template;
		}

		function load_scripts() {
			// Don't load JS if current product type is bundle to prevent the page from not working
			if (!(wc_get_product() && wc_get_product()->is_type('bundle'))) {
				wp_deregister_script( 'wc-add-to-cart-variation' );
				wp_register_script( 'wc-add-to-cart-variation', $this->get_plugin_url() . 'assets/js/frontend/add-to-cart-variation.js', array( 'jquery', 'wp-util' ), self::VERSION );
			}
		}
	}

	new WC_Radio_Buttons();

	if ( ! function_exists( 'print_attribute_radio' ) ) {
		function print_attribute_radio( $checked_value, $value, $label, $name ) {
			global $product;

			$input_name = 'attribute_' . esc_attr( $name ) ;
			$target = 'attribute-' . esc_attr( $name ) ;
			$esc_value = esc_attr( $value );
			$id = esc_attr( $name . '_v_' . $value . $product->get_id() ); //added product ID at the end of the name to target single products
			$checked = checked( $checked_value, $value, false );
			$filtered_label = apply_filters( 'woocommerce_variation_option_name', $label, esc_attr( $name ) );
			printf( '<div><input type="radio" class="anony-variation-radio" name="%1$s" value="%2$s" id="%3$s" data-target="%4$s" %5$s><label for="%3$s">%6$s</label></div>', $input_name, $esc_value, $id,$target, $checked, $filtered_label );
		}
	}
}


function my_edit_wc_attribute_anony_attribute_icon() {
    $id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
    $value = $id ? get_option( "wc_attribute_anony_attribute_icon-$id" ) : '';
    ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="anony-attribute-icon">SVG Icon</label>
            </th>
            <td>
                <input type="text" name="anony_attribute_icon" id="anony-attribute-icon" value="<?php echo esc_attr( $value ); ?>"/>
            </td>
        </tr>
    <?php
}
add_action( 'woocommerce_after_add_attribute_fields', 'my_edit_wc_attribute_anony_attribute_icon' );
add_action( 'woocommerce_after_edit_attribute_fields', 'my_edit_wc_attribute_anony_attribute_icon' );

function my_save_wc_attribute_anony_attribute_icon( $id ) {
    if ( is_admin() && isset( $_POST['anony_attribute_icon'] ) ) {
        $option = "wc_attribute_anony_attribute_icon-$id";
       
        update_option( $option, sanitize_text_field( $_POST['anony_attribute_icon'] ) );
    }
}
add_action( 'woocommerce_attribute_added', 'my_save_wc_attribute_anony_attribute_icon' );
add_action( 'woocommerce_attribute_updated', 'my_save_wc_attribute_anony_attribute_icon' );

add_action( 'woocommerce_attribute_deleted', function ( $id ) {
    delete_option( "wc_attribute_anony_attribute_icon-$id" );
} );
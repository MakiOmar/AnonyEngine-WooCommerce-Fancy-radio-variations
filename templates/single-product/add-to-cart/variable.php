<?php
/**
 * Variable product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.4.1
 *
 * Modified to use radio buttons instead of dropdowns
 * @author 8manos
 */

defined( 'ABSPATH' ) || exit;

global $product;
global $woocommerce;

$attribute_keys = array_keys( $attributes );

do_action( 'woocommerce_before_add_to_cart_form' );
add_action('wp_footer', function(){
    ?>
    <script>
        
        jQuery(document).ready(function($) {
          $('.anony-variation-label').on('click', function(){
              var target = $(this).attr('for');
              $('.anony-variation-value').removeClass('anony-variation-visible');
              $('#' + target + '_values').addClass('anony-variation-visible');
             
          });
          $('.anony-variation-close').on('click', function(){
              $(this).closest('div.anony-variation-value').removeClass('anony-variation-visible');
          });
          $('.anony-variation-radio').on('change', function(){
              var target = $(this).data('target');
              $('.' + target).find('.anony-selected-variation').text(decodeURIComponent($(this).val()));
          });
        });
    </script>
    <?php
});
?>

<style>
    
    .anony-variations{
        display: flex;
        flex-wrap:wrap;
    }
    .anony-variation{
        margin: 2px;
        display: inline-flex;
        justify-content:center;
        align-items:center;
        background-color: #F8F8FB;
        color: #000;
        border-radius:8px;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        position:relative;
        min-width: 29%;
        box-sizing: border-box;
    }
    .anony-variation i{
        position: absolute;
        top: 5px;
        left: 5px;
    }
    .anony-selected-variation{
        text-align:center;
        padding: 5px;
    }
    
    .anony-variation-value{
        background-color:#fff;
        color: #000;
        position:fixed;
        bottom:0;
        right: 0;
        padding:10px;
        padding-top: 40px;
        border-top-left-radius: 30px;
        border-top-right-radius: 30px;
        z-index: 20;
        width: 100%;
        bottom:-100vh;
        -webkit-box-shadow: 0px 0px 5px 0px rgba(194,194,194,1);
        -moz-box-shadow: 0px 0px 5px 0px rgba(194,194,194,1);
        box-shadow: 0px 0px 5px 0px rgba(194,194,194,1);
        transition: all 1s ease-in-out;

    }
    .anony-variation-value .variation-option{
        border: 1px solid #a41e01;
        border-radius: 5px;
        margin-bottom: 8px;
    }
    .anony-variation-value .variation-option > div{
        padding: 10px;
        display: flex;
        flex-direction: row-reverse;
        justify-content: space-between;
        height: 100%;
        width: 100%;
    }
    .anony-variation-value .variation-option input[type="radio"] {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      border-radius: 50%;
      border: 2px solid #a41e01;
      width: 20px;
      height: 20px;
      outline: none;
    }
    
    .anony-variation-value .variation-option input[type="radio"]:checked {
      background-color: #a41e01;
    }
    .anony-variation-header{
        height: 60px;
        border-bottom: 1px solid #d0d0d0;
        margin-bottom: 20px;
    }
    .anony-variation-close{
        position: absolute;
        top:10px;
        left:20px;
        background:#d00e0e;
        color:#fff;
        padding:6px;
        border-radius:3px;
        height: 25px;
        width: 25px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor:pointer;
        z-index:30;
    }
    .anony-variation-value.anony-variation-visible{
        bottom:0;
    }
    .anony-variation-label{
        display: inline-flex;
        justify-content: center;
        align-items: center;
        padding-top: 30px;
        width: 100px;
        cursor: pointer;
        padding-bottom: 10px;
    }
    .anony-variation-line{
        display: block;
        margin: auto;
        height:1px;
        width:100px;
        border-bottom:1px solid #000;
    }
    .anony-variation-icon{
        position: absolute;
        top: 5px;
        right: 10px;
    }
</style>
<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ) ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
	<?php else : ?>
		<div class="variations anony-variations" cellspacing="0">
				<?php foreach ( $attributes as $name => $options ) : ?>
                    <?php 
                    $sanitized_name = sanitize_title( $name );
                    $attr_id = wc_attribute_taxonomy_id_by_name( $name );
                    $icon = get_option( "wc_attribute_anony_attribute_icon-$attr_id" );
                    ?>
                    <div class="variation anony-variation attribute-<?php echo esc_attr( $sanitized_name ); ?>">
                        <?php
                            if( $icon ){?>
                                <img class="anony-variation-icon" src="<?php echo $icon ?>"/>
                            <?php }
                        ?>
                        <i class="fa fa-angle-down fa-border"></i>
                        <div><label class="label anony-variation-label" for="<?php echo esc_attr( $sanitized_name ); ?>"><?php echo wc_attribute_label( $name ); ?></label></div>
                        <div class="anony-selected-variation"><?php esc_html_e('Choose an option', 'woocommerce') ?></div>
                        <?php
                        if ( isset( $_REQUEST[ 'attribute_' . $sanitized_name ] ) ) {
                            $checked_value = $_REQUEST[ 'attribute_' . $sanitized_name ];
                        } elseif ( isset( $selected_attributes[ $sanitized_name ] ) ) {
                            $checked_value = $selected_attributes[ $sanitized_name ];
                        } else {
                            $checked_value = '';
                        }
                        ?>
                        <div id="<?php echo esc_attr( $sanitized_name ); ?>_values" class="value anony-variation-value">
                            <div class="anony-variation-header">
                                <span class="anony-variation-close">x</span>
                                <span class="anony-variation-line"></span>
                                <h4><?php echo wc_attribute_label( $name ); ?></h4>
                            </div>
                            <?php
                            if ( ! empty( $options ) ) {
                                if ( taxonomy_exists( $name ) ) {
                                    // Get terms if this is a taxonomy - ordered. We need the names too.
                                    $terms = wc_get_product_terms( $product->get_id(), $name, array( 'fields' => 'all' ) );
                
                                    foreach ( $terms as $term ) {
                                        if ( ! in_array( $term->slug, $options ) ) {
                                            continue;
                                        }
                                        ?>
                                        <div class="variation-option">
                                            <?php print_attribute_radio( $checked_value, $term->slug, $term->name, $sanitized_name ); ?>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    foreach ( $options as $option ) {
                                        ?>
                                        <div class="variation-option">
                                            <?php print_attribute_radio( $checked_value, $option, $option, $sanitized_name ); ?>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                
                            if ( end( $attribute_keys ) === $name ) {
                                echo wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<div class="reset_variations"><a href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a></div>' ) );
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
		</div>

		<?php
			if ( version_compare($woocommerce->version, '3.4.0') < 0 ) {
				do_action( 'woocommerce_before_add_to_cart_button' );
			}
		?>

		<div class="single_variation_wrap">
			<?php
				do_action( 'woocommerce_before_single_variation' );
				do_action( 'woocommerce_single_variation' );
				do_action( 'woocommerce_after_single_variation' );
			?>
		</div>

		<?php
			if ( version_compare($woocommerce->version, '3.4.0') < 0 ) {
				do_action( 'woocommerce_after_add_to_cart_button' );
			}
		?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

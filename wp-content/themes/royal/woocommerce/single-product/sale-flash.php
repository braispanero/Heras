<?php
/**
 * Product loop sale flash
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product;
?>
<div class="label-product">
	<?php if ( $product->is_on_sale() ) : ?>
	
		<div class="type-label-2">
			<div class="sale"><?php _e('Sale', ETHEME_DOMAIN); ?></div>
		</div>
	
	<?php endif; ?>
</div>
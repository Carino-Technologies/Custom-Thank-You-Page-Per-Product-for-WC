<?php

namespace WCCustomThankYouPage;

class WCCustomThankYouPage {
	/**
	 * The meta key used to store the products custom thank you page
	 */
	const META_KEY = 'custom-thank-you-page';

	/**
	 * The label input's name that is visible for the user
	 */
	const LABEL_INPUT_NAME = "product-thank-you-label";

	/**
	 * The ID input's name that is used to store the (invisible) ID of the local page
	 */
	const ID_INPUT_NAME = "product-thank-you";

	/**
	 * Method to kickstart the plugin. Can do whatever you want, but be careful to use it in compliance with WordPress
	 *
	 * @return void
	 */
	public static function start(){
		new WCCustomThankYouPage;
	}

	/**
	 * Adds all necessary action hooks
	 */
	private function __construct(){
		// The field
		add_action('woocommerce_product_options_general_product_data', array($this, 'addProductGeneralTabField'));
		add_action('woocommerce_process_product_meta', array($this, 'saveProductGeneralTabField'));

		// Custom thank you page handling after checkout
		add_action('woocommerce_thankyou', array($this, 'redirectThankYouPage'));
	}

	/**
	 * Sets up the text input field used for selecting the thank you page
	 */
	public function addProductGeneralTabField(){
		global $post;

		$labelFieldValue = "";
		$IDFieldValue = "";

		$metaValue = get_post_meta($post->ID, self::META_KEY, true);

		if(!empty($metaValue)){
			if((int) $metaValue !== 0){
				$IDFieldValue = $metaValue;
				$labelFieldValue = get_the_title((int) $metaValue);
			} else {
				$labelFieldValue = $metaValue;
			}
		}

		echo "<div class=\"options_group\">";
		woocommerce_wp_text_input(array('placeholder' => __('Add Your URL Here', 'custom-thank-you-page'), 'id' => self::LABEL_INPUT_NAME, 'label' => __('Custom Thank You Page', 'custom-thank-you-page'), 'value' => $labelFieldValue));
		woocommerce_wp_hidden_input(array('id' => self::ID_INPUT_NAME, 'value' => $IDFieldValue));
		echo "</div>";
	}

	/**
	 * Saves the contents from the thank you page field
	 */
	public function saveProductGeneralTabField($id){
		if(!isset($_REQUEST[self::ID_INPUT_NAME]) || !isset($_REQUEST[self::LABEL_INPUT_NAME])){
			new \WP_Error('Necessary field values are not present');
			return;
		}

		$thankYouPage = $_REQUEST[self::ID_INPUT_NAME];
		$thankYouPageLabel = trim($_REQUEST[self::LABEL_INPUT_NAME]);

		if(strpos($thankYouPageLabel, 'http') === 0){
			update_post_meta($id, self::META_KEY, $thankYouPageLabel);
		} else if(!empty($thankYouPage)){
			update_post_meta($id, self::META_KEY, $thankYouPage);
		} else {
			update_post_meta($id, self::META_KEY, '');
		}
	}

	/**
	 * Redirects to the proper selected thank you page (if any)
	 *
	 * @param int $orderID
	 */
	public function redirectThankYouPage($orderID){
		$order = wc_get_order($orderID);
		$items = $order->get_items();

		if(count($items) === 1){
			$keys = array_keys($items);
			$thankYouPage = get_post_meta($items[$keys[0]]['product_id'], self::META_KEY, true);

			if(!empty($thankYouPage)){
				if((int) $thankYouPage !== 0){
					$page = get_permalink((int) $thankYouPage);
					wp_redirect($page);
				} else {
					wp_redirect($thankYouPage);
				}
			}
		}
	}
}
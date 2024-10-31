<?php
/**
 * Businesspay Gateway
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Class Give_businesspay_php
 *
 */
class Give_businesspay_php {
	
	public $id = 'businesspay';
	/**
	 * Give_businesspay_php constructor.
	 */
	public function __construct() {

		add_filter( 'give_payment_gateways', array( $this, 'register_gateway' ) );
		add_action( 'give_gateway_' . $this->id, array( $this, 'process_payment' ) );
		add_filter( 'give_get_settings_gateways', array( $this, 'add_settings' ), 2 );		
		add_filter( 'give_get_sections_gateways', array( $this, 'businesspay_get_sections_gateways') );
		add_action( 'give_donation_form_before_cc_form',array($this,'give_businesspay_ach_cc_form'), 10, 1 );
		add_action( 'give_payment_mode_after_gateways',array( $this,'add_logo_image_businesspay'), 10, 1 );		
    	add_action( 'give_payment_mode_top',array( $this,'hide_show_image_businesspay'), 10, 1 );    	
    	add_action( 'give_pre_form', array( $this, 'businesspay_render_frontend_form_notices' ), 10, 1 );
	}
    
    public function businesspay_render_frontend_form_notices(){
		
		if(isset($_GET['businesspay_error'])){
		$businesspay_errors = sanitize_text_field($_GET['businesspay_error']);		
	        if(isset($businesspay_errors)){
			   echo '<div id="businesspay_error" class="give_error give_warning"><p>'.__('BusinessPay : '.$businesspay_errors,'give-businesspay').'</p></div>';
			    $scriptdata = '<script>
					jQuery(document).ready(function () {					
						window.setTimeout(
							function () {	jQuery("#businesspay_error").slideUp(); 	},5000);
					})</script>';
			    echo  __($scriptdata, 'give-businesspay');
			}
		}
    }
    /*
    * This function add image on donation form
    */  
    public function add_logo_image_businesspay(){
		echo _e( '<img  class="visa_master_businesspay businesspay" src="'.plugin_dir_url( dirname( __FILE__ ) ).'/images/Asoriba_logo.png' .'" title="businesspay" width="100px" alt="businesspay">', 'give-businesspay' );
	}	     
    /*
    * This function use for show image on donation form after ratio button
    */  
	public function hide_show_image_businesspay(){
		
		$scriptimgdata =  '<script type="text/javascript">
		 jQuery(document).ready(function(){				
				jQuery( ".give-gateway" ).each(function() {
					if (jQuery(this).prop("checked")) {	
							if(this.value == "businesspay"){
								jQuery(".visa_master_businesspay").show();
							}else{
								jQuery(".visa_master_businesspay").hide();
							}
					}
	 			});				
				jQuery(".give-gateway").click(function(){ 			
						if(this.value == "businesspay"){					
							jQuery(".visa_master_businesspay").show();
						}else{
							jQuery(".visa_master_businesspay").hide();
						} });
				});
		 </script>';
		  echo  __($scriptimgdata, 'give-businesspay');
     }
	/**
	 * Add section to giveWP payment gateway
	 */
		function businesspay_get_sections_gateways( $sections ) {
			
			// `instamojo-settings` is the name/slug of the payment gateway section.
			$sections['businesspay-settings'] = __( 'Asoriba Businesspay', 'businesspay-payment' );

			return $sections;
		}
	/**
	*
	*
	*/
	function give_businesspay_ach_cc_form( $form_id ) {
		$current_getway= give_get_chosen_gateway( $form_id );
		if($current_getway=='businesspay')
		{
		   $publickey = '';
		   $businesspay_testmode=give_get_option('businesspay_testmode');
		   $businesspay_test_public_key=give_get_option('businesspay_test_public_key');
		   $businesspay_public_key=give_get_option('businesspay_public_key');
		     
		    if($businesspay_testmode == 'on'){
						$publickey = $businesspay_test_public_key;
				}else{
						$publickey = $businesspay_public_key;
				}
		    if($publickey==''){
        		 
        		 Give_Notices::print_frontend_notice( sprintf(
        		__( 'BusinessPay : The payment gateway account is not set properly.', 'give-businesspay' )), true, 'warning' );
        		
		    }
		  
		  remove_action('give_cc_form','give_get_cc_form' );		 

			?>
			<br>
			<fieldset id="give_cc_address" class="cc-address">
				<legend>CUSTOMER INFO</legend>
				<?php $desc = give_get_option('businesspay_description'); 					 
					 echo  __('<em>'.$desc.'</em>', 'give-businesspay');
					  ?>
				<div id="give-message-wrap" class="form-row form-row-wide">
				<label class="give-label" for="give-engraving-message_mb"> <?php _e( 'Mobile Number *', 'give-businesspay' ); ?><?php if ( give_field_is_required( 'give_businesspay_mobile', $form_id ) ) : ?>
						<span class="give-required-indicator">*</span>
						<?php endif ?>
						<span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e( 'Mobile Number', 'give-businesspay' ) ?>"></span>
			  </label>
				<input type="text" class="give-textbox" name="give_businesspay_mobile" required id="give_businesspay_mobile"  >
				</div>	
			</fieldset>
		<?php
		}		
	}
	/**
	 * Registers the gateway.	
	 *
	 * @return array
	 */
	public function register_gateway( $gateways ) {
		
		$gateways[ $this->id] = array(
			'admin_label'    => esc_html__( 'Businesspay', 'businesspay-payment' ),
			'checkout_label' => esc_html__( 'Businesspay', 'businesspay-payment' )
		);
		return $gateways;
	}
	function insta_for_give_register_payment_gateway_sections( $sections ) {	
	
		$sections['businesspay-settings'] = __( 'Businesspay', 'businesspay-payment' );
		return $sections;
	}
	/**
	 * Register the gateway settings.
	 *
	 * Adds the settings to the Payment Gateways section (CMB2).	 
	 */
	public function add_settings( $businesspay_settings ) {
		
		switch ( give_get_current_setting_section() ) {
		case 'businesspay-settings':
		
		$businesspay_settings = array(
				array(
					'id'   => 'give_title_businesspay',
					'type' => 'title',
				),
			);
		  	$businesspay_settings[] = array(
				'name' => '<strong>' . esc_html__( 'Businesspay', 'businesspay-payment' ) . '</strong>',
				'desc' => '<p style="background: #FFF; padding: 15px;border-radius: 5px;">' . sprintf( __( 'Businesspay payment gateway', 'businesspay-payment' ), '' ) . '</p>',
				'id' => 'give_title_businesspay_payment',
				'type' => 'give_title',
				);
			$businesspay_settings[] = array(
				'id' => 'businesspay_title',
				'name' => esc_html__( 'Title', 'businesspay-payment' ),
				'desc' => esc_html__( 'This Title show on payment gateway page', 'businesspay-payment' ),
				'type' => 'text',
				'size' => 'regular'
				);
			$businesspay_settings[] = array(
				'id' => 'businesspay_description',
				'name' => esc_html__( 'Description', 'businesspay-payment' ),
				'desc' => esc_html__( 'This Description show on donation and payment gateway page', 'businesspay-payment' ),
				'type' => 'text',
				'size' => 'regular'
				);
			$businesspay_settings[] = array(
				'id' => 'businesspay_testmode',
				'title' => __( 'Enable Businesspay sandbox', 'businesspay-payment' ),
				'type' => 'checkbox',
				'label' => __( 'Enable Businesspay sandbox', 'businesspay-payment' ),
				'default' => 'no',
				'description' => __( 'Businesspay sandbox can be used to test payments.' ),
				);
			$businesspay_settings[] = array(
				'id' => 'businesspay_test_public_key',
				'name' => esc_html__( 'Test Public Key', 'businesspay-payment' ),
				'desc' => esc_html__( 'This Test Public Key Provided by Businesspay Payment Gateway', 'businesspay-payment' ),
				'type' => 'api_key',
				'size' => 'regular'
				);
			$businesspay_settings[] = array(
				'id' => 'businesspay_public_key',
				'name' => esc_html__( 'Public Key', 'businesspay-payment' ),
				'desc' => esc_html__( 'This Public Key Provided by Businesspay Payment Gateway', 'businesspay-payment' ),
				'type' => 'api_key',
				'size' => 'regular'
				);
			$businesspay_settings[] = array(
				'id' => 'businesspay_order_image_url',
				'name' => esc_html__( 'Order Image URL', 'businesspay-payment' ),
				'desc' => esc_html__( 'Order Image URL', 'businesspay-payment' ),
				'type' => 'file',
				'size' => 'regular'
				);
			$businesspay_settings[] = array(
				'id' => 'businesspay_return_page_id',
				'title' => __('Callback Return Page'),
				'type' => 'select',
				'options' => $this -> get_pages('Select Page'),
				'description' => "Callback URL of return page"
				);
			$businesspay_settings[] = array(
				'id' => 'businesspay_post_page_id',
				'title' => __('Post Page URL'),
				'type' => 'select',
				'options' => $this -> get_pages('Select Page'),
				'description' => "Post page URL of form page"
				);
	
			$businesspay_settings[] = array(
				'id'   => 'give_title_businesspay',
				'type' => 'sectionend',
			);
		break;
	} // End switch().
		return $businesspay_settings;
			
	}
	function get_pages($title = false, $indent = true) {
        $wp_pages = get_pages('sort_column=menu_order');
        $page_list = array();
        if ($title) $page_list[] = $title;
        foreach ($wp_pages as $page) {
            $prefix = '';
            // show indented child pages?
            if ($indent) {
                $has_parent = $page->post_parent;
                while($has_parent) {
                    $prefix .=  ' - ';
                    $next_page = get_page($has_parent);
                    $has_parent = $next_page->post_parent;
                }
            }
            // add to page list array 
            $page_list[$page->ID] = $prefix . $page->post_title;
        }
        return $page_list;
    }

	function give_jpay_ach_cc_form( $form_id ) {
		$current_getway= give_get_chosen_gateway( $form_id );
		if($current_getway=='businesspay'){
			
			  add_action( 'give_after_cc_fields', 'give_default_cc_address_fields' );
		}
	}
	/**
	 * Processes the payment.
	 *
	 * @param array $purchase_data
	 */
	public function process_payment( $purchase_data ) {
	//Get Submitted records from Give Form
		$give_options = give_get_settings();
		// Payment complete, log to Give and return user to success page.
		if(isset($purchase_data['post_data']['give-cs-currency']) && $purchase_data['post_data']['give-cs-currency'] != "" ){
			$curency = $purchase_data['post_data']['give-cs-currency'];
		} else {
		    $curency = "";
		}
		
		$finalamount 	= $purchase_data['post_data']['give-amount'];
		$product_name	= $purchase_data['post_data']['give-form-title'];
		$product_desc	= $purchase_data['post_data']['give-form-title'];			
		$pub_key		= "";
		$first_name		= $purchase_data['user_info']['first_name'];
		$last_name		= $purchase_data['user_info']['last_name'];
		$email			= $purchase_data['user_info']['email'];
		$phone_number	= $purchase_data['post_data']['give_businesspay_mobile'];
	/*Get dynamic data from setting page*/	
		$businesspay_testmode 		= give_get_option('businesspay_testmode'); 
		$businesspay_test_public_key= give_get_option('businesspay_test_public_key');
		$businesspay_public_key		= give_get_option('businesspay_public_key');
		$businesspay_order_image_url= give_get_option('businesspay_order_image_url');
		$businesspay_title          = give_get_option('businesspay_title');
		$businesspay_description     = give_get_option('businesspay_description');
		$businesspay_description     = give_get_option('businesspay_description');
		$businesspay_return_page_id  = get_permalink(give_get_option('businesspay_return_page_id'));
		
		$businesspay_post_page_id  = get_permalink(give_get_option('businesspay_post_page_id'));
		
		if ($businesspay_testmode == "on")
		{
		    $pub_key = $businesspay_test_public_key;
		   
		} else {
			$pub_key = $businesspay_public_key;
		
		}
		// Setup the payment details.
			$payment_data = array(
				'price'           => $finalamount,
				'give_form_title' => $product_name,
				'give_form_id'    => intval( $purchase_data['post_data']['give-form-id'] ),
				'date'            => $purchase_data['date'],
				'user_email'      => $purchase_data['post_data']['give_email'],
				'purchase_key'    => $purchase_data['purchase_key'],
				'currency'        => $curency,
				'user_info'       => $purchase_data['user_info'],
				'status'          => 'processing'
			);
			// record this payment.
		$payment_id = give_insert_payment( $payment_data );
	
		$args = array(
                'pub_key' => $pub_key  ,
				'amount' => $finalamount,
				'metadata' => array(
					'order_id' => $payment_id,
					'product_name' => $businesspay_title,
					'product_description' => $businesspay_description
				),
				'callback' => $businesspay_return_page_id ,
				'post_url' => $businesspay_post_page_id ,
				'order_image_url' => $businesspay_order_image_url ,
				'sharable' => false,
				'first_name' => $first_name,
				'last_name' => $last_name,
				'email' => $email,
				'phone_number' => $phone_number,
				 'currency' => $curency
			);

		$response = wp_remote_post('https://app.mybusinesspay.com/payment/v1.0/initialize', array(
				'method' => 'POST',
				'headers' => 
								array(
									'Content-Type' => 'application/json',
									'Accept' => 'application/json',
								//	'Authorization' => 'Bearer $pub_key',
									'x-widget' => 'true'),
					'sslverify' => false,
					'body' => json_encode($args)
			 		)
			  );
		
		if( !is_wp_error( $response ) )
		{
				$array_response = json_decode( $response['body'], true );				
				if(!empty($array_response) && $array_response['status']=="success" && $array_response['status_code']=="100")
				{
							
					header( "Location: ".$array_response['url'], true );
					exit;
				} else {
					
					if(isset($array_response['error']) && $array_response['error'] != ""){
					$error_message = $array_response['error'];
					} else {
						$error_message = "Somthing wrong in cURL";
					}
					header( "Location: ".$businesspay_post_page_id."?businesspay_error=".$error_message, true );
					exit;
				}
		}
	/**************************************************************************/
			//give_send_to_success_page(); // this function redirects and exits itself
	}

public function checkvalidate(){

        $msg= array();
        //Get records from database 
    	$businesspay_testmode = give_get_option('businesspay_testmode'); 
    	$businesspay_test_public_key= give_get_option('businesspay_test_public_key');
		$businesspay_public_key		= give_get_option('businesspay_public_key');
		$businesspay_return_page_id = give_get_option('businesspay_return_page_id');
		
    	if ($businesspay_testmode == "on")
		{
		    $pub_key = $businesspay_test_public_key;
		} else {
			$pub_key = $businesspay_public_key;
		}
		$transaction_uuid	= sanitize_text_field($_GET['transaction_uuid']);
		if(isset($transaction_uuid) && $transaction_uuid != "" )
		{
		        
       //Start verify transactions    	
	   $args = array(
				'headers' => array(
					'Authorization' => $pub_key
				)
			);
		$verifyurl = "https://app.mybusinesspay.com/payment/v1.0/verify?transaction_uuid=".$transaction_uuid;
		$response = wp_remote_get( $verifyurl, $args );

        $array_response = json_decode($response['body'], true);
        
        if(!empty($array_response) )
        {
           $order_id = $array_response['metadata']['order_id'];
           $status = $array_response['status'];
            $status_code = $array_response['status_code'];
           $message = $array_response['message'];

            if(isset($order_id) && $order_id != "")
            {                               
                give_update_meta( $order_id, '_give_businesspay_meta',$response );
                give_insert_payment_note($order_id,$response);
                
                // Update payment status as per response data
                 if (isset($status_code) && $status_code == "100") {
                    
                      $msg['message']   = $message;
		            give_set_payment_transaction_id($order_id, $transaction_uuid);
					give_insert_payment_note($order_id,$msg['message']);
					give_insert_payment_note($order_id,'transaction_uuid: '.$transaction_uuid);
                    give_update_payment_status( $order_id, 'publish' );
                    give_record_gateway_error($message);
                    
                 }
                else if(isset($status_code) && $status_code == "632")
                {
                    $msg['message']   = "Opps! The transaction has been Cancelled by you";
		            give_set_payment_transaction_id($order_id, $transaction_uuid);
					give_insert_payment_note($order_id,$msg['message']);
					give_insert_payment_note($order_id,'transaction_uuid: '.$transaction_uuid);
                    give_update_payment_status( $order_id, 'cancelled' );
                    give_record_gateway_error('Payment creation cancelled before sending buyer to SecurePay.');
                }
                elseif(isset($status_code) && $status_code == "0000"){
                 
                    $msg['message']   = $message;
		            give_set_payment_transaction_id($order_id, $transaction_uuid);
					give_insert_payment_note($order_id,$msg['message']);
					give_insert_payment_note($order_id,'transaction_uuid: '.$transaction_uuid);
                    give_record_gateway_error($message);
                }
                else 
                {
                    $msg['message']   = $message;
		            give_set_payment_transaction_id($order_id, $transaction_uuid);
					give_insert_payment_note($order_id,$msg['message']);
						give_insert_payment_note($order_id,'transaction_uuid: '.$transaction_uuid);
                    give_update_payment_status( $order_id, 'failed' );
                    give_record_gateway_error($message);
                }
            }   
        }						
	}else{
	    $msg['message']   = 'Connection error.';
	 
	}//Close if condition
	
	 header( "Location: ".site_url('/donation-thank-you?message='.$msg['message']), true );
     exit;  
}
}
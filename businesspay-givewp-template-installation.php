<?php
function businesspay_installer(){

	//create the thankyou page for businesspay gateway
	 $page = get_page_by_path( 'businesspay-thank-you', OBJECT );
     $page_thankyou = get_page_by_path( 'donation-thank-you', OBJECT );

     if ( !isset($page) ){
		$new_page_title = 'BusinessPay Thank you';
		$new_page_content = '';
		$new_page_template =  dirname( __FILE__ ) .'template/custompage-template.php'; 
		
		//don't change the code bellow, unless you know what you're doing
		$page_check = get_page_by_title($new_page_title);
		$new_page = array(
			'post_type' => 'page',
			'post_title' => $new_page_title,
			'post_content' => $new_page_content,
			'post_status' => 'publish',
			'post_author' => 1,
		);
		if(!isset($page_check->ID)){
			$new_page_id = wp_insert_post($new_page);
			if(!empty($new_page_template)){
				update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
			}
		}
	}
	
	if ( !isset($page_thankyou) ){
		$new_page_title_thankyou = 'Donation Thank you';
		$new_page_content_thankyou = '';
		$new_page_template_thankyou =  dirname( __FILE__ ) .'template/custom-thankyou-template.php'; 
		
		//don't change the code bellow, unless you know what you're doing
		$page_check_thankyou = get_page_by_title($new_page_title);
		$new_page_thankyou = array(
			'post_type' => 'page',
			'post_title' => $new_page_title_thankyou,
			'post_content' => $new_page_content_thankyou,
			'post_status' => 'publish',
			'post_author' => 1,
		);
		if(!isset($page_check->ID)){
			$new_page_id_thankyou = wp_insert_post($new_page_thankyou);
			if(!empty($new_page_template_thankyou)){
				update_post_meta($new_page_id_thankyou, '_wp_page_template', $new_page_template_thankyou);
			}
		}
	}

}

function wp_page_template_businesspay( $page_template )
{ 
    if ( is_page( 'businesspay-thank-you' ) ) {
        $page_template = plugin_dir_path( __FILE__ ) . 'template/custompage-template.php';
    }
     if ( is_page( 'donation-thank-you' ) ) {
        $page_template = plugin_dir_path( __FILE__ ) . 'template/custom-thankyou-template.php';
    }
    return $page_template;
}  
?>
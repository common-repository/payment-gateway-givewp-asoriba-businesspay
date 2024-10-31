<?php
/**
 * Template Name:Businesspay Gateway
 *
 */
ob_start();
get_header(); ?>
<?php 		
	$status_msg =(new Give_businesspay_php)->checkvalidate(); 
 ?>
<div id="mpcth_main" class="custom-thankyoupage">
    <div id="mpcth_main_container">
		<?php get_sidebar(); ?>
    <div id="mpcth_content_wrap">
			<div id="mpcth_content">
				<article id="page-<?php  echo __(get_the_ID(), 'give-businesspay' ); ?>" class="mpcth-page page type-page status-publish hentry" style="padding-top: 1.583em;">
			        
			             <header class="mpcth-page-header">
			                 <?php mpcth_breadcrumbs(); ?>
    			            <h1 class="mpcth-page-title mpcth-deco-header">
                			<span class="mpcth-color-main-border"><?php echo __(get_the_title(), 'give-businesspay' ); ?></span></h1>
			             </header>
			         <section class="mpcth-page-content">
						<p style="width: 100%;text-align: center;font-size: 20px;font-weight: 600;"> <?php   echo __($status_msg, 'give-businesspay' ); ?></p>
			        </section>
			    </article>
	        </div>
    </div>
     </div>
</div>

<?php
    get_footer();
?>
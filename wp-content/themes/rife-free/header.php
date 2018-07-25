<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="mid">
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

?><!DOCTYPE html>
<!--[if IE 9]>    <html class="no-js lt-ie10" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
<?php
    /* It is great place to add your Google Tag Manager code for example.
     * Below is example code that you can add in functions.php file(PHP > 5.3)
     *
     * ******* *
     *
add_action('apollo13framework_head_start', function(){
	echo <<<SCRIPT

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','XXX-YYYYYY');</script>
<!-- End Google Tag Manager -->

SCRIPT;
});
     *
     * ******* *
    */
    do_action('apollo13framework_head_start');
?>

<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php
    global $apollo13framework_a13;
	if( !is_user_logged_in() ){
	    apollo13framework_waiting_page();
	}

    wp_head();
?>
</head>

<body id="top" <?php body_class(); ?>>
<?php
    /* It is great place to add your Google Tag Manager code for example.
     * Below is example code that you can add in functions.php file(PHP > 5.3)
     *
     * ******* *
     *
add_action('apollo13framework_body_start', function(){
	echo <<<SCRIPT

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=XXX-YYYYYY"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

SCRIPT;
});
     *
     * ******* *
    */
    do_action('apollo13framework_body_start');
?>
<div class="whole-layout">
<?php
    apollo13framework_page_preloader();
    apollo13framework_page_background();
    if( ! apply_filters('apollo13framework_only_content', false) ) {
        apollo13framework_theme_header();
    }
    ?>
    <div id="mid" class="to-move <?php echo esc_attr( apollo13framework_get_mid_classes() ); ?>">
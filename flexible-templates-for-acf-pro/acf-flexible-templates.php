<?php
/**
 * @package Flexible_Templates
 * @version 0.1
 */
/*
Plugin Name: Flexible Templates for ACF PRO
Plugin URI: ttp://blueglass.ee/plugins/flexiblep-templates-for-acf
Description: Allows saving templates of the "Flexible Content" field, for easy and fast use of them on other pages.  
Author: Gleb Makarov
Version: 0.1
Author URI: http://blueglass.ee
*/


if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$acf_ft_version 	= '0.2';


register_activation_hook( __FILE__, 'acf_ft_install' );

//update_option( "acf_ft_db_installed", "no" );

function acf_ft_install(){
	global $wpdb;

	$acf_ft_table_name 	= $wpdb->prefix . 'flexible_templates_acf';

	if($wpdb->get_var( "show tables like '$acf_ft_table_name'" ) != $acf_ft_table_name) {

		$sql = "CREATE TABLE $acf_ft_table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			name text NOT NULL,
			template longblob NOT NULL,
			PRIMARY KEY (id)
		);";

		require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}



function acfft_add_template( $name, $template ) {
	global $wpdb;
	
	$acf_ft_table_name 	= $wpdb->prefix . 'flexible_templates_acf';
	
	if( !$name || !$template ) return false; 

	$wpdb->insert( 
		$acf_ft_table_name, 
		array( 
			'time' 		=> current_time( 'mysql' ), 
			'name' 		=> $name, 
			'template' 	=> $template, 
		) 
	);

	return true;
}

function acfft_remove_template( $name ) {
	global $wpdb;

	$acf_ft_table_name 	= $wpdb->prefix . 'flexible_templates_acf';
	
	if( !$name ) return false; 

	$wpdb->delete( $acf_ft_table_name, array( 'name' => $name ) );

	return true;
}



function acfft_get_templates(){
	global $wpdb;

	$acf_ft_table_name 	= $wpdb->prefix . "flexible_templates_acf";

	$row = $wpdb->get_results( "SELECT * FROM $acf_ft_table_name" );
	if( !empty($row) ) return $row; 

	return false;
}

function acfft_get_templates_by_name( $name ){
	global $wpdb;

	$acf_ft_table_name 	= $wpdb->prefix . "flexible_templates_acf";

	$row = $wpdb->get_results( "SELECT * FROM $acf_ft_table_name WHERE name = '$name'" );
	if( !empty($row) ) return $row; 

	return false;
}

function acfft_check_name( $name ){
	$row = acfft_get_templates_by_name( $name );

	if( !empty($row) ) return 'name_exists'; 
	return 'ok';
}


include_once('class-flexible-templates.php');
?>

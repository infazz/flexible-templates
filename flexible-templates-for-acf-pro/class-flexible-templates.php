<?php 


if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( ! class_exists('Flexible_Templates') ) :

	class Flexible_Templates{

		function __construct() {
			
			// admin
			if( is_admin() ) {

				add_action('init', array($this, 'add_actions_filters') );
				
			}

		}

		public function add_actions_filters(){
			add_action('admin_head', array($this, 'input_admin_enqueue_scripts') );

			add_filter("acf/get_field_label", array($this, 'filter_get_field_label'), 1000, 2);

			add_action('wp_ajax_ajax_template_save', array($this,'ajax_template_save') );
			add_action('wp_ajax_ajax_template_load', array($this,'ajax_template_load') );
			add_action('wp_ajax_ajax_template_remove', array($this,'ajax_template_remove') );
			
		}


		public function ajax_template_remove() {

			if( isset($_POST['name']) && $_POST['name'] != '' ){

				$name 		= strip_tags($_POST['name']);

				acfft_remove_template( $name );

				echo 'ok';
			}else{
				echo 'not ok';
			}
			die();
		}

		public function ajax_template_load() {

			if( isset($_POST['name']) && $_POST['name'] != '' ){

				$name 		= strip_tags($_POST['name']);

				$template 	= acfft_get_templates_by_name( $name );

				$template 	= $template[0]->template;

				echo $template;
			}else{
				echo 'not ok';
			}
			die();
		}

		public function ajax_template_save() {
			global $post;

			if( isset($_POST['name']) && $_POST['template'] != '' ){

				$name 		= strip_tags($_POST['name']);
				$template 	= strip_tags($_POST['template']);
				$pt 		= strip_tags($_POST['post_type']);

				$check 		= acfft_check_name( $name );

				if( $check === 'ok' ){
					acfft_add_template( $name, $template, $pt );

					echo $check;
				}

			}else{
				echo 'not ok';
			}
			die();
		}


	    public function input_admin_enqueue_scripts(){

	        $dir = plugin_dir_url(__FILE__);

	        // register & include JS
	        wp_register_script('acf-ft-scripts', "{$dir}js/script.js");
	        wp_enqueue_script('acf-ft-scripts');

	        $localized				= array();
	        $localized['ajaxurl'] 	= admin_url( 'admin-ajax.php' );
	        $localized['no_name']	= __('Please input template name.', 'acf-ft');
	        $localized['name_exists'] = __('Template with same name already exists.', 'acf-ft');
	        $localized['saved'] 	= __('Template saved.', 'acf-ft');
	        $localized['tpl_name']	= __("Template name", 'acf-ft');
	        $localized['tpl_save']	= __("Save this template", 'acf-ft');

	        wp_localize_script('acf-ft-scripts', 'acfft', $localized );

	        // register & include CSS
	        wp_register_style('acf-ft-styles', "{$dir}css/style.css");
	        wp_enqueue_style('acf-ft-styles');

	    }

		public function filter_get_field_label($label, $field){
			global $post;
			/*
			echo '<pre>';
			print_r( $post );
			echo '</pre>';
			*/

			if( $field['type'] == 'flexible_content' && $post->post_type != 'acf-field-group' ) {
				$label .= $this->get_templates_list();
			}

			return $label;
		}


		public function cmp($a, $b){
		    return strcmp($a->name, $b->name);
		}


		public function get_templates_list(){
			global $post;

			$post_type = $post->post_type;

			$templates = acfft_get_templates( $post_type );

			// old templates withour post_type
			$depricated_templates = acfft_get_templates( '' );

			$out = '<div class="acfft_flexible_templates button-primary">';
				$out .= '<div class="selected">'.__("Saved Template's", 'acf-ft').'</div>';

				if( $templates || $depricated_templates ){
					//sorting alphabetically
					usort($templates, array($this, 'cmp'));
					$i = 1;
					$out .= '<div class="acfft-dropdown" style="display: none;">';
						if($templates){
							$out .= '<div class="acfft-option acfft-separator new" data-value="'.$name.'">';
								$out .= '<span>Templates for: "'.$post_type.'"</span>';
							$out .= '</div>';
							foreach ($templates as $key => $tmp) {
								//print_r($tmp);
								$name = $tmp->name;

								$out .= '<div class="acfft-option" data-value="'.$name.'">';
									$out .= '<span class="acfft-select" data-value="'.$name.'">'.$i.' - '.$name.'</span>';
									$out .= '<span class="acfft-remove" data-value="'.$name.'">&nbsp;</span>';
								$out .= '</div>';
								$i++;
							}
						}
						if( $depricated_templates ){
							$out .= '<div class="acfft-option acfft-separator" data-value="'.$name.'">';
								$out .= '<span>Other templates</span>';
							$out .= '</div>';
							foreach ($depricated_templates as $key => $tmp) {
								//print_r($tmp);
								$name = $tmp->name;

								$out .= '<div class="acfft-option" data-value="'.$name.'">';
									$out .= '<span class="acfft-select" data-value="'.$name.'">'.$i.' - '.$name.'</span>';
									$out .= '<span class="acfft-remove" data-value="'.$name.'">&nbsp;</span>';
								$out .= '</div>';
								$i++;
							}
						}
					$out .= '</div>';
				}else{
					$out .= '<div class="acfft-dropdown">';
						$out .= '<div class="info">'.__("To save template, scroll to the bottom of your flexible layout, input name and hit save button.", "acf-ft").'</div>';
					$out .= '</div>';
				}

			$out .= '</div>';

			// $out = '<select class="acfft_flexible_templates" style="float: right; width: 200px;">';
			// 	$out .= '<option value="" selected>'.__('Select Template', 'acf-ft').'</option>';

			// 	if( $templates ){
			// 		foreach ($templates as $key => $tmp) {
			// 			//print_r($tmp);
			// 			$name = $tmp->name;

			// 			$out .= '<option value="'.$name.'">'.$name.'</option>';
			// 		}
			// 	}else{
			// 		$out .= '<option>'.__("No saved templates...", "acf-ft").'</option>';
			// 	}

			// $out .= '</select>';

			return $out;
		}

	}

	new Flexible_Templates();

endif;

?>
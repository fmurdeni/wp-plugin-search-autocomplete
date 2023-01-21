<?php
/**
 * Search autocomplete
 *
 * @package       SEARCHAUTO
 * @author        Feri Murdeni
 * @version       1.0.0
 *
 * @wordpress-plugin-murdeni
 * Plugin Name:   Search autocomplete
 * Plugin URI:    https://murdeni.com
 * Description:   Search Autocomplete by Murdeni
 * Version:       1.0.0
 * Author:        Feri Murdeni
 * Author URI:    https://murdeni.com
 * Text Domain:   murdeni
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;


class Murdeni_SearchAutoComplete{
    
    public $plugin_name = 'search-autocomplete';
    public $version = '1.0.0';

    function __construct(){
        add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts'), 100 );
        
        add_shortcode( 'search-form-autocomplete', array($this, 'search_form_shortcode' ) );
        // AJAX
		add_action('wp_ajax_sac_get_posts', array($this, 'ajax_get_posts') );
		add_action('wp_ajax_nopriv_sac_get_posts', array($this, 'ajax_get_posts') );
    }

    public function enqueue_scripts(){
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ). 'style.css', array(), $this->version );
		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ). 'app.js', array('jquery'), '1.0', true );
		wp_localize_script( $this->plugin_name, 'murdeni', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );		
	}

    public function search_form_shortcode(){
        ob_start();
        
        // Panggil script hanya ketika digunakan
        wp_enqueue_script($this->plugin_name);

        ?>
        <div class="text-search">
			<form id="murdeni_keyword_search">
				<div class="search-wrapper">
					<?php $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : ''; ?>
					<input type="search" name="q" placeholder="<?php echo __('Cari Post, Produk, dan Halaman', 'murdeni') ?>" value="<?php echo $keyword ?>" autocomplete="off">
					<input type="submit" value="Cari">
				</div>
			</form>				
		</div>
        <?php
        return ob_get_clean();
    }

    // Fungsi ajax untuk memanggil daftar Post, produk dan halaman
    public function ajax_get_posts(){
		$search_term = $_POST['s'];
		$args = array(
			'post_type' 	=> array('post', 'page', 'product'),
			'post_status'	=> 'publish',
			'numberposts' 	=> 10,
			's'			=> $_POST['s'],
			'orderby'	=> 'post_title',
			'order'		=> 'ASC'
		);
		
		$posts = get_posts($args);		
		if (!empty($posts)) {
			$html = '<ul class="murdeni-autocomplete active">';
			foreach ($posts as $key => $post) {
				$pattern = "/".$search_term."/i";
				$title = preg_replace($pattern, '<strong style="text-decoration: underline;">'.$search_term.'</strong>', $post->post_title);
				$html .= '<li><a href="'.get_the_permalink($post->ID).'"><span class="title">'.$title.'</span><span class="type">'.$post->post_type.'</span></a></li>';
			}
			$html .= '</ul>';
		}

		echo $html;
		wp_reset_postdata();

		die();
	}

}

new Murdeni_SearchAutoComplete;
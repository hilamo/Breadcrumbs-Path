<?php
/**
 * Plugin Name: Breadcrumbs Path
 * Plugin URI:
 * Description: This plugin create a simple breadcrumbs path.
 * Version: 1.0.0
 * Author: Hila Moalem
 * Author URI:
 * License:
 */
 add_shortcode('bc_path','breadcrumbs_path');
 function breadcrumbs_path($atts) {
     extract( shortcode_atts( array(
				'home_title'=>'Home',
                'seperator' => '&#92;'
			), $atts
		)
	);

    ob_start();
    $object = get_queried_object();

    $output     = '' ;
    $sep        = '<span class="crumb_sep">'.$seperator.'</span>' ;
    $home       = '<div class="crumb home"><a href="'.home_url().'">'.$home_title.'</a></div>';

    $output = '<div id="breadcrumbs_path" class="clearfix">';
        $output .= '<div class="crumbs clearfix">';
            $output .= $home;

    /** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/

            if(is_tax()){
                $tax_id = $object->term_id;
                $output .= $sep.'<div class="crumb last"><a href="'.get_term_link($object->taxonomy,$object).'">'.$tax_id->name.'</a></div>';
            }
            elseif(is_category()){
                $cat_id = $object->term_id;
                $output .= $sep.'<div class="crumb last"><a href="'.get_category_link($cat_id).'">'.$object->name.'</a></div>';
            }
            elseif(is_archive()){
                $post_type = $object->name;
                $name = $object->label;
                if(is_post_type_archive($post_type)){
                   $output .= $sep.'<div class="crumb last"><a href="'.get_post_type_archive_link($post_type).'">'.$name.'</a></div>';
                }
            }
            elseif(is_single()){
                $post_type = $object->post_type;
                $archive_title = '';
                if(is_singular($post_type)){
					if($post_type == 'post'){
					   $cats = get_the_terms($object->ID,'category');
                       if(!empty($cats)){
                            $cat = reset($cats);
                            $output .= $sep.'<div class="crumb"><a href="'.get_category_link($cat->term_id).'">'.$cat->name.'</a></div>';
                       }
					}else{
					    $archive_title = $post_type;
                        $output .= $sep.'<div class="crumb"><a href="'.get_post_type_archive_link($post_type).'">'.$archive_title.'</a></div>';
                    }
                    $output .= $sep.'<div class="crumb last"><a href="'.get_permalink($object->ID).'">'.get_the_title($object->ID).'</a></div>';
                }
            }
            elseif(is_page() && !is_home() && !is_front_page()){
				if($object->post_parent){
			        $parent = get_post($object->post_parent);
                    $output .= $sep.'<div class="crumb"><a href="'. get_the_permalink($parent->ID).'">'.get_the_title($parent->ID).'</a></div>';
                    $output .= $sep.'<div class="crumb last"><a href="'. get_the_permalink($object->ID).'">'.get_the_title($object->ID).'</a></div>';
                }
                else{
					$output .= $sep.'<div class="crumb last"><a href="'. get_the_permalink($object->ID).'">'.get_the_title($object->ID).'</a></div>';
				}
			}

        /** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/

        $output .= '</div>';
    $output .= '</div>';

    echo $output;
    return ob_get_clean();
 }// end of function


/**********************************************************************************************/
add_action( 'wp_enqueue_scripts', 'my_enqueued_assets' );
function my_enqueued_assets() {
    $plugin_url = plugins_url( '/', __FILE__ );
	wp_enqueue_style( 'bc-path-style', $plugin_url.'bc-path.css' );
    wp_enqueue_style('bc-path-script', $plugin_url . 'bc-path.js',array( 'jquery' ),'1.0.0',true);
}

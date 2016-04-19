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
                'sep' => '&#92;',
                'fontawsome' => ''
			), $atts
		)
	);

    ob_start();
    $object = get_queried_object();

    $output     = '' ;
    $font_awsome = '<i class="fa fa-'.$fontawsome.'"></i>';

    if($fontawsome){
        $sep = '<span class="crumb_sep">'.$font_awsome.'</span>' ;
    }else{
        $sep = '<span class="crumb_sep">'.$sep.'</span>' ;
    }
    $home       = '<div class="crumb home"><a href="'.home_url().'">'.$home_title.'</a></div>';

    $output = '<div id="breadcrumbs_path" class="clearfix">';
        $output .= '<div class="crumbs clearfix">';
            $output .= $home;

    /** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** ** **/

            if(is_tax() || is_tag()){
                $tax_id = $object->term_id;
                if($object->parent){
                    $term_parents = get_ancestors($object->term_id, $object->taxonomy);
                    if(!empty($term_parents)){
                        $reversed_ancestors = array_reverse($term_parents,false);
                        foreach ($reversed_ancestors as $parent_id) {
                            $parent = get_term_by('id',$parent_id,$object->taxonomy);
                            $output .= $sep.'<div class="crumb"><a href="'.get_term_link($parent, $parent->taxonomy).'">'.$parent->name.'</a></div>';
                        }
                    }
                }
                $output .= $sep.'<div class="crumb last"><a href="'.get_term_link($object, $object->taxonomy).'">'.$object->name.'</a></div>';
            }
            elseif(is_category()){
                $cat_id = $object->term_id;
                if($object->parent){
                    $term_parents = get_ancestors($object->term_id, $object->taxonomy);
                    if(!empty($term_parents)){
                        $reversed_ancestors = array_reverse($term_parents,false);
                        foreach ($reversed_ancestors as $parent_id) {
                            $parent = get_term_by('id',$parent_id,$object->taxonomy);
                            $output .= $sep.'<div class="crumb"><a href="'.get_term_link($parent, $parent->taxonomy).'">'.$parent->name.'</a></div>';
                        }
                    }
                }
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
                    $ancestors = get_ancestors($object->ID, 'page');
                    if(!empty($ancestors)){
                        $reversed_ancestors = array_reverse($ancestors,false);
                        foreach ($reversed_ancestors as $parent_id) {
                            $parent = get_post($parent_id);
                            $output .= $sep.'<div class="crumb"><a href="'. get_the_permalink($parent->ID).'">'.get_the_title($parent->ID).'</a></div>';
                        }
                        $output .= $sep.'<div class="crumb last"><a href="'. get_the_permalink($object->ID).'">'.get_the_title($object->ID).'</a></div>';
                    }
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
    wp_enqueue_style('fontawsome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css');
    wp_enqueue_style('bc-path-script', $plugin_url . 'bc-path.js',array( 'jquery' ),'1.0.0',true);
}


function get_custom_post_types(){
    $post_types_args = array(
       'public'   => true,
       '_builtin' => false
    );
    $output = 'objects'; // names or objects, note names is the default
    $operator = 'and'; // 'and' or 'or'

    $post_types = get_post_types( $post_types_args, $output, $operator );

    return $post_types;
}

function get_post_type_taxonomies(){

    $post_types = get_custom_post_types();
    if(!$post_types){
        return ;
    }

    $post_taxonomies = array();
    foreach ($post_types as $key => $type) {
        $post_taxonomies[$key]['taxonomies'] = $type->taxonomies;
    }
    return $post_taxonomies;
}

function get_taxonomy_post_type($term){

    if(!$term){ return;}

    $available_tax = get_post_type_taxonomies();
    //print_r($available_tax);
    foreach ($available_tax as $key => $atax) {
        // print_r($atax['taxonomies']);
        // //print_r($term);
        // if(in_array($term->taxonomy, $atax['taxonomies'])){
        //     echo '123';
        // }
    }
}

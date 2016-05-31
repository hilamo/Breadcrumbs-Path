<?php 
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
function get_archive_label_by_post_type($post_type){
    $object = get_post_type_object( $post_type );
    if($object){
        return $object->labels->name;
    }else {
        return $post_type;
    }
}

function get_archive_data_by_post_type($post_type , $data){
    $object = get_post_type_object( $post_type );
    if($object){
        return $object->$data;
    }else {
        return $post_type;
    }
}
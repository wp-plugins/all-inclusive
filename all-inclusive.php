<?php
/*
Plugin Name: All Inclusive
Plugin URI: http://ili.com.ua/wordpress/all-inclusive-en.html
Description: Two additional SQL query appends all metadata fields and pictures in the original sample of $ wp_query-> posts
Author: Stepanov Yuri (stur)
Version: 1.0.1
Author URI: http://ili.com.ua/wordpress/
*/
function all_inclusive($posts, $forcibly = 0){
   global $wpdb, $wp_query, $paged;
   if(!$forcibly){
       foreach ($posts as $key=>$value) {
            if( $wp_query->posts[$key] !== $value ){
                return  $posts;
            }
       }
       if($posts[0]->meta  OR (is_admin() or is_page()) ){
           remove_filter('posts_results', 'all_inclusive');
    	   return  $posts;
       }
   }
   $ar_post_id = array();
   foreach ($posts as $key => $post) {
        $ar_post_id[] = $post->ID;
        $ar_link[$post->ID] = & $posts[$key];
   }

   $st_id = @implode("','", $ar_post_id);
   //We obtain the first atachmenty
   $my_wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
   // коректируем  found_posts  запоминаем его в глоб переменую и возвращаем через фильтр found_posts
   global $all_found_rows;
   $all_found_rows = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
   add_filter('found_posts','all_found_rows');
   $query = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE  $wpdb->posts.post_parent IN('$st_id') AND $wpdb->posts.post_parent
   AND $wpdb->posts.post_type = 'attachment'";

   $my_wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
   $files = $my_wpdb->get_results($query);
   // obtain the list id atachmentov
   $ar_file_id = array();

   foreach ($files as $key=>$file) {
      $ar_file_id[] =  $file->ID;
      $ar_link[$file->ID] = & $files[$key];
   }

   // add up, we obtain a general list
   $ar_all_id = @array_merge($ar_post_id,$ar_file_id);


   $all_meta = update_meta_cache('post', $ar_all_id);
   if(!sizeof($all_meta))return $posts;
   // now clings meta Dane to posts
   $uploads_dir = wp_upload_dir('baseurl');
   foreach ($all_meta as $post_id=>$meta) {

       foreach($meta as $meta_key=>$value){
           $meta_value = maybe_unserialize($value[0]);

            if('_wp_attachment_metadata' == $meta_key AND sizeof($meta_value) ){

              $imgUrl = strstr ($meta_value['file'], '/wp-content');
              if(!$imgUrl){
                $imgUrl = $uploads_dir['baseurl'].'/'. $meta_value['file'];
              }
              $dirname = dirname($imgUrl);

              if($meta_value['sizes']){

                  $thumbUrl =  $dirname.'/'.$meta_value['sizes']['thumbnail']['file'];
                  if($meta_value['sizes']['medium'])
                    $mediumUrl =  $dirname.'/'.$meta_value['sizes']['medium']['file'];
                  else
                    $mediumUrl =  $imgUrl;
              }elseif($meta_value['thumb']){
                  $thumbUrl =  $dirname.'/'.$meta_value['thumb'];
                  $mediumUrl =  $dirname.'/'.$meta_value['thumb'];
              }else{
                 $mediumUrl = $thumbUrl =  $imgUrl;
              }

              $ar_link[$post_id]->thumbnail = $thumbUrl;
              $ar_link[$post_id]->medium = $mediumUrl;
              $ar_link[$post_id]->full = $imgUrl;
          }
          elseif('_wp_attached_file'==$row->meta_key){
          	  	$ar_link[$post_id]->attachment_url = $uploads_dir['baseurl'].'/'.$meta_value;
          }
          $ar_link[$post_id]->meta[$meta_key] = $meta_value;
          $meta_value = ''; $thumbUrl = ''; $mediumUrl = '';


       }


       //$meta_value = maybe_unserialize($row->meta_value);
      //'thumbnail' | 'medium' |

   }
   //  now clings atachmenty to posts

   usort ($files,'_sort_order');
   foreach ($files as $row) {
       if($row->menu_order == 0)
        $ar_link[$row->post_parent]->files[] = $row;
       else
        $ar_link[$row->post_parent]->files[$row->menu_order] = $row;
   }
   remove_filter('posts_results', 'all_inclusive');
   return $posts;
}

function all_found_rows ($found_rows){
    global $all_found_rows;
    remove_filter('found_posts', 'all_found_rows');
    return  $all_found_rows;
};

function _sort_order($a,$b){
   return ($a->menu_order < $b->menu_order) ? -1 : 1;
}


add_filter('posts_results', 'all_inclusive');

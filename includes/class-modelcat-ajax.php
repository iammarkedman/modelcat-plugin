<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class modelcat_ajax {
  /**
   * Setup
   */
  static function setup() {
    $action = 'getresults';
    add_action( 'wp_enqueue_scripts', __CLASS__ .'::enqueue_scripts' );
    add_action( 'wp_ajax_getresults', __CLASS__ .'::getresults' );
    add_action( 'wp_ajax_nopriv_getresultsname_of_action', __CLASS__ .'::getresults' );
  }

  /**
   * Enqueue scripts
   */
  static function enqueue_scripts() {
    // Register the script
    wp_register_script( 'modelcat', modelcat::get_plugin_url() .'assets/modelcat.js', array('jquery'), '1.0.0', true );

    // Localize the script with new data
    wp_localize_script( 'modelcat', 'modelcat', [
      'ajax_url' => admin_url( 'admin-ajax.php' ),
    ] );

    // Enqueued script with localized data.
    wp_enqueue_script( 'modelcat' );
  }

  /**
   * Return WP_Query metaquery parameters
   */
  static function form_metaqueries( $s ) {
    $metaquery = array();

    // ---------------------------------------------------------------------------
    // gender
    if( !empty( $s["gender"] ) && $s["gender"] != "all" ) {
      $values = array();
      if( $s["gender"] == "male" ) {
        array_push( $values, 1 );
      } else if( $s["gender"] == "female" ) {
        array_push( $values, 2 );
      }

      array_push( $metaquery, array(
        'key' => 'wpcf-gender',
        'value' => $values,
        'compare' => 'IN'
      ));
    }

    return $metaquery;
  }

  /**
   * AJAX query: search results
   */
  static function getresults() {
    global $post;
    global $dynamic_featured_image;

    $q = new WP_Query();

    $metaquery = modelcat_ajax::form_metaqueries($_POST);

    $q->query( array(
      'post_type' => 'model',
      'posts_per_page' => 1000,
      'order' => 'ASC',
      'orderby' => 'post_title',
      'meta_query' => array(
        'relation' => 'AND',
        $metaquery
      )
    ));

    $results = array();
    while( $q->have_posts() ) {
      $q->the_post();

      // meta info
      $meta = get_post_meta( $post->ID );
      $metainfo = array();
      foreach(array("gender", "date-of-birth") as $key) {
        if( !empty( $meta["wpcf-".$key] ) && is_array( $meta["wpcf-".$key] )) {
          switch( $key ) {
            case "date-of-birth":
              $t = $meta["wpcf-".$key][0];
              $metainfo["date-of-birth"] = strftime("%d.%m.%Y", $t);
              $metainfo["age"] = floor((time() - $t) / (60*60*24*365));
              break;
            default:
              $metainfo[$key] = $meta["wpcf-".$key][0];
          }
        } else {
          $metainfo[$key] = "";
        }
      }

      // photos
      $images = array();
      $mainthumb = "";
      $thumb_id = get_post_thumbnail_id( $post->ID );
      if( $thumb_id ) {
        $imgurl_thumb = wp_get_attachment_image_src( $thumb_id, "thumbnail" );
        $imgurl_full = wp_get_attachment_image_src( $thumb_id, "full" );
        $mainthumb = $imgurl_full[0];
        array_push( $images, array(
          "thumb" => $imgurl_thumb[0],
          "full" => $imgurl_full[0]
        ));
      }
      if( class_exists('Dynamic_Featured_Image') ) {
        $featured_images = $dynamic_featured_image->get_featured_images( $post->ID );
        foreach( $featured_images as $img ) {
          $i = array(
            "thumb" => $img["thumb"],
            "full" => $img["full"]
          );
          array_push( $images, $i );
        }
      }

      // hide surname
      $names = preg_split("/[\ ]+/", $post->post_title);
      if( count($names) < 2 ) {
        $name = $names[0];
      } else {
        $name = $names[0] . " " . substr($names[count($names)-1], 0, 1);
      }

      // add to results
      array_push( $results, array(
        "id" => $post->ID,
        "name" => $name,
        "info" => $metainfo,
        "images" => $images,
        "mainthumb" => $mainthumb,
        "permalink" => get_permalink( $post->ID )
      ));
    }

    $json = json_encode( $results );
    header('Content-Type: application/json');
    die( $json );
  }
}
?>

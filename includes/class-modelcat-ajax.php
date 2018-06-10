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
   * AJAX query: search results
   */
  static function getresults() {
    global $post;

    $q = new WP_Query();
    $q->query( array(
      'post_type' => 'model',
      'posts_per_page' => 1000
    ));

    $results = array();
    while( $q->have_posts() ) {
      $q->the_post();

      $meta = get_post_meta( $post->ID );
      $metainfo = array();
      foreach(array("gender", "date-of-birth") as $key) {
        if( !empty( $meta["wpcf-".$key] ) && is_array( $meta["wpcf-".$key] )) {
          $metainfo[$key] = $meta["wpcf-".$key][0];
        } else {
          $metainfo[$key] = "";
        }
      }

      array_push( $results, array(
        "id" => $post->ID,
        "name" => $post->post_title,
        "info" => $metainfo
      ));
    }

    $json = json_encode( $results );
    header('Content-Type: application/json');
    die( $json );
  }
}
?>

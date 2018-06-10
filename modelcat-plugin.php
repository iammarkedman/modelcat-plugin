<?php
/*
 * Plugin Name: Modelcat
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class modelcat {

  /**
   * Setup
   */
  static function setup() {
    self::_include( 'modelcat-ajax' );
    modelcat_ajax::setup();
  }

  /**
   * Include
   * Helper method to load class files
   * @param  string $class
   */
  static function _include( $class ) {
    require_once __DIR__ ."/includes/class-{$class}.php";
  }

  /**
   * Get plugin URL
   * @param  string $path
   * @return string
   */
  static function get_plugin_url( $path = '' ) {
    return plugin_dir_url( __FILE__ ) . $path;
  }
}

modelcat::setup();
?>

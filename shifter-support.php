<?php
/*
Plugin Name: Shifter Support
Author: DigitalCube
Author URI: https://www.getshifter.io
Description: Support plugin for Shifter sites
*/

/*
 * Shifter WP API
 * Check if Shifter WordPress Plugin API Plugin Exists
 */

if (!class_exists('Shifter_API')) {
  return;
}

$shifter = array();

/**
 * PHP to Console Debug Helper
 */
function debugToConsole($var) {
  echo "<script>console.log(".json_encode($var).")</script>";
}

/**
 * Create WP SLS Dir
 */
function create_shifter_config_dir() {
  
  $upload_dir =  WP_CONTENT_DIR;
  $save_path = $upload_dir . '/.shifter/.';
  $dirname = dirname($save_path);

  if (!is_dir($dirname)) {
      mkdir($dirname, 0755, true);
  }
}


/**
 * Shifter Site Settings
 */
function shifter_site_settings() {
  $api = new Shifter_API;

  $shifter_site_settings = array(
    // Todo: Get ENV Vars from Shifter Config
    "Project Name"=> "example project name",
    "Project ID"=> "abc-a310-11e8-8c25-123",
    "CloudFront"=> "123.cloudfront.net"
  );

  return $shifter_site_settings;

}

/**
 * Create Theme Array
 */
function create_theme_array() {

  $themes = wp_get_themes();

  foreach ($themes as $theme) {

    if (wp_get_theme()->get('Name') === $theme->get('Name')) {
      $active = true;
    } else {
      $active = false;
    }
    
    $theme_data[] = array(
      'Name' => $theme->get('Name'),
      'Version' => $theme->get('Version'),
      'Active' => $active
    );
  }

  $themes_array = array(
    "Themes" => $theme_data
  );
  
  return $themes_array;
}

/**
 * Create Plugin Array
 */
function create_plugin_array() {

  if ( ! function_exists( 'get_plugins' ) ) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }
  
  $plugins = get_plugins();

  foreach ($plugins as $plugin) {
    $plugin_data[] = $plugin;
  }

  $plugins_array = array(
    "Plugins" => $plugin_data
  );

  return $plugins_array;
}

/**
 * Create Shifter Config
 */
function create_shifter_config() {

  $config = array_merge(
    shifter_site_settings(),
    create_theme_array(),
    create_plugin_array()
  );

  ob_start();
  echo json_encode($config);
  $config = ob_get_clean();

  $save_path = WP_CONTENT_DIR . '/.shifter/config.json';

  file_put_contents($save_path, $config);

}

create_shifter_config_dir();
create_shifter_config();
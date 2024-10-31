<?php
/**
* Plugin Name: ReAim Web Push
* Description: ReAim web push notifications for WordPress 
* Version: 1.0.3
* Author: ReAim
* Author URI: https://reaim.me
*/

require_once plugin_dir_path(__FILE__).'settings.php';

define('REAIM_PUSH_API_KEY', get_option('reaim_push_api_key'));
define('REAIM_PUSH_CONFIG_SCRIPT', get_option('reaim_push_config_script'));
define('REAIM_PLUGIN_URL', plugin_dir_url(__FILE__));

if (!empty(REAIM_PUSH_API_KEY) && empty(!REAIM_PUSH_API_KEY)) {
  add_option('reaim_push_config_script', 'var push = new ReAimSDK(function() {     
    // executed on Allow
    }, function() {
    // executed on Block
    });
    push.init();
    ');
}

add_action('admin_menu', 'reaim_push_plugin_setup_menu');
add_action('admin_notices', 'reaim_push_is_active');
add_action('admin_enqueue_scripts', 'reaim_push_load_style');
add_action('admin_head', 'reaim_push_code_mirror');
add_action('wp_enqueue_scripts', 'reaim_push_install_script');
add_action('wp_footer', 'reaim_push_script');

function reaim_push_install_script() {
  wp_enqueue_script('reaim_push_install_script',  'https://cdn.reaim.me/js/install.min.js' , '', '', false);
}

function reaim_push_load_style() {
  wp_enqueue_style('reaim_push_settings_style', REAIM_PLUGIN_URL . 'style/settings/style.css' , '', '', false);
}

function reaim_push_code_mirror() {
  wp_enqueue_script('reaim_push_code_mirror_script',  'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.55.0/codemirror.min.js' , '', '', false);
  wp_enqueue_script('reaim_push_code_mirror_script_2',  'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.55.0/mode/javascript/javascript.min.js' , '', '', false);
  wp_enqueue_script('reaim_push_code_mirror_script_3',  REAIM_PLUGIN_URL . 'scripts/editor/editor.js' , '', '', false);
  wp_enqueue_style('reaim_push_code_mirror_style', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.55.0/codemirror.min.css' , '', '', false);
  wp_enqueue_style('reaim_push_code_mirror_style_theme', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.55.0/theme/dracula.min.css' , '', '', false);
}

// Checks if users inserted ReAim API key and shows/hide notification in the admin panel
function reaim_push_is_active () {
  if(!REAIM_PUSH_API_KEY || strlen(REAIM_PUSH_API_KEY) === 0) {
    echo '
      <div class="updated">
        <p>
          <strong>ReAim Push:</strong> In order to use ReAim Push you need to add API key. Update <a href="' . admin_url('admin.php?page=reaim-plugin') . '">' . __('settings', 'pushalert') . '</a> now!
        </p>
      </div>
    ';
  }
}

// Registers ReAim Settings page in WordPress side menu
function reaim_push_plugin_setup_menu(){
  add_menu_page( 'ReAim settings page', 'ReAim Settings', 'manage_options', 'reaim-plugin', 'reaim_push_dashboard', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTQiIGhlaWdodD0iNTQiIHZpZXdCb3g9IjAgMCA1NCA1NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggb3BhY2l0eT0iMC4xIiBkPSJNMjcgNTNDNDEuMzU5NCA1MyA1MyA0MS4zNTk0IDUzIDI3QzUzIDEyLjY0MDYgNDEuMzU5NCAxIDI3IDFDMTIuNjQwNiAxIDEuMDAwMDEgMTIuNjQwNiAxLjAwMDAxIDI3QzEuMDAwMDEgNDEuMzU5NCAxMi42NDA2IDUzIDI3IDUzWiIgc3Ryb2tlPSIjMDIwRTE3IiBzdHJva2Utd2lkdGg9IjIiLz4KPHBhdGggb3BhY2l0eT0iMC4xNSIgZD0iTTI0IDQ2QzMyLjI4NDMgNDYgMzkgMzkuMjg0MyAzOSAzMUMzOSAyMi43MTU3IDMyLjI4NDMgMTYgMjQgMTZDMTUuNzE1NyAxNiA5LjAwMDAxIDIyLjcxNTcgOS4wMDAwMSAzMUM5LjAwMDAxIDM5LjI4NDMgMTUuNzE1NyA0NiAyNCA0NloiIHN0cm9rZT0iIzAyMEUxNyIgc3Ryb2tlLXdpZHRoPSIyIi8+CjxwYXRoIGQ9Ik0yNC41IDM4LjA0OEMyOC42Njg3IDM4LjA0OCAzMi4wNDggMzQuNjY4NiAzMi4wNDggMzAuNUMzMi4wNDggMjYuMzMxNCAyOC42Njg3IDIyLjk1MiAyNC41IDIyLjk1MkMyMC4zMzE0IDIyLjk1MiAxNi45NTIgMjYuMzMxNCAxNi45NTIgMzAuNUMxNi45NTIgMzQuNjY4NiAyMC4zMzE0IDM4LjA0OCAyNC41IDM4LjA0OFoiIGZpbGw9IiNERTMxMzkiIHN0cm9rZT0iI0Y0RjVGNyIgc3Ryb2tlLXdpZHRoPSI0LjA5NiIvPgo8cGF0aCBkPSJNMjcuNSAzMEMyOS40MzMgMzAgMzEgMjguNDMzIDMxIDI2LjVDMzEgMjQuNTY3IDI5LjQzMyAyMyAyNy41IDIzQzI1LjU2NyAyMyAyNCAyNC41NjcgMjQgMjYuNUMyNCAyOC40MzMgMjUuNTY3IDMwIDI3LjUgMzBaIiBzdHJva2U9IiMwMjBFMTciIHN0cm9rZS13aWR0aD0iMiIvPgo8L3N2Zz4K');
}



// Inserts scripts into page if Reaim API key exists 
function reaim_push_script () {
  $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $reaim_settings_api_include = get_option('reaim_settings_include_exclude_include');
  $reaim_settings_api_exclude = get_option('reaim_settings_include_exclude_exclude');

  $reaim_settings_api_include_filtered = array_filter($reaim_settings_api_include);
  $reaim_settings_api_exclude_filtered = array_filter($reaim_settings_api_exclude);


  if(get_option('reaim_push_active_urls') == 'include') {
    if(!empty($reaim_settings_api_include_filtered)) {
      if(REAIM_PUSH_API_KEY) {
        for ($i = 0; $i < count($reaim_settings_api_include_filtered); $i++) {
          if(filter_var($reaim_settings_api_include_filtered[$i], FILTER_VALIDATE_URL)) {
            if($url == $reaim_settings_api_include_filtered[$i]) {
              ?>
                <script id="reaim-sw-script">
                  window.REAIM_SW_PATH_GLOBAL = "<?php echo REAIM_PLUGIN_URL.'sw/sw.js.php';?>"
                </script>
                <script id="reaim-sw-config-script">
                  <?php echo REAIM_PUSH_CONFIG_SCRIPT;?>
                </script>
              <?php
            } else {
              if(preg_match($reaim_settings_api_include_filtered[$i], $url)) {
                ?>
                  <script id="reaim-sw-script">
                    window.REAIM_SW_PATH_GLOBAL = "<?php echo REAIM_PLUGIN_URL.'sw/sw.js.php';?>"
                  </script>
                  <script id="reaim-sw-config-script">
                    <?php echo REAIM_PUSH_CONFIG_SCRIPT;?>
                  </script>
                <?php
              }
            }
          }
        }
      }
    } else {
      if(empty($reaim_settings_api_include_filtered) && empty($reaim_settings_api_exclude_filtered)) {
        if(REAIM_PUSH_API_KEY) {
          ?>
            <script id="reaim-sw-script">
              window.REAIM_SW_PATH_GLOBAL = "<?php echo REAIM_PLUGIN_URL.'sw/sw.js.php';?>"
            </script>
            <script id="reaim-sw-config-script">
              <?php echo REAIM_PUSH_CONFIG_SCRIPT;?>
            </script>
          <?php
        }
      }
    }
  } else if(get_option('reaim_push_active_urls') == 'exclude') {
    if(!empty($reaim_settings_api_exclude_filtered)) {
      if(REAIM_PUSH_API_KEY) {
        for ($i = 0; $i < count($reaim_settings_api_exclude_filtered); $i++) {
          if(filter_var($reaim_settings_api_exclude_filtered[$i], FILTER_VALIDATE_URL)) {
            if ($url == $reaim_settings_api_exclude_filtered[$i]) {
            } else {
              ?>
                <script id="reaim-sw-script">
                  window.REAIM_SW_PATH_GLOBAL = "<?php echo REAIM_PLUGIN_URL.'sw/sw.js.php';?>"
                </script>
                <script id="reaim-sw-config-script">
                  <?php echo REAIM_PUSH_CONFIG_SCRIPT;?>
                </script>
              <?php
            }
          } else {
            if(preg_match($reaim_settings_api_exclude_filtered[$i], $url)) {
            } else {
              ?>
                <script id="reaim-sw-script">
                  window.REAIM_SW_PATH_GLOBAL = "<?php echo REAIM_PLUGIN_URL.'sw/sw.js.php';?>"
                </script>
                <script id="reaim-sw-config-script">
                  <?php echo REAIM_PUSH_CONFIG_SCRIPT;?>
                </script>
              <?php
            }
          }
        }
      }
    } else {
      if(empty($reaim_settings_api_include_filtered) && empty($reaim_settings_api_exclude_filtered)) {
        if(REAIM_PUSH_API_KEY) {
          ?>
            <script id="reaim-sw-script">
              window.REAIM_SW_PATH_GLOBAL = "<?php echo REAIM_PLUGIN_URL.'sw/sw.js.php';?>"
            </script>
            <script id="reaim-sw-config-script">
              <?php echo REAIM_PUSH_CONFIG_SCRIPT;?>
            </script>
          <?php
        }
      }
    }
  } else {
    if(empty($reaim_settings_api_include_filtered) && empty($reaim_settings_api_exclude_filtered)) {
      if(REAIM_PUSH_API_KEY) {
        ?>
          <script id="reaim-sw-script">
            window.REAIM_SW_PATH_GLOBAL = "<?php echo REAIM_PLUGIN_URL.'sw/sw.js.php';?>"
          </script>
          <script id="reaim-sw-config-script">
            <?php echo REAIM_PUSH_CONFIG_SCRIPT;?>
          </script>
        <?php
      }
    }
  }
}
?>
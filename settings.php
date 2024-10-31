<?php
// ReAim Settings page UI
function reaim_push_dashboard() {
  ?>
  <script>
    async function checkApi() {
      
      var data = document.getElementById('reaim_push_api_key')
      const isValid = await fetch(
        `https://api.reaim.me/api/v1/site/data/is_verified?token=${data.value}`
      );
      document.cookie = "ream_push_valid_token=" + isValid.status;
    }

    function getCookieValue(a) {
      var b = document.cookie.match('(^|;)\\s*' + a + '\\s*=\\s*([^;]+)');
      return b ? b.pop() : '';
    }

    function changeIncludeExcludeUrl(type) {
      if(window.location.search.includes('include')) {
        window.location = window.location.href.replace('include', 'exclude')
      } else if(window.location.search.includes('exclude')) {
        window.location = window.location.href.replace('exclude', 'include')
      } else {
        window.location.href += '&' + type;
      }
    }
  </script>
    <img class="reaim_logo" src="<?php echo esc_url(REAIM_PLUGIN_URL."images/reaim.png") ?>" alt="">
    <div class="reaim_settings">
        <h3 class="title">ReAim Settings</h3>
        <p class="second_title">Follow steps below to add web push to your Wordpress Website</p>
        <ol>
        <li>Create your account on <a href="//reaim.me" target="_blank">reaim.me</a></li>
        <li>Obtain your API Key from Profile Settings page</li>
        <li>Paste your API Key bellow and click `Save`</li>
        </ol>
    </div>

    <form class="reaim_settings_api_form" action="<?=$_SERVER['PHP_SELF'];?>" method="POST">
      <p>In order to verify your website you need to enter API which you can obtain on ReAim platform under Profile settings.</p>
      <div class="reaim_push_api_key">
        <label for="reaim_push_api_key">API Key</label>
        <div>
        <input type="text" id="reaim_push_api_key" name="reaim_push_api_key" placeholder="XXXXXXXXXXXXXXXXXXXXXX" value="<?php echo REAIM_PUSH_API_KEY; ?>" onfocusout="checkApi()" required>
        <p className="invalid_key_message"><script>document.write(decodeURIComponent(getCookieValue('reaim_push_token_response')).replace("+", " ").replace("+", " "))</script></p></div>
      </div>
      <button type="submit">Save</button>
    </form>
    <details class="reaim_push_include_exclude_urls">
      <summary>
        <p>
          In this section you can specify pages where you want to show or hide push notifications.
          <br>
          You can also use RegEx to match specific URL patterns
        </p>
      </summary>
      <div>
        <p>First you need to select if you want to Include or Exclude specific URLs on your website.
          <br>
          If you choose <em>Include URLs</em> notifications will be shown only on those pages.
          <br>
          If you choose <em>Exclude URLs</em> notifications will not be shown on pages that you entered.
        </p>
        <form name="reaim_settings_include_exclude_include_form" id="reaim_settings_include_exclude_include_form" action="<?=$_SERVER['PHP_SELF'];?>" method="POST">
          <div class="type">
            <?php if(isset($_GET['include'])) {
              $reaim_push_selected_radio_include = "checked='checked'";
              $reaim_push_selected_radio_exclude = '';
            } else if(isset($_GET['exclude'])) {
              $reaim_push_selected_radio_exclude = "checked='checked'";
              $reaim_push_selected_radio_include = '';
            } else {
              if(get_option('reaim_push_active_urls') == 'exclude') {
                $reaim_push_selected_radio_exclude = "checked='checked'";
                $reaim_push_selected_radio_include = '';
                ?>
                  <script>
                    window.location.href += '&exclude';
                  </script>
                <?php
              } else {
                $reaim_push_selected_radio_include = "checked='checked'";
                $reaim_push_selected_radio_exclude = '';
                ?>
                  <script>
                    window.location.href += '&include';
                  </script>
                <?php
              }
            }
            ?>
            <label for="include">Include URLs</label>
            <input type="radio" id="include" name="reaim_push_url_type" value="include" <?php echo $reaim_push_selected_radio_include ?> onclick="changeIncludeExcludeUrl('include')">
            <br>
            <label for="exclude">Exclude URLs</label>
            <input type="radio" id="exclude" name="reaim_push_url_type" value="exclude" <?php echo $reaim_push_selected_radio_exclude ?> onclick="changeIncludeExcludeUrl('exclude')">
          </div>
          <div class="responsive">
            <div id="reaim_push_include_fields">
              <?php 
              if(isset($_GET['include'])) {
                $items = get_option('reaim_settings_include_exclude_include');
              } else if(isset($_GET['exclude'])) {
                $items = get_option('reaim_settings_include_exclude_exclude');
              }
              for($i = 0; $i < count($items); $i++){
                if($i == 0) {
                  ?>
                    <div class="reaim_push_include_input_button">
                      <input id="reaim_push_include_remove<?php echo $items[$i] ?>_input" type="text" name="reaim_settings_include_exclude_include[]" placeholder="Enter URL" class="form-control name_list" value="<?php echo $items[$i]; ?>"/>
                    </div>
                  <?php
                } else {
                  ?>
                    <div class="reaim_push_include_input_button">
                      <input id="reaim_push_include_remove<?php echo $items[$i] ?>_input" type="text" name="reaim_settings_include_exclude_include[]" placeholder="Enter URL" class="form-control name_list" value="<?php echo $items[$i]; ?>"/>
                      <button type="button" name="remove" id="reaim_push_include_remove<?php echo $items[$i] ?>" class="btn btn-danger btn_remove">X</button>
                    </div>
                  <?php
                }
                  ?>
              <?php
              }
              ?>
            </div>
            <button type="button" name="add_include" id="add_include" class="btn btn-success">Add More</button>
            <button type="submit" name="submit" id="submit_include" class="btn btn-info" value="Submit">Save</button>
          </div>
        </form>
      </div>
    </details>
    <div class="reaim_push_config_script_section">
      <button type="button" class="reaim_push_dropdown_collapsible"><p>In this section you can write your own configuration for ReAim Push using our SDK</p></button>
      <div class="reaim_push_dropdown_content">
        <form class="reaim_push_config_script" action="<?=$_SERVER['PHP_SELF'];?>" method="POST">
          <div class="editor-holder">
            <div class="scroller">
              <textarea id="js_editor" name="reaim_push_config_script">
                <?php echo REAIM_PUSH_CONFIG_SCRIPT; ?>  
              </textarea>
            </div>
            <button type="submit">Save</button>
          </div>
        </form>
      </div>
    </div>
<script>
  jQuery(document).ready(function(){
    var i=1;
    jQuery('#add_include').click(function(){
      i++;
      jQuery('#reaim_push_include_fields').append('<div class="reaim_push_include_input_button"><input id="reaim_push_include_remove'+i+'_input" type="text" name="reaim_settings_include_exclude_include[]" placeholder="Enter URL" class="form-control name_list" /><button type="button" name="remove" id="reaim_push_include_remove'+i+'" class="btn btn-danger btn_remove">X</button></div>');
    });
    jQuery(document).on('click', '.btn_remove', function(){
      var button_id = jQuery(this).attr("id");
      jQuery('#'+button_id+'_input').remove();
      jQuery('#'+button_id).remove();
    });
  });

  var coll = document.getElementsByClassName("reaim_push_dropdown_collapsible");
  var i;

  for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function() {
      this.classList.toggle("reaim_push_dropdown_active");
      var content = this.nextElementSibling;
      if (content.offsetHeight === 419) {
        content.style.height = 0;
      } else {
        content.style.height = '419px';
      }
    });
  }
</script>
  <?php
}

if (isset($_POST['reaim_push_api_key'])) {
  $reaim_push_api_key = sanitize_text_field( $_POST['reaim_push_api_key'] );

  $cookie = $_COOKIE['ream_push_valid_token'];

  if($cookie == 200) {
    if (!empty(get_option('reaim_push_api_key')) && !empty(get_option('reaim_push_api_key'))) {
      update_option('reaim_push_api_key', $reaim_push_api_key);
    } else {
      add_option('reaim_push_api_key', $reaim_push_api_key);
    }
    setcookie("reaim_push_token_response", '');
  } else {
    setcookie("reaim_push_token_response", 'Invalid api key');
  }
  header('Location: '.$_SERVER['HTTP_REFERER']);
}

if(isset($_POST['reaim_push_config_script'])) {
  $reaim_push_config_script = $_POST['reaim_push_config_script'];

  if (!empty(REAIM_PUSH_API_KEY) && !empty(REAIM_PUSH_API_KEY)) {
    update_option('reaim_push_config_script', $reaim_push_config_script);
  }
  header('Location: '.$_SERVER['HTTP_REFERER']);
}

if(isset($_POST['reaim_settings_include_exclude_include'])) {
  if($_POST['reaim_push_url_type'] == 'include') {

    $reaim_settings_include_exclude_include = $_POST['reaim_settings_include_exclude_include'];
    delete_option('reaim_settings_include_exclude_include');

    if (!empty(get_option('reaim_push_active_urls')) && !empty(get_option('reaim_push_active_urls')) && !empty(REAIM_PUSH_API_KEY)) {
      update_option('reaim_push_active_urls', 'include');
    } else {
      add_option('reaim_push_active_urls', 'include');
    }
  
    if (!empty(get_option('reaim_settings_include_exclude_include')) && !empty(get_option('reaim_settings_include_exclude_include')) && !empty(REAIM_PUSH_API_KEY)) {
      update_option('reaim_settings_include_exclude_include', $reaim_settings_include_exclude_include);
    } else {
      add_option('reaim_settings_include_exclude_include', $reaim_settings_include_exclude_include);
    }

    header('Location: '.$_SERVER['HTTP_REFERER']);
  } else {
    $reaim_settings_include_exclude_exclude = $_POST['reaim_settings_include_exclude_include'];
    delete_option('reaim_settings_include_exclude_exclude');

    if (!empty(get_option('reaim_push_active_urls')) && !empty(get_option('reaim_push_active_urls')) && !empty(REAIM_PUSH_API_KEY)) {
      update_option('reaim_push_active_urls', 'exclude');
    } else {
      add_option('reaim_push_active_urls', 'exclude');
    }

    if (!empty(get_option('reaim_settings_include_exclude_include')) && !empty(get_option('reaim_settings_include_exclude_include')) && !empty(REAIM_PUSH_API_KEY)) {
      update_option('reaim_settings_include_exclude_exclude', $reaim_settings_include_exclude_exclude);
    } else {
      add_option('reaim_settings_include_exclude_exclude', $reaim_settings_include_exclude_exclude);
    }

    header('Location: '.$_SERVER['HTTP_REFERER']);
  }
} 
?>
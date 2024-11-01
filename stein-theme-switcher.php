<?php
/*
  Plugin Name: Stein Mobile Switcher
  Version: 0.8.4
  Author URI: http://www.steinm.com
  Plugin URI: http://www.steinm.com/blog/stein-mobile-switcher/
  Description:  Switch themes by Device (Smartphone / Tablet).
  Author: Matthias Stein
  License: GPLv2
  Text Domain: stein-mobile-switcher

  Copyright 2012  Matthias Stein  (email : info@steinm.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


add_filter('template', 'switch_theme_by_device_or_url');
add_filter('stylesheet', 'switch_theme_by_device_or_url');

function switch_theme_by_device_or_url($theme) {
    # Start Session
    if (!session_id()) {
        @session_start();
    }

    if ($_GET['sms_device'] != '') {
        $_SESSION['sms_device'] = $_GET['sms_device'];
    }

    if ($_SESSION['sms_device'] != '') {
        if (!isset($_SESSION['sms_entrydevice'])) {
            $_SESSION['sms_entrydevice'] = $_SESSION['sms_device'];
        }
        if ($_SESSION['sms_device'] == 'screen' || $_SESSION['sms_device'] == 'full') {
            return strtolower(get_current_theme()); 
            # wp_get_theme()
        }
        if ($_SESSION['sms_device'] == 'tablet') {
            return get_option('tablettheme');
        }
        if ($_SESSION['sms_device'] == 'mobile') {
            return get_option('mobiletheme');
        }
		 if ($_SESSION['sms_device'] == 'iphone') {
            return get_option('iphonetheme');
        }
    }

    # Using The MobileESP Project: http://blog.mobileesp.com/
    include_once('mdetect.php');

    $uainfo = new uagent_info;

    # Detect Tablets
    if ($uainfo->DetectTierTablet()) {
        $theme = get_option('tablettheme');
        $_SESSION['sms_device'] = 'tablet';
        $_SESSION['sms_entrydevice'] = 'tablet';
    }
    # Detect iPhone/iPod
    else if ($uainfo->DetectIphoneOrIpod()) {
        $theme = get_option('iphonetheme');
        $_SESSION['sms_device'] = 'iphone';
        $_SESSION['sms_entrydevice'] = 'iphone';
    } 
	# Detect Smartphones
	else if ($uainfo->DetectTierIphone() || $uainfo->DetectBlackBerry() || $uainfo->DetectTierRichCss() || $uainfo->DetectBlackBerryLow() || $uainfo->DetectTierOtherPhones()) {
        $theme = get_option('mobiletheme');
        $_SESSION['sms_device'] = 'mobile';
        $_SESSION['sms_entrydevice'] = 'mobile';
    }

    return $theme;
}

# Create Menu and Options
if (is_admin()) {
    add_action('admin_init', 'register_smssettings');
    add_action('admin_menu', 'sms_plugin_menu');
}

function sms_plugin_menu() {
    add_submenu_page('themes.php', 'Switch Themes by URL and DEVICE', 'Stein Mobile Switcher', 'manage_options', 'stein-mobile-switcher', 'sms_plugin_options');
}

function sms_plugin_options() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.', 'stein-mobile-switcher'));
    }
    echo '<div class="wrap">';

    if ($_GET['settings-updated']) {
        ?>
        <div id="setting-error-settings_updated" class="updated settings-error"> 
            <p><strong>Settings saved.</strong></p></div>
        <?php
    }

    echo '<h2>Stein Mobile Switcher</h2>';
    echo '<p>' . __('This is the Stein Mobile Switcher. Select Templates for each device!', 'stein-mobile-switcher') . '</p>';

    echo '<form method="post" action="options.php">';
    settings_fields('stein-mobile-switcher-group');

    # Get available themes ... 
    if (function_exists('wp_get_themes')) {
        foreach (wp_get_themes() as $theme) :
            $themes[] = $theme->template;
        endforeach;
    } else {
        $themes = array_keys(get_themes());
    }
    # ... and sort them
    natsort($themes);
    ?>

    <table class="form-table">
	
		<tr valign="top">
            <th scope="row"><?php _e('iPhone / iPod Theme', 'stein-mobile-switcher') ?></th>
            <td>
                <select name="iphonetheme">
                    <?php foreach ($themes as $k => $theme) { ?>
                        <option value="<?php echo $theme ?>" <?php selected($theme, get_option('iphonetheme')) ?>><?php echo $theme ?> &nbsp; </option>
                    <?php } ?>
                </select></td>
            <td><i><?php _e('Theme for iPhone- and iPod-Users', 'stein-mobile-switcher') ?> </i></td>
        </tr>
		
        <tr valign="top">
            <th scope="row"><?php _e('Smartphone Theme', 'stein-mobile-switcher') ?></th>
            <td>
                <select name="mobiletheme">
                    <?php foreach ($themes as $k => $theme) { ?>
                        <option value="<?php echo $theme ?>" <?php selected($theme, get_option('mobiletheme')) ?>><?php echo $theme ?> &nbsp; </option>
                    <?php } ?>
                </select>
            </td>
            <td><i><?php _e('Theme for Smartphone-Users (Android phones, multi-media MP3-Players, Windows Phone 7, etc.) without iPhone- and iPod', 'stein-mobile-switcher') ?></i></td>
        </tr>

        <tr valign="top">
            <th scope="row"><?php _e('Tablet Theme', 'stein-mobile-switcher') ?></th>
            <td>
                <select name="tablettheme">
                    <?php foreach ($themes as $k => $theme) { ?>
                        <option value="<?php echo $theme ?>" <?php selected($theme, get_option('tablettheme')) ?>><?php echo $theme ?> &nbsp; </option>
                    <?php } ?>
                </select></td>
            <td><i><?php _e('Theme for Tablet-Users', 'stein-mobile-switcher') ?></i></td>
        </tr>
    </table>

    <?php
    submit_button();
    echo '</form>';
    echo '</div>';
}

function register_smssettings() { // whitelist options
    load_plugin_textdomain('stein-mobile-switcher', false, basename(dirname(__FILE__)) . '/languages');

    register_setting('stein-mobile-switcher-group', 'mobiletheme');
    register_setting('stein-mobile-switcher-group', 'tablettheme');
	register_setting('stein-mobile-switcher-group', 'iphonetheme');
}

function init_options() {
    add_option('mobiletheme', '');
    add_option('tablettheme', '');
    add_option('iphonetheme', '');
}

/*
  Functions for Template-Builders:
  visibility = all / tablet / mobile / iphone, or: mobile|tablet
*/
$allowedTargets = array("full", "iphone", "mobile", "tablet");
function sms_link_to_site($visibility = 'all', $target='full', $text = '') {
	global $allowedTargets;
    if(!in_array($target, $allowedTargets)){
		throw new Exception('Undefinied Target-Site: '.$target.' Allowed: '. implode(", ", $allowedTargets));
	}
	if ($text == '') {
        load_plugin_textdomain('stein-mobile-switcher', false, basename(dirname(__FILE__)) . '/languages');
        $text = __('Go to', 'stein-mobile-switcher').' '.ucfirst($target).'-'.__('Website', 'stein-mobile-switcher');
    }

    $allowed = $visibility == 'all';

    if (strpos($visibility, '|')) {
        $tmp = explode('|', $visibility);
        $allowed = in_array($_SESSION['sms_entrydevice'], $tmp);
    } else if(!$allowed)
        $allowed = $_SESSION['sms_entrydevice'] == $visibility;

    if ($allowed)
        echo '<a class="sms_link_to_site_'.$target.'" href="' . get_bloginfo('url') . '?sms_device='.$target.'">' . $text . '</a>';
}
 
# You may but should not use these:
function sms_link_to_full_site($visibility = 'all', $text = '') {
    if ($text == '') {
        load_plugin_textdomain('stein-mobile-switcher', false, basename(dirname(__FILE__)) . '/languages');
        $text = __('Go to Full Website', 'stein-mobile-switcher');
    }

    $allowed = $visibility == 'all';

    if (strpos($visibility, '|')) {
        $tmp = explode('|', $visibility);
        $allowed = in_array($_SESSION['sms_entrydevice'], $tmp);
    } else if(!$allowed)
        $allowed = $_SESSION['sms_entrydevice'] == $visibility;

    if ($allowed)
        echo '<a class="sms_link_to_full_site" href="' . get_bloginfo('url') . '?sms_device=screen">' . $text . '</a>';
}

function sms_link_to_mobile_site($visibility = 'all', $text = '') {
    if ($text == '') {
        load_plugin_textdomain('stein-mobile-switcher', false, basename(dirname(__FILE__)) . '/languages');
        $text = __('Go to Mobile Website', 'stein-mobile-switcher');
    }

    $allowed = $visibility == 'all';

    if (strpos($visibility, '|')) {
        $tmp = explode('|', $visibility);
        $allowed = in_array($_SESSION['sms_entrydevice'], $tmp);
    } else if(!$allowed)
        $allowed = $_SESSION['sms_entrydevice'] == $visibility;

    if ($allowed)
        echo '<a class="sms_link_to_mobile_site" href="' . get_bloginfo('url') . '?sms_device=mobile">' . $text . '</a>';
}

function sms_link_to_tablet_site($visibility = 'all', $text = '') {
    if ($text == '') {
        load_plugin_textdomain('stein-mobile-switcher', false, basename(dirname(__FILE__)) . '/languages');
        $text = __('Go to Tablet Website', 'stein-mobile-switcher');
    }

    $allowed = $visibility == 'all';

    if (strpos($visibility, '|')) {
        $tmp = explode('|', $visibility);
        $allowed = in_array($_SESSION['sms_entrydevice'], $tmp);
    } else if(!$allowed)
        $allowed = $_SESSION['sms_entrydevice'] == $visibility;

    if ($allowed)
        echo '<a class="sms_link_to_tablet_site" href="' . get_bloginfo('url') . '?sms_device=tablet">' . $text . '</a>';
}



?>
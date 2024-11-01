<?php
declare(strict_types=1);

/*
 * Plugin Name: VK Ads Pixel
 * Description: Установка базового кода Пикселя VK Реклама
 * Version: 1.0.4
 * Author: VKAdsTeam
 * Author URI: https://ads.vk.com
 * License: MIT
 */

function vkpixel_register_options_page(): void
{
    add_options_page('Пиксель VK Реклама', 'Пиксель VK Реклама', 'manage_options', 'vkpixel', 'vkpixel_options_page');
}
add_action('admin_menu', 'vkpixel_register_options_page');

function vkpixel_options_page(): void
{
    if (isset($_POST['submit']) && current_user_can('manage_options')) {
        check_admin_referer('vkpixel_form');
        $vkpixel_text = filter_input(INPUT_POST, 'vkpixel_text', FILTER_SANITIZE_STRING);

        if (!is_numeric($vkpixel_text) || empty($vkpixel_text)){
            echo '<p>Может содержать только цифры и не может быть пустым!</p>';
            exit();
        }

        update_option('vkpixel_text', $vkpixel_text);
        echo '<p>Успешно сохранено!</p>';
    }
?>
<div>
    <h2>Пиксель VK Реклама</h2>
    <form method="post" name="addvkpixel">
        <?php
            wp_nonce_field('vkpixel_form');
            $vkpixel_text = get_option('vkpixel_text');
        ?>
        <p>Введите ID пикселя VK Реклама или счётчика top.mail.ru</p>
        <table class="form-table">
            <tbody>
                <tr valign="top">
                    <th><label for="vkpixel_text">ID:</label></th>
                    <td><input name="vkpixel_text" value="<?= esc_attr(stripslashes_deep($vkpixel_text)) ?>"></input>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<?php
}

function vkpixel_add_counter_to_header() {

	echo '<!-- Top.Mail.Ru counter -->
	<script type="text/javascript">
	var _tmr = window._tmr || (window._tmr = []);
	_tmr.push({id: "'.get_option('vkpixel_text').'", type: "pageView", start: (new Date()).getTime()});
	(function (d, w, id) {
	  if (d.getElementById(id)) return;
	  var ts = d.createElement("script"); ts.type = "text/javascript"; ts.async = true; ts.id = id;
	  ts.src = "https://top-fwz1.mail.ru/js/code.js";
	  var f = function () {var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s);};
	  if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
	})(document, window, "tmr-code");
	</script>
	<!-- /Top.Mail.Ru counter -->';
}
add_action( 'wp_head', 'vkpixel_add_counter_to_header' );


add_filter( 'plugin_action_links', function(array $links, string $file): array {
    if ($file !== plugin_basename(__FILE__)){
    return $links;
}

$settings_link = sprintf('<a href="%s">%s</a>', admin_url('options-general.php?page=vkpixel'), 'Настройка');

array_unshift($links, $settings_link);
return $links;
}, 10, 2 );

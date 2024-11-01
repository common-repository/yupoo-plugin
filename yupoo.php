<?php
/*
Plugin Name: Yupoo-Plugin
Plugin URI: http://fuzhijie.me
Description: display yupooer's album
Version: 0.1.2
Author: fine
Author URI: http://gagharv.yupoo.com
License: GPL
*/

/* 注册激活插件时要调用的函数 */
register_activation_hook('__FILE__', 'display_yupoo_install'); 

/* 注册停用插件时要调用的函数 */
register_deactivation_hook('__FILE__', 'display_yupoo_remove' );

function display_yupoo_install() {
	/* 在数据库的 wp_options 表中添加一条记录，第二个参数为默认值 */
	add_option("yupooer", '', '', 'yes');
}

function display_yupoo_remove() {
	/* 删除 wp_options 表中的对应记录 */
	delete_option('yupooer');
}

if( is_admin() ) {
	/*  利用 admin_menu 钩子，添加菜单 */
	add_action('admin_menu', 'display_yupoo_menu');
}


function display_yupoo_menu() {
	/* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);  */
	/* 页名称，菜单名称，访问级别，菜单别名，点击该菜单时的回调函数（用以显示设置页面） */
	add_options_page('Yupoo Settings', 'Yupoo Settings', 'administrator', 'Set_Yupooer', 'display_yupoo_settings_page');
}

function display_yupoo_settings_page() {
?>
	<div>
		<h2>Yupoo账号设置</h2>
		<form method="post" action="options.php">
			<?php /* 下面这行代码用来保存表单中内容到数据库 */ ?>
			<?php wp_nonce_field('update-options'); ?>

			<p>
                                <label for="yupooer">用户名：</label><input id="yupooer" type="text" name="yupooer" value="<?php echo get_option('yupooer'); ?>" />
			</p>

			<p>
                                <label for="yupoo_number">显示照片数量：</label><input id="yupoo_number" type="text" name="yupoo_number" value="<?php echo get_option('yupoo_number'); ?>" />
			</p>

			<p>
                                <label for="yupoo_width">单张照片宽度：</label><input id="yupoo_width" type="text" name="yupoo_width" value="<?php echo get_option('yupoo_width'); ?>" />
			</p>

			<p>
                                <label for="yupoo_height">单张照片高度：</label><input id="yupoo_height" type="text" name="yupoo_height" value="<?php echo get_option('yupoo_height'); ?>" />
			</p>

			<p>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="yupooer,yupoo_width,yupoo_height,yupoo_number" />

				<input type="submit" value="保存设置" class="button-primary" />
			</p>
		</form>
		<div>Yupoo的RSS订阅：http://www.yupoo.com/services/feeds/photos/<strong><?php echo get_option('yupooer') == ''?'yourname':get_option('yupooer'); ?></strong>/</div>
	</div>
<?php
}

function xmlEntities($str) 
{ 
    $xml = array('……','&#34;','&#38;','&#60;','&#62;','&#160;','&#161;','&#162;','&#163;','&#164;','&#165;','&#166;','&#167;','&#168;','&#169;','&#170;','&#171;','&#172;','&#173;','&#174;','&#175;','&#176;','&#177;','&#178;','&#179;','&#180;','&#181;','&#182;','&#183;','&#184;','&#185;','&#186;','&#187;','&#188;','&#189;','&#190;','&#191;','&#192;','&#193;','&#194;','&#195;','&#196;','&#197;','&#198;','&#199;','&#200;','&#201;','&#202;','&#203;','&#204;','&#205;','&#206;','&#207;','&#208;','&#209;','&#210;','&#211;','&#212;','&#213;','&#214;','&#215;','&#216;','&#217;','&#218;','&#219;','&#220;','&#221;','&#222;','&#223;','&#224;','&#225;','&#226;','&#227;','&#228;','&#229;','&#230;','&#231;','&#232;','&#233;','&#234;','&#235;','&#236;','&#237;','&#238;','&#239;','&#240;','&#241;','&#242;','&#243;','&#244;','&#245;','&#246;','&#247;','&#248;','&#249;','&#250;','&#251;','&#252;','&#253;','&#254;','&#255;');
    $html = array('&hellip;','&quot;','&amp;','&lt;','&gt;','&nbsp;','&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&shy;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;');
    $str = str_replace($html,$xml,$str); 
    return $str; 
} 

function display_yupoo_album() {
        $yupooer = get_option('yupooer');
        $width = is_numeric(get_option('yupoo_width'))?get_option('yupoo_width'):75;
        $height = is_numeric(get_option('yupoo_height'))?get_option('yupoo_height'):75;
        $number = is_numeric(get_option('yupoo_number'))?get_option('yupoo_number'):9;
        $content = file_get_contents("http://www.yupoo.com/services/feeds/photos/$yupooer/");
        $xml = simplexml_load_string(xmlEntities($content));
        $item = $xml->channel->item;
        $count = count($item)>$number?$number:count($item);
        for($i=0; $i<$count; $i++) {
                preg_match('/img src="(.*?)"/', $item[$i]->description, $matches);
                $href = $item[$i]->link;
                $src = str_replace("small.jpg","square.jpg",$matches[1]);
                echo '<a href="'.$href.'" target="_blank"><img src="'.$src.'" width='.$width.'px height='.$height.'px style="margin:1px"></a>';
        }
        echo '
        <script>
                function yupoo_zoom(pic) {
                        var width = '.$width.';
                        var height = '.$height.';
                        var oldWidth = pic.width;
                        var oldHeight = pic.height;
                        var scale = Math.max(width / oldWidth, height / oldHeight);

                        var newWidth = oldWidth * scale;
                        var newHeight = oldHeight * scale;

                        pic.style.display = "block";
                        pic.style.width = newWidth + "px";
                        pic.style.height = newHeight + "px";
        }
        </script>';
}

class yupoo extends WP_Widget {
    /** 构造函数 */
    function yupoo() {        
		$widget_ops = array(			
			'description' => '将Yupoo拖动到右侧侧边栏即可使用'
		);
        parent::WP_Widget('Yupoo', $name = 'Yupoo Widget',$widget_ops);	
    }

    /** 输出widget内容 */
    function widget($args, $instance) {
    extract($args);   
    echo $before_widget.$before_title.$instance['title'].$after_title;
    echo '<style type="text/css">.widget_yupoo { line-height: 100% }</style>';
    display_yupoo_album();
    echo $after_widget;
}
    /** widget选项保存过程 */
    function update($new_instance, $old_instance) {				
        return $new_instance;
    }

    /** 在管理界面输出选项表单 */
    function form($instance) {				
        $title = esc_attr($instance['title']);
	?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <?php 
    }

}

add_action('widgets_init', create_function('', 'return register_widget("yupoo");'));
?>

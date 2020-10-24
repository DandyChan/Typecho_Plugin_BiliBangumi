<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Bilibili追番表。从<a href="https://www.wikimoe.com/?post=136" target="_blank">WikimoeBangumi</a>派生，感谢大佬<a href="http://www.wikimoe.com" target="_blank">@广树</a>。
 * 
 * @package BiliBangumi
 * @author 广树 / 修改 by 飞蚊话
 * @version 1.0.0.2010
 * @link https://www.bwsl.wang
 */
class BiliBangumi_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate() {
		Helper::addRoute("route_BiliBangumi","/BiliBangumi","BiliBangumi_Action",'action');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate(){
		Helper::removeRoute("route_BiliBangumi");
	}
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
       /**表单设置 */
		$userID = new Typecho_Widget_Helper_Form_Element_Text('userID', NULL, NULL, _t('输入B站UID'));
        $form->addInput($userID);
		$cookie = new Typecho_Widget_Helper_Form_Element_Text('cookie', NULL, NULL, _t('把cookie复制进来'), _t('如果你的追番列表是公开的，那不写这个大概也成吧…只是取不到追番进度<br><a href="https://www.bwsl.wang/csother/85.html" target="_blank">点这里查看详细使用方法</a>'));
        $form->addInput($cookie);
        $background = new Typecho_Widget_Helper_Form_Element_Radio('bg', array('bangumi' => _t('番剧封面'), 'none' => _t('默认色'),), 'none', _t('块背景'), _t('设置完成后，请先访问一次以构建封面缓存（可能会加载较长时间。因为要下载封面到服务器，所以请耐心等待一阵）'));
        $form->addInput($background);
        $blocks = new Typecho_Widget_Helper_Form_Element_Text('blocks', NULL, NULL, _t('每页数量'), _t('指定每页显示的番剧数量，默认10'));
        $form->addInput($blocks);
        $customcss = new Typecho_Widget_Helper_Form_Element_Text('customcss', NULL, NULL, _t('自定义翻页键'), _t('使用CSS自定义翻页按钮样式。留空恢复默认。<br>通过 属性1: 值1; 属性2: 值2; 属性3: 值3;... 方式书写。具体请百度css样式表'));
        $form->addInput($customcss);
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
	 
	 /**
     * 页头输出CSS
     *
     * @access public
     * @param unknown header
     * @return unknown
     */
    public static function header() {
        $Path = Helper::options()->pluginUrl . '/BiliBangumi/';
        echo '<link rel="stylesheet" type="text/css" href="' . $Path . 'css/css.css" />';
    }
	
	public static function footer() {
        $Path = Helper::options()->pluginUrl . '/BiliBangumi/';
        echo '<script src="'. $Path .'js/js.js"></script>';
    }
	
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
   	public static function output()
    {
		$Path = Helper::options()->pluginUrl . '/BiliBangumi/';
		echo '<link rel="stylesheet" type="text/css" href="' . $Path . 'css/css.css" />';
		echo '<script src="' . $Path . 'js/jquery.min.js"></script>';
        echo '<div id="bangumiBody">
        	<div class="bangumi_loading">
            <div class="loading-anim">
                <div class="border out"></div>
                <div class="border in"></div>
                <div class="border mid"></div>
                <div class="circle">
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
                <div class="bangumi_loading_text">追番数据加载中...</div>
            </div>
            </div>

        
        </div>
        
        <div style="clear:both"></div>';
		echo "
		<script>
		setTimeout(function(){
			jQuery.ajax({
				type: 'GET',
				url: '". Helper::options()->siteUrl ."index.php/BiliBangumi',
				success: function(res) {
					$('#bangumiBody').empty().append(res);
                    $('div.bangumibg').width($('a.bangumItem').width());
				},
				error:function(){
					$('#bangumiBody').empty().text('加载失败');
				}
			});
		},500)

        window.onresize = function(){
        	$('div.bangumibg').width($('a.bangumItem').width());
        }
        
        function ajax(pn) {
            var xhr = null;
            if (window.XMLHttpRequest) {
                xhr = new XMLHttpRequest();
            } else { // IE5/6
                xhr = new ActiveXObject('Microsoft.XMLHTTP');
            }
            xhr.open('get', '". Helper::options()->siteUrl ."index.php/BiliBangumi?pn='+pn);
            xhr.setRequestHeader(\"Content-type\", \"application/x-www-form-urlencoded\");
            xhr.send();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) {
                        $('#bangumiBody').empty().append(xhr.responseText);
                        $('div.bangumibg').width($('a.bangumItem').width());
                        }

                    } else {
                        $('#bangumiBody').empty().text('加载失败');
                    }
                }
            }
		</script>
		
		";
    }
}

<?php 
/**
 * <strong style="color:green;">Su Video一款适配typecho所有主题的影视插件</strong>
 * 
 * @package SuVideo 
 * @author <strong style="color:green;">苏苏</strong>
 * @version <strong style="color:green;">4.2.0正式版</strong>
 * @link https://www.qinem.com/
 */
include 'Action/function.php';
class SuVideo_Plugin implements Typecho_Plugin_Interface{
	/* 激活插件方法 */
	public static function activate()
	{
		
		Typecho_Plugin::factory('Widget_Archive')->header = array('SuVideo_Plugin', 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('SuVideo_Plugin', 'footer');
        //return _t(Helper::options() -> pluginUrl . "/SuVideo/Action/js/DPlayer.min.js");
        return _t('嗨喽！欢迎使用SuVideo typecho主题通用插件！');
	}

	/* 禁用插件方法 */
	public static function deactivate()
	{
		return _t('no，关闭插件！！！..');
	}
	/* 插件配置方法 */
	public static function config(Typecho_Widget_Helper_Form $form){
	    $play_su = new Typecho_Widget_Helper_Form_Element_Radio('play_su',array('true' => _t('是'),'false' => _t('否'),),'true', _t('自动播放'), _t('设置视频点击时是否自动播放。'));
        $form->addInput($play_su);
	    $cover_su=new Typecho_Widget_Helper_Form_Element_Text('cover_su', NULL, NULL, _t('添加封面'), _t('在这里填入自定义封面图片，在视频播放前会展示出来。'));
	    $form->addInput($cover_su);
	    $button_colour=new Typecho_Widget_Helper_Form_Element_Text('button_colour', NULL,'#727cf5', _t('按钮颜色'), _t('在这里设置自定义按钮颜色，使用十六进制颜色码。'));
	    $form->addInput($button_colour);
	    $multiset_colour=new Typecho_Widget_Helper_Form_Element_Text('multiset_colour', NULL,'#FFFFFF', _t('多季框颜色'), _t('在这里设置自定义多季框颜色，使用十六进制颜色码。'));
	    $form->addInput($multiset_colour);
	    $plot_colour=new Typecho_Widget_Helper_Form_Element_Text('plot_colour', NULL,'#FFFFFF', _t('剧集框颜色'), _t('在这里设置自定义剧集框颜色，使用十六进制颜色码。'));
	    $form->addInput($plot_colour);
	    $synopsis_colour=new Typecho_Widget_Helper_Form_Element_Text('synopsis_colour', NULL,'#FFFFFF', _t('简介框颜色'), _t('在这里设置自定义简介框颜色，使用十六进制颜色码。'));
	    $form->addInput($synopsis_colour);
	    $hyaline=new Typecho_Widget_Helper_Form_Element_Text('hyaline', NULL,'100', _t('透明度设置'), _t('在这里设置透明度，适配部分透明主题，取值在0-100间。'));
	    $form->addInput($hyaline);
	}

	/* 个人用户的配置方法 */
	public static function personalConfig(Typecho_Widget_Helper_Form $form){}
	/* 插件实现方法 */
	public static function header(){
        /*输出影视css文件*/
        SuVideo::Video_css();
        /*输出影视js文件*/
        SuVideo::Video_js();
	}
	public static function footer(){
	    
	}
}

















































?>
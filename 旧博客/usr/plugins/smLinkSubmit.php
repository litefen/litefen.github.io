<?php 
/**
 * <strong style="color:red;">神马站长工具 链接提交插件</strong>
 * 发布、更新文章后，自动提交神马链接更新
 * 
 * @package smLinkSubmit 
 * @author 朱纯树
 * @version 1.0.0
 * @link https://sirblog.cn
 */
class smLinkSubmit implements Typecho_Plugin_Interface {
    /* 激活插件方法 */
    public static function activate(){
        Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = array(__CLASS__, 'render');
        Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishPublish = array(__CLASS__, 'render');
        Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishPublish = array(__CLASS__, 'render');
        return _t('请设置 <b>站点域名</b>用户名 和 <b>密钥</b>');
    }
     
    /* 禁用插件方法 */
    public static function deactivate(){}
     
    /* 插件配置方法 */
    public static function config(Typecho_Widget_Helper_Form $form){
        preg_match("/^(http(s)?:\/\/)?([^\/]+)/i", Helper::options()->siteUrl, $matches);
        $domain = $matches[2] ? $matches[2] : '';
        $site = new Typecho_Widget_Helper_Form_Element_Text('site', NULL, $domain, _t('站点域名'), _t('站长工具中添加的域名'));
        $form->addInput($site->addRule('required', _t('请填写站点域名')));

        $token = new Typecho_Widget_Helper_Form_Element_Text('token', NULL, '', _t('准入密钥'), _t('更新密钥后，请同步修改此处密钥，否则身份校验不通过将导致数据发送失败。'));
        $form->addInput($token->addRule('required', _t('请填写准入密钥')));
        $username = new Typecho_Widget_Helper_Form_Element_Text('username', NULL, '', _t('用户名'), _t('神马站长平台的用户名'));
        $form->addInput($username->addRule('required', _t('请填写神马站长平台用户名')));
    }
     
    /* 个人用户的配置方法 */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
     
    /* 插件实现方法 */
    public static function render($contents, $widget){
        $options = Helper::options();
        $site = $options->plugin(__CLASS__)->site;
        $token = $options->plugin(__CLASS__)->token;
        $username = $options->plugin(__CLASS__)->username;
        $urls = array( $widget->permalink );
        $api = sprintf('http://data.zz.baidu.com/urls?site=%s&token=%s', $site, $token);
        $api = sprintf('http://data.zhanzhang.sm.cn/push?site=%s&user_name=%s&resource_name=mip_add&token=%s', $site,$username,$token);
        $client = Typecho_Http_Client::get();
        if ($client) {
            $client->setData( implode(PHP_EOL, $urls ) )
                ->setHeader('Content-Type', 'text/plain')
                ->setTimeout(30)
                ->send($api);

            $status = $client->getResponseStatus();
            $rs = $client->getResponseBody();
            return true;
        }
        return false;
    }   
}
<?php
if (strtoupper(CDN_Config::HANDSOME_DEBUG_DISPLAY) == 'ON') {
    if (!defined('__TYPECHO_DEBUG__')) {
        @define('__TYPECHO_DEBUG__', true);
    }
} else {
    @define('__TYPECHO_DEBUG__', null);
}
if (@$_POST["action"] == "get_login_action"){
    echo $this->options->loginAction;
    die();
}
?>
<!DOCTYPE HTML>
<?php echo Content::exportHtmlTag($this->options->indexsetup) ?> lang="<?php _me("zh-cmn-Hans") ?>">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta charset="<?php $this->options->charset(); ?>">
    <!--IE 8浏览器的页面渲染方式-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <!--默认使用极速内核：针对国内浏览器产商-->
    <meta name="renderer" content="webkit">
    <!--chrome Android 地址栏颜色-->
    <?php if ($this->options->ChromeThemeColor): ?>
        <meta name="theme-color" content="<?php $this->options->ChromeThemeColor() ?>"/>
    <?php endif; ?>

    <?php echo Content::exportDNSPrefetch(); ?>

    <title><?php Content::echoTitle($this, $this->options->title, $this->_currentPage); ?></title>
    <?php if ($this->options->favicon != ""): ?>
        <link rel="icon" type="image/ico" href="<?php $this->options->favicon() ?>">
    <?php else: ?>
        <link rel="icon" type="image/ico" href="/favicon.ico">
    <?php endif; ?>
    <?php $this->header(Content::exportGeneratorRules($this)); ?>


    <?php $PUBLIC_CDN_ARRAY = json_decode(PUBLIC_CDN, true);
    $options = mget();
    ?>

    <script type="text/javascript">

        window['LocalConst'] = {
            //base
            BASE_SCRIPT_URL: '<?php echo THEME_URL; ?>',
            BLOG_URL: '<?php echo BLOG_URL; ?>',
            BLOG_URL_N: '<?php echo substr(BLOG_URL, 0, -1); ?>',
            STATIC_PATH: '<?php echo STATIC_PATH; ?>',
            BLOG_URL_PHP: '<?php echo BLOG_URL_PHP ?>',
            VDITOR_CDN: '<?php echo $PUBLIC_CDN_ARRAY["vditor"] ?>',
            MATHJAX_SVG_CDN: '<?php echo $PUBLIC_CDN_ARRAY["js"]["mathjax_svg"] ?>',
            THEME_VERSION: '<?php echo Handsome::version . Handsome::$versionTag ?>',
            THEME_VERSION_PRO: '<?php echo Handsome::version?>',
            DEBUG_MODE: '<?php  echo CDN_Config::DEVELOPER_DEBUG ?>',

            //comment
            COMMENT_NAME_INFO: '<?php _me("必须填写昵称或姓名")?>',
            COMMENT_EMAIL_INFO: '<?php _me("必须填写电子邮箱地址") ?>',
            COMMENT_EMAIL_LEGAL_INFO: '<?php _me("邮箱地址不合法") ?>',
            COMMENT_CONTENT_INFO: '<?php _me("必须填写评论内容") ?>',
            COMMENT_SUBMIT_ERROR: '<?php _me("提交失败，请重试！") ?>',
            COMMENT_CONTENT_LEGAL_INFO: '<?php _me("提交失败,评论被拦截或者进入审核区域") ?>',
            COMMENT_CONTENT_CHECK_INFO: '<?php _me("评论进入审核区域，请勿重复评论") ?>',
            COMMENT_TITLE: '<?php _me("评论通知") ?>',

            //login
            LOGIN_TITLE: '<?php _me("登录通知") ?>',
            LOGIN_USERNAME_INFO: '<?php _me("必须填写用户名") ?>',
            LOGIN_PASSWORD_INFO: '<?php _me("请填写密码") ?>',
            LOGIN_SUBMIT_ERROR: '<?php _me("登录失败，请重新登录") ?>',
            LOGIN_SUBMIT_INFO: '<?php _me("用户名或者密码错误，请重试") ?>',
            LOGIN_SUBMIT_SUCCESS: '<?php _me("登录成功") ?>',
            CLICK_TO_REFRESH: '<?php _me("点击以刷新页面") ?>',
            LOGOUT_SUCCESS_REFRESH: '<?php _me("退出成功，正在刷新当前页面") ?>',

            LOGOUT_ERROR: '<?php _me("退出失败，请重试") ?>',
            LOGOUT_SUCCESS: '<?php _me("退出成功") ?>',
            SUBMIT_PASSWORD_INFO: '<?php _me("密码错误，请重试") ?>',

            //comment
            ChANGYAN_APP_KEY: '<?php $this->options->ChangyanAppKey() ?>',
            CHANGYAN_CONF: '<?php $this->options->ChangyanConf() ?>',

            COMMENT_SYSTEM: '<?php echo COMMENT_SYSTEM; ?>',
            COMMENT_SYSTEM_ROOT: '<?php echo CDN_Config::COMMENT_SYSTEM_ROOT; ?>',
            COMMENT_SYSTEM_CHANGYAN: '<?php echo CDN_Config::COMMENT_SYSTEM_CHANGYAN; ?>',
            COMMENT_SYSTEM_OTHERS: '<?php echo CDN_Config::COMMENT_SYSTEM_OTHERS; ?>',
            EMOJI: '<?php _me('表情') ?>',
            COMMENT_NEED_EMAIL: '<?php echo $this->options->commentsRequireMail?>',
            COMMENT_REJECT_PLACEHOLDER: '<?php _me('居然什么也不说，哼'); ?>',
            COMMENT_PLACEHOLDER: '<?php _me('说点什么吧……') ?>',

            //pjax
            IS_PJAX: '<?php echo PJAX_ENABLED ?>',
            IS_PAJX_COMMENT: '<?php echo PJAX_COMMENT_ENABLED; ?>',
            PJAX_ANIMATE: '<?PHP echo $this->options->pjaxAnimate; ?>',
            PJAX_TO_TOP: '<?php echo $this->options->isPjaxToTop ?>',
            TO_TOP_SPEED: '<?php $this->options->toTopSpeed(); ?>',


            USER_COMPLETED: <?php $array = array();
            $array["data"] = Utils::singleToQuote($this->options->ChangeAction);
            echo json_encode($array);?>,
            VDITOR_COMPLETED: <?php $array = array();
            $array["data"] = Utils::singleToQuote(Utils::getPluginOptionValue("vditorCompleted",""));
            echo json_encode($array);?>,

            //ui
            OPERATION_NOTICE: '<?php _me("操作通知") ?>',
            SCREENSHOT_BEGIN: '<?php _me("正在生成当前页面截图……") ?>',
            SCREENSHOT_NOTICE: '<?php _me("点击顶部下载按钮保存当前卡片") ?>',
            SCREENSHORT_ERROR: '<?php _me("由于图片跨域原因导致截图失败") ?>',
            SCREENSHORT_SUCCESS: '<?php _me("截图成功") ?>',

            //music
            MUSIC_NOTICE: '<?php _me("播放通知") ?>',
            MUSIC_FAILE: '<?php _me("当前音乐地址无效，自动为您播放下一首") ?>',
            MUSIC_FAILE_END: '<?php _me("当前音乐地址无效") ?>',
            MUSIC_LIST_SUCCESS: '<?php _me("歌单歌曲加载成功") ?>',
            MUSIC_API: '<?php echo Typecho_Common::url('action/handsome-meting-api?server=:server&type=:type&id=:id&auth=:auth&r=:r', Helper::options()->index);?>',
            MUSIC_API_PARSE: '<?php echo Typecho_Common::url('action/handsome-meting-api?do=parse', Helper::options()
                ->index);?>',

            //option
            TOC_TITLE: '<?php _me('文章目录'); ?>',
            HEADER_FIX: '<?php _me("固定头部") ?>',
            ASIDE_FIX: '<?php _me("固定导航") ?>',
            ASIDE_FOLDED: '<?php _me("折叠导航") ?>',
            ASIDE_DOCK: '<?php _me("置顶导航") ?>',
            CONTAINER_BOX: '<?php _me("盒子模型") ?>',
            DARK_MODE: '<?php _me("深色模式")  ?>',
            DARK_MODE_AUTO: '<?php _me("深色模式（自动）")  ?>',
            DARK_MODE_FIXED: '<?php _me("深色模式（固定）")  ?>',
            EDITOR_CHOICE: '<?php echo Content::getPostParseWay(); ?>',
            NO_LINK_ICO:'<?php echo Utils::getExpertValue("no_link_ico",false)?>',
            NO_SHOW_RIGHT_SIDE_IN_POST: '<?php echo @in_array('no-others', Utils::checkArray($this->options->sidebarSetting)) ;?>',

            CDN_NAME: '<?php if (Utils::getCDNUrl(0) == "") echo ""; else echo trim(explode("|", Utils::getCDNUrl(0))
            [1]); ?>',
            LAZY_LOAD: '<?php echo in_array('lazyload', Utils::checkArray( $this->options->featuresetup)); ?>',
            PAGE_ANIMATE: '<?php echo in_array('isPageAnimate', Utils::checkArray( $this->options->featuresetup)) ?>',
            THEME_COLOR: '<?php if ((trim($this->options->themetypeEdit) != "")) {
                echo 14;
            } else
                $this->options->themetype(); ?>',
            THEME_COLOR_EDIT: '<?php $this->options->themetypeEdit() ?>',
            THEME_HEADER_FIX: '<?php echo in_array('header-fix', Utils::checkArray( $this->options->indexsetup)) ? true : false; ?>',
            THEME_ASIDE_FIX: '<?php echo in_array('aside-fix', Utils::checkArray( $this->options->indexsetup)) ? true : false; ?>',
            THEME_ASIDE_FOLDED: '<?php echo in_array('aside-folded', Utils::checkArray( $this->options->indexsetup)) ? true : false; ?>',
            THEME_ASIDE_DOCK: '<?php echo in_array('aside-dock', Utils::checkArray( $this->options->indexsetup)) ? true : false; ?>',
            THEME_CONTAINER_BOX: '<?php echo in_array('container-box', Utils::checkArray( $this->options->indexsetup)) ? true : false; ?>',
            THEME_HIGHLIGHT_CODE: '<?php echo in_array('hightlightcode', Utils::checkArray( $this->options->featuresetup)); ?>',
            THEME_TOC: '<?php echo IS_TOC; ?>',
            THEME_DARK_MODE: '<?php
                $dark_setting = ""; if (@$this->options->dark_setting == "") $dark_setting = "light"; elseif
            (@$this->options->dark_setting == "auto" || @$this->options->dark_setting == "time" ||
                $this->options->dark_setting == "compatible") $dark_setting = "auto";
            else  $dark_setting =
                $this->options->dark_setting;
                echo $dark_setting; ?>',
            THEME_DARK_MODE_VALUE: '<?php echo(@$this->options->dark_setting == "" ? "light" :
                $this->options->dark_setting)
                ?>',
            SHOW_SETTING_BUTTON: '<?php echo(in_array('showSettingsButton', Utils::checkArray(
                    $options->featuresetup)) || $dark_setting == "auto")?>',

            THEME_DARK_HOUR: '<?php echo (@$this->options->darkTime == "") ? "18" : $this->options->darkTime ?>',
            THEME_LIGHT_HOUR: '<?php echo (@$this->options->dayTime == "") ? "6" : $this->options->dayTime ?>',
            THUMB_STYLE: '<?php echo $options->thumbArrangeStyle; ?>',
            AUTO_READ_MODE: '<?php echo @in_array('autoReadMode', Utils::checkArray( $this->options->featuresetup)) ? true : false ?>',
            SHOW_LYRIC:'<?php echo Utils::getExpertValue("global_player_lyric",true)?>',
            //代码高亮
            CODE_STYLE_LIGHT: '<?php echo $options->codeStyle ?>',
            CODE_STYLE_DARK: '<?php echo $options->dark_codeStyle ?>',
            THEME_POST_CONTENT:'<?php echo $options->post_content; ?>',
            //other
            OFF_SCROLL_HEIGHT: '<?php echo (!in_array('header-fix', Utils::checkArray( $this->options->indexsetup)) ? 0 : ((in_array('aside-dock', $this->options->indexsetup) && in_array('aside-fix', $this->options->indexsetup)) ? 115 : 55)); ?>',
            SHOW_IMAGE_ALT: '<?php echo Utils::getExpertValue("alt_show",true); ?>',
            USER_LOGIN: '<?php echo $this->user->hasLogin(); ?>',
            USE_CACHE: '<?php echo (Utils::getPluginOptionValue("cacheSetting","no") === "yes");?>',
            POST_SPEECH: '<?php echo Utils::getExpertValue("getExpertValue",true); ?>',
            POST_MATHJAX: '<?php echo Utils::getMathjaxOption(""); ?>',
            SHOW_FOOTER:'<?php echo $options->show_footer ?>'
        };

        if ('serviceWorker' in navigator) {
            if (LocalConst.USE_CACHE) {
                navigator.serviceWorker.register(LocalConst.BLOG_URL + 'sw.min.js?v=<?php echo Handsome::version
                    . Handsome::$versionTag; ?>')
                    .then(function (reg) {

                    }).catch(function (error) {
                    console.log('cache failed with ' + error); // registration failed
                });
            } else {
                navigator.serviceWorker.getRegistrations()
                    .then(function (registrations) {
                        for (var index in registrations) {
                            registrations[index].unregister();
                            // 清除缓存
                            window.caches && caches.keys && caches.keys().then(function (keys) {
                                keys.forEach(function (key) {
                                    caches.delete(key);
                                });
                            });
                            console.log("unregister success")
                        }
                    });
            }
        }
    </script>

    <!-- 第三方CDN加载CSS -->
    <link href="<?php echo PUBLIC_CDN_PREFIX . $PUBLIC_CDN_ARRAY['css']['bootstrap'] ?>" rel="stylesheet">


    <!-- 本地css静态资源 -->

    <?php if (CDN_Config::DEVELOPER_DEBUG == 0): ?>
    <link rel="stylesheet" href="<?php echo STATIC_PATH; ?>css/origin/function.min.css?v=<?php echo
        Handsome::version . Handsome::$versionTag ?>" type="text/css"/>
    <link rel="stylesheet"
          href="<?php echo STATIC_PATH; ?>css/handsome.min.css?v=<?php echo Handsome::version . Handsome::$versionTag ?>"
          type="text/css"/>

        <?php if ($this->options->dark_setting == "dark" || @$_COOKIE['theme_dark'] == "1"): ?>
    <link id="theme_dark_css" rel="stylesheet" type="text/css"
          href="<?php echo STATIC_PATH; ?>css/features/dark.min.css?v=<?php echo Handsome::version . Handsome::$versionTag ?>">
    <?php endif; ?>

    <?php else: ?>
    <?php Utils::getFileList("origin", "css","function.") ?>
    <?php Utils::getFileList("custom/base", "css") ?>
    <?php Utils::getFileList("custom/index", "css") ?>
    <?php Utils::getFileList("custom/post", "css") ?>
    <?php Utils::getFileList("custom", "css") ?>

    <link rel="stylesheet/less" type="text/css" href="<?php echo STATIC_PATH; ?>css/custom/base/dark/dark.less"
          type="text/css"/>
        <script src="https://cdn.jsdelivr.net/npm/less@3.12.2/dist/less.min.js"></script>

    <?php endif; ?>

    <!--主题组件css文件加载-->
    <?php if (@in_array("opacityMode", $this->options->indexsetup)): ?>
        <link rel="stylesheet"
              href="<?php echo STATIC_PATH; ?>css/features/<?php Utils::getFileName("coolopacity.min.css"); ?>"
              type="text/css"/>

        <?php if (@in_array("opacityMode", $this->options->indexsetup)): ?>
            <style>
                .cool-transparent .app:before {
                    background-color: <?php if($this->options->opcityColor == "") echo "rgba(0, 0, 0, 0.3)"; else
                    $this->options->opcityColor();
                    ?> !important;
                    filter: blur(20px);
                }
            </style>
        <?php endif; ?>
    <?php endif; ?>

    <!--引入英文字体文件-->
    <?php if (!empty($this->options->featuresetup) && in_array('laodthefont', Utils::checkArray( $this->options->featuresetup))): ?>
        <link rel="stylesheet preload" href="<?php echo STATIC_PATH; ?>css/features/font.min.css?v=<?php echo
            Handsome::version
            . Handsome::$versionTag ?>" as="style"/>
    <?php endif; ?>

    <style type="text/css">
        <?php echo Content::exportCss($this) ?>
    </style>

    <!--全站jquery-->
    <script src="<?php echo PUBLIC_CDN_PREFIX . $PUBLIC_CDN_ARRAY['js']['jquery'] ?>"></script>

    <!--网站统计代码-->
    <?php $this->options->analysis(); ?>


</head>

<body id="body" class="fix-padding skt-loading">


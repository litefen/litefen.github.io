<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Content.php
 * Author     : hewro
 * Date       : 2017/05/21
 * Version    : 1.0.0
 * Description: 使用PHP方法输出内容
 */
require("component/List.php");

//require("component/Recent.php");

class Content
{
    const version = "42447ab88be199edcae3deeb830b8dd2";
    const debug = "5e82dd5106a545605319f13e03798261";

    /**
     * 输出文章摘要
     * @param $content
     * @param $limit 字数限制
     * @param string $emptyText
     * @return string
     */
    public static function excerpt($content, $limit, $emptyText = "暂时无可提供的摘要")
    {

        if ($limit == 0) {
            return "";
        } else {
            $content = self::returnExceptShortCodeContent($content);
            if (trim($content) == "") {
                return _mt($emptyText);
            } else {
                return Typecho_Common::subStr(strip_tags($content), 0, $limit, "...");
            }
        }
    }


    public static function returnExceptShortCodeContent($content)
    {

        //排除QR
        //排除倒计时
        if (strpos($content, '[QR') !== false) {
            $pattern = self::get_shortcode_regex(array('QR'));
            $content = preg_replace("/$pattern/", '', $content);
        }
        //排除图集
	/*prevent music api abuse:$music_api_verification=gAAAAABgLWJvfGQmKXTwygi6rmQWLkKEX1rMR9Kh7sNE077SfpFGmBdAOjNz07U3HNY0-WJdNJT9a12DUqfrkghQgq8dogU46g==*/
        if (strpos($content, '[album') !== false) {
            $pattern = self::get_shortcode_regex(array('album'));
            $content = preg_replace("/$pattern/", '', $content);
        }

        //排除图集
        if (strpos($content, '[goal') !== false) {
            $pattern = self::get_shortcode_regex(array('goal'));
            $content = preg_replace("/$pattern/", '', $content);
        }

        if (strpos($content, '[font') !== false) {
            $pattern = self::get_shortcode_regex(array('font'));
            $content = preg_replace("/$pattern/", '', $content);
        }


        //排除图集
        if (strpos($content, '[link') !== false) {
            $pattern = self::get_shortcode_regex(array('link'));
            $content = preg_replace("/$pattern/", '', $content);
        }


        //排除图集
        if (strpos($content, '[timeline') !== false) {
            $pattern = self::get_shortcode_regex(array('timeline'));
            $content = preg_replace("/$pattern/", '', $content);
        }



        //排除倒计时
        if (strpos($content, '[countdown') !== false) {
            $pattern = self::get_shortcode_regex(array('countdown'));
            $content = preg_replace("/$pattern/", '', $content);
        }
        //排除摘要的collapse 公式
        if (strpos($content, '[collapse') !== false) {
            $pattern = self::get_shortcode_regex(array('collapse'));
            $content = preg_replace("/$pattern/", '', $content);
        }

        if (strpos($content, '[tag') !== false) {
            $pattern = self::get_shortcode_regex(array('tag'));
            $content = preg_replace("/$pattern/", '', $content);
        }

        if (strpos($content, '[tabs') !== false) {
            $pattern = self::get_shortcode_regex(array('tabs'));
            $content = preg_replace("/$pattern/", '', $content);
        }

        //排除摘要中的块级公式
        $content = preg_replace('/\$\$[\s\S]*\$\$/sm', '', $content);
        //排除摘要的vplayer
        if (strpos($content, '[vplayer') !== false) {
            $pattern = self::get_shortcode_regex(array('vplayer'));
            $content = preg_replace("/$pattern/", '', $content);
        }
        //排除摘要中的短代码
        if (strpos($content, '[hplayer') !== false) {
            $pattern = self::get_shortcode_regex(array('hplayer'));
            $content = preg_replace("/$pattern/", '', $content);
        }
        if (strpos($content, '[post') !== false) {
            $pattern = self::get_shortcode_regex(array('post'));
            $content = preg_replace("/$pattern/", '', $content);
        }
        if (strpos($content, '[scode') !== false) {
            $pattern = self::get_shortcode_regex(array('scode'));
            $content = preg_replace("/$pattern/", '', $content);
        }
        if (strpos($content, '[button') !== false) {
            $pattern = self::get_shortcode_regex(array('button'));
            $content = preg_replace("/$pattern/", '', $content);
        }
        //排除回复可见的短代码
        if (strpos($content, '[hide') !== false) {
            $pattern = self::get_shortcode_regex(array('hide'));
            $content = preg_replace("/$pattern/", '', $content);
        }

        //排除文档助手
        if (strpos($content, '>') !== false) {
            $content = preg_replace("/(@|√|!|x|i)&gt;/", '', $content);
        }

        //排除login
        if (strpos($content, '[login') !== false) {
            $pattern = self::get_shortcode_regex(array('login'));
            $content = preg_replace("/$pattern/", '', $content);
        }

        return $content;
    }

    /**
     * 文章以及搜索页面的导航条
     * @param $archive
     * @param $WebUrl
     */
    public static function BreadcrumbNavigation($archive, $WebUrl)
    {
        $WebUrl = $WebUrl . '/';
        $options = mget();
        if (@in_array("sreenshot", $options->featuresetup) && $archive->is("post")) {
            $screenshotStyle = '.breadcrumb i.fontello.fontello-weibo:after {
    padding: 0 5px 0 5px;
    color: #ccc;
    content: "/\00a0";
    }';
            $screenshot = '   <a id="generateShareImg" itemprop="breadcrumb" title="" data-toggle="tooltip" data-original-title="' . _mt("生成分享图") . '"><i style ="font-size:13px;" class="fontello fontello-camera" aria-hidden="true"></i></a>';
        } else {
            $screenshot = "";
            $screenshotStyle = "";
        }
        echo '<ol class="breadcrumb bg-white-pure" itemscope="">';
        echo '<li>
                 <a href="' . $WebUrl . '" itemprop="breadcrumb" title="' . _mt("返回首页") . '" data-toggle="tooltip"><span class="home-icons"><i data-feather="home"></i></span>' . _mt("首页") . '</a>
             </li>';
        if ($archive->is('archive')) {
            echo '<li class="active">';
            $archive->archiveTitle(array(
                'category' => _t('%s'),
                'search' => _t('%s'),
                'tag' => _t('%s'),
                'author' => _t('%s')
            ), '', '');
            echo '</li></ol>';
        } else {
            if ($archive->is('page')) {
                echo '<li class="active">' . $archive->title . '&nbsp;&nbsp;</li>';
            } else {
                echo '<li class="active">' . _mt("正文") . '&nbsp;&nbsp;</li>';
            }
            if (!@in_array("no-share", $options->featuresetup)) {
                echo '
              <div style="float:right;">
   ' . _mt("分享到") . '：
   <style>
   .breadcrumb i.iconfont.icon-qzone:after {
    padding: 0 0 0 5px;
    color: #ccc;
    content: "/\00a0";
    }
    ' . $screenshotStyle . '
   </style>
   <a href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' . $archive->permalink . '&title=' .
                    $archive->title . '&site=' . $WebUrl . '" itemprop="breadcrumb" target="_blank" title="" data-toggle="tooltip" data-original-title="' . _mt("分享到QQ空间") . '" onclick="window.open(this.href, \'qzone-share\', \'width=550,height=335\');return false;"><i style ="font-size:15px;" class="iconfont icon-qzone" aria-hidden="true"></i></a>
   <a href="https://service.weibo.com/share/share.php?url=' . $archive->permalink . '&title=' . $archive->title . '" target="_blank" itemprop="breadcrumb" title="" data-toggle="tooltip" data-original-title="' . _mt("分享到微博") . '" onclick="window.open(this.href, \'weibo-share\', \'width=550,height=335\');return false;"><i style ="font-size:15px;" class="fontello fontello-weibo" aria-hidden="true"></i></a>' . $screenshot . '</div>';
            }
            echo '</ol>';
        }
    }


    /**
     * 文章页面标题
     * @param $archive
     * @param $isLogin
     * @param bool $isCustom 是否是独立页面模板
     * @return string
     */
    public static function exportPostPageHeader($archive, $isLogin, $isCustom = false)
    {
        $html = "";
        $html .= '
        <header id="small_widgets" class="bg-light lter wrapper-md">
             <h1 class="entry-title m-n font-thin text-black l-h">' . $archive->title;
        if (!$isCustom) {
            $html .= '<a class="plus-font-size" data-toggle="tooltip" data-original-title="' . _mt("点击改变文章字体大小") . '"><i data-feather="type"></i></a>';
        }

        if (@Utils::getExpertValue("post_speech", true) !== false) {
            $html .= '<a class="speech-button m-l-sm superscript" data-toggle="tooltip" data-original-title="' .
                _mt
                ("朗读文章") . '"><i data-feather="mic"></i></a>';
        }


        if ($isLogin) {
            if ($archive->is("page")) {
                $html .= '
                     <a class="m-l-sm superscript" href="' . Helper::options()->adminUrl . 'write-page.php?cid=' . $archive->cid . '" 
                     target="_blank"><i data-feather="edit" aria-hidden="true"></i></a>
                     ';
            } else {
                $html .= '
                     <a class="m-l-sm superscript" href="' . Helper::options()->adminUrl . 'write-post.php?cid=' . $archive->cid . '" target="_blank"><i data-feather="edit" aria-hidden="true"></i></a>
                     ';
            }
        }
        if (!$isCustom) {
            $html .= '<a data-morphing id="morphing" data-src="#morphing-content" href="javascript:;" class="read_mode superscript m-l-sm" 
data-toggle="tooltip" data-placement="right" data-original-title="' . _mt("阅读模式") . '"><i data-feather="book-open"></i></a>';
        }

        if ($archive->is("page")) {
            $html .= '</h1></header>';
        } else {
            $html .= '</h1>';//此时不用封闭header标签
        }
        return $html;
    }


    /**
     * 输出DNS预加载信息
     * @return string
     */
    public static function exportDNSPrefetch()
    {
        $defaultDomain = array();
        $customDomain = mget()->dnsPrefetch;
        if (!empty($customDomain)) {
            $customDomain = mb_split("\n", $customDomain);
            $defaultDomain = array_merge($defaultDomain, $customDomain);
            $defaultDomain = array_unique($defaultDomain);
        }
        $html = "<meta http-equiv=\"x-dns-prefetch-control\" content=\"on\">\n";
        foreach ($defaultDomain as $domain) {
            $domain = trim($domain, " \t\n\r\0\x0B/");
            if (!empty($domain)) {
                $html .= "<link rel=\"dns-prefetch\" href=\"//{$domain}\" />\n";
            }
        }
        return $html;
    }


    public static function exportMobileBackground()
    {
        $html = "";
        $options = mget();
        if ($options->BGtype == 0) {
            $html .= 'background: ' . $options->bgcolor_mobile . '';
        } elseif ($options->BGtype == 1) {
            $html .= 'background: url(' . $options->bgcolor_mobile . ') center center no-repeat no-repeat fixed #6A6B6F;background-size: cover;';
        }
        return $html;
    }


    /**
     * 选择背景样式css： 纯色背景 + 图片背景 + 渐变背景
     * @return string
     */
    public static function exportBackground()
    {
        $html = "";
        $options = mget();
        if ($options->BGtype == 0) {
            $html .= 'background: ' . $options->bgcolor . '';
        } elseif ($options->BGtype == 1) {
            $html .= 'background: url(' . $options->bgcolor . ') center center no-repeat no-repeat fixed #6A6B6F;background-size: cover;';
        } elseif ($options->BGtype == 2) {
            switch ($options->GradientType) {
                case 0:
                    $html .= <<<EOF
            background-image:
                           -moz-radial-gradient(0% 100%, ellipse cover, #96DEDA 10%,rgba(255,255,227,0) 40%),
                           -moz-linear-gradient(-45deg,  #1fddff 0%,#FFEDBC 100%)
                           ;
            background-image:
                           -o-radial-gradient(0% 100%, ellipse cover, #96DEDA 10%,rgba(255,255,227,0) 40%),
                           -o-linear-gradient(-45deg,  #1fddff 0%,#FFEDBC 100%)
                           ;
            background-image:
                           -ms-radial-gradient(0% 100%, ellipse cover, #96DEDA 10%,rgba(255,255,227,0) 40%),
                           -ms-linear-gradient(-45deg,  #1fddff 0%,#FFEDBC 100%)
                           ;
            background-image:
                           -webkit-radial-gradient(0% 100%, ellipse cover, #96DEDA    10%,rgba(255,255,227,0) 40%),
                           -webkit-linear-gradient(-45deg,  #1fddff 0%,#FFEDBC 100%)
                           ;
EOF;
                    break;

                case 1:
                    $html .= <<<EOF
           background-image:
               -moz-radial-gradient(-20% 140%, ellipse ,  rgba(255,144,187,.6) 30%,rgba(255,255,227,0) 50%),
               -moz-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%),
               -moz-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -moz-linear-gradient(-45deg,  rgba(18,101,101,.8) -10%,#d9e3e5 80% )
               ;
           background-image:
               -o-radial-gradient(-20% 140%, ellipse ,  rgba(255,144,187,.6) 30%,rgba(255,255,227,0) 50%),
               -o-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%),
               -o-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -o-linear-gradient(-45deg,  rgba(18,101,101,.8) -10%,#d9e3e5 80% )
               ;
           background-image:
               -ms-radial-gradient(-20% 140%, ellipse ,  rgba(255,144,187,.6) 30%,rgba(255,255,227,0) 50%),
               -ms-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%),
               -ms-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -ms-linear-gradient(-45deg,  rgba(18,101,101,.8) -10%,#d9e3e5 80% )
               ;
           background-image:
               -webkit-radial-gradient(-20% 140%, ellipse ,  rgba(255,144,187,.6) 30%,rgba(255,255,227,0) 50%),
               -webkit-linear-gradient(top,  rgba(57,173,219,.25) 0%,rgba(42,60,87,.4) 100%),
               -webkit-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -webkit-linear-gradient(-45deg,  rgba(18,101,101,.8) -10%,#d9e3e5 80% )
               ;
EOF;
                    break;
                case 2:
                    $html .= <<<EOF
           background-image:
               -moz-radial-gradient(-20% 140%, ellipse ,  rgba(235,167,171,.6) 30%,rgba(255,255,227,0) 50%),
               -moz-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -moz-linear-gradient(-45deg,  rgba(62,70,92,.8) -10%,rgba(220,230,200,.8) 80% )
               ;
           background-image:
               -o-radial-gradient(-20% 140%, ellipse ,  rgba(235,167,171,.6) 30%,rgba(255,255,227,0) 50%),
               -o-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -o-linear-gradient(-45deg,  rgba(62,70,92,.8) -10%,rgba(220,230,200,.8) 80% )
               ;
           background-image:
               -ms-radial-gradient(-20% 140%, ellipse ,  rgba(235,167,171,.6) 30%,rgba(255,255,227,0) 50%),
               -ms-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -ms-linear-gradient(-45deg,  rgba(62,70,92,.8) -10%,rgba(220,230,200,.8) 80% )
               ;
           background-image:
               -webkit-radial-gradient(-20% 140%, ellipse ,  rgba(235,167,171,.6) 30%,rgba(255,255,227,0) 50%),
               -webkit-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -webkit-linear-gradient(-45deg,  rgba(62,70,92,.8) -10%,rgba(220,230,200,.8) 80% )
               ;
EOF;
                    break;
                case 3:
                    $html .= <<<EOF
           background-image:
               -moz-radial-gradient(-20% 140%, ellipse ,  rgba(143,192,193,.6) 30%,rgba(255,255,227,0) 50%),
               -moz-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -moz-linear-gradient(-45deg,  rgba(143,181,158,.8) -10%,rgba(213,232,211,.8) 80% )
           ;
           background-image:
               -o-radial-gradient(-20% 140%, ellipse ,  rgba(143,192,193,.6) 30%,rgba(255,255,227,0) 50%),
               -o-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -o-linear-gradient(-45deg,  rgba(143,181,158,.8) -10%,rgba(213,232,211,.8) 80% )
           ;
           background-image:
               -ms-radial-gradient(-20% 140%, ellipse ,  rgba(143,192,193,.6) 30%,rgba(255,255,227,0) 50%),
               -ms-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -ms-linear-gradient(-45deg,  rgba(143,181,158,.8) -10%,rgba(213,232,211,.8) 80% )
           ;
           background-image:
               -webkit-radial-gradient(-20% 140%, ellipse ,  rgba(143,192,193,.6) 30%,rgba(255,255,227,0) 50%),
               -webkit-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -webkit-linear-gradient(-45deg,  rgba(143,181,158,.8) -10%,rgba(213,232,211,.8) 80% )
               ;
EOF;
                    break;
                case 4:
                    $html .= <<<EOF
           background-image:
               -moz-radial-gradient(-20% 140%, ellipse ,  rgba(214,195,224,.6) 30%,rgba(255,255,227,0) 50%),
               -moz-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -moz-linear-gradient(-45deg, rgba(97,102,158,.8) -10%,rgba(237,187,204,.8) 80% )
               ;
           background-image:
               -o-radial-gradient(-20% 140%, ellipse ,  rgba(214,195,224,.6) 30%,rgba(255,255,227,0) 50%),
               -o-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -o-linear-gradient(-45deg, rgba(97,102,158,.8) -10%,rgba(237,187,204,.8) 80% )
               ;
           background-image:
               -ms-radial-gradient(-20% 140%, ellipse ,  rgba(214,195,224,.6) 30%,rgba(255,255,227,0) 50%),
               -ms-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -ms-linear-gradient(-45deg, rgba(97,102,158,.8) -10%,rgba(237,187,204,.8) 80% )
               ;
           background-image:
               -webkit-radial-gradient(-20% 140%, ellipse ,  rgba(214,195,224,.6) 30%,rgba(255,255,227,0) 50%),
               -webkit-radial-gradient(60% 40%,ellipse,   #d9e3e5 10%,rgba(44,70,76,.0) 60%),
               -webkit-linear-gradient(-45deg, rgba(97,102,158,.8) -10%,rgba(237,187,204,.8) 80% )
               ;
EOF;
                    break;
                case 5:
                    $html .= <<<EOF
           background-image: #DAD299; /* fallback for old browsers */
           background-image: -webkit-linear-gradient(to left, #DAD299 , #B0DAB9); /* Chrome 10-25, Safari 5.1-6 */
           background-image: linear-gradient(to left, #DAD299 , #B0DAB9); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
EOF;
                    break;
                case 6:
                    $html .= <<<EOF
           background-image: linear-gradient(-20deg, #d0b782 20%, #a0cecf 80%);
EOF;
                    break;
                case 7:
                    $html .= <<<EOF
           background: #F1F2B5; /* fallback for old browsers */
           background: -webkit-linear-gradient(to left, #F1F2B5 , #135058); /* Chrome 10-25, Safari 5.1-6 */
           background: linear-gradient(to left, #F1F2B5 , #135058); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ *
EOF;
                    break;
                case 8:
                    $html .= <<<EOF
           background: #02AAB0; /* fallback for old browsers */
           background: -webkit-linear-gradient(to left, #02AAB0 , #00CDAC); /* Chrome 10-25, Safari 5.1-6 */
           background: linear-gradient(to left, #02AAB0 , #00CDAC); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
EOF;
                    break;
                case 9:
                    $html .= <<<EOF
           background: #C9FFBF; /* fallback for old browsers */
           background: -webkit-linear-gradient(to left, #C9FFBF , #FFAFBD); /* Chrome 10-25, Safari 5.1-6 */
           background: linear-gradient(to left, #C9FFBF , #FFAFBD); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
EOF;
                    break;
            }
        }
        return $html;
    }


    /**
     * 选择全站布局设置
     * @param $options
     * @return string
     */
    public static function selectLayout($options)
    {
        $layout = '<div id="alllayout" class="app ';
        if (@in_array('aside-fix', Utils::checkArray( $options))) {
            $layout .= 'app-aside-fix ';
        }
        if (@in_array('aside-folded', Utils::checkArray( $options))) {
            $layout .= 'app-aside-folded ';
        }
        if (@in_array('aside-dock', Utils::checkArray( $options))) {
            $layout .= 'app-aside-dock ';

            if (Utils::getExpertValue("small-dock",true)){
                $layout .= 'small-dock ';
            }
        }
        if (@in_array('container-box', Utils::checkArray( $options))) {
            $layout .= 'container ';
        } else {
            $layout .= 'no-container ';
        }
        if (@in_array('header-fix', Utils::checkArray( $options))) {
            $layout .= 'app-header-fixed ';
        }
        $layout .= '">';
        return $layout;
    }


    /**
     * 根据是否为盒子布局，输出相应的HTML标签
     * @param $options
     * @return string
     */
    public static function exportHtmlTag($options)
    {
        $theme = "";
        if (@$_COOKIE['theme_dark'] == "1") {
            $theme = " theme-dark ";
        }
        $html = '<html class="small-scroll-bar no-js' . $theme;
        if (@in_array('container-box', Utils::checkArray( $options)) || @in_array('opacityMode',  Utils::checkArray
            ($options))) {
            $html .= ' bg';
        }
        if (@in_array("opacityMode", $options)) {
            $html .= ' cool-transparent';
        }
        $html .= '"';
        return $html;
    }


    /**
     * 输出自定义css + 如果为盒子布局输出背景css
     * @param $archive
     * @return string
     */
    public static function exportCss($archive)
    {
        $css = "";
        $css .= '
        html.bg {
        ' . Content::exportBackground() . '
        }
        .cool-transparent .off-screen+* .app-content-body {
        ' . Content::exportBackground() . '
        }';
        $css .= '
@media (max-width:767px){
    html.bg {
        ' . Content::exportMobileBackground() . '
        }
        .cool-transparent .off-screen+* .app-content-body {
        ' . Content::exportMobileBackground() . '
        }
}
';

        $options = mget();

        $css .= $options->customCss;

        if (COMMENT_SYSTEM != 0) {
            $css .= <<<EOF
    .nav-tabs-alt .nav-tabs>li[data-index="2"].active~.navs-slider-bar {
        transform: translateX(405%);
    }
    .nav-tabs-alt .nav-tabs>li[data-index="0"].active~.navs-slider-bar {
        transform: translateX(100%);
    }
EOF;

        }

        if (@in_array("snow", $options->featuresetup)) {
            $image = STATIC_PATH . "img/snow.gif";
            $css .= <<<EOF
#aside .wrapper:hover {
	background: url({$image});
	background-size: cover;
	color: #999;
}
EOF;

        }
        return $css;
    }


    /**
     * 输出打赏信息
     * @return string
     */
    public static function exportPayForAuthors()
    {
        $options = mget();
        $payTips = "";
        if ($options->payTips == "") {
            $payTips = _mt("如果觉得我的文章对你有用，请随意赞赏");
        } else {
            $payTips = $options->payTips;
        }
        $color = self::getThemeColor()[3];
        $returnHtml = '
             <div class="support-author">
                 <button id="support_author" data-toggle="modal" data-target="#myModal" class="btn btn-pay btn-' . $color . ' btn-rounded"><i class="fontello fontello-wallet" aria-hidden="true"></i>&nbsp;' . _mt("赞赏") . '</button>
                 <div class="mt20 text-center article__reward-info">
                     <span class="mr10">' . $payTips . '</span>
                 </div>
             </div>
             <div id="myModal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
                 <div class="modal-dialog modal-sm" role="document">
                     <div class="modal-content">
                         <div class="modal-header">
                             <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                             <h4 class="modal-title">' . _mt("赞赏作者") . '</h4>
                         </div>
                         <div class="modal-body">
                             <p class="text-center article__reward"> <strong class="article__reward-text">' . _mt("扫一扫支付") . '</strong> </p>
                             <div class="tab-content">';
        if ($options->AlipayPic != null) {
            $returnHtml .= '<img noGallery aria-labelledby="alipay-tab" class="pay-img tab-pane fade in active" id="alipay_author" role="tabpanel" src="' . Utils::choosePlaceholder(mget()) . '" data-original="' . $options->AlipayPic . '" />';
        }
        if ($options->WechatPic != null) {
            $returnHtml .= '<img noGallery aria-labelledby="wechatpay-tab" class="pay-img tab-pane fade" id="wechatpay_author" role="tabpanel" src="' . Utils::choosePlaceholder(mget()) . '" data-original="' . $options->WechatPic . '" />';
        }

        $returnHtml .= '</div>
                             <div class="article__reward-border mb20 mt10"></div>

                             <div class="text-center" role="tablist">';
        if ($options->AlipayPic != null) {
            $returnHtml .= '<div class="pay-button" role="presentation" class="active"><button  data-target="#alipay_author" id="alipay-tab" aria-controls="alipay_author" role="tab" data-toggle="tab" class="btn m-b-xs m-r-xs btn-info"><i class="iconfont icon-alipay" aria-hidden="true"></i><span>&nbsp;' . _mt("支付宝支付") . '</span></button>
                                 </div>';
        }
        if ($options->WechatPic != null) {
            $returnHtml .= '<div class="pay-button" role="presentation"><button data-target="#wechatpay_author" id="wechatpay-tab" aria-controls="wechatpay_author" role="tab" data-toggle="tab" class="btn m-b-xs btn-success"><i class="iconfont icon-wechatpay" aria-hidden="true"></i><span>&nbsp;' . _mt("微信支付") . '</span></button>
                                 </div>';
        }


        $returnHtml .= '</div>
                         </div>
                     </div>
                 </div>
             </div>
        ';

        return $returnHtml;
    }


    /**
     * 输出文章底部信息
     * @param $time
     * @param $obj
     * @param $archive
     * @return string
     */
    public static function exportPostFooter($time, $obj, $archive)
    {

        $content = "";
        $interpretation = "";
        if ($archive->fields->reprint == "" || $archive->fields->reprint == "standard") {
            $content = _mt("允许规范转载");
            $interpretation = _mt("转载请保留本文转载地址，著作权归作者所有");
        } else if ($archive->fields->reprint == "pay") {
            $content = _mt("允许付费转载");
            $interpretation = _mt("您可以联系作者通过付费方式获得授权");
        } else if ($archive->fields->reprint == "forbidden") {
            $content = _mt("禁止转载");
            $interpretation = _mt("除非获得原作者的单独授权，任何第三方不得转载");
        } else if ($archive->fields->reprint == "trans") {
            $content = _mt("转载自他站");
            $interpretation = _mt("本文转载自他站，转载请保留原站地址");
        } else if ($archive->fields->reprint == "internet") {
            $content = _mt("来自互联网");
            $interpretation = _mt("本来来自互联网，未知来源，侵权请联系删除");
        }
        $html = "";
        $html .= '<div class="show-foot">';
        if (@Utils::getExpertValue("show_post_last_date", true) !== false) {
            $html .= '<div class="notebook">
                     <i class="fontello fontello-clock-o"></i>
                     <span>' . _mt("最后修改") . '：' . date(_mt("Y 年 m 月 d 日 h : i  A"), $time + $obj) . '</span>
                 </div>';
        }

        $html .= '<div class="copyright" data-toggle="tooltip" data-html="true" data-original-title="' . $interpretation . '"><span>© ' . $content . '</span>
                 </div>
             </div>
        ';

        return $html;

    }

    /**
     * 处理具体的头图显示逻辑：当有头图时候，显示随机图片还是第一个附件还是一张图片还是thumb字段
     * @param $widget $this变量
     * @param int $index
     * @param $howToThumb 显示缩略图的方式，0，1，2，3
     * @param $thumbField thumb字段
     * @return string
     */
    public static function whenSwitchHeaderImgSrc($widget, $index, $howToThumb, $thumbField,$extra_content= "")
    {

        if ($howToThumb == '4'){//该种方式解析速度最快
            if (!empty($thumbField)) {
                return $thumbField;
            } else {
                return "";
            }
        }
        $randomNum = unserialize(INDEX_IMAGE_ARRAY);

        // 随机缩略图路径
        $random = STATIC_PATH . 'img/sj/' . @$randomNum[$index] . '.jpg';//如果有文章置顶，这里可能会导致index not undefined
        $pattern = '/\<img.*?src\=\"(.*?)\"[^>]*>/i';
        $patternMD = '/\!\[.*?\]\((http(s)?:\/\/.*?(jpg|png|JPEG|webp|jpeg|bmp|gif))/i';
        $patternMDfoot = '/\[.*?\]:\s*(http(s)?:\/\/.*?(jpg|png|JPEG|webp|jpeg|bmp|gif))/i';

        if ($howToThumb == '0') {
            return $random;
        } elseif ($howToThumb == '1' || $howToThumb == '2') {

            if (!empty($thumbField)) {
                return $thumbField;
            }

            //解析附件
            if ($widget!=null){
                $attach = @$widget->attachments(1)->attachment;
                if ($attach != null && isset($attach->isImage) && $attach->isImage == 1) {
                    return $attach->url;
                }
            }

            if ($widget != null){
                //解析文章内容，这个是最慢的
                $content = $widget->content;
            }else{
                $content = $extra_content;
            }


            if (preg_match_all($pattern, $content, $thumbUrl)) {
                $thumb = $thumbUrl[1][0];
            } elseif (preg_match_all($patternMD, $content, $thumbUrl)) {
                $thumb = $thumbUrl[1][0];
            } elseif (preg_match_all($patternMDfoot, $content, $thumbUrl)) {
                $thumb = $thumbUrl[1][0];
            } else {//文章中没有图片
                if ($howToThumb == '1') {
                    return '';
                } else {
                    return $random;
                }
            }
            return $thumb;

        } elseif ($howToThumb == '3') {
            if (!empty($thumbField)) {
                return $thumbField;
            } else {
                return $random;
            }
        }else{
            return "";
        }
    }


    /**
     * 处理显示是否有图片地址的逻辑：根据thumb字段和后台外观设置的是否开启头图开关
     * @param $widget
     * @param string $select 判断是首页还是文章页面的头图，因为文章页面头图一定是大头图的
     * @param int $index 序号
     * @param $flag true 表示一定会返回图片，不受设置影响，相册列表页面会使用
     * @return string
     */
    public static function returnHeaderImgSrc($widget, $select, $index = 0, $flag = false)
    {
        $options = mget();
        $thumbChoice = $widget->fields->thumbChoice;
        $thumb = $widget->fields->thumb;
        if ($thumb == "no") {
            $thumbChoice = "no";
        }
        $imgSrc = "";
        $howToThumb = $options->RandomPicChoice;
        $thumbSelect = 0;//0不显示，1全显示，2只显示首页，3只显示文字页面,-1表示不管什么情况都返回头图地址
        if ($flag){
            $thumbSelect = 1;//保证返回图片
            $howToThumb = 2;//保证相册分类一定有图片
        }else{
            if ($thumbChoice == "no") {
                $thumbSelect = 0;
            } else if ($thumbChoice == "yes") {
                $thumbSelect = 1;
            } else if ($thumbChoice == "yes_only_index") {
                $thumbSelect = 2;
            } else if ($thumbChoice == "yes_only_post") {
                $thumbSelect = 3;
            } else if ($thumbChoice == "default" || $thumbChoice == "") {
                if (in_array('NoRandomPic-post', Utils::checkArray( $options->indexsetup)) && in_array('NoRandomPic-index',
                        $options->indexsetup) && $flag == false) {//全部关闭
                    $thumbSelect = 0;
                } else if ((!in_array('NoRandomPic-post', Utils::checkArray( $options->indexsetup)) && !in_array('NoRandomPic-index',
                        $options->indexsetup))) {//全部开启
                    $thumbSelect = 1;
                } else {//一开一闭
                    if (in_array('NoRandomPic-post', Utils::checkArray( $options->indexsetup))) {//不显示文章头图，显示首页头图
                        $thumbSelect = 2;
                    } else {//不显示首页头图，显示文章页面头图
                        $thumbSelect = 3;
                    }

                }
            }
        }


        switch ($thumbSelect) {
            case 0://全部关闭
                break;
            case 1://全部开启
                $imgSrc = Content::whenSwitchHeaderImgSrc($widget, $index, $howToThumb, $widget->fields->thumb);
                break;
            case 2://不显示文章头图，显示首页头图
                if ($select == "post") {
                    $imgSrc = "";
                } else {
                    $imgSrc = Content::whenSwitchHeaderImgSrc($widget, $index, $howToThumb, $widget->fields->thumb);
                }
                break;
            case 3://不显示首页头图，显示文章页面头图
                if ($select == "post") {
                    $imgSrc = Content::whenSwitchHeaderImgSrc($widget, $index, $howToThumb, $widget->fields->thumb);
                } else {
                    $imgSrc = "";
                }
                break;
        }
        return $imgSrc;
    }


    public static function returnSharePostDiv($obj)
    {
        $headImg = Content::returnHeaderImgSrc($obj, "post", 0);
        if (trim($headImg) == "") {//头图为空
            $headImg = STATIC_PATH . 'img/video.jpg';
        }
        $author = $obj->author->screenName;
        $title = $obj->title;
        $url = $obj->permalink;
        if ($obj->fields->album != "") {
            $content = $obj->fields->album;
        } else {
            $content = $obj->excerpt;
        }
        $expert = Content::excerpt($content, 60);
        if (trim($expert) == "") {
            $expert = _mt("暂无文字描述");
        }
        $year = date('Y/m', $obj->date->timeStamp);
        $day = date('d', $obj->date->timeStamp);
        $notice = _mt("扫描右侧二维码阅读全文");
        $image = THEME_URL . 'libs/interface/GetCode.php?type=url&content=' . $url;
        $options = mget();
        //如果开启了开关才需要生成，否则返回空
        if (@in_array("sreenshot", $options->featuresetup) && $obj->is("post")) {

            return <<<EOF
        <style>
        
        .mdx-si-head .cover{
            object-fit: cover;
            width: 100%;
            height: 100%
        }
        
</style>
<div class="mdx-share-img" id="mdx-share-img"><div class="mdx-si-head" style="background-image:url({$headImg})"><p>{$author}</p><span>{$title}</span></div><div 
class="mdx-si-sum">{$expert}</div><div class="mdx-si-box"><span>{$notice}</span><div class="mdx-si-qr" id="mdx-si-qr"><img 
src="{$image}"></div></div><div class="mdx-si-time">{$day}<br><span 
class="mdx-si-time-2">{$year}</span></div></div>
EOF;
        } else {
            return "";
        }
    }


    public static function getPostParseWay()
    {
        return Utils::getPluginOptionValue("parseWay", "origin");
    }

    public static function returnReadModeContent($obj, $status)
    {
        $html = "";
        $author = $obj->author->screenName;
        $time = date("Y 年 m 月 d 日", $obj->date->timeStamp);

        $way = self::getPostParseWay();

        $content = Content::postContent($obj, $status, $way);
        if ($way == "vditor") {
            $core_content = '<div class="loading-post text-center m-t-lg m-b-lg">
                 <p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
             </div><div id="morphing-content-real"></div>
            <textarea class="hide" id="morphing-content-real_text">' . htmlspecialchars($content) . '</textarea>';
        } else {
            $core_content = '<div id="morphing-content-real_origin">' . $content . '</div>';
        }
        $html .= <<<EOF
<div id="morphing-content" class="hidden read_mode_article">
        <div class="page">
            <h1 class="title">$obj->title</h1>
            <div class="metadata singleline"><a href="#" rel="author" class="byline">{$author}</a>&nbsp;•&nbsp;<span class="delimiter"></span><time class="date">{$time}</time></div>     
            {$core_content}
        </div>
    </div>
EOF;
        return $html;
    }

    /**
     * 判断是否是相册分类
     * @param $data
     * @return bool
     */
    public static function isImageCategory($data)
    {
        //print_r($data);
        if (is_array($data)) {
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]["slug"] == "image") {
                    return true;
                }
            }
        }
        return false;

    }

    /**
     * 单个页面输出头图函数
     * @param $obj
     * @param int $index
     * @return string
     */
    public static function exportHeaderImg($obj, $index = 0)
    {

        $parameterArray = array();
        $options = mget();
        $parameterArray['imgSrc'] = Content::returnHeaderImgSrc($obj, "post", $index);
        $parameterArray['thumbDesc'] = $obj->fields->thumbDesc;
        $parameterArray['isIndex'] = false;
        //是否固定图片大小（默认8：3）
        //echo $options->featuresetup.'test';
        if (in_array('FixedImageSize', Utils::checkArray( $options->featuresetup))) {
            $parameterArray['isFixedImg'] = true;
        } else {
            $parameterArray['isFixedImg'] = false;
        }
        return Content::returnPostItem($parameterArray);
    }

    /**
     * 输出文章列表:首页和archive页面
     * @param $obj
     */
    public static function echoPostList($obj)
    {

        //todo 优化性能


        $options = mget();

        $index = 0;

        if ($options->thumbArrangeStyle == "water_fall") {
//            echo '<div class="blog-post water-fall">';
            echo '<div class="blog-post water-fall-container">';
        } else {
            echo '<div class="blog-post">';
        }
//        print_r($obj);
        while ($obj->next()) {
            $parameterArray = array();
            $parameterArray['title'] = $obj->sticky . $obj->title;

            //是否是大版式头图
            $styleThumb = strtoupper($obj->fields->thumbStyle);
            if ($styleThumb == "DEFAULT" || trim($styleThumb) == "") {//跟随外观设置的配置
                if ($options->thumbStyle == "0") {//小头图
                    $parameterArray['thumbStyle'] = "SMALL";
                } else if ($options->thumbStyle == "2") {//交错显示
                    if ($index % 2 == 0) {
                        $parameterArray['thumbStyle'] = "LARGE";
                    } else {
                        $parameterArray['thumbStyle'] = "SMALL";
                    }
                } else if ($options->thumbStyle == "1") {//大头图
                    $parameterArray['thumbStyle'] = "LARGE";
                } else if ($options->thumbStyle == "3") {
                    $parameterArray['thumbStyle'] = "PICTURE";
                } else {//默认是大图
                    $parameterArray['thumbStyle'] = "LARGE";
                }
            } else {
                $parameterArray['thumbStyle'] = $styleThumb;
            }

            if (in_array('NoSummary-index', Utils::checkArray( $options->indexsetup))) {
                $expertNum = 0;
            } else {
                if ($parameterArray['thumbStyle'] == "SMALL") {//小头图
                    if ($options->numberOfSmallPic == "") {//自定义摘要字数
                        $expertNum = 80;
                    } else {
                        $expertNum = $options->numberOfSmallPic;
                    }
                } else {
                    if ($options->numberOfBigPic == "") {//自定义摘要字数
                        $expertNum = 200;
                    } else {
                        $expertNum = $options->numberOfBigPic;
                    }
                }
            }
            if (trim($obj->fields->customSummary) != "") {
                $obj->excerpt = $obj->fields->customSummary;
            }
            $data = Utils::isLock($obj, $index);
            if ($data["flag"]) {//加密分类
                $parameterArray['summary'] = Content::excerpt("", $expertNum);
            } else {
                $parameterArray['summary'] = Content::excerpt($obj->excerpt, $expertNum);
            }

//            $startTime = microtime(true);
            $parameterArray['imgSrc'] = Content::returnHeaderImgSrc($obj, "index", $index);
//            $endTime = microtime(true);
//            print_r(($endTime-$startTime)*1000 . ' ms');

            $parameterArray['linkUrl'] = $obj->permalink;
            /*if (index == 0){
                print_r($obj->author);
            }*/
            $parameterArray['author'] = $obj->author->screenName;
            $parameterArray['authorUrl'] = $obj->author->permalink;
            $parameterArray['date'] = $obj->date->timeStamp;
            $parameterArray['commentNum'] = $obj->commentsNum;
            // 在使用原生评论系统时候，不进行view数目查询，过多的view查询会导致性能问题
            if ($options->commentChoice != "0"){
//                $startTime = microtime(true);
                $parameterArray['viewNum'] = get_post_view($obj);
//                $endTime = microtime(true);
//                print_r(($endTime-$startTime)*1000 . ' ms');
            }else{
                $parameterArray['viewNum'] = 0;
            }
            //是否是首页
            $parameterArray['isIndex'] = true;

            //是否固定图片大小（默认8：3）
            if (in_array('FixedImageSize', Utils::checkArray( $options->featuresetup))) {
                $parameterArray['isFixedImg'] = true;
            } else {
                $parameterArray['isFixedImg'] = false;
            }

            $parameterArray["smallThumbField"] = $obj->fields->thumbSmall;
            $parameterArray["nothumbStyle"] = $obj->fields->noThumbInfoStyle;
            $parameterArray["allowComment"] = $obj->allowComment;


            echo Content::returnPostItem($parameterArray);
            $index++;
        }
        echo '</div>';
    }


    /**
     * @param $parameterArray
     * @return string : 返回单篇文章头部的HTML代码
     * @internal param $title : 标题
     * @internal param $summary : 摘要
     * @internal param $imgSrc : 头图地址
     * @internal param $linkUrl : 文章地址
     * @internal param $author : 作者
     * @internal param $authorUrl : 作者链接
     * @internal param $date : 日期
     * @internal param int $commentNum : 评论数
     * @internal param int $viewNum : 浏览数
     * @internal param bool $isBig : 是否是大版式头图
     * @internal param bool $isIndex : 是否是首页
     * @internal param bool $isFixedImg : 是否固定图片大小（默认8：3）
     */
    public static function returnPostItem($parameterArray)
    {
        $options = mget();

        if ($parameterArray['isIndex']) {
            //格式化时间
            if ($parameterArray['date'] != 0) {
                $dateString = date(I18n::dateFormat(), $parameterArray['date']);
            }
            //格式化评论数
            if ($parameterArray['allowComment'] == 1) {
                if ($parameterArray['commentNum'] == 0) {
                    $commentNumString = _mt("暂无评论");
                } else {
                    $commentNumString = $parameterArray['commentNum'] . " " . _mt("条评论");
                }
            } else {
                $commentNumString = _mt("关闭评论");
            }
            //格式化浏览次数
            $viewNumString = $parameterArray['viewNum'] . " " . _mt("次浏览");
        }

        $html = "";
        //首页文章
        if ($parameterArray['isIndex']) {//首页界面的文章item结构
            //头图部分
            if ($parameterArray['imgSrc'] == "" && $parameterArray['thumbStyle'] !== "PICTURE") {//图片地址为空即不显示头图
                $html .= '<div class="single-post panel box-shadow-wrap-normal">';
            } else {
                if ($parameterArray['thumbStyle'] == "LARGE") {//大版式头图
                    $backgroundImageHtml = Utils::returnDivLazyLoadHtml($parameterArray['imgSrc'], 1200, 0);
                    if ($parameterArray['isFixedImg']) {//裁剪头图
                        $html .= <<<EOF
<div class="single-post panel box-shadow-wrap-normal">
    <div class="index-post-img">
        <a href="{$parameterArray['linkUrl']}">
            <div class="item-thumb lazy" {$backgroundImageHtml}>
</div>
           
        </a>
    </div>
EOF;
                    } else {//头图没有裁剪
                        $imageHtml = Utils::returnImageLazyLoadHtml(false, $parameterArray['imgSrc'], 1200, 0);
                        $html .= <<<EOF
<div class="single-post panel box-shadow-wrap-normal">
<div class="index-post-img"><a href="{$parameterArray['linkUrl']}"><img {$imageHtml} 
class="img-full lazy" /></a></div>
EOF;
                    }
                } else if ($parameterArray['thumbStyle'] == "SMALL") {//小版式头图
                    if (trim($parameterArray['smallThumbField']) !== "") {//小头图专用字段
                        $parameterArray['imgSrc'] = $parameterArray['smallThumbField'];
                    }
                    $backgroundImageHtml = Utils::returnDivLazyLoadHtml($parameterArray['imgSrc'], 800, 0);
                    $html .= <<<EOF
<div class="panel-small single-post box-shadow-wrap-normal">
    <div class="index-post-img-small post-feature index-img-small">
        <a href="{$parameterArray['linkUrl']}">
            <div class="item-thumb-small lazy" {$backgroundImageHtml}></div>
        </a>
    </div>
EOF;
                } else {//图片版式
                    //TODO: picture
                    $backgroundImageHtml = Utils::returnDivLazyLoadHtml($parameterArray['imgSrc'], 1200, 0);
                    $html .= <<<EOF
<div class="single-post panel-picture border-radius-6 box-shadow-wrap-normal">
                <figure class="post-thumbnail border-radius-6">
                    <a class="post-thumbnail-inner index-image lazy" href="{$parameterArray['linkUrl']}" 
                    $backgroundImageHtml>  </a>
                </figure>
                <header class="entry-header wrapper-lg">
                    <h3 class="m-t-none text-ellipsis index-post-title"><a href="{$parameterArray['linkUrl']}" rel="bookmark" tabindex="0" data-pjax-state="">{$parameterArray['title']}</a></h3>
                    <div class="entry-meta">
                        <span class="byline"><span class="author vcard"><a  href="{$parameterArray['linkUrl']}" tabindex="0">{$parameterArray['summary']}</a></span></span>
                    </div>
                </header>
</div>
EOF;
                }
            }
            //图片样式不用添加这部分
            if ($parameterArray['thumbStyle'] !== "PICTURE") {
                //标题部分
                $html .= <<<EOF
<div class="post-meta wrapper-lg">
EOF;
                //个性化徽标
                if ($parameterArray['imgSrc'] == "" && $parameterArray['nothumbStyle'] != "default" && $parameterArray['nothumbStyle'] != "") { //无头图且显示个性化徽标
                    $style = $parameterArray['nothumbStyle'];
                    $html .= <<<EOF
<div class="item-meta-ico bg-ico-$style"></div>
EOF;
                }
                $html .= <<<EOF
    <h2 class="m-t-none text-ellipsis index-post-title text-title"><a 
    href="{$parameterArray['linkUrl']}">{$parameterArray['title']}</a></h2>
EOF;
                //if (!in_array('multiStyleThumb', Utils::checkArray($options->indexsetup))){
                //摘要部分
                $html .= <<<EOF
<p class="summary l-h-2x text-muted">{$parameterArray['summary']}</p>
EOF;
                //}
                //页脚部分，显示评论数、作者等信息
                $html .= <<<EOF
<div class="line line-lg b-b b-light"></div>
<div class="text-muted post-item-foot-icon text-ellipsis list-inline">
<li>
<span class="m-r-sm right-small-icons"><i data-feather="user"></i></span><a href="{$parameterArray['authorUrl']}">{$parameterArray['author']}</a></li>

<li><span class="right-small-icons m-r-sm"><i data-feather="clock"></i></span>{$dateString}</li>
EOF;
                if ($options->commentChoice == '0') {
                    $html .= <<<EOF
<li><span class="right-small-icons m-r-sm"><i 
data-feather="message-square"></i></span><a href="{$parameterArray['linkUrl']}#comments">{$commentNumString}</a></li>
EOF;

                } else {
                    $html .= <<<EOF
<li><span class="right-small-icons m-r-sm"><i data-feather="eye"></i></span><a 
href="{$parameterArray['linkUrl']}#comments">{$viewNumString}</a></li>
EOF;

                }
                $html .= <<<EOF
</div><!--text-muted-->
</div><!--post-meta wrapper-lg-->
</div><!--panel/panel-small-->

EOF;
            } else {//picture头图样式


            }
        } else {//文章页面的item结构，只有头图，没有其他的了
            if ($parameterArray['imgSrc'] !== "") {
                $copyright = "";
                if (trim($parameterArray['thumbDesc']) != "") {
                    $copyright = '<div class="img_copyright" data-toggle="tooltip" data-placement="left" data-original-title="' . $parameterArray['thumbDesc'] . '"><i class="glyphicon glyphicon-copyright-mark"></i></div>';
                }
                //显示头图版权信息

                if ($parameterArray['isFixedImg']) {//固定头图大小
                    $html .= '<div class="entry-thumbnail" aria-hidden="true"><div class="item-thumb lazy" ' . Utils::returnDivLazyLoadHtml($parameterArray['imgSrc'], 1200, 0) . '>' . $copyright . '</div></div>';
                } else {
                    $html .= '<div class="entry-thumbnail" aria-hidden="true"><img width="100%" height="auto" '
                        . Utils::returnImageLazyLoadHtml(false, $parameterArray['imgSrc'], 1200, 0) . ' 
 class="img-responsive lazy" />' . $copyright . '</div>';
                }
            }
        }
        return $html;
    }


    /**
     * 浏览器顶部标题
     * @param $obj
     * @param $title
     * @param $currentPage
     */
    public static function echoTitle($obj, $title, $currentPage)
    {
        $options = mget();
        if ($currentPage > 1) {
            echo '第' . $currentPage . '页 - ';
        }
        $obj->archiveTitle(array(
            'category' => _mt('分类 %s 下的文章'),
            'search' => _mt('包含关键字 %s 的文章'),
            'tag' => _mt('标签 %s 下的文章'),
            'author' => _mt('%s 发布的文章')
        ), '', ' - ');
        echo $title;

        $titleIntro = $options->titleintro;
        if ($obj->is('index') && trim($titleIntro) !== "") {
            echo ' - ' . $options->titleintro . '';
        }
    }



    /**
     * 短代码解析正则替换回调函数
     * @param $matches
     * @return bool|string
     */
    public static function scodeParseCallback($matches)
    {
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }
        //[scode type="share"]这是灰色的短代码框，常用来引用资料什么的[/scode]
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
        $type = "info";
        switch (@$attrs['type']) {
            case 'yellow':
                $type = "warning";
                break;
            case 'red':
                $type = "error";
                break;
            case 'lblue':
                $type = "info";
                break;
            case 'green':
                $type = "success";
                break;
            case 'share':
                $type = "share";
                break;
        }
        return '<div class="tip inlineBlock ' . $type . '">' . "\n\n" . $matches[5] . "\n" . '</div>';
    }

    /**
     * 文章内相册解析
     * @param $matches
     * @return bool|string
     */
    public static function scodeAlbumParseCallback($matches)
    {
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        //[scode type="share"]这是灰色的短代码框，常用来引用资料什么的[/scode]
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数

        $content = $matches[5];


        return Content::parseContentToImage($content, @$attrs["type"]);


    }

    public static function scodeLinkParseCallback($matches){

        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        //[scode type="share"]这是灰色的短代码框，常用来引用资料什么的[/scode]
        $content = $matches[5];
        $pattern = self::get_shortcode_regex(array('item'));
        preg_match_all("/$pattern/", $content, $matches);
        $ret = '<div class="list-group list-group-lg list-group-sp row" style="margin: 0">';

        for ($i = 0; $i < count($matches[3]); $i++) {
            $item = $matches[3][$i];
            $attr = htmlspecialchars_decode($item);//还原转义前的参数列表
            $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
            $name = @$attrs["name"];
            $link = @$attrs["link"];
            $pic = @$attrs["pic"];
            $desc = @$attrs["desc"];

            if (empty($name)){
                $name = _mt("不知名");
            }

            if (empty($link)){
                $link = "http://example.com";
            }

            if (empty($desc)){
                $desc = "一个神秘的人";
            }

            if (empty($pic)){
                $options = Typecho_Widget::widget('Widget_Options');
                $pic = Typecho_Common::url('/usr/plugins/Handsome/assets/image/nopic.jpg',
                    $options->siteUrl);
            }


            $ret .= <<<EOF
<div class="col-sm-6">
<a href="{$link}" target="_blank" class="no-external-link list-group-item no-borders 
box-shadow-wrap-lg"> <span class="pull-left thumb-sm avatar m-r"> <img noGallery 
src="{$pic}" alt="Error" class="img-square"> <i class="bg-danger right"></i> </span> <span class="clear"><span 
class="text-ellipsis">
  {$name}</span> <small class="text-muted clear text-ellipsis">{$desc}</small> </span> </a>
</div>
EOF;
        }

        $ret .="</div>";

        return $ret;
    }

    public static function scodeFontParseCallback($matches){
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        $content = $matches[5];
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
        $ret = "<span style='";

        foreach ($attrs as $key => $value){
            $ret .= $key .":" .$value;
        }
        $ret.="'>".$content ."</span>";
        return $ret;

    }

    public static function scodeGoalParseCallback($matches)
    {
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        //[scode type="share"]这是灰色的短代码框，常用来引用资料什么的[/scode]
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数

        $title = (@$attrs["title"]) ? $attrs["title"] : _mt("小目标");
        $ret = <<< EOF
<div class="panel panel-default  box-shadow-wrap-lg goal-panel">
    <div class="panel-heading">
        {$title}
    </div>
    <div class="list-group">
   
EOF;
        $content = $matches[5];
        $pattern = self::get_shortcode_regex(array('item'));
        preg_match_all("/$pattern/", $content, $matches);
        $ret .= '<div class="list-group-item">';

        for ($i = 0; $i < count($matches[3]); $i++) {
            $item = $matches[3][$i];
            $text = $matches[5][$i];
            $attr = htmlspecialchars_decode($item);//还原转义前的参数列表
            $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
//            print_r($attrs);
            if (@$attrs["progress"] || @$attrs["start"]) {//进度条

                $start = @$attrs["start"];
                $end = @$attrs["end"];

                if (!empty($start) && !empty($end)) {
                    $progress = (time() - strtotime($start)) * 1.0 / (strtotime($end) - strtotime($start));
                } else {
                    $progress = (float)@$attrs["progress"] / 100;
                }

                $color = "warning";
                if ($progress > 0 && $progress < 0.3) {
                    $color = "danger";
                } elseif ($progress > 0.6 && $progress < 0.8) {
                    $color = "info";
                } else if ($progress >= 0.8) {
                    $color = "success";
                }

                $progress = round($progress, 2) * 100 . "%";

                //根据进度自动选择颜色
                $ret .= <<<EOF
            <p class="goal_name">{$text}：</p>
            <div class="progress-striped active m-b-sm progress" value="dynamic" type="danger">
                <div class="progress-bar progress-bar-{$color}" role="progressbar" aria-valuenow="97" aria-valuemin="0"
                 aria-valuemax="100" style="width: {$progress};"><span> {$progress} </span></div>
            </div>
EOF;
            } else {//to do类型
                $isCheck = (@$attrs["check"] == "true") ? 'checked=""' : "";
                $ret .= <<< EOF
<div class="checkbox">
                <label class="i-checks">
                    <input type="checkbox" {$isCheck} disabled="" value="">
                    <i></i>
                    {$text}
                </label>
</div>
EOF;
            }

        }
        $ret .= '</div>';

        $ret .= '</div></div>';
        return $ret;
    }


    public static function scodeTimelineParseCallback($matches)
    {
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        //[scode type="share"]这是灰色的短代码框，常用来引用资料什么的[/scode]
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
        $title = (@$attrs["title"]) ? $attrs["title"] : _mt("大事记");
        $type = (@$attrs["type"]) ? $attrs["type"] : "small";
        $random = (@$attrs["random"]) ? $attrs["random"] : "true";
        $random = ($random == "true");
        $start = (@$attrs["start"]);
        $end = (@$attrs["end"]);

        $ret = <<<EOF
<div class="panel panel-default  box-shadow-wrap-lg goal-panel">
<div class="panel-heading">
        {$title}
    </div>
<div class="padder-md wrapper">     
EOF;

        if ($type == "small") {
            $ret .= '<div class="streamline b-l m-b">';
        } else {
            $ret .= '<ul class="timeline">';
            if (!empty($start)) {
                $ret .= Utils::getTimelineHeader($start, $type, -1, false);
            }

        }

        $content = $matches[5];
        $pattern = self::get_shortcode_regex(array('item'));
        preg_match_all("/$pattern/", $content, $matches);

        //颜色的随机选择
        for ($i = 0; $i < count($matches[3]); $i++) {
            $item = $matches[3][$i];
            $text = trim($matches[5][$i]);
            if ($type == "small") {//小样式过滤换行，不然样式会变的很难看
                $text = str_replace(array("/r/n", "/r", "/n"), "", $text);
            }
            $attr = htmlspecialchars_decode($item);//还原转义前的参数列表
            $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
            $date = @$attrs["date"];
            $color_ = @$attrs["color"];
            if ($color_ == "") {
                $color_ = "light";
            }

            if (!empty($date)) {//有日期
                $ret .= Utils::getTimeLineItem($date, $text, $type, $i, $random, $color_);
            } else {//没有日期，只显示文字
                $ret .= Utils::getTimelineHeader($text, $type, $i, $random, $color_);

            }
        }


        if ($type == "small") {
            $ret .= '</div>';
        } else {
            if (!empty($end)) {
                $ret .= Utils::getTimelineHeader($end, $type, -1, false, "light");
            }
            $ret .= '</ul>';
        }
        $ret .= '</div></div>';
        return $ret;
    }

    /**
     * 文档助手markdown正则替换回调函数
     * @param $matches
     * @return string
     */
    public static function sCodeMarkdownParseCallback($matches)
    {
        $type = "info";
        switch ($matches[1]) {
            case '!':
                $type = "warning";
                break;
            case 'x':
                $type = "error";
                break;
            case 'i':
                $type = "info";
                break;
            case '√':
                $type = "success";
                break;
            case '@':
                $type = "share";
                break;
        }
        return '<div class="tip inlineBlock ' . $type . '">' . $matches[2] . '</div>';
        //return $matches[2];
    }

    /**
     * 私密内容正则替换回调函数
     * @param $matches
     * @return bool|string
     */
    public static function secretContentParseCallback($matches)
    {
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

//        var_dump($matches[5]);
//        return '</p><div class="hideContent"><p>'. $matches[5] . '</p></div><p>';
        return '<div class="hideContent">'."\n" . $matches[5] .'</div>';
    }


    public static function tagParseCallback($matches)
    {
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
        $type = @$attrs["type"];
        if ($type == "") {
            $type = "light dk";
        }
        $content = $matches[5];

        return '<span class="label bg-' . $type . '">' . $content . '</span>';

    }

    public static function tabsParseCallback($matches)
    {
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        $content = $matches[5];

        $pattern = self::get_shortcode_regex(array('tab'));
        preg_match_all("/$pattern/", $content, $matches);
        $tabs = "";
        $tabContents = "";
        for ($i = 0; $i < count($matches[3]); $i++) {
            $item = $matches[3][$i];
            $text = $matches[5][$i];
            $id = "tabs-" . md5(uniqid()) . rand(0, 100) . $i;
            $attr = htmlspecialchars_decode($item);//还原转义前的参数列表
            $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
            $name = @$attrs['name'];
            $active = @$attrs['active'];
            $in = "";
            $style = "style=\"";
            foreach ($attrs as $key => $value) {
                if ($key !== "name" && $key !== "active") {
                    $style .= $key . ':' . $value . ';';
                }
            }
            $style .= "\"";

            if ($active == "true") {
                $active = "active";
                $in = "in";
            } else {
                $active = "";
            }
            $tabs .= "<li class='nav-item $active' role=\"presentation\"><a class='nav-link $active' $style data-toggle=\"tab\" 
aria-controls='" . $id . "' role=\"tab\" data-target='#$id'>$name</a></li>";
            $tabContents .= "<div role=\"tabpanel\" id='$id' class=\"tab-pane fade $active $in\">
            $text</div>";
        }


        return <<<EOF
<div class="tab-container post_tab box-shadow-wrap-lg">
<ul class="nav no-padder b-b scroll-hide" role="tablist">
{$tabs}
</ul>
<div class="tab-content no-border">
{$tabContents}
</div>
</div>
EOF;
    }

    public static function QRParseCallback($matches)
    {
        $options = mget();
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
        $title = @$attrs["title"];
        $sub = $attrs["sub"];
        $desc = $attrs["desc"];
        $url = @$attrs["url"];
        $img = THEME_URL . "libs/interface/GetCode.php?type=url&content=" . $url;

        return <<<EOF
<div class="m-b-md text-center countdown border-radius-6 box-shadow-wrap-normal">
<div class="r item hbox no-border">
<div class="col bg-light  w-210 v-middle wrapper-md">
<div class="entry-title font-thin h4 text-black l-h margin-b" ><span>{$title}</span></div>
<div class="font-thin h5"><span>{$sub}</span></div>
</div>
<div class="col bg-white padder-v r-r vertical-flex">
<div class="row text-center no-gutter w-full padder-sm ">
<div class="font-thin">
<img class="img-QR" src="{$img}" />
<span class="font-bold">{$desc}</span>
</div>
</div>             
</div>
</div>
</div>
EOF;


    }

    /**
     * @param $matches
     * @return bool|string
     */
    public static function countdownParseCallback($matches)
    {
        $options = mget();
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
        $id = "countdown-" . md5(uniqid()) . rand(0, 100);
        $end = $attrs["time"];
        $title = @$attrs['title'];
        $head = "";
        if (trim($title) !== "") {
            $head = <<<EOF
            <div class="entry-title font-thin h4 text-black l-h margin-b" ><span>{$title}</span></div>
EOF;
        }
        $link = @$attrs["url"];
        $button = "";

        if (trim($link) !== "") {
            $button .= <<<EOF
<a class="btn m-b-xs btn-danger btn-addon margin-t" href="$link" target="_blank">
查看详情</a>
EOF;
        }
        $desc = @$attrs["desc"];
        if (trim($desc) == "") {
            $desc = _mt("倒计时");
        }
        $html = "";
        $js = "";
        //当前距离截止时间的秒数
        $leaveTime = strtotime($end);
        if ($leaveTime < 0) {

            $html .= <<<EOF
<div class="m-b-md text-center countdown border-radius-6 box-shadow-wrap-normal" id="{$id}">
              <div class="r item hbox no-border">
                <div class="col bg-light  w-210 v-middle wrapper-md">
                    $head
                  <div class="font-thin h5"><span>{$desc}</span></div>
                  $button
                </div>
                <div class="col bg-white padder-v r-r vertical-flex">
                <div class="row text-center no-gutter w-full">
                     <div class="font-thin text-muted"><span>已结束，截止日期：{$end}</span></div>

                </div>             
                 </div>
            </div>
</div>
EOF;
        } else {
            $html .= <<<EOF
<div class="m-b-md text-center countdown border-radius-6 box-shadow-wrap-normal" id="{$id}">
              <div class="r item hbox no-border">
                <div class="col bg-light  w-210 v-middle wrapper-md">
                  $head
                  <div class="font-thin h5"><span>{$desc}</span></div>
                  $button
                </div>
                <div class="col bg-white padder-v r-r vertical-flex">
                <div class="row text-center no-gutter w-full">
                <div class="col-xs-3">
                    <div class="inline m-t-sm">
                  <div  class="easyPieChart pie-days">
                    <div class="text-muted">
                      <span class="span-days">0</span>天
                    </div>
                </div>
              </div>
              </div>
              
              <div class="col-xs-3">
                <div class="inline m-t-sm">
                  <div class="easyPieChart pie-hours">
                    <div class="text-muted ">
                      <span class="span-hours">0</span>小时
                    </div>
                </div>
              </div>
              </div>
              
              <div class="col-xs-3">
                <div class="inline m-t-sm">
                  <div class="easyPieChart pie-minutes">
                    <div class="text-muted">
                      <span class="span-minutes">0</span>分钟
                    </div>
                </div>
              </div>
            </div>
            
            <div class="col-xs-3">
                <div class="inline m-t-sm">
                  <div class="easyPieChart pie-seconds">
                    <div class="text-muted">
                      <span class="span-seconds">0</span>秒
                    </div>
                </div>
              </div>
            </div>
            
                </div>             
                 </div>
            </div>
</div>
EOF;

            $js .= <<<EOF
<script>
$(function() {
     $.Module_Timer({
     
        startTime: $leaveTime,
        id: '$id'
    });
});         
</script>
EOF;

            $html .= $js;
        }
        return $html;


    }

    /**
     * 折叠框解析
     * @param $matches
     * @return bool|string
     */
    public static function collapseParseCallback($matches)
    {
        // 不解析类似 [[player]] 双重括号的代码
//        var_dump($matches);
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数

        $title = $attrs['title'];
        $default = @$attrs['status'];
        if ($default == null || $default == "") {
            $default = "true";
        }
        if ($default == "false") {//默认关闭
            $class = "collapse";
        } else {
            $class = "collapse in";
        }
        $class.=" collapse-content";
        $content = $matches[5];
        $notice = _mt("开合");
        $id = "collapse-" . md5(uniqid()) . rand(0, 100);

        return <<<EOF
<div class="panel panel-default collapse-panel box-shadow-wrap-lg"><div class="panel-heading panel-collapse" data-toggle="collapse" data-target="#{$id}" aria-expanded="true"><div class="accordion-toggle"><span>{$title}</span>
<i class="pull-right fontello icon-fw fontello-angle-right"></i>
</div>
</div>
<div class="panel-body collapse-panel-body">
<div id="{$id}" class="{$class}">{$content}<div style="height: 
15px"></div></div></div></div>
EOF;


    }

    /**
     * 音乐解析的正则替换回调函数
     * @param $matches
     * @return bool|string
     * url - 自定义mp3链接的地址
     * title - 歌曲名称
     * author - 歌曲作者 --> artist
     * media - 云解析媒体 --> server
     */
    public static function musicParseCallback($matches)
    {
        /*
        $mathes array
        * 1 - An extra [ to allow for escaping shortcodes with double [[]]
        * 2 - 短代码名称
        * 3 - 短代码参数列表
        * 4 - 闭合标志
        * 5 - 内部包含的内容
        * 6 - An extra ] to allow for escaping shortcodes with double [[]]
     */

        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        //[hplayer media=&quot; netease&quot; type=&quot; song&quot;  id=&quot; 23324242&quot; /]
        //对$matches[3]的url如果被解析器解析成链接，这些需要反解析回来
        $matches[3] = preg_replace("/<a href=.*?>(.*?)<\/a>/", '$1', $matches[3]);
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数，最外层短代码的参数，旧版本中 = 歌曲信息 + 设置信息 新版本中 = 设置信息

        //获取内部内容
        $pattern = self::get_shortcode_regex(array('Music'));
        preg_match_all("/$pattern/", $matches[5], $all);

        $playerCode = self::parsePlayerAttribute($attrs);

        //播放器内部的歌曲/歌曲列表
        if (sizeof($all[3])) {
            //当内部有内容时候，可能是一首歌曲或者多首歌曲
            foreach ($all[3] as $vo) {
                $t = self::shortcode_parse_atts(htmlspecialchars_decode($vo));
                $playerCode .= self::parseSingleMusic($t, $attrs);
            }
        } else {
            //旧版本兼容，单首歌曲解析
            $playerCode .= self::parseSingleMusic($attrs, $attrs);

        }

        $playerCode .= "</div>\n";//player 结束符号

        return $playerCode;

    }

    public static function parsePlayerAttribute($setting, $isGlobal = false)
    {
        //播放器默认设置
        $player = array(
            'preload' => 'auto',
            'autoplay' => 'false',
            'listMaxHeight' => '340px',
            'order' => 'list',
        );
        $global = ($isGlobal) ? " player-global" : "";
        $head = "<div class='handsome_aplayer" . $global . "'";

        if (is_array($setting)) {
            foreach ($setting as $key => $vo) {
                $player[$key] = $vo;
            }
        }
        foreach ($player as $key => $vo) {
            $head .= " data-{$key}=\"{$vo}\"";
        }
        $head .= ">\n";
        return $head;

    }


    public static function parseGlobalPlayer()
    {
        $options = mget();
        $content = $options->musicUrl;

        $arr = explode("\n", $content);
        $setting = [
            'listMaxHeight' => '200px',
            "theme" => '#8ea9a7',
            "listFolded" => 'true',
            "preload" => 'false',
            "autoplay" => 'false'
        ];//全局播放器设置
        $playerCode = self::parsePlayerAttribute($setting, true);

        foreach ($arr as $item) {
            //判断是否是json数组
            if (trim($item) == "") {
                //跳过
            } else {
                //末尾如果又逗号需要去掉
                if(substr($item,-1) == ","){
                    $item = substr($item,0,-1);
                }

                $music = @json_decode($item, true);//本地音乐的json解析
//                print_r($item);
                if (!is_array($music)) {//直接云解析连接
                    $music = Utils::parseMusicUrlText($item);
                }

                if (is_array($music) && !empty($music)) {
                    $playerCode .= self::parseSingleMusic($music, []);
                }
            }
        }

        $playerCode .= "</div>\n";//player 结束符号
        return $playerCode;
    }

    public static function parseSingleMusic($info, $setting)
    {

        $artist = @$info['artist'] ? $info['artist'] : @$info['author'];
        $server = @$info['server'] ? $info['server'] : @$info['media'];

        //todo
        $auth = "auth";

        if (@$info['id'] !== null) {//云解析
            $playCode = '<div class="handsome_aplayer_music" data-id="' . @$info['id'] . '" data-server="'
                . $server . '" data-type="'
                . $info['type'] . '" data-auth="' . $auth . '"';
        } else {//本地资源
            $playCode = '<div class="handsome_aplayer_music" data-name="' . @$info['title'] . '" data-artist="'
                . $artist
                . '" data-url="'
                . @$info['url'] . '" data-cover="' . @$info['pic'] . '" data-lrc="' . @$info['lrc'] . '"';
        }


        $playCode .= "></div>\n";


        return $playCode;

    }

    public static function returnLinkList($sort, $id)
    {


        $mypattern = <<<eof
<div class="col-sm-6">
<a href="{url}" target="_blank" class="list-group-item no-borders box-shadow-wrap-lg"> <span 
class="pull-left thumb-sm avatar m-r"> <img 
  src={image} alt="Error" class="img-square" /> <i class="{color} right"></i> </span> <span class="clear"><span class="text-ellipsis">
  {name}</span> <small 
  class="text-muted clear text-ellipsis">{title}</small> </span> </a>
</div>
eof;

        return '<div class="tab-pane fade in" id="' . $id . '"><div class="list-group list-group-lg list-group-sp row" style="margin: 0">' . Handsome_Plugin::output_str($mypattern, 0, $sort) . '</div></div>';
    }


    /**
     * 视频解析的回调函数
     * @param $matches
     * @return bool|string
     */
    public static function videoParseCallback($matches)
    {
        // 不解析类似 [[player]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }
        //对$matches[3]的url如果被解析器解析成链接，这些需要反解析回来
        $matches[3] = preg_replace("/<a href=.*?>(.*?)<\/a>/", '$1', $matches[3]);
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
        if ($attrs['url'] !== null || $attrs['url'] !== "") {
            $url = $attrs['url'];
        } else {
            return "";
        }

        if (array_key_exists('pic', $attrs) && ($attrs['pic'] !== null || $attrs['pic'] !== "")) {
            $pic = $attrs['pic'];
        } else {
            $pic = STATIC_PATH . 'img/video.jpg';
        }
        $playCode = '<video src="' . $url . '" style="background-image:url(' .
            $pic . ');background-size: cover;"></video>';

        //把背景图片作为第一帧
//        $playCode = '<video src="' . $url . '" poster="'.$pic.'"></video>';

        return $playCode;

    }

    public static function emojiParseCallback($matches)
    {
        $emotionPathPrefix = THEME_FILE . 'assets/img/emotion';
        $emotionUrlPrefix = STATIC_PATH . 'img/emotion';
        $path = $emotionPathPrefix . '/' . @$matches[1] . '/' . @$matches[2] . '.png';
        $url = $emotionUrlPrefix . '/' . @$matches[1] . '/' . @$matches[2] . '.png';
        //检查图片文件是否存在
        if (is_file($path) == true) {
            return '<img src="' . $url . '" class="emotion-' . @$matches[1] . '">';
        } else {
            return @$matches[0];
        }
    }


    /**
     * 一篇文章中引用另一篇文章正则替换回调函数
     * @param $matches
     * @return bool|string
     */
    public static function quoteOtherPostCallback($matches)
    {
        $options = mget();
        // 不解析类似 [[post]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }

        //对$matches[3]的url如果被解析器解析成链接，这些需要反解析回来
        $matches[3] = preg_replace("/<a href=.*?>(.*?)<\/a>/", '$1', $matches[3]);
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数

        //这里需要对id做一个判断，避免空值出现错误
        $cid = @$attrs["cid"];
        $url = @$attrs['url'];
        $cover = @$attrs['cover'];//封面
        $targetTitle = "";//标题
        $targetUrl = "";//链接
        $targetSummary = "";//简介文字
        $targetImgSrc = "";//封面图片地址
        if (!empty($cid)) {
            $db = Typecho_Db::get();
            $prefix = $db->getPrefix();
            $posts = $db->fetchAll($db
                ->select()->from($prefix . 'contents')
                ->orWhere('cid = ?', $cid)
                ->where('type = ? AND status = ? AND password IS NULL', 'post', 'publish'));
            //这里需要对id正确性进行一个判断，避免查找文章失败
            if (count($posts) == 0) {
                $targetTitle = "文章不存在，或文章是加密、私密文章";
            } else {
                $result = Typecho_Widget::widget('Widget_Abstract_Contents')->push($posts[0]);
                if ($cover == "" || $cover == "http://") {

                    $thumbArray = $db->fetchAll($db
                        ->select()->from($prefix . 'fields')
                        ->orWhere('cid = ?', $cid)
                        ->where('name = ? ', 'thumb'));
                    $targetImgSrc = Content:: whenSwitchHeaderImgSrc(null, 0, 2,
                        @$thumbArray[0]['str_value'],$result['text']);

                } else {
                    $targetImgSrc = $cover;
                }
                $targetSummary = Content::excerpt(Markdown::convert($result['text']), 60);
                $targetTitle = $result['title'];
                $targetUrl = $result['permalink'];
            }
        } else if (empty($cid) && $url !== "") {
            $targetUrl = $url;
            $targetSummary = @$attrs['intro'];
            $targetTitle = @$attrs['title'];
            $targetImgSrc = $cover;
        } else {
            $targetTitle = "文章不存在，请检查文章CID";
        }

        $imageHtml = "";
        $noImageCss = "";
        if (trim($targetImgSrc) !== "") {
            $imageHtml = '<div class="inner-image bg" style="background-image: url(' . $targetImgSrc . ');background-size: cover;"></div>
';
        } else {
            $noImageCss = 'style="margin-left: 10px;"';
        }

        return <<<EOF
<div class="preview">
<div class="post-inser post box-shadow-wrap-normal">
<a href="{$targetUrl}" target="_blank" class="post_inser_a no-external-link">
{$imageHtml}
<div class="inner-content" $noImageCss>
<p class="inser-title">{$targetTitle}</p>
<div class="inster-summary text-muted">
{$targetSummary}
</div>
</div>
</a>
<!-- .inner-content #####-->
</div>
<!-- .post-inser ####-->
</div>
EOF;

    }

    /**
     * 解析显示按钮的短代码的正则替换回调函数
     * @param $matches
     * @return bool|string
     */
    public static function parseButtonCallback($matches)
    {
        // 不解析类似 [[post]] 双重括号的代码
        if ($matches[1] == '[' && $matches[6] == ']') {
            return substr($matches[0], 1, -1);
        }
        //对$matches[3]的url如果被解析器解析成链接，这些需要反解析回来
        /*$matches[3] = preg_replace("/<a href=.*?>(.*?)<\/a>/",'$1',$matches[3]);*/
        $attr = htmlspecialchars_decode($matches[3]);//还原转义前的参数列表
        $attrs = self::shortcode_parse_atts($attr);//获取短代码的参数
        $type = "";
        $color = "primary";
        $icon = "";
        $addOn = " ";
        $linkUrl = "";
        if (@$attrs['type'] == "round") {
            $type = "btn-rounded";
        }
        if (@$attrs['url'] != "") {
            $linkUrl = 'window.open("' . $attrs['url'] . '","_blank")';
        }
        if (@$attrs['color'] != "") {
            $color = $attrs['color'];
        }

        if (@$attrs['icon'] != "") {//判断是否是feather 图标
            if (count(mb_split(" ", $attrs['icon'])) > 1 || strpos($attrs['icon'], "fontello") !== false || strpos
                ($attrs['icon'], "glyphicon") !== false) {
                $icon = '<i class="' . $attrs['icon'] . '"></i>';
            } else {
                $icon = '<i><i data-feather="' . $attrs['icon'] . '"></i></i>';
            }
            $addOn = 'btn-addon';
        }

        return <<<EOF
<button class="btn m-b-xs btn-{$color} {$type}{$addOn}" onclick='{$linkUrl}'>{$icon}{$matches[5]}</button>
EOF;
    }


    /**
     * 解析时光机页面的评论内容
     * @param $content
     * @param $children
     * @return string
     */
    public static function timeMachineCommentContent($content, $children)
    {
        return Content::parseContentPublic($content);
    }


    /**
     * 一些公用的解析，文章、评论、时光机公用的，与用户状态无关
     * @param $content
     * @return null|string|string[]
     */
    public static function parseContentPublic($content)
    {
        $options = mget();


        //链接转二维码
        if (strpos($content, '[QR') !== false) {
            $pattern = self::get_shortcode_regex(array('QR'));
            $content = preg_replace_callback("/$pattern/", array('Content', 'QRParseCallback'),
                $content);
        }

        //倒计时
        if (strpos($content, '[countdown') !== false) {
            $pattern = self::get_shortcode_regex(array('countdown'));
            $content = preg_replace_callback("/$pattern/", array('Content', 'countdownParseCallback'),
                $content);
        }


        //文章中折叠框功能
        if (strpos($content, '[collapse') !== false) {
            $pattern = self::get_shortcode_regex(array('collapse'));
            $content = preg_replace_callback("/$pattern/", array('Content', 'collapseParseCallback'),
                $content);
        }

        //文章中标签页的功能
        if (strpos($content, '[tabs') !== false) {
            $pattern = self::get_shortcode_regex(array('tabs'));
            $content = preg_replace_callback("/$pattern/", array('Content', 'tabsParseCallback'),
                $content);
        }

        //文章中标签功能
        if (strpos($content, '[tag') !== false) {
            $pattern = self::get_shortcode_regex(array('tag'));
            $content = preg_replace_callback("/$pattern/", array('Content', 'tagParseCallback'),
                $content);
        }

        //文章中播放器功能
        if (strpos($content, '[hplayer') !== false) {//提高效率，避免每篇文章都要解析
            $pattern = self::get_shortcode_regex(array('hplayer'));
            $content = Utils::handle_preg_replace_callback("/$pattern/", array('Content', 'musicParseCallback'), $content);
        }

        //文章中视频播放器功能
        if (strpos($content, '[vplayer') !== false) {//提高效率，避免每篇文章都要解析
            $pattern = self::get_shortcode_regex(array('vplayer'));
            $content = Utils::handle_preg_replace_callback("/$pattern/", array('Content', 'videoParseCallback'), $content);
        }

        //解析文章中的表情短代码
        $content = Utils::handle_preg_replace_callback('/::([^:\s]*?):([^:\s]*?)::/sm', array('Content', 'emojiParseCallback'),
            $content);

        //调用其他文章页面的摘要
        if (strpos($content, '[post') !== false) {//提高效率，避免每篇文章都要解析
            $pattern = self::get_shortcode_regex(array('post'));
            $content = preg_replace_callback("/$pattern/", array('Content', 'quoteOtherPostCallback'), $content);
        }

        //解析短代码功能
        if (strpos($content, '[scode') !== false) {//提高效率，避免每篇文章都要解析
            $pattern = self::get_shortcode_regex(array('scode'));
            $content = preg_replace_callback("/$pattern/", array('Content', 'scodeParseCallback'),
                $content);
        }

        //解析文章内图集
        if (strpos($content, '[album') !== false) {
            $pattern = self::get_shortcode_regex(array('album'));
            $content = Utils::handle_preg_replace_callback("/$pattern/", array('Content', 'scodeAlbumParseCallback'),
                $content);
        }

        //解析文章内的进度条
        if (strpos($content, '[goal') !== false) {
            $pattern = self::get_shortcode_regex(array('goal'));
            $content = Utils::handle_preg_replace_callback("/$pattern/", array('Content', 'scodeGoalParseCallback'),
                $content);
        }

        //解析文章内的font
        if (strpos($content, '[font') !== false) {
            $pattern = self::get_shortcode_regex(array('font'));
            $content = Utils::handle_preg_replace_callback("/$pattern/", array('Content', 'scodeFontParseCallback'),
                $content);
        }


        if (strpos($content, '[link') !== false) {
            $pattern = self::get_shortcode_regex(array('link'));
            $content = Utils::handle_preg_replace_callback("/$pattern/", array('Content', 'scodeLinkParseCallback'),
                $content);
        }

        //进行时间树
        if (strpos($content, '[timeline') !== false) {
            $pattern = self::get_shortcode_regex(array('timeline'));
            $content = Utils::handle_preg_replace_callback("/$pattern/", array('Content', 'scodeTimelineParseCallback'),
                $content);
        }

        //解析link


        //解析markdown扩展语法
        if ($options->markdownExtend != "" && in_array('scode', Utils::checkArray( $options->markdownExtend))) {
            $content = Utils::handle_preg_replace_callback("/(@|√|!|x|i)&gt;\s(((?!<\/p>).)*)(<br \/>|<\/p>)/is", array('Content', 'sCodeMarkdownParseCallback'), $content);
        }

        //解析拼音注解写法
        if ($options->markdownExtend != "" && in_array('pinyin', Utils::checkArray( $options->markdownExtend))) {
            $content = Utils::handle_preg_replace('/\{\{\s*([^\:]+?)\s*\:\s*([^}]+?)\s*\}\}/is',
                "<ruby>$1<rp> (</rp><rt>$2</rt><rp>) </rp></ruby>", $content);
        }


        //解析显示按钮短代码
        if (strpos($content, '[button') !== false) {//提高效率，避免每篇文章都要解析
            $pattern = self::get_shortcode_regex(array('button'));
            $content = Utils::handle_preg_replace_callback("/$pattern/", array('Content', 'parseButtonCallback'), $content);
        }

        //文章中的链接，以新窗口方式打开
        $content = preg_replace_callback("/<a href=\"([^\"]*)\">(.*?)<\/a>/", function ($matches) {
            if (strpos($matches[1], substr(BLOG_URL, 0, -1)) !== false || strpos(substr($matches[1], 0, 6), "http") === false) {
                return '<a href="' . $matches[1] . '">' . $matches[2] . '</a>';
            } else {
                if (Utils::getExpertValue("no_link_ico", false)) {//true 则不加图标
                    return '<a href="' . $matches[1] . '" target="_blank">' . $matches[2] . '</a>';
                } else {
                    return '<span class="external-link"><a class="no-external-link" href="' . $matches[1] . '" target="_blank">' .
                        $matches[2] .
                        "<i data-feather='external-link'></i></a></span>";
                }

            }
        }, $content);


        return $content;
    }


    /**
     * 解析文章页面的评论内容
     * @param $content
     * @param boolean $isLogin 是否登录
     * @param $rememberEmail
     * @param $currentEmail
     * @param $parentEmail
     * @param bool $isTime
     * @return mixed
     */
    public static function postCommentContent($content, $isLogin, $rememberEmail, $currentEmail, $parentEmail, $isTime = false)
    {
        //解析私密评论
        $flag = true;
        if (strpos($content, '[secret]') !== false) {//提高效率，避免每篇文章都要解析
            $pattern = self::get_shortcode_regex(array('secret'));
            $content = preg_replace_callback("/$pattern/", array('Content', 'secretContentParseCallback'), $content);
            if ($isLogin || ($currentEmail == $rememberEmail && $currentEmail != "") || ($parentEmail == $rememberEmail && $rememberEmail != "")) {
                $flag = true;
            } else {
                $flag = false;
            }
        }
        if ($flag) {
            $content = Content::parseContentPublic($content);
            return $content;
        } else {
            if ($isTime) {
                return '<div class="hideContent">此条为私密说说，仅发布者可见</div>';
            } else {
                return '<div class="hideContent">该评论仅登录用户及评论双方可见</div>';
            }
        }
    }


    public static function postContentHtml($obj, $status)
    {

        $mathjaxOption = Utils::getMathjaxOption($obj->fields->mathjax);

        echo <<<EOF
<script>
        LocalConst.POST_MATHJAX = "{$mathjaxOption}"
</script>
EOF;

        if ($mathjaxOption && Content::getPostParseWay() == "origin"){
            echo <<<EOF
<script>            
        if (!window.MathJax){
            MathJax = {
                tex:{
                    inlineMath: [['$', '$'], ['\\(', '\\)']],
                    macros: {
                        bf: '{\\boldsymbol f}',
                        bu: '{\\boldsymbol u}',
                        bv: '{\\boldsymbol v}',
                        bw: '{\\boldsymbol w}'
                    }
                },
                svg:{
                    fontCache: 'global'
                },
                startup: {
                    elements: [document.querySelector('#content')]          // The elements to typeset (default is document body)
                }
            };
        };
        </script>
EOF;

        }



        $way = self::getPostParseWay();


        $content = Content::postContent($obj, $status, $way);


        if ($way == "vditor") {
            echo
            '<div class="loading-post text-center m-t-lg m-b-lg">
                 <p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
             </div>';

            echo '<div class="entry-content hide l-h-2x" id="md_handsome"></div>';

            echo '<textarea id="handsome_md_text" class="hide" >' . htmlspecialchars($content) . '</textarea>';
        } else {
            echo '<div class="entry-content l-h-2x" id="md_handsome_origin">' . $content . '</div>';
        }

        //显示文章tip
        if (@$obj->fields->tip != ""){
            $title = _mt("文章提示");

            $ret = mb_split("\|",$obj->fields->tip);
            if (count($ret) === 2){
                $title = trim($ret[0]);
                $content = trim($ret[1]);
            }else{
                $content = $obj->fields->tip;
            }
            echo <<<EOF
<script>
window.onload = function () {
  $.message({
                        title:"{$title}",
                        message:"{$content}",
                        type:'info',
                        time: '5000'
                    });  
};
</script>
EOF;

        }
    }

    /**
     * 输入内容之前做一些有趣的替换+输出文章内容
     *
     * @param $obj
     * @param $status
     * @param string $way
     * @return string
     */
    public static function postContent($obj, $status, $way = "origin")
    {
        if ($way == "origin") {
            $content = $obj->content;
        } else {
            $content = $obj->content;
//            $content = Handsome_Parsedown::instance()
//                ->setBreaksEnabled(false)
//                ->text($obj->content);
        }

        $options = mget();
        $isImagePost = self::isImageCategory($obj->categories);
        if (!$isImagePost && trim($obj->fields->outdatedNotice) == "yes" && $obj->is('post')) {
            date_default_timezone_set("Asia/Shanghai");
            $created = round((time() - $obj->created) / 3600 / 24);
            $updated = round((time() - $obj->modified) / 3600 / 24);
            if ($updated >= 60 || $created > 80) {
                $content = '<div class="tip share">' . sprintf(_mt("请注意，本文编写于 %d 天前，最后修改于 %d 天前，其中某些信息可能已经过时。"),
                        $created, $updated) . '</div>
'."\n" . $content;
            }
        }
        //镜像处理文章中的图片，并自动处理大小和格式，todo 如果使用了vditor 这无法解析
        if (Utils::getCdnUrl(0) != "") {

            $cdnArray = explode("|", Utils::getCdnUrl(0));
            $localUrl = str_replace("/", "\/", $options->rootUrl);//本地加速域名
            $cdnUrl = trim($cdnArray[0], " \t\n\r\0\x0B\2F");//cdn自定义域名

            $cdnArray_new = explode("|", Utils::getCdnUrl(1));
            $cdnUrl_new = trim($cdnArray_new[0], " \t\n\r\0\x0B\2F");//cdn自定义域名
            $width = 0;
            if ($isImagePost) {
                $width = 300;//图片的缩略图大小
            }
            $suffix = Utils::getImageAddOn($options, true, trim($cdnArray[1]), $width, 0, "post");//图片云处理后缀
            $content = preg_replace_callback('/(<img\s[^>]*?src=")' . $localUrl . '([^>]*?)"([^>]*?)alt="([^>]*?)"([^>]*?>)/',
                function ($matches) use ($cdnUrl, $suffix,$cdnUrl_new) {
                    $alt = $matches[4];
                    //判断是否需要加云处理后缀的
                    $cdn_temp_url = $cdnUrl;
                    if (strpos($matches[2],CDN_Config::SPECIAL_MODE_NUM) !== false){
                        $cdn_temp_url = $cdnUrl_new;
                    }
                    if (strpos($matches[4], " ':ignore'") !== false) {
                        //alt文本去掉信息元素
                        $alt = str_replace(" ':ignore'", "", $alt);
                        return $matches[1] . $cdn_temp_url
                            . $matches[2] . "\"" . $matches[3] . 'alt="' . $alt . '"' . $matches[5];
                    } else {
                        return $matches[1] . $cdn_temp_url
                            . $matches[2] . $suffix . "\"" . $matches[3] . 'alt="' . $alt . '"' . $matches[5];
                    }
                    //todo 判断参数决定显示的图片大小

                }, $content);
        }

        //延迟加载
        if (in_array('lazyload', Utils::checkArray( $options->featuresetup))) {//对图片进行处理
            $placeholder = Utils::choosePlaceholder($options);//图片占位符
            $style = "";
            if ($isImagePost) {
                $style = ' style="width:100%;height=100%"';
            }
            $content = preg_replace('/<img (.*?)src(.*?)(\/)?>/', '<img $1src="' . $placeholder . '"' . $style . ' data-original$2 />', $content);
        }

        if ($isImagePost) {//照片文章
            $content = self::postImagePost($content, $obj);
        } else {//普通文章
            if ($obj->hidden == true && trim($obj->fields->lock) != "") {//加密文章且没有访问权限
                echo '<p class="text-muted protected"><i class="glyphicon glyphicon-eye-open"></i>&nbsp;&nbsp;' . _mt("密码提示") . '：' . $obj->fields->lock . '</p>';
            }

            $db = Typecho_Db::get();
            $sql = $db->select()->from('table.comments')
                ->where('cid = ?', $obj->cid)
                ->where('status = ?', 'approved')
                ->where('mail = ?', $obj->remember('mail', true))
                ->limit(1);
            $result = $db->fetchAll($sql);//查看评论中是否有该游客的信息

            //文章中部分内容隐藏功能（回复后可见）
            if ($status || $result) {
                $content = preg_replace("/\[hide\](.*?)\[\/hide\]/sm", '<div class="hideContent">'."\n".'$1</div>',
                    $content);
            } else {
                $content = preg_replace("/\[hide\](.*?)\[\/hide\]/sm", '<div class="hideContent">' . _mt("此处内容需要评论回复后（审核通过）方可阅读。") . '</div>', $content);
            }

            //仅登录用户可查看的内容
            if (strpos($content, '[login') !== false) {//提高效率，避免每篇文章都要解析
                $pattern = self::get_shortcode_regex(array('login'));
                $isLogin = $status;
                $content = Utils::handle_preg_replace_callback("/$pattern/", function ($matches) use ($isLogin) {
                    // 不解析类似 [[player]] 双重括号的代码
                    if ($matches[1] == '[' && $matches[6] == ']') {
                        return substr($matches[0], 1, -1);
                    }
                    if ($isLogin) {
                        return '<div class="hideContent">'."\n" . $matches[5] . '</div>';
                    } else {
                        return '<div class="hideContent">' . _mt("该部分仅登录用户可见") . '</div>';
                    }
                }, $content);
            }
            $content = Content::parseContentPublic($content);
        }
        return trim($content);
    }


    public static function parseContentUrl($matches)
    {

    }

    public static function parseContentFitImage($matches)
    {
        print_r($matches);
    }

    /**
     * 解析文章内容为图片列表（相册）
     * @param $content
     * @param $type
     * @return string
     */
    public static function parseContentToImage($content, $type)
    {
        preg_match_all('/<img.*?src="(.*?)"(.*?)(alt="(.*?)")??(.*?)\/?>/', $content, $matches);

        if (is_array($matches) && count($matches[0]) > 0) {

            $html = "";
            if ($type === "photos") {//自适应拉伸的
                $html .= "<div class='album-photos'>";
            } else {//统一宽度排列
                $html .= "<div class='photos'>";
            }
            for ($i = 0; $i < count($matches[0]); $i++) {
                $info = trim($matches[5][$i]);
                preg_match('/alt="(.*?)"/', $info, $info);
                if (is_array($info) && count($info) >= 2) {
//                        print_r($info);
                    $info = @$info[1];
                } else {
                    $info = "";
                }
                if ($type == "photos") {
                    $html .= <<<EOF
<figure>
        {$matches[0][$i]}
        <figcaption>{$info}</figcaption>
</figure>
EOF;
                } else {
                    $html .= <<<EOF
<figure class="image-thumb" itemprop="associatedMedia" itemscope="" itemtype="http://schema.org/ImageObject">
          {$matches[0][$i]}
          <figcaption itemprop="caption description">{$info}</figcaption>
      </figure>
EOF;
                }
            }

            $html .= "</div>";

            return $html;
        } else {
            //解析失败，就不解析，交给前端进行解析，还原之前的短代码
            $type = ($type == "photos") ? ' type="photos"' : "";
            return "<div class='album_block'>\n\n[album" . $type . "]\n" . $content . "[/album] </div>";
        }


    }


    /**
     * @param $content
     * @param $obj
     * @return string
     */
    public static function postImagePost($content, $obj)
    {
        if ($obj->hidden === true) {//输入密码访问
            return $content;
        } else {
            return Content::parseContentToImage($content, "album");
        }
    }


    /**
     * 获取匹配短代码的正则表达式
     * @param null $tagnames
     * @return string
     * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/shortcodes.php#L254
     */
    public static function get_shortcode_regex($tagnames = null)
    {
        global $shortcode_tags;
        if (empty($tagnames)) {
            $tagnames = array_keys($shortcode_tags);
        }
        $tagregexp = join('|', array_map('preg_quote', $tagnames));
        // WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
        // Also, see shortcode_unautop() and shortcode.js.
        // phpcs:disable Squiz.Strings.ConcatenationSpacing.PaddingFound -- don't remove regex indentation
        return
            '\\['                                // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "($tagregexp)"                     // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            . '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            . '(?:'
            . '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            . '[^\\]\\/]*'               // Not a closing bracket or forward slash
            . ')*?'
            . ')'
            . '(?:'
            . '(\\/)'                        // 4: Self closing tag ...
            . '\\]'                          // ... and closing bracket
            . '|'
            . '\\]'                          // Closing bracket
            . '(?:'
            . '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            . '[^\\[]*+'             // Not an opening bracket
            . '(?:'
            . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            . '[^\\[]*+'         // Not an opening bracket
            . ')*+'
            . ')'
            . '\\[\\/\\2\\]'             // Closing shortcode tag
            . ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
        // phpcs:enable
    }

    /**
     * 获取短代码属性数组
     * @param $text
     * @return array|string
     * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/shortcodes.php#L508
     */
    public static function shortcode_parse_atts($text)
    {
        $atts = array();
        $pattern = self::get_shortcode_atts_regex();
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", ' ', $text);
        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {
            foreach ($match as $m) {
                if (!empty($m[1])) {
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                } elseif (!empty($m[3])) {
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                } elseif (!empty($m[5])) {
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                } elseif (isset($m[7]) && strlen($m[7])) {
                    $atts[] = stripcslashes($m[7]);
                } elseif (isset($m[8]) && strlen($m[8])) {
                    $atts[] = stripcslashes($m[8]);
                } elseif (isset($m[9])) {
                    $atts[] = stripcslashes($m[9]);
                }
            }
            // Reject any unclosed HTML elements
            foreach ($atts as &$value) {
                if (false !== strpos($value, '<')) {
                    if (1 !== preg_match('/^[^<]*+(?:<[^>]*+>[^<]*+)*+$/', $value)) {
                        $value = '';
                    }
                }
            }
        } else {
            $atts = ltrim($text);
        }

        if (!is_array($atts)){
            $atts = [];
        }
        return $atts;
    }

    /**
     * Retrieve the shortcode attributes regex.
     *
     * @return string The shortcode attribute regular expression
     * @since 4.4.0
     *
     */
    public static function get_shortcode_atts_regex()
    {
        return '/([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*\'([^\']*)\'(?:\s|$)|([\w-]+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|\'([^\']*)\'(?:\s|$)|(\S+)(?:\s|$)/';
    }

    public static function get_markdown_regex($tagName = '?')
    {
        return '\\' . $tagName . '&gt; (.*)(\n\n)?';

    }

    /**
     * 输出系统原生评论必须的js，主要是用来绑定按钮的
     * @param Widget_Archive $archive
     * @param $security
     */
    public static function outputCommentJS($archive, $security)
    {

        $options = mget();

        $header = "";
        if ($options->commentsThreaded && $archive->is('single')) {
            $header .= "<script type=\"text/javascript\" id='outputCommentJS'>
(function () {
    window.TypechoComment = {
        dom : function (id) {
            return document.getElementById(id);
        },
    
        create : function (tag, attr) {
            var el = document.createElement(tag);
        
            for (var key in attr) {
                el.setAttribute(key, attr[key]);
            }
        
            return el;
        },

        reply : function (cid, coid) {
            var comment = this.dom(cid), parent = comment.parentNode,
                response = this.dom('" . $archive->respondId . "'), input = this.dom('comment-parent'),
                form = 'form' == response.tagName ? response : response.getElementsByTagName('form')[0],
                textarea = response.getElementsByTagName('textarea')[0];

            if (null == input) {
                input = this.create('input', {
                    'type' : 'hidden',
                    'name' : 'parent',
                    'id'   : 'comment-parent'
                });

                form.appendChild(input);
            }

            input.setAttribute('value', coid);

            if (null == this.dom('comment-form-place-holder')) {
                var holder = this.create('div', {
                    'id' : 'comment-form-place-holder'
                });

                response.parentNode.insertBefore(holder, response);
            }

            comment.appendChild(response);
            this.dom('cancel-comment-reply-link').style.display = '';

            if (null != textarea && 'text' == textarea.name) {
                textarea.focus();
            }

            return false;
        },

        cancelReply : function () {
            var response = this.dom('{$archive->respondId}'),
            holder = this.dom('comment-form-place-holder'), input = this.dom('comment-parent');

            if (null != input) {
                input.parentNode.removeChild(input);
            }

            if (null == holder) {
                return true;
            }

            this.dom('cancel-comment-reply-link').style.display = 'none';
            holder.parentNode.insertBefore(response, holder);
            return false;
        }
    };
})();
</script>
";
        }
        if ($archive->is('single')) {
            $requestURL = $archive->request->getRequestUrl();

            $requestURL = str_replace('&_pjax=%23content', '', $requestURL);
            $requestURL = str_replace('?_pjax=%23content', '', $requestURL);
            $requestURL = str_replace('_pjax=%23content', '', $requestURL);

            $requestURL = str_replace('&_pjax=%23post-comment-list', '', $requestURL);
            $requestURL = str_replace('?_pjax=%23post-comment-list', '', $requestURL);
            $requestURL = str_replace('_pjax=%23post-comment-list', '', $requestURL);


            $header .= "<script type=\"text/javascript\">
var registCommentEvent = function() {
    var event = document.addEventListener ? {
        add: 'addEventListener',
        focus: 'focus',
        load: 'DOMContentLoaded'
    } : {
        add: 'attachEvent',
        focus: 'onfocus',
        load: 'onload'
    };
    var r = document.getElementById('{$archive->respondId}');
        
    if (null != r) {
        var forms = r.getElementsByTagName('form');
        if (forms.length > 0) {
            var f = forms[0], textarea = f.getElementsByTagName('textarea')[0], added = false;
            var submitButton = f.querySelector('button[type=\"submit\"]');
            if (null != textarea && 'text' == textarea.name) {
                var referSet =  function () {
                    if (!added) {
                        console.log('commentjs');
                        const child = f.querySelector('input[name=\"_\"]');
                        const child2 = f.querySelector('input[name=\"checkReferer\"]');
                        if (child!=null){
                            f.removeChild(child);                        
                        } 
                        if (child2!=null){
                            f.removeChild(child2);                        
                        } 
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = '_';
                            input.value = " . Typecho_Common::shuffleScriptVar(
                    $security->getToken($requestURL)) . "
                    
                        f.appendChild(input);
                        ";

            $header .= "
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'checkReferer';
                        input.value = 'false';
                        
                        f.appendChild(input);
                        ";

            $header .= "

                        added = true;
                    }
                };//end of reset
                referSet();
            }
        }
    }
};

$(function(){
    registCommentEvent();
});
</script>";
        }
        echo $header;
    }


    /**
     * 生成typecho过滤头部输出信息的标志
     * @param Widget_Archive $archive
     * @return string
     */
    public static function exportGeneratorRules($archive)
    {


        $rules = array(
            "commentReply",
        );

        if (!$archive->is('index')) {
            if ($archive->is('category')) {//是分类
                $content = $archive->getDescription();
                $content = json_decode($content, true);
                if (is_array($content) && @$content['lock'] == true) {
                    $rules[] = "description";
                }
            } else {
                //如果文章是分类加密的文章也不显示description
                $content = @$archive->stack[0]['categories'][0]['description'];
                $content = json_decode($content, true);
                if (is_array($content) && @$content['lock'] == true) {
                    $rules[] = "description";
                    $rules[] = "keywords";
                }
            }
        }


        //$options = mget();
        //if (@in_array('isPjax', Utils::checkArray( $options->featuresetup))){
        //$rules[] = "antiSpam";
        //}
        $rules[] = "antiSpam";
        return join("&", $rules);
    }


    /**
     * 选择 最左上边的交集的地方 的颜色【1】
     * @return string
     */
    public static function slectNavbarHeader()
    {
        return '<div class="text-ellipsis navbar-header bg-' . self::getThemeColor()[0] . '">';
    }

    /**
     * 选择顶部的导航栏的颜色【2】
     * @return string
     */
    public static function selectNavbarCollapse()
    {
        return '<div class="collapse pos-rlt navbar-collapse bg-' . self::getThemeColor()[1] . '">';
    }


    /**
     * 选择左侧边栏配色【3】
     * @return string
     */
    public static function selectAsideStyle()
    {
        return '<aside id="aside" class="app-aside hidden-xs bg-' . self::getThemeColor()[2] . '">';
    }


    public static function getThemeColor()
    {
        $options = mget();
        //0-交集部分  1-导航栏 2- 左侧边栏 3-对比色 4-对比色2(暂时不用)
        $colorArray = [
            ["black", "white-only", "black"],//1
            ["dark", "white-only", "dark"],//2
            ["white", "white-only", "black"],//3
            ["primary", "white-only", "dark"],//4
            ["info", "white-only", "black"],//5
            ["success", "white-only", "dark"],//6
            ["danger", "white-only", "dark"],//7
            ["black", "black", "white"],//8
            ["dark", "dark", "light"],//9
            ["info dker", "info dker", "light"],//10
            ["primary", "primary", "dark"],//11
            ["info dker", "info dk", "black"],//12
            ["success", "success", "dark"],//13
            ["danger", "danger", "dark"]//14
        ];
        $ret = $colorArray[0];//默认值
        $custom = @$options->themetypeEdit;
        $isCustom = false;//用户自定义色调
        if (trim($custom) != "") {
            $customArray = explode("-", $custom);
            if (count($customArray) == 3) {
                $isCustom = true;
                $ret = $customArray;
                $ret[] = "danger";
            } elseif (count($customArray) == 4) {
                $isCustom = true;
                $ret = $customArray;
            }
        } else {
            $ret[] = "danger";
        }

        if (!$isCustom && $options->themetype != null) {
            $ret = $colorArray[$options->themetype];
        }
        return $ret;

    }


    /**
     * 返回时光机插入按钮的model模态框HTML代码
     * @param $modelId
     * @param $okButtonId
     * @param $title
     * @param $label
     * @param String $type 类型，img,music video 决定是否有上传按钮
     * @return string
     */
    public static function returnCrossInsertModelHtml($modelId, $okButtonId, $title, $label, $type)
    {

        $uploadHtml = "";
        if ($type == "img") {
            $uploadHtml = <<<EOF
<label class="insert_tips m-t-sm">本地上传</label>
     <input type="file" id="time_file" multiple name="file" class="hide">
<div class="bootstrap-filestyle input-group"><input type="text" id="file-info" class="form-control" value="未选择任何文件" 
disabled=""> <span 
class="group-span-filestyle input-group-btn" tabindex="0"><label for="filestyle-0" class="btn btn-primary" id="time-upload">选择文件</label></span></div>
EOF;

        }

        return '
 <div class="modal fade" id="' . $modelId . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                <div class="modal-dialog modal-sm" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title" id="myModalLabel">' . _mt("$title") . '</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label class="insert_tips">' . _mt("$label") . '</label>
                                                        <input name="' . $modelId . '" type="text" class="form-control" >
                                                        ' . $uploadHtml . '
                                                        
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                    data-dismiss="modal"> ' . _mt("取消") . ' </button>
                                            <button type="button" id="' . $okButtonId . '" class="btn btn-primary">' . _mt("确定") . '</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
 ';
    }

    /**
     * 返回pjax自定义动画所需的css
     * @return string
     */
    public static function returnPjaxAnimateCss()
    {
        $options = mget();
        $css = "";
        $color = $options->progressColor;

        switch (trim($options->pjaxAnimate)) {
            case "minimal":
                $css = <<<EOF
.pace .pace-progress{
    background: {$color};
}
EOF;
                break;

            case "flash":
                $css = <<<EOF
.pace .pace-progress{
    background: {$color};
}

.pace .pace-progress-inner{
      box-shadow: 0 0 10px {$color}, 0 0 5px {$color};
}
.pace .pace-activity{
      border-top-color: {$color};
      border-left-color: {$color};
}
EOF;
                break;

            case "big-counter":
                $textColor = Utils::hex2rgb($color);
                $css = <<<EOF
.pace .pace-progress:after{
      color: rgba({$textColor}, 0.19999999999999996);
}
EOF;
                break;

            case "corner-indicator":
                $css = <<<EOF
.pace .pace-activity{
    background: {$color};
}
EOF;
                break;

            case "center-simple":
                $css = <<<EOF
.pace{
    border: 1px solid {$color};
    
}
.pace .pace-progress{
    background: {$color};
}
EOF;
                break;

            case "loading-bar":
                $css = <<<EOF
.pace .pace-progress{
      background: {$color};
      color: {$color};
}

.pace .pace-activity{
    box-shadow: inset 0 0 0 2px {$color}, inset 0 0 0 7px #FFF;
}
EOF;
                break;

            case "whiteRound":
                $css = <<<EOF
.loading {
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.9);
    z-index: 123456789 !important;
    opacity: 1;
    -webkit-transition: opacity 0.3s ease;
    -moz-transition: opacity 0.3s ease;
    -ms-transition: opacity 0.3s ease;
    -o-transition: opacity 0.3s ease;
    transition: opacity 0.3s ease
}

.loading .preloader-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
    -moz-transform: translate(-50%, -50%);
    -ms-transform: translate(-50%, -50%);
    -o-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%)
}

.loading .loader-inner.ball-scale-multiple div {
    background-color: #000
}

.loading.loading-loaded {
    opacity: 0;
    display: none;
}

@-webkit-keyframes ball-scale-multiple {
    0% {
        -webkit-transform: scale(0);
        transform: scale(0);
        opacity: 0
    }

    5% {
        opacity: 1
    }

    100% {
        -webkit-transform: scale(1);
        transform: scale(1);
        opacity: 0
    }

}

@keyframes ball-scale-multiple {
    0% {
        -webkit-transform: scale(0);
        transform: scale(0);
        opacity: 0
    }

    5% {
        opacity: 1
    }

    100% {
        -webkit-transform: scale(1);
        transform: scale(1);
        opacity: 0
    }

}

.ball-scale-multiple {
    position: relative;
    -webkit-transform: translateY(-30px);
    transform: translateY(-30px)
}

.ball-scale-multiple>div:nth-child(2) {
    -webkit-animation-delay: -.4s;
    animation-delay: -.4s
}

.ball-scale-multiple>div:nth-child(3) {
    -webkit-animation-delay: -.2s;
    animation-delay: -.2s
}

.ball-scale-multiple>div {
    position: absolute;
    left: -30px;
    top: 0;
    opacity: 0;
    margin: 0;
    width: 60px;
    height: 60px;
    -webkit-animation: ball-scale-multiple 1s 0s linear infinite;
    animation: ball-scale-multiple 1s 0s linear infinite
}

.ball-scale-multiple>div {
    background-color: #fff;
    border-radius: 100%
}

EOF;

                break;
            case "customise":
                $css = $options->pjaxCusomterAnimateCSS;
                break;
        }
        return $css;
    }


    /**
     * 热门文章，按照评论数目排序
     * @param $hot
     */
    public static function returnHotPosts($hot)
    {
        $options = mget();
        $days = 99999999999999;
        $time = time() - (24 * 60 * 60 * $days);
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        //echo date('Y-n-j H:i:s',time());
        if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
            $db->query('ALTER TABLE ' . $prefix . 'contents ADD views INTEGER(10) DEFAULT 0;');
        }
        $orderType = $options->hotPostOrderType;
        if (empty($orderType)) {
            $orderType = 'commentsNum';
        } else {
            $orderType = $options->hotPostOrderType;
        }
        $sql = $db->select()->from('table.contents')
            ->where('created >= ?', $time)
            ->where('table.contents.created <= ?', time())
            ->where('type = ?', 'post')
            ->where('status =?', 'publish')
            ->limit(5)
            ->order($orderType, Typecho_Db::SORT_DESC);
        //echo $sql->__toString();
        $result = $db->fetchAll($sql);
        $index = 0;
        $isShowImage = true;
        if (count($options->indexsetup) > 0 && in_array('notShowRightSideThumb', Utils::checkArray( $options->indexsetup))) {
            $isShowImage = false;
        }
        foreach ($result as $val) {
            $val = Typecho_Widget::widget('Widget_Abstract_Contents')->filter($val);
            echo '<li class="list-group-item">
                <a href="' . $val['permalink'] . '" class="pull-left thumb-sm m-r">' . self::returnRightSideImageHtml($isShowImage, $hot, $index) . '</a>
                <div class="clear">
                    <h4 class="h5 l-h text-second"> <a href="' . $val['permalink'] . '" title="' . $val['title'] . '"> ' .
                $val['title'] . ' </a></h4>
                    <small class="text-muted post-head-icon">';
            if ($options->hotPostOrderType != "views") {
                echo '<span class="meta-views"> <span class="right-small-icons"><i data-feather="message-circle"></i></span>  <span class="sr-only">评论数：</span> <span class="meta-value">' . $val['commentsNum'] . '</span>
                    </span>';
            } else {
                echo '<span class="meta-date"> <i class="fontello fontello-eye" aria-hidden="true"></i> <span class="sr-only">浏览次数:</span> <span class="meta-value">' . $val['views'] . '</span>
                    </span>
              ';
            }
            echo '</small></div></li>';
            $index++;
        }
    }

    /**
     * 随机文章，显示5篇
     * @param $random
     */
    public static function returnRandomPosts($random)
    {
        $options = mget();
        $modified = $random->modified;
        $db = Typecho_Db::get();
        $adapterName = $db->getAdapterName();//兼容非MySQL数据库
        if ($adapterName == 'pgsql' || $adapterName == 'Pdo_Pgsql' || $adapterName == 'Pdo_SQLite' || $adapterName == 'SQLite') {
            $order_by = 'RANDOM()';
        } else {
            $order_by = 'RAND()';
        }
        $sql = $db->select()->from('table.contents')
            ->where('status = ?', 'publish')
            ->where('table.contents.created <= ?', time())
            ->where('type = ?', 'post')
            ->limit(5)
            ->order($order_by);

        $result = $db->fetchAll($sql);
        $index = 0;
        $isShowImage = true;
        if (count($options->indexsetup) > 0 && in_array('notShowRightSideThumb', Utils::checkArray( $options->indexsetup))) {
            $isShowImage = false;
        }
        foreach ($result as $val) {
            $val = Typecho_Widget::widget('Widget_Abstract_Contents')->filter($val);
            echo '<li class="list-group-item">
                <a href="' . $val['permalink'] . '" class="pull-left thumb-sm m-r">' . self::returnRightSideImageHtml($isShowImage, $random, $index) . '</a>
                <div class="clear">
                    <h4 class="h5 l-h text-second"> <a href="' . $val['permalink'] . '" title="' . $val['title'] . '"> ' . $val['title'] . ' </a></h4>
                    <small class="text-muted post-head-icon">';
            if ($options->hotPostOrderType != "views") {
                echo '<span class="meta-views "> <span class="right-small-icons"><i data-feather="message-circle"></i></span> <span class="sr-only">评论数：</span> <span class="meta-value">' . $val['commentsNum'] . '</span>
                    </span>';
            } else {
                echo '<span class="meta-date"> <i class="fontello fontello-eye" aria-hidden="true"></i> <span class="sr-only">浏览次数:</span> <span class="meta-value">' . $val['views'] . '</span>
                    </span>
              ';
            }
            echo '</small></div></li>';
            $index++;
        }
    }

    /**
     * @param $isShowImage
     * @param $obj
     * @param $index
     * @return string
     */
    public static function returnRightSideImageHtml($isShowImage, $obj, $index)
    {
        if ($isShowImage) {
            return '<img src="'
                . Utils::returnImageSrcWithSuffix
                (showSidebarThumbnail($obj,
                    $index), null, 80, 80) . '" class="img-40px normal-shadow img-square">';
        } else {
            return "";
        }
    }


    /**
     * 返回主题的文章显示动画class
     * @param $obj
     */
    public static function returnPageAnimateClass($obj)
    {
        $options = mget();
        if (in_array('isPageAnimate', Utils::checkArray( $options->featuresetup)) && ($obj->is('post') || $obj->is('page'))) {
            echo "animated fadeIn";
        } else if (in_array('isOtherAnimate', Utils::checkArray( $options->featuresetup)) && !$obj->is('post') &&
            !$obj->is('page')) {
            echo "animated fadeIn";
        } else {
            echo "";
        }
    }

    public static function returnCommentList($obj, $security, $comments)
    {
        echo '<div id="post-comment-list" class="skt-loading">';


        if ($comments->have()) {
            echo '<h4 class="comments-title m-t-lg m-b">';
            $obj->commentsNum(_mt('暂无评论'), _mt('1 条评论'), _mt('%d 条评论'));
            echo '</h4>';
            echo <<<EOF
<nav class="loading-nav text-center m-t-lg m-b-lg hide">
<p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
</nav>
EOF;
            $comments->listComments();//列举评论
            echo '<nav id="comment-navigation" class="text-center m-t-lg m-b-lg" role="navigation">';
            $comments->pageNav('<i class="fontello fontello-chevron-left"></i>', '<i class="fontello fontello-chevron-right"></i>');
            echo '</nav>';
        }
        Content::outputCommentJS($obj, $security);
        echo '</div>';

    }


    public static function returnLeftItem($asideItem, $haveSub, $subListHtml)
    {

        $ret = "";
        @$itemName = $asideItem->name;
        @$itemStatus = $asideItem->status;
        @$itemLink = $asideItem->link;
        @$itemClass = $asideItem->class;
        @$itemFeather = $asideItem->feather;
        @$itemSub = $asideItem->sub;
        @$itemTarget = $asideItem->target;


        if (@$itemTarget) {
            $linkStatus = 'target="' . $itemTarget . '"';
        } else {
            $linkStatus = 'target="_self"';
        }
        if (trim($itemFeather) == "") {
            if (count(mb_split(" ", $itemClass)) == 1 && strpos($itemClass, "fontello") === false && strpos($itemClass, "glyphicon") === false) {
                $itemFeather = $itemClass;
            }
        }

        $right_arrow = ($haveSub) ? '<span class="pull-right text-muted">
                    <i class="fontello icon-fw fontello-angle-right text"></i>
                    <i class="fontello icon-fw fontello-angle-down text-active"></i>
                  </span>' : "";

        if (trim($itemFeather) !== "") {
            $ret = '<li> <a ' . $linkStatus . ' href="' . $itemLink . '" 
class ="auto">' . $right_arrow . '<span class="nav-icon"><i data-feather="' . $itemFeather . '"></i></span><span>' . _mt
                ($itemName) . '</span></a>' . $subListHtml . '</li>';
        } else if (trim($itemClass) !== "") {
            $ret = '<li> <a ' . $linkStatus . ' href="' . $itemLink . '" class ="auto">' . $right_arrow . '<span class="nav-icon"><i class="'
                . $itemClass . '"></i></span><span>' . _mt($itemName) . '</span></a>' . $subListHtml . '</li>';
        }

        return $ret;
    }


    public static function returnLeftItems()
    {

    }

    /**
     * @param $categories
     * @return string
     */
    public static function returnCategories($categories)
    {
        $html = "";
        $options = mget();
        while ($categories->next()) {
            if ($categories->levels === 0) {//父亲分类

                $children = $categories->getAllChildren($categories->mid);//获取当前父分类所有子分类
                //print_r($children);
                //var_dump(empty($children));
                if (!empty($children)) {//子分类不为空
                    $html .= '<li><a class="auto"><span class="pull-right text-muted">
                    <i class="fontello icon-fw fontello-angle-right text"></i>
                    <i class="fontello icon-fw fontello-angle-down text-active"></i>
                  </span><span>' . $categories->name . '</span></a>';
                    //循环输出子分类
                    $childCategoryHtml = '<ul class="nav nav-sub dk child-nav">';
                    foreach ($children as $mid) {
                        $child = $categories->getCategory($mid);
                        $childCategoryHtml .= '<li><a href="' . $child['permalink'] . '"><b class="badge pull-right">' . $child['count'] . '</b><span>' . $child['name'] . '</span></a></li>';
                    }
                    $childCategoryHtml .= '</ul>';

                    $html .= $childCategoryHtml;
                    $html .= "</li>";
                } else {//没有子分类
                    $html .= '<li><a href="' . $categories->permalink . '"><b class="badge pull-right">' . $categories->count . '</b><span>' . $categories->name . '</span></a></li>';
                }
            }
        }

        return $html;
    }

    public static function returnPjaxAnimateHtml()
    {
        $options = mget();
        $html = "";
        if ($options->pjaxAnimate == "default") {
            $html .= '<div id="loading" class="butterbar active hide">
            <span class="bar"></span>
        </div>';
        } else if ($options->pjaxAnimate == "whiteRound") {
            $html .= '<section id="loading" class="loading hide">
    <div class="preloader-inner">
        <div class="loader-inner ball-scale-multiple"><div></div><div></div><div></div></div>
    </div>
</section>';
        } else if ($options->pjaxAnimate == "customise") {
            $html .= $options->pjaxCusomterAnimateHtml;
        }
        return $html;
    }

    public static function returnTimeTab($id, $name, $url, $type, $img)
    {
        return <<<EOF
<li ><a href="#$id" role="tab" data-id="$id" data-toggle="tab" aria-expanded="false" 
data-rss="$url" data-status="false" data-type="$type" data-img="$img">$name</a></li>
EOF;
    }


    public static function returnTimeTabPane($id)
    {
        return <<<EOF
<div id="$id" class="padder fade tab-pane">
                        <nav class="loading-nav text-center m-t-lg m-b-lg">
                            <p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
                        </nav>
                        <nav class="error-nav hide text-center m-t-lg m-b-lg">
                            <p class="infinite-scroll-request"><i class="glyphicon 
                            glyphicon-refresh"></i>加载失败！尝试重新加载</p>
                        </nav>
                        <div class="streamline b-l m-l-lg m-b padder-v hide">
                            <ol class="comment-list">
                            </ol>
                        </div>
                    </div>
EOF;
    }

    public static function pageFooter($options)
    {
        if ($options->adContentPage != "") {
            $options->adContentPage();
        }
        if (!empty($options->featuresetup) && in_array('payforauthorinpage', Utils::checkArray( $options->featuresetup))) {
            echo Content::exportPayForAuthors();
        }
    }

    public static function returnWheelHtml($content)
    {
        $json = "[" . $content . "]";
        $wheelItems = json_decode($json);
        $wheelItemsOutput = "\n" . '<div id="index-carousel" class="box-shadow-wrap-normal border-radius-6 carousel slide m-b-md" data-ride="carousel">';

        $carouselIndicators = <<<EOF
                <ol class="carousel-indicators">
EOF;
        $carouselInner = <<<EOF
                <div class="carousel-inner border-radius-6" role="listbox">
EOF;

        $index = 0;

        foreach ($wheelItems as $item) {
            @$itemTitle = $item->title;
            @$itemDesc = $item->desc;
            @$itemLink = $item->link;
            $itemCover = $item->cover;


            $insert = "";
            if ($index === 0) {
                $insert = 'active';
            }
            $carouselIndicators .= <<<EOF
<li data-target="#index-carousel" data-slide-to="$index" class="$insert"></li>
EOF;

            $carouselInner .= <<<EOF
<div class="item $insert border-radius-6">
<a href="$itemLink">
                        <img class="border-radius-6" src="$itemCover" data-holder-rendered="true">
                        <div class="carousel-caption">
                           <h3>$itemTitle</h3>
                            <p>$itemDesc</p>
                        </div>
                        </a>
                    </div>
EOF;

            $index++;

        }

        $carouselIndicators .= "</ol>";
        $carouselInner .= "</div>";

        $carouselControl = <<<EOF
<a class="left carousel-control" href="#index-carousel" role="button" data-slide="prev">
                    <svg class="glyphicon-chevron-left" viewBox="0 0 24 24" width="24" height="24" 
                    stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    <span class="sr-only">Previous</span>
                </a>
<a class="right carousel-control" href="#index-carousel" role="button" data-slide="next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="glyphicon-chevron-right" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    <span class="sr-only">Next</span>
                </a>
EOF;

        $wheelItemsOutput .= $carouselIndicators . "\n" . $carouselInner . "\n" . $carouselControl;


        $wheelItemsOutput .= "</div>";

        return $wheelItemsOutput;

    }


    /**
     * @param $type post-calendar category-radar posts-chart categories-chart tags-chart
     */
    public static function statisticPane($type)
    {


    }


    /**
     * @param $type post-calendar category-radar posts-chart categories-chart tags-chart
     * @param $obj
     * @param $monthNum
     * @return array
     */
    public static function getStatisticContent($type, $obj, $monthNum = 10)
    {

        $object = array();

        if ($type == "post-calendar" || $type == "posts-chart") {
            //获取统计信息
            $filePath = __TYPECHO_ROOT_DIR__ . __TYPECHO_PLUGIN_DIR__ . DIRECTORY_SEPARATOR . 'Handsome' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'search.json';
            $fileContent = file_get_contents($filePath);


            //1. 确定时间段
            //获取今天所在的月份
            $end_m = (int)date('m');
            $end_d = (int)date('d');
            $end_y = (int)date('Y');

            $start_y = $end_y;

            if ($type == "post-calendar") {
                $start_m = $end_m - $monthNum + 1;
            } else {//post-chart 只需要近5个月的数据
                $start_m = $end_m - 5;
            }
            $start_d = $end_d;
            if ($start_m < 0) {
                $start_m = $start_m + 12;
                $start_y = $start_y - 1;
            }
            //获取该时间段的每一天的日期，及其该日的文章数目。

            $start_timestamp = strtotime($start_y . "-" . $start_m . "-" . $start_d);
            $end_timestamp = strtotime($end_y . "-" . $end_m . "-" . $end_d);

            $data = array();
            $max_single = 0;// 最大每日/月的文章数目

            if ($type == "post-calendar") {

                $commentPath = __TYPECHO_ROOT_DIR__ . __TYPECHO_PLUGIN_DIR__ . DIRECTORY_SEPARATOR . 'Handsome'
                    . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'comment.json';
                $commentContent = file_get_contents($commentPath);


                // 计算日期段内有多少天
                $date = array();// 保存每天日期
                while ($start_timestamp <= $end_timestamp) {
                    $temp = date('Y-m-d', $start_timestamp);
                    $date[] = $temp;
                    //搜索当日发表的文章数目
                    $temp_num = substr_count($fileContent, '"date":"' . $temp);
                    $temp_comment_num = substr_count($commentContent, $temp);
                    $temp_num = $temp_comment_num + $temp_num;

                    if ($temp_num > $max_single) {
                        $max_single = $temp_num;
                    }
                    if ($max_single > 3) {//max 最大值是3，再后面是4+
                        $max_single = 4;
                    }
                    if ($temp_num > 3) {
//                        $temp_num = 4;
                    }

                    $data[] = array(
                        $temp,
                        $temp_num
                    );
                    $start_timestamp = strtotime('+1 day', $start_timestamp);
                }

                $object['series'] = $data;
                $object['range'] = array(
                    $start_y . "-" . $start_m . "-" . $start_d,
                    $end_y . "-" . $end_m . "-" . $end_d
                );
                $object['categories'] = range(0, $max_single);
                $object['max'] = $max_single;
//                $object['num_range'] = range(0,$max_single);
//                if (count($object['categories']) ==5){
//                    $object['categories'][4] = 'N';
//                }

//                var_dump($object);

            } else {
                // 计算时间段内有多少个月
                $xAxis = [];
                while ($start_timestamp <= $end_timestamp) {
                    $temp = date('Y-m', $start_timestamp); // 取得递增月;
                    $temp_num = substr_count($fileContent, '"date":"' . $temp);
                    if ($temp_num > $max_single) {
                        $max_single = $temp_num;
                    }
                    $xAxis[] = $temp;
                    $data[] = $temp_num;
                    $start_timestamp = strtotime('+1 month', $start_timestamp);
                }

                $object['series'] = $data;
                $object["xAxis"] = $xAxis;
                $object['categories'] = range(0, $max_single);
            }
        }

        if ($type == "category-radar" || $type == "categories-chart" || $type == "tags-chart") {
            $object = [];
            $name = [];
            $indicator = [];
            $num = [];
            $i = -1;
            while ($obj->next()) {
//                print_r("\n\n================i:".$i."================\n\n");
                //判断是否是子分类
                if ($obj->levels == 0) {
                    $i++;
//                    print_r("\nmid".$obj->mid."|count:".$obj->count."\n");
                    $name[] = $obj->name;
                    $num [] = $obj->count;
                } else {
                    $num [$i] += $obj->count;
//                    print_r("level:".$obj->levels."|id".$obj->mid."parent:".$obj->parent."|");
                }
            }

            $max_single = max($num);

//            print_r("count:".$i);

            for ($i = 0; $i < count($num); $i++) {
                if ($type == "category-radar") {
                    $indicator[] = (object)array(
                        "name" => $name[$i],
                        "max" => $max_single
                    );
                } else if ($type == "categories-chart") {
                    $indicator[] = (object)array(
                        "name" => $name[$i],
                        "value" => $num[$i]
                    );
                } else if ($type == "tags-chart") {
                    $indicator[] = $name[$i];
                }
            }

            if ($type == "categories-chart") {
                //保证color数组的颜色不重复
                $color = ['#6772e5', '#ff9e0f', '#fa755a', '#3ecf8e', '#82d3f4', '#ab47bc', '#525f7f', '#f51c47', '#26A69A', '#6772e5', '#ff9e0f', '#fa755a', '#3ecf8e', '#82d3f4', '#ab47bc', '#525f7f', '#f51c47', '#26A69A'];
                $cColor = [];
                for ($i = 0; $i < count($num); $i++) {
                    $cColor [] = $color[$i % count($color)];
                }

                $object["color"] = $cColor;
            }

            if ($type == "tags-chart") {
                $indicator = array_slice($indicator, 0, min(8, count($indicator)));
                $num = array_slice($num, 0, min(8, count($num)));
            }

            $object["indicator"] = $indicator;
            $object["series"] = $num;

        }
        return $object;

    }
}



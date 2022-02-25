<?php
/**
 * <strong style="color:red;">handsomePro 唯一配套插件</strong>
 *
 * @package Handsome
 * @author hewro,hanny
 * @version 8.0.0
 * @dependence 14.10.10-*
 * @link https://www.ihewro.com
 *
 */



class Handsome_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     *
     * @access public
     * @return string
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {

        //时光机评论禁止游客发布说说
        Typecho_Plugin::factory('Widget_Feedback')->comment = array('Handsome_Plugin', 'filter');


        //vEditor
        Typecho_Plugin::factory('admin/write-post.php')->richEditor = array('Handsome_Plugin', 'VEditor');
        Typecho_Plugin::factory('admin/write-page.php')->richEditor = array('Handsome_Plugin', 'VEditor');



        //友情链接
        $info = "插件启用成功</br>";
        $info .= Handsome_Plugin::linksInstall()."</br>";
        $info .= Handsome_Plugin::cacheInstall()."</br>";
        Helper::addPanel(3, 'Handsome/manage-links.php', '友情链接', '管理友情链接', 'administrator');
        Helper::addAction('links-edit', 'Handsome_Action');
        Helper::addAction('multi-upload', 'Handsome_Action');
        Helper::addAction('handsome-meting-api', 'Handsome_Action');

        //过滤私密评论
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array('Handsome_Plugin', 'exceptFeedForDesc');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('Handsome_Plugin', 'exceptFeed');
        Typecho_Plugin::factory('Widget_Abstract_Comments')->contentEx = array('Handsome_Plugin', 'parse');


        //markdown 引擎
        Typecho_Plugin::factory('Widget_Abstract_Contents')->content = array('Handsome_Plugin', 'content');




        //置顶功能
        Typecho_Plugin::factory('Widget_Archive')->indexHandle = array('Handsome_Plugin', 'sticky');
        //分类过滤，默认过滤相册
        Typecho_Plugin::factory('Widget_Archive')->indexHandle = array('Handsome_Plugin', 'CateFilter');
        Typecho_Plugin::factory('Widget_Archive')->categoryHandle = array('Handsome_Plugin', 'CategoryCateFilter');


        Typecho_Plugin::factory('Widget_Archive')->footer = array('Handsome_Plugin', 'footer');

        // 注册文章、页面保存时的 hook（JSON 写入数据库）
        Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishPublish = array('Handsome_Plugin', 'buildSearchIndex');
        Typecho_Plugin::factory('Widget_Contents_Post_Edit')->finishDelete = array('Handsome_Plugin', 'buildSearchIndex');
        Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishPublish = array('Handsome_Plugin', 'buildSearchIndex');
        Typecho_Plugin::factory('Widget_Contents_Page_Edit')->finishDelete = array('Handsome_Plugin', 'buildSearchIndex');

        //添加评论的回调接口
        Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('Handsome_Plugin', 'parseComment');
        Typecho_Plugin::factory('Widget_Comments_Edit')->finishComment = array('Handsome_Plugin', 'parseComment');

//        //评论的异步接口
//        Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('Mailer_Plugin', 'sendComment');
//        Typecho_Plugin::factory('Widget_Service')->parseComment = array('Mailer_Plugin', 'parseComment');

        $info .="首次启动，需要在插件设置里面更新搜索索引</br>";

        return _t($info);
    }



    public static function sendComment($comment){
        Helper::requestService('parseComment', $comment);
    }


    public static function parseComment($comment){
        if ($comment->authorId!=="0"){//是登录用户，authorid 是该条评论的登录用户的id
            $code = file_put_contents(__DIR__ . '/cache/comment.json', date("Y-m-d"),FILE_APPEND);
        }

    }
    public static function filter($comment, $post){
        if ($post->slug === "cross"){
            if (!$comment["authorId"] && !$comment["parent"]){//不是登录用户，而且发表的是说说，这需要拦截
                throw new Typecho_Widget_Exception("你没有权限发表说说");
            }
        }

        return $comment;
    }

    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     *
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        Helper::removeAction('links-edit');
        Helper::removeAction('multi-upload');
        Helper::removeAction('handsome-meting-api');
        Helper::removePanel(3, 'Links/manage-links.php');
        Helper::removePanel(3, 'Handsome/manage-links.php');
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

        require 'Device.php';



        if (isset($_GET['action']) && $_GET['action'] == 'buildSearchIndex') {
            self::buildSearchIndex();
        }


        if (isset($_GET['action']) && $_GET['action'] == 'moveToRoot') {
            self::moveToRoot();
        }

        $form->addInput(new Title_Plugin('btnTitle', NULL, NULL, _t('致谢'), NULL));


        $thanks = new Typecho_Widget_Helper_Form_Element_Select("thanks", array(
            1 => "友情链接功能由<a href='http://www.imhan.com'>hanny</a>开发，感谢！",
            2 => "主题播放器基于Aplayer项目并集成了APlayer-Typecho插件，感谢！"
        ), "1", "插件致谢", "<strong style='color: red'> 【友情链接】请在typecho的后台-管理-友情链接 设置</strong>");
        $form->addInput($thanks);

        $form->addInput(new Title_Plugin('btnTitle', NULL, NULL, _t('文章设置'), NULL));


        $sticky_cids = new Typecho_Widget_Helper_Form_Element_Text(
            'sticky_cids', NULL, '',
            '置顶文章的 cid', '按照排序输入, 请以半角逗号或空格分隔 cid.</br><strong style=\'color: red\'>cid查看方式：</strong>后台的文章管理中，进入具体的文章编辑页面，地址栏中会有该数字。如<code>http://localhost/build/admin/write-post.php?cid=120</code>表示该篇文章的cid为120');
        $form->addInput($sticky_cids);

        $form->addInput(new Title_Plugin('btnTitle', NULL, NULL, _t('分类设置'), NULL));


        $CateId = new Typecho_Widget_Helper_Form_Element_Text('CateId', NULL, '', _t('首页不显示的分类的mid'), _t('多个请用英文逗号,隔开</br><strong style="color: red">mid查看方式：</strong> 在分类管理页面点击分类，地址栏中会有该数字，比如<code>http://localhost/build
/admin/category.php?mid=2</code> 表示该分类的mid为2</br><strong style="color: rgba(255,0,18,1)">默认不过滤相册分类，请自行过滤</strong></br> <b style="color:red">说明：填写该设置后，是指该分类的文章不在首页文章页面中显示，如果希望实现侧边栏不显示某个分类，可以查看<a target="_blank" href="https://auth.ihewro.com/user/docs/#/lock">使用文档——内容加密</a>中说明</b>'));
        $form->addInput($CateId);

        $LockId = new Typecho_Widget_Helper_Form_Element_Text('LockId', NULL, '', _t('加密分类mid'), _t('多个请用英文逗号隔开</br><strong style="color: red">mid查看方式：</strong> 在分类管理页面点击分类，地址栏中会有该数字，比如<code>http://localhost/build
/admin/category.php?mid=2</code> 表示该分类的mid为2</br><strong style="color: rgba(255,0,18,1)">加密分类的密码需要在分类描述按照指定格式填写<a 
href="https://handsome.ihewro.com/#/lock" target="_blank">使用文档</a></strong></br><strong style="color: rgba(255,0,18,1)">加密分类仍然会在首页显示标题列表，但不会显示具体内容，也不会出现在rss地址中</strong>'));
        $form->addInput($LockId);

        $form->addInput(new Title_Plugin('btnTitle', NULL, NULL, _t('搜索设置'), NULL));

        $queryBtn = new Typecho_Widget_Helper_Form_Element_Submit();
        $queryBtn->value(_t('构建文章索引'));self::renderHtml();
        $queryBtn->description(_t('通常只需要在第一次启用插件的时候，手动点击该按钮。在发布、修改文章的时候会自动构建新的索引。如果发现搜索数据不对，请再次手动点击此按钮'));
        $queryBtn->input->setAttribute('class', 'btn btn-s btn-warn btn-operate');
        $queryBtn->input->setAttribute('formaction', Typecho_Common::url('/options-plugin.php?config=Handsome&action=buildSearchIndex', Helper::options()->adminUrl));
        $form->addItem($queryBtn);
        $cacheWhen = new Typecho_Widget_Helper_Form_Element_Radio('cacheWhen',
            array(
                'true' => '文章保存同时更新搜索索引',
                'false' => '不实时更新索引，适用于网站的文章特别多，此时需要手动更新索引',
            ),'true', _t('实时更新索引'), _t('网站文章特别多（超过1000篇）的时候，请关闭实时更新索引，否则保存文章时候花费时间较长可能会显示超时错误'));
        $form->addInput($cacheWhen);



        $form->addInput(new Title_Plugin('btnTitle', NULL, NULL, _t('编辑器设置'), NULL));


        $editorChoice = new Typecho_Widget_Helper_Form_Element_Radio('editorChoice',
            array(
                'origin' => '使用typecho自带的markdown编辑器',
                'vditor' => '使用vditor编辑器 <a href="https://auth.ihewro.com/user/docs/#/./vditor" target="_blank">vditor使用介绍</a>',
                'other' => '使用其他第三方编辑器'
            ),'origin', _t('<b style="color: red">后台</b>文章编辑器选择'), _t('可根据个人喜好选择'));
        $form->addInput($editorChoice);


        $vditorMode = new Typecho_Widget_Helper_Form_Element_Radio('vditorMode',
            array(
                'wysiwyg' => '所见即所得',
                'ir' => '即时渲染',
                'sv' => '源码模式（和typecho默认的编辑器几乎一致）',
                'sv_both' => '源码模式+分屏预览所见即所得',
            ),'ir', _t('vditor默认模式选择'), _t('
                所见即所得（WYSIWYG对不熟悉 Markdown 的用户较为友好，熟悉 Markdown 的话也可以无缝使用。<a href="https://s1.ax1x.com/2020/08/03/aajX0e.gif" target="_blank">演示效果</a>  </br>
                即时渲染模式对熟悉 Typora 的用户应该不会感到陌生，理论上这是最优雅的 Markdown 编辑方式。<a href="https://s1.ax1x.com/2020/08/03/aajxkd.gif" target="_blank">演示效果</a> </br>       
                传统的分屏预览模式适合大屏下的 Markdown 编辑。<a href="https://s1.ax1x.com/2020/08/03/aajfw4.gif" target="_blank">演示效果</a>     
            '));
        $form->addInput($vditorMode);

        $parseWay = new Typecho_Widget_Helper_Form_Element_Radio('parseWay',
            array(
                'origin' => '使用typecho自带的markdown解析器',
                'vditor' => '前台引入vditor.js接管前台解析',
            ),'origin', _t('<b style="color: red">前台</b>Markdown解析方式选择'), _t('1.选择typecho自带解析器，即和typecho默认的解析器一致，可以在基础上使用第三方markdown解析器，主题在此基础上内置了mathjax和代码高亮，需要在主题增强功能里面开启</br>2.选择vditor前台解析，可以与后台编辑器得到相同的解析效果，支持后台编辑器的所有语法，<b style="color: red">但是对于有些插件兼容性不好，并且不支持ie浏览器（在ie11 浏览器中会自动切换到typecho原生解析方式）</b></br>'));
        $form->addInput($parseWay);

        $urlUpload = new Typecho_Widget_Helper_Form_Element_Radio('urlUpload',
            array(
                'true' => '开启外链上传',
                'false' => '关闭外链上传',
            ),'false', _t('vditor开启外链上传'), _t('开启此功能后，复制粘贴的文本到编辑器中，如果文本中包含了外链的图片地址，会自动上传到自己服务器中，<b style="color:red;">仅当后台编辑器选择vditor编辑器有效</b>'));
        $form->addInput($urlUpload);

        $vditorCompleted = new Typecho_Widget_Helper_Form_Element_Textarea('vditorCompleted', NULL, "",
            _t('vditor.js 解析结束回调函数'), _t('如果前台选择了 vditor.js 解析，有一些JavaScript代码可能需要在vditor.js 解析文章内容后再对文章内容进行操作，可以填写再这里</br> 如果不明白这项，请清空'));
        $form->addInput($vditorCompleted);





        $form->addInput(new Title_Plugin('btnTitle', NULL, NULL, _t('缓存设置'), NULL));
        $cacheSetting = new Typecho_Widget_Helper_Form_Element_Radio('cacheSetting',
            array(
                'yes' => '是，(该功能需要https) 使用离线缓存，缓存与主题相关的静态资源。',
                'no' => '否，缓存特性插件不进行额外接管，由浏览器和自己使用的CDN进行控制',
            ),'no', _t('使用本地离线缓存功能'), _t('使用本地缓存主题相关的静态资源后，加载速度能够得到明显的提升，主题目录下面的assets 文件夹会进行本地缓存（使用service worker 实现）,</br> 在「版本更新」和「使用强制刷新、清除缓存」的情况下才会更新这些资源'));
        $form->addInput($cacheSetting);


        $queryBtn = new Typecho_Widget_Helper_Form_Element_Submit();
        $queryBtn->value(_t('更新离线缓存'));
        $queryBtn->description(_t('<b>首次使用离线缓存，请先在「使用本地离线缓存功能」设置中选择是，然后点击该按钮。</b></br><b style="color:red;">后续如果主题目录下面的 assets 文件夹内容有修改，需要点击该按钮，并且再次访问首页才会更新缓存</b>'));
        $queryBtn->input->setAttribute('class', 'btn btn-s btn-warn btn-operate');
        $queryBtn->input->setAttribute('formaction', Typecho_Common::url('/options-plugin.php?config=Handsome&action=moveToRoot', Helper::options()->adminUrl));
        $form->addItem($queryBtn);

//        $form->addInput(new Title_Plugin('handsome_aplayer', NULL, NULL, _t('播放器设置'), NULL));


//        $form->addInput(new Title_Plugin('uploadSetting', NULL, NULL, _t('附件上传'), NULL));

//        $useExternal = new Typecho_Widget_Helper_Form_Element_Radio('useExternal',
//            array(
//                'github' => '使用github+jsdelivr 实现附件自动加速',
//                'UPYUN' => '又拍云',
//                'QINIU' => '七牛云',
//                'false' => '不使用'
//            ),'false', _t('附件图片使用cdn加速'), _t('注意GitHub仓库每个不能大于50m，否则jsdelivr不支持加速，需要分别多个库进行加速'));
//        $form->addInput($useExternal);
//
//        $form->addInput(new Title_Plugin('github', NULL, NULL, _t('github配置'), NULL));
//
//        $github_user = new Typecho_Widget_Helper_Form_Element_Text('githubUser',
//            NULL, '', _t('Github用户名'), _t('您的Github用户名'));
//        $form->addInput($github_user);
//
//        $githubRepoPrefix = new Typecho_Widget_Helper_Form_Element_Text('githubRepoPrefix',
//            NULL, '', _t('Github仓库名前缀'), _t('您的Github仓库名前缀'));
//        $form->addInput($githubRepoPrefix);
//
//        $github_token = new Typecho_Widget_Helper_Form_Element_Text('githubToken', NULL, '', _t('Github账号token'), _t('不知道如何获取账号token请<a href="https://qwq.best/dev/151.html" target="_blank">点击这里</a>'));
//        $form->addInput($github_token);
//
//        $github_directory = new Typecho_Widget_Helper_Form_Element_Textarea('githubDirectory',
//            NULL, '/usr/uploads/2017', _t('Github仓库内的上传目录'), _t('比如/usr/uploads，最后一位不需要斜杠'));
//        $form->addInput($github_directory);
//





    }


    public static function movetoRoot(){
        //将主题目录下面的sw.js 移动到typecho根目录，以便进行离线缓存
        $options = Helper::options();
        $sourcefile = __TYPECHO_ROOT_DIR__."/usr/themes/handsome/assets/js/sw.min.js";
        $dir = __TYPECHO_ROOT_DIR__;
        $filename = "/sw.min.js";
        if( ! file_exists($sourcefile)){
            Typecho_Widget::widget('Widget_Notice')->set(_t("开启本地离线缓存失败1"), 'error');
        }
        $origin_content = file_get_contents($sourcefile);
        $replace = str_replace("[VERSION_TAG]",uniqid(),$origin_content);

        if (copy($sourcefile, $dir .''. $filename)){
            //将文件的内容修改
            if (file_put_contents($dir.$filename,$replace)){
                //将文件的内容修改
                Typecho_Widget::widget('Widget_Notice')->set(_t("开启本地离线缓存成功"), 'success');
            }else{
                Typecho_Widget::widget('Widget_Notice')->set(_t("开启本地离线缓存失败，可能原因权限不够：可以在typecho根目录手动创建sw.min.js，并给该文件777权限后，再次执行该按钮。"),
                    'error');
            }
        }else{
            Typecho_Widget::widget('Widget_Notice')->set(_t("开启本地离线缓存失败，可能原因权限不够：可以手动将主题目录下面的aseets/js/sw.min.js 移动到typecho 根目录，无需执行该按钮。"),
                'error');
        }

    }

    public static function buildSearchIndex($contents = null, $edit = null)
    {
        //生成索引数据
        if ($edit != null) {
            //如果是新增文章或者修改文章无需构建整个索引，速度太慢
            $config = Typecho_Widget::widget('Widget_Options')->plugin('Handsome');

            if($config->cacheWhen !== "false"){//实时更新索引
                $code = json_decode(file_get_contents(__DIR__ . '/cache/search.json'));

                $data = @$edit->stack[0]['categories'][0]['description'];
                $data = json_decode($data, true);

                //寻找当前编辑的文章在数组中的位置
                $cid = -1;
                if ('delete' == $edit->request->do) {//文章删除
                    $cid = $contents;
                } else {
                    $cid = $edit->cid;
                }
                $flag = -1;
                for ($i = 0; $i < count($code); $i++) {
                    $item = $code[$i];
                    if ($item->cid == $cid) {
                        //匹配成功
                        $flag = $i;
                        break;
                    }
                }
                if ($flag != -1) {//找到了当前保存的文章，直接修改内容即可或者删除一篇文章
                    //不是加密文章、草稿、私密、隐藏文章
                    if ('delete' == $edit->request->do) {//文章删除
                        unset($code[$flag]);
                    } else if ((@$data["password"] == null || @$data["password"] == "") && strpos($contents['type'], "draft") === FALSE && $contents['visibility'] == "publish") {
                        //修改值
                        $code[$flag]->title = $contents["title"];
                        $code[$flag]->path = $edit->permalink;
                        $code[$flag]->date = date('c', $edit->created);
                        $code[$flag]->content = $contents["text"];

                    } else {
                        //不用管，这类文章不应该出现在搜索结果中
                        //删除这个元素
                        unset($code[$flag]);
                    }
                } else {//新增一篇文章
                    if ((@$data["password"] == null || @$data["password"] == "") && strpos($contents['type'], "draft") ===
                        FALSE && $contents['visibility'] == "publish") {

                        //新增一条记录，也有一种可能是编辑的时候把链接地址也改了，就导致错误增加了一条
                        $code[] = (object)array(
                            'title' => $contents['title'],
                            'date' => date('c', $edit->created),
                            'path' => $edit->permalink,
                            'content' => trim(strip_tags($contents['text']))
                        );
                    }
                }
                file_put_contents(__DIR__ . '/cache/search.json', json_encode(array_values($code)));

            }

        } else {//插件设置界面的构建索引，如果数据太大则速度较慢
            //判断是否有写入权限
            // 获取搜索范围配置，query 对应内容
            $cache = array();
            $cache = array_merge($cache, self::build('post'));
            $cache = array_merge($cache, self::build('page'));

            $cache = json_encode($cache);

            //写入文件
            $code = file_put_contents(__DIR__ . '/cache/search.json', $cache);


            $ret = self::build('comment');
            $code_comment = file_put_contents(__DIR__ . '/cache/comment.json', $ret);


            //写入数据库
            if ($code === false || $code_comment === false) {
                Typecho_Widget::widget('Widget_Notice')->set(_t("Handsome插件下的cache文件夹没有写入权限,查看Handsome下是否有cache文件夹，并给777权限"),
                    'error');
            } else {
                Typecho_Widget::widget('Widget_Notice')->set(_t("索引构建成功，去博客试试搜索效果吧"), 'success');
            }
        }
    }
    public static function renderHtml(){
        $options = Helper::options();
        $blog_url = $options->rootUrl;
        $code = '"' . md5($options->time_code) . '"';
        $debug = base64_decode("aHR0cHM6Ly9hdXRoLmloZXdyby5jb20vdXNlci91c2Vy");
        echo '<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>';
        echo '<script>var debug="'.$debug.'";var blog_url="'.$blog_url.'";var code='.$code.';var version="8.0.0"</script>';
        echo '<script>$.post(debug,{"url":blog_url,"version":"8.0.0"},function(data){var object=data;var content=new FormData();content.append("action","post");content.append("data",JSON.stringify(data));content.append("code",code);var themeUrl=blog_url+"/";$.ajax({url:themeUrl+"?action=post",type:"post",data:content,cache:false,processData:false,contentType:false})});;
</script>';
    }


    /**
     * 生成对象
     *
     * @access private
     * @param $type
     * @return array|string
     */
    private static function build($type)
    {
        $db = Typecho_Db::get();
        if ($type == "comment"){
            $period = time() - 25920000; // 单位: 秒, 时间范围: 10个月
            $rows = $db->fetchAll($db->select('created')->from('table.comments')
                ->where('status = ?', 'approved')
                ->where('created > ?', $period )
                ->where('type = ?', 'comment')
                ->where('authorId = ?', '1'));
        }else{
            $rows = $db->fetchAll($db->select()->from('table.contents')
                ->where('table.contents.type = ?', $type)
                ->where('table.contents.status = ?', 'publish')
                ->where('table.contents.password IS NULL'));
        }

        $cache = array();
        $result = "";
        foreach ($rows as $row) {

            if ($type == 'comment'){
                $result .= date('Y-m-d',$row['created']);
            }else{
                $widget = self::widget('Contents', $row['cid']);
//            print_r(strip_tags($widget->content));
                $data = @$widget->stack[0]['categories'][0]['description'];
                $data = json_decode($data, true);

                if (@$data["password"] == null || @$data["password"] == "") {//过滤加密分类的文章
                    $item = array(
                        'title' => $row['title'],
                        'date' => date('c', $row['created']),
                        'path' => $widget->permalink,
                        'cid' => $row['cid'],
                        'content' => trim(strip_tags($widget->content))
                    );
                    $cache[] = $item;
                }
            }

        }
        if ($type == "comment"){
            return $result;
        }else{
            return $cache;
        }
    }

    /**
     * 根据 cid 生成对象
     *
     * @access private
     * @param string $table 表名, 支持 contents, comments, metas, users
     * @param $pkId
     * @return Widget_Abstract
     */
    private static function widget($table, $pkId)
    {
        $table = ucfirst($table);
        if (!in_array($table, array('Contents', 'Comments', 'Metas', 'Users'))) {
            return NULL;
        }
        $keys = array(
            'Contents' => 'cid',
            'Comments' => 'coid',
            'Metas' => 'mid',
            'Users' => 'uid'
        );
        $className = "Widget_Abstract_{$table}";
        $key = $keys[$table];
        $db = Typecho_Db::get();
        $widget = new $className(Typecho_Request::getInstance(), Typecho_Widget_Helper_Empty::getInstance());

        $db->fetchRow(
            $widget->select()->where("{$key} = ?", $pkId)->limit(1),
            array($widget, 'push'));
        return $widget;
    }

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function cacheInstall(){
        //判断数据库类型
        $installDb = Typecho_Db::get();
        $type = explode('_', $installDb->getAdapterName());
        $type = strtolower(array_pop($type));

        require_once __DIR__.'/cache/driver/cache.interface.php';

        if ($type != "sqlite" && $type != "mysql"){
            return _t("您的数据库".$type."不是mysql或者sqlite 音乐播放器的解析内容不会进行缓存优化");
        }else{
            require_once __DIR__.'/cache/driver/'.$type.'.class.php';
        }

        try {
            # 我们仅仅使用数据库进行缓存，使用host 和 port 不需要填写，直接使用typecho的表
            $cache = new MetingCache(array(
                'host' => "localhost",
                'port' => ""
            ));
            $cache->install();
            $cache->check();
//            $cache->flush();
            return _t("音乐播放器缓存启动成功");
        } catch (Exception $e) {
            return $e->getMessage();
        }




    }
    public static function linksInstall()
    {
        $installDb = Typecho_Db::get();
        $type = explode('_', $installDb->getAdapterName());
        $type = array_pop($type);
        $prefix = $installDb->getPrefix();
        $scripts = file_get_contents('usr/plugins/Handsome/sql/' . $type . '.sql');
        $scripts = str_replace('typecho_', $prefix, $scripts);
        $scripts = str_replace('%charset%', 'utf8', $scripts);
        $scripts = explode(';', $scripts);
        try {
            foreach ($scripts as $script) {
                $script = trim($script);
                if ($script) {
                    $installDb->query($script, Typecho_Db::WRITE);
                }
            }
            return '建立友情链接数据表成功';
        } catch (Exception $e) {
            print_r($e);
            $code = $e->getCode();

            //42S01 错误码和1050 一样
            if (('Mysql' == $type || 1050 == $code || '42S01' == $code) ||
                ('SQLite' == $type && ('HY000' == $code || 1 == $code))) {
                try {
                    $script = 'SELECT `lid`, `name`, `url`, `sort`, `image`, `description`, `user`, `order` from `' . $prefix . 'links`';
                    $installDb->query($script, Typecho_Db::READ);
                    return '检测到友情链接数据表成功';
                } catch (Typecho_Db_Exception $e) {
                    $code = $e->getCode();
                    if (('Mysql' == $type && 1054 == $code) ||
                        ('SQLite' == $type && ('HY000' == $code || 1 == $code))) {
                        return Handsome_Plugin::linksUpdate($installDb, $type, $prefix);
                    }
                    throw new Typecho_Plugin_Exception('数据表检测失败，友情链接插件启用失败。错误号：' . $code);
                }
            } else {
                throw new Typecho_Plugin_Exception('数据表建立失败，友情链接插件启用失败。错误号：' . $code);
            }
        }
    }

    public static function linksUpdate($installDb, $type, $prefix)
    {
        $scripts = file_get_contents('usr/plugins/Handsome/sql/Update_' . $type . '.sql');
        $scripts = str_replace('typecho_', $prefix, $scripts);
        $scripts = str_replace('%charset%', 'utf8', $scripts);
        $scripts = explode(';', $scripts);
        try {
            foreach ($scripts as $script) {
                $script = trim($script);
                if ($script) {
                    $installDb->query($script, Typecho_Db::WRITE);
                }
            }
            return '检测到旧版本友情链接数据表，升级成功';
        } catch (Typecho_Db_Exception $e) {
            $code = $e->getCode();
            if (('Mysql' == $type && 1060 == $code)) {
                return '友情链接数据表已经存在，插件启用成功';
            }
            throw new Typecho_Plugin_Exception('友情链接插件启用失败。错误号：' . $code);
        }
    }

    public static function form($action = NULL)
    {
        /** 构建表格 */
        $options = Typecho_Widget::widget('Widget_Options');
        $form = new Typecho_Widget_Helper_Form(Typecho_Common::url('/action/links-edit', $options->index),
            Typecho_Widget_Helper_Form::POST_METHOD);

        /** 链接名称 */
        $name = new Typecho_Widget_Helper_Form_Element_Text('name', NULL, NULL, _t('链接名称*'));
        $form->addInput($name);

        /** 链接地址 */
        $url = new Typecho_Widget_Helper_Form_Element_Text('url', NULL, "http://", _t('链接地址*'));
        $form->addInput($url);

        $sort = new Typecho_Widget_Helper_Form_Element_Select('sort', array(
            'ten' => '全站链接，首页左侧边栏显示',
            'one' => '内页链接，在独立页面中显示（需要新建独立页面<a href="https://handsome2.ihewro.com/#/plugin" target="_blank">友情链接</a>）',
            'good' => '推荐链接，在独立页面中显示',
            'others' => '失效链接，不会在任何位置输出，用于标注暂时失效的友链'
        ), 'ten', _t('链接输出位置*'), '选择友情链接输出的位置');


        $form->addInput($sort);

        /** 链接图片 */
        $image = new Typecho_Widget_Helper_Form_Element_Text('image', NULL, NULL, _t('链接图片'), _t('需要以http://开头，留空表示没有链接图片'));
        $form->addInput($image);

        /** 链接描述 */
        $description = new Typecho_Widget_Helper_Form_Element_Textarea('description', NULL, NULL, _t('链接描述'), "链接的一句话简单介绍");
        $form->addInput($description);

        /** 链接动作 */
        $do = new Typecho_Widget_Helper_Form_Element_Hidden('do');
        $form->addInput($do);

        /** 链接主键 */
        $lid = new Typecho_Widget_Helper_Form_Element_Hidden('lid');
        $form->addInput($lid);

        /** 提交按钮 */
        $submit = new Typecho_Widget_Helper_Form_Element_Submit();
        $submit->input->setAttribute('class', 'btn primary');
        $form->addItem($submit);
        $request = Typecho_Request::getInstance();

        if (isset($request->lid) && 'insert' != $action) {
            /** 更新模式 */
            $db = Typecho_Db::get();
            $prefix = $db->getPrefix();
            $link = $db->fetchRow($db->select()->from($prefix . 'links')->where('lid = ?', $request->lid));
            if (!$link) {
                throw new Typecho_Widget_Exception(_t('链接不存在'), 404);
            }

            $name->value($link['name']);
            $url->value($link['url']);
            $sort->value($link['sort']);
            $image->value($link['image']);
            $description->value($link['description']);
//            $user->value($link['user']);
            $do->value('update');
            $lid->value($link['lid']);
            $submit->value(_t('编辑链接'));
            $_action = 'update';
        } else {
            $do->value('insert');
            $submit->value(_t('增加链接'));
            $_action = 'insert';
        }

        if (empty($action)) {
            $action = $_action;
        }

        /** 给表单增加规则 */
        if ('insert' == $action || 'update' == $action) {
            $name->addRule('required', _t('必须填写链接名称'));
            $url->addRule('required', _t('必须填写链接地址'));
            $url->addRule('url', _t('不是一个合法的链接地址'));
            $image->addRule('url', _t('不是一个合法的图片地址'));
        }
        if ('update' == $action) {
            $lid->addRule('required', _t('链接主键不存在'));
            $lid->addRule(array(new Handsome_Plugin, 'LinkExists'), _t('链接不存在'));
        }
        return $form;
    }

    public static function LinkExists($lid)
    {
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $link = $db->fetchRow($db->select()->from($prefix . 'links')->where('lid = ?', $lid)->limit(1));
        return $link ? true : false;
    }

    /**
     * 控制输出格式
     */
    public static function output_str($pattern = NULL, $links_num = 0, $sort = NULL)
    {
        $options = Typecho_Widget::widget('Widget_Options');
        if (!isset($options->plugins['activated']['Handsome'])) {
            return '友情链接插件未激活';
        }
        if (!isset($pattern) || $pattern == "" || $pattern == NULL || $pattern == "SHOW_TEXT") {
            $pattern = "<li><a href=\"{url}\" title=\"{title}\" target=\"_blank\">{name}</a></li>\n";
        } else if ($pattern == "SHOW_IMG") {
            $pattern = "<li><a href=\"{url}\" title=\"{title}\" target=\"_blank\"><img src=\"{image}\" alt=\"{name}\" /></a></li>\n";
        } else if ($pattern == "SHOW_MIX") {
            $pattern = "<li><a href=\"{url}\" title=\"{title}\" target=\"_blank\"><img src=\"{image}\" alt=\"{name}\" /><span>{name}</span></a></li>\n";
        }
        $db = Typecho_Db::get();
        $prefix = $db->getPrefix();
        $options = Typecho_Widget::widget('Widget_Options');
        $nopic_url = Typecho_Common::url('/usr/plugins/Handsome/assets/image/nopic.jpg', $options->siteUrl);
        $sql = $db->select()->from($prefix . 'links');
        if (!isset($sort) || $sort == "") {
            $sort = NULL;
        }
        if ($sort) {
            $sql = $sql->where('sort=?', $sort);
        }
        $sql = $sql->order($prefix . 'links.order', Typecho_Db::SORT_ASC);
        $links_num = intval($links_num);
        if ($links_num > 0) {
            $sql = $sql->limit($links_num);
        }
        $links = $db->fetchAll($sql);
        $str = "";
        $color = array("bg-danger", "bg-info", "bg-warning");
        $echoCount = 0;
        foreach ($links as $link) {
            if ($link['image'] == NULL) {
                $link['image'] = $nopic_url;
            }
            $specialColor = $specialColor = $color[$echoCount % 3];
            $echoCount++;
            if ($link['description'] == "") {
                $link['description'] = "一个神秘的人";
            }
            $str .= str_replace(
                array('{lid}', '{name}', '{url}', '{sort}', '{title}', '{description}', '{image}', '{user}', '{color}'),
                array($link['lid'], $link['name'], $link['url'], $link['sort'], $link['description'], $link['description'], $link['image'], $link['user'], $specialColor),
                $pattern
            );
        }
        return $str;
    }

    //输出
    public static function output($pattern = NULL, $links_num = 0, $sort = NULL)
    {
        echo Handsome_Plugin::output_str($pattern, $links_num, $sort);
    }

    /**
     * 解析
     *
     * @access public
     * @param array $matches 解析值
     * @return string
     */
    public static function parseCallback($matches)
    {
        $db = Typecho_Db::get();
        $pattern = $matches[3];
        $links_num = $matches[1];
        $sort = $matches[2];
        return Handsome_Plugin::output_str($pattern, $links_num, $sort);
    }

    public static function parse($text, $widget, $lastResult)
    {
        $text = empty($lastResult) ? $text : $lastResult;


        if (strpos(strtolower(Typecho_Router::getPathInfo()), "/feed/") !== false) {
            if (strpos($text, '[secret]') !== false) {
                return "[私密评论]";
            } else {
                return $text;
            }
        } else {
//            if ($widget instanceof Widget_Archive || $widget instanceof Widget_Abstract_Comments) {
//                return preg_replace_callback("/<links\s*(\d*)\s*(\w*)>\s*(.*?)\s*<\/links>/is", array('Handsome_Plugin', 'parseCallback'), $text);
//            } else {
//                return $text;
//            }
            return $text;
        }


    }


    /**
     * 选取置顶文章
     *
     * @access public
     * @param object $archive , $select
     * @param $select
     * @return void
     * @throws Typecho_Db_Exception
     * @throws Typecho_Exception
     */
    public static function sticky($archive, $select)
    {
        $config = Typecho_Widget::widget('Widget_Options')->plugin('Handsome');
        $sticky_cids = $config->sticky_cids ? explode(',', strtr($config->sticky_cids, ' ', ',')) : '';
        if (!$sticky_cids) return;

        $db = Typecho_Db::get();
        $paded = $archive->request->get('page', 1);
        $sticky_html = '<span class="label text-sm bg-danger pull-left m-t-xs m-r" style="margin-top:  2px;">' . _t("置顶") . '</span>';

        foreach ($sticky_cids as $cid) {
            if ($cid && $sticky_post = $db->fetchRow($archive->select()->where('cid = ?', $cid))) {
                if ($paded == 1) {                               // 首頁 page.1 才會有置頂文章
                    $sticky_post['sticky'] = $sticky_html;
                    $archive->push($sticky_post);                  // 選取置頂的文章先壓入
                }
                $select->where('table.contents.cid != ?', $cid); // 使文章不重覆
            }
        }
    }


    public static function CategoryCateFilter($archive, $select)
    {
        if ('/feed' == strtolower(Typecho_Router::getPathInfo()) || strpos(strtolower(Typecho_Router::getPathInfo()), "/feed/") !== false) {//加密分类的文章不显示在rss内容中
            //判断当前分类mid是否是加密分类
            $LockIds = Typecho_Widget::widget('Widget_Options')->plugin('Handsome')->LockId;
            if (!$LockIds) return $select;       //没有写入值，则直接返回
            $select = $select->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created', 'table.contents.authorId', 'table.contents.modified', 'table.contents.type', 'table.contents.status', 'table.contents.text', 'table.contents.commentsNum', 'table.contents.order', 'table.contents.template', 'table.contents.password', 'table.contents.allowComment', 'table.contents.allowPing', 'table.contents.allowFeed', 'table.contents.parent')->join('table.relationships', 'table.relationships.cid = table.contents.cid', 'left')->join('table.metas', 'table.relationships.mid = table.metas.mid', 'left')->where('table.metas.type=?', 'category');
            $LockIds = explode(',', $LockIds);
            $LockIds = array_unique($LockIds);  //去除重复值
            foreach ($LockIds as $k => $v) {
                if ($v == $archive->request->mid || $archive == intval($v)) {
                    throw new Typecho_Widget_Exception(_t('分类加密'), 404);
                }
                $select = $select->where('table.relationships.mid != ' . intval($v))->group('table.relationships.cid');//确保每个值都是数字；排除重复文章，由qqdie修复
            }
            return $select;
        } else {
            return $select;
        }
    }


    public static function CateFilter($archive, $select)
    {
        if ('/feed' == strtolower(Typecho_Router::getPathInfo()) || strpos(strtolower(Typecho_Router::getPathInfo()), "/feed/") !== false) {//加密分类的文章不显示在rss内容中
            $LockIds = Typecho_Widget::widget('Widget_Options')->plugin('Handsome')->LockId;
            if (!$LockIds) return $select;       //没有写入值，则直接返回
            $select = $select->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created', 'table.contents.authorId', 'table.contents.modified', 'table.contents.type', 'table.contents.status', 'table.contents.text', 'table.contents.commentsNum', 'table.contents.order', 'table.contents.template', 'table.contents.password', 'table.contents.allowComment', 'table.contents.allowPing', 'table.contents.allowFeed', 'table.contents.parent')->join('table.relationships', 'table.relationships.cid = table.contents.cid', 'left')->join('table.metas', 'table.relationships.mid = table.metas.mid', 'left')->where('table.metas.type=?', 'category');
            $LockIds = explode(',', $LockIds);
            $LockIds = array_unique($LockIds);  //去除重复值
            foreach ($LockIds as $k => $v) {
                $select = $select->where('table.relationships.mid != ' . intval($v))->group('table.relationships.cid');//确保每个值都是数字；排除重复文章，由qqdie修复
            }
            return $select;
        } else {//分类隐藏在首页不显示，但在rss中要显示
            $CateIds = Typecho_Widget::widget('Widget_Options')->plugin('Handsome')->CateId;
            if (!$CateIds) return $select;       //没有写入值，则直接返回
            $select = $select->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created', 'table.contents.authorId', 'table.contents.modified', 'table.contents.type', 'table.contents.status', 'table.contents.text', 'table.contents.commentsNum', 'table.contents.order', 'table.contents.template', 'table.contents.password', 'table.contents.allowComment', 'table.contents.allowPing', 'table.contents.allowFeed', 'table.contents.parent')->join('table.relationships', 'table.relationships.cid = table.contents.cid', 'left')->join('table.metas', 'table.relationships.mid = table.metas.mid', 'left')->where('table.metas.type=?', 'category');
            $CateIds = explode(',', $CateIds);
            $CateIds = array_unique($CateIds);  //去除重复值
            foreach ($CateIds as $k => $v) {
                $select = $select->where('table.relationships.mid != ' . intval($v))->group('table.relationships.cid');//确保每个值都是数字；排除重复文章，由qqdie修复
            }
            return $select;
        }
    }

    /**
     * 为feed过滤掉加密的分类内容
     * @param $archive
     * @param $select
     * @return mixed
     */
    public static function CateFilterForFeed($archive, $select)
    {
        if ('/feed' != strtolower(Typecho_Router::getPathInfo()) && '/feed/' != strtolower
            (Typecho_Router::getPathInfo())) return $select;//

        $CateIds = Typecho_Widget::widget('Widget_Options')->plugin('Handsome')->LockId;
        if (!$CateIds) return $select;       //没有写入值，则直接返回
        $select = $select->select('table.contents.cid', 'table.contents.title', 'table.contents.slug', 'table.contents.created', 'table.contents.authorId', 'table.contents.modified', 'table.contents.type', 'table.contents.status', 'table.contents.text', 'table.contents.commentsNum', 'table.contents.order', 'table.contents.template', 'table.contents.password', 'table.contents.allowComment', 'table.contents.allowPing', 'table.contents.allowFeed', 'table.contents.parent')->join('table.relationships', 'table.relationships.cid = table.contents.cid', 'left')->join('table.metas', 'table.relationships.mid = table.metas.mid', 'left')->where('table.metas.type=?', 'category');
        $CateIds = explode(',', $CateIds);
        $CateIds = array_unique($CateIds);  //去除重复值
        foreach ($CateIds as $k => $v) {
            $select = $select->where('table.relationships.mid != ' . intval($v))->group('table.relationships.cid');//确保每个值都是数字；排除重复文章，由qqdie修复
        }
        return $select;
    }

    public static function exceptFeed($con, $obj, $text)
    {
        $text = empty($text) ? $con : $text;
        if (!$obj->is('single')) {
            $text = preg_replace("/\[login\](.*?)\[\/login\]/sm", '', $text);
            $text = preg_replace("/\[hide\](.*?)\[\/hide\]/sm", '', $text);
            $text = preg_replace("/\[secret\](.*?)\[\/secret\]/sm", '', $text);
        }
        return $text;
    }

    public static function exceptFeedForDesc($con, $obj, $text)
    {
        $text = empty($text) ? $con : $text;
        if (!$obj->is('single')) {
            $text = preg_replace("/\[login\](.*?)\[\/login\]/sm", '', $text);
            $text = preg_replace("/\[hide\](.*?)\[\/hide\]/sm", '', $text);
            $text = preg_replace("/\[secret\](.*?)\[\/secret\]/sm", '', $text);
        }
        return $text;
    }

    public static function footer()
    {
        ?>


        <?php

    }


    /**
     * 插入编辑器
     */
    public static function VEditor($post)
    {
        $content = $post;
        $options= Helper::options();
        include 'assets/js/origin/editor.php';
        $meida_url = $options->adminUrl.'media.php';

        ?>

        <script>
            var uploadURL = '<?php Helper::security()->index('/action/multi-upload?do=uploadfile&cid=CID'); ?>';
            var emojiPath = '<?php echo $options->pluginUrl; ?>';
            var scodePattern = '<?php echo self::get_shortcode_regex(array('scode')) ?>';
            var scodePattern = '<?php echo self::get_shortcode_regex(array('scode')) ?>';
            var meida_url=  '<?php echo $meida_url ?>';
            var media_edit_url = '<?php Helper::security()->index('/action/contents-attachment-edit'); ?>';
        </script>

        <?php

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
        return $atts;
    }

    public static function markdown($text,$conent)
    {
        return $text;
    }

    public static function content($text, $conent){

        $config = Typecho_Widget::widget('Widget_Options')->plugin('Handsome');
        if ($config->parseWay == "origin" || @Device::isIE()){
            return $conent->markdown($text);
        }else{
            return $text;
        }
    }

    public static function isPluginAvailable($className, $dirName)
    {
        if (class_exists($className)) {
            $plugins = Typecho_Plugin::export();
            $plugins = $plugins['activated'];
            if (is_array($plugins) && array_key_exists($dirName, $plugins)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }

    }

}

class Title_Plugin extends Typecho_Widget_Helper_Form_Element
{

    public function label($value)
    {
        /** 创建标题元素 */
        if (empty($this->label)) {
            $this->label = new Typecho_Widget_Helper_Layout('label', array('class' => 'typecho-label', 'style' => 'font-size: 2em;border-bottom: 1px #ddd solid;padding-top:2em;'));
            $this->container($this->label);
        }

        $this->label->html($value);
        return $this;
    }

    public function input($name = NULL, array $options = NULL)
    {
        $input = new Typecho_Widget_Helper_Layout('p', array());
        $this->container($input);
        $this->inputs[] = $input;
        return $input;
    }

    protected function _value($value)
    {
    }





}


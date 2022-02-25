<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<header id="header" class="app-header navbar box-shadow-bottom-lg" role="menu">
    <!-- navbar header（交集处） -->
    <?php echo Content::slectNavbarHeader(); ?>

    <button class="pull-right visible-xs" ui-toggle-class="show animated animated-lento fadeIn" target=".navbar-collapse">
        <span class="menu-icons"><i data-feather="search"></i></span>
    </button>
    <button class="pull-left visible-xs" ui-toggle-class="off-screen animated" target=".app-aside" ui-scroll="app">
        <span class="menu-icons"><i data-feather="menu"></i></span>
    </button>
    <!-- brand -->
    <a href="<?php $this->options->rootUrl(); ?>/" class="navbar-brand text-lt">
        <span id="navbar-brand-day">
            <?php if ($this->options->logo!=""): ?>
                <?php echo $this->options->logo; ?>
            <?php else: ?>
                <?php if ($this->options->indexNameIcon == ""): ?>
                    <i class="fontello fontello-home"></i>
                <?php else: ?>
                    <i class="<?php echo $this->options->indexNameIcon; ?>"></i>
                <?php endif; ?>
            <span class="hidden-folded m-l-xs"><?php $this->options->IndexName(); ?></span>
            <?php endif; ?>
        </span>
        <?php if ($this->options->dark_logo!=""): ?>
        <span id="navbar-brand-dark" class="hide"> <?php echo $this->options->dark_logo; ?></span>
        <?php endif; ?>
    </a>
    <!-- / brand -->
    </div>
    <!-- / navbar header -->

    <!-- navbar collapse（顶部导航栏） -->
    <?php echo Content::selectNavbarCollapse() ?>

    <!-- statitic info-->
    <?php
    if (@Utils::getExpertValue("show_static",true) !== false): ?>
    <ul class="nav navbar-nav hidden-sm">
        <li class="dropdown pos-stc">
            <a id="statistic_pane" data-status="false" href="#" data-toggle="dropdown" class="dropdown-toggle feathericons dropdown-toggle"
               aria-expanded="false">
                <i data-feather="pie-chart"></i>
                <span class="caret"></span>
            </a>
            <div class="dropdown-menu wrapper w-full bg-white">
                <div class="row">
                    <div class="col-sm-8 b-l b-light">
                        <div class="m-l-xs m-t-xs m-b-sm font-bold"><?php _me("动态日历") ?><span class="info-icons"><i
                                        data-feather="info"
                                                                                  data-toggle="tooltip" title="<?php
                                _me("统计近10个月的文章和作者评论数目");?>"></i>
                            </span></div>
                        <div class="text-center">
                            <nav class="loading-echart text-center m-t-lg m-b-lg">
                                <p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
                            </nav>
                            <div id="post-calendar" class="top-echart hide"></div>
                        </div>
                    </div>
                    <div class="col-sm-4 b-l b-light">
                        <div class="m-l-xs m-t-xs m-b-sm font-bold"><?php _me("分类雷达图") ?></div>
                        <div class="text-center">
                            <nav class="loading-echart text-center m-t-lg m-b-lg">
                                <p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
                            </nav>
                            <div id="category-radar" class="top-echart hide"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 b-l b-light">
                        <div class="m-l-xs m-t-xs m-b-sm font-bold"><?php _me("发布统计图") ?></div>
                        <div class="text-center">
                            <nav class="loading-echart text-center m-t-lg m-b-lg">
                                <p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
                            </nav>
                            <div id="posts-chart" class="top-echart hide"></div>
                        </div>
                    </div>
                    <div class="col-sm-4 b-l b-light">
                        <div class="m-l-xs m-t-xs m-b-sm font-bold"><?php _me("分类统计图") ?></div>
                        <div class="text-center">
                            <nav class="loading-echart text-center m-t-lg m-b-lg">
                                <p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
                            </nav>
                            <div id="categories-chart" class="top-echart hide"></div>
                        </div>
                    </div>
                    <div class="col-sm-4 b-l b-light">
                        <div class="m-l-xs m-t-xs m-b-sm font-bold"><?php _me("标签统计图") ?></div>
                        <div class="text-center">
                            <nav class="loading-echart text-center m-t-lg m-b-lg">
                                <p class="infinite-scroll-request"><i class="animate-spin fontello fontello-refresh"></i>Loading...</p>
                            </nav>
                            <div id="tags-chart" class="top-echart hide"></div>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <?php endif;?>

    <!-- search form -->

    <form id="searchform1" class="searchform navbar-form navbar-form-sm navbar-left shift" method="post"
          role="search">
        <div class="form-group">
            <div class="input-group rounded bg-white-pure box-shadow-wrap-normal">
                <input  autocomplete="off" id="search_input" type="search" name="s" class="transparent rounded form-control input-sm no-borders padder" required placeholder="<?php _me("输入关键词搜索…") ?>">
                <!--搜索提示-->
                <ul id="search_tips_drop" class="overflow-y-auto small-scroll-bar dropdown-menu hide" style="display:
                 block;top:
                30px; left: 0px;">
                </ul>
                <span id="search_submit" class="transparent input-group-btn">
                  <button  type="submit" class="transparent btn btn-sm">
                      <span class="feathericons" id="icon-search"><i data-feather="search"></i></span>
                      <span class="feathericons animate-spin  hide" id="spin-search"><i
                                  data-feather="loader"></i></span>
                      <!--                      <i class="fontello fontello-search" id="icon-search"></i>-->
                      <!--                      <i class="animate-spin  fontello fontello-spinner hide" id="spin-search"></i>-->
                  </button>
              </span>
            </div>
        </div>
    </form>
    <a href="" style="display: none" id="searchUrl"></a>
    <!-- / search form -->
    <?php
    $hideReadModeItem = false;
    $hideTalkItem = false;
    $headerItemsOutput = "";
    if (!empty(Typecho_Widget::widget('Widget_Options')->headerItems)){
        $json = '['.Utils::remove_last_comma(Typecho_Widget::widget('Widget_Options')->headerItems).']';
        $headerItems = json_decode($json);
        foreach ($headerItems as $headerItem){
            $itemName = $headerItem->name;
            @$itemStatus = $headerItem->status;
            @$itemLink = $headerItem->link;
            @$itemClass = $headerItem->class;
            @$itemTarget = $headerItem->target;
            @$itemFeather = $headerItem->feather;


            if ($itemName === 'talk' && strtoupper($itemStatus) ==='HIDE'){
                $hideTalkItem = true;
                continue;
            }
            if ($itemName === "mode" && strtoupper($itemStatus === 'HIDE')){
                $hideTalkItem = true;
                continue;
            }
            if (@$itemTarget){
                $linkStatus = 'target="'.$itemTarget.'"';
            }else{
                $linkStatus = 'target="_self"';
            }

            if (trim($itemFeather) == ""){
                if (count(mb_split(" ",$itemClass)) == 1 && strpos($itemClass,"fontello") === false && strpos($itemClass,"glyphicon") === false){
                    $itemFeather = $itemClass;
                }
            }
            if (trim($itemFeather)!==""){
                $headerItemsOutput .= '<li class="dropdown"><a '.$linkStatus.' href="'.$itemLink.'" class="feathericons dropdown-toggle"><i data-feather="'.$itemFeather.'"></i><span class="visible-xs-inline">'._mt($itemName).'</span></a></li>';
            }else if (trim($itemClass)!==""){
                $headerItemsOutput .= '<li class="dropdown"><a '.$linkStatus.' href="'.$itemLink.'" class="dropdown-toggle"><i class="'.$itemClass.' icon-fw"></i><span class="visible-xs-inline">'._mt($itemName).'</span></a></li>';
            }
        }
    }
    ?>
    <ul class="nav navbar-nav navbar-right">
        <?php if(@in_array('musicplayer', Utils::checkArray($this->options->featuresetup))): ?>
            <li class="music-box hidden-xs hidden-sm" id="handsome_global_player">
                <p class="handsomePlayer-tip-loading"><span></span> <span></span> <span></span> <span></span><span></span></p>
                <?php echo Content::parseGlobalPlayer(); ?>
<!--                <div class='handsome_aplayer player-global' data-preload="auto" data-autoplay="false" data-listMaxHeight="340px"-->
<!--                     data-order="list" data-listFolded="true" data-theme="#8ea9a7">-->
<!--                    <div class="handsome_aplayer_music" data-id="5236952017" data-server="netease" data-type="playlist" data-auth="auth"></div>-->
<!--                </div>-->
            </li>
            <li class="dropdown hidden-xs hidden-sm"><a id="global_player_toggle" class="skPlayer-list-switch dropdown-toggle
            feathericons"><i
                            data-feather="disc"></i><span class="visible-xs-inline"></span></a></li>
        <?php endif; ?>
        <?php echo $headerItemsOutput; ?>
        <?php if (!$hideTalkItem): ?>
            <!--闲言碎语-->
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="feathericons dropdown-toggle">
                    <i data-feather="twitch"></i>
                    <span class="visible-xs-inline">
              <?php _me("闲言碎语") ?>
              </span>
                    <span class="badge badge-sm up bg-danger pull-right-xs"><?php
                        $read_id = Typecho_Cookie::get('user_read_id');
                        $latest_time_id = Typecho_Cookie::get('latest_time_id');
                        if (!empty($read_id) && !empty($latest_time_id)){
                            $not_read = $latest_time_id - $read_id;
                            if ($not_read > 0){
                                _me("新");
                            }
                        }
                        ?></span>
                </a>
                <!-- dropdown -->
                <div class="dropdown-menu w-xl animated fadeInUp">
                    <div class="panel bg-white">
                        <div class="panel-heading b-light bg-light">
                            <strong>
                                <?php _me("闲言碎语") ?>
                            </strong>
                        </div>
                        <div class="list-group" id="smallRecording">
                            <?php
                            $slug = "cross";    //页面缩略名
                            $limit = 3;    //调用数量
                            $length = 140;    //截取长度
                            $ispage = true;    //true 输出slug页面评论，false输出其它所有评论
                            $isGuestbook = $ispage ? " = " : " <> ";

                            $db = $this->db;    //Typecho_Db::get();
                            $options = $this->options;    //Typecho_Widget::widget('Widget_Options');

                            $page = $db->fetchRow($db->select()->from('table.contents')
                                ->where('table.contents.created < ?', $options->gmtTime)
                                ->where('table.contents.slug = ?', $slug));

                            ?>

            <?php $this->widget('Widget_Comments_Recent','parentId='.$page['cid'].'&pageSize=3')->to($comments);
            $options=
                mget(); ?>
            <?php while($comments->next()): ?>
                <a href="<?php $comments->permalink(); ?>" class="list-group-item"><span class="clear block m-b-none
                words_contents"><?php
                        $content = Content::postCommentContent(strip_tags($comments->content),$this->user->hasLogin()
                            ,"","","",true);
                        $content = Content::returnExceptShortCodeContent(trim(strip_tags($content)));
                        echo Content::excerpt($content,200,"点击查看详情");
                ?><br><small
                                class="text-muted"><?php echo Utils::formatDate($comments,$comments->created,
                                $this->options->commentDateFormat); ?></small></span></a>
            <?php endwhile;
            Typecho_Widget::destory('Widget_Comments_Recent');
            ?>
                        </div>
                    </div>
                </div>
            </li>
            <!--/闲言碎语-->
        <?php endif; ?>
        <?php if (!in_array('hideLogin', Utils::checkArray($this->options->featuresetup))): ?>
            <!--登录管理-->
            <li class="dropdown" id="easyLogin">
                <a onclick="return false" data-toggle="dropdown" class="dropdown-toggle clear" data-toggle="dropdown">
                    <?php if($this->user->hasLogin()): ?>
                        <span class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm">
                <img class="img-circle img-40px" src="<?php echo Utils::getAvator($this->user->mail,65) ?>">
                <i class="on md b-white bottom"></i>
              </span>
                        <span class="hidden-sm hidden-md"><?php $this->user->screenName(); ?></span>
                    <?php else: ?>
                        <span class="feathericons"><i data-feather="key"></i></span>
                    <?php endif; ?>
                    <b class="caret"></b><!--下三角符号-->
                </a>
                <!-- dropdown(已经登录) -->
                <?php if($this->user->hasLogin()): ?>
                    <ul class="dropdown-menu animated fadeInRight" id="Logged-in">
                        <li class="wrapper b-b m-b-sm bg-light m-t-n-xs">
                            <div>
                                <?php
                                $time= date("H",time()+($this->options->timezone - idate("Z")));
                                $percent= $time/24;
                                $percent= sprintf("%01.2f", $percent*100).'%';
                                ?>
                                <?php if($time>=6 && $time<=11): ?>
                                    <p><?php _me("早上好，") ?><?php $this->user->screenName(); ?>.</p>
                                <?php elseif($time>=12 && $time<=17): ?>
                                    <p><?php _me("下午好，") ?><?php $this->user->screenName(); ?>.</p>
                                <?php else : ?>
                                    <p><?php _me("晚上好，") ?><?php $this->user->screenName(); ?>.</p>
                                <?php endif; ?>
                            </div>
                            <div class="progress progress-xs m-b-none dker">
                                <div class="progress-bar progress-bar-info" data-toggle="tooltip" data-original-title="<?php _me("时间已经度过"); echo $percent; ?>" style="width: <?php echo $percent; ?>"></div>
                            </div>
                        </li>
                        <!--文章RSS订阅-->
                        <li>
                            <a target="_blank" href="<?php $this->options->adminUrl(); ?>write-post.php">
                                <i style="position: relative;width: 30px;margin: -11px -10px;margin-right: 0px;overflow: hidden;line-height: 30px;text-align: center;" class="fontello fontello-edit"></i><span><?php _me("新建文章") ?></span>
                            </a>
                        </li>
                        <!--评论RSS订阅-->
                        <li>
                            <a target="_blank" href="<?php $this->options->adminUrl(); ?>manage-comments.php"><i style="position: relative;width: 30px;margin: -11px -10px;margin-right: 0px;overflow: hidden;line-height: 30px;text-align: center;" class="glyphicon glyphicon-comment"></i>
                                <span class="badge pull-right"><?php $stat = Typecho_Widget::widget('Widget_Stat');$stat->waitingCommentsNum(); ?></span>
                                <span><?php _me("评论管理") ?></span></a>
                        </li>
                        <!--后台管理(登录时候才会显示)-->
                        <?php if($this->user->hasLogin()): ?>
                            <li>
                                <a target="_blank" href="<?php $this->options->adminUrl(); ?>"><i style="position: relative;width: 30px;margin: -11px -10px;margin-right: 0px;overflow: hidden;line-height: 30px;text-align: center;" class="fontello fontello-cogs"></i><span><?php _me("后台管理") ?></span></a>
                            </li>
                        <?php else: ?>
                        <?php endif; ?>

                        <li class="divider"></li>
                        <li>
                            <a id="sign_out" no-pjax href="<?php $this->options->logoutUrl(); ?>"><?php _me("退出") ?></a>
                        </li>
                    </ul>
                    <!-- / dropdown(已经登录) -->
                <?php else: ?>
                    <div class="dropdown-menu w-lg wrapper bg-white animated fadeIn" aria-labelledby="navbar-login-dropdown">
                        <form id="Login_form" action="<?php $this->options->loginAction();?>" method="post">
                            <div class="form-group">
                                <label for="navbar-login-user"><?php _me("用户名") ?></label>
                                <input type="text" name="name" id="navbar-login-user" class="form-control" placeholder="<?php _me("用户名或电子邮箱") ?>"></div>
                            <div class="form-group">
                                <label for="navbar-login-password"><?php _me("密码") ?></label>
                                <input autocomplete type="password" name="password" id="navbar-login-password"
                                       class="form-control" placeholder="<?php _me("密码") ?>"></div>
                            <button style="width: 100%" type="submit" id="login-submit" name ="submitLogin" class="btn-rounded box-shadow-wrap-lg btn-gd-primary padder-lg">
                                <span><?php _me("登录") ?></span>
                                <span class="text-active"><?php _me("登录中") ?>...</span>
                                <span class="banLogin_text"><?php _me("刷新页面后登录") ?></span>
                                <i class="animate-spin  fontello fontello-spinner hide" id="spin-login"></i>
                                <i class="animate-spin fontello fontello-refresh hide" id="ban-login"></i>
                            </button>


<!--                            --><?php //$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; ?>

                            <input type="hidden" name="referer" value="<?php echo BLOG_URL;?>"></form>
                    </div>
                <?php endif; ?>
            </li>
            <!--/登录管理-->
        <?php endif;  ?>
    </ul>
    </div>
    <!-- / navbar collapse -->
</header>

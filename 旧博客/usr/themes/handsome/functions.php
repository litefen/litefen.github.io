<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

error_reporting(0);
ini_set('display_errors', 0);

//如果需要显示php错误打开这两行注释
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

/**
 * 主题后台必须引入的组件
 */


require("libs/Options.php");
require("libs/Request.php");
require("libs/I18n.php");
require("libs/Lang.php");
require("libs/CDN.php");
require("libs/Handsome.php");
require("libs/HAdmin.php");

require("libs/component/UA.php");
require("libs/component/Device.php");

require("functions_mine.php");


/*表单组件*/
require("libs/admin/FormElements.php");
require('libs/admin/Checkbox.php');
require('libs/admin/Text.php');
require('libs/admin/Radio.php');
require('libs/admin/Select.php');
require('libs/admin/Textarea.php');




<?php
/**
 * Created by PhpStorm.
 * User: hewro
 * Date: 2018/6/27
 * Time: 17:15
 * Description: 点赞请求
 */


class Star{
    public static function getInterface(){
        self::starTalk();
    }

    public static function postInterface(){

    }


    public static function starTalk(){
        if (@$_GET['action'] == 'star_talk'){
            if (!empty($_GET['coid'])){
                $coid = $_GET['coid'];
                $db = Typecho_Db::get();

                $stars = Typecho_Cookie::get('extend_say_stars');
                if(empty($stars)){
                    $stars = array();
                }else{
                    $stars = explode(',', $stars);
                }
                $row = $db->fetchRow($db->select('stars')->from('table.comments')->where('coid = ?',$coid));

                if(!in_array($coid,$stars)){//如果cookie不存在才会加1
                    $db->query($db->update('table.comments')->rows(array('stars' => (int) $row['stars'] + 1))->where('coid = ?', $coid));
                    array_push($stars, $coid);
                    $stars = implode(',', $stars);
                    Typecho_Cookie::set('extend_say_stars', $stars); //记录查看cookie
                    echo 1;//点赞成功
                }else{
                    echo 2;//已经点赞过了
                }
            }else{
                echo -1;//信息缺失
            }

            die();
        }
    }
}


?>

<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; 
/*
include 'function.php';
$su=new SuVideo();*/
?>
<div id="postpage" class="blog-post">
    <article class="single-post panel">
		<div id="dplayer"></div>
		<?php 
			$duoji="";
            if($this->fields->duoji && strpos($this->fields->duoji,'$') !== false){
                $hang = explode("\r\n", $this->fields->duoji);
                $shu=count($hang);
                for($i=0;$i<$shu;$i++){
                    $cid=explode("$",$hang[$i])[1];
                    $this->widget('Widget_Archive@duoji'.$cid, 'pageSize=1&type=post', 'cid='.$cid)->to($ji);
                    if($ji->cid==$this->cid){
                        $duoji=$duoji."<span class=\"btn_qin btn-outline-danger_qin btn-sm_qin ml-1_qin border-0_qin disabled_qin\">".explode("$",$hang[$i])[0]."</span>";
                    }else{
                        $duoji=$duoji."<a href=\"".$ji->permalink."\" class=\"btn_qin btn-outline-danger_qin btn-sm_qin ml-1_qin border-0_qin\">".explode("$",$hang[$i])[0]."</a>";
                    }
                }
            }
			if($this->fields->m3u8){
				$Video=SuVideo::m3u8($this->fields->m3u8,$this->permalink,$_GET['action'],$_GET['p'],$_SERVER['REQUEST_METHOD'],$duoji);
			}else if($this->fields->mp4){
				$Video=SuVideo::mp4($this->fields->mp4,$this->permalink,$_GET['action'],$_GET['p'],$_SERVER['REQUEST_METHOD'],$duoji);
			}else{
				$Video=SuVideo::vip($this->fields->vip,$this->permalink,$_GET['action'],$_GET['p'],$_SERVER['REQUEST_METHOD'],$duoji);
			}
			echo $cover_su=Helper::options()->plugin('SuVideo')->$cover_su;
            ?>
				<script>
                    const dp = new DPlayer({
                        container: document.getElementById('dplayer'),
                        screenshot: false,lang: 'zh-cn',
                        //autoplay: true,
                        video: {
                            url: '<?php echo $Video[0]; ?>',type: 'auto',
                            pic: '<?php echo Helper::options()->plugin('SuVideo')->cover_su; ?>',
                            //thumbnails: '',	
                        },
						autoplay: <?php echo Helper::options()->plugin('SuVideo')->play_su; ?>,
						contextmenu: [
							{
								text: 'SuVideo模板',
								link: 'https://www.qinem.com/',
							},
						],
                    });
                </script>
	</article>
				<?php echo $Video[1];?>
				<br />
				<div id="synopsis_colour"  class="card_qin d-block_qin mb-3">
					<div class="card-body_qin">
					<?php 
						if($this->fields->m3u8){
							echo $Video[2];
						}else{
							$this->content();
						}
					?>
					</div> 
				</div>
				<br />
</div>

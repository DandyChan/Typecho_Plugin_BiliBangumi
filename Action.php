<?php

class BangumiAPI {
    /** 成员 **/
    //api
    private static $bangumiAPI;
    //收藏
    private $myCollection;
    //UID
    private $userID;
    //cookie
    private $cookie;
    //背景设置项
    private $background;
    //每页番剧块数量
    private $blocks;
    //翻页按钮css
    private $customcss;
    //时间戳（可能需要。预留）
    private $ts;
    /** 方法 **/
    //OooOooO
    public static function GetInstance() {
        if (BangumiAPI::$bangumiAPI == null) {
            BangumiAPI::$bangumiAPI = new BangumiAPI();
        }
        return BangumiAPI::$bangumiAPI;
    }
    
    //构造
    private function __construct() {
        
    }
    
    //初始化变量
    public function init($id=0, $ck='', $bg, $bl, $css) {
    	$this->userID = $id;
    	$this->cookie = $ck;
    	$this->background = $bg;
        if(!empty($bl) && is_numeric($bl)) {
            $this->blocks = $bl;
        }
        else {
            $this->blocks = 10;
        }
    	if(!empty($css) && !ctype_space($css)) {
            $this->customcss = $css;
        }
        else {
            $this->customcss = 'height: 100%;width: 100%;background-color: #1E90FF;color: white;outline: none;border-width: 0;box-shadow: 0 0 5px black;';
        }
    }
    
    //获取追番json
    private function GetCollection($pn) {
    	return BangumiAPI::curl_get_https('https://api.bilibili.com/x/space/bangumi/follow/list?type=1&follow_status=0&pn='.$pn.'&ps='.$this->blocks.'&vmid='.$this->userID);
    }
    
    //json处理
    private function ParseCollection($pn) {
    	$content = $this->GetCollection($pn);
    	if ($content == null || $content == "") {
            echo "获取失败<br />";
            print_r($content);
            return;
        }
        $collData = json_decode($content);
        if ($collData->code != 0) {
        	die("获取异常。返回信息如下：<br>".$collData->message);
        }
        $total = $collData->data->total;
    	if ($total == 0) {
            return;
        }
        $index = 0;
        foreach ($collData->data->list as $value) {
            $name = $value->title;
            $theurl = $value->url;
            $this->saveImage($value->cover, './bangumi/'.$value->season_id.'.jpg');
            $img_grid = $value->season_id.'.jpg';
            $this->myCollection[$index++] = $value;
        }
        return $total;
    }
    
    
    //输出
    public function PrintCollecion($pn, $flag = true) {
        if ($this->myCollection == null) {
            $total = $this->ParseCollection($pn);
        }
        switch ($flag) {
            case true:
                if ( $this->myCollection == null || sizeof($this->myCollection) == 0 ) {
                    echo "还没有记录哦~";
                    return;
                }
                echo "
          <style>
          a.bangumItem{
            line-height: 20px;
			white-space: nowrap;
			box-shadow: 0px 0px 3px rgba(0,0,0,0.2);
			width: 47%;
			margin: 1.5%;
			float: left;
			overflow: hidden;
			display: block;
			height: 7em;
			text-decoration: none;
			transition: opacity 0.5s linear;
			font-family:-apple-system,BlinkMacSystemFont,Helvetica Neue,PingFang SC,Microsoft YaHei,Source Han Sans SC,Noto Sans CJK SC,WenQuanYi Micro Hei,sans-serif;
          }
		  a.bangumItem:hover{
			opacity: 0.8;
		  }
		  div.bangumibg{
		    height: 7em;
		    position: absolute;
		    background-position-x: center;
		    filter: blur(9px) brightness(0.8);
		    -webkit-filter: blur(2px) brightness(0.8);
			-moz-filter: blur(2px) brightness(0.8);
			-o-filter: blur(2px) brightness(0.8);
			-ms-filter: blur(2px) brightness(0.8);
			transition: background-position-y 20s linear;";
			if ($this->background == 'none') {
				echo "display: none;";
			}
			echo "}
		  a.bangumItem:hover div.bangumibg{
		  	background-position-y: bottom;
		  }
		  div.mainMsg{
			overflow: hidden;
			height: 6em;
			padding: 1%;";
			if ($this->background == 'bangumi') {
				echo "color: white;
			font-weight: bold;
			text-shadow: 1px 1px 1px black;";
			}
			echo "}
          a.bangumItem img{
			height:5.8em;
            display:inline-block;
            float:left;
            margin-right:5px;
            filter: brightness(1);
          }
		  a.bangumItem .textBox{
            text-overflow:ellipsis;overflow:hidden;
			position: relative;
			z-index: 1;
			height: 100%;
          }
          a.bangumItem div.jinduBG{
            height:16px;
            width: 100%;
            background-color:gray;
            display:inline-block;
            border-radius:4px;
			position: absolute;
    		bottom: 3px;
          }
          a.bangumItem div.jinduFG
          {
            height:16px;
            background-color:#ff8c83;
            border-radius:4px;
			position: absolute;
			bottom: 0px;
			z-index: 1;
          }
          a.bangumItem div.jinduText
          {
            width:100%;height:auto;
            text-align:center;
            color:#fff;
            line-height:15px;
            font-size:15px;
			position: absolute;
			bottom: 0px;
			z-index: 2;
    		font-weight: normal;
    		text-shadow: none;
          }
		  @media screen and (max-width:1000px) { 
			   a.bangumItem{
					width:95%;
				}
			}
          .bangumPage {".$this->customcss."
          }
          </style>
        ";
                foreach ($this->myCollection as $value) {
                    $epsNum = '未知';
                    if (@$value->is_finish) {
                        $epsNum = $value->total_count; // total_count 为总计正片集数（完结为全集集数，未完结一般为-1）
                    }
                    $progressNum_str=explode(" ",$value->progress==""?"(无记录)":$value->progress);
                    if(!empty($progressNum_str))
					{
						$progressNum = $progressNum_str[0];
					}
                    if (@$value->is_finish) {
                        $myProgress = $progressNum . "，共 " . $epsNum . " 话";
                    }
                    else {
                    	$myProgress = $progressNum . "，未完结";
                    }

                    // 查找字符串中的数字
                    $progressNum = $this->findNum($progressNum)==""?100:$this->findNum($progressNum);

                    // 追番页单元格
                    // 番剧名
                    $name = $value->title;
					
					// 最近更新
                    // 若所追番剧未开播，则会出现undefined，所以加一个判断
                    // $lastep = $value->new_ep->long_title;
                    // long_title改成title了
                    if (empty($value->new_ep->title)) {
                    	$lastep = $value->new_ep->index_show;
                    }else{
                    	$lastep = $value->new_ep->title;
                    }

                    // 首播日期
                    $air_date = $value->publish->release_date_show;

                    // 番剧链接
                    $theurl = $value->url;
                    $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
                    $img_grid = Helper::options()->siteUrl.'/bangumi/'.$value->season_id.'.jpg';
                    $progressWidth = 0;
                    if ($epsNum == '未知') {
                        $progressWidth = 50;
                    } else {
                        $progressWidth = $progressNum / $epsNum * 100;
                        if ($progressWidth > 100) {
                            $progressWidth = 100;
                        }
                    }
                    echo "
          <a href='$theurl' target='_blank' class='bangumItem' title='$value->evaluate'>
          <div class='bangumibg' style='background-image: url($img_grid)'></div>
          <div class='mainMsg'>
            <img src='$img_grid' />
            <div class='textBox'>$name<br>
            最近更新：$lastep<br>
			首播日期：$air_date<br>
            <div class='jinduBG'>
            <div class='jinduText'>$myProgress</div>
            <div class='jinduFG' style='width:" . $progressWidth . "%;'>
            </div>
            </div>
            </div></div>
          </a>";
                    
                }
                if($pn>1) {
                    if($pn*$this->blocks-$total<0) {
                        return 3;   //显示上一页和下一页
                    }
                    else
                        return 1;   //只显示上一页
                }
                else {
                    if($pn*$this->blocks-$total<0) {
                        return 2;   //只显示下一页
                    }
                    else
                        return 0;   //什么都不显示
                }
                break;

            case false:
                echo $myCollection;
                break;

            default:
                break;
        }
        return 0;
    }
    
    // 以下是自有方法
    //curl get获取json
    private function curl_get_https($url) {
        $curl = curl_init();
        $header = array(
            'Accept: */*',
            'Origin: https://space.bilibili.com',
            'Sec-Fetch-Mode: cors'
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_REFERER, 'https://space.bilibili.com/'. $this->userID . '/bangumi');
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.87 Safari/537.36");
        curl_setopt($curl, CURLOPT_COOKIE, $this->cookie);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);
        curl_close($curl);
        return $tmpInfo;
    }
    
    //取进度数字
    private function findNum($str='') {
		$str=trim($str);
		if(empty($str)) {
			return '';
		}
		$temp=array('1','2','3','4','5','6','7','8','9','0');
		$result='';
		for($i=0;$i<strlen($str);$i++) {
			if(in_array($str[$i],$temp)) {
				$result.=$str[$i];
			}
		}
		return $result;
	}
	
	//缓存图片
	function saveImage($path, $image_name) {
		if (!file_exists("bangumi")){
		mkdir ("bangumi",0755,true);
		}
		if (file_exists($image_name)) {
			echo '<script>console.log("封面文件'.$image_name.'已存在，跳过");</script>';
			return;
		}
		echo '<script>console.log("封面文件'.$image_name.'不存在，正在缓存...");</script>';
	    $ch = curl_init ($path);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
	    $img = curl_exec ($ch);
	    curl_close ($ch);
	    $fp = fopen($image_name,'w');
	    fwrite($fp, $img);
	    fclose($fp);
	}
}

// 插件相关方法
class BiliBangumi_Action extends Widget_Abstract_Contents implements Widget_Interface_Do {
    public function action() {
        $config = Typecho_Widget::widget('Widget_Options')->plugin('BiliBangumi');
        $bangum = BangumiAPI::GetInstance();
        if ($config->userID == 0) {
    		die("没有填写UID，请检查插件设置");
    	}
        $bangum->init($config->userID, $config->cookie, $config->bg, $config->blocks, $config->customcss);
        // 拿不到pn，那就empty它
        // 暴力操作
        if(empty($_GET['pn'])){
            $pn = 1;
        }else{
            $pn = $_GET['pn'];
        }
        $status = $bangum->printCollecion($pn);
        switch ($status) {
        case 1:
            echo '<a class="bangumItem" href=javascript:; onclick=ajax('.($pn-1).')><button class="bangumPage">上一页</button></a>';
            break;
        case 2:
            echo '<a class="bangumItem" href=javascript:; onclick=ajax('.($pn+1).')><button class="bangumPage">下一页</button></a>';
            break;
        case 3:
            echo '<a class="bangumItem" href=javascript:; onclick=ajax('.($pn-1).')><button class="bangumPage">上一页</button></a><a class="bangumItem" href=javascript:; onclick=ajax('.($pn+1).')><button class="bangumPage">下一页</button></a>';
            break;
        default:
            break;
        }
    }
}


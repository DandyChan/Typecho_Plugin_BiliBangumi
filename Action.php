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
    public function init($id=0, $ck='', $bg) {
    	$this->userID = $id;
    	$this->cookie = $ck;
    	$this->background = $bg;
    }
    
    //获取追番json
    private function GetCollection($pn) {
    	return BangumiAPI::curl_get_https('https://api.bilibili.com/x/space/bangumi/follow/list?type=1&follow_status=0&pn='.$pn.'&ps=50&vmid='.$this->userID.'&ts=1576028920527');
    }
    
    //json处理
    private function ParseCollection() {
    	$content = $this->GetCollection(1);
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
            $img_grid = $value->cover;
            $this->myCollection[$index++] = $value;
        }
        $i = 1;
        while(1==1){
        	if($i*50-$total>=0){
        		break;
        	}
        	$i++;
        	$content = $this->GetCollection($i);
        	$collData = json_decode($content);
        	foreach ($collData->data->list as $value) {
	            $name = $value->title;
	            $theurl = $value->url;
	            $img_grid = $value->cover;
	            $this->myCollection[$index++] = $value;
	        }
	        sleep(0.5);
        }
    }
    
    
    //输出
    public function PrintCollecion($flag = true) {
        if ($this->myCollection == null) {
            $this->ParseCollection();
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
			height: 9em;
			text-decoration: none;
			transition: all 0.5s linear;
			font-family:-apple-system,BlinkMacSystemFont,Helvetica Neue,PingFang SC,Microsoft YaHei,Source Han Sans SC,Noto Sans CJK SC,WenQuanYi Micro Hei,sans-serif;
          }
		  a.bangumItem:hover{
			/*color: #14191e;*/
			opacity: 0.8;
		  }
		  div.bg{
		  	width: 47%;
		    height: 9em;
		    position: absolute;
		    background-position-x: center;
		    filter: blur(9px) brightness(0.8);
		    -webkit-filter: blur(2px) brightness(0.8);
			-moz-filter: blur(2px) brightness(0.8);
			-o-filter: blur(2px) brightness(0.8);
			-ms-filter: blur(2px) brightness(0.8);
			transition: width 0.5s linear, background-position-y 20s linear;";
			if ($this->background == 'none') {
				echo "display: none;";
			}
			echo "}
		  a.bangumItem:hover div.bg{
		  	background-position-y: bottom;
		  }
		  div.mainMsg{
			overflow: hidden;
			height: 7em;
			margin: 2%;";
			if ($this->background == 'bangumi') {
				echo "color: white;
			font-weight: bold;
			text-shadow: 1px 1px 1px black;";
			}
			echo "}
          a.bangumItem img{
            /*width:60px;*/
			height:5.8em;
            display:inline-block;
            float:left;
            margin-right:5px;
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
				div.bg{
		  			width: 95%;
				}
			}
          </style>
        ";
                foreach ($this->myCollection as $value) {
                    // print_r($value);
                    $epsNum = '未知';
                    if (@$value->is_finish) {
                        $epsNum = $value->total_count;
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
                    $progressNum = $this->findNum($progressNum)==""?100:$this->findNum($progressNum);
                    $name = $value->title;
                    $lastep = $value->new_ep->long_title;
                    $air_date = $value->publish->release_date_show;
                    $theurl = $value->url;
                    $img_grid = str_replace("http", "https", $value->cover);
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
          <a href=" . $theurl . " target='_blank' class='bangumItem' title=\"" . $value->evaluate . "\"><div class=\"bg\" style=\"background-image: url(".$img_grid.")\"></div><div class=\"mainMsg\">
            <img referrerPolicy=\"no-referrer\" src='$img_grid' />
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
                break;

            case false:
                echo $myCollection;
                break;

            default:
                break;
        }
    }
    
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
}
class BiliBangumi_Action extends Widget_Abstract_Contents implements Widget_Interface_Do {
    public function action() {
        $config = Typecho_Widget::widget('Widget_Options')->plugin('BiliBangumi');
        $bangum = BangumiAPI::GetInstance();
        if ($config->userID == 0) {
    		die("没有填写UID，请检查插件设置");
    	}
        $bangum->init($config->userID, $config->cookie, 'none');
        $bangum->printCollecion();
    }
}


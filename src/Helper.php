<?php
/**
 * Helper.php
 * Created on 2021/8/12 17:20
 * Created by Lxd.
 * QQ: 790125098
 */

namespace Peartonlixiao\Helper;

class Helper
{
    private static $instance;

    private function __construct(){}

    private function __clone(){}

    /**
     * 单例对象
     * @noinspection PhpUnused
     */
    public static function getInstance():Helper
    {
        if(!(self::$instance instanceof self)){
            self::$instance = new static;
        }
        return self::$instance;
    }

    /**
     * 编码空格转换回+号
     * Created by Lxd
     * @param $data
     * @return string|string[]
     */
    public function defineStrReplace($data)
    {
        return str_replace(' ','+',$data);
    }

    /**
     * 获取单个汉字拼音首字母
     * 注意:此处不要纠结。汉字拼音是没有以U和V开头的
     * Created by Lxd.
     * Created on 2021/4/7 9:48
     * @param $str
     * @return int|string|null
     */
    public function getfirstchar($str)
    {
        if(empty($str)){return '';}
        if(is_numeric($str{0})) return $str{0};// 如果是数字开头 则返回数字
        $fchar=ord($str{0});
        if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0}); //如果是字母则返回字母的大写
        $s1=iconv('UTF-8','gb2312',$str);
        $s2=iconv('gb2312','UTF-8',$s1);
        $s=$s2==$str?$s1:$str;
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319&&$asc<=-20284) return 'A';//这些都是汉字
        if($asc>=-20283&&$asc<=-19776) return 'B';
        if($asc>=-19775&&$asc<=-19219) return 'C';
        if($asc>=-19218&&$asc<=-18711) return 'D';
        if($asc>=-18710&&$asc<=-18527) return 'E';
        if($asc>=-18526&&$asc<=-18240) return 'F';
        if($asc>=-18239&&$asc<=-17923) return 'G';
        if($asc>=-17922&&$asc<=-17418) return 'H';
        if($asc>=-17417&&$asc<=-16475) return 'J';
        if($asc>=-16474&&$asc<=-16213) return 'K';
        if($asc>=-16212&&$asc<=-15641) return 'L';
        if($asc>=-15640&&$asc<=-15166) return 'M';
        if($asc>=-15165&&$asc<=-14923) return 'N';
        if($asc>=-14922&&$asc<=-14915) return 'O';
        if($asc>=-14914&&$asc<=-14631) return 'P';
        if($asc>=-14630&&$asc<=-14150) return 'Q';
        if($asc>=-14149&&$asc<=-14091) return 'R';
        if($asc>=-14090&&$asc<=-13319) return 'S';
        if($asc>=-13318&&$asc<=-12839) return 'T';
        if($asc>=-12838&&$asc<=-12557) return 'W';
        if($asc>=-12556&&$asc<=-11848) return 'X';
        if($asc>=-11847&&$asc<=-11056) return 'Y';
        if($asc>=-11055&&$asc<=-10247) return 'Z';
        return null;
    }

    /**
     * curl请求
     * Created by Lxd.
     * Created on 2021/4/7 9:48
     * @param string $url
     * @param string $method
     * @param null $postfields
     * @param array $headers
     * @param false $debug
     * @return bool|string
     */
    public function httpRequest(string $url, $method="GET", $postfields = null, $headers = array(), $debug = false) {
        $method = strtoupper($method);
        $ci = curl_init();
        /* Curl settings */
        curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ci, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0");
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 0); /* 在发起连接前等待的时间，如果设置为0，则无限等待 */
        curl_setopt($ci, CURLOPT_TIMEOUT, 600); /* 设置cURL允许执行的最长秒数 */
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case "POST":
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    $tmpdatastr = is_array($postfields) ? http_build_query($postfields) : $postfields;
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $tmpdatastr);
                }
                break;
            default:
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method); /* //设置请求方式 */
                break;
        }
        $ssl = preg_match('/^https:\/\//i',$url) ? TRUE : FALSE;
        curl_setopt($ci, CURLOPT_URL, $url);
        if($ssl){
            curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
            curl_setopt($ci, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
        }
        //curl_setopt($ci, CURLOPT_HEADER, true); /*启用时会将头文件的信息作为数据流输出*/
        //curl_setopt($ci, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ci, CURLOPT_MAXREDIRS, 2);/*指定最多的HTTP重定向的数量，这个选项是和CURLOPT_FOLLOWLOCATION一起使用的*/
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLINFO_HEADER_OUT, true);
        /*curl_setopt($ci, CURLOPT_COOKIE, $Cookiestr); * *COOKIE带过去** */
        $response = curl_exec($ci);
        $requestinfo = curl_getinfo($ci);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
        if ($debug) {
            echo "=====post data======\r\n";
            var_dump($postfields);
            echo "=====info===== \r\n";
            print_r($requestinfo);
            echo "=====response=====\r\n";
            print_r($response);
        }
        curl_close($ci);
        return $response;
        //return array($http_code, $response,$requestinfo);
    }

    /**
     * 提取富文本框文本
     * Created by Lxd.
     * Created on 2021/4/7 17:42
     * @param $info
     * @param null $length
     * @return string
     */
    public function richTextTochar($info,$length = null):string
    {
        $html_string = htmlspecialchars_decode($info);              //把一些预定义的 HTML 实体转换为字符
        $content = str_replace(" ", "", $html_string);              //将空格替换成空
        $content = str_replace("&nbsp;", "", $content);
        $contents = strip_tags($content);                           //函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
        if($length !== null) {                                      //返回字符串中的指定长度
            $contents = mb_substr($contents, 0, $length, "utf-8");
            $contents .= '...';
        }
        return $contents;
    }

    /**
     * .结构获取数组内元素
     * Created by Lxd.
     * @param array $array
     * @param $key
     * @param null $default
     * @return array|mixed|null
     */
    public function array_get(array $array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }
        if(isset($array[$key])){
            return $array[$key];
        }
        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }
        foreach (explode('.', $key) as $segment) {
            if (isset($segment[$key])) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * 判断请求来源是否移动端
     * Created by Lxd.
     * Created on 2021/4/21 17:16
     * @return bool
     */
    public function isMobile():bool
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])){
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])){
            $clientkeywords = array ('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i",
                strtolower($_SERVER['HTTP_USER_AGENT']))){
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])){
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) &&
                (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false ||
                    (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') <
                        strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
                return true;
            }
        }
        return false;
    }

    /**
     * 获取项目内访问来源[目前仅小程序及app]
     * Created by Lxd.
     * Created on 2021/4/21 17:17
     * @return false|string
     */
    public function getUserAgentType()
    {
        if(!$this->isMobile()){
            return false;
        }
        $userAgent = 'app';
        if (
            //目前C端:ios/android/小程序 用户agent携带下面三个参数,即判断为通过小程序访问.
            strpos($_SERVER['HTTP_USER_AGENT'], 'miniprogram') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'wechatdevtools') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false
        ) {
            $userAgent = 'miniprogram';
        }

        return $userAgent;
    }

    /**
     * 获取当前环境是否小程序
     * Created by Lxd.
     * Created on 2021/7/6 14:24
     * @return bool
     */
    public function getIsMiniProgram()
    {
        return $this->getUserAgentType() == 'miniprogram';
    }

    /**
     * Created by Lxd.
     * Created on 2021/5/6 11:57
     * @param $lat
     * @param $lng
     * @return string
     */
    public function getDistanceSql($lat,$lng):string
    {
        return "ACOS(SIN(( {$lat} * 3.1415) / 180 ) *SIN((latitude * 3.1415) / 180 ) +COS(( {$lat}* 3.1415) / 180 ) * COS((latitude * 3.1415) / 180 ) *COS(( {$lng}* 3.1415) / 180 - (longitude * 3.1415) / 180 ) ) * 6380";
    }

    /**
     * 获取时间段
     * Created by Lxd.
     * Created on 2021/5/13 10:11
     * @param int $type
     * @return array
     */
    public function getTimeQuantum($type = 1):array
    {
        $time = time();
        switch ($type){
            case 1: //今天
                $date1 = date('Y-m-d H:i:s',strtotime(date('Y-m-d')));
                $date2 = date('Y-m-d H:i:s',strtotime('+1 day'));
                break;
            case 2: //本周
                $date1 = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m", $time),date("d", $time)-date("w", $time)+1,date("Y", $time)));
                $date2 = date("Y-m-d H:i:s",mktime(23,59,59,date("m", $time),date("d", $time)-date("w", $time)+7,date("Y", $time)));
                break;
            case 3: //本月
                $date1 = date('Y-m-d H:i:s',mktime(0,0,0,date("m",$time),1,date("Y",$time)));
                $date2 = date('Y-m-d H:i:s',mktime(23,59,59,date("m",$time),date("t",strtotime($date1)),date("Y",$time)));
                break;
            case 4: //本季度
                $season = ceil((date('n', $time))/3);//当月是第几季度
                $date1 = date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')));
                $date2 = date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));
                break;
            case 5: //上周
                $date1 = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m", $time),date("d", $time)-date("w", $time)+1-7,date("Y", $time)));
                $date2 = date("Y-m-d H:i:s",mktime(23,59,59,date("m", $time),date("d", $time)-date("w", $time)+7-7,date("Y", $time)));
                break;
            case 6: //上月
                $date1 = date('Y-m-d H:i:s',mktime(0,0,0,date("m",$time)-1,1,date("Y",$time)));
                $date2 = date('Y-m-d H:i:s',mktime(23,59,59,date("m",$time)-1,date("t",strtotime($date1)),date("Y",$time)));
                break;
            case 7: //上季度
                $season = ceil((date('n', $time))/3) - 1;//当月是第几季度
                $date1 = date('Y-m-d H:i:s', mktime(0, 0, 0,$season*3-3+1,1,date('Y')));
                $date2 = date('Y-m-d H:i:s', mktime(23,59,59,$season*3,date('t',mktime(0, 0 , 0,$season*3,1,date("Y"))),date('Y')));
                break;
            default: //今年
                $date = date('Y-m-d H:i:s');
                $date1 = date(date("Y-01-01 00:00:00",strtotime("$date -1 year")));
                $date2 = date('Y-12-31 23:59:59', strtotime("$date -1 year"));
        }
        return [$date1,$date2];
    }

    /**
     * 校验身份证
     * Created by Lxd.
     * Created on 2021/5/31 11:14
     * @param string $id_card
     * @return bool
     */
    public function validation_filter_id_card(string $id_card):bool
    {
        if(strlen($id_card)==18){
            return $this->idcard_checksum18($id_card);
        }elseif((strlen($id_card)==15)){
            $id_card=$this->idcard_15to18($id_card);
            return $this->idcard_checksum18($id_card);
        }else{
            return false;
        }
    }

    /**
     * 计算身份证校验码，根据国家标准GB 11643-1999
     * Created by Lxd.
     * Created on 2021/5/31 11:12
     * @param $idcard_base
     * @return false|string
     */
    private function idcard_verify_number(string $idcard_base){
        if(strlen($idcard_base)!=17){
            return false;
        }
        //加权因子
        $factor=array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2);
        //校验码对应值
        $verify_number_list=array('1','0','X','9','8','7','6','5','4','3','2');
        $checksum=0;
        for($i=0;$i<strlen($idcard_base);$i++){
            $checksum += substr($idcard_base,$i,1) * $factor[$i];
        }
        $mod=$checksum % 11;
        return $verify_number_list[$mod];
    }

    /**
     * 将15位身份证升级到18位
     * Created by Lxd.
     * Created on 2021/5/31 11:12
     * @param $idcard
     * @return false|string
     */
    private function idcard_15to18(string $idcard){
        if(strlen($idcard)!=15){
            return false;
        }else{
            // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
            if(array_search(substr($idcard,12,3),array('996','997','998','999')) !== false){
                $idcard=substr($idcard,0,6).'18'.substr($idcard,6,9);
            }else{
                $idcard=substr($idcard,0,6).'19'.substr($idcard,6,9);
            }
        }
        $idcard=$idcard.$this->idcard_verify_number($idcard);
        return $idcard;
    }

    /**
     * 18位身份证校验码有效性检查
     * Created by Lxd.
     * Created on 2021/5/31 11:13
     * @param $idcard
     * @return bool
     */
    private function idcard_checksum18(string $idcard):bool
    {
        if(strlen($idcard)!=18){
            return false;
        }
        $idcard_base=substr($idcard,0,17);
        if($this->idcard_verify_number($idcard_base)!=strtoupper(substr($idcard,17,1))){
            return false;
        }else{
            return true;
        }
    }

    /**
     * 车牌号格式验证
     * Created by Lxd.
     * Created on 2021/6/5 15:28
     * @param string $license
     * @return bool
     */
    public function isCarLicense(string $license):bool
    {
        if (empty($license)) {
            return false;
        }
        #匹配民用车牌和使馆车牌
        # 判断标准
        # 1，第一位为汉字省份缩写
        # 2，第二位为大写字母城市编码
        # 3，后面是5位仅含字母和数字的组合
        $regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新使]{1}[A-Z]{1}[0-9a-zA-Z]{5}$/u";
        preg_match($regular, $license, $match);
        if (isset($match[0])) {
            return true;
        }

        #匹配特种车牌(挂,警,学,领,港,澳)
        #参考 https://wenku.baidu.com/view/4573909a964bcf84b9d57bc5.html
        $regular = '/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[0-9a-zA-Z]{4}[挂警学领港澳]{1}$/u';
        preg_match($regular, $license, $match);
        if (isset($match[0])) {
            return true;
        }

        #匹配武警车牌
        #参考 https://wenku.baidu.com/view/7fe0b333aaea998fcc220e48.html
        $regular = '/^WJ[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]?[0-9a-zA-Z]{5}$/ui';
        preg_match($regular, $license, $match);
        if (isset($match[0])) {
            return true;
        }

        #匹配军牌
        #参考 http://auto.sina.com.cn/service/2013-05-03/18111149551.shtml
        $regular = "/[A-Z]{2}[0-9]{5}$/";
        preg_match($regular, $license, $match);
        if (isset($match[0])) {
            return true;
        }

        #匹配新能源车辆6位车牌
        #参考 https://baike.baidu.com/item/%E6%96%B0%E8%83%BD%E6%BA%90%E6%B1%BD%E8%BD%A6%E4%B8%93%E7%94%A8%E5%8F%B7%E7%89%8C
        #小型新能源车
        $regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[DF]{1}[0-9a-zA-Z]{5}$/u";
        preg_match($regular, $license, $match);
        if (isset($match[0])) {
            return true;
        }

        #大型新能源车
        $regular = "/[京津冀晋蒙辽吉黑沪苏浙皖闽赣鲁豫鄂湘粤桂琼川贵云渝藏陕甘青宁新]{1}[A-Z]{1}[0-9a-zA-Z]{5}[DF]{1}$/u";
        preg_match($regular, $license, $match);
        if (isset($match[0])) {
            return true;
        }
        return false;
    }

    /**
     * 时间格式化展示[刚刚,10分钟前,1.5小时前...]
     * Created by Lxd.
     * Created on 2021/6/5 16:10
     * @param $difference
     * @return string
     */
    public function timeFormatting($difference)
    {
        $msg = '很久之前';
        switch ($difference) {
            case $difference <= '60' :
                $msg = '刚刚';
                break;
            case $difference > '60' && $difference <= '3600' :
                $msg = floor($difference / 60) . '分钟前';
                break;

            case $difference > '3600' && $difference <= '86400' :
                $msg = number_format($difference / 3600,1) . '小时前';
                break;

            case $difference > '86400' && $difference <= '2592000' :
                $msg = floor($difference / 86400) . '天前';
                break;

            case $difference > '2592000' && $difference <= '7776000':
                $msg = floor($difference / 2592000) . '个月前';
                break;
            case $difference > '7776000':
                $msg = '很久以前';
                break;
        }
        return $msg;
    }

    /**
     * 时间格式化展示[相对↑更加准确描述]
     * Created by Lxd.
     * Created on 2021/6/9 16:09
     * @param $difference
     * @return string
     */
    public function timeFormattingPrecise($difference)
    {
        $msg = '很久之前';
        switch ($difference) {
            case $difference <= '60' :
                $msg = "{$difference}秒";
                break;
            case $difference > '60' && $difference <= '3600' :
                $msg = floor($difference / 60) . '分钟';
                break;

            case $difference > '3600' && $difference <= '86400' :
                $msg = number_format($difference / 3600,1) . '小时';
                break;

            case $difference > '86400' && $difference <= '2592000' :
                $msg = number_format($difference / 86400) . '天';
                if($difference%86400 > '3600'){
                    $msg .= number_format($difference%86400/3600) . '小时';
                }
                break;

            case $difference > '2592000' && $difference <= '31104000':
                $msg = number_format($difference / 2592000) . '个月';
                if($difference%2592000 > '86400'){
                    $msg .= number_format($difference%2592000/86400) . '天';
                }
                break;
            case $difference > '31104000':
                $msg = '一年前';
                break;
        }
        return $msg;
    }
}
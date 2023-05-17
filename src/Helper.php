<?php
/**
 * Helper.php
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
     * @param $string
     * @return string|string[]
     */
    public function defineStrReplace(string $string):string
    {
        return str_replace(' ','+',$string);
    }

    /**
     * 获取单个汉字拼音首字母
     * 注意:此处不要纠结。汉字拼音是没有以U和V开头的
     * Created by Lxd.
     * @param $str
     * @return int|string|null
     */
    public function getfirstchar(string $str)
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
     * @param $info
     * @param null $length
     * @return string
     */
    public function richTextTochar(string $info,$length = null):string
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
     * 作用方法:时间字符串
     * Created by Lxd.
     * @return string
     */
    function getDateTime():string
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Limit the number of characters in a string.
     *
     * Created by test.
     * Created on 2021/4/16 15:22
     * @param $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    function str_limit($value, $limit = 100, $end = '...'):string
    {
        if(!$value){
            return '';
        }
        return \Illuminate\Support\Str::limit($value, $limit, $end);
    }

    /**
     * 隐藏手机号中间4位
     * @param $phone
     * @return string|string[]|null
     */
    function hidMobile($phone){
        $IsWhat = preg_match('/(0[0-9]{2,3}[\-]?[2-9][0-9]{6,7}[\-]?[0-9]?)/i',$phone); //固定电话
        if($IsWhat == 1){
            return preg_replace('/(0[0-9]{2,3}[\-]?[2-9])[0-9]{3,4}([0-9]{3}[\-]?[0-9]?)/i','$1****$2',$phone);
        }else{
            return  preg_replace('/(1[358]{1}[0-9])[0-9]{4}([0-9]{4})/i','$1****$2',$phone);
        }
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
     * 获取项目内访问来源[目前web/微信小程序/微信浏览器/其他]
     * Created by Lxd.
     * @return string
     */
    public function getUserAgentType():string
    {
        $userAgent = 'web';
        if(!$this->isMobile()){
            return $userAgent;
        }
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'miniprogram') !== false){
            $userAgent = 'miniprogram';
        }elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'wechatdevtools') !== false){
            $userAgent = 'wechatbrowser';
        }else{
            $userAgent = 'other';
        }

        return $userAgent;
    }

    /**
     * 获取当前环境是否小程序
     * Created by Lxd.
     * Created on 2021/7/6 14:24
     * @return bool
     */
    public function getIsMiniProgram():bool
    {
        return $this->getUserAgentType() == 'miniprogram';
    }

    /**
     * 计算经纬度排序的sql
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
     * @noinspection DuplicatedCode
     */
    public function getTimeQuantum($type = 1):array
    {
        $time = time();
        switch ($type){
            case 1: //今天
                $date1 = date('Y-m-d H:i:s',strtotime(date('Y-m-d')));
                $date2 = date('Y-m-d',time()).' 23:59:59';
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
     * 作用方法:时间格式化展示[刚刚,10分钟前,1.5小时前...]
     * Created by Lxd.
     * @param $difference
     * @return string
     */
    public function timeFormatting($difference):string
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

            case $difference > '2592000' && $difference <= '31104000':
                $msg = floor($difference / 2592000) . '个月前';
                break;
            case $difference > '31104000' && $difference <= '155520000':    //5年
                $msg = floor($difference / 31104000) . '年前';
                break;
            case $difference > '155520000';
                $msg = '很久以前';
                break;
        }
        return $msg;
    }

    /**
     * 作用方法:时间格式化展示[相对↑更加准确描述]
     * Created by Lxd.
     * @param $difference
     * @return string
     */
    public function timeFormattingPrecise($difference):string
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
            case $difference > '31104000' && $difference <= '62208000':
                $msg = '一年多前';
                break;
            case $difference > '62208000' && $difference <= '93312000':
                $msg = '两年多前';
                break;
            case $difference > '62208000' && $difference <= '124416000':
                $msg = '三年多前';
                break;
            case $difference > '124416000':
                $msg = '很久以前';
                break;
        }
        return $msg;
    }

    /**
     * 作用方法:生成随机字符串(数字+字母大小写)
     * Created by Lxd.
     * @param int $length
     * @param string $prefix
     * @return string
     */
    public function getRandomString(int $length = 6,string $prefix = ''):string
    {
        $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZadcdefghijklmnopqrstuvwxyz";
        $strContent = "";
        for ( $i = 0; $i < $length; $i++ )
        {
            $strContent .= $chars[mt_rand(0,strlen($chars)-1)];
        }
        return $prefix.$strContent;
    }

    /**
     * 作用方法:随机生成字符串(仅数字)
     * Created by Lxd.
     * @param string $prefix
     * @param bool $length
     * @return string
     */
    public function getRandomFigure(string $prefix = '',$length = false):string
    {
        if($length){
            //适用短信验证码等
            $string = $prefix.substr(base_convert(md5(uniqid(md5(microtime(true)),true)), 16, 10), 0, $length);
        }else{
            //使用订单号等(尽量避重,业务内自己验证)
            $string = $prefix.date('YmdH',time()).str_pad(mt_rand(10, 999999), 6, "0", STR_PAD_BOTH);
        }
        return $string;
    }

    /**
     * 作用方法:去除数组中的空值或返回白名单允许的值
     * Created by Lxd.
     * @param array $arr
     * @param array $whiteList
     * @return array
     */
    public function arrayFilterEmpty(array $arr,array $whiteList = []):array
    {
        foreach ($arr as $index=>$value){
            if(empty($value) || !in_array($index,$whiteList)){
                unset($arr[$index]);
            }
        }
        return $arr;
    }

    /**
     * 作用方法:按key清理字段
     * Created by Lxd.
     * @param array $arr
     * @param string $key
     * @return array
     */
    public function unserKeyPerItem(array $arr,string $key):array
    {
        if(!empty($arr)){
            foreach ($arr as &$row){
                unset($row[$key]);
            }
        }
        return $arr;
    }

    /**
     * 作用方法: 处理无限级分类，返回带有层级关系的树形结构
     * Created by Lxd.
     * 处理无限级分类，返回带有层级关系的树形结构
     * @param array $data 数据数组
     * @param int $root 根节点的父级id
     * @param string $id id字段名
     * @param string $pid 父级id字段名
     * @param string $child 树形结构子级字段名
     * @return array $tree 树形结构数组
     */
    public static function getMultilevelTree(array $data, $root = 0, $id = 'id', $pid = 'pid', $child = 'child'):array
    {
        $tree = [];
        $temp = [];

        foreach ($data as $key => $val) {
            $temp[$val[$id]] = &$data[$key];
        }
        foreach ($data as $key => $val) {
            $parentId = $val[$pid];
            if ($root == $parentId) {
                $tree[] = &$data[$key];
            } else {
                if (isset($temp[$parentId])) {
                    $parent = &$temp[$parentId];
                    $parent[$child][] = &$data[$key];
                }
            }
        }
        return $tree;
    }

    /**
     * 作用方法:插入数据到数组指定位置.
     * Created by Lxd.
     * @param integer $offset
     * @param array $data
     * @param array $insertData
     * @return array
     */
    public static function insertDataToArray(int $offset, array $data, array $insertData):array
    {
        $prevData = array_slice($data, 0, $offset);
        $lastData = array_slice($data, $offset);
        $prevData = array_merge($prevData, $insertData);
        return array_merge($prevData, $lastData);
    }

    /**
     * 作用方法:取得两个数组中值的比较情况
     * Created by Lxd.
     * @param array $newIds
     * @param array $oldIds
     * @return array
     */
    public static function updateArrayDiff(array $newIds, array $oldIds):array
    {
        $addIds = array_diff($newIds, $oldIds);      // 删除的id
        $deleteIds = array_diff($oldIds, $newIds);         // 新增的id
        $sameIds = array_intersect($newIds, $oldIds);   // 不变的id

        return ['delete' => $deleteIds, 'add' => $addIds, 'same' => $sameIds];
    }

    /**
     * 作用方法:二维数组查找
     * @Author Pearton <pearton@126.com>
     * @Time 2022/6/16 15:42
     * @param array $array
     * @param string $findField
     * @param string $findValue
     * @return false|int|string
     */
    public static function twoArraySearch(array $array,string $findField,string $findValue)
    {
        $valuePluck = array_column($array,$findField);
        return array_search($findValue,$valuePluck);
    }

    /*
     * 作用方法:检测是否是url
     * Created by Lxd.
     * @param string $url
     * @return bool
     */
    public static function checkUrl ($url):bool
    {
        $pattern = "/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/";
        if (!preg_match($pattern, $url)){
            return false;
        }
        return true;
    }

    /*
     * 获取指定目录下指定文件后缀的函数
     * @$path   文件路径
     * @$ext    文件后缀名，默认为false为不指定，如果指定，请以数组方式传入
     * @$filename   使用时请提前赋值为空数组
     * @$recursive  是否递归查找，默认为false
     * @$baseurl    是否包含路径，默认包含
     * @$getDetail  是否获取文件详细信息（大小等）
     */
    public static function getDirFilesLists($path,&$filename,$recursive = false,$ext = false,$baseurl = true,$getDetail = false)
    {
        if (!$path) {
            die('请传入目录路径');
        }
        $resource = opendir($path);
        if (!$resource) {
            die('你传入的目录不正确');
        }
        #默认值
        $filename = [];
        //遍历目录
        while ($rows = readdir($resource)) {
            //如果指定为递归查询
            if ($recursive) {
                if (is_dir($path . '/' . $rows) && $rows != "." && $rows != "..") {
                    getDirFilesLists($path . '/' . $rows, $filename, $resource, $ext, $baseurl);
                } elseif ($rows != "." && $rows != "..") {
                    //如果指定后缀名
                    if ($ext) {
                        //必须为数组
                        if (!is_array($ext)) {
                            die('后缀名请以数组方式传入');
                        }
                        //转换小写
                        foreach ($ext as &$v) {
                            $v = strtolower($v);
                        }
                        //匹配后缀
                        $file_ext = strtolower(pathinfo($rows)['extension']);
                        if (in_array($file_ext, $ext)) {
                            //是否包含路径
                            if ($baseurl) {
                                $filename[] = $path . '/' . $rows;
                            } else {
                                $filename[] = $rows;
                            }
                        }
                    } else {
                        if ($baseurl) {
                            $filename[] = $path . '/' . $rows;
                        } else {
                            $filename[] = $rows;
                        }
                    }
                }
            } else {
                //非递归查询
                if (is_file($path . '/' . $rows) && $rows != "." && $rows != "..") {
                    if ($baseurl) {
                        $filename[] = $path . '/' . $rows;
                    } else {
                        $filename[] = $rows;
                    }
                }
            }
        }
        if($getDetail){
            foreach ($filename as &$v){
                $fileUri = $v;
                $v = [];
                $v['file_uri'] = $fileUri;
                //打开文件，r表示以只读方式打开
                $handle = fopen($v['file_uri'],"r");
                //获取文件的统计信息
                $fstat = fstat($handle);

                $v['file_name'] = basename($v['file_uri']);
                //文件大小 KB
                $v['file_size'] = round($fstat["size"]/1024,2);
                //最后访问时间
                $v['file_atime'] = date("Y-m-d h:i:s",$fstat["atime"]);
                //最后修改时间
                $v['file_mtime'] = date("Y-m-d h:i:s",$fstat["mtime"]);
            }
        }
    }

    /**
     * 作用方法:字符串隐藏(后数第五位开始 隐藏4位)
     * Created by Lxd.
     * @param string $str
     * @return string|string[]
     */
    function hidStr(string $str){
        if(!$str){
            return '';
        }
        $len = strlen($str);
        if($len <= 5){
            return '****';
        }
        if(strlen($str) <= 9){
            $start = (int)(($len-4)/2);
            return substr_replace($str,'****',$start,4);
        }
        $start = $len-8;
        return substr_replace($str,'****',$start,4);
    }
}
<?php
/**
 * 通用的方法
 * @author lonphy
 */

/**
 * 判断是否是关联数组
 * @param  mixed $value
 * @return boolean
 */
function is_assoc($value) {
    return array_keys($value) !== range(0, count($value) - 1);
}
/**
 * 加密/解密函数 用于cookie 等
 * hash_code(10000, 'ENCODE');//加密  hash_code($auth_hash);//解密
 * @param string $string 加密/解密字符
 * @param string $operation ENCODE=加密   DECODE=解密
 * @param string $key 加密KEY
 * @param int $expiry
 * @return string
 */
function hashCode($string, $operation = 'DECODE', $key = '', $expiry = 0){
    $string = str_replace('x2013x', '+', $string);
    $ckey_length = 6;
    $key = md5($key != '' ? $key : HASHKEY);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace(array('=','+'), array('','x2013x'), base64_encode($result));
    }

}

/**
 * 格式化日期搜索
 * @param string $start 开始时间
 * @param string $end 结束时间
 * @return array array($column => '>=' $startTime)
 * @author hanliqiang
 * @date 2016年9月19日
 */
function format_search_time($startTime, $endTime)
{
    if(empty($startTime) && empty($endTime)) return [false, ''];

    $start = $end = 0;
    $retrun_arr = [];

    if(!empty($startTime)) {
        $start = strtotime($startTime);
    }
    if(!empty($endTime)) {
        $end = strtotime($endTime)+86399;
    }

    if($start > 0 && $end < 1){
        $retrun_arr = [
            '>=' => $start,
        ];
    }
    if($start > 0 && $end > 0){
        $retrun_arr = [
            '>=' => $start,
            '<=' => $end
        ];
    }
    if($start < 1 && $end > 0){
        $retrun_arr = [
            '<=' => $end
        ];
    }
    return [true, $retrun_arr];
}

/**
 * 过滤数组中空值（不包括0）
 * @author: lihu
 * @param $array 过滤数组
 * @date: 2017-7-24
 */
function filter_value(array $array){
    $return = array_filter($array, function($val){
        if(trim($val) != ""){
            return true;
        }
    });
    return $return;
}

/**
 * 将阿拉伯数字，转化为汉字数字
 * @param int $num
 * @return string
 */
function chinanum($num){
    if (!empty($num)){
        $china=array('零','一','二','三','四','五','六','七','八','九');
        $arr=str_split($num);
        $str = "";
        for($i=0;$i<count($arr);$i++){
            $str .= $china[$arr[$i]];
        }
        return $str.'年';
    }
    return '';
}

/**
 * 格式化日期
 * @param string $start 开始时间
 * @param string $end 结束时间
 * @return array array($column => '>=' $startTime)
 * @author lihu
 * @date 2016年9月19日
 */
function format_time($startTime, $endTime)
{
    if(empty($startTime) && empty($endTime)) return [false, ''];

    $start = $end = 0;
    $retrun_arr = [];

    if(!empty($startTime)) {
        $start = strtotime($startTime);
    }
    if(!empty($endTime)) {
        $end = strtotime($endTime);
    }

    if($start == $end){
        $retrun_arr = [
            '=' => $start,
        ];
    }else{
        if($start > 0 && $end < 1){
            $retrun_arr = [
                '>=' => $start,
            ];
        }
        if($start > 0 && $end > 0){
            $retrun_arr = [
                '>=' => $start,
                '<=' => $end
            ];
        }
        if($start < 1 && $end > 0){
            $retrun_arr = [
                '<=' => $end
            ];
        }
    }


    return [true, $retrun_arr];
}

/*
 * 检测vin
 * @param $sVin
 * @return bool
 * @author lixin
 */
function _checkVin($sVin)
{
    static $aCharMap = array(
        '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
        'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'J' => 1, 'K' => 2,
        'L' => 3, 'M' => 4, 'N' => 5, 'P' => 7, 'R' => 9, 'S' => 2, 'T' => 3, 'U' => 4, 'V' => 5, 'W' => 6,
        'X' => 7, 'Y' => 8, 'Z' => 9
    );
    static $aWeightMap = array(8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2);
    foreach (array_keys($aCharMap) as $sNode) {//取出key
        $aCharKeys[] = strval($sNode);
    }
    $sVin = strtoupper($sVin); //强制输入大写

    if (strlen($sVin) !== 17) {
        return false; //长度不对
    } elseif (!in_array($sVin{8}, array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'X'))) {
        return false; //校验位的值不对
    }
    //检查vincode字符是否超表
    for ($i = 0; $i < 17; $i++) {
        if (!in_array($sVin{$i}, $aCharKeys)) {
            return false; //超出范围
        }
    }
    //计算权值总和
    $iTotal = 0;
    for ($i = 0; $i < 17; $i++) {
        $iTotal += $aCharMap[$sVin{$i}] * $aWeightMap[$i];
    }
    //计算校验码
    $sMode = $iTotal % 11;
    if ($sMode < 10 && $sMode === intval($sVin{8})) {
        return true;
    } elseif (10 === $sMode && 'X' === $sVin{8}) {
        return true;
    } else {
        return false;
    }
}

/**
 * 编码
 * @param int $dealerId 经销商id
 * @param string $vin 车架号
 * @return string
 * @author lixin
 */
function carSerial(int $dealerId, string $vin) : string
{
    // $binDealerId 长度一定为4, 其中第一位忽略不要
    $binDealerId = pack('N', $dealerId & 0xffffff);
    $data = base64_encode(substr($binDealerId, 1));
    $data = str_replace(['+', '/'], ['-', '_'], $data) . $vin;
    return $data;
}

/**
 * 车牌号验证规则
 * @return bool
 * @author magus.lee
 */
function checkCarno($carno){
    $match = '/^[京津沪渝冀豫云辽黑湘皖鲁新苏浙赣鄂桂甘晋蒙陕吉闽贵粤青藏川宁琼使领]{1}[A-Za-z]{1}[A-Za-z0-9]{4}[A-Za-z0-9挂学警港澳]{1}$/u';
    return preg_match($match, $carno);
}

/**
 * 验证金额小数点最多保留两位
 * @return bool
 * @author magus.lee
 */
function checkMoney($M){
    $match = '/^([1-9]\d*|0)(\.\d{1,2})?$/';
    return preg_match($match,$M);
}

/**
 * 转化属性名称
 * @param int $group
 * @return string $groupName
 * @author lihu
 * @date 2017-12-8
 */
function switch_group($group){
    $result = '';
    if(!empty(trim($group))){
        switch ($group){
            case 1:
                $result = "工单信息";
                break;
            case 2:
                $result = "车辆信息";
                break;
            case 3:
                $result = "费用信息";
                break;
            case 4:
                $result = "时间信息";
                break;
            case 5:
                $result = "车主信息";
                break;
            case 6:
                $result = "车辆信息";
                break;
            case 7:
                $result = "联系人信息";
                break;
            case 8:
                $result = "保养年检信息";
                break;
            case 9:
                $result = "保险信息";
                break;
            case 10:
                $result = "其他信息";
                break;
            default:
                $result = "";
        }
    }
    return $result;
}
/**
 * 处理csv导入的时间格式数据
 * @param $strTime string 导入时间字符串
 * @return $strtotimr int 转换后的时间戳
 * @author lihu
 */
function disposeImportTimeData($strTime){
    $strtoTime = strtotime($strTime);
    //如果strtotime 转换成功不执行以下代码
    if ($strtoTime !== false && $strtoTime !== -1){
        return $strtoTime;
    }else{
        $strTime = strtolower($strTime);
        //导入时间格式参考值
        $reference = [
            "rrrr年r月rr日",
            "rrrr年r月r日",
            "rrrr年r月rr日",
            "rrrr年r月rr日 r:rr",
            "rrrr年r月rr日 rr:r",
            "rrrr年r月rr日 rr:rr",
            "rrrr年r月rr日 rr:rr:rr",
            "rrrr年r月rr日 r:rr:r",
            "rrrr年r月rr日 r:r:rr",
            "rrrr年r月rr日 rr:r:r",
            "rrrr年r月rr日 r:r:r",
            "rrrr年rr月r日",
            "rrrr年rr月r日 r:rr",
            "rrrr年rr月r日 rr:r",
            "rrrr年rr月r日 rr:rr",
            "rrrr年rr月r日 rr:rr:rr",
            "rrrr年rr月r日 r:rr:r",
            "rrrr年rr月r日 r:r:rr",
            "rrrr年rr月r日 rr:r:r",
            "rrrr年rr月r日 r:r:r",
            "rrrr年rr月rr日",
            "rrrr年rr月rr日 r:rr",
            "rrrr年rr月rr日 rr:r",
            "rrrr年rr月rr日 rr:rr",
            "rrrr年rr月rr日 rr:rr:rr",
            "rrrr年rr月rr日 r:rr:r",
            "rrrr年rr月rr日 r:r:rr",
            "rrrr年rr月rr日 rr:r:r",
            "rrrr年rr月rr日 r:r:r",
            "rrrr/r/r rr:rr am",
            "rrrr/r/r r:rr am",
            "rrrr/r/r rr:r am",
            "rrrr/r/r rr:rr pm",
            "rrrr/r/r r:rr pm",
            "rrrr/r/r rr:r pm",
            "rrrr/rr/rr rr:rr am",
            "rrrr/rr/rr rr:r am",
            "rrrr/rr/rr r:rr am",
            "rrrr/rr/rr rr:rr pm",
            "rrrr/rr/rr r:rr pm",
            "rrrr/rr/rr rr:r pm",
            "rrrr-r-r rr:rr am",
            "rrrr-r-r r:rr am",
            "rrrr-r-r rr:r am",
            "rrrr-r-r rr:rr pm",
            "rrrr-r-r r:rr pm",
            "rrrr-r-r rr:r pm",
            "rrrr-rr-rr rr:rr am",
            "rrrr-rr-rr rr:r am",
            "rrrr-rr-rr r:rr am",
            "rrrr-rr-rr rr:rr pm",
            "rrrr-rr-rr r:rr pm",
            "rrrr-rr-rr rr:r pm",
        ];

        $replaceValue = str_replace([
            '0','1','2','3','4','5','6','7','8','9',
            "零","一","二","三","四","五","六","七","八","九"
        ],"r", $strTime);

        if(in_array($replaceValue,$reference)){
            //是否存在中文
            if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $strTime)){
                //中文"年","月","日"转"-",汉字数字转数字
                $strTime = str_replace(['年','月','日'],"-", $strTime);
                $strTime = str_replace(['时','分','秒'],":", $strTime);
                //转换中文数字
                $arrTime = switchChnNumber($strTime);
                if(empty($arrTime)){
                    return false;
                }
                //去除多余 “-” 或 “:”
                $strTime = trimTimeArr($arrTime);

                return strtotime($strTime);
            }else{
                return strtotime($strTime);
            }
        }else{
            return false;
        }
    }
}

/**
 * 处理 字符串替换后的多余值
 * @param $arrTime
 * @return array|string
 * @author lihu
 */
function trimTimeArr($arrTime){
    $arrTime = $arrTime." ";
    $arrTime = explode(" ", $arrTime);
    $arrTime[0] = rtrim($arrTime[0], "-");
    $arrTime[1] = rtrim($arrTime[1], ":");
    $arrTime = implode(" ", $arrTime);
    $arrTime = rtrim($arrTime, " ");
    return $arrTime;
}

/**
 * 中文 一、二... 转阿拉伯数字
 * @param $time
 * @return string
 * @author lihu
 */
function switchChnNumber($time){
    if(!empty($time)){
        $replaceValueC = [
            "零"=>0,"一"=>1,"二"=>2,"三"=>3,"四"=>4,"五"=>5,"六"=>6,"七"=>7,"八"=>8,"九"=>9
        ];
        //拆分含有中文的字符串
        $arrTime = preg_split('/(?<!^)(?!$)/u', $time);
        foreach ($arrTime as $key => $value){
            if($replaceValueC[$value] != ''){
                $arrTime[$key] = $replaceValueC[$value];
            }else{
                $arrTime[$key] = $value;
            }
        }
        return implode("", $arrTime);
    }else{
        return $time;
    }
}

/**
 * 生成机器id
 * @return array
 * @author zhaoce
 */
function readMachineId(): array {
    $hostname = $_SERVER['SERVER_NAME'];
    return getBinaryBytes(md5($hostname));
}

/**
 * 16进制-》10进制数组
 * @param $string
 * @return array
 * @author zhaoce
 */
function getBinaryBytes($string): array {
    $str = [];
    for($i = 0; $i < 16; $i++) {
        $str[] = hexdec(substr($string, $i*2, 2));
    }
    return $str;
}

/**
 * 10进制数组转字符串
 * @param $bytes
 * @return string
 * @author zhaoce
 */
function getBytesToString($bytes): string {
    $str = '';
    foreach($bytes as $b){
        $str .= chr($b);
    }
    return $str;
}

/**
 * 获取一个4字节随机数
 * @return int
 * @author zhaoce
 */
function readRandomUint32() {
    $b = [];
    for($i = 0; $i < 4; $i++){
        $b[] = rand(0, 255);
    }
    return $b[0] << 0 | $b[1] << 8 | $b[2] << 16 | $b[3] << 24;
}

/**
 * 获取MsgID
 * @return string
 * @author zhaoce
 */
function genMsgID(): string {

    $b = [];
    // Timestamp, 4 bytes, big endian
    $unixTime =  time();
    $b[0] =$unixTime  >> 24 & 0xff;
    $b[1] = $unixTime >> 16 & 0xff;
    $b[2] = $unixTime >> 8 & 0xff;
    $b[3] = $unixTime & 0xff;
    // Machine, first 3 bytes of md5(hostname)
    $machineId = readMachineId();
    $b[4] = $machineId[0];
    $b[5] = $machineId[1];
    $b[6] = $machineId[2];
    // Pid, 2 bytes, specs don't specify endianness, but we use big endian.
    $b[7] = getmygid()>> 8;
    $b[8] = getmygid();
    // Increment, 3 bytes, big endian
    $i = readRandomUint32();
    $b[9] = $i >> 16;
    $b[10] = $i >> 8;
    $b[11] = $i;
    return mb_convert_encoding(getBytesToString($b), 'utf-8', 'ascii');
}
  

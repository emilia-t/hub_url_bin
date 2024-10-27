<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// php设置///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
date_default_timezone_set('Asia/Hong_Kong');//设置时区
require_once __DIR__ . '/workerman/Autoloader.php'; // 需要通过 Composer 安装 Workerman
use Workerman\Worker;
use Workerman\Timer;
use Workerman\Connection\TcpConnection;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Protocols\Http\Response;
require_once  'B_installSqlite.php';
require_once  'D_store.php';
require_once  'D_JDT.php';
require_once  'D_QIR.php';
require_once  'D_FO.php';
require_once  'D_instruct.php';
require_once 'components/LayerHead.php';
require_once 'components/LayerContent.php';
require_once 'components/LayerAnchor.php';
require_once 'components/LayerServices.php';
require_once 'components/LayerNetwork.php';
/// php设置///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// 服务配置////////Server Config/////////////////////////////////////////////////////////////////////////////////////////////
const __LANGUAGE__='chinese';//english
const __VERSION__='1.0';//版本号
const __LAN__='192.168.0.';//局域网前缀
const __MY_IP__='192.168.0.170';//hub_url_bin服务的ip
const __MY_PORT__=80;//hub_url_bin服务的port
const __PUBLIC_KEY__='-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDRCpmZoZTNcGwOsSjqn/vOkV0p
Je1uMd5vsdF2Wci6rCy4lPiYKA/Za8TZzIg+vWeSOVTW3YrRMEbxvQVTf27xRwW6
cjy0fXUZA0UyMeCyTiTz1AIHM+AKYlANTNJICw+OVpVFsQ9b8cMs4fCdWaJ0AtlV
npgV2hbKhsnv+Kq6ZQIDAQAB
-----END PUBLIC KEY-----';
const __PRIVATE_KEY__='-----BEGIN PRIVATE KEY-----
MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBANEKmZmhlM1wbA6x
KOqf+86RXSkl7W4x3m+x0XZZyLqsLLiU+JgoD9lrxNnMiD69Z5I5VNbditEwRvG9
BVN/bvFHBbpyPLR9dRkDRTIx4LJOJPPUAgcz4ApiUA1M0kgLD45WlUWxD1vxwyzh
8J1ZonQC2VWemBXaFsqGye/4qrplAgMBAAECgYEAs6esgsC/piAsfiP7yklcnBeK
PUb/W2k4hj4IivJ29RfsB2bgj8Q+etmIALcrkOAvFxh2tYMJPueC0VdmMHCg4uYN
1IV4e1fLlruvYqQOEWrSzHem0drSI9pwcyBx/nlwUbLEpLi9KWjkWr5RUvoebP2K
MeLiGrVwN9fsU2g//DECQQDvZ7jTBapnQ+oPyn3D3naFuq7TJ4PViyLbR5OtKJQ9
Ln1a5ZwBqKLDYd8l8LXZFuG7clcLi/gliexAs5g/pWyLAkEA34gRpGRSyhM6hGek
GyzOPbuqv6SsfEG/hxYVzvtR1PmdLWY3QCoLkORxAri4vo13WozUqdPbtkhEbfTW
yKiizwJAPQt+plhQfipkGYixjus/35OdlnwB8saaqb1Tm5i4S+15y1626/lbH2Tq
aJs7U1KxVoGuTmRvbur/UfQ5gykkRwJARmNVktuYilNlwN3V+kywoRXgXbqgKfyW
MBaPRX80NIlpqiseyNC0laqpv36lhjOL2vKv4M56yDqXebx4ifU4VwJBAJL3tfzP
UdAbfX+SCNhUcqfVTvXDqeiaZtGbVoqhmFefrfbL7d5bhNvd2Lidsqk3SimsrbZT
1/SOtfigeH4miLw=
-----END PRIVATE KEY-----';
/// 服务配置////////Server Config/////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// 参数变量////////Parameter variables//////////////////////////////////////////////////////////////////////////////////////
$Version=__VERSION__;
$store=new D_Store();
$EtLayerHead=new LayerHead();
$EtLayerHeadHtml=null;
$EtLayerContent=new LayerContent();
$EtLayerContentHtml=null;
$EtLayerAnchor=new LayerAnchor();
$EtLayerAnchorHtml=null;
$EtLayerServices=new LayerServices();
$EtLayerServicesHtml=null;
$EtLayerNetwork=new LayerNetwork();
$EtLayerNetworkHtml=null;
$newJDT=new D_JDT();
$newQIR=new D_QIR();
$newFO=new D_FO();
$instruct=new D_instruct();
$theData=[
    'globalUid'=>0,
    'ip_connect_times'=>[
        /*示例
         * "192.168.1.2"=>5
         **/
    ]
];
$theConfig=[
    'userDataStructure'=>[
        'userEmail'=>null,
        'userName'=>null,
        'headColor'=>null,
    ],
    'globalId'=>0,
    'time'=>date('m-j'),
    'logFile'=>null,
    'typeList'=>[
        'ping','pong',
        'broadcast','get_serverConfig','get_publickey','publickey',
        'login','loginStatus',
        'get_webs','send_webs',
        'get_ports','send_ports'
    ],
    'automateTime'=>60,
];
if(!file_exists('log')){
    mkdir('log', 0777, true);
    echo "\n文件夹 'log' 创建成功\n";
}
if(!file_exists('cache')){
    mkdir('cache', 0777, true);
    echo "\n文件夹 'cache' 创建成功\n";
}
$theConfig['logFile']=fopen('./log/'.$theConfig['time'].'.txt','a+');
/// 参数变量////////Parameter variables//////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// 函数//////// function //////////////////////////////////////////////////////////////////////////////////////////////////////
function  RsaTranslate ($data,$type){
    if ($type=='encode') {
        $return=openssl_pkey_get_public(__PUBLIC_KEY__);//检查公钥是否可用
        if(!$return){
            echo("公钥不可用");
        }
        openssl_public_encrypt($data,$crypted,$return);//使用公钥加密数据
        $crypted=base64_encode($crypted);
        return $crypted;
    }
    if ($type=='decode') {
        $private_is_use=openssl_pkey_get_private(__PRIVATE_KEY__);//检查私钥是否可用
        if(!$private_is_use){
            echo("私钥不可用");
        }
        //对私钥进行解密
        openssl_private_decrypt(base64_decode($data),$decrypted,$private_is_use);
        return($decrypted);
    }
}
/**生成当前时间
 * @return string
 */
function creatDate(){
    $date=getdate();
    $mon=sprintf('%02d',$date['mon']);
    $day=sprintf('%02d',$date['mday']);
    $hours=sprintf('%02d',$date['hours']);
    $minutes=sprintf('%02d',$date['minutes']);
    $seconds=sprintf('%02d',$date['seconds']);
    return "{$date['year']}-{$mon}-{$day} {$hours}:{$minutes}:{$seconds}";
}
/**生成log
 * @param $logType
 * @param $logData
 * @return void
 */
function createLog($logType,$logData){
    global $theConfig;
    $time=creatDate();
    switch ($logType){
        case 'connect':{
            $log=<<<ETX

{$time}--连接Id为:{$logData['connectionId']};连接IP为:{$logData['connectionIp']};匿名用户连接

ETX;
            echo $log;
            fwrite($theConfig['logFile'],$log);
            break;
        }
        case 'disconnect':{
            $log=<<<ETX

{$time}--连接Id为:{$logData['connectionId']};Account为:{$logData['userEmail']};断开连接

ETX;
            echo $log;
            fwrite($theConfig['logFile'],$log);
            break;
        }
        case 'createGroupLayer':{
            $log=<<<ETX

{$time}--连接Id为:{$logData['connectionId']};Account为:{$logData['broadcastEmail']};新建分组图层id:{$logData['id']}

ETX;
            echo $log;
            fwrite($theConfig['logFile'],$log);
            break;
        }
        case 'login':{
            $log=<<<ETX

{$time}--连接Id为:{$logData['connectionId']};Account为:{$logData['userEmail']};用户登录

ETX;
            echo $log;
            fwrite($theConfig['logFile'],$log);
            break;
        }
        case 'anonymousLogin':{
            $log=<<<ETX

{$time}--连接Id为:{$logData['connectionId']};Account为:{$logData['userAccount']};匿名登录

ETX;
            echo $log;
            fwrite($theConfig['logFile'],$log);
            break;
        }
        case 'textMessage':{
            $log=<<<ETX

{$time}--连接Id为:{$logData['connectionId']};Account为:{$logData['broadcastEmail']};发送一条消息:{$logData['text']}

ETX;
            echo $log;
            fwrite($theConfig['logFile'],$log);
            break;
        }
        case 'warn':{
            $log=<<<ETX

{$time}--连接Id为:{$logData['connectionId']};连接IP为:{$logData['connectionIp']};恶意连接

ETX;
            echo $log;
            fwrite($theConfig['logFile'],$log);
            break;
        }
        default:{}
    }
}
/**获取当前在线人数
 * @return int
 */
function getOnlineNumber(){
    global $socket_worker;
    $users=[];
    foreach ($socket_worker->connections as $con){
        if(!property_exists($con,'email')){//匿名且未登录的socket
            continue;
        }
        if($con->email===''){//空用户
            continue;
        }
        if(in_array($con->email,$users)){//同一账号但不同会话的socket
            continue;
        }
        array_push($users,$con->email);
    }
    return count($users);
}

/**hub socket 连接事件
 * @param $connection
 */
function handle_connection($connection){
    echo 'id: '.$connection->id." link to hub\n";
}

/**hub socket 消息事件
 * @param $connection
 * @param $data
 * @return bool
 */
function handle_message($connection,$data){//收到客户端消息
    global $theData,$theConfig,$socket_worker,$newQIR,$newJDT,$newFO,$instruct,$Version,$store;
    $jsonData=$newJDT->checkJsonData($data);//1.校验并解析json格式
    $activated=true;//暂时默认设置为true允许所有连接运行指令
    if(gettype($jsonData)==='array'){//2.检测是否为数组类型
        if(array_key_exists('type',$jsonData)){//3.检测是否存在必要属性 'type'
            if(in_array($jsonData['type'],$theConfig['typeList'])){//4.检测type类型是否合规
                $nowType=$jsonData['type'];//5.处理数据
                switch ($nowType){
                    case 'ping':{
                        $connection->send($instruct->pong());
                        break;
                    }
                    case 'broadcast':{//广播数据
                        if($activated){//必须是非匿名会话才能使用
                            if(array_key_exists('class',$jsonData)){//0.class检测
                                $nowClass=$jsonData['class'];//1.提取类型
                                switch ($nowClass){
                                    case 'themeChange':{
                                        foreach ($socket_worker->connections as $con) {
                                            $theme='dark';
                                            if(isset($jsonData['data']['theme'])){
                                                $theme=$jsonData['data']['theme'];
                                            }
                                            $store->setServerConfigTheme($theme);
                                            $con->send($instruct->broadcast_themeChange($theme));
                                        }
                                        break;
                                    }
                                    case 'textMessage':{//普通文本消息
                                        $dateTime=creatDate();
                                        $logData=['connectionId'=>$connection->id,'broadcastEmail'=>$connection->email,'text'=>$jsonData['data']['message']];
                                        $sendArr = ['type'=>'broadcast','class'=>'textMessage','conveyor'=>$connection->email,'time'=>$dateTime,'data'=>$jsonData['data']];
                                        $sendJson = json_encode($sendArr,JSON_UNESCAPED_UNICODE);
                                        createLog('textMessage',$logData);
                                        foreach ($socket_worker->connections as $con) {
                                            if(property_exists($con,'email')){//避免发送给匿名用户
                                                $con->send($sendJson);//普通文本消息不会上传服务器,这里会将汉字之类的转化为base64
                                            }
                                        }
                                        break;
                                    }
                                }
                            }
                        }break;
                    }
                    case 'get_webs':{
                        $webs=$store->getWebList();
                        $connection->send($instruct->send_webs($webs));
                        break;
                    }
                    case 'get_ports':{
                        $ports=$store->getPortList();
                        $connection->send($instruct->send_ports($ports));
                        break;
                    }
                    case 'get_serverConfig':{//获取服务器的配置
                        $config=[
                            'version'=>$Version,
                            'theme'=>$store->getServerConfigTheme()
                        ];
                        $connection->send($instruct->send_serverConfig($config));
                        break;
                    }
                    case 'get_publickey':{//获取公钥数据
                        $sendArr=['type'=>'publickey','data'=>__PUBLIC_KEY__];
                        $sendJson=json_encode($sendArr,JSON_UNESCAPED_UNICODE);
                        $connection->send($sendJson);
                        break;
                    }
                }
            }
        }
    }
    return true;
}

/**hub socket 断开连接事件
 * @param $connection
 */
function handle_close($connection){
    echo 'id: '.$connection->id." link close\n";
}
/**hub socket 初始化
 *
 */
function handle_start(){
    echo "\nhub socket started!\n";
    Timer::add(30,function(){
        reSendWebList();
        reSendPortsList();
    });
}
/**导出前端
 * @return string
 */
function exportHtml(){
    global $store,
    $EtLayerHeadHtml,$EtLayerContentHtml,$EtLayerAnchorHtml,$EtLayerServicesHtml,$EtLayerNetworkHtml,
    $EtLayerHead,         $EtLayerContent,        $EtLayerAnchor         ,$EtLayerServices,        $EtLayerNetwork;
    $Scripts=file_get_contents("js/store.js");
    $EtLayerNetworkHtml=$EtLayerNetwork->export();
    $EtLayerHeadHtml=$EtLayerHead->export();
    $EtLayerContentHtml=$EtLayerContent->export();
    $EtLayerAnchorHtml=$EtLayerAnchor->export();
    $EtLayerServicesHtml=$EtLayerServices->export();
    return <<<HTML
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hub</title>
    <script>$Scripts</script>
    $EtLayerNetworkHtml
</head>
<body>
    $EtLayerHeadHtml
    $EtLayerContentHtml
    $EtLayerAnchorHtml
    $EtLayerServicesHtml
</body>
</html>
HTML;
}

/**reSendWebList
 * @return void
 */
function reSendWebList(){
    global $socket_worker,$instruct,$store;
    $webs=$store->getWebList();
    $ins=$instruct->send_webs($webs);
    foreach($socket_worker->connections as $con){
        $con->send($ins);
    }
}

/**reSendWebList
 * @return void
 */
function reSendPortsList(){
    global $socket_worker,$instruct,$store;
    $ports=$store->getPortList();
    $ins=$instruct->send_ports($ports);
    foreach($socket_worker->connections as $con){
        $con->send($ins);
    }
}

/**ping
 * @param $ip string
 * @return bool $activeHosts
 */
function ping($ip) {
    $command='';
    if(PHP_OS==='WINNT'){
        $command='ping -n 1 -w 100 '.$ip;
    }else{
        $command='timeout 0.1 ping -c 1 '.$ip;
    }
    $output=shell_exec($command);
    if($output===null){
        return false;
    }else{
        return stripos($output, 'ttl=') !== false;
    }
}

/**扫描指定网段(24)的主机活跃状态，并返回活跃的主机ip地址列表
 * @param $network string
 * @return array $activeHosts
 */
function scanActiveHost($network='192.168.0.'){
    $activeHosts = [];
    for ($i = 1; $i <= 255; $i++) {
        $ip = $network . $i;
        if (ping($ip)) {
//            echo "Host $ip is up.\n";
            $activeHosts[]=$ip;
        } else {
//            echo "Host $ip is down.\n";
        }
    }
    return $activeHosts;
}

/**扫描指定ip的活跃的特定端口，并返回活跃的端口列表
 * @param string $ip
 * @return array $activeHosts
 */
function scanPort($ip='192.168.0.1'){
    $ports = [
        80,443,8080,8000,8081,8082,8083,8084,8888,12345,5005,1000,2000,3000,4000,5000,6000,7000,4343,4433,10880,2550,2345,1234,5678,9443,9998,9999
    ];//尽可能覆盖已知的web端口
    $timeout = 0.1;
    $activePort = [];
    // 遍历每个端口
    foreach ($ports as $port) {
        $connection = @fsockopen($ip, $port, $errno, $errstr, $timeout);
        if ($connection) {
            $activePort[]=$port;
            fclose($connection);
        }
    }
    return $activePort;
}

/**用于判断指定的ip port是否是一个web网站,如果是则返回true并写入数据库
 * @param $ip
 * @param int $port
 * @return bool $activeHosts
 */
function isWebService($ip, $port = 80) {
    global $store;
    if ($ip === __MY_IP__ && $port === __MY_PORT__) { // 跳过自身
        return false;
    }
    // 尝试连接指定的 IP 和端口
    $fp = fsockopen($ip, $port, $errno, $errstr, 1); // 1 秒超时
    if (!$fp) {
        // 如果连接失败，返回 false
        return false;
    }
    // 构造 HTTP 请求头
    $httpRequest = "GET / HTTP/1.1\r\n";
    $httpRequest .= "Host: $ip";
    if ($port !== 80 && $port !== 443) {
        $httpRequest .= ":$port";
    }
    $httpRequest .= "\r\nConnection: Close\r\n\r\n";
    // 发送 HTTP 请求
    fwrite($fp, $httpRequest);
    // 读取完整的响应内容
    $response = fread($fp, 65536); // 读取 64 KB 数据
    fclose($fp);
    // 检查响应是否包含 HTTP 头部
    if (preg_match('/^HTTP\/\d\.\d \d{3}/', $response)) {
        // 尝试提取 HTML 内容中的 <title> 标签
        if (preg_match('/<title>(.*?)<\/title>/i', $response, $matches)) {
            $title = $matches[1];
        } else {
            $title = "Unknown Title";  // 如果未找到 title，则使用默认值
        }
        // 尝试提取网站图标 <link rel="icon"> 或 <link rel="shortcut icon">
        $icon = '';
        if (preg_match('/<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]+href=["\'](.*?)["\']/i', $response, $iconMatches)) {
            $icon = $iconMatches[1];
            // 如果是相对路径，转换为完整的 URL
            if (strpos($icon, 'http') !== 0) {
                $protocol = ($port === 443) ? 'https' : 'http';
                $urlBase = $protocol . '://' . $ip;
                if ($port !== 80 && $port !== 443) {
                    $urlBase .= ":$port";
                }
                // 处理相对路径（以 "/" 开头或者不以 "/" 开头）
                if (strpos($icon, '/') === 0) {
                    $icon = $urlBase . $icon;
                } else {
                    $icon = $urlBase . '/' . $icon;
                }
            }
        } else {
//             如果没有找到 <link> 标签，使用默认的 /favicon.ico
//            $protocol = ($port === 443) ? 'https' : 'http';
//            $icon = $protocol . '://' . $ip;
//            if ($port !== 80 && $port !== 443) {
//                $icon .= ":$port";
//            }
//            $icon .= '/favicon.ico';
            $icon .= 'http://'.__MY_IP__.'/favicon.ico';
        }
        // 构造 URL
        $protocol = ($port === 443) ? 'https' : 'http';
        $url = $protocol . '://' . $ip;
        if ($port !== 80 && $port !== 443) {
            $url .= ":$port";
        }
        // 将 URL、Title 和 Icon 存储在 $store['webList'] 数组中
        $store->pushWebList(['url' => $url, 'title' => $title, 'icon' => $icon]);
        return true;
    } else {
        // 如果没有有效的 HTTP 响应，返回 false
        return false;
    }
}


/**用于更新数据
 * @return void
 */
function scan(){
    global $store;
    echo __LANGUAGE__==='chinese'?"扫描网络中......\n":"Scanning network for the first time...\n";
    $ipDoPort=[];////[ [ '192.168.0.1'=>[80,8080] ] ......]
    $urlList=[];////"http://192.168.0.1:8080"
    $activeIps=scanActiveHost(__LAN__);//扫描活跃主机
    foreach($activeIps as $ip){$ipDoPort[$ip]=scanPort($ip);}//扫描活跃主机的活跃端口
    foreach($ipDoPort as $IP=>$PORTS){//判断这些端口否是web服务
        foreach($PORTS as $PORT){
            if(isWebService($IP,$PORT)){
                if($PORTS!==443){
                    $urlList[]='http://'.$IP.':'.$PORT;
                }else{
                    $urlList[]='https://'.$IP.':'.$PORT;
                }
            }
        }
    }
    $store->resetPortList($ipDoPort);
//    print_r("扫描结束！\n");
//    print_r("结果如下！\n");
//    print_r($store->getWebList());
//    print_r("结果如上！\n");
}

/// 函数//////// function //////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// 运行//////// run ///////////////////////////////////////////////////////////////////////////////////////////////////////////
echo __LANGUAGE__==='chinese'?"中文Chinese\n":"English英文\n";
echo __LANGUAGE__==='chinese'?'URL收纳箱 版本 '.__VERSION__."\n":'OnlineMapServer Version '.__VERSION__."\n";
echo __LANGUAGE__==='chinese'?"(c) Minxi Wan，保留所有权利。\n":"(c) Minxi Wan All right reserved.\n";
usleep(1000000);
$workerA=new Worker();//网络扫描
$workerA->count=1;
$workerA->onWorkerStart=function(){
    scan();
    Timer::add(300,function(){
        scan();
    });
};
$workerB=new Worker('tcp://0.0.0.0:80');//web服务
$workerB->count=10;
$workerB->onMessage = function (TcpConnection $connection, $data) {
    // 解析请求的第一行，通常是类似于 "GET / HTTP/1.1" 的内容
    if (preg_match('/^GET\s+(.*?)\s+HTTP\/\d\.\d/', $data, $matches)) {
        $path = $matches[1]; // 获取请求路径
        // 根据请求的路径来区分是页面请求还是图标请求
        if ($path === '/') {
            // 这是网页整体请求
            // 发送 HTTP 头部
            $connection->send("HTTP/1.1 200 OK\r\nContent-Type: text/html\r\n\r\n");
            // 发送 HTML 内容
            $connection->send(exportHtml()); // 返回网页内容
        } elseif ($path === '/favicon.ico') {
            // 这是图标请求
            // 发送 HTTP 头部
            $connection->send("HTTP/1.1 200 OK\r\nContent-Type: image/x-icon\r\n\r\n");
            // 读取图标文件并发送
            $icon = file_get_contents(__DIR__ . '/favicon.ico');
            $connection->send($icon); // 返回图标内容
        } else {
            // 如果请求的路径不是已知的，返回 404 Not Found
            $connection->send("HTTP/1.1 404 Not Found\r\nContent-Type: text/html\r\n\r\n");
            $connection->send("<h1>404 Not Found</h1>");
        }
    } else {
        // 如果请求格式不正确，也返回 400 Bad Request
        $connection->send("HTTP/1.1 400 Bad Request\r\nContent-Type: text/html\r\n\r\n");
        $connection->send("<h1>400 Bad Request</h1>");
    }
    // 关闭连接，表示响应结束
    $connection->close();
};
$socket_worker=new Worker('websocket://0.0.0.0:20000');//局域网通信服务器
$socket_worker->count=1;
$socket_worker->onConnect='handle_connection';
$socket_worker->onMessage='handle_message';
$socket_worker->onClose='handle_close';
$socket_worker->onWorkerStart='handle_start';
Worker::runAll();
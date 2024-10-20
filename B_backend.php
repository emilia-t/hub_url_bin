<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// php设置///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
date_default_timezone_set('Asia/Hong_Kong');//设置时区
require_once __DIR__ . '/workerman/Autoloader.php'; // 需要通过 Composer 安装 Workerman
require_once  'B_installSqlite.php';
require_once  'D_store.php';
require_once 'components/LayerHead.php';
require_once 'components/LayerContent.php';
require_once 'components/LayerAnchor.php';
use Workerman\Connection\TcpConnection;
use Workerman\Connection\AsyncTcpConnection;
use Workerman\Worker;
use Workerman\Timer;
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
$store=new D_Store();
$EtLayerHead=new LayerHead('dark');
$EtLayerHeadHtml=null;
$EtLayerContent=new LayerContent('dark');
$EtLayerContentHtml=null;
$EtLayerAnchor=new LayerAnchor('dark');
$EtLayerAnchorHtml=null;
/// 参数变量////////Parameter variables//////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/// 函数//////// function //////////////////////////////////////////////////////////////////////////////////////////////////////
function exportHtml(){
    global $EtLayerHeadHtml,$EtLayerContentHtml,$EtLayerAnchorHtml,
                $EtLayerHead,         $EtLayerContent,        $EtLayerAnchor,        $store;
    $EtLayerHeadHtml=$EtLayerHead->export();
    $EtLayerContentHtml=$EtLayerContent->export($store->getWebList());
    $EtLayerAnchorHtml=$EtLayerAnchor->export();
    return <<<HTML
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hub</title>
</head>
<body>
    $EtLayerHeadHtml
    $EtLayerContentHtml
    $EtLayerAnchorHtml
</body>
</html>
HTML;
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
    if($ip===__MY_IP__ && $port===__MY_PORT__){//跳过自身
        return false;
    }
    // 使用 fsockopen 尝试连接指定的 IP 和端口
    $fp = @fsockopen($ip, $port, $errno, $errstr, 1); // 1 秒超时
    if (!$fp) {
        // 如果连接失败，返回 false
        return false;
    }
    // 构造 HTTP 请求头
    $httpRequest = "GET / HTTP/1.1\r\n";
    $httpRequest .= "Host: $ip\r\n";
    $httpRequest .= "Connection: Close\r\n\r\n";
    // 发送 HTTP 请求
    fwrite($fp, $httpRequest);
    // 读取响应内容（可以读取更多字节来尝试获取完整的 HTML 页面）
    $response = fread($fp, 8192);
    fclose($fp);
    // 检查响应是否包含 HTTP 头部
    if (preg_match('/^HTTP\/\d\.\d \d{3}/', $response)) {
        // 如果有响应头部，判断该地址是一个 Web 服务
        // 尝试提取 HTML 内容中的 <title> 标签
        if (preg_match('/<title>(.*?)<\/title>/i', $response, $matches)) {
            $title = $matches[1];
        } else {
            $title = "Unknown Title";  // 如果未找到 title，则使用默认值
        }
        // 构造 URL
        $protocol = ($port === 443) ? 'https' : 'http';
        $url = $protocol . '://' . $ip . ':' . $port;
        // 将 URL 和 Title 存储在 $store['webList'] 数组中
        $store->pushWebList(['url' => $url, 'title' => $title]);
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
    Timer::add(125,function(){
        global $store;
        echo "\nworkerA中的store start：\n";
        print_r($store);
        echo "\nworkerA中的store end：\n";
    });
};
$workerB=new Worker('tcp://0.0.0.0:80');//web服务
$workerB->count=10;
$workerB->onMessage=function (TcpConnection $connection,$data){
    //var_dump($data);
    // 发送 HTTP 头部
    $connection->send("HTTP/1.1 200 OK\r\nContent-Type: text/html\r\n\r\n");
    // 发送 HTML 内容
    $connection->send(exportHtml());
    // 关闭连接，表示响应结束
    $connection->close();
};
$workerB->onWorkerStart=function(){
    Timer::add(130,function(){
        global $store;
        echo "\nworkerB中的store start：\n";
        print_r($store);
        echo "\nworkerB中的store end：\n";
    });
};
Worker::runAll();
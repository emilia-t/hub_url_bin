<?php
/** 连接服务器和管理通信的组件
 * $mode 运行的模式
 *  normal 模式下不会输出错误信息
 *  debug 模式下会输出错误信息
 */
class LayerNetwork{
    private $mode;

    public function __construct($mode = 'normal') {
        $this->mode = $mode;
    }
    function export(){
        $Instruct=file_get_contents("js/Instruct.js");
        $lame=file_get_contents("js/libmp3lame.min.js");
        $ipPort='wss://'.__MY_IP__.':20000';
        return <<<HTML
<script>
    let INSTRUCT =$Instruct;
    var Instruct=new INSTRUCT('$ipPort');
</script>
HTML;
    }
}
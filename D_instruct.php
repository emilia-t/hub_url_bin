<?php


class D_instruct
{
    private $packed=true;
    private $checked=true;
    private $options=JSON_UNESCAPED_UNICODE;
    public function __construct($packed=true,$checked=true){
        if(is_bool($packed)){
            $this->packed=$packed;
        }
        if(is_bool($checked)){
            $this->checked=$checked;
        }
    }
    public function ping(){
        $obj=[
            'type'=>'ping'
        ];
        if($this->packed){
            return json_encode($obj,$this->options);
        }else{
            return $obj;
        }
    }
    public function pong(){
        $obj=[
            'type'=>'pong'
        ];
        if($this->packed){
            return json_encode($obj,$this->options);
        }else{
            return $obj;
        }
    }
    public function send_serverConfig($data){
        $obj=[
            'type'=>'send_serverConfig',
            'data'=>$data
        ];
        if($this->packed){
            return json_encode($obj,$this->options);
        }else{
            return $obj;
        }
    }
    public function send_webs($webs){
        $obj=[
            'type'=>'send_webs',
            'data'=>['webs'=>$webs]
        ];
        if($this->packed){
            return json_encode($obj,$this->options);
        }else{
            return $obj;
        }
    }
    public function send_ports($ports){
        $obj=[
            'type'=>'send_ports',
            'data'=>['ports'=>$ports]
        ];
        if($this->packed){
            return json_encode($obj,$this->options);
        }else{
            return $obj;
        }
    }
    public function send_onlineNumber($number){
        $obj=[
            'type'=>'send_onlineNumber',
            'data'=>['number'=>$number]
        ];
        if($this->packed){
            return json_encode($obj,$this->options);
        }else{
            return $obj;
        }
    }
    public function broadcast_themeChange($theme){
        $obj = [
            'type'=>'broadcast',
            'class'=>'themeChange',
            'data'=>['theme'=>$theme]
        ];
        if($this->packed){
            return json_encode($obj,$this->options);
        }else{
            return $obj;
        }
    }
    //登录名称和密码验证(非匿名)
    public function ckLogonAccount($email,$password){
        $pattern ="/[^a-zA-Z0-9_@.+\/=-]/";
        if (preg_match($pattern, $email.$password)){
            return false;
        }else{
            return true;
        }
    }
    //匿名登录名称验证
    public function ckAnonymousLogonAccount($accountName){
        if(!is_string($accountName)){
            return false;
        }
        $pattern = "/[^_A-Za-z0-9\x{4e00}-\x{9fa5}]/u";
        if (preg_match($pattern, $accountName)) {
            return false;
        } else {
            return true;
        }
    }
}
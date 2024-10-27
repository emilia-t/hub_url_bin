class Instruct{//综合的一个连接服务端的通讯类
    constructor(url){
        this.url=url;
        this.isLink=false;
        this.isLogin=false;
        this.socket=undefined;//会话
        this.errors=[];//执行错误的指令返回数据
        this.errorsROnly=[];//只读的
        this.corrects=[];//执行正确的指令返回数据
        this.correctsROnly=[];//只读的
        this.messages=[];
        this.presence=[];
        this.publickey='';
        this.userEmail=null;
        this.getConfigTime=0;//最后一次获取的服务器配置的时间
        this.lastPing=0;
        this.lastPong=0;
        this.typeList=[
            'ping','pong',
            'broadcast','get_serverConfig','get_publickey','publickey','login',
            'login','loginStatus',
            'get_webs','send_webs',
            'get_ports','send_ports',
            'get_onlineNumber','send_onlineNumber'
        ];
        this.Instruct={//指令合集
            ping(){
                return {type:'ping'};
            },
            login(email,password){//登录指令
                this.email=email || '';
                this.password=password || '';
                return {type:'login',data:{email:this.email,password:this.password}}
            },
            anonymousLogin(name){
                return {
                    type:'anonymousLogin',
                    data:{name}
                }
            },
            get_publickey(){//获取公钥指令
                return {type:'get_publickey'}
            },
            get_serverConfig(){//获取服务器配置
                return {type:'get_serverConfig'}
            },
            get_webs(){
                return {type:'get_webs'}
            },
            get_ports(){
                return {type:'get_ports'}
            },
            get_onlineNumber(){
                return {type:'get_onlineNumber'}
            },
            broadcast_themeChange(theme){
                return {type:'broadcast',class:'themeChange',data:{theme:theme}}
            }
        };
        this.QIR={//检测间
            /**
             * 日志函数
             */
            onLog(text,type){
                function reset(){
                    window.logConfig={
                        message:{code:-1,time:'',text:'',from:'',type:'',data:undefined}
                    };
                }
                let lock=false;
                try{
                    window.logConfig.message.code-=1;
                    window.logConfig.message.text=text;
                    window.logConfig.message.from='external:instructPipe';
                    window.logConfig.message.type=type;
                }catch (e) {
                    lock=true;
                }
                if(lock){
                    reset();
                }
            },
            /**检测是否为对象类型的数据,是则返回t
             * @return boolean
             * @param obj any
             */
            isObject (obj) {
                return Object.prototype.toString.call(obj) === '[object Object]';
            },
            /**检测一个对象是否存在某一个属性,有则返回t
             * @return boolean
             * @param obj any
             * @param propName string
             */
            hasProperty(obj, propName) {
                return obj.hasOwnProperty(propName);
            },
            /**检测一个字符串是否为六位的十六进制颜色,是则返回t
             * @return boolean
             * @param str
             */
            color16Check(str){
                if(Object.prototype.toString.call(str)!=='[object String]'){return false;}
                let Exp=/^[0-9A-F]{6}$/i;
                if(Exp.test(str)===false){
                    this.onLog('请输入正确的16进制颜色格式例如#123456','warn');
                    return false;
                }
                return true;
            },
            isNumber(value) {
                if (typeof value === 'string' && !isNaN(value)) {
                    return true;
                }
                return typeof value === 'number' && !isNaN(value);
            },
            isArray(obj) {
                return Array.isArray(obj);
            }
        };
        this.startSetting();
    }
    startSetting(){//初始化配置
        this.link();
        this.intervalPing();
    }
    unicastInstructCheck(DATA,TYPE){//指令的检查移动到此处
        let status=true;
        switch (TYPE){
            case 'pong':{//无需检查
                break;
            }
            case 'publickey':{//服务器发来公钥
                break;
            }
            case 'send_serverConfig':{//服务器发来配置信息
                if('data' in DATA){
                    status = 'theme' in DATA.data;
                    status = 'version' in DATA.data;
                }else {
                    status=false;
                }
                break;
            }
            case 'send_webs':{
                if('data' in DATA){
                    if('webs' in DATA.data){
                        status=Array.isArray(DATA.data.webs);
                    }else{
                        status=false;
                    }
                }else {
                    status=false;
                }
                break;
            }
            case 'send_ports':{
                if('data' in DATA){
                    if('ports' in DATA.data){
                        status=Array.isArray(DATA.data.ports);
                    }else{
                        status=false;
                    }
                }else {
                    status=false;
                }
                break;
            }
            case 'send_onlineNumber':{
                if('data' in DATA){
                    if('number' in DATA.data){
                        status=typeof DATA.data.number==='number';
                    }else{
                        status=false;
                    }
                }else {
                    status=false;
                }
            }
            default:{break;}
        }
        return status;
    }
    broadInstructCheck(DATA,CLASS){//指令的检查移动到此处
        let status=true;
        switch (CLASS){
            case 'textMessage':{//普通文本消息
                break;
            }
            case 'themeChange':{
                if('data' in DATA){
                    status = 'theme' in DATA.data;
                }else {
                    status=false;
                }
                break;
            }
            default:{break;}
        }
        return status;
    }
    intervalPing(){
        setInterval(
            ()=>{
                if(this.isLink){
                    this.lastPing=new Date().getTime();
                    this.send(this.Instruct.ping());
                    setTimeout(()=>{if(Math.abs(this.lastPong-this.lastPing)>1000){this.onLog('服务器连接超时(>1S)','warn');this.socket.close();}},1000);//连接超时检测
                }
            },
            5000
        );
    }
    broadcastThemeChange(theme){
        this.send(this.Instruct.broadcast_themeChange(theme));
    }
    getServerPublickey(){//获取服务器公钥
        this.send(this.Instruct.get_publickey());
    }
    getServerConfig(){//获取服务器公钥
        this.send(this.Instruct.get_serverConfig());
    }
    getWebs(){
        this.send(this.Instruct.get_webs());
    }
    getPorts(){
        this.send(this.Instruct.get_ports());
    }
    getOnlineNumber(){
        this.send(this.Instruct.get_onlineNumber());
    }
    login(email,password){//登录方法
        let pat=new RegExp('[^a-zA-Z0-9\_@.+/=-]');
        if(!pat.test(''+email+password)){
            this.send(this.Instruct.login(email,password));
        }else {
            this.onLog('邮箱及密码只能是字母、数字、下划线 @ . - ，','warn');
        }
    }
    anonymousLogin(name){
        let pat=/[^_A-Za-z0-9\u4e00-\u9fa5]/;
        if(!pat.test(''+name)){
            this.send(this.Instruct.anonymousLogin(name));
        }else {
            this.onLog('名称只能是字母、数字、下划线或汉字','warn');
        }
    }
    link(){//连接服务器方法
        this.socket=new WebSocket(this.url);
        this.socket.onopen=(ev)=>this.onOpen(ev);
        this.socket.onmessage=(ev)=>this.onMessage(ev);
        this.socket.onclose=(ev)=>this.onClose(ev);
        this.socket.onerror=(ev)=>this.onError(ev);
        return true;
    }
    send(instructObj){//发送数据
        if(this.isLink){
            if(this.instructObjCheck(instructObj)){//1.数据检查
                let json=JSON.stringify(instructObj);
                // 指令截取
                // if(instructObj.type==='broadcast' && instructObj.class==='area'){
                //   console.log("%c↓↓↓截取到的发送指令↓↓↓","color:blue;");
                //   console.log(JSON.parse(JSON.stringify(instructObj)));
                //   console.log("%c↑↑↑截取到的发送指令↑↑↑","color:blue;");
                // }
                // 指令截取
                // 始终输出发送的指令
                if(true){
                    console.log("%c↓↓↓发送出去的指令↓↓↓","color:blue;");
                    console.log(JSON.parse(JSON.stringify(instructObj)));
                    console.log("%c↑↑↑发送出去的指令↑↑↑","color:blue;");
                }
                // 始终输出发送的指令
                this.socket.send(json);
            }else{
                this.onLog('指令无效或不安全','error');
            }
        }else{
            this.onLog('服务器连接中断','warn');
        }
    }
    instructObjCheck(instructObj){//指令检查
        if(Object.prototype.toString.call(instructObj)!=='[object Object]'){
            return false;
        }
        if(instructObj.type===undefined){
            return false;
        }
        if(this.typeList.indexOf(instructObj.type)===-1){
            return false;
        }
        if(instructObj.type==='broadcast'){
            if(instructObj.class===undefined){
                return false;
            }
        }
        return true;
    }
    onLog(text,type,data){
        function reset(){
            window.logConfig={
                message:{code:-1,time:'',text:'',from:'',type:'',data:undefined}
            };
        }
        let lock=false;
        try{
            window.logConfig.message.code-=1;
            window.logConfig.message.text=text;
            window.logConfig.message.from='external:instructPipe';
            window.logConfig.message.type=type;
            window.logConfig.message.data=data;
        }catch (e) {
            lock=true;
        }
        if(lock){
            reset();
        }
    }
    onMessage(ev){//收到消息事件
        let jsonData=null;
        try{jsonData=JSON.parse(ev.data);}catch(e){this.onLog('无法解析指令','error',ev.data);return false;}
        // 指令截取
        // if(jsonData.type==='broadcast' && jsonData.class==='area'){
        //   console.log("%c↓↓↓截取到的发送指令↓↓↓","color:green;");
        //   console.log(JSON.parse(JSON.stringify(jsonData)));
        //   console.log("%c↑↑↑截取到的发送指令↑↑↑","color:green;");
        // }
        // 指令截取
        // 始终输出接收的指令
        if(true){
            console.log("%c↓↓↓接收到的指令↓↓↓","color:green;");
            console.log(JSON.parse(JSON.stringify(jsonData)));
            console.log("%c↑↑↑接收到的指令↑↑↑","color:green;");
        }
        // 始终输出接收的指令
        if(jsonData.type!==undefined && typeof jsonData.type==='string'){
            let nowType=jsonData.type;
            if(!this.unicastInstructCheck(jsonData,nowType)){this.onLog('无法解析指令','error',jsonData);return false;}
            switch (nowType){
                case 'pong':{
                    this.lastPong=new Date().getTime();
                    break;
                }
                case 'broadcast':{//服务器发来的广播
                    let classIs=jsonData.class;//获取广播类型
                    if(!this.broadInstructCheck(jsonData,classIs)){this.onLog('无法解析指令','error',jsonData);return false;}
                    switch (classIs){
                        case 'themeChange':{
                            $store['config.theme']=jsonData.data.theme;
                            break;
                        }
                        case 'textMessage':{//普通文本消息
                            let NewMessageObj={'type':'broadcast','class':'textMessage','conveyor':jsonData.conveyor,'time':jsonData.time,'data':jsonData.data};
                            this.messages.push(NewMessageObj);
                            break;
                        }
                    }
                    break;
                }
                case 'send_onlineNumber':{
                    $store['onlineNumber']=jsonData.data.number;
                    break;
                }
                case 'send_webs':{
                    $store['webs']=jsonData.data.webs;
                    break;
                }
                case 'send_ports':{
                    $store['ports']=jsonData.data.ports;
                    break;
                }
                case 'send_serverConfig':{
                    $store['config.theme']=jsonData.data.theme;
                    $store['config.version']=jsonData.data.version;
                    this.getConfigTime=new Date().getTime();
                    break;
                }
                case 'publickey':{//服务器发来公钥
                    this.publickey=jsonData.data;
                    break;
                }
                default:{this.onLog('无法解析指令','error',jsonData);}
            }
        }
        else{this.onLog('无法解析指令','error',jsonData);}
    }
    onClose(){//断开连接事件
        this.isLink=false;
        this.onLog('服务器连接中断','warn');
        return true;
    }
    onError(){//连接失败事件
        this.isLink=false;
        this.onLog('服务器连接失败','warn');
        return true;
    }
    onOpen(){//连接成功事件
        this.isLink=true;
        this.onLog('服务器连接成功','tip');
        this.getServerPublickey();//获取公钥
        this.getServerConfig();
        this.getWebs();
        this.getPorts();
        this.getOnlineNumber();
        return true;
    }
}

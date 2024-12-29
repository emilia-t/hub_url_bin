/**
 * 用于监听 $store 中某个属性的变化，并通知所有订阅者
 * @param obj - 要监听的对象
 * @param property - 要监听的属性名
 */
function watch(obj,property){
    let value=obj[property];// 保存当前属性的值
    if (!$subscribe[property]){
        $subscribe[property]=[];// 初始化订阅列表
    }
    Object.defineProperty(obj,property,{
        get(){return value;},
        set(newValue){
            if(newValue!==value){
                value=newValue;
                $subscribe[property].forEach(callback=>callback(newValue));
            }
        }
    });
}
/**
 * 订阅某个属性的变化
 * @param property - 要订阅的属性
 * @param callback - 属性变化时要执行的回调函数
 */
function subscribe(property, callback) {
    if (!$subscribe[property]){
        $subscribe[property]=[];// 如果没有订阅列表则初始化
    }
    $subscribe[property].push(callback);// 添加新的订阅者
}
var $store=Object.create(null);
var $subscribe=Object.create(null);
$store['config.theme']=null;
$store['config.version']=null;
$store['webs']=[];// [ { 'url':'xxxx', 'icon':'xxxx', 'title':'xxxx' } ]
$store['ports']=[];// [ { 'ip':'xxxx', 'ports':'xxxx, xxxx' } ]
$store['onlineNumber']=0;
$store['muted']=false;//禁用语音状态
for(let [key,value] of Object.entries($store)){watch($store,key);}

<?php
/**
 * 通过连接sqlite数据库来创建一个共享的数据库，并使用同样的函数或方法存取数据
**/

class D_Store {
    private $store;
    public function __construct(){
        $this->link();
    }
    private function link(){
        $this->store=new PDO('sqlite:' . 'database.sqlite');
        $this->store->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $storeExample=[
            'portList'=>[
//        [
//        'ip'=>'192.168.0.1',
//        'ports'=>'80,8080,443',
//        'sequence'=>1
//        ]
                //......
            ],//开放的端口列表
            'webList'=>[
//        [
//        'url'=>'http://192.168.0.1:8080',
//        'title'=>'一个网站',
//        'icon'=>'http://192.168.0.1:8080/favicon.icon',
//        'sequence'=>1
//        ]
                //......
            ],//web列表
            'serverConfig'=>[
                'theme'=>'dark'//web主题名称
            ]//服务器配置信息
        ];
    }
    public function getPortList(){//获取portList表数据
        try{
            $selectSQL = "SELECT * FROM portList";
            $stmt = $this->store->query($selectSQL);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);// 获取结果
        } catch (PDOException $e) {
            echo __LANGUAGE__==='chinese'?"获取portList表失败：\n". $e->getMessage():"get portList failed:  \n" . $e->getMessage();
            return [];
        }
    }
    public function getServerConfigTheme(){
        try{
            $selectSQL = "SELECT theme FROM serverConfig";
            $stmt = $this->store->query($selectSQL);
            $result=$stmt->fetch(PDO::FETCH_NUM);
            if($result!==false){
                return $result[0];// 获取结果
            }else{
                return 'dark';
            }
        } catch (PDOException $e) {
            echo __LANGUAGE__==='chinese'?"获取serverConfig表失败：\n". $e->getMessage():"get serverConfig failed:  \n" . $e->getMessage();
            return 'dark';
        }
    }
    public function setServerConfigTheme($theme){
        try{
            $updateSQL = "UPDATE serverConfig SET theme = :value1";
            $stmt = $this->store->prepare($updateSQL);
            $stmt->bindParam(':value1', $theme);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo __LANGUAGE__==='chinese'?"设置主题失败：\n". $e->getMessage():"set theme failed:  \n" . $e->getMessage();
            return false;
        }
    }
    public function getWebList(){//获取webList表数据
        try{
            $selectSQL = "SELECT * FROM webList";
            $stmt = $this->store->query($selectSQL);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);// 获取结果
        } catch (PDOException $e) {
            echo __LANGUAGE__==='chinese'?"获取webList表失败：\n". $e->getMessage():"get webList failed:  \n" . $e->getMessage();
            return [];
        }
    }
    public function pushPortList($ipStr,$portsArr){

    }
    public function pushWebList($webArr){//单条web添加或更新
        //$webArr = ['url' => 'http://192.168.0.1', 'title' => '一个网站', 'icon'=>'http://192.168.0.1/favicon.ico']
        try {
            $webListExp=['url'=>$webArr['url'],'title'=>$webArr['title'],'icon'=>$webArr['icon'],'sequence'=>-1];
            $sql = "REPLACE INTO webList (url, title, icon, sequence) VALUES (:url, :title, :icon, :sequence)";
            $stmt = $this->store->prepare($sql);
            $stmt->bindParam(':url', $webListExp['url']);
            $stmt->bindParam(':title', $webListExp['title']);
            $stmt->bindParam(':icon', $webListExp['icon']);
            $stmt->bindParam(':sequence', $webListExp['sequence']);
            $stmt->execute();
            echo __LANGUAGE__==='chinese'?"替换或插入到webList表成功。\n":"replace or insert webList successful.\n";
        } catch (PDOException $e) {
            echo __LANGUAGE__==='chinese'?"替换或插入到webList表失败：\n" . $e->getMessage():"replace or insert webList failed:  \n" . $e->getMessage();
        }
    }
    public function resetPortList($portListArr){//重置port list
        //$portListArr = [ [ '192.168.0.1'=>[80,8080] ] ......]
        try {
            $sql = "DELETE FROM portList";// DELETE 语句，清空所有数据
            $stmtA = $this->store->prepare($sql);
            $stmtA->execute();
            $portListExp=[];
            foreach($portListArr as $ip=>$portsArr){
                $portsStr=implode(',',$portsArr);
                $sequence=count($portListExp)+1;
                $portListExp[]=['ip'=>$ip,'ports'=>$portsStr,'sequence'=>$sequence];
            }
            $sql = "INSERT INTO portList (ip, ports, sequence) VALUES (:ip, :ports, :sequence)";// INSERT 语句，添加多条数据
            $stmt = $this->store->prepare($sql);
            $this->store->beginTransaction();// 开始事务（整合多条插入操作）
            foreach ($portListExp as $row) {
                $stmt->execute([
                    ':ip' => $row['ip'],
                    ':ports' => $row['ports'],
                    ':sequence' => $row['sequence']
                ]);
            }
            $this->store->commit();// 提交事务
            echo __LANGUAGE__==='chinese'?"重新设置portList表成功。\n":"reset portList successful.\n";
        } catch (PDOException $e) {
            $this->store->rollBack();// 若错误回滚事务
            echo __LANGUAGE__==='chinese'?"重新设置portList表失败：\n" . $e->getMessage():"reset portList failed:  \n" . $e->getMessage();
        }
    }
    public function resetWebList($webListArr){

    }
}

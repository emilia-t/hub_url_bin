<?php
//表格1：webList
//列名|                  列值类型|             列值示例值|                                                              备注说明
//url|                    字符串|                 "http://192.168.0.1:8080"|                                     网站url
//icon|                  字符串|                 "http://192.168.0.1:8080/favicon.ico"|                 网站图标链接
//title|                  字符串|                 "一个网站"|                                                                网站名称
//sequence|         整数|                    1|                                                                               排序用途
//
//
//表格2：portList
//列名|                  列值类型|            列值示例值|                                           备注说明
//ip|                       字符串|                  "192.168.0.1"|                                   ip地址
//ports|                 字符串|                  "80,443,8080"|                                  这个ip所开放的端口
//sequence|          整数|                       1|                                                       排序用途
//
//
//表格3：serverConfig
//列名|            列值类型|            列值示例值|                                          备注说明
//theme|         字符串|               "dark"|                                                 web UI 界面的主题名称


// 定义 SQLite 数据库文件路径
$dbFile = 'database.sqlite';

try {
    // 检查数据库文件是否存在
    if (!file_exists($dbFile)) {
        // 如果数据库文件不存在，创建数据库
        $db = new PDO('sqlite:' . $dbFile);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "数据库不存在，正在创建...\n";

        // 创建表格1：webList
        $createWebListTable = "
            CREATE TABLE IF NOT EXISTS webList (
                url TEXT PRIMARY KEY NOT NULL,
                title TEXT NOT NULL,
                icon TEXT NOT NULL,
                sequence INTEGER NOT NULL
            );
        ";
        $db->exec($createWebListTable);
        echo "表格 webList 创建成功\n";

        // 创建表格2：portList
        $createPortListTable = "
            CREATE TABLE IF NOT EXISTS portList (
                ip TEXT PRIMARY KEY NOT NULL,
                ports TEXT NOT NULL,
                sequence INTEGER NOT NULL
            );
        ";
        $db->exec($createPortListTable);
        echo "表格 portList 创建成功\n";

        // 创建表格3：serverConfig
        $createServerConfigTable = "
            CREATE TABLE IF NOT EXISTS serverConfig (
                theme TEXT NOT NULL
            );
        ";
        $db->exec($createServerConfigTable);
        $insertServerConfig="INSERT INTO 
        serverConfig(theme)
        VALUES('dark')";
        $db->exec($insertServerConfig);
        echo "表格 serverConfig 创建成功\n";
    } else {
        echo "数据库已经存在，无需创建。\n";
    }
} catch (PDOException $e) {
    echo "数据库操作出错: " . $e->getMessage();
}

<?php
/**
 * 右下角服务器描述信息
 * 用于显示主要的内容
 * $theme 主题可选项目：dark , white
 **/

class LayerAnchor{
    private $theme='dark';
    public function __construct($theme='dark'){
        $this->theme=$theme;
    }
    //输出html代码
    public function export(){
        $styles = $this->theme === 'dark' ? $this->darkStyles() : $this->whiteStyles();
        return <<<HTML
<style>$styles</style>
<div class="LayerAnchor">
    Power by hub_url_bin (C) Minxi Wan Use PHP 7.3 & Workerman 4.1
</div>
HTML;
    }
    // 暗黑模式样式
    private function darkStyles() {
        return <<<CSS
        .LayerAnchor{
            width: auto;
            height: 20px;
            font-size: 12px;
            font-weight: 100;
            background: #121212;
            color: #ffffff;
            padding: 10px;
            position: fixed;
            right: 0;
            bottom: 0;
            z-index: 600;
        }
CSS;
    }
    // 白色模式样式
    private function whiteStyles() {
        return <<<CSS
        body {
            background-color: #ffffff;
            color: #000000;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: #f0f0f0;
            padding: 10px 20px;
        }
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        .menu {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 20px;
        }
        .menu li {
            display: inline;
        }
        .menu a {
            color: #000000;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .menu a:hover {
            background-color: #dddddd;
        }
CSS;
    }
}
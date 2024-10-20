<?php
/**
 * 头部的组件
 * 用于显示菜单等内容
 * $theme 菜单栏主题可选项目：dark , white
 **/
class LayerHead {
    private $theme = 'dark';
    public function __construct($theme = 'dark') {
        $this->theme = $theme;
    }
    // 输出html代码
    public function export() {
        $styles = $this->theme === 'dark' ? $this->darkStyles() : $this->whiteStyles();
        return <<<HTML
<style>$styles</style>
<header class="header">
   <nav class="navbar">
     <div class="logo">
       HUB&nbsp;&nbsp;hub_url_bin
     </div>
     <ul class="menu">
       <li><a href="#">Home</a></li>
       <li><a href="#">About</a></li>
       <li><a href="#">Services</a></li>
       <li><a href="#">Contact</a></li>
     </ul>
   </nav>
 </header>
HTML;
    }
    // 暗黑模式样式
    private function darkStyles() {
        return <<<CSS
        body {
            background-color: #121212;
            color: #ffffff;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .header {
            position: fixed;
            z-index: 500;
            top: 0;
            left: 0;
            background-color: #1f1f1f;
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
            color: #ffffff;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .menu a:hover {
            background-color: #444444;
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
            position: fixed;
            z-index: 500;
            top: 0;
            left: 0;
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

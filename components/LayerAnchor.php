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
            height: 15px;
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
         .LayerAnchor{
            width: auto;
            height: 15px;
            font-size: 12px;
            font-weight: 100;
            background: #fefefe;
            color: #121212;
            padding: 10px;
            position: fixed;
            right: 0;
            bottom: 0;
            z-index: 600;
        }
CSS;
    }
}
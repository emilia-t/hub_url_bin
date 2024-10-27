<?php
/**
 * 右下角描述信息
 * 用于显示主要的内容
 * $theme 主题可选项目：dark , white
 **/

class LayerAnchor{
    private $theme='dark';
    public function __construct($theme='dark'){
        $this->theme=$theme;
    }
    public function export(){
        $styles=$this->Styles();
        $scripts=$this->Scripts();
        return <<<HTML
<style>
    $styles
</style>
<div id="LayerAnchor" class="LayerAnchor-white">
    Power by hub_url_bin (C) Minxi Wan
</div>
<script>
    $scripts
</script>
HTML;
    }
    private function Styles(){
        return <<<CSS
        #LayerAnchor{
            opacity: 0.2;
            width: auto;
            height: 15px;
            font-size: 12px;
            font-weight: 100;
            padding: 10px;
            position: fixed;
            right: 0;
            bottom: 0;
            z-index: 600;
            transition: 0.4s;
        }
        .LayerAnchor-dark{
            background: #121212;
            color: #ffffff;
        }
        .LayerAnchor-white{
            background: #fefefe;
            color: #121212;
        }
CSS;
    }
    private function Scripts(){
        $Scripts=file_get_contents("js/LayerAnchor.js");
        return $Scripts!==false?$Scripts:"";
    }
}
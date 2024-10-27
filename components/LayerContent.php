<?php
/**
 * 内容的组件
 * 用于显示主要的内容
 * $theme 主题可选项目：dark , white
 * $store
 **/
class LayerContent {
    private $theme = 'dark';
    public function __construct($theme = 'dark') {
        $this->theme = $theme;
    }
    // 输出 html 代码
    public function export(){
        $styles=$this->Styles();
        $scripts=$this->Scripts();
        $html=<<<HTML
        <style>
            $styles
        </style>
        <div class="LayerContentBox">
            <div class="LayerContent" id="LayerContent">
            
            </div>
        </div>
        <script>
            $scripts
        </script>
HTML;
        return $html;
    }
    private function Styles(){
        return <<<CSS
            body{
                background-color: #ffffff;
                color: #000000;
                font-family: Arial, sans-serif;
                height: 100%;
                width: 100%;
                margin: 0;
            }
            html{
                height: 100%;
                width: 100%;
                margin: 0;
            }
            .LayerContentAppItemV{
                width: 110px;
                height: 110px;
            }
            .LayerContentBox{
                width:100%;
                height:auto;
                display:flex;
                flex-direction:row;
                flex-wrap:wrap;
                justify-content:flex-start;
            }
            .LayerContent{
                padding: 100px 0px;
                width: 100%;
                height: auto;
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                justify-content: center;
            }
            .LayerContentAppItem{
                width: 90px;
                height: 90px;
                border-radius: 10px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                text-align: center;
                text-decoration: none;
                padding: 10px;
            }
            .LayerContentAppItem-dark{
                background-color: #000000;
                color: #ffffff;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            .LayerContentAppItem-white{
                color: #000000;
            }
            .LayerContentAppTitle{
                width: 100%;
                height: auto;
                max-height: 36px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .LayerContentAppItem img{
                width: 30px;
                height: 30px;
                margin-bottom: 10px;
            }
            .LayerContentAppItem span{
                font-size: 14px;
                word-wrap: break-word;
            }
CSS;
    }
    private function Scripts(){
        $Scripts=file_get_contents("js/LayerContent.js");
        return $Scripts!==false?$Scripts:"";
    }
}

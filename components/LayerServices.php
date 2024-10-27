<?php
/**
 * 主机和开放端口列表
 * $theme 主题可选项目：dark , white
 **/
class LayerServices
{
    private $theme;

    public function __construct($theme = 'dark') {
        $this->theme = $theme;
    }

    public function export() {
        $styles=$this->Styles();
        $scripts=$this->Scripts();
        return <<<HTML
        <style>
            $styles
        </style>
        <div class="LayerServices-dark" id="LayerServices">
            <div class="LayerServicesApp" id="LayerServicesApp">
                <button class="LayerServicesBtn" id="LayerServicesBtn">
                    <svg width="17" height="17" viewBox="0 0 100 100">
                        <polygon points="60,20 20,50 60,80"/>
                    </svg>
                </button>
                <div id="LayerServicesList">
                
                </div>
            </div>
        </div>
        <script>
            $scripts
        </script>
HTML;
    }

    private function Styles(){
        return <<<CSS
            .LayerServices-dark{
                background: #333333;
                color: #ffffff;
            }
            .LayerServices-white{
                background: #ffffff;
                color: #333333;
            }
            .LayerServicesApp{
                position: fixed;
                top: 100px;
                right: -300px;
                width: 300px;
                border-radius: 10px;
                padding: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                transition: right 0.5s ease;
                font-family: Arial, sans-serif;
            }
            .LayerServices-dark .LayerServicesApp{
                background-color: #333;
                color: #fff;
            }
            .LayerServices-white .LayerServicesApp{
                background-color: #f9f9f9;
                color: #333;
            }
            .LayerServicesApp.visible{
                right: 10px;
            }
            .LayerServicesBtn{
                position: absolute;
                left: -40px;
                top: 20px;
                border: none;
                padding: 5px 7px;
                cursor: pointer;
                border-radius: 5px;
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
            }
            .LayerServices-dark .LayerServicesBtn{
                background-color: #282828;
                color: #ffffff;
            }
            .LayerServices-white .LayerServicesBtn{
                background-color: #f9f9f9;
                color: #333;
            }
            .LayerServicesApp .LayerServicesItem{
                margin-bottom: 15px;
                display: flex;
                align-items: center;
            }
            .LayerServicesBoxA{
                width: 45px;
            }
            .LayerServicesBoxB{
                width: calc(100% - 45px);
            }
            .LayerServicesBoxA svg{
                margin-right: 10px;
                fill: currentColor;
                width: 24px;
                height: 24px;
            }
            .LayerServicesPorts{
                width: 100%;
                height: auto;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
            }
            .LayerServicesPorts a{
                margin: 4px;
                color: inherit;
                text-decoration: none;
                padding: 3px 5px;
                border-radius: 3px;
                transition: background-color 0.3s ease;
            }
            .LayerServices-dark .LayerServicesPorts a{
                background-color: rgba(0, 0, 0, 0.1);
            }
            .LayerServices-white .LayerServicesPorts a{
                background-color: rgba(175, 175, 175, 0.1);
            }
            .LayerServices-dark .LayerServicesPorts a:hover{
                background-color: rgba(255, 255, 255, 0.3);
            }
            .LayerServices-white .LayerServicesPorts a:hover{
                background-color: rgba(0, 0, 0, 0.3);
            }
CSS;
    }

    private function Scripts(){
        $Scripts=file_get_contents("js/LayerServices.js");
        return $Scripts!==false?$Scripts:"";
    }
}

<?php
/**
 * Class LayerVoice
 *  语音通信组件
 */

class LayerVoice{
    private $theme;

    public function __construct($theme = 'dark'){
        $this->theme = $theme;
    }

    public function export(){
        $styles=$this->Styles();
        $scripts=$this->Scripts();
        return <<<HTML
        <style>
            $styles
        </style>
        <div class="LayerVoiceContainer" id="LayerVoiceContainer">
            <audio id="LayerVoiceAudioA" autoplay />
        </div>
        <div class="LayerVoiceControl">
                <svg id="LayerVoiceOpen" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" width="200" height="200">
                    <path d="M512 42.666667a213.333333 213.333333 0 0 1 213.333333 213.333333v170.666667a213.333333 213.333333 0 1 1-426.666666 0V256a213.333333 213.333333 0 0 1 213.333333-213.333333zM130.346667 469.333333H216.32a298.752 298.752 0 0 0 591.274667 0h86.016A384.170667 384.170667 0 0 1 554.666667 808.32V981.333333h-85.333334v-173.013333A384.170667 384.170667 0 0 1 130.346667 469.333333z">
                    
                    </path>
                </svg>
                <svg id="LayerVoiceClose" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" width="200" height="200">
                    <path d="M700.8 761.130667A381.525333 381.525333 0 0 1 554.666667 808.32V981.333333h-85.333334v-173.013333A384.170667 384.170667 0 0 1 130.346667 469.333333H216.32a298.752 298.752 0 0 0 421.12 228.437334l-66.176-66.133334A213.333333 213.333333 0 0 1 298.666667 426.666667V358.997333L59.434667 119.808l60.373333-60.373333 844.757333 844.8-60.373333 60.330666-203.392-203.434666z m125.866667-114.304l-61.568-61.525334c21.717333-34.56 36.522667-73.813333 42.538666-115.968h86.016a381.866667 381.866667 0 0 1-66.986666 177.493334z m-124.16-124.117334l-374.613334-374.613333A213.333333 213.333333 0 0 1 725.333333 256l0.042667 170.666667a212.48 212.48 0 0 1-22.784 96.042666h-0.085333z">
                    
                    </path>
                </svg>
        </div>
        <script>
            $scripts
        </script>
HTML;
    }

    private function Styles(){
        return <<<CSS
        #LayerVoiceAudioA{
            display: none;
        }
        .LayerVoiceControl{
            position: fixed;
            left: 10px;
            bottom: 10px;
            z-index: 600;
            width: auto;
            height: auto;
        }
        .LayerVoiceControl svg{
            width: 50px;
            height: 50px;
            fill: currentColor;
        }
CSS;
    }

    private function Scripts(){
        $Scripts=file_get_contents("js/LayerVoice.js");
        return $Scripts!==false?$Scripts:"";
    }
}
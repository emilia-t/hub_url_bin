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
        <div class="LayerVoice" id="LayerVoice">
            
        </div>
        <script>
            $scripts
        </script>
HTML;
    }

    private function Styles(){
        return <<<CSS
            
CSS;
    }

    private function Scripts(){
        $Scripts=file_get_contents("js/LayerVoice.js");
        return $Scripts!==false?$Scripts:"";
    }
}
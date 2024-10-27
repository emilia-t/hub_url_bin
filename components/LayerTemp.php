<?php
//这是一个模板文件

class LayerTemp
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
        <div class="LayerTemp" id="LayerTemp">
            
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
        $Scripts=file_get_contents("js/LayerTemp.js");
        return $Scripts!==false?$Scripts:"";
    }
}
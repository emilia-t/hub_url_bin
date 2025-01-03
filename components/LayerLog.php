<?php

class LayerLog
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
        <div class="LayerLog" id="LayerLog">
            
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
        $Scripts=file_get_contents("js/LayerLog.js");
        return $Scripts!==false?$Scripts:"";
    }
}
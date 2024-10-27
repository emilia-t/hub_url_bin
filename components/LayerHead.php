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
    public function export(){
        $styles = $this->Styles();
        $jsScript=$this->Scripts();
        return <<<HTML
<style>
$styles
</style>
<header id="LayerHead" class="LayerHead-white">
   <nav class="LayerHeadNav">
     <div class="logo">
       HUB
     </div>
     <ul class="menu">
       <li><a class="LayerHeadA LayerHeadA-white" href="#">Home</a></li>
       <li><a class="LayerHeadA LayerHeadA-white" id="LayerHeadTheme">$this->theme</a></li>
     </ul>
   </nav>
 </header>
 <script>
$jsScript
</script>
HTML;
    }
    private function Scripts(){
        $Scripts=file_get_contents("js/LayerHead.js");
        return $Scripts!==false?$Scripts:"";
    }
    private function Styles() {
        return <<<CSS
        #LayerHead {
            width: calc(100% - 40px);
            position: fixed;
            z-index: 500;
            top: 0;
            left: 0;
            padding: 10px 20px;
        }
        .LayerHead-white {
            background-color: #f0f0f0;
            color: #000000;
        }
        .LayerHead-dark {
            background-color: #1f1f1f;
            color: #ffffff;
        }
        .LayerHeadNav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .LayerHeadNav .logo {
            font-size: 17px;
            font-weight: 600;
        }
        .LayerHeadNav .menu {
            list-style: none;
            padding: 0;
            display: flex;
            gap: 20px;
        }
        .LayerHeadNav .menu li {
            display: inline;
        }
        .LayerHeadA {
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .LayerHeadA-white {
            color: #000000;
        }
        .LayerHeadA-dark {
            color: #ffffff;
        }
        .LayerHeadA-hover-white {
            background-color: #dddddd;
        }
        .LayerHeadA-hover-dark {
            background-color: #444444;
        }
CSS;
    }
}

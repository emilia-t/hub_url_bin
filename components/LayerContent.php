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
    public function export($webList){
        echo "exportingContent\n";
        $backgroundColor = $this->theme === 'dark' ? '#333' : '#fff';// 根据主题选择不同的样式
        $textColor = $this->theme === 'dark' ? '#fff' : '#000';
        $html = <<<HTML
        <style>
            body {
                background-color: {$backgroundColor};
                color: {$textColor};
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .app-grid {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                justify-content: center;
            }
            .app-item {
                width: 120px;
                height: 120px;
                background-color: {$textColor};
                color: {$backgroundColor};
                border-radius: 10px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                text-align: center;
                text-decoration: none;
                padding: 10px;
            }
            .app-item img {
                width: 40px;
                height: 40px;
                margin-bottom: 10px;
            }
            .app-item span {
                font-size: 14px;
                word-wrap: break-word;
            }
        </style>
        <div class="app-grid">
HTML;
        echo "\ncontent class webList------->\n";
        print_r($webList);
        echo "\ncontent class webList<-------\n";
        foreach ($webList as $web) {
            $url = $web['url'];
            $title = htmlspecialchars($web['title'], ENT_QUOTES, 'UTF-8');
            $favicon = $url . '/favicon.ico'; // 使用 favicon
            $html .= <<<HTML
            <a href="{$url}" class="app-item" target="_blank">
                <img src="{$favicon}" alt="{$title} favicon" onerror="this.src='default-icon.png'"/>
                <span>{$title}</span>
            </a>
HTML;
        }
        $html .= '</div>';
        return $html;
    }
}

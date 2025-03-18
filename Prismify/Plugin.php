<?php

/**
 * 基于 Prism.js 的代码高亮插件，支持行高亮、行号、显示语言、工具栏、复制按钮和树形视图功能。
 * 
 * @package Prismify
 * @author Claude 3.7 & hqoq
 * @version 1.0.8
 * @link https://github.com/hqoq/Prismify
 */

class Prismify_Plugin implements Typecho_Plugin_Interface
{
    /**
     * 激活插件方法，做一些初始化工作
     * 
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->header = array(__CLASS__, 'header');
        Typecho_Plugin::factory('Widget_Archive')->footer = array(__CLASS__, 'footer');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array(__CLASS__, 'parse');
        Typecho_Plugin::factory('Widget_Abstract_Contents')->excerptEx = array(__CLASS__, 'parseExcerpt');
    }

    /**
     * 禁用插件方法，清理插件注册的钩子
     * 
     * @return void
     */
    public static function deactivate()
    {
        // Nothing to do
    }

    /**
     * 获取插件配置面板
     * 
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // 主题选择
        $themes = array(
            'default' => '默认',
            'coy' => 'Coy',
            'dark' => 'Dark',
            'funky' => 'Funky',
            'okaidia' => 'Okaidia',
            'solarizedlight' => 'Solarized Light',
            'tomorrow' => 'Tomorrow',
            'twilight' => 'Twilight'
        );

        $theme = new Typecho_Widget_Helper_Form_Element_Select(
            'theme',
            $themes,
            'default',
            _t('代码高亮主题'),
            _t('选择 Prism.js 的高亮主题')
        );
        $form->addInput($theme);

        // 行号显示
        $lineNumbers = new Typecho_Widget_Helper_Form_Element_Radio(
            'lineNumbers',
            array('1' => '启用', '0' => '禁用'),
            '1',
            _t('显示行号'),
            _t('是否在代码块中显示行号')
        );
        $form->addInput($lineNumbers);

        // 行高亮
        $lineHighlight = new Typecho_Widget_Helper_Form_Element_Radio(
            'lineHighlight',
            array('1' => '启用', '0' => '禁用'),
            '1',
            _t('行高亮'),
            _t('是否支持特定行高亮显示，使用 data-line="1,4,6-8" 属性')
        );
        $form->addInput($lineHighlight);

        // 显示语言
        $showLanguage = new Typecho_Widget_Helper_Form_Element_Radio(
            'showLanguage',
            array('1' => '启用', '0' => '禁用'),
            '1',
            _t('显示语言'),
            _t('是否在代码块右上角显示编程语言')
        );
        $form->addInput($showLanguage);

        // 工具栏
        $toolbar = new Typecho_Widget_Helper_Form_Element_Radio(
            'toolbar',
            array('1' => '启用', '0' => '禁用'),
            '1',
            _t('显示工具栏'),
            _t('是否显示代码块工具栏')
        );
        $form->addInput($toolbar);

        // 复制按钮
        $copyButton = new Typecho_Widget_Helper_Form_Element_Radio(
            'copyButton',
            array('1' => '启用', '0' => '禁用'),
            '1',
            _t('复制按钮'),
            _t('是否显示复制代码按钮')
        );
        $form->addInput($copyButton);

        // 树形视图
        $treeview = new Typecho_Widget_Helper_Form_Element_Radio(
            'treeview',
            array('1' => '启用', '0' => '禁用'),
            '1',
            _t('树形视图'),
            _t('是否启用树形视图支持')
        );
        $form->addInput($treeview);
    }

    /**
     * 个人用户的配置面板
     * 
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
        // 暂无个人用户配置
    }

    /**
     * 输出头部 CSS 和 JS
     * 
     * @param string $header
     * @return string
     */
    public static function header()
    {
        $options = Helper::options()->plugin('Prismify');
        $themeFile = $options->theme == 'default' ? 'prism.css' : 'prism-' . $options->theme . '.css';

        echo '<link rel="stylesheet" href="' . Helper::options()->pluginUrl . '/Prismify/assets/' . $themeFile . '">' . "\n";

        // 根据选项加载额外的 CSS
        if ($options->lineNumbers) {
            echo '<link rel="stylesheet" href="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/line-numbers/prism-line-numbers.css">' . "\n";
        }

        if ($options->lineHighlight) {
            echo '<link rel="stylesheet" href="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/line-highlight/prism-line-highlight.css">' . "\n";
        }

        // 使用原始的 toolbar.css
        if ($options->toolbar) {
            echo '<link rel="stylesheet" href="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/toolbar/prism-toolbar.css">' . "\n";
        }

        if ($options->treeview) {
            echo '<link rel="stylesheet" href="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/treeview/prism-treeview.css">' . "\n";
        }

        // 添加自定义 CSS 样式
        echo '<style>
            /* 确保工具栏始终显示 */
            div.code-toolbar > .toolbar {
                opacity: 1;
            }
            
            /* 强制统一语言标签和按钮样式 */
            div.code-toolbar > .toolbar .prism-show-language,
            div.code-toolbar > .toolbar .prism-show-language-label {
                margin: 0;
                padding: 0;
                background: none;
                box-shadow: none;
                text-shadow: none;
            }
            
            /* 为语言标签应用与 Copy 按钮完全相同的样式 */
            div.code-toolbar > .toolbar span.prism-show-language-label {
                color: #bbb;
                font-size: .8em;
                padding: 0 .5em;
                background: #f5f2f0;
                background: rgba(224, 224, 224, 0.2);
                box-shadow: 0 2px 0 0 rgba(0,0,0,0.2);
                border-radius: .5em;
                cursor: pointer;
                border-bottom: 1px solid #EEE;
            }
            
            div.code-toolbar > .toolbar span.prism-show-language-label:hover {
                color: inherit;
                text-decoration: none;
            }
            
            /* 修复行号溢出问题 */
            pre.line-numbers {
                position: relative;
                padding-left: 3.2em; /* 减小左侧内边距 */
                counter-reset: linenumber;
                white-space: pre-wrap;
                overflow: visible;
                margin: 0.5em 0;
            }
            
            .line-numbers .line-numbers-rows {
                position: absolute;
                pointer-events: none;
                top: 0;
                font-size: 95%; /* 稍微缩小行号字体大小 */
                left: -2.8em; /* 调整行号位置 */
                width: 2.5em; /* 减小行号区域宽度 */
                letter-spacing: -1px;
                border-right: 1px solid #999;
                user-select: none;
                margin-top: 0;
                padding-right: 0.2em; /* 为行号右侧添加一点内边距 */
            }
            
            .line-numbers-rows > span {
                display: block;
                counter-increment: linenumber;
            }
            
            .line-numbers-rows > span:before {
                content: counter(linenumber);
                color: #999;
                display: block;
                padding-right: 0.5em; /* 调整行号右侧内边距 */
                text-align: right;
            }
            
            /* 设置一致的字体系列和行高 */
            code[class*="language-"], 
            pre[class*="language-"],
            .line-numbers-rows,
            .line-numbers-rows > span,
            .line-numbers-rows > span:before {
                font-family: Consolas, Monaco, "Andale Mono", "Ubuntu Mono", monospace;
                line-height: 1.5;
            }
            
            /* 确保行号不会被截断 */
            .line-numbers-rows {
                display: block !important;
            }
        </style>' . "\n";
    }

    /**
     * 输出底部 JavaScript
     * 
     * @return void
     */
    public static function footer()
    {
        $options = Helper::options()->plugin('Prismify');

        // 输出主 Prism.js 脚本
        echo '<script src="' . Helper::options()->pluginUrl . '/Prismify/assets/prism.js"></script>' . "\n";

        // 根据选项输出额外的 JS
        if ($options->lineNumbers) {
            echo '<script src="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/line-numbers/prism-line-numbers.js"></script>' . "\n";
        }

        if ($options->lineHighlight) {
            echo '<script src="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/line-highlight/prism-line-highlight.js"></script>' . "\n";
        }

        // 工具栏插件先加载
        if ($options->toolbar) {
            echo '<script src="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/toolbar/prism-toolbar.js"></script>' . "\n";
        }

        // 其他依赖工具栏的插件，按顺序加载
        if ($options->showLanguage) {
            echo '<script src="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/show-language/prism-show-language.js"></script>' . "\n";
        }

        if ($options->copyButton) {
            echo '<script src="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/copy-to-clipboard/prism-copy-to-clipboard.js"></script>' . "\n";
        }

        if ($options->treeview) {
            echo '<script src="' . Helper::options()->pluginUrl . '/Prismify/assets/plugins/treeview/prism-treeview.js"></script>' . "\n";
        }

        // 添加初始化脚本，确保 Prism 正确处理所有代码块
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                // 重新初始化 Prism，确保所有功能正常工作
                if (typeof Prism !== "undefined") {
                    // 强制为所有代码块应用所需的类
                    document.querySelectorAll("pre > code").forEach(function(codeBlock) {
                        // 获取语言
                        var parentPre = codeBlock.parentNode;
                        var language = "none";
                        var classes = codeBlock.className.split(" ");
                        
                        for (var i = 0; i < classes.length; i++) {
                            if (classes[i].indexOf("language-") === 0) {
                                language = classes[i].substring(9);
                                break;
                            }
                        }
                        
                        // 确保行号功能正常
                        if (' . ($options->lineNumbers ? 'true' : 'false') . ' && !parentPre.className.includes("line-numbers")) {
                            parentPre.className += " line-numbers";
                        }
                        
                        // 确保代码块被工具栏包装
                        if ((' . ($options->toolbar ? 'true' : 'false') . ' || ' . ($options->showLanguage ? 'true' : 'false') . ' || ' . ($options->copyButton ? 'true' : 'false') . ') && 
                            !parentPre.parentNode.classList.contains("code-toolbar")) {
                            
                            var wrapper = document.createElement("div");
                            wrapper.className = "code-toolbar";
                            parentPre.parentNode.insertBefore(wrapper, parentPre);
                            wrapper.appendChild(parentPre);
                        }
                    });
                    
                    // 重新高亮代码
                    Prism.highlightAll();
                    
                    // 调整语言标签的位置并应用正确的样式
                    setTimeout(function() {
                        // 确保语言标签出现在复制按钮之前
                        document.querySelectorAll(".toolbar").forEach(function(toolbar) {
                            var langItem = toolbar.querySelector(".prism-show-language");
                            if (langItem) {
                                toolbar.insertBefore(langItem, toolbar.firstChild);
                                
                                // 应用与 Copy 按钮相同的样式
                                var label = langItem.querySelector(".prism-show-language-label");
                                if (label) {
                                    label.style.color = "#bbb";
                                    label.style.fontSize = ".8em";
                                    label.style.padding = "0 .5em";
                                    label.style.background = "rgba(224, 224, 224, 0.2)";
                                    label.style.boxShadow = "0 2px 0 0 rgba(0,0,0,0.2)";
                                    label.style.borderRadius = ".5em";
                                    label.style.cursor = "pointer";
                                    label.style.borderBottom = "1px solid #EEE";
                                }
                            }
                        });
                    }, 200);
                }
            });
        </script>' . "\n";
    }

    /**
     * 解析文章内容
     * 
     * @param string $content
     * @param Widget_Abstract_Contents $obj
     * @return string
     */
    public static function parse($content, $obj)
    {
        $options = Helper::options()->plugin('Prismify');
        $content = self::parseCode($content, $options);
        return $content;
    }

    /**
     * 解析文章摘要
     * 
     * @param string $content
     * @param Widget_Abstract_Contents $obj
     * @return string
     */
    public static function parseExcerpt($content, $obj)
    {
        // 摘要中不进行代码高亮处理
        return $content;
    }

    /**
     * 解析代码块，添加 Prism.js 所需的类和属性
     * 
     * @param string $content
     * @param object $options
     * @return string
     */
    private static function parseCode($content, $options)
    {
        // 匹配 Markdown 中的代码块
        $pattern = '/```([a-zA-Z0-9_-]+)?\s*\n(.*?)```/s';

        // 替换为 Prism.js 兼容的代码块
        $content = preg_replace_callback($pattern, function ($matches) use ($options) {
            $language = empty($matches[1]) ? 'none' : $matches[1];
            $code = htmlspecialchars(trim($matches[2]));

            // 构建类名列表，确保包含所需的类
            $classes = ['language-' . $language];

            // 如果启用了行号，添加 line-numbers 类
            if ($options->lineNumbers) {
                $classes[] = 'line-numbers';
            }

            // 构建 HTML，确保代码块被正确包装
            $classString = implode(' ', $classes);

            // 构建一个完整的代码块，包括工具栏支持
            if ($options->toolbar || $options->showLanguage || $options->copyButton) {
                return '<div class="code-toolbar"><pre class="' . $classString . '"><code class="language-' . $language . '">' . $code . '</code></pre></div>';
            } else {
                return '<pre class="' . $classString . '"><code class="language-' . $language . '">' . $code . '</code></pre>';
            }
        }, $content);

        return $content;
    }
}

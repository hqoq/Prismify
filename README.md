# Prismify 插件

基于 Prism.js 的 Typecho 代码高亮插件，支持多种主题和功能特性。

## 功能特性

- 支持多种高亮主题：默认、Coy、Dark、Funky、Okaidia、Solarized Light、Tomorrow、Twilight
- 行号显示：在代码块左侧显示行号（bug: 不同 Prism 主题下还有显示 bug 待修）
- 行高亮：支持特定行高亮显示
- 显示语言：在代码块右上角显示编程语言（bug: 只有单行代码显示时右侧的语言和复制按钮位置显示不齐）
- 工具栏：提供代码块工具栏
- 复制按钮：一键复制代码内容
- 树形视图：支持树形结构代码的显示

## 安装说明

1. 下载仓库文件并解压
2. 将文件夹 `Prismify` 上传到 Typecho 插件目录：`/usr/plugins/`
3. 登录控制台，在"插件管理"中找到 `Prismify`，点击"启用"
4. 根据需要进行相关设置

## 使用方法

安装并启用插件后，使用 Markdown 语法书写代码块即可自动高亮：

```markdown
```php
<?php
echo "Hello, World!";
?>
```
```

### 行高亮使用方法

在代码块上添加 `data-line` 属性指定需要高亮的行：

```html
<pre class="line-numbers" data-line="1,4,6-8"><code class="language-php">
<?php
echo "这是第一行，会被高亮";
echo "这是第二行，不会被高亮";
echo "这是第三行，不会被高亮";
echo "这是第四行，会被高亮";
echo "这是第五行，不会被高亮";
echo "这是第六行，会被高亮";
echo "这是第七行，会被高亮";
echo "这是第八行，会被高亮";
?>
</code></pre>
```

### 树形视图使用方法

使用特定的 HTML 结构可以创建树形视图：

```html
<pre class="language-treeview">
<code>
📦 project
 ┣ 📂 src
 ┃ ┣ 📜 index.js
 ┃ ┗ 📜 util.js
 ┣ 📂 tests
 ┃ ┗ 📜 test.js
 ┣ 📜 .gitignore
 ┗ 📜 package.json
</code>
</pre>
```

## 配置说明

在 Typecho 后台插件管理页面中，可以对本插件进行以下配置：

- 选择高亮主题
- 开启/关闭行号显示
- 开启/关闭行高亮功能
- 开启/关闭语言显示
- 开启/关闭工具栏
- 开启/关闭复制按钮
- 开启/关闭树形视图支持

## 版本历史

- v1.0.8 - 初始版本发布

## 许可证

本插件采用 MIT 许可证发布。

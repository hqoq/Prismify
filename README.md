# Typecho 博客代码高亮插件 - Prismify

一个 Typecho 代码高亮插件，基于 PrismJS，支持官方主题，添加了行号显示，语言显示，复制按钮和树形结构显示插件。

## 功能特性

- 支持官方高亮主题：
    - Default, Dark, Funky, Okaidia, Twilight, Coy, Solarized Light, Tomorrow Night
- 行号显示：在代码块左侧显示行号
- 行高亮：支持特定行高亮显示
- 显示语言：在代码块右上角显示编程语言
- 复制按钮：一键复制代码内容
- 树形视图：支持树形结构代码的显示

## to be fixed

- 启用 `行号显示` 时，代码显示会溢出代码框 (除了 `Coy` 主题)
- 只有一行代码时右侧的语言和复制按钮位置显示超出代码框
- 左边的行号显示和代码略微没对齐 (除了 `Coy` 主题)
- `行高亮` 功能目前解析和显示不正常

## 安装说明

1. 下载仓库文件并解压
2. 将文件夹 `Prismify` 上传到 Typecho 插件目录：`/usr/plugins/`
3. 登录控制台，在 `插件管理` 中找到 `Prismify` 并启用
4. 根据需要进行相关设置

## 配置说明

在 Typecho 后台插件管理页面中，可以对本插件进行以下配置：

- 选择高亮主题
- 开启/关闭行号显示
- 开启/关闭行高亮功能
- 开启/关闭语言显示
- 开启/关闭复制按钮
- 开启/关闭树形视图支持

## 使用方法

启用插件后，使用 Markdown 语法书写代码块即可自动高亮：

```php
<?php
echo "Hello, World!";
?>
```

### 行高亮使用方法

bug 待修复

### 树形视图使用方法

> You may use `tree -F` to get a compatible text structure.
> 
> 译：使用 `tree -F` 来获取兼容的文本结构。

在代码块用 \```treeview 可以显示带图标的树形图：

```treeview
./
├── folder1/
├── folder2/
│   ├── file1.txt
│   └── file2.pdf
├── file3.jpg
├── file4.mp4
└── file5.mp3
```

## 版本历史

- v1.0.8 - 初始版本发布

## 许可证

本插件采用 MIT 许可证发布。

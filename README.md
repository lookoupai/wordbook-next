# Wordbook Next

一个面向文档站的现代经典 WordPress 主题。

目标不是复刻旧版 `wordbook` 的实现，而是保留它“左侧目录 + 右侧正文 + 文档阅读体验”的信息架构，同时提升对新版 WordPress、移动端和区块内容的兼容性。

## 当前状态

- 已建立本地 Git 仓库
- 已推送到 GitHub `main`
- 当前目录即实际开发目录

## 主题特性

- 左侧文档目录导航
- 顶部阅读控制
- 支持亮色 / 护眼 / 夜间阅读主题
- 文档上一篇 / 下一篇导航
- 首页封面化展示
- 搜索结果页强化
- 移动端抽屉式侧边栏
- `theme.json` 驱动的区块内容样式兼容

## 菜单位置

主题注册了以下菜单位置：

- `docs`
  文档目录主菜单，优先使用这个位置
- `main`
  旧站兼容目录位置，`docs` 未配置时回退到这里
- `utility`
  功能菜单，例如登录、关于、外链
- `footer`
  页脚导航

## 目录结构

```text
.
├── 404.php
├── archive.php
├── assets/
│   ├── css/
│   │   ├── editor.css
│   │   └── main.css
│   └── js/
│       └── theme.js
├── category.php
├── comments.php
├── footer.php
├── front-page.php
├── functions.php
├── header.php
├── home.php
├── inc/
│   ├── assets.php
│   ├── compat.php
│   ├── customizer.php
│   ├── navigation.php
│   ├── setup.php
│   └── template-tags.php
├── index.php
├── page.php
├── search.php
├── single.php
├── style.css
├── template-parts/
│   └── content/
│       ├── content-front-page.php
│       ├── content-list.php
│       ├── content-none.php
│       ├── content-search.php
│       └── content-singular.php
└── theme.json
```

## 关键实现说明

### 1. 旧主题兼容

兼容层在 `inc/compat.php`：

- 回退读取旧版权配置 `footbanquan`
- 回退读取旧 logo 配置 `header_logo_image`

### 2. 文档导航

导航相关逻辑在 `inc/navigation.php`：

- 目录菜单位置选择
- 文档菜单项过滤
- 上一篇 / 下一篇文档计算
- 首页快速入口 / 最近更新的数据源

### 3. 首页

首页模板在 `template-parts/content/content-front-page.php`，主要包含：

- Hero 封面区
- 站点说明
- 快速入口
- 最近更新

### 4. 搜索页

搜索页已做成文档检索风格：

- 搜索头部
- 结果计数
- 关键词高亮
- 结果路径展示
- 无结果时推荐入口与最近更新

## 开发说明

### 本地检查

PHP 语法检查：

```bash
find . -name '*.php' -type f -print0 | xargs -0 -n 1 php -l
```

JS 检查：

```bash
node --check assets/js/theme.js
```

Git 补丁检查：

```bash
git diff --check
```

## 缓存说明

这个站点之前存在 PHP OPCache 与页面缓存干扰开发的问题。

当前建议开发环境使用：

- `opcache.validate_timestamps=1`
- `opcache.revalidate_freq=0`

否则修改 PHP 模板后，前台可能不会立即生效。

## 后续可继续优化的方向

- 首页进一步封面化
- 文档目录折叠状态持久化
- 搜索结果按内容类型分组
- 增加 `screenshot.png`
- 最终收敛目录名，替换掉预览目录命名


# PHP工具包

[![Php Version](https://img.shields.io/badge/php-%3E=7.1.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/toolkit/str-utils)

php的一些有用的基础工具库实现和收集整理。

> 本仓库是主仓库，开发后推送到各个组件仓库。如果只需要一个或部分工具，可以单独配置require

**1. 字符串工具**

常用的字符串操作帮助工具类库，以及一些 html，json 编解码， token，url 等工具类

- 独立包名 [toolkit/str-utils](https://github.com/php-toolkit/str-utils)
- 在本仓库的 [libs/str-utils](libs/str-utils)

**2. 数组工具**

常用的数组操作帮助工具类库

- 独立包名 [toolkit/arr-utils](https://github.com/php-toolkit/arr-utils)
- 在本仓库的 [libs/arr-utils](libs/arr-utils)

**3. 对象工具** 

常用的对象操作帮助工具类、traits库

- 独立包名 [toolkit/obj-utils](https://github.com/php-toolkit/obj-utils)
- 在本仓库的 [libs/obj-utils](libs/obj-utils)

**4. 系统工具** 

常用的系统操作帮助工具类库。系统环境信息，执行命令，简单的进程操作使用类（fork,run,stop,wait ...）等

- 独立包名 [toolkit/sys-utils](https://github.com/php-toolkit/sys-utils)
- 在本仓库的 [libs/sys-utils](libs/sys-utils) 

**5. php工具** 

常用的php操作帮助工具类库。php环境信息，数据打印，`.env`加载，简单的autoload类等

- 独立包名 [toolkit/php-utils](https://github.com/php-toolkit/php-utils)
- 在本仓库的 [libs/php-utils](libs/php-utils) 

**6. 文件系统工具** 

常用的文件系统操作帮助工具类库。文件查找，创建，判断，信息获取，内容读取等，目录的创建，权限，拷贝，删除等。

- 独立包名 [toolkit/file-utils](https://github.com/php-toolkit/file-utils)
- 在本仓库的 [libs/file-utils](libs/file-utils) 

**7. CLI工具** 

常用的php cli环境的帮助工具类库。cli下的内容输出，读取。丰富的颜色内容输出，cli下的php文件高亮，简单的光标操作。

- 独立包名 [toolkit/cli-utils](https://github.com/php-toolkit/cli-utils)
- 在本仓库的 [libs/cli-utils](libs/cli-utils) 

**8. 数据收集器** 

数据收集器 `Collection` 实现。可用于配置数据管理、数据收集、数据迭代等。

- 独立包名 [toolkit/collection](https://github.com/php-toolkit/collection)
- 在本仓库的 [libs/collection](libs/collection) 

**9. 简单的DI容器实现** 

简单的 `psr/container` 对象管理容器实现

- 独立包名 [toolkit/di](https://github.com/php-toolkit/di)
- 在本仓库的 [libs/di](libs/di) 

**10. 数据解析器** 

数据解析器。`json` `php` `swoole` `msgpack` 格式的数据解析的简单封装。

- 独立包名 [toolkit/data-parser](https://github.com/php-toolkit/data-parser)
- 在本仓库的 [libs/data-parser](libs/data-parser) 

**11. 额外的帮助类库**

额外的帮助类库。数据、日期、格式化等帮助类。 简单的 config,options,event,alias等traits收集整理

- 独立包名 [toolkit/helper-utils](https://github.com/php-toolkit/helper-utils)
- 在本仓库的 [libs/helper-utils](libs/helper-utils) 

## 安装

```bash
composer require toolkit/toolkit
```

## 文档

- classes docs https://php-toolkit.github.io/toolkit/classes-docs/master/

## 开发

```bash
composer install
php toolkit dev -h
```

### git subtree

git subtree usage example:

- add a lib repo

```bash
git subtree add --prefix=libs/php-utils https://github.com/php-toolkit/php-utils master --squash
```

- update a lib repo

```bash
git subtree pull --prefix=libs/php-utils https://github.com/php-toolkit/php-utils master --squash
```

- push a lib repo

```bash
git subtree push --prefix=libs/php-utils https://github.com/php-toolkit/php-utils master
```

## License

MIT

## 我的其他项目

- **`inhere/console`** 功能丰富的命令行应用，命令行工具库
  - git repo [github](https://github.com/inhere/php-console) [gitee](https://gitee.com/inhere/php-console)
- **`inhere/php-validate`** 一个简洁小巧且功能完善的php验证库。仅有几个文件，无依赖。
  - git repo [github](https://github.com/inhere/php-validate)  [gitee](https://gitee.com/inhere/php-validate)
- **`inhere/sroute`** 轻量且快速的路由库
  - git repo [github](https://github.com/inhere/php-srouter)  [gitee](https://gitee.com/inhere/php-srouter)
- **`inhere/event-manager`** psr-14 的事件管理实现
  - git repo [github](https://github.com/inhere/php-event-manager)  [gitee](https://gitee.com/inhere/php-event-manager)
- **`inhere/middleware`** psr-15 HTTP中间件的实现
  - git repo [github](https://github.com/inhere/php-middleware)  [gitee](https://gitee.com/inhere/php-middleware)


> 更多请查看我的 [github](https://github.com/inhere)

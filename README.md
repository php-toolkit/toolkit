# toolkit for php

php的一些有用的基础工具库实现。 

- 数据收集器(in `toolkit/libs`)
- 依赖注入容器(in `toolkit/libs`)
- 基础文件系统工具(in `toolkit/libs`)
- 各种帮助类库(in `toolkit/libs`)

## install

```bash
composer require toolkit/toolkit
```

## docs

- classes docs https://php-toolkit.github.io/toolkit/classes-docs/master/

## development

```bash
composer install
php toolkit dev -h
```

## git subtree

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

## license

MIT

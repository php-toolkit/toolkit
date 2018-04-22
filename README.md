# toolkit for php

## install

```bash
composer require toolkit/toolkit
```

## docs

- classes api https://php-toolkit.github.io/toolkit/classes-docs/master

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

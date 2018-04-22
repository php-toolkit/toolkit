# toolkit for php

## install

```bash
composer require toolkit/toolkit
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

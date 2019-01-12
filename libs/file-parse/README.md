# file parse

[![License](https://img.shields.io/packagist/l/php-toolkit/file-parse.svg?style=flat-square)](LICENSE)
[![Php Version](https://img.shields.io/badge/php-%3E=7.1.0-brightgreen.svg?maxAge=2592000)](https://packagist.org/packages/php-toolkit/file-parse)
[![Latest Stable Version](http://img.shields.io/packagist/v/php-toolkit/file-parse.svg)](https://packagist.org/packages/php-toolkit/file-parse)

Some useful file parse utils for the php.

`ini`, `json`, `yml` 格式的文件解析

- json 文件支持去除注释，即是有注释不会导致解析失败
- 支持特殊关键字 
  - `extend` 继承另一个文件的内容
  - `import` 导入另一个文件的内容
  - `reference` 参考另一个key的值 **todo**

例如在 yml 文件(其他格式的文件类似)可以这样：

```text
// will parse special keywords
extend: ../parent.yml
debug: true
db: import#../db.yml
cache:
  debug: reference#debug
```

## Install

```bash
composer require toolkit/file-parse
```

## License

MIT

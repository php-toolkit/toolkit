# 文件内容解析

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

## install

```bash
composer require toolkit/file-parse
```

## license

MIT

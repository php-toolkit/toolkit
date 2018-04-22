# Dependency injection container

## 注册服务

```php
public function set(string $id, mixed $definition, array $opts = []): Container
```

### 参数说明

#### 服务ID名

 string `$id` 服务组件注册id

#### 服务定义 
 
mixed(string|array|object|callback) `$definition` 服务实例对象|服务信息定义

- string:

```php
   $definition = className
```

- array:

```php    
   $definition = [
      // 1. 仅类名 参数($definition[0])则传入对应构造方法
      'class' => 'className',
      // 2. 类的静态方法,  参数($definition[0])则传入对应方法 className::staticMethod(args...)
      'class' => 'className::staticMethod',
      // 3. 类的动态方法,  参数($definition[0]) 则传入对应方法 (new className)->method(args...)
      'class' => 'className->method',
 
      // 设置参数方式, 没有key
      [
          arg1,arg2,arg3,...
      ]
 
      // 设置属性 ， // prop1 prop2 prop3 将会被收集 作为属性
      prop1 => value1,
      prop2 => value2,
      ... ...
      
      // 一些服务设置(别名,是否共享). 将会合并到最后一个参数中
      '_options' => [...] 
   ]
```

- object:

```php
   $definition = new xxClass();
```

- closure:

```php
   $definition = function($di){ 
        return object;
   };
```
 
#### 选项
 
- array `$opts` 选项

```php
  [
   'shared' => (bool), 是否共享,单例
   'locked' => (bool), 是否锁定服务
   'aliases' => (array), 别名
   'init' => (bool), 立即激活
  ]
```

## license

MIT

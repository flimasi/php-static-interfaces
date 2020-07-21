#PHP static interfaces

#### commom functions to rapidy develop webites

- Config  - BaseFile to config global variables

```php
// DATABASE
define("DATABASE_HOST", "127.0.0.1");
define("DATABASE_USER", "root");
define("DATABASE_PASSWORD", "root");
define("DATABASE_SCHEMA", "mysql");
define("DATABASE_PORT", "3306");
```

- Class Smtp - Interface Email

```php
class smtp{}
```


- Class Http - Interface Http Response

```php
class http{}
```

- Class SqlParser - Interface Database

```php
class sqlparser{}
```

- Class Util - Interface Common Helper

```php
class util{}
```
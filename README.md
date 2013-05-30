# hD.arr

This module contains adds extra functionality to Kohana containing arrays and configuration.

## Arrays

 - Place a value at a specific point in your array
 - Place or move a key with a value at a specific point in your array
 - Flatten an array (keys become paths)
 - Unset a value based on a path

## Config

 - Export a configuration set

##Instalation

### Place the files in your modules directory.

#### As a Git submodule:

```bash
git clone git://github.com/happyDemon/arr.git modules/arr
```
#### As a [Composer dependency](http://getcomposer.org)

```javascript
{
	"require": {
		"php": ">=5.4.0",
		"composer/installers": "*",
		"happyDemon/arr":"*"
	}
}
```

### Activate the module in `bootstrap.php`.

```php
<?php
Kohana::modules(array(
	...
	'arr' => MODPATH.'arr',
));
```

# Documentation

The code is mostly commented, a userguide is on its way
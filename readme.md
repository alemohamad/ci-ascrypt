# CodeIgniter Library: AS3 Encryption

**ci-ascrypt**

## About this library

This CodeIgniter's Library is used to encrypt text to send and/or receive from a Flash application.    
Its usage is recommended for CodeIgniter 2 or greater.  

You can use the Flash files that are in the folder **as3-files** to work with this library (thanks **zykbrun**!).

*Note:* This doesn't replace the CodeIgniter's native **Encryption Class**. This is a library I created for connect to a Flash app and have some security for it's data.

## Usage

### /application/config/ascrypt.php

Set a keyphrase for your project in the config file.

```php
$config['str_key']		= ''; // ASCrypt keyphrase
$config['debug']		= FALSE; // ASCrypt debug option
```

You can also set to TRUE the debug option. This option returns the received phrase, in case you want to read what value is passed to the method.

### Usage in a controller

```php
$this->load->library('ASCrypt');

$phrase = 'This is an example text.';

$encrypt = $this->ascrypt->encrypt($phrase);
$decrypt = $this->ascrypt->decrypt($encrypt);
```

![Ale Mohamad](http://codeigniter.alemohamad.com/images/logo2012am.png)
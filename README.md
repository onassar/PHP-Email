PHP Email
===

PHP-Email provides a class which exposes a simple API for defining a template
and rendering it with native PHP (including global/native variables).

It&#039;s not meant to send mail. For that, you can use an SMTP server or third
party (like AWS SES, or Postmark).

### Sample Template

``` html
<div style="background-color: red; width: 100px; height: 100px; text-align: center; line-height: 100px;">
    <?= ($message) ?> (<?= ($_SERVER['REMOTE_ADDR']) ?>)
</div>
```


### Sample Instantiation

``` php
<?php

    // load dependency
    require_once APP . '/vendors/PHP-Email/Email.class.php';
    
    // template and render
    $email = (new Email(APP . ('/includes/emails/hello.inc.php'));
    $message = $email->render(array(
        'message' => 'Hello World!'
    ));
    echo $message;
    exit(0);

```

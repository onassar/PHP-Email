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

### Sample Rendering

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

### Sample Mailing

``` php
<?php

    // load dependency
    require_once APP . '/vendors/PHP-Email/Email.class.php';
    
    // template and render
    $email = (new Email(APP . ('/includes/emails/hello.inc.php'));
    $message = $email->render(array(
        'message' => 'Hello World!'
    ));

    // postmark
    require_once APP . '/vendors/postmark/Postmark.php';
    define('POSTMARKAPP_API_KEY', '***');
    define('POSTMARKAPP_MAIL_FROM_ADDRESS', 'onassar@gmail.com');
    define('POSTMARKAPP_MAIL_FROM_NAME', 'Oliver Nassar');

    // send
    Mail_Postmark::compose()
    ->addTo('onassar@gmail.com', '')
    ->subject('Sample Email')
    ->messageHtml($message)
    ->send();

```

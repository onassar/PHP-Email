PHP Email
===

PHP-Email provides a class which exposes a simple API for defining a template
and rendering it with native PHP (including global/native variables).

It&#039;s not meant to send mail. For that, you can use an SMTP server or third
party (like [AWS SES](http://aws.amazon.com/ses/), or
[Postmark](http://postmarkapp.com/)).

### Sample Template

``` html
<div style="background-color: red; width: 100px; height: 100px; text-align: center; line-height: 50px;">
    <?= ($message) ?><br />(<?= ($_SERVER['REMOTE_ADDR']) ?>)
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
    $email = (new Email(APP . ('/includes/emails/sample.inc.php')));
    $message = $email->render(array(
        'message' => 'Hello World!'
    ));

    // postmark
    require_once APP . '/vendors/postmark-php/Postmark.php';
    define('POSTMARKAPP_API_KEY', '***');
    define('POSTMARKAPP_MAIL_FROM_ADDRESS', 'onassar@gmail.com');
    define('POSTMARKAPP_MAIL_FROM_NAME', 'Oliver Nassar');

    // send
    Mail_Postmark::compose()
    ->addTo('onassar@gmail.com', '')
    ->subject('Sample Email')
    ->messageHtml($message)
    ->send();
    exit(0);

```

### Sample Postmark Mailing

``` php
<?php

    // Postmark SDK loading
    require_once APP . '/vendors/postmark-php/Postmark.php';

    // Postmark constants
    define('POSTMARKAPP_API_KEY', 'fb2e27a8-aa1a-4c91-828c-0c06067e82f6');
    define('POSTMARKAPP_MAIL_FROM_ADDRESS', 'onassar@gmail.com');
    define('POSTMARKAPP_MAIL_FROM_NAME', 'Oliver Nassar');

    // load postmark mail class; assign template
    require_once APP . '/vendors/PHP-Email/PostmarkEmail.class.php';
    $email = (new PostmarkEmail(APP . '/vendors/PHP-Email/sample.inc.php'));

    // generate and send email
    $message = $email->render(array(
        'message' => 'Hello World!'
    ));
    $email->send(
        'onassar@gmail.com',
        'Hello World!',
        $message,
        'Sample Tag'
    );
    exit(0);

```

### Alternative Receiver Specification

``` php
<?php

    // no name specified
    $email->send(
        'onassar@gmail.com',
        'Hello World!',
        $message
    );

    // name and email specified
    $email->send(
        array(
            'address' => 'onassar@gmail.com',
            'name' => 'Oliver Nassar'
        ),
        'Hello World!',
        $message
    );

```

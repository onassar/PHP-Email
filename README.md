PHP Email
===

PHP-Email provides classes which expose a simple API for defining a template,
rendering it with native PHP (including global/native variables), and sending it
out (in the case of the `PostmarkEmail` class).

Use of the `Email` class alone will not provide email sending functionality. For
that, you can use the `PostmarkEmail` class, or another third party service
(eg. [AWS SES](http://aws.amazon.com/ses/)).

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
    $email = (new Email('sample.inc.php'));
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
    $email = (new Email('sample.inc.php'));
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
    ->subject('Hello World!')
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
    $email = (new PostmarkEmail('sample.inc.php'));

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

The above example works fully, however isn&#039;t essential (or important
really, imo).

PHP Email
===

PHP-Email provides a cross-platform emailing API which includes standard
emailing features (eg. setting the subject, body, attachments, etc).

Currently limited to Mailgun and Postmark.

### Sample Mailgun emailing

``` php
<?php

    // load dependencies
    require_once APP . '/vendors/source/mailgun-php/v1.7.1/autoload.php';
    require_once APP . '/vendors/submodules/PHP-Email/PlatformUtils.class.php';
    require_once APP . '/vendors/submodules/PHP-Email/MailgunUtils.class.php';
    require_once APP . '/vendors/submodules/PHP-Email/OutboundEmail.class.php';
    require_once APP . '/vendors/submodules/PHP-Email/MailgunEmail.class.php';
    
    // setup
    Email\MailgunUtils::addOutboundSignatures($signatures);
    Email\MailgunUtils::addRecipientWhitelistPatterns($patterns);
    Email\MailgunUtils::setAPIKey($apiKey);

    // send
    $email = new Email\MailgunEmail();
    $email->addToRecipient($address);
    $email->setSubject($subject);
    $email->setBody($body);
    $success = $email->send();
    $sendId = $email->getSendId();

```

### Sample Postmark Mailing

``` php
<?php

    // load dependencies
    require_once APP . '/vendors/source/postmark-php/v0.5/src/Postmark/Mail.php';
    require_once APP . '/vendors/submodules/PHP-Email/PlatformUtils.class.php';
    require_once APP . '/vendors/submodules/PHP-Email/PostmarkkUtils.class.php';
    require_once APP . '/vendors/submodules/PHP-Email/OutboundEmail.class.php';
    require_once APP . '/vendors/submodules/PHP-Email/PostmarkEmail.class.php';
    
    // setup
    Email\PostmarkUtils::addOutboundSignatures($signatures);
    Email\PostmarkUtils::addRecipientWhitelistPatterns($patterns);
    Email\PostmarkUtils::setAPIKey($apiKey);

    // send
    $email = new Email\PostmarkEmail();
    $email->addToRecipient($address);
    $email->setSubject($subject);
    $email->setBody($body);
    $success = $email->send();
    $sendId = $email->getSendId();

```

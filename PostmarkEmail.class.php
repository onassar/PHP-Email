<?php

    // load dependency
    require_once 'Email.class.php';

    // check for <Mail_Postmark> class dependency
    if (!class_exists('Postmark\Mail')) {
        throw new Exception('Postmark SDK not found.');
    }

    /**
     * PostmarkEmail
     * 
     * Extends the email class to provide email generation functionality along
     * with sending through the Postmark API. Expects the following three
     * constants to be set in order for mail to be sent properly:
     * - POSTMARKAPP_API_KEY
     * - POSTMARKAPP_MAIL_FROM_ADDRESS
     * - POSTMARKAPP_MAIL_FROM_NAME
     * 
     * @extends Email
     * @final
     * @source  https://github.com/onassar/PHP-Email
     * @see     https://github.com/Znarkus/postmark-php
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class PostmarkEmail extends Email
    {
        /**
         * send
         * 
         * Uses the Postmark service (and preset constants) to send a piece of
         * mail.
         * 
         * @notes  If sending out compious amounts of mail, static usage may be
         *         preferred, as it may be more memory-conscious. See 
         *         https://github.com/Znarkus/postmark-php for more information.
         * @access public
         * @param  string|array $to Email of the recipient, or
         *         associatively-keyed (with keys <email> and <name>) array
         *         with recipient details. In my experience, the email is
         *         enough
         * @param  string $subject (default: '(test)')
         * @param  string $message (default: '(test)') Ought to be HTML
         * @param  string|null $tag Optional string which "tags" the email for
         *         further breakdown within the Postmark dashboard
         * @param  boolean $sendAsHtml (default: true)
         * @param  false|array $from (default: false)
         * @param  boolean|array $attachments (default: false)
         * @return string|Exception
         */
        public function send(
            $to,
            $subject = '(test)',
            $message = '(test)',
            $tag = null,
            $sendAsHtml = true,
            $from = false,
            $attachments = false
        ) {
            // Instance
            $postmark = (new Postmark\Mail(POSTMARKAPP_API_KEY));

            // Recipient
            $email = $to;
            $name = $to;
            if (is_array($to)) {
                if (isset($to['email'])) {
                    $email = $to['email'];
                    $name = $to['name'];
                    $postmark->addTo($email, $name);
                } else {
                    foreach ($to as $address) {
                        $postmark->addTo($address, $address);
                    }
                }
            } else {
                $postmark->addTo($email, $name);
            }

            // Subject
            $postmark->subject($subject);

            // Sender
            if ($from !== false) {
                $postmark->from(
                    $from['email'],
                    $from['name']
                );
            } elseif ($this->_from !== array()) {
                $postmark->from(
                    $this->_from['email'],
                    $this->_from['name']
                );
            } else {
                $postmark->from(
                    POSTMARKAPP_MAIL_FROM_ADDRESS,
                    POSTMARKAPP_MAIL_FROM_NAME
                );
            }

            // Attachments
            if ($attachments !== false) {
                foreach ((array) $attachments as $attachment) {
                    $postmark->addAttachment($attachment);
                }
            }

            // Body
            if ($sendAsHtml === true) {
                $postmark->messageHtml($message);
            } else {
                $postmark->messagePlain($message);
            }

            // Email open tracking
            $postmark->trackOpen();

            // if a tag was specified (native to how Postmark organizes emails)
            if (!is_null($tag)) {
                $postmark->tag($tag);
            }

            // Send, passing back the messageId
            try {
                return $postmark->send(array(
                    'returnMessageId' => true
                ));
            } catch (Exception $exception) {
                return new Exception($exception->getMessage());
            }
        }
    }

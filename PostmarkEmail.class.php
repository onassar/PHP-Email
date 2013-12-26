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
         * @param  String|Array $to Email of the recipient, or
         *         associatively-keyed (with keys <address> and <name>) array
         *         with recipient details. In my experience, the address is
         *         enough
         * @param  String $subject (default: '(test)')
         * @param  String $message (default: '(test)') Ought to be HTML
         * @param  String|null $tag Optional string which "tags" the email for
         *         further breakdown within the Postmark dashboard
         * @param  false|array $from
         * @param  boolean $sendAsHtml (default: true)
         * @return string
         */
        public function send(
            $to,
            $subject = '(test)',
            $message = '(test)',
            $tag = null,
            $sendAsHtml = true,
            $from = false
        )
        {
            // to details
            $address = $to;
            $name = $to;
            if (is_array($to)) {
                $address = $to['address'];
                $name = $to['name'];
            }

            // generate
            $postmark = (new Postmark\Mail(POSTMARKAPP_API_KEY));
            $postmark->addTo($address, $name)
            ->subject($subject);
            if ($from === false) {
                $postmark->from(
                    POSTMARKAPP_MAIL_FROM_ADDRESS,
                    POSTMARKAPP_MAIL_FROM_NAME
                );
            } else {
                $postmark->from(
                    $from['email'],
                    $from['name']
                );
            }
            if ($sendAsHtml === true) {
                $postmark->messageHtml($message);
            } else {
                $postmark->messagePlain($message);
            }

            // if a from address was specified (via the constructor)
            if (!empty($this->_from)) {
                $postmark->from(
                    $this->_from['email'],
                    $this->_from['name']
                );
            }

            // if a tag was specified (native to how Postmark organizes emails)
            if (!is_null($tag)) {
                $postmark->tag($tag);
            }

            // Send, passing back the message Id
            return $postmark->send(array(
                'returnMessageId' => true
            ));
        }
    }

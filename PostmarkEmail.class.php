<?php

    // load dependency
    require_once 'Email.class.php';

    // check for <Mail_Postmark> class dependency
    if (!class_exists('Mail_Postmark')) {
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
         * 
         * 
         * @access public
         * @param  Array|String $to
         * @param  String $subject
         * @param  String $message
         * @param  String|null $tag
         * @return void
         */
        public function send($to, $subject, $message, $tag = null)
        {
            // to details
            $address = $to;
            $name = $to;
            if (is_array($to)) {
                $address = $to['address'];
                $name = $to['name'];
            }

            // generate
            $postmark = (new Mail_Postmark());
            $postmark->addTo($address, $name)
            ->subject($subject)
            ->messageHtml($message);

            // if a tag was specified (native to how Postmark organizes emails)
            if (!is_null($tag)) {
                $postmark->tag($tag);
            }

            // send
            $postmark->send();
        }
    }

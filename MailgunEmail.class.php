<?php

    // load dependency
    require_once 'Email.class.php';

    // check for <Mail_Postmark> class dependency
    if (!class_exists('Mailgun\Mailgun')) {
        throw new Exception('Mailgun SDK not found.');
    }

    /**
     * MailgunEmail
     * 
     * Extends the email class to provide email generation functionality along
     * with sending through the Mailgun API
     * 
     * @extends Email
     * @final
     * @source  https://github.com/onassar/PHP-Email
     * @see     https://github.com/mailgun/mailgun-php
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class MailgunEmail extends Email
    {
        /**
         * _apiKey
         * 
         * @var    string
         * @access protected
         */
        protected $_apiKey;

        /**
         * __construct
         * 
         * @access public
         * @param  string $apiKey
         * @param  array $from
         * @param  string $template (default: null) The path to the template
         *         file, containing markup mixed with standard PHP echos. The
         *         path specified here must be absolute.
         * @return void
         */
        public function __construct(
            $apiKey,
            $from,
            $template = null
        ) {
            $this->_apiKey = $apiKey;
            $this->_reference = (new Mailgun\Mailgun($this->_apiKey));
            parent::__construct($template, $from);
        }

        /**
         * send
         * 
         * Uses the Mailgun service to send a piece of mail
         * 
         * @access public
         * @param  string|array $to Email of the recipient, or
         *         associatively-keyed (with keys <address> and <name>) array
         *         with recipient details. In my experience, the address is
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
            // Recipient
            $data = array();
            $name = $to;
            $email = $to;
            if (is_array($to)) {
                if (isset($to['name'])) {
                    $name = $to['name'];
                    $email = $to['email'];
                    $data['to'] = ($name) . ' <'.  ($email) . '>';
                } else {
                    $data['to'] = array();
                    foreach ($to as $address) {
                        $data['to'][] = $address;
                    }
                }
            } else {
                $data['to'] = ($name) . ' <'.  ($email) . '>';
            }

            // Subject
            $data['subject'] = $subject;

            // Sender
            if ($from !== false) {
                $data['from'] = ($from['name']) . ' <'.  ($from['email']) . '>';
            } else {
                $data['from'] = ($this->_from['name']) . ' <'. 
                    ($this->_from['email']) . '>';
            }

            // Attachments
            $postFiles = array();
            if ($attachments !== false) {
                $postFiles['attachment'] = array();
                foreach ((array) $attachments as $attachment) {
                    array_push($postFiles['attachment'], $attachment);
                }
            }

            // Body
            if ($sendAsHtml === true) {
                $data['html'] = $message;
            } else {
                $data['text'] = $message;
            }

            // Open trakcing
            $data['o:tracking'] = true;

            // Send
            try {
                $response = $this->_reference->sendMessage(
                    $this->_from['domain'],
                    $data,
                    $postFiles
                );
                if ((int) $response->http_response_code === 200) {
                    return $response->http_response_body->id;
                }
                return false;
            } catch (Exception $exception) {
                return new Exception($exception->getMessage());
            }
        }
    }

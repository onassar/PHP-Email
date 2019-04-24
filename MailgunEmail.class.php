<?php

    // load dependency
    require_once 'Email.class.php';

    // check for <Mailgun> class dependency
    if (class_exists('Mailgun\Mailgun') === false) {
        throw new Exception('Mailgun SDK not found.');
    }

    /**
     * MailgunEmail
     * 
     * Extends the email class to provide email generation functionality along
     * with sending through the Mailgun API
     * 
     * @final
     * @extends Email
     * @link    https://github.com/onassar/PHP-Email
     * @link    https://github.com/mailgun/mailgun-php
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class MailgunEmail extends Email
    {
        /**
         * _reference
         * 
         * @access  protected
         * @var     Mailgun\Mailgun
         */
        protected $_reference;

        /**
         * __construct
         * 
         * @access  public
         * @param   string $apiKey
         * @param   string $template (default: null) The path to the template
         *          file, containing markup mixed with standard PHP echos. The
         *          path specified here must be absolute.
         * @return  void
         */
        public function __construct($apiKey, $template = null)
        {
            $this->_reference = new Mailgun\Mailgun($apiKey);
            parent::__construct($template);
        }

        /**
         * send
         * 
         * Uses the Mailgun service to send a piece of mail
         * 
         * @access  public
         * @param   string|array $to Email of the recipient, or
         *          associatively-keyed (with keys <address> and <name>) array
         *          with recipient details. In my experience, the address is
         *          enough
         * @param   string $subject (default: '(test)')
         * @param   string $message (default: '(test)') Ought to be HTML
         * @param   string|null $tag Optional string which "tags" the email for
         *          further breakdown within the Mailgun dashboard
         * @param   bool $sendAsHtml (default: true)
         * @param   false|array $from (default: false)
         * @param   bool|array $attachments (default: false)
         * @param   bool|string $account (default: false)
         * @param   bool|string $signature (default: false)
         * @param   bool $track (default: true)
         * @return  string|Exception
         */
        public function send(
            $to,
            $subject = '(test)',
            $message = '(test)',
            $tag = null,
            $sendAsHtml = true,
            $from = false,
            $attachments = false,
            $account = false,
            $signature = false,
            $track = true
        ) {
            // Data to pass to SDK
            $data = array();

            // Recipient
            $name = $to;
            $email = $to;
            if (is_array($to) === true) {
                if (isset($to['name']) === true) {
                    $name = $to['name'];
                    $email = $to['email'];
                    $data['to'] = ($name) . ' <'.  ($email) . '>';
                } else {
                    $data['to'] = array();
                    foreach ($to as $address) {
                        if (isset($address['name']) === true) {
                            $name = $address['name'];
                            $email = $address['email'];
                            $data['to'][] = ($name) . ' <'.  ($email) . '>';
                        } else {
                            $data['to'][] = ($address) . ' <'.  ($address) .
                                '>';
                        }
                    }
                }
            } else {
                $data['to'] = ($name) . ' <'.  ($email) . '>';
            }

            // Subject
            $data['subject'] = $subject;

            // Signature lookup
            $account = ($account === false ? 'default' : $account);
            $signature = ($signature === false ? 'default' : $signature);
            $config = \Plugin\Config::retrieve('TurtlePHP-EmailerPlugin');
            $signature = $config['mailgun']['accounts'][$account]['signatures'][$signature];

            // Sender
            $email = $signature['email'];
            $name = $signature['name'];
            if ($from !== false) {
                if (isset($from['email'])) {
                    $email = $from['email'];
                }
                if (isset($from['name'])) {
                    $name = $from['name'];
                }
            }
            $data['from'] = ($name) . ' <'.  ($email) . '>';

            // Attachments
            $postFiles = array();
            if ($attachments !== false) {
                $postFiles['attachment'] = array();
                foreach ((array) $attachments as $attachment) {
                    if (is_array($attachment) === true) {
                        array_push(
                            $postFiles['attachment'],
                            array(
                                'filePath' => $attachment['path'],
                                'remoteName' => $attachment['name']
                            )
                        );
                    } else {
                        array_push($postFiles['attachment'], $attachment);
                    }
                }
            }

            // Body
            if ($sendAsHtml === true) {
                $data['html'] = $message;
            } else {
                $data['text'] = $message;
            }

            // Open tracking
            $data['o:tracking'] = $track;

            // Tagging
            if (is_null($tag) === false) {
                $data['o:tag'] = array($tag);
            }

            // Send
            try {
                $response = $this->_reference->sendMessage(
                    $signature['domain'],
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

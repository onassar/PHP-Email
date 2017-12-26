<?php

    // load dependency
    require_once 'Email.class.php';

    // check for <Mail_Postmark> class dependency
    if (class_exists('Postmark\Mail') === false) {
        throw new Exception('Postmark SDK not found.');
    }

    /**
     * PostmarkEmail
     * 
     * Extends the email class to provide email generation functionality along
     * with sending through the Postmark API
     * 
     * @final
     * @extends Email
     * @link    https://github.com/onassar/PHP-Email
     * @link    https://github.com/Znarkus/postmark-php
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class PostmarkEmail extends Email
    {
        /**
         * _reference
         * 
         * @var     Postmark\Mail
         * @access  protected
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
            $this->_reference = new Postmark\Mail($apiKey);
            parent::__construct($template);
        }

        /**
         * send
         * 
         * Uses the Postmark service (and preset constants) to send a piece of
         * mail.
         * 
         * @note    If sending out compious amounts of mail, static usage may be
         *          preferred, as it may be more memory-conscious. See 
         *          https://github.com/Znarkus/postmark-php for more
         *          information.
         * @access  public
         * @param   string|array $to Email of the recipient, or
         *          associatively-keyed (with keys <email> and <name>) array
         *          with recipient details. In my experience, the email is
         *          enough
         * @param   string $subject (default: '(test)')
         * @param   string $message (default: '(test)') Ought to be HTML
         * @param   string|null $tag Optional string which "tags" the email for
         *          further breakdown within the Postmark dashboard
         * @param   boolean $sendAsHtml (default: true)
         * @param   false|array $from (default: false)
         * @param   boolean|array $attachments (default: false)
         * @param   boolean|string $account (default: false)
         * @param   boolean|string $signature (default: false)
         * @param   boolean $track (default: true)
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
            // Cleanup
            $this->_reference->reset();

            // Recipient
            $email = $to;
            $name = $to;
            if (is_array($to) === true) {
                if (isset($to['email']) === true) {
                    $email = $to['email'];
                    $name = $to['name'];
                    $this->_reference->addTo($email, $name);
                } else {
                    foreach ($to as $address) {
                        if (isset($address['email']) === true) {
                            $this->_reference->addTo(
                                $address['email'],
                                $address['to']
                            );
                        } else {
                            $this->_reference->addTo($address, $address);
                        }
                    }
                }
            } else {
                $this->_reference->addTo($email, $name);
            }

            // Subject
            $this->_reference->subject($subject);

            // Signature lookup
            $account = ($account === false ? 'default' : $account);
            $signature = ($signature === false ? 'default' : $signature);
            $config = \Plugin\Config::retrieve('TurtlePHP-EmailerPlugin');
            $signature = $config['postmark']['accounts'][$account]['signatures'][$signature];

            // Sender
            $email = $signature['email'];
            $name = $signature['name'];
            if ($from !== false) {
                if (isset($from['email']) === true) {
                    $email = $from['email'];
                }
                if (isset($from['name']) === true) {
                    $name = $from['name'];
                }
            }
            $this->_reference->from($email, $name);

            // Attachments
            if ($attachments !== false) {
                foreach ((array) $attachments as $attachment) {
                    if (is_array($attachment) === true) {
                        $this->_reference->addAttachment(
                            $attachment['path'],
                            array(
                                'filenameAlias' => $attachment['name']
                            )
                        );
                    } else {
                        $this->_reference->addAttachment(
                            $attachment
                        );
                    }
                }
            }

            // Body
            if ($sendAsHtml === true) {
                $this->_reference->messageHtml($message);
            } else {
                $this->_reference->messagePlain($message);
            }

            // Open tracking
            if ($track === true) {
                $this->_reference->trackOpen();
            }

            // if a tag was specified (native to how Postmark organizes emails)
            if (is_null($tag) === false) {
                $this->_reference->tag($tag);
            }

            // Send, passing back the messageId
            try {
                return $this->_reference->send(array(
                    'returnMessageId' => true
                ));
            } catch (Exception $exception) {
                return new Exception($exception->getMessage());
            }
        }
    }

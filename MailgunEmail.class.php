<?php

    // Namespace
    namespace Email;

    /**
     * MailgunEmail
     * 
     * @final
     * @extends OutboundEmail
     * @link    https://github.com/onassar/PHP-Email
     * @link    https://github.com/mailgun/mailgun-php
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class MailgunEmail extends OutboundEmail
    {
        /**
         * _clientSendAttachments
         * 
         * @access  protected
         * @var     array (default: array())
         */
        protected $_clientSendAttachments = array();

        /**
         * _clientSendData
         * 
         * @access  protected
         * @var     array (default: array())
         */
        protected $_clientSendData = array();

        /**
         * __construct
         * 
         * @access  public
         * @return  void
         */
        public function __construct()
        {
            parent::__construct();
        }

        /**
         * _attemptClientSend
         * 
         * @access  protected
         * @return  bool
         */
        protected function _attemptClientSend(): bool
        {
            $client = $this->_client;
            $domain = $this->_getOutboundSignatureDomain();
            $data = $this->_clientSendData;
            $postFiles = $this->_clientSendAttachments;
            $args = array($domain, $data, $postFiles);
            try {
                $response = $client->sendMessage(... $args);
                $httpResponseCode = $response->http_response_code;
                $httpResponseCode = (int) $httpResponseCode;
                if ($httpResponseCode === 200) {
                    $this->_sendId = $response->http_response_body->id;
                    return true;
                }
                return false;
            } catch (\Exception $exception) {
                $this->_lastException = $exception;
                return false;
            }
        }

        /**
         * _buildClient
         * 
         * @access  protected
         * @return  void
         */
        protected function _buildClient(): void
        {
            $apiKey = MailgunUtils::getAPIKey();
            $client = new \Mailgun\Mailgun($apiKey);
            $this->_client = $client;
        }

        /**
         * _checkForDependencies
         * 
         * @throws  \Exception
         * @access  protected
         * @return  bool
         */
        protected function _checkForDependencies(): bool
        {
            if (class_exists('Mailgun\Mailgun') === true) {
                return true;
            }
            $msg = 'Mailgun SDK not found.';
            throw new \Exception($msg);
        }

        /**
         * _getNormalizedEmailAddress
         * 
         * @access  protected
         * @param   array $email
         * @return  string
         */
        protected function _getNormalizedEmailAddress(array $email): string
        {
            $address = $email['address'];
            $name = $email['name'] ?? $address;
            $normalizedEmailAddress = ($name) . ' <' . ($address) . '>';
            return $normalizedEmailAddress;
        }

        /**
         * _getOutboundSignatureDomain
         * 
         * @access  protected
         * @return  string
         */
        protected function _getOutboundSignatureDomain(): string
        {
            $signature = $this->_getOutboundSignature();
            $domain = $signature['domain'];
            return $domain;
        }

        /**
         * _getOutboundSignatures
         * 
         * @access  protected
         * @return  array
         */
        protected function _getOutboundSignatures(): array
        {
            $outboundSignatures = MailgunUtils::getOutboundSignatures();
            return $outboundSignatures;
        }

        /**
         * _getRecipientWhitelistPatterns
         * 
         * @access  protected
         * @return  array
         */
        protected function _getRecipientWhitelistPatterns(): array
        {
            $recipientWhitelistPatterns = MailgunUtils::getRecipientWhitelistPatterns();
            return $recipientWhitelistPatterns;
        }

        /**
         * _getSendEmails
         * 
         * @access  protected
         * @return  bool
         */
        protected function _getSendEmails(): bool
        {
            $sendEmails = MailgunUtils::getSendEmails();
            return $sendEmails;
        }

        /**
         * _setClientAttachments
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientAttachments(): bool
        {
            $attachments = $this->_attachments;
            foreach ($attachments as $attachment) {
                $this->_clientSendAttachments['attachment'] = $this->_clientSendAttachments['attachment'] ?? array();
                array_push(
                    $this->_clientSendAttachments['attachment'],
                    array(
                        'filePath' => $attachment['path'],
                        'remoteName' => $attachment['basename']
                    )
                );
            }
            return true;
        }

        /**
         * _setClientBody
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientBody(): bool
        {
            $body = $this->_getBody();
            if ($body === null) {
                return false;
            }
            $html = $this->_html;
            if ($html === true) {
                $this->_clientSendData['html'] = $body;
                return true;
            }
            $this->_clientSendData['text'] = $body;
            return true;
        }

        /**
         * _setClientFrom
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientFrom(): bool
        {
            $signature = $this->_getOutboundSignature();
            $email = $signature['email'];
            $normalizedEmailAddress = $this->_getNormalizedEmailAddress($email);
            $this->_clientSendData['from'] = $normalizedEmailAddress;
            return true;
        }

        /**
         * _setClientSubject
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientSubject(): bool
        {
            $subject = $this->_subject;
            $this->_clientSendData['subject'] = $subject;
            return true;
        }

        /**
         * _setClientTags
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientTags(): bool
        {
            $tags = $this->_tags;
            if (empty($tags) === true) {
                return false;
            }
            $this->_clientSendData['o:tag'] = $tags;
            return true;
        }

        /**
         * _setClientToRecipients
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientToRecipients(): bool
        {
            $this->_clientSendData['to'] = $this->_clientSendData['to'] ?? array();
            $toRecipients = $this->_toRecipients;
            foreach ($toRecipients as $toRecipient) {
                $normalizedEmailAddress = $this->_getNormalizedEmailAddress(
                    $toRecipient
                );
                array_push(
                    $this->_clientSendData['to'],
                    $normalizedEmailAddress
                );
            }
            return true;
        }

        /**
         * _setClientTracking
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientTracking(): bool
        {
            $tracking = $this->_tracking;
            $this->_clientSendData['o:tracking'] = $tracking;
            return true;
        }

        /**
         * send
         * 
         * @access  public
         * @return  bool
         */
        public function send(): bool
        {
            $validSendAttempt = $this->_validSendAttempt();
            if ($validSendAttempt === false) {
                return false;
            }
            $this->_setClientAttachments();
            $this->_setClientBody();
            $this->_setClientFrom();
            $this->_setClientSubject();
            $this->_setClientTags();
            $this->_setClientToRecipients();
            $this->_setClientTracking();
            $successful = $this->_attemptClientSend();
            return $successful;
        }
    }

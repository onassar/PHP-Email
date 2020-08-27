<?php

    // Namespace overhead
    namespace onassar\Email;

    /**
     * PostmarkEmail
     * 
     * @final
     * @extends OutboundEmail
     * @link    https://github.com/onassar/PHP-Email
     * @link    https://github.com/Znarkus/postmark-php
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class PostmarkEmail extends OutboundEmail
    {
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
         * _buildClient
         * 
         * @access  protected
         * @return  void
         */
        protected function _buildClient(): void
        {
            $apiKey = PostmarkUtils::getAPIKey();
            $client = new \Postmark\Mail($apiKey);
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
            if (class_exists('Postmark\Mail') === true) {
                return true;
            }
            $msg = 'Postmark SDK not found.';
            throw new \Exception($msg);
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
            $returnMessageId = true;
            $properties = compact('returnMessageId');
            try {
                $response = $client->send($properties);
                $this->_sendId = $response;
                return true;
            } catch (\Exception $exception) {
                $this->_lastException = $exception;
                return false;
            }
        }

        /**
         * _getOutboundSignatures
         * 
         * @access  protected
         * @return  array
         */
        protected function _getOutboundSignatures(): array
        {
            $outboundSignatures = PostmarkUtils::getOutboundSignatures();
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
            $recipientWhitelistPatterns = PostmarkUtils::getRecipientWhitelistPatterns();
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
            $sendEmails = PostmarkUtils::getSendEmails();
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
            $client = $this->_client;
            foreach ($attachments as $attachment) {
                $path = $attachment['path'];
                $filenameAlias = $attachment['basename'];
                $properties = compact('filenameAlias');
                $client->addAttachment($path, $properties);
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
            $client = $this->_client;
            if ($html === true) {
                $client->messageHtml($body);
                return true;
            }
            $client->messagePlain($body);
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
            $client = $this->_client;
            $signature = $this->_getOutboundSignature();
            $email = $signature['email'];
            $address = $email['address'];
            $name = $email['name'];
            $client->from($address, $name);
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
            $client = $this->_client;
            $subject = $this->_subject;
            $client->subject($subject);
            return true;
        }

        /**
         * _setClientTags
         * 
         * @note    Postmark SDK is currently limited to one tag per email. So
         *          I break after the initial tag is added.
         * @access  protected
         * @return  bool
         */
        protected function _setClientTags(): bool
        {
            $tags = $this->_tags;
            if (empty($tags) === true) {
                return false;
            }
            $client = $this->_client;
            foreach ($tags as $tag) {
                $client->tag($tag);
                break;
            }
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
            $toRecipients = $this->_toRecipients;
            $client = $this->_client;
            foreach ($toRecipients as $toRecipient) {
                $address = $toRecipient['address'];
                $name = $toRecipient['name'];
                $client->addTo($address, $name);
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
            if ($tracking === false) {
                return false;
            }
            $client = $this->_client;
            $client->trackOpen();
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

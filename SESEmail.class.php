<?php

    // Namespace overhead
    namespace onassar\Email;

    /**
     * SESEmail
     * 
     * @final
     * @extends OutboundEmail
     * @link    https://github.com/onassar/PHP-Email
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class SESEmail extends OutboundEmail
    {
        /**
         * _clientSendProperties
         * 
         * @access  protected
         * @var     array (default: array())
         */
        protected $_clientSendProperties = array();

        /**
         * _plainText
         * 
         * @access  protected
         * @var     null|string (default: null)
         */
        protected $_plainText = null;

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
            $properties = $this->_clientSendProperties;
            try {
                $response = $client->send($properties);
                if ($response === null) {
                    return false;
                }
                $this->_sendId = $response['MessageId'];
                return true;
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
            $client = \ClientWrappers::get('SES');
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
            if (class_exists('\ClientWrappers') === true) {
                return true;
            }
            $msg = 'ClientWrappers not found.';
            throw new \Exception($msg);
        }

        /**
         * _getOutboundSignatures
         * 
         * @access  protected
         * @return  array
         */
        protected function _getOutboundSignatures(): array
        {
            $outboundSignatures = SESUtils::getOutboundSignatures();
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
            $recipientWhitelistPatterns = SESUtils::getRecipientWhitelistPatterns();
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
            $sendEmails = SESUtils::getSendEmails();
            return $sendEmails;
        }

        /**
         * _setClientBCCRecipients
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientBCCRecipients(): bool
        {
            $bccRecipients = $this->_bccRecipients;
            $client = $this->_client;
            foreach ($bccRecipients as $bccRecipient) {
                $address = $bccRecipient['address'];
                $name = $bccRecipient['name'];
                // $this->_clientSendProperties['bcc'] = $this->_clientSendProperties['bcc'] ?? array();
                // $this->_clientSendProperties['bcc'][] = array($address, $name);
            }
            return true;
        }

        /**
         * _setClientCCRecipients
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientCCRecipients(): bool
        {
            $ccRecipients = $this->_ccRecipients;
            $client = $this->_client;
            foreach ($ccRecipients as $ccRecipient) {
                $address = $ccRecipient['address'];
                $name = $ccRecipient['name'];
                // $this->_clientSendProperties['cc'] = $this->_clientSendProperties['cc'] ?? array();
                // $this->_clientSendProperties['cc'][] = array($address, $name);
            }
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
            $this->_clientSendProperties['source'] = $email;
            return true;
        }

        /**
         * _setClientMessageBodyHTML
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientMessageBodyHTML(): bool
        {
            $body = $this->_getBody();
            if ($body === null) {
                return false;
            }
            $html = $this->_html;
            if ($html === false) {
                return false;
            }
            $this->_clientSendProperties['html'] = $body;
            return true;
        }

        /**
         * _setClientMessageBodyText
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientMessageBodyText(): bool
        {
            $text = $this->_plainText;
            if ($text === null) {
                return false;
            }
            $this->_clientSendProperties['text'] = $text;
            return true;
        }

        /**
         * _setClientReplyTo
         * 
         * @access  protected
         * @return  bool
         */
        protected function _setClientReplyTo(): bool
        {
            $replyToAddress = $this->_replyToAddress;
            if ($replyToAddress === null) {
                return false;
            }
            $replyToName = $this->_replyToName;
            $replyTo = array();
            $replyTo['address'] = $replyToAddress;
            $replyTo['name'] = $replyToName;
            $this->_clientSendProperties['replyTo'] = $replyTo;
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
            $this->_clientSendProperties['subject'] = $this->_subject;
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
            $this->_clientSendProperties['recipients'] = $this->_toRecipients;
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
            $this->_setClientMessageBodyHTML();
            $this->_setClientMessageBodyText();
            $this->_setClientFrom();
            $this->_setClientReplyTo();
            $this->_setClientSubject();
            $this->_setClientToRecipients();
            $this->_setClientCCRecipients();
            $this->_setClientBCCRecipients();
            $successful = $this->_attemptClientSend();
            return $successful;
        }

        /**
         * setPlainText
         * 
         * @access  public
         * @param   null|string $plainText
         * @return  bool
         */
        public function setPlainText(?string $plainText): bool
        {
            if ($plainText === null) {
                return false;
            }
            $this->_plainText = $plainText;
            return true;
        }
    }

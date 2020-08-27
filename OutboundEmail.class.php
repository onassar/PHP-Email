<?php

    // Namespace overhead
    namespace onassar\Email;

    /**
     * OutboundEmail
     * 
     * @link    https://github.com/onassar/PHP-Email
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    class OutboundEmail
    {
        /**
         * _attachments
         * 
         * @access  protected
         * @var     array (default: array())
         */
        protected $_attachments = array();

        /**
         * _body
         * 
         * @access  protected
         * @var     null|string (default: null)
         */
        protected $_body = null;

        /**
         * _bodyPath
         * 
         * @access  protected
         * @var     null|string (default: null)
         */
        protected $_bodyPath = null;

        /**
         * _client
         * 
         * @access  protected
         * @var     mixed (default: null)
         */
        protected $_client = null;

        /**
         * _html
         * 
         * @access  protected
         * @var     bool (default: true)
         */
        protected $_html = true;

        /**
         * _lastException
         * 
         * @access  protected
         * @var     null|Exception (default: null)
         */
        protected $_lastException = null;

        /**
         * _outboundSignatureKey
         * 
         * @access  protected
         * @var     string (default: 'default')
         */
        protected $_outboundSignatureKey = 'default';

        /**
         * _sendId
         * 
         * @access  protected
         * @var     null|string (default: null)
         */
        protected $_sendId = null;

        /**
         * _subject
         * 
         * @access  protected
         * @var     null|string (default: null)
         */
        protected $_subject = null;

        /**
         * _tags
         * 
         * @access  protected
         * @var     array (default: array())
         */
        protected $_tags = array();

        /**
         * _toRecipients
         * 
         * @access  protected
         * @var     array (default: array())
         */
        protected $_toRecipients = array();

        /**
         * _tracking
         * 
         * @access  protected
         * @var     bool (default: true)
         */
        protected $_tracking = true;

        /**
         * __construct
         * 
         * @access  public
         * @return  void
         */
        public function __construct()
        {
            $this->_checkForDependencies();
            $this->_buildClient();
        }

        /**
         * _getBody
         * 
         * @access  protected
         * @return  null|string
         */
        protected function _getBody(): ?string
        {
            $body = $this->_body;
            if ($body !== null) {
                return $body;
            }
            $bodyPath = $this->_bodyPath;
            if ($bodyPath !== null) {
                $body = file_get_contents($bodyPath);
                return $body;
            }
            $body = null;
            return $body;
        }

        /**
         * _getOutboundSignature
         * 
         * @access  protected
         * @return  array
         */
        protected function _getOutboundSignature(): array
        {
            $outboundSignatureKey = $this->_outboundSignatureKey;
            $outboundSignatures = $this->_getOutboundSignatures();
            $outboundSignature = $outboundSignatures[$outboundSignatureKey];
            return $outboundSignature;
        }

        /**
         * _patternMatches
         * 
         * @access  protected
         * @param   string $pattern
         * @param   string $str
         * @return  bool
         */
        protected function _patternMatches(string $pattern, string $str): bool
        {
            if (@preg_match($pattern, $str) === 1) {
                return true;
            }
            return false;
        }

        /**
         * _validSendAttempt
         * 
         * @access  protected
         * @return  bool
         */
        protected function _validSendAttempt(): bool
        {
            $sendEmails = $this->_getSendEmails();
            if ($sendEmails === true) {
                return true;
            }
            $this->_whitelistBasedToRecipientsFiltering();
            $toRecipients = $this->_toRecipients;
            if (count($toRecipients) === 0) {
                return false;
            }
            return true;
        }

        /**
         * _whitelistBasedToRecipientsFiltering
         * 
         * @see     https://stackoverflow.com/questions/7558022/php-reindex-array
         * @access  protected
         * @return  bool
         */
        protected function _whitelistBasedToRecipientsFiltering(): bool
        {
            $toRecipients = $this->_toRecipients;
            $recipientWhitelistPatterns = $this->_getRecipientWhitelistPatterns();
            foreach ($toRecipients as $index => $recipient) {
                $address = $recipient['address'];
                $matches = false;
                foreach ($recipientWhitelistPatterns as $pattern) {
                    $args = array($pattern, $address);
                    $patternMatches = $this->_patternMatches(... $args);
                    if ($patternMatches === true) {
                        $matches = true;
                        break;
                    }
                }
                if ($matches === false) {
                    unset($toRecipients[$index]);
                }
            }
            $toRecipients = array_values($toRecipients);
            $this->_toRecipients = $toRecipients;
            return true;
        }

        /**
         * addAttachment
         * 
         * @access  public
         * @param   string $basename
         * @param   string $path
         * @return  bool
         */
        public function addAttachment(string $basename, string $path): bool
        {
            $attachment = compact('basename', 'path');
            array_push($this->_attachments, $attachment);
            return true;
        }

        /**
         * addAttachments
         * 
         * @access  public
         * @param   array $attachments
         * @return  bool
         */
        public function addAttachments(array $attachments): bool
        {
            foreach ($attachments as $attachment) {
                $basename = $attachment['basename'];
                $path = $attachment['path'];
                $this->addAttachment($basename, $path);
            }
            return true;
        }

        /**
         * addTag
         * 
         * @access  public
         * @param   null|string $tag
         * @return  bool
         */
        public function addTag(?string $tag): bool
        {
            if ($tag === null) {
                return false;
            }
            array_push($this->_tags, $tag);
            return true;
        }

        /**
         * addToRecipient
         * 
         * @access  public
         * @param   null|string $address
         * @param   null|string $name (default: null)
         * @return  bool
         */
        public function addToRecipient(?string $address, ?string $name = null): bool
        {
            if ($address === null) {
                return false;
            }
            $recipient = compact('address', 'name');
            array_push($this->_toRecipients, $recipient);
            return true;
        }

        /**
         * getLastException
         * 
         * @access  public
         * @return  null|\Exception
         */
        public function getLastException(): ?\Exception
        {
            $lastException = $this->_lastException;
            return $lastException;
        }

        /**
         * getSendId
         * 
         * @access  public
         * @return  null|string
         */
        public function getSendId(): ?string
        {
            $sendId = $this->_sendId;
            return $sendId;
        }

        /**
         * setBody
         * 
         * @access  public
         * @param   null|string $body
         * @return  bool
         */
        public function setBody(?string $body): bool
        {
            if ($body === null) {
                return false;
            }
            $this->_body = $body;
            return true;
        }

        /**
         * setBodyPath
         * 
         * @access  public
         * @param   null|string $bodyPath
         * @return  bool
         */
        public function setBodyPath(?string $bodyPath): bool
        {
            if ($bodyPath === null) {
                return false;
            }
            $this->_bodyPath = $bodyPath;
            return true;
        }

        /**
         * setHTML
         * 
         * @access  public
         * @param   null|bool $html
         * @return  bool
         */
        public function setHTML(?bool $html): bool
        {
            if ($html === null) {
                return false;
            }
            $this->_html = $html;
            return true;
        }

        /**
         * setOutboundSignatureKey
         * 
         * @access  public
         * @param   null|string $outboundSignatureKey
         * @return  bool
         */
        public function setOutboundSignatureKey(?string $outboundSignatureKey): bool
        {
            if ($outboundSignatureKey === null) {
                return false;
            }
            $this->_outboundSignatureKey = $outboundSignatureKey;
            return true;
        }

        /**
         * setSubject
         * 
         * @access  public
         * @param   null|string $subject
         * @return  bool
         */
        public function setSubject(?string $subject): bool
        {
            if ($subject === null) {
                return false;
            }
            $this->_subject = $subject;
            return true;
        }

        /**
         * setText
         * 
         * @access  public
         * @param   null|bool $text
         * @return  bool
         */
        public function setText(?bool $text): bool
        {
            if ($text === null) {
                return false;
            }
            $this->_html = $text === false;
            return true;
        }

        /**
         * setTracking
         * 
         * @access  public
         * @param   null|bool $tracking
         * @return  bool
         */
        public function setTracking(?bool $tracking): bool
        {
            if ($tracking === null) {
                return false;
            }
            $this->_tracking = $tracking;
            return true;
        }
    }

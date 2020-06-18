<?php

    // Namespace
    namespace Email;

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
            $email = compact('address', 'name');
            array_push($this->_toRecipients, $email);
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

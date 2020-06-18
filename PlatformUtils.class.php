<?php

    // Namespace
    namespace Email;

    /**
     * PlatformUtils
     * 
     * @link    https://github.com/onassar/PHP-Email
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    class PlatformUtils
    {
        /**
         * _apiKey
         * 
         * @access  protected
         * @var     null|string (default: null)
         * @static
         */
        protected static $_apiKey = null;

        /**
         * _outboundSignatures
         * 
         * @access  protected
         * @var     array (default: array())
         * @static
         */
        protected static $_outboundSignatures = array();

        /**
         * _recipientWhitelistPatterns
         * 
         * @access  protected
         * @var     array (default: array())
         * @static
         */
        protected static $_recipientWhitelistPatterns = array();

        /**
         * _sendEmails
         * 
         * @access  protected
         * @var     bool (default: true)
         * @static
         */
        protected static $_sendEmails = true;

        /**
         * addOutboundSignature
         * 
         * @access  public
         * @static
         * @param   string $outboundSignatureKey
         * @param   array $outboundSignature
         * @return  void
         */
        public static function addOutboundSignature(string $outboundSignatureKey, array $outboundSignature): void
        {
            static::$_outboundSignatures[$outboundSignatureKey] = $outboundSignature;
        }

        /**
         * addOutboundSignatures
         * 
         * @access  public
         * @static
         * @param   array $outboundSignatures
         * @return  void
         */
        public static function addOutboundSignatures(array $outboundSignatures): void
        {
            foreach ($outboundSignatures as $outboundSignatureKey => $outboundSignature) {
                $args = array($outboundSignatureKey, $outboundSignature);
                static::addOutboundSignature(... $args);
            }
        }

        /**
         * addRecipientWhitelistPattern
         * 
         * @access  public
         * @static
         * @param   string $recipientWhitelistPattern
         * @return  void
         */
        public static function addRecipientWhitelistPattern(string $recipientWhitelistPattern): void
        {
            array_push(static::$_recipientWhitelistPatterns, $recipientWhitelistPattern);
        }

        /**
         * addRecipientWhitelistPatterns
         * 
         * @access  public
         * @static
         * @param   array $recipientWhitelistPatterns
         * @return  void
         */
        public static function addRecipientWhitelistPatterns(array $recipientWhitelistPatterns): void
        {
            foreach ($recipientWhitelistPatterns as $recipientWhitelistPattern) {
                static::addRecipientWhitelistPattern($recipientWhitelistPattern);
            }
        }

        /**
         * getAPIKey
         * 
         * @access  public
         * @static
         * @return  string
         */
        public static function getAPIKey(): string
        {
            $apiKey = static::$_apiKey;
            return $apiKey;
        }

        /**
         * getOutboundSignatures
         * 
         * @access  public
         * @static
         * @return  array
         */
        public static function getOutboundSignatures(): array
        {
            $outboundSignatures = static::$_outboundSignatures;
            return $outboundSignatures;
        }

        /**
         * getRecipientWhitelistPatterns
         * 
         * @access  public
         * @static
         * @return  array
         */
        public static function getRecipientWhitelistPatterns(): array
        {
            $recipientWhitelistPatterns = static::$_recipientWhitelistPatterns;
            return $recipientWhitelistPatterns;
        }

        /**
         * getSendEmails
         * 
         * @access  public
         * @static
         * @return  bool
         */
        public static function getSendEmails(): bool
        {
            $sendEmails = static::$_sendEmails;
            return $sendEmails;
        }

        /**
         * setSendEmails
         * 
         * @access  public
         * @static
         * @param   null|bool $sendEmails
         * @return  bool
         */
        public static function setSendEmails(?bool $sendEmails): bool
        {
            if ($sendEmails === null) {
                return false;
            }
            static::$_sendEmails = $sendEmails;
            return true;
        }

        /**
         * setAPIKey
         * 
         * @access  public
         * @static
         * @param   string $apiKey
         * @return  void
         */
        public static function setAPIKey(string $apiKey): void
        {
            static::$_apiKey = $apiKey;
        }
    }

<?php

    // Namespace overhead
    namespace onassar\Email;
    use onassar;
    use Utils;

    /**
     * PostmarkUtils
     * 
     * @final
     * @extends PlatformUtils
     * @link    https://github.com/onassar/PHP-Email
     * @author  Oliver Nassar <onassar@gmail.com>
     */
    final class PostmarkUtils extends PlatformUtils
    {
        /**
         * _accountToken
         * 
         * API key associated with managing a Postmark account.
         * 
         * @access  protected
         * @var     null|string (default: null)
         * @static
         */
        protected static $_accountToken = null;

        /**
         * _lastError
         * 
         * @access  protected
         * @var     null|array (default: null)
         * @static
         */
        protected static $_lastError = null;

        /**
         * _serverToken
         * 
         * API key associated with managing a Postmark server.
         * 
         * @access  protected
         * @var     null|string (default: null)
         * @static
         */
        protected static $_serverToken = null;

        /**
         * _createSenderSignature
         * 
         * @link    https://postmarkapp.com/developer/api/signatures-api#create-signature
         * @access  protected
         * @static
         * @param   string $address
         * @param   string $name
         * @param   null|string $replyToAddress (default: null)
         * @return  array
         */
        protected static function _createSenderSignature(string $address, string $name, ?string $replyToAddress = null): array
        {
            $url = static::_getCreateSenderSignatureURL();
            $tokenType = 'account';
            $request = static::_getRequest($url, $tokenType);
            $data = static::_getCreateSenderSignatureData($address, $name, $replyToAddress);
            $postContent = $data;
            $postContent = json_encode($postContent);
            $request->setPOSTContent($postContent);
            $response = $request->post();
            return $response;
        }

        /**
         * _deleteSenderSignature
         * 
         * @link    https://postmarkapp.com/developer/api/signatures-api#delete-signature
         * @access  protected
         * @static
         * @param   string $senderSignatureId
         * @return  array
         */
        protected static function _deleteSenderSignature(string $senderSignatureId): array
        {
            $url = static::_getDeleteSenderSignatureURL($senderSignatureId);
            $tokenType = 'account';
            $request = static::_getRequest($url, $tokenType);
            $request->setRequestMethod('delete');
            $response = $request->get();
            return $response;
        }

        /**
         * _getAccountToken
         * 
         * @access  protected
         * @static
         * @return  null|string
         */
        protected static function _getAccountToken(): ?string
        {
            $accountToken = static::$_accountToken;
            return $accountToken;
        }

        /**
         * _getCreateSenderSignatureData
         * 
         * @access  protected
         * @static
         * @param   string $address
         * @param   string $name
         * @param   null|string $replyToAddress (default: null)
         * @return  array
         */
        protected static function _getCreateSenderSignatureData(string $address, string $name, ?string $replyToAddress = null): array
        {
            $FromEmail = $address;
            $Name = $name;
            $ReplyToEmail = $replyToAddress ?? $FromEmail;
            $data = compact('FromEmail', 'Name', 'ReplyToEmail');
            return $data;
        }

        /**
         * _getCreateSenderSignatureURL
         * 
         * @access  protected
         * @static
         * @return  string
         */
        protected static function _getCreateSenderSignatureURL(): string
        {
            $path = '/senders';
            $host = 'api.postmarkapp.com';
            $url = Utils\Shared\URL::getURL($path, $host);
            return $url;
        }

        /**
         * _getDeleteSenderSignatureURL
         * 
         * @access  protected
         * @static
         * @param   string $senderSignatureId
         * @return  string
         */
        protected static function _getDeleteSenderSignatureURL(string $senderSignatureId): string
        {
            $path = '/senders/' . ($senderSignatureId);
            $host = 'api.postmarkapp.com';
            $url = Utils\Shared\URL::getURL($path, $host);
            return $url;
        }

        /**
         * _getListSenderSignaturesURL
         * 
         * @access  protected
         * @static
         * @return  string
         */
        protected static function _getListSenderSignaturesURL(): string
        {
            $path = '/senders';
            $host = 'api.postmarkapp.com';
            $url = Utils\Shared\URL::getURL($path, $host);
            return $url;
        }

        /**
         * _getMessageDetails
         * 
         * @link    https://postmarkapp.com/developer/api/messages-api#outbound-message-details
         * @access  protected
         * @static
         * @param   string $messageId
         * @return  array
         */
        protected static function _getMessageDetails(string $messageId): array
        {
            $url = static::_getMessageDetailsURL($messageId);
            $tokenType = 'server';
            $request = static::_getRequest($url, $tokenType);
            $response = $request->get();
            return $response;
        }

        /**
         * _getMessageDetailsURL
         * 
         * @access  protected
         * @static
         * @param   string $messageId
         * @return  string
         */
        protected static function _getMessageDetailsURL(string $messageId): string
        {
            $path = '/messages/outbound/' . ($messageId) . '/details';
            $host = 'api.postmarkapp.com';
            $url = Utils\Shared\URL::getURL($path, $host);
            return $url;
        }

        /**
         * _getRequest
         * 
         * @access  protected
         * @static
         * @param   string $url
         * @param   string $tokenType
         * @return  onassar\RemoteRequests\Base
         */
        protected static function _getRequest(string $url, string $tokenType): onassar\RemoteRequests\Base
        {
            $request = Utils\Shared\Requests::getRemoteRequest($url);
            $contentType = 'application/json';
            $request->setExpectedResponseContentType($contentType);
            $headers = static::_getRequestHeaders($tokenType);
            $header = implode("\r\n", $headers);
            $http = compact('header');
            $streamOptions = compact('http');
            $request->setStreamOptions($streamOptions);
            return $request;
        }

        /**
         * _getRequestHeaders
         * 
         * @access  protected
         * @static
         * @param   string $tokenType
         * @return  array
         */
        protected static function _getRequestHeaders(string $tokenType): array
        {
            $headers = array();
            $accountToken = static::_getAccountToken();
            $serverToken = static::_getServerToken();
            $header = 'X-Postmark-Account-Token: ' . ($accountToken);
            if ($tokenType === 'server') {
                $header = 'X-Postmark-Server-Token: ' . ($serverToken);
            }
            array_push($headers, $header);
            $header = 'Content-Type: application/json';
            array_push($headers, $header);
            $header = 'Accept: application/json';
            array_push($headers, $header);
            return $headers;
        }

        /**
         * _getResendSenderSignatureConfirmationURL
         * 
         * @access  protected
         * @static
         * @param   string $senderSignatureId
         * @return  string
         */
        protected static function _getResendSenderSignatureConfirmationURL(string $senderSignatureId): string
        {
            $path = '/senders/' . ($senderSignatureId) . '/resend';
            $host = 'api.postmarkapp.com';
            $url = Utils\Shared\URL::getURL($path, $host);
            return $url;
        }

        /**
         * _getServerToken
         * 
         * @access  protected
         * @static
         * @return  null|string
         */
        protected static function _getServerToken(): ?string
        {
            $serverToken = static::$_serverToken;
            return $serverToken;
        }

        /**
         * _listSenderSignatures
         * 
         * @link    https://postmarkapp.com/developer/api/signatures-api#list-sender-signatures
         * @access  protected
         * @static
         * @param   int $count
         * @param   int $offset
         * @return  array
         */
        protected static function _listSenderSignatures(int $count, int $offset): array
        {
            $url = static::_getCreateSenderSignatureURL();
            $tokenType = 'account';
            $request = static::_getRequest($url, $tokenType);
            $request->setRequestDataValue('count', $count);
            $request->setRequestDataValue('offset', $offset);
            $response = $request->get();
            $senderSignatures = $response['SenderSignatures'] ?? array();
            return $senderSignatures;
        }

        /**
         * _resendSenderSignatureConfirmation
         * 
         * @link    https://postmarkapp.com/developer/api/signatures-api#resend-confirmation
         * @access  protected
         * @static
         * @param   string $senderSignatureId
         * @return  array
         */
        protected static function _resendSenderSignatureConfirmation(string $senderSignatureId): array
        {
            $url = static::_getResendSenderSignatureConfirmationURL($senderSignatureId);
            $tokenType = 'account';
            $request = static::_getRequest($url, $tokenType);
            $postContent = array();
            $postContent = json_encode($postContent);
            $request->setPOSTContent($postContent);
            $response = $request->post();
            return $response;
        }

        /**
         * batch
         * 
         * @link    https://postmarkapp.com/developer/api/email-api#send-batch-emails
         * @see     https://postmarkapp.com/support/article/1208-how-to-add-an-unsubscribe-link
         * @access  public
         * @static
         * @param   array $payloads
         * @return  null|array
         */
        public static function batch(array $payloads): ?array
        {
            $url = 'https://api.postmarkapp.com/email/batch';
            $tokenType = 'server';
            $request = static::_getRequest($url, $tokenType);
            $postContent = $payloads;
            $postContent = json_encode($postContent);
            $request->setPOSTContent($postContent);
            $response = $request->post();
            return $response;
        }

        /**
         * createSenderSignature
         * 
         * @access  public
         * @static
         * @param   string $address
         * @param   string $name
         * @param   null|string $replyToAddress (default: null)
         * @return  null|array
         */
        public static function createSenderSignature(string $address, string $name, ?string $replyToAddress = null): ?array
        {
            $response = static::_createSenderSignature($address, $name, $replyToAddress);
            $error = $response['ErrorCode'] ?? null;
            if ($error === null || (int) $error === 0) {
                return $response;
            }
            static::$_lastError = $response;
            return null;
        }

        /**
         * deleteSenderSignature
         * 
         * @access  public
         * @static
         * @param   string $senderSignatureId
         * @return  bool
         */
        public static function deleteSenderSignature(string $senderSignatureId): bool
        {
            $response = static::_deleteSenderSignature($senderSignatureId);
            $error = $response['ErrorCode'] ?? null;
            if ($error === null || (int) $error === 0) {
                return true;
            }
            return false;
        }

        /**
         * getLastError
         * 
         * @access  public
         * @static
         * @return  null|array
         */
        public static function getLastError(): ?array
        {
            $lastError = static::$_lastError;
            return $lastError;
        }

        /**
         * getMessage
         * 
         * @access  public
         * @static
         * @param   string $messageId
         * @return  null|array
         */
        public static function getMessage(string $messageId): ?array
        {
            $response = static::_getMessageDetails($messageId);
            $error = $response['ErrorCode'] ?? null;
            if ($error === null || (int) $error === 0) {
                return $response;
            }
            static::$_lastError = $response;
            return null;
        }

        /**
         * listSenderSignatures
         * 
         * @access  public
         * @static
         * @param   int $count
         * @param   int $offset
         * @return  array
         */
        public static function listSenderSignatures(int $count, int $offset): array
        {
            $response = static::_listSenderSignatures($count, $offset);
            return $response;
        }

        /**
         * resendSenderSignatureConfirmation
         * 
         * @access  public
         * @static
         * @param   string $senderSignatureId
         * @return  bool
         */
        public static function resendSenderSignatureConfirmation(string $senderSignatureId): bool
        {
            $response = static::_resendSenderSignatureConfirmation($senderSignatureId);
            $error = $response['ErrorCode'] ?? null;
            if ($error === null || (int) $error === 0) {
                return true;
            }
            return false;
        }

        /**
         * setAccountToken
         * 
         * @access  public
         * @static
         * @param   string $accountToken
         * @return  void
         */
        public static function setAccountToken(string $accountToken): void
        {
            static::$_accountToken = $accountToken;
        }

        /**
         * setServerToken
         * 
         * @access  public
         * @static
         * @param   string $serverToken
         * @return  void
         */
        public static function setServerToken(string $serverToken): void
        {
            static::$_serverToken = $serverToken;
        }

        /**
         * smartCreateSenderSignature
         * 
         * Either resends a confirmation email to the sender signature found
         * (based on the address and name passed in), or creates a sender
         * signature based on those details.
         * 
         * @todo    Recursively look up signatures
         * @throws  \Exception
         * @access  public
         * @static
         * @param   string $address
         * @param   string $name
         * @param   null|string $replyToAddress (default: null)
         * @return  array
         */
        public static function smartCreateSenderSignature(string $address, string $name, ?string $replyToAddress = null): array
        {
            $count = 500;
            $offset = 0;
            $signatures = static::listSenderSignatures($count, $offset);
            foreach ($signatures as $signature) {
                if ($signature['EmailAddress'] !== $address) {
                    continue;
                }
                if ($signature['Name'] !== $name) {
                    continue;
                }
                $id = $signature['ID'] ?? null;
                if ($id === null) {
                    $msg = 'Invalid signature properties';
                    throw new \Exception($msg);
                }
                static::resendSenderSignatureConfirmation($id);
                return $signature;
            }
            $response = static::_createSenderSignature($address, $name. $replyToAddress);
            return $response;
        }
    }

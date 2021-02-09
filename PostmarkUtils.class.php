<?php

    // Namespace overhead
    namespace onassar\Email;

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
         * getInlinedMarkupStyles
         * 
         * @access  public
         * @static
         * @param   string $markup
         * @return  string
         */
        public static function getInlinedMarkupStyles(string $markup): string
        {
            $premailer = new \Premailer();
            $premailer->setMarkup($markup);
            $response = $premailer->getInlinedMarkup($markup);
            return $response;
        }
    }

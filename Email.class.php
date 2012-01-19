<?php

    /**
     * Email
     * 
     * Class which provides a simple API for defining a template for an outgoing
     * email. Using this class, emails can use native PHP (including global
     * variables) for output.
     * 
     * Does not manage sending of mail. For that an external mailer (like
     * Postmark) should be used.
     * 
     * @source https://github.com/onassar/PHP-Email
     * @see    https://github.com/Znarkus/postmark-php
     * @author Oliver Nassar <onassar@gmail.com>
     */
    class Email
    {
        /**
         * _template
         * 
         * @var    String
         * @access protected
         */
        protected $_template;

        /**
         * __construct
         * 
         * @access public
         * @param  String $template The path to the template file, containing
         *         markup mixed with standard PHP echos. The path specified here
         *         must be absolute.
         * @return void
         */
        public function __construct($template)
        {
            $this->_template = $template;
        }

        /**
         * render
         * 
         * @access public
         * @param  array $__data
         * @return string
         */
        public function render(array $__data)
        {
            // bring variables forward
            foreach ($__data as $_name => $_value) {
                $$_name = $_value;
            }

            // buffer handling
            ob_start();
            include $this->_template;
            $_response = ob_get_contents();
            ob_end_clean();

            // return rendered response
            return $_response;
        }
    }

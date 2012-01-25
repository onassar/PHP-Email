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
     * @author Oliver Nassar <onassar@gmail.com>
     */
    class Email
    {
        /**
         * _template
         * 
         * The path to the PHP file that ought to be used as a template. Can
         * contain native PHP, and make use of variables passed into the
         * <render> method as well as in the <$GLOBALS> array.
         * 
         * @var    String
         * @access protected
         */
        protected $_template;

        /**
         * __construct
         * 
         * Sets the path to the template, for use by the <render> method.
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
         * Accepts array-argument of associatively-keyed values to accomodate
         * the rendering of the template set in the constructor.
         * 
         * @access public
         * @param  array $__data
         * @return string
         */
        public function render(array $__data = array())
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

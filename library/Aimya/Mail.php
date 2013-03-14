<?php
class Aimya_Mail
{
    // templates name
    const SIGNUP_ACTIVATION          = "signup-activation";
    const FORGOT_PASSWORD            = "forgot_password";
    const SUBSCRIBE                  = "subscribe";
    const JOIN_CLUB_CONFIRMATION     = "join-club-confirmation";
    const ALERT                      = "alert";

    protected $_viewSubject;
    protected $_viewContent;
    protected $templateVariables = array();
    protected $templateName;
    protected $_mail;
    protected $recipient;

    public function __construct()
    {
        $this->_mail = new Zend_Mail();
        $this->_viewSubject = new Zend_View();
        $this->_viewContent = new Zend_View();
    }

    /**
     * Set variables for use in the templates
     *
     * @param string $name  The name of the variable to be stored
     * @param mixed  $value The value of the variable
     */
    public function __set($name, $value)
    {
        $this->templateVariables[$name] = $value;
    }

    /**
     * Set the template file to use
     *
     * @param string $filename Template filename
     */
    public function setTemplate($filename)
    {
        $this->templateName = $filename;
    }

    /**
     * Set the recipient address for the email message
     *
     * @param string $email Email address
     */
    public function setRecipient($email)
    {
        $this->recipient = $email;
    }

    /**
     * Send email
     *
     * @todo Add from name
     */
    public function send()
    {
        //$config = Zend_Registry::get('config');

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');


        $templatePath = $config->production->email->templatePath;
        //Zend_Debug::dump($emailPath);
        //die;

        $smtpServer = $config->production->email->smtpserver;
        $username = $config->production->email->smtpuser;
        $password = $config->production->email->smtppassword;
        $smtpport = $config->production->email->smtpport;
        $conf = array(
            //'ssl' => 'tls',
            'port' => $smtpport,
            'username' => $username,
            'password' => $password);

        $transport = new Zend_Mail_Transport_Smtp($smtpServer, $conf);

        $templateVars = $config->production->resources->mail->toArray();

        foreach ($templateVars as $key => $value)
        {
            if (!array_key_exists($key, $this->templateVariables)) {
                $this->{$key} = $value;
            }
        }

        $viewContent = realpath($templatePath) . DIRECTORY_SEPARATOR . $this->templateName . '.subj.tpl';
        $stringSubject = file_get_contents($viewContent);
        $subject = vsprintf($stringSubject, $this->templateVariables);

        $viewContent = realpath($templatePath) . DIRECTORY_SEPARATOR . $this->templateName . '.tpl';
        $stringMail = file_get_contents($viewContent);
        $html = vsprintf($stringMail, $this->templateVariables);

        $this->_mail->addTo($this->recipient);
        $this->_mail->setSubject($subject);
        $this->_mail->setBodyHtml($html);

        try {
            $this->_mail->send(1);
            return true;
        } catch(Exception $e) {
            //return false;
            echo 'Error Code: ',  $e->getMessage(), "\n";
        }
    }
}
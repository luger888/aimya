<?php
//include Selenium RC library
//require_once 'PHPUnit/Autolod.php'; //Extensions/SeleniumTestCase.php';

/**
 * Test Case for testing Admin login feature
 */
class Example extends PHPUnit_Extensions_SeleniumTestCase// PHPUnit_Extensions_SeleniumTestCase {
{
    private $_host = 'aimya.svitla.com';
    private $_port = 4469;
    private $_browser = '*firefox';
    private $_browserUrl = 'http://aimya.svitla.com/';
    private $_login = 'server';
    private $_password = 'AimyaServer123';
    private $_waitTime = '120';
    private $_lessonUrl = '';

    protected function setUp() {

       $fh = @fopen("./logfile.txt", "a+");
 	    fwrite($fh, 'second param: ' . $_SERVER['argv'][2]);
            fwrite($fh, 'third param: ' . $_SERVER['argv'][3]);
	    fwrite($fh, 'fourth param: ' . $_SERVER['argv'][4]);
            fclose($fh);

        $this->setHost($this->_host);
        $this->setPort((int)$_SERVER['argv'][4]);
        $this->setBrowser($this->_browser);
        $this->setBrowserUrl($this->_browserUrl);
        $this->application_url_path = $this->_browserUrl;
        $this->admin_login          = $this->_login;
        $this->admin_password       = $this->_password;
        $this->wait_time       = $this->_waitTime;
	$this->_lessonUrl = 'http://aimya.svitla.com/en/lesson/recording/?lessonId=' . (int)$_SERVER['argv'][3];

    }
    public function testMyTestCase()
   {

    $this->open("/");
    $this->type("id=username-login", "server");
    $this->type("id=password-login", "AimyaServer123");
    $this->click("id=login");
    $this->waitForPageToLoad("50000");
    $this->open($this->_lessonUrl);
    //$this->click("link=MY LESSONS");
    $this->waitForPageToLoad("30000");
    $this->verifyTextPresent("On The Air");
    $this->waitForPageToLoad("36000000");

  }

}

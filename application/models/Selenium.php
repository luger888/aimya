<?php
require_once 'Testing/Selenium.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Application_Model_Selenium extends PHPUnit_Framework_TestCase
{

    private $_host = 'aimya.svitla.com';
    private $_port = '4459';
    private $_browser = 'firefox';
    private $_browserUrl = 'aimya.svitla.com';
    private $_login = 'server';
    private $_password = 'AimyaServer123';
    private $_waitTime = '120';

    protected function setUp() {

        /*cmd=getNewBrowserSession&1=*firefox&2=http://aimya.svitla.com
        cmd=open&1=http://aimya.svitla.com&sessionId=1ac47ac3e26a4eac992125c1c28e085e*/


        $this->setHost($this->_host);
        $this->setPort($this->_port);
        $this->setBrowser($this->_browser);
        $this->setBrowserUrl($this->_browserUrl);

        $this->application_url_path = $this->_browserUrl;
        $this->admin_login          = $this->_login;
        $this->admin_password       = $this->_password;
        $this->wait_time       = $this->_waitTime;
    }

    public function openLessonPage()
    {

        $this->open($this->_browserUrl);
        $this->type("id=username-login", "server");
        $this->type("id=password-login", "AimyaServer123");
        $this->click("id=login");
        $this->waitForPageToLoad("30000");
        $this->click("link=MY LESSONS");
        $this->waitForPageToLoad("30000");
        try {
            $this->assertTrue($this->isTextPresent("Dashboard"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, 'Assertion #1: '.$e->toString());
        }
        try {
            $this->assertEquals("Dashboard / Magento Admin", $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, 'Assertion #2: '.$e->toString());
        }

        //$this->click("link=Log Out");
        //$this->waitForPageToLoad(30000);

    }

}
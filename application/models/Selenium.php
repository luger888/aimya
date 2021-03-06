<?php
require_once 'PHPUnit/Autoload.php';

class Application_Model_Selenium extends PHPUnit_Extensions_SeleniumTestCase //PHPUnit_Framework_TestCase
{

    private $_host = 'aimya.svitla.com';
    private $_port = '4459';
    private $_browser = 'firefox';
    private $_browserUrl = 'aimya.svitla.com';
    private $_applicationUrlPath = 'aimya.svitla.com/en/lesson';
    private $_login = 'server';
    private $_password = 'AimyaServer123';
    private $_waitTime = '120';

    protected function setUp() {


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
        $this->open($this->_applicationUrlPath);
        $this->waitForPageToLoad("30000");
        /*try {
            $this->assertTrue($this->isTextPresent("Dashboard"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, 'Assertion #1: '.$e->toString());
        }
        try {
            $this->assertEquals("Dashboard / Magento Admin", $this->getTitle());
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            array_push($this->verificationErrors, 'Assertion #2: '.$e->toString());
        }*/

        //$this->click("link=Log Out");
        //$this->waitForPageToLoad(30000);

    }

}
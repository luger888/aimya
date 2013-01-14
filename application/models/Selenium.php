<?php
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';
class Application_Model_Selenium extends PHPUnit_Extensions_SeleniumTestCase
{

    private $_host = 'aimya.svitla.com';
    private $_port = '';
    private $_browser = 'firefox';
    private $_browserUrl = 'aimya.svitla.com';
    private $_login = 'server';
    private $_password = 'AimyaServer123';
    private $_waitTime = '99999';

    public function openLessonPage()
    {

        $this->open($this->_browserUrl);
        $this->type("username", $this->_login);
        $this->type("login", $this->_password);
        $this->click("//input[@value='Login']");
        $this->waitForPageToLoad(3);
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

        $this->click("link=Log Out");
        $this->waitForPageToLoad($this->wait_time);

    }

}
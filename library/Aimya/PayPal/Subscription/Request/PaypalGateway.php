<?php
//  PaypalGateway.php
//  PaypalRecurringPayments
//
// Copyright 2011 Roman Efimov <romefimov@gmail.com>
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//    http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//

class Aimya_PayPal_Subscription_Request_PaypalGateway {
    
    public $apiUsername;
    public $apiPassword;
    public $apiSignature;
    public $testMode;
    public $returnUrl;
    public $cancelUrl;
    
    public function __construct($apiUsername = "", $apiPassword = "", $apiSignature = "", $testMode = false) {
        $this->apiUsername = $apiUsername;
        $this->apiPassword = $apiPassword;
        $this->apiSignature = $apiSignature;
        $this->testMode = $testMode;
    }
    
    public function getHost() {
        return $this->testMode ? "api-3t.sandbox.paypal.com" : "api-3t.paypal.com";
    }
    
    public function getGate() {
        return $this->testMode ? "https://www.sandbox.paypal.com/cgi-bin/webscr?" : "https://www.paypal.com/cgi-bin/webscr?";
    }
    
}

?>
<?php


    class Aimya_Application_Google_Spreadsheet {

        /**
        * @var Zend_Gdata_Spreadsheets
        */
        protected $spreadsheetService;
        protected $spreadsheetId = '';
        protected $worksheet;

        public function __construct($gmailAccount, $password, $spreadsheetId, $worksheet = "default")
        {
            $client = Zend_Gdata_ClientLogin::getHttpClient($gmailAccount, $password, Zend_Gdata_Spreadsheets::AUTH_SERVICE_NAME);

            $this->spreadsheetService = new Zend_Gdata_Spreadsheets($client);
            $this->spreadsheetId = $spreadsheetId;
            $this->worksheet = $worksheet;

        }

        public function getRows(){

            $query = new Zend_Gdata_Spreadsheets_DocumentQuery();
            $query->setSpreadsheetKey($this->spreadsheetId);
            $feed = $this->spreadsheetService->getWorksheetFeed($query);
            return $feed->entries[0]->getContentsAsRows();
        }


    public function getColumns(){

        $query = new Zend_Gdata_Spreadsheets_CellQuery();
        $query->setSpreadsheetKey($this->spreadsheetId);
        $feed = $currentWorksheet = $this->spreadsheetService->getCellFeed($query);
        $columns = array();
        $columnCount = intval($feed->getColumnCount()->getText());
        for($i = 0; $i < $columnCount; $i++)
        {
            if ($feed->entries[$i])
                $columns[$i] = $feed->entries[$i]->getCell()->getText();
        }

        return $columns;
    }

    public function insertRow($payload){

        return $this->spreadsheetService->insertRow($payload, $this->spreadsheetId);
    }


}

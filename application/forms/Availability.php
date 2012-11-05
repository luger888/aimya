<?php

class Application_Form_Availability extends Zend_Form
{
    public $basicDecorators = array('ViewHelper', 'Errors');
    public $basicFilters = array('StripTags', 'StringTrim');

    public function init()
    {
        $this->setName('availability');

        /* HOURS array for SELECT */
        $hours = array();
        foreach (range(1,24) as $fullhour) {
            $parthour = $fullhour > 12 ? $fullhour - 12 : $fullhour;
            $parthour .= $fullhour > 12 ? " pm" : " am";
            $hours["$fullhour:00"] = $parthour;
        }
        /*--END: HOURS array for SELECT */

        $daysOfWeek = array('Mon' => 'Mon', 'Tue' => 'Tue', 'Wed' => 'Wed', 'Thu' => 'Thu', 'Fri' => 'Fri', 'Sat' => 'Sat', 'Sun' => 'Sun');
        /* DropDowns 'FROM' and 'TO' for every day of week */
        foreach($daysOfWeek as  $day) {

            $this->addElement('select',  'from' . $day, array(
                'multiOptions' => $hours,
                'class' => 'input-small'
            ));
            $this->addElement('select',  'to' . $day, array(
                'multiOptions' => $hours,
                'class' => 'input-small'
            ));

        }
        /*--END: DropDowns 'FROM' and 'TO' for every day of week */
        $daysOfWeek = array_values($daysOfWeek);
        for($i = 0; $i < sizeof($daysOfWeek); $i++){
            $days[] = new Zend_Form_Element_Checkbox('checkbox' .$daysOfWeek[$i], array(
                'label'=> $daysOfWeek[$i]
            ));
        }
        $this->addElements($days);
        $day = new Zend_Form_Element_MultiCheckbox('day');
        $day ->addMultiOptions($daysOfWeek);


        $this->addElements(array( $day));
    }
}

?>
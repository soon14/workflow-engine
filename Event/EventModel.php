<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EventModel
 *
 * @author michael.hampton
 */
class EventModel extends BaseEvent
{

    public function create ($arrData)
    {

//        if (isset($aData["EVN_DESCRIPTION"])) {
//            $aData["EVN_DESCRIPTION"] = str_replace("__AMP__", "&", $aData["EVN_DESCRIPTION"]);
//        }
        try {

            $this->setTasUid ($arrData['task_id']);

            foreach ($arrData['conditions'] as $event => $aData) {

                if ( !isset ($aData['status']) || trim ($aData['status']) === "" || !in_array (trim ($aData['status']), array("Yes", "No")) || trim ($event) === "" )
                {
                    continue;
                }

                if ( isset ($aData['EVN_RELATED_TO']) )
                {

                    $this->setEvnRelatedTo ($aData['EVN_RELATED_TO']);
                    if ( $aData['EVN_RELATED_TO'] == 'SINGLE' )
                    {
                        if ( isset ($aData['TAS_UID']) )
                        {
                            $this->setTasUid ($aData['TAS_UID']);
                        }
                        $this->setEvnTasUidTo ('');
                        $this->setEvnTasUidFrom ('');
                    }
                    else
                    {
                        $this->setTasUid ('');
                        if ( isset ($aData['EVN_TAS_UID_TO']) )
                        {
                            $this->setEvnTasUidTo ($aData['EVN_TAS_UID_TO']);
                        }
                        if ( isset ($aData['EVN_TAS_UID_FROM']) )
                        {
                            $this->setEvnTasUidFrom ($aData['EVN_TAS_UID_FROM']);
                        }
                    }
                }

                if ( isset ($aData['EVN_POSX']) )
                {
                    $this->setEvnPosx ($aData['EVN_POSX']);
                }
                if ( isset ($aData['EVN_POSY']) )
                {
                    $this->setEvnPosy ($aData['EVN_POSY']);
                }
                if ( isset ($aData['EVN_TYPE']) )
                {
                    $this->setEvnType ($aData['EVN_TYPE']);
                }
                if ( isset ($aData['EVN_TIME_UNIT']) && isset($aData['EVN_TAS_ESTIMATED_DURATION']) )
                {
                    $this->setEvnTimeUnit ($aData['EVN_TIME_UNIT']);
                    if ( trim ($aData['EVN_TIME_UNIT']) == 'HOURS' )
                    {
                        $aData['EVN_TAS_ESTIMATED_DURATION'] = $aData['EVN_TAS_ESTIMATED_DURATION'] / 24;
                    }
                }

                if ( isset ($aData['EVN_TAS_ESTIMATED_DURATION']) )
                {
                    $this->setEvnTasEstimatedDuration ($aData['EVN_TAS_ESTIMATED_DURATION']);
                }
                if ( isset ($aData['EVN_WHEN_OCCURS']) )
                {
                    $this->setEvnWhenOccurs ($aData['EVN_WHEN_OCCURS']);
                }
                if ( isset ($aData['EVN_ACTION']) )
                {
                    $this->setEvnAction ($aData['EVN_ACTION']);
                }
                if ( isset ($aData['EVN_CONDITIONS']) )
                {
                    $this->setEvnConditions ($aData['EVN_CONDITIONS']);
                }

                if ( isset ($aData['EVN_WHEN']) )
                {
                    $this->setEvnWhen ($aData['EVN_WHEN']);
                }
                $this->setEvnMaxAttempts (3);
                //start the transaction
                if ( isset ($aData['EVN_TYPE']) )
                {
                    if ( $aData['EVN_TYPE'] === 'bpmnEventEmptyEnd' )
                    {
                        unset ($aData['TRI_UID']);
                    }
                }
                if ( isset ($aData['TRI_UID']) )
                {
                    $oTrigger = new Triggers();
                    if ( trim ($aData['TRI_UID']) === "" || (!$oTrigger->TriggerExists ($aData['TRI_UID'])) )
                    {
                        //create an empty trigger
                        $aTrigger = array();
                        $aTrigger['PRO_UID'] = $aData['PRO_UID'];
                        $aTrigger['TRI_TITLE'] = 'For event: ' . $aData['EVN_DESCRIPTION'];
                        $aTrigger['TRI_DESCRIPTION'] = 'Autogenerated ' . $aTrigger['TRI_TITLE'];
                        $aTrigger['TRI_WEBBOT'] = '// ' . $aTrigger['TRI_DESCRIPTION'];
                        ;
                        $oTrigger->create ($aTrigger);
                    }
                    else
                    {
                        $oTrigger = TriggersPeer::retrieveByPk ($aData['TRI_UID']);
                    }
                    $this->setTriUid ($oTrigger->getTriUid ());
                }

                $parameters = new StdClass();

                if ( isset ($aData['EVN_ACTION_PARAMETERS']['subject']) )
                {
                    $parameters->SUBJECT = $aData['EVN_ACTION_PARAMETERS']['subject'];
                    $parameters->TO = $aData['EVN_ACTION_PARAMETERS']['to'];
                    $parameters->CC = $aData['EVN_ACTION_PARAMETERS']['cc'];
                    $parameters->BCC = $aData['EVN_ACTION_PARAMETERS']['bcc'];
                    //$parameters->TEMPLATE = $aData['EVN_ACTION_PARAMETERS']['template'];
                }

                $this->setEvnActionParameters (serialize ($parameters));

                $this->setEvent ($event);
                $this->setEvnStatus ($aData['status']);

                if ( $this->validate () )
                {
                    $iResult = $this->doSave ();
                    if ( isset ($aData['EVN_DESCRIPTION']) )
                    {
                        $this->setEvnDescription ($aData['EVN_DESCRIPTION']);
                    }
                }
                else
                {
                    $sMessage = '';
                    $aValidationFailures = $this->getValidationFailures ();
                    foreach ($aValidationFailures as $message) {
                        $sMessage .= $message . '<br />';
                    }
                    throw (new Exception ('The row Event cannot be created!<br />' . $sMessage));
                }
            }
        } catch (Exception $oError) {
            throw ($oError);
        }
    }

}

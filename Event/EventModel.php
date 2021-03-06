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

    private $objMysql;

    private function getConnection ()
    {
        $this->objMysql = new Mysql2();
    }

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
                if ( isset ($aData['EVN_TIME_UNIT']) && isset ($aData['EVN_TAS_ESTIMATED_DURATION']) )
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

                        $oTrigger->create ($aTrigger);
                    }
                    else
                    {
                        //$oTrigger = TriggersPeer::retrieveByPk ($aData['TRI_UID']);
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
                $this->setMapId ($arrData['STEP_UID']);
                $this->setEvent ($event);
                $this->setEvnStatus ('ACTIVE');
                $this->setProUid ($arrData['PRO_UID']);
                $this->setEvnTasUidFrom ($arrData['EVN_TAS_UID_FROM']);

                if ( $this->validate () )
                {
                    $this->doSave ();

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

    public function closeAppEvents (Workflow $objWorkflow, $objMike, Task $objTask)
    {
        $id = method_exists($objMike, "getParentId") ? $objMike->getParentId() : $objMike->getId();
        
        $aAppEvents = $this->getAppEvents ($id, $objTask->getTasUid ());

        if ( $aAppEvents )
        {
            foreach ($aAppEvents as $aRow) {
                if ( $aRow['EVN_RELATED_TO'] == 'SINGLE' || ($aRow['EVN_RELATED_TO'] == $objTask->getTasUid ()) )
                {
                    $oAppEvent = (new AppEvent())->retrieveByPK ($aRow['APP_UID'], $aRow['DEL_INDEX'], $aRow['EVN_UID'], $aRow['CASE_UID']);
                    $oAppEvent->setAppEvnLastExecutionDate (date ('Y-m-d H:i:s'));
                    $oAppEvent->setAppEvnStatus ('CLOSE');
                    $oAppEvent->save ();
                }
            }
        }
    }

    public function createAppEvents (Workflow $objWorkflow, $objMike, Task $objTask)
    {
        $aRows = array();
        $aEventsRows = $this->getBy ((new Workflow ($objWorkflow->getWorkflowId ())), $objTask);
        if ( $aEventsRows !== false )
        {
            $aRows = array_merge ($aRows, $aEventsRows);
        }

        foreach ($aRows as $aData) {
            // if the events has a condition
            if ( trim ($aData['EVN_CONDITIONS']) != '' )
            {

                $oCase = new Cases();
                $Fields = $oCase->getCaseVariables ((int) $objMike->getId (), (int) $objMike->getParentId (), (int) $objTask->getTasUid ());

                $conditionContents = trim ($aData['EVN_CONDITIONS']);
                //$sContent    = G::unhtmlentities($sContent);
                $iAux = 0;
                $iOcurrences = preg_match_all ('/\@(?:([\>])([a-zA-Z\_]\w*)|([a-zA-Z\_][\w\-\>\:]*)\(((?:[^\\\\\)]*(?:[\\\\][\w\W])?)*)\))((?:\s*\[[\'"]?\w+[\'"]?\])+)?/', $conditionContents, $aMatch, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
                if ( $iOcurrences )
                {
                    for ($i = 0; $i < $iOcurrences; $i ++) {
                        preg_match_all ('/@>' . $aMatch[2][$i][0] . '([\w\W]*)' . '@<' . $aMatch[2][$i][0] . '/', $conditionContents, $aMatch2, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
                        $sGridName = $aMatch[2][$i][0];
                        $sStringToRepeat = $aMatch2[1][0][0];
                        if ( isset ($Fields[$sGridName]) )
                        {
                            if ( is_array ($Fields[$sGridName]) )
                            {
                                $sAux = '';
                                foreach ($Fields[$sGridName] as $aRow) {
                                    $sAux .= $oCase->replaceDataField ($sStringToRepeat, $aRow);
                                }
                            }
                        }
                        $conditionContents = str_replace ('@>' . $sGridName . $sStringToRepeat . '@<' . $sGridName, $sAux, $conditionContents);
                    }
                }
                $sCondition = $oCase->replaceDataField ($conditionContents, $Fields);
                /* $evalConditionResult = false;
                  $sCond = 'try{ $evalConditionResult=(' . $sCondition . ')? true: false; } catch(Exception $e){$evalConditionResult=false;}';
                  @eval( $sCond );
                  if (! $evalConditionResult) {
                  continue;
                  } */
            }

            $appEventData['APP_UID'] = method_exists ($objMike, "getParentId") ? $objMike->getParentId () : $objMike->getId ();
            $appEventData['CASE_UID'] = $objMike->getId ();
            $appEventData['DEL_INDEX'] = $objTask->getTasUid ();
            $appEventData['EVN_UID'] = $aData['EVN_UID'];
            $appEventData['APP_EVN_ACTION_DATE'] = $this->toCalculateTime ($aData);
            $appEventData['APP_EVN_ATTEMPTS'] = 3;
            $appEventData['APP_EVN_LAST_EXECUTION_DATE'] = null;
            $appEventData['APP_EVN_STATUS'] = 'OPEN';

            $oAppEvent = new AppEvent();
            $oAppEvent->create ($appEventData);
        }
    }

    public function getAppEvents ($APP_UID, $DEL_INDEX)
    {
        if ( $this->objMysql === null )
        {
            $this->getConnection ();
        }

        //for single task event

        $sql = "SELECT APP_UID, 
        DEL_INDEX, 
        CASE_UID,
        a.EVN_UID, 
        APP_EVN_ACTION_DATE, 
        APP_EVN_ATTEMPTS, 
        APP_EVN_LAST_EXECUTION_DATE, 
        APP_EVN_STATUS, 
        PRO_UID, 
        EVN_STATUS, 
        EVN_WHEN_OCCURS, 
        EVN_RELATED_TO, 
        TAS_UID, 
        EVN_TAS_UID_FROM, 
        EVN_TAS_UID_TO, 
        EVN_TAS_ESTIMATED_DURATION, 
        EVN_WHEN, 
        EVN_MAX_ATTEMPTS, 
        EVN_ACTION, 
        EVN_CONDITIONS, 
        EVN_ACTION_PARAMETERS
       FROM workflow.EVENT e
        INNER JOIN workflow.APP_EVENT a ON a.EVN_UID = e.EVN_UID
        WHERE a.APP_UID = ?
        AND a.DEL_INDEX = ? 
        AND a.APP_EVN_STATUS = 'OPEN'";

        $aRows = $this->objMysql->_query ($sql, [$APP_UID, $DEL_INDEX]);

        return (count ($aRows) > 0) ? $aRows : false;
    }

    public function getBy (Workflow $objWorkflow, Task $objTask)
    {
        if ( $this->objMysql === null )
        {
            $this->getConnection ();
        }

        $sql = "SELECT EVN_UID, TAS_UID, EVN_TAS_UID_FROM, EVN_TAS_UID_TO FROM workflow.EVENT WHERE EVN_STATUS = 'ACTIVE' AND EVN_ACTION != '' AND PRO_UID = ?";

        $results = $this->objMysql->_query ($sql, [$objWorkflow->getWorkflowId ()]);

        $eventsTask = array();
        foreach ($results as $aDataEvent) {

            if ( $objTask->getTasUid () == $aDataEvent['TAS_UID'] || $objTask->getTasUid () == $aDataEvent['EVN_TAS_UID_FROM'] || $objTask->getTasUid () == $aDataEvent['EVN_TAS_UID_TO'] )
            {
                $eventsTask[] = $aDataEvent['EVN_UID'];
            }
            else
            {
                //$flag = $this->verifyTaskbetween( $PRO_UID, $aDataEvent['EVN_TAS_UID_FROM'], $aDataEvent['EVN_TAS_UID_TO'], $taskUid );
                //if ($flag) {
                //$eventsTask[] = $aDataEvent['EVN_UID'];
                //}
            }
        }
        $aRows = array();

        if ( count ($eventsTask) > 0 )
        {
            $sql = " SELECT EVN_UID, 
                           PRO_UID, 
                           EVN_STATUS,
                           EVN_WHEN_OCCURS,
                           EVN_RELATED_TO, 
                
                           TAS_UID,
                           EVN_TAS_UID_FROM,
                           EVN_TAS_UID_TO, 
                           EVN_TAS_ESTIMATED_DURATION, 
                           EVN_WHEN, 
                           EVN_MAX_ATTEMPTS, 
                           EVN_ACTION, 
                           EVN_CONDITIONS, 
                           EVN_ACTION_PARAMETERS, 
                           TRI_UID 
                       FROM workflow.EVENT
                       WHERE EVN_UID IN  (" . implode (',', $eventsTask) . ")";

            $aRows = $this->objMysql->_query ($sql);
        }

        return (count ($aRows) > 0) ? $aRows : false;
    }

    public function toCalculateTime ($aData, $iDate = null)
    {

        $oCalendar = new CalendarFunctions();
        $iDate = isset ($iDate) ? $iDate : date ('Y-m-d H:i:s');
        $estimatedDuration = $aData['EVN_TAS_ESTIMATED_DURATION']; //task duration
        $when = $aData['EVN_WHEN']; //how many days
        $whenOccurs = $aData['EVN_WHEN_OCCURS']; //time on action (AFTER_TIME/TASK_STARTED)
        if ( $oCalendar->pmCalendarUid == '' )
        {
            $oCalendar->getCalendar (null, $aData['PRO_UID'], $aData['TAS_UID']);
            $oCalendar->getCalendarData ();
        }
        if ( $whenOccurs == 'TASK_STARTED' )
        {
            $calculatedDueDateA = $oCalendar->calculateDate ($iDate, $when, 'days');
            $sActionDate = date ('Y-m-d H:i:s', $calculatedDueDateA['DUE_DATE_SECONDS']);
        }
        else
        {
            $calculatedDueDateA = $oCalendar->calculateDate ($iDate, $estimatedDuration + $when, 'days');
            $sActionDate = date ('Y-m-d H:i:s', $calculatedDueDateA['DUE_DATE_SECONDS']);
        }
        return $sActionDate;
    }

}

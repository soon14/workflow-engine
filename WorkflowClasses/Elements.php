<?php

class Elements
{

    public $object;
    private $location;
    private $batch;
    private $description;
    private $name;
    private $current_step;
    private $workflow_id;
    private $audit;
    private $source_id;
    private $id;
    private $sampleRef;
    private $file2;
    private $rejectionReason;
    private $workflowName;
    private $current_user;
    private $currentStepId;
    public $arrElement = array();
    private $addedBy;
    private $requestId;
    private $projectName;
    private $dateCompleted;
    private $dueDate;
    private $originalTitle;
    private $originalDescription;
    private $heldReason;
    private $reassignReason;
    public $objJobFields = array(
        "location" => array("required" => "true", "type" => "string", "accessor" => "getLocation", "mutator" => "setLocation"),
        "batch" => array("required" => "true", "type" => "string", "accessor" => "getBatch", "mutator" => "setBatch"),
        "description" => array("required" => "true", "type" => "int", "accessor" => "getDescription", "mutator" => "setDescription"),
        "name" => array("required" => "true", "type" => "int", "accessor" => "getName", "mutator" => "setName"),
        "current_step" => array("required" => "true", "type" => "int", "accessor" => "getCurrent_step", "mutator" => "setCurrent_step"),
        "workflow_id" => array("required" => "true", "type" => "int", "accessor" => "getWorkflow_id", "mutator" => "setWorkflow_id"),
        "source_id" => array("required" => "true", "type" => "int", "accessor" => "getSource_id", "mutator" => "setSource_id"),
        "sampleRef" => array("required" => "true", "type" => "string", "accessor" => "getSampleRef", "mutator" => "setSampleRef"),
        "file2" => array("required" => "true", "type" => "int", "accessor" => "getFile2", "mutator" => "setFile2"),
        "rejectionReason" => array("required" => "false", "type" => "string", "accessor" => "getRejectionReason", "mutator" => "setRejectionReason"),
        "originalTitle" => array("required" => "false", "type" => "string", "accessor" => "getOriginalTitle", "mutator" => "setOriginalTitle"),
        "originalDescription" => array("required" => "false", "type" => "string", "accessor" => "getOriginalDescription", "mutator" => "setOriginalDescription"),
        "heldReason" => array("required" => "false", "type" => "string", "accessor" => "getHeldReason", "mutator" => "setHeldReason"),
    );
    public $objSchedulerFields = array(
        "description" => array("fieldName" => "title", "required" => "true", "type" => "int"),
        "name" => array("fieldName" => "title", "required" => "true", "type" => "int"),
        "current_step" => array("fieldName" => "current_step", "required" => "true", "type" => "int"),
    );
    private $arrToIgnore = array("claimed", "status", "dateCompleted", "priority", "deptId", "workflow", "added_by", "date_created", "project_status", "dueDate");
    private $status;
    private $objMysql;

    public function __construct ($parentId, $id = null)
    {
        $this->setParentId ($parentId);
        $this->getProjectById ();

        if ( $id !== null )
        {
            $this->setId ($id);
        }

        $this->getElement ();

        $this->objMysql = new Mysql2();
    }

    /**
     * 
     * @param type $id
     */
    public function setId ($id)
    {
        $this->id = $id;
    }

    /**
     * 
     * @param type $parentId
     */
    public function setParentId ($parentId)
    {
        $this->source_id = $parentId;
    }

    public function getParentId ()
    {
        return $this->source_id;
    }

    /**
     * 
     * @param type $arrElements
     * @return boolean
     */
    public function loadObject ($arrElements)
    {

        foreach ($arrElements as $formField => $formValue) {

            if ( isset ($this->objJobFields[$formField]) )
            {
                $mutator = $this->objJobFields[$formField]['mutator'];

                if ( method_exists ($this, $mutator) && is_callable (array($this, $mutator)) )
                {
                    if ( isset ($this->objJobFields[$formField]) && trim ($formValue) != "" )
                    {
                        call_user_func (array($this, $mutator), $formValue);
                    }
                }
            }
            elseif ( !in_array ($formField, $this->arrToIgnore) )
            {

                $objVariable = (new \BusinessModel\StepVariable())->getVariableForField ($formField);

                if ( is_a ($objVariable, 'Variable') )
                {
                    $variableName = $objVariable->getVariableName ();

                    switch ($objVariable->getValidationType ()) {
                        case "string":
                            if ( !is_string ($formValue) )
                            {
                                //die("Mike");
                            }
                            break;
                    }

                    $this->arrElement[$variableName] = $formValue;
                }
            }
        }

        return true;
    }

    public function getLocation ()
    {
        return $this->location;
    }

    public function getDescription ()
    {
        return $this->description;
    }

    public function getName ()
    {
        return $this->name;
    }

    public function getStatus ()
    {
        $this->status = trim ($this->status) == "" ? "IN PROGRESS" : $this->status;
        return $this->status;
    }

    /**
     * 
     * @param type $status
     */
    public function setStatus ($status)
    {
        $this->status = $status;
    }

    public function getCurrentStep ()
    {
        return $this->current_step;
    }

    public function getWorkflow_id ()
    {
        return $this->workflow_id;
    }

    public function getSource_id ()
    {
        return $this->source_id;
    }

    public function getSampleRef ()
    {
        return $this->sampleRef;
    }

    public function getFile2 ()
    {
        return $this->file2;
    }

    /**
     * 
     * @param type $location
     */
    public function setLocation ($location)
    {
        $this->location = $location;
        $this->arrElement['location'] = $location;
    }

    /**
     * 
     * @param type $description
     */
    public function setDescription ($description)
    {
        $this->description = $description;
        $this->arrElement['description'] = $description;
        $this->object['scheduler']['description'] = $description;
    }

    /**
     * 
     * @param type $name
     */
    public function setName ($name)
    {
        $this->name = $name;
        $this->arrElement['name'] = $name;
        $this->object['scheduler']['name'] = $name;
    }

    /**
     * 
     * @param type $current_step
     */
    public function setCurrent_step ($current_step)
    {
        $this->current_step = $current_step;
        $this->arrElement['current_step'] = $current_step;
        $this->object['scheduler']['current_step'] = $current_step;
    }

    /**
     * 
     * @param type $workflow_id
     */
    public function setWorkflow_id ($workflow_id)
    {
        $this->workflow_id = $workflow_id;
        $this->arrElement['workflow_id'] = $workflow_id;
    }

    /**
     * 
     * @param type $source_id
     */
    public function setSource_id ($source_id)
    {
        $this->source_id = $source_id;
        $this->arrElement['source_id'] = $source_id;
    }

    /**
     * 
     * @param type $sampleRef
     */
    public function setSampleRef ($sampleRef)
    {
        $this->sampleRef = $sampleRef;
        $this->arrElement['sampleRef'] = $sampleRef;
    }

    /**
     * 
     * @param string $file2
     */
    public function setFile2 ($file2)
    {
        if ( isset ($this->file2) )
        {
            $file2 .= "," . $this->file2;
        }

        $this->file2 = $file2;
        $this->arrElement['file2'] = $file2;
    }

    public function getBatch ()
    {
        return $this->batch;
    }

    /**
     * 
     * @param type $batch
     */
    public function setBatch ($batch)
    {
        $this->batch = $batch;
        $this->arrElement['batch'] = $batch;
    }

    public function getRejectionReason ()
    {
        return $this->rejectionReason;
    }

    /**
     * 
     * @param type $rejectionReason
     */
    public function setRejectionReason ($rejectionReason)
    {
        $this->rejectionReason = $rejectionReason;
        $this->arrElement['rejectionReason'] = $rejectionReason;
    }

    public function getWorkflowName ()
    {
        return $this->workflowName;
    }

    public function getCurrent_user ()
    {
        return $this->current_user;
    }

    public function getRequestId ()
    {
        return $this->requestId;
    }

    public function setRequestId ($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * 
     * @param type $workflowName
     */
    public function setWorkflowName ($workflowName)
    {
        if($workflowName === false) {
            return false;
        }
        
        $this->workflowName = $workflowName;
    }

    public function getCurrentStepId ()
    {
        return $this->currentStepId;
    }

    public function setCurrentStepId ($currentStepId)
    {
        $this->currentStepId = $currentStepId;
    }

    /**
     * 
     * @param type $current_user
     */
    public function setCurrent_user ($current_user)
    {
        $this->current_user = $current_user;
    }

    public function getAddedBy ()
    {
        return $this->addedBy;
    }

    public function setAddedBy ($addedBy)
    {
        $this->addedBy = $addedBy;
    }

    public function getDateCompleted ()
    {
        return $this->dateCompleted;
    }

    public function setDateCompleted ($dateCompleted)
    {
        $this->dateCompleted = $dateCompleted;
    }

    public function getProjectName ()
    {
        return $this->projectName;
    }

    public function setProjectName ($projectName)
    {
        $this->projectName = $projectName;
    }

    public function getHeldReason ()
    {
        return $this->heldReason;
    }

    /**
     * 
     * @param type $heldReason
     */
    public function setHeldReason ($heldReason)
    {
        $this->heldReason = $heldReason;
        $this->arrElement['heldReason'] = $heldReason;
    }

    public function getReassignReason ()
    {
        return $this->reassignReason;
    }

    /**
     * 
     * @param type $reassignReason
     */
    public function setReassignReason ($reassignReason)
    {
        $this->reassignReason = $reassignReason;
        $this->arrElement['reassignReason'] = $reassignReason;
    }
    
    public function getProjectById ()
    {
        $objMysql = new Mysql2();
        $result = $objMysql->_select ("task_manager.projects", array(), array("id" => $this->source_id));

        if ( !empty ($result[0]['step_data']) )
        {
            $JSON = json_decode ($result[0]['step_data'], true);

            if ( isset ($JSON['job']['added_by']) )
            {
                $this->setAddedBy ($JSON['job']['added_by']);
            }

            if ( isset ($JSON['job']['name']) )
            {
                $this->setProjectName ($JSON['job']['name']);
            }

            if ( $this->id != "" )
            {
                if ( isset ($JSON['elements'][$this->id]) )
                {
                    foreach ($JSON['elements'] as $element) {
                        $this->loadObject ($element);
                    }
                }
            }
            else
            {
                $this->loadObject ($JSON['job']);
            }
        }



        if ( isset ($JSON) && !empty ($JSON) )
        {
            $this->JSON = $JSON;
        }

        $auditResult = $objMysql->_select ("workflow.workflow_data", [], ["object_id" => $this->source_id]);

        if ( isset ($auditResult[0]) && !empty ($auditResult[0]) && isset ($auditResult[0]['audit_data']) )
        {
            $this->audit = json_decode ($auditResult[0]['audit_data'], true);
        }
    }

    public function getAudit ()
    {
        return $this->audit;
    }

    public function getOriginalDescription ()
    {
        return $this->originalDescription;
    }

    public function setOriginalDescription ($originalDescription)
    {
        $this->originalDescription = $originalDescription;
        $this->arrElement['originalDescription'] = $originalDescription;
    }

    public function getOriginalTitle ()
    {
        return $this->originalTitle;
    }

    public function setOriginalTitle ($originalTitle)
    {
        $this->originalTitle = $originalTitle;
        $this->arrElement['originalTitle'] = $originalTitle;
    }

    public function updateTitle (\Users $objUser, WorkflowStep $objStep = null)
    {
        $this->getElement ();

        $intChanged = 0;

        $dynaformId = $objStep !== null && is_numeric ($objStep->getNextTask ()) && (int) $objStep->getNextTask () !== 0 ? $objStep->getNextTask () : '';

        $objCases = new \BusinessModel\Cases();

        $Fields = $objCases->getCaseVariables ((int) $this->id, $this->source_id, $dynaformId);

        if ( !empty ($Fields) )
        {
            if ( trim ($this->originalTitle) !== "" )
            {
                $title = $objCases->replaceDataField ($this->originalTitle, $Fields);
                $this->setName ($title);
                $intChanged++;
            }

            if ( trim ($this->originalDescription) !== "" )
            {
                $description = $objCases->replaceDataField ($this->originalDescription, $Fields);
                $this->setDescription ($description);
                $intChanged++;
            }

            if ( $intChanged > 0 )
            {
                $this->save ($objUser);
            }
        }
    }

    public function getElement ()
    {

        $objMysql = new Mysql2();
        $result = $objMysql->_select ("task_manager.projects", array(), array("id" => $this->source_id));

        if ( !empty ($result[0]['step_data']) )
        {
            $JSON = json_decode ($result[0]['step_data'], true);

            if ( isset ($this->id) && isset ($JSON['elements'][$this->id]) )
            {
                $this->loadObject ($JSON['elements'][$this->id]);
            }
        }
    }

    /**
     * 
     * @param type $sourceId
     * @param type $workflow
     * @return type
     */
    public function buildObjectId ($sourceId)
    {
        if ( $this->objMysql === null )
        {
            $this->getConnection ();
        }

        $result = $this->objMysql->_select ("task_manager.projects", array(), array("id" => $sourceId));
        $JSON = json_decode ($result[0]['step_data'], true);

        $arrWorkflowData = $this->objMysql->_select ("workflow.workflow_data", array(), array("object_id" => $this->source_id));

        $workflowData = [];

        if ( isset ($arrWorkflowData[0]['workflow_data']) )
        {
            $workflowData = json_decode ($arrWorkflowData[0]['workflow_data'], true);
        }

        return isset ($JSON['elements']) ? count ($JSON['elements']) + 1 : 1;
    }

    public function getId ()
    {
        return $this->id;
    }

    /**
     * This function return an array without difference
     *
     *
     * @name arrayRecursiveDiff
     * @param  array $aArray1
     * @param  array $aArray2
     * @access public
     * @return $aReturn
     */
    public function arrayRecursiveDiff ($aArray1, $aArray2)
    {
        $aReturn = array();
        foreach ($aArray1 as $mKey => $mValue) {
            if ( is_array ($aArray2) && array_key_exists ($mKey, $aArray2) )
            {
                if ( is_array ($mValue) )
                {
                    $aRecursiveDiff = $this->arrayRecursiveDiff ($mValue, $aArray2[$mKey]);
                    if ( count ($aRecursiveDiff) )
                    {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                }
                else
                {
                    if ( $mValue != $aArray2[$mKey] )
                    {
                        $aReturn[$mKey] = $aArray2[$mKey];
                    }
                }
            }
            else
            {
                $aReturn[$mKey] = isset ($aArray2[$mKey]) ? $aArray2[$mKey] : null;
            }
        }
        return $aReturn;
    }

    public function array_key_intersect (&$a, &$b)
    {
        $array = array();
        while (list($key, $value) = each ($a)) {
            if ( isset ($b[$key]) )
            {
                if ( is_object ($b[$key]) && is_object ($value) )
                {
                    if ( serialize ($b[$key]) === serialize ($value) )
                    {
                        $array[$key] = $value;
                    }
                }
                else
                {
                    if ( $b[$key] !== $value )
                    {
                        $array[$key] = $value;
                    }
                }
            }
        }
        return $array;
    }

    private function doAudit (\Users $objUser)
    {
        $result = $this->objMysql->_select ("task_manager.projects", array(), array("id" => $this->source_id));
        $data = json_decode ($result[0]['step_data'], true);

        $FieldsBefore = $data['elements'][$this->id];

        $aApplicationFields = $this->arrElement;
        $FieldsDifference = $this->arrayRecursiveDiff ($FieldsBefore, $aApplicationFields);
        $fieldsOnBoth = $this->array_key_intersect ($FieldsBefore, $aApplicationFields);

        if ( (is_array ($FieldsDifference)) && (count ($FieldsDifference) > 0) )
        {
            $appHistory = new Audit();

            $FieldsDifference['before'] = $fieldsOnBoth;

            $aFieldsHistory = array(
                "APP_UID" => $this->source_id,
                "system_id" => 14,
                "PRO_UID" => 120,
                "TAS_UID" => $this->id,
                "APP_UPDATE_DATE" => date ("Y-m-d H:i:s"),
                "USER_UID" => $objUser->getUsername (),
                "before" => $fieldsOnBoth,
                "message" => "Field Updated"
            );
            $aFieldsHistory['APP_DATA']['BEFORE'] = $fieldsOnBoth;
            $aFieldsHistory['APP_DATA'] = serialize ($FieldsDifference);
            $appHistory->insertHistory ($aFieldsHistory);
        }
    }

    public function getConnection ()
    {
        $this->objMysql = new Mysql2();
    }

    public function save (\Users $objUser)
    {

        if ( $this->objMysql === null )
        {
            $this->getConnection ();
        }

        if ( trim ($this->source_id) !== "" )
        {
            $result = $this->objMysql->_select ("task_manager.projects", [], ["id" => $this->source_id]);

            if ( isset ($result[0]) && !empty ($result[0]) )
            {
                $data = json_decode ($result[0]['step_data'], true);
            }

            if ( isset ($data['elements']) )
            {
                $this->JSON['elements'] = $data['elements'];
            }
        }

        if ( $this->id == "" )
        {
            $id = (new AppSequence())->sequenceNumber();

            $this->setId ($id);

            $this->JSON['scheduler']['id'] = $id;
            $this->JSON['scheduler']['status'] = "Not Started";

            $this->JSON['scheduler']['backlogs'][$id] = $this->object['scheduler'];
            $this->JSON['elements'][$id] = $this->arrElement;

            $this->objMysql->_update ("task_manager.projects", array("step_data" => json_encode ($this->JSON)), array("id" => $this->source_id));
        }
        else
        {
            $this->doAudit ($objUser);
            $this->JSON['elements'][$this->id] = $this->arrElement;

            $this->objMysql->_update ("task_manager.projects", array("step_data" => json_encode ($this->JSON)), array("id" => $this->source_id));
        }

        (new \Log (LOG_FILE))->log (
                $this->JSON['elements'][$this->id], \Log::NOTICE);

        $additionalTables = new AdditionalTables();
        $additionalTables->updateReportTables ($this);
    }

    public function getCurrent_step ()
    {
        return $this->current_step;
    }

    public function getDueDate ()
    {
        return $this->dueDate;
    }

    public function setDueDate ($dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**

     *

     * @name ThrowUnpauseDaemon

     * author: erik@colosa.com

     * Description: This method set all cases with the APP_DISABLE_ACTION_DATE for today

     * @return void

     */
    public function ThrowUnpauseDaemon ($today, $cron = 0)
    {
        $today = (count (explode (" ", $today)) > 1) ? $today : $today . " 23:59:59";

        $sql = "SELECT * FROM workflow.APP_DELAY WHERE (APP_DISABLE_ACTION_USER = 0 OR ISNULL(APP_DISABLE_ACTION_USER) ) 
                AND (APP_DISABLE_ACTION_DATE <= '" . $today . "' OR APP_DISABLE_ACTION_DATE IS NOT NULL  )";

        $results = $this->objMysql->_query ($sql);

        foreach ($results as $aRow) {

            if ( $cron == 1 )
            {

                $arrayCron = unserialize (trim (@file_get_contents (PATH_DATA . "cron")));

                $arrayCron["processcTimeStart"] = time ();

                @file_put_contents (PATH_DATA . "cron", serialize ($arrayCron));
            }

            $this->unpauseCase ($aRow['APP_UID'], $aRow['APP_THREAD_INDEX'], 'System Daemon');
        }
    }

    /*

     * unpause a case

     *

     * @name unpauseCase

     * @param string $sApplicationUID

     * @param string $iDelegation

     * @param string $sUserUID

     * @return object

     */

    public function unpauseCase ($sApplicationUID, $iDelegation, $sUserUID, $moveTask = true)
    {

        //Verify status of the case

        $oDelay = new AppDelay();

        if ( method_exists ($oDelay, 'isPaused') )
        {

            if ( $oDelay->isPaused ($sApplicationUID, $iDelegation) === false )
            {

                return false;
            }
        }


        //get information about current $iDelegation row

        $results2 = $this->objMysql->_select ("workflow.workflow_data", [], ["object_id" => $sApplicationUID]);

        if ( !isset ($results2[0]) || empty ($results2[0]) )
        {
            return false;
        }

        $workflowData = json_decode ($results2[0]['workflow_data'], true);
        $auditData = json_decode ($results2[0]['audit_data'], true);

        $nextTaskSql = $this->objMysql->_query ("SELECT sm.id, sm.step_from FROM workflow.status_mapping sm WHERE sm.step_from = (SELECT step_to FROM workflow.status_mapping WHERE id = ?)", [$iDelegation]);

        if ( !isset ($nextTaskSql[0]) || empty ($nextTaskSql[0]) )
        {
            return false;
        }

        $nextTask = $nextTaskSql[0]['id'];

        $sql3 = "SELECT 
                    APP_DELAY_UID, 
                    APP_THREAD_INDEX, 
                    APP_STATUS 
                FROM workflow.APP_DELAY 
                WHERE 
                    APP_UID = ? AND 
                    APP_THREAD_INDEX = ? 
                    AND APP_TYPE = 'PAUSE'
                    AND (APP_DISABLE_ACTION_USER = 0 OR ISNULL(APP_DISABLE_ACTION_USER) )";
        $results3 = $this->objMysql->_query ($sql3, [$sApplicationUID, $iDelegation]);

        if ( !isset ($results3[0]) || empty ($results3[0]) )
        {
            return false;
        }

        $caseId = null;

        foreach ($workflowData['elements'] as $elementId => $element) {
            if ( $element['current_step'] === $iDelegation )
            {
                $workflowData['elements'][$elementId]['current_step'] = $nextTask;
                $caseId = $elementId;
            }
        }

        if ( $moveTask === true )
        {
            $auditData['elements'][$caseId]['steps'][$iDelegation]['status'] = "CLOSED";
            $auditData['elements'][$caseId]['steps'][$iDelegation]['finish_date'] = date ("Y-m-d H:i:s");

            $auditData['elements'][$caseId]['steps'][$nextTask]['claimed'] = 'auto_system';
            $auditData['elements'][$caseId]['steps'][$nextTask]['dateCompleted'] = date ("Y-m-d H:i:s");

            $this->objMysql->_update ("workflow.workflow_data", ["audit_data" => json_encode ($auditData), "workflow_data" => json_encode ($workflowData)], ["object_id" => $sApplicationUID]);
        }


        //update the DEL_INDEX ? in APP_THREAD table?

        $aData['APP_DELAY_UID'] = $results3[0]['APP_DELAY_UID'];

        $aData['APP_DISABLE_ACTION_USER'] = $sUserUID;

        $aData['APP_DISABLE_ACTION_DATE'] = date ('Y-m-d H:i:s');

        $oAppDelay = new AppDelay();

        $oAppDelay->update ($aData);

        $objTask = (new Task())->retrieveByPk ($nextTask);
        $objUser = (new \BusinessModel\UsersFactory())->getUser (2);

        (new BusinessModel\StepTrigger())->executeSendMail ($objUser, $objTask, "pauseCase");
    }

}

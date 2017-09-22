<?php

/**
 * Base class that represents a row from the 'LOGIN_LOG' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseLoginLog implements Persistent
{

    /**
     * The value for the log_uid field.
     * @var        string
     */
    protected $log_uid = '';

    /**
     * The value for the log_status field.
     * @var        string
     */
    protected $log_status = '';

    /**
     * The value for the log_ip field.
     * @var        string
     */
    protected $log_ip = '';

    /**
     * The value for the log_sid field.
     * @var        string
     */
    protected $log_sid = '';

    /**
     * The value for the log_init_date field.
     * @var        int
     */
    protected $log_init_date;

    /**
     * The value for the log_end_date field.
     * @var        int
     */
    protected $log_end_date;

    /**
     * The value for the log_client_hostname field.
     * @var        string
     */
    protected $log_client_hostname = '';

    /**
     * The value for the usr_uid field.
     * @var        string
     */
    protected $usr_uid = '';

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Get the [log_uid] column value.
     * 
     * @return     string
     */
    public function getLogUid ()
    {

        return $this->log_uid;
    }

    /**
     * Get the [log_status] column value.
     * 
     * @return     string
     */
    public function getLogStatus ()
    {

        return $this->log_status;
    }

    /**
     * Get the [log_ip] column value.
     * 
     * @return     string
     */
    public function getLogIp ()
    {

        return $this->log_ip;
    }

    /**
     * Get the [log_sid] column value.
     * 
     * @return     string
     */
    public function getLogSid ()
    {

        return $this->log_sid;
    }

    /**
     * Get the [optionally formatted] [log_init_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getLogInitDate ($format = 'Y-m-d H:i:s')
    {

        if ( $this->log_init_date === null || $this->log_init_date === '' )
        {
            return null;
        }
        elseif ( !is_int ($this->log_init_date) )
        {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime ($this->log_init_date);
            if ( $ts === -1 || $ts === false )
            {
                throw new Exception ("Unable to parse value of [log_init_date] as date/time value: ");
            }
        }
        else
        {
            $ts = $this->log_init_date;
        }
        if ( $format === null )
        {
            return $ts;
        }
        elseif ( strpos ($format, '%') !== false )
        {
            return strftime ($format, $ts);
        }
        else
        {
            return date ($format, $ts);
        }
    }

    /**
     * Get the [optionally formatted] [log_end_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getLogEndDate ($format = 'Y-m-d H:i:s')
    {

        if ( $this->log_end_date === null || $this->log_end_date === '' )
        {
            return null;
        }
        elseif ( !is_int ($this->log_end_date) )
        {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime ($this->log_end_date);
            if ( $ts === -1 || $ts === false )
            {
                throw new Exception ("Unable to parse value of [log_end_date] as date/time value: ");
            }
        }
        else
        {
            $ts = $this->log_end_date;
        }
        if ( $format === null )
        {
            return $ts;
        }
        elseif ( strpos ($format, '%') !== false )
        {
            return strftime ($format, $ts);
        }
        else
        {
            return date ($format, $ts);
        }
    }

    /**
     * Get the [log_client_hostname] column value.
     * 
     * @return     string
     */
    public function getLogClientHostname ()
    {

        return $this->log_client_hostname;
    }

    /**
     * Get the [usr_uid] column value.
     * 
     * @return     string
     */
    public function getUsrUid ()
    {

        return $this->usr_uid;
    }

    /**
     * Set the value of [log_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogUid ($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ( $v !== null && !is_string ($v) )
        {
            $v = (string) $v;
        }

        if ( $this->log_uid !== $v || $v === '' )
        {
            $this->log_uid = $v;
        }
    }

    /**
     * Set the value of [log_status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogStatus ($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ( $v !== null && !is_string ($v) )
        {
            $v = (string) $v;
        }

        if ( $this->log_status !== $v || $v === '' )
        {
            $this->log_status = $v;
        }
    }

    /**
     * Set the value of [log_ip] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogIp ($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ( $v !== null && !is_string ($v) )
        {
            $v = (string) $v;
        }

        if ( $this->log_ip !== $v || $v === '' )
        {
            $this->log_ip = $v;
        }
    }

    /**
     * Set the value of [log_sid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogSid ($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ( $v !== null && !is_string ($v) )
        {
            $v = (string) $v;
        }

        if ( $this->log_sid !== $v || $v === '' )
        {
            $this->log_sid = $v;
        }
    }

    /**
     * Set the value of [log_init_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setLogInitDate ($v)
    {

        if ( $v !== null && !is_int ($v) )
        {
            $ts = strtotime ($v);
            //Date/time accepts null values
            if ( $v == '' )
            {
                $ts = null;
            }
            if ( $ts === -1 || $ts === false )
            {
                throw new Exception ("Unable to parse date/time value for [log_init_date] from input: ");
            }
        }
        else
        {
            $ts = $v;
        }
        if ( $this->log_init_date !== $ts )
        {
            $this->log_init_date = date("Y-m-d H:i:s", $ts);
        }
    }

    /**
     * Set the value of [log_end_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setLogEndDate ($v)
    {

        if ( $v !== null && !is_int ($v) )
        {
            $ts = strtotime ($v);
            //Date/time accepts null values
            if ( $v == '' )
            {
                $ts = null;
            }
            if ( $ts === -1 || $ts === false )
            {
                throw new Exception ("Unable to parse date/time value for [log_end_date] from input: ");
            }
        }
        else
        {
            $ts = $v;
        }
        if ( $this->log_end_date !== $ts )
        {
            $this->log_end_date = date("Y-m-d H:i:s");
        }
    }

    /**
     * Set the value of [log_client_hostname] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLogClientHostname ($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ( $v !== null && !is_string ($v) )
        {
            $v = (string) $v;
        }

        if ( $this->log_client_hostname !== $v || $v === '' )
        {
            $this->log_client_hostname = $v;
        }
    }

    /**
     * Set the value of [usr_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setUsrUid ($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ( $v !== null && !is_string ($v) )
        {
            $v = (string) $v;
        }

        if ( $this->usr_uid !== $v || $v === '' )
        {
            $this->usr_uid = $v;
        }
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     * @throws Exception
     */
    public function delete ()
    {

        try {
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Stores the object in the database.  If the object is new,
     * it inserts it; otherwise an update is performed.  
     *
     * @return     int The number of rows affected by this insert/update
     * @throws     Exception
     */
    public function save ()
    {

        try {
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return     array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures ()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @return     boolean Whether all columns pass validation.
     * @see        getValidationFailures()
     */
    public function validate ()
    {
       return true;
    }

    /**
     * 
     * @param array $arrData
     */
    public function loadObject(array $arrData)
    {
        
    }

}
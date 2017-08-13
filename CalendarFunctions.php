<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CalendarFunctions
 *
 * @author michael.hampton
 */
class CalendarFunctions extends CalendarDefinition
{

    private $objMysql;
    public $pmCalendarUid = '';
    public $pmCalendarData = array();

    public function __construct ()
    {
        $this->objMysql = new Mysql2();
    }

    private function getConnection ()
    {
        $this->objMysql = new Mysql2();
    }

    /*     * ************SLA classes************** */

    public function dashCalculateDate ($iniDate, $duration, $formatDuration, $calendarData = array())
    {
        $calendarData['HOURS_FOR_DAY'] = 8;

        if ( strtoupper ($formatDuration) == 'DAYS' )
        {
            $duration = $duration * $calendarData['HOURS_FOR_DAY'];
        }
        if ( strtoupper ($formatDuration) == 'MINUTES' )
        {
            $duration = $duration / 60;
        }
        $hoursDuration = (float) $duration;
        $newDate = $iniDate;

        while ($hoursDuration > 0) {
            $newDate = $this->dashGetIniDate ($newDate, $calendarData);

            $rangeWorkHour = $this->dashGetRangeWorkHours ($newDate, $calendarData['BUSINESS_DAY']);

            $onlyDate = (date ('Y-m-d', strtotime ($newDate))) . ' ' . $rangeWorkHour['END'];

            if ( (((float) $hoursDuration) >= ((float) $rangeWorkHour['TOTAL'])) ||
                    ((strtotime ($onlyDate) - strtotime ($newDate)) < (((float) $hoursDuration) * 3600))
            )
            {
                $secondRes = (float) (strtotime ($onlyDate) - strtotime ($newDate));
                $newDate = $onlyDate;
                $hoursDuration -= (float) ($secondRes / 3600);
            }
            else
            {
                $newDate = date ('Y-m-d H:i:s', strtotime ('+' . (((float) $hoursDuration) * 3600) . ' seconds', strtotime ($newDate)));
                $hoursDuration = 0;
            }
        }

        return $newDate;
    }

    public function getCalendarData ($calendarUid = null)
    {
        if ( $this->objMysql === null )
        {
            $this->getConnection ();
        }

        $calendarUid = (is_null ($calendarUid)) ? $this->pmCalendarUid : $calendarUid;
        $this->pmCalendarUid = $calendarUid;
        //if exists the row in the database will update it, otherwise will insert.

        $tr = (new \CalendarDefinition())->retrieveByPK ($calendarUid);
        $defaultCalendar ['CALENDAR_UID'] = '00000000000000000000000000000001';
        $defaultCalendar ['CALENDAR_NAME'] = 'Default';
        $defaultCalendar ['CALENDAR_CREATE_DATE'] = date ('Y-m-d');
        $defaultCalendar ['CALENDAR_UPDATE_DATE'] = date ('Y-m-d');
        $defaultCalendar ['CALENDAR_DESCRIPTION'] = 'Default';
        $defaultCalendar ['CALENDAR_STATUS'] = 'ACTIVE';
        $defaultCalendar ['CALENDAR_WORK_DAYS'] = '1|2|3|4|5';
        $defaultCalendar ['CALENDAR_WORK_DAYS'] = explode ('|', '1|2|3|4|5');
        $defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_DAY'] = 7;
        $defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_START'] = '09:00';
        $defaultCalendar ['BUSINESS_DAY'] [1] ['CALENDAR_BUSINESS_END'] = '17:00';
        $defaultCalendar ['BUSINESS_DAY'] [1] ['DIFF_HOURS'] = '8';
        $defaultCalendar ['HOURS_FOR_DAY'] = '8';
        $defaultCalendar ['HOLIDAY'] = array();
        if ( (is_object ($tr) && get_class ($tr) == 'CalendarDefinition' ) )
        {
            $fields ['CALENDAR_UID'] = $tr->getCalendarUid ();
            $fields ['CALENDAR_NAME'] = $tr->getCalendarName ();
            $fields ['CALENDAR_CREATE_DATE'] = $tr->getCalendarCreateDate ();
            $fields ['CALENDAR_UPDATE_DATE'] = $tr->getCalendarUpdateDate ();
            $fields ['CALENDAR_DESCRIPTION'] = $tr->getCalendarDescription ();
            $fields ['CALENDAR_STATUS'] = $tr->getCalendarStatus ();
            $fields ['CALENDAR_WORK_DAYS'] = $tr->getCalendarWorkDays ();
            $fields ['CALENDAR_WORK_DAYS_A'] = explode ('|', $tr->getCalendarWorkDays ());
        }
        else
        {
            $fields = $defaultCalendar;
            //$this->saveCalendarInfo ( $fields );
            $fields ['CALENDAR_WORK_DAYS'] = '1|2|3|4|5';
            $fields ['CALENDAR_WORK_DAYS_A'] = explode ('|', '1|2|3|4|5');
            //$tr = CalendarDefinitionPeer::retrieveByPK ( $calendarUid );
        }

        $CalendarBusinessHoursObj = new \CalendarBusinessHours();
        $CalendarBusinessHours = $this->getCalendarBusinessHours ($calendarUid);

        $numDay = 8;
        $daysHours = array();
        $hoursCant = array();
        $modaHours = 0;
        $keyModa = 0;
        foreach ($CalendarBusinessHours as $value) {
            if ( $value['CALENDAR_BUSINESS_DAY'] != $numDay )
            {
                $numDay = $value['CALENDAR_BUSINESS_DAY'];
                $daysHours[$numDay] = 0;
            }
            $daysHours[$numDay] += $value['DIFF_HOURS'];
        }
        foreach ($daysHours as $value) {
            if ( isset ($hoursCant[$value]) )
            {
                $hoursCant[$value] ++;
            }
            else
            {
                $hoursCant[$value] = 1;
            }
        }
        foreach ($hoursCant as $key => $value) {
            if ( $value > $modaHours )
            {
                $modaHours = $value;
                $keyModa = $key;
            }
        }

        $fields ['HOURS_FOR_DAY'] = $keyModa;
        $fields ['BUSINESS_DAY'] = $CalendarBusinessHours;

        $CalendarHolidays = $this->getCalendarHolidays ($calendarUid);

        $fields ['HOLIDAY'] = $CalendarHolidays;
        $fields = $this->validateCalendarInfo ($fields, $defaultCalendar);

        $this->pmCalendarData = $fields;
        return $this->pmCalendarData;
    }

    public function dashIs_holiday ($date, $holidays = array())
    {
        $auxIniDate = explode (' ', $date);
        $iniDate = $auxIniDate['0'];
        $iniDate = strtotime ($iniDate);
        foreach ($holidays as $value) {
            $holidayStartDate = strtotime (date ('Y-m-d', strtotime ($value['CALENDAR_HOLIDAY_START'])));
            $holidayEndDate = strtotime (date ('Y-m-d', strtotime ($value['CALENDAR_HOLIDAY_END'])));
            if ( ($holidayStartDate <= $iniDate) && ($iniDate <= $holidayEndDate) )
            {
                return true;
            }
        }
        return false;
    }

    public function getCalendar ($userUid, $proUid = null, $tasUid = null)
    {
        $calendarData = array();
        //Default Calendar
        $calendarData['UID'] = '00000000000000000000000000000001';
        $calendarData['TYPE'] = 'DEFAULT';

        //Load User,Task and Process calendars (if exist)
        $sql = "SELECT CALENDAR_UID, USER_UID, OBJECT_TYPE FROM calendar.calendar_assignees WHERE USER_UID IN (" . implode (array($userUid, $proUid, $tasUid)) . ")";
        $results = $this->objMysql->_query ($sql);

        if ( !isset ($results[0]) || empty ($results[0]) )
        {
            return false;
        }

        $calendarArray = array();
        foreach ($results as $aRow) {
            if ( $aRow['USER_UID'] == $userUid )
            {
                $calendarArray['USER'] = $aRow ['CALENDAR_UID'];
            }
            if ( $aRow['USER_UID'] == $proUid )
            {
                $calendarArray['PROCESS'] = $aRow ['CALENDAR_UID'];
            }
            if ( $aRow['USER_UID'] == $tasUid )
            {
                $calendarArray['TASK'] = $aRow ['CALENDAR_UID'];
            }
        }

        if ( isset ($calendarArray['USER']) )
        {
            $calendarData['UID'] = $calendarArray['USER'];
            $calendarData['TYPE'] = 'USER';
        }
        elseif ( isset ($calendarArray['PROCESS']) )
        {
            $calendarData['UID'] = $calendarArray['PROCESS'];
            $calendarData['TYPE'] = 'PROCESS';
        }
        elseif ( isset ($calendarArray['TASK']) )
        {
            $calendarData['UID'] = $calendarArray['TASK'];
            $calendarData['TYPE'] = 'TASK';
        }
        $this->pmCalendarUid = $calendarData['UID'];
        return $this->pmCalendarUid;
    }

    /**
     * Small function used to add important information about the calcs and actions
     * to the log (that log will be saved)
     *
     * @name addCalendarLog
     * @param text $msg
     * @access public
     *
     */
    function addCalendarLog ($msg)
    {
        $this->calendarLog .= "\n" . date ("D M j G:i:s T Y") . ": " . $msg;
    }

    //Calculate the duration betwen two dates with a calendar
    public function dashCalculateDurationWithCalendar ($iniDate, $finDate = null, $calendarData = array())
    {
        if ( (is_null ($finDate)) || ($finDate == '') )
        {
            $finDate = date ('Y-m-d H:i:s');
        }


        if ( (strtotime ($finDate)) <= (strtotime ($iniDate)) )
        {
            return 0.00;
        }

        $secondDuration = 0.00;

        $finDate = $this->dashGetIniDate ($finDate, $calendarData);
        $newDate = $iniDate;

        $timeIniDate = strtotime ($iniDate);
        $timeFinDate = strtotime ($finDate);

        while ($timeIniDate < $timeFinDate) {
            $newDate = $this->dashGetIniDate ($newDate, $calendarData);

            $rangeWorkHour = $this->dashGetRangeWorkHours ($newDate, $calendarData['BUSINESS_DAY']);
            $onlyDate = (date ('Y-m-d', strtotime ($newDate))) . ' ' . $rangeWorkHour['END'];

            if ( (strtotime ($finDate)) < (strtotime ($onlyDate)) )
            {
                $secondRes = ( ((float) strtotime ($finDate)) - ((float) strtotime ($newDate)) );
                $timeIniDate = strtotime ($finDate);
                $secondDuration += (float) $secondRes;
            }
            else
            {
                $secondRes = ( ((float) strtotime ($onlyDate)) - ((float) strtotime ($newDate)) );
                $newDate = $onlyDate;
                $timeIniDate = strtotime ($onlyDate);
                $secondDuration += (float) $secondRes;
            }
        }
        return $secondDuration;
    }

    public function getCalendarHolidays ($calendarUid = null)
    {
        $calendarUid = (is_null ($calendarUid)) ? $this->pmCalendarUid : $calendarUid;
        $this->pmCalendarUid = $calendarUid;

        $sql = "SELECT CALENDAR_UID, CALENDAR_HOLIDAY_NAME, CALENDAR_HOLIDAY_START, CALENDAR_HOLIDAY_END FROM calendar.calendar_holidays WHERE CALENDAR_UID = ?";
        $arrParameters = array($calendarUid);
        $results = $this->objMysql->_query ($sql, $arrParameters);

        $fields = array();

        $count = 0;
        foreach ($results as $row) {
            $a = explode (' ', $row['CALENDAR_HOLIDAY_START']);
            $row['CALENDAR_HOLIDAY_START'] = $a[0];
            $a = explode (' ', $row['CALENDAR_HOLIDAY_END']);
            $row['CALENDAR_HOLIDAY_END'] = $a[0];
            $fields[$count] = $row;

            $count++;
        }
        return $fields;
    }

    public function getCalendarBusinessHours ($calendarUid = null)
    {
        $calendarUid = (is_null ($calendarUid)) ? $this->pmCalendarUid : $calendarUid;
        $this->pmCalendarUid = $calendarUid;

        $sql = "SELECT CALENDAR_UID, CALENDAR_BUSINESS_DAY, CALENDAR_BUSINESS_START, CALENDAR_BUSINESS_END FROM calendar.calendar_business_hours WHERE CALENDAR_UID = ?";
        $sql .= " ORDER BY CALENDAR_BUSINESS_DAY DESC, CALENDAR_BUSINESS_START ASC";
        $arrParameters = array($calendarUid);
        $results = $this->objMysql->_query ($sql, $arrParameters);

        $fields = array();
        $count = 0;
        foreach ($results as $row) {

            $iniTime = (float) str_replace (':', '', $row['CALENDAR_BUSINESS_START']);
            $finTime = (float) str_replace (':', '', $row['CALENDAR_BUSINESS_END']);
            $row['DIFF_HOURS'] = (($finTime - $iniTime) / 100);
            $fields[$count] = $row;
            $count++;
        }

        return $fields;
    }

    public function dashNextWorkHours ($date, $weekDay, $workHours = array())
    {
        $auxIniDate = explode (' ', $date);

        $timeDate = $auxIniDate['1'];
        $timeDate = (float) str_replace (':', '', ((strlen ($timeDate) == 8) ? $timeDate : $timeDate . ':00'));
        $nextWorkHours = array();

        $workHoursDay = array();
        $tempWorkHoursDay = array();

        foreach ($workHours as $value) {
            if ( $value['CALENDAR_BUSINESS_DAY'] == $weekDay )
            {
                $rangeWorkHour = array();
                $timeStart = $value['CALENDAR_BUSINESS_START'];
                $timeEnd = $value['CALENDAR_BUSINESS_END'];
                $rangeWorkHour['START'] = ((strlen ($timeStart) == 8) ? $timeStart : $timeStart . ':00');

                $rangeWorkHour['END'] = ((strlen ($timeEnd) == 8) ? $timeEnd : $timeEnd . ':00');

                $workHoursDay[] = $rangeWorkHour;
            }

            if ( $value['CALENDAR_BUSINESS_DAY'] == '7' )
            {
                $rangeWorkHour = array();
                $timeStart = $value['CALENDAR_BUSINESS_START'];
                $timeEnd = $value['CALENDAR_BUSINESS_END'];
                $rangeWorkHour['START'] = ((strlen ($timeStart) == 8) ? $timeStart : $timeStart . ':00');
                $rangeWorkHour['END'] = ((strlen ($timeEnd) == 8) ? $timeEnd : $timeEnd . ':00');

                $tempWorkHoursDay[] = $rangeWorkHour;
            }
        }

        if ( !(count ($workHoursDay)) )
        {
            $workHoursDay = $tempWorkHoursDay;
        }

        $countHours = count ($workHoursDay);
        if ( $countHours )
        {
            for ($i = 1; $i < $countHours; $i++) {
                for ($j = 0; $j < $countHours - $i; $j++) {
                    $dataft = (float) str_replace (':', '', $workHoursDay[$j]['START']);
                    $datasc = (float) str_replace (':', '', $workHoursDay[$j + 1]['END']);
                    if ( $dataft > $datasc )
                    {
                        $aux = $workHoursDay[$j + 1];
                        $workHoursDay[$j + 1] = $workHoursDay[$j];
                        $workHoursDay[$j] = $aux;
                    }
                }
            }

            foreach ($workHoursDay as $value) {
                $iniTime = (float) str_replace (':', '', ((strlen ($value['START']) == 8) ? $value['START'] : $value['START'] . ':00'));
                $finTime = (float) str_replace (':', '', ((strlen ($value['END']) == 8) ? $value['END'] : $value['END'] . ':00'));

                if ( $timeDate <= $iniTime )
                {
                    $nextWorkHours['STATUS'] = true;
                    $nextWorkHours['DATE'] = $auxIniDate['0'] . ' ' . ((strlen ($value['START']) == 8) ? $value['START'] : $value['START'] . ':00');
                    return $nextWorkHours;
                }
                elseif ( ($iniTime <= $timeDate) && ($timeDate < $finTime) )
                {
                    $nextWorkHours['STATUS'] = true;
                    $nextWorkHours['DATE'] = $date;
                    return $nextWorkHours;
                }
            }
        }

        $nextWorkHours['STATUS'] = false;
        return $nextWorkHours;
    }

    public function dashGetRangeWorkHours ($date, $workHours)
    {
        $auxIniDate = explode (' ', $date);
        $timeDate = $auxIniDate['1'];
        $timeDate = (float) str_replace (':', '', ((strlen ($timeDate) == 8) ? $timeDate : $timeDate . ':00'));
        $weekDay = date ('w', strtotime ($date));

        $workHoursDay = array();
        $tempWorkHoursDay = array();

        foreach ($workHours as $value) {
            if ( $value['CALENDAR_BUSINESS_DAY'] == $weekDay )
            {
                $rangeWorkHour = array();
                $timeStart = $value['CALENDAR_BUSINESS_START'];
                $timeEnd = $value['CALENDAR_BUSINESS_END'];
                $rangeWorkHour['START'] = ((strlen ($timeStart) == 8) ? $timeStart : $timeStart . ':00');
                $rangeWorkHour['END'] = ((strlen ($timeEnd) == 8) ? $timeEnd : $timeEnd . ':00');

                $workHoursDay[] = $rangeWorkHour;
            }

            if ( $value['CALENDAR_BUSINESS_DAY'] == '7' )
            {
                $rangeWorkHour = array();
                $timeStart = $value['CALENDAR_BUSINESS_START'];
                $timeEnd = $value['CALENDAR_BUSINESS_END'];
                $rangeWorkHour['START'] = ((strlen ($timeStart) == 8) ? $timeStart : $timeStart . ':00');
                $rangeWorkHour['END'] = ((strlen ($timeEnd) == 8) ? $timeEnd : $timeEnd . ':00');

                $tempWorkHoursDay[] = $rangeWorkHour;
            }
        }

        if ( !(count ($workHoursDay)) )
        {
            $workHoursDay = $tempWorkHoursDay;
        }

        foreach ($workHoursDay as $value) {
            $iniTime = (float) str_replace (':', '', $value['START']);
            $finTime = (float) str_replace (':', '', $value['END']);

            if ( ($iniTime <= $timeDate) && ($timeDate <= $finTime) )
            {
                //pr($finTime .' menos '.$iniTime .' = '.($finTime-$iniTime));
                $value['TOTAL'] = (($finTime - $iniTime) / 10000);
                return $value;
            }
        }
        return false;
    }

    public function dashGetIniDate ($iniDate, $calendarData = array())
    {
        $flagIniDate = true;

        while ($flagIniDate) {
            // 1 if it's a work day
            $weekDay = date ('w', strtotime ($iniDate));
            if ( !(in_array ($weekDay, $calendarData['CALENDAR_WORK_DAYS_A'])) )
            {
                $iniDate = date ('Y-m-d' . ' 00:00:00', strtotime ('+1 day', strtotime ($iniDate)));
                continue;
            }

            // 2 if it's a holiday
            $iniDateHolidayDay = $this->dashIs_holiday ($iniDate, $calendarData['HOLIDAY']);
            if ( $iniDateHolidayDay )
            {
                $iniDate = date ('Y-m-d' . ' 00:00:00', strtotime ('+1 day', strtotime ($iniDate)));
                continue;
            }

            // 3 if it's work time
            $workHours = $this->dashNextWorkHours ($iniDate, $weekDay, $calendarData['BUSINESS_DAY']);
            if ( !($workHours['STATUS']) )
            {
                $iniDate = date ('Y-m-d' . ' 00:00:00', strtotime ('+1 day', strtotime ($iniDate)));
                continue;
            }
            else
            {

                $iniDate = $workHours['DATE'];
            }
            $flagIniDate = false;
        }

        return $iniDate;
    }

}

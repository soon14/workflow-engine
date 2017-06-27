<?php
namespace BusinessModel;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GroupUser
 *
 * @author michael.hampton
 */
class GroupUser
{

    use Validator;
    
    private $objMysql;
    
    public function __construct ()
    {
        $this->objMysql = new Mysql2();
    }

    /**
     * Verify if doesn't exist the User in Group
     *
     * @param string $groupUid              Unique id of Group
     * @param string $userUid               Unique id of User
     * @param string $fieldNameForException Field name for the exception
     *
     * return void Throw exception if doesn't exist the User in Group
     */
    public function throwExceptionIfNotExistsGroupUser (\Teams $objGroup, \Users $objUser, $fieldNameForException)
    {
        try {
            
            if(trim($objGroup->getId ()) === "") {
                return false;
            }
            
            if(trim($objUser->getUserId ()) === "") {
                return false;
            }
            
            $obj = $this->retrieveByPK ($objGroup,  $objUser);
            if ( !(is_object ($obj) && get_class ($obj) == "GroupUser") )
            {
                throw new \Exception ("ID_GROUP_USER_IS_NOT_ASSIGNED");
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param string $grp_uid
     * @param string $usr_uid
     * @param      Connection $con
     * @return     GroupUser
     */
    public function retrieveByPK (\Teams $objGroup, \Users $objUser)
    {
      
        $v = $this->objMysql->_select("user_management.poms_users", [], ["team_id" => $objGroup->getId (), "usrid" => $objUser->getUserId ()]);
        
        return !empty ($v) ? $v[0] : null;
    }

    /**
     * Verify if exists the User in Group
     *
     * @param string $groupUid              Unique id of Group
     * @param string $userUid               Unique id of User
     *
     * return void Throw exception if exists the User in Group
     */
    public function throwExceptionIfExistsGroupUser (\Teams $objGroup, \Users $objUser)
    {
        try {
            
             if(trim($objGroup->getId ()) === "") {
                return false;
            }
            
            if(trim($objUser->getUserId ()) === "") {
                return false;
            }
            
            $obj =  $this->retrieveByPK ($objGroup, $objUser);
            
            if ( $obj !== null )
            {
                throw new Exception ("ID_GROUP_USER_IS_ALREADY_ASSIGNED");
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Assign User to Group
     *
     * @param string $groupUid  Unique id of Group
     * @param array  $arrayData Data
     *
     * return array Return data of the User assigned to Group
     */
    public function create (\Teams $objGroup, \Users $objUser)
    {
        try {
            
             if(trim($objGroup->getId ()) === "") {
                return false;
            }
            
            if(trim($objUser->getUserId ()) === "") {
                return false;
            }

            $group = new Team();
            $group->throwExceptionIfNotExistsGroup ($groupUid);
            $this->validateUserId ($userId);
            $this->throwExceptionIfExistsGroupUser ($objGroup, $objUser);
            //Create
            $group = new Team();
            
            $group->addUserToGroup ($groupUid, $userId);
            //Return
            $arrayData = array("GRP_UID" => $groupUid, "USR_UID" => $userId);

            return $arrayData;
        } catch (Exception $e) {
            throw $e;
        }
    }
    /**
     * Unassign User of the Group
     *
     * @param string $groupUid Unique id of Group
     * @param string $userUid  Unique id of User
     *
     * return void
     */
    public function delete (\Users $objUser, \Teams $objTeam = null)
    {
        try {
            
            if(trim($objUser->getUserId ()) === "") {
                return false;
            }
            
            //Verify data
            $group = new Team();
            if($groupUid !== null) {
                 $group->throwExceptionIfNotExistsGroup ($objTeam->getId ()); 
                 $this->throwExceptionIfNotExistsGroupUser ($objTeam, $objUser);
            }
           
            $this->validateUserId ($objUser->getUserId ());
           
            //Delete
            $groups = new \Teams();
            $group->removeUsersFromGroup ($userUid, $groupUid);
        } catch (Exception $e) {
            throw $e;
        }
    }
}

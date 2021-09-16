<?php
class ProfileClass
{
    /**
     * @var MainClass;
     */
    var $registry;
    /**
     * @var DatabaseClass
     */
    var $db;

    function __construct (&$registry){
        $this->registry=&$registry;
        $this->db=&$this->registry->db;
    }


    function InsertProfile ($data,$user_id){
        $this->db->query_write("INSERT INTO `users_profile`
(`profile_user_id`,`profile_name`,`profile_lastname`,`profile_phone`)
VALUES (
".$this->db->sql_prepare($user_id).",
".$this->db->sql_prepare($data['name']).",
".$this->db->sql_prepare($data['lastname']).",
".$this->db->sql_prepare($data['phone']).")"
        );
    }
    function UpdateProfile ($data,$user_id){
        $this->db->query_write("UPDATE `users_profile`
        SET `profile_name`=".$this->db->sql_prepare($data['name']).",
        `profile_lastname`=".$this->db->sql_prepare($data['lastname']).",
        `profile_phone`=".$this->db->sql_prepare($data['phone'])."
        WHERE `profile_user_id`=".$this->db->sql_prepare($user_id));
    }

    function GetUsersProfileListFromDb (){
        return $this->db->query_read("SELECT * FROM `users_profile`");
    }

    function  GetUserProfileList () {
        $users_array=array();
        $result=$this->GetUsersProfileListFromDb();
        while ($result_item = $this->db->fetch_array($result))
        {
            $users_array[$result_item['user_id']]=$result_item;
        }
        return $users_array;
    }


    function RemoveUserProfileFromDb ($user_id) {
        $this->db->query_write("DELETE FROM `users_profile` WHERE `profile_user_id`=".$this->db->sql_prepare($user_id));
    }


    function CheckProfileFields ($user_data,$profile_data){
        if(intval($user_data['id'])>0) {
            if ($this->registry->route->do != 'settings' AND $this->registry->route->do != 'change_email') {
                if ($profile_data['profile_name'] == '' OR
                    $profile_data['profile_lastname'] == '' OR
                    $profile_data['profile_phone'] == '' OR
                    $user_data['profile_email'] == ''
                ) {
                    SiteRedirect('/account/');
                }
            }
        }
    }
}

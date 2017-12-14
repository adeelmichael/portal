<?php
class UsersModel extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
    
    
    public function AuthUser($email, $pass)
    {
        $query = $this->db->query("select email, password, user_id, nice_name, users.role_id, roles.role, status from users join roles on roles.role_id = users.role_id where email = '$email' and password = '$pass'");
       return $query->row();

    }
    
   public function getTempCheck($password)
    {
        $query = $this->db->query("select temp_password, user_id, role_id from users where temp_password = '$password'");
       //echo $this->db->last_query();
        return $query->row_array();
    }
	
	public function getUser($user_id)
    {
        $query = $this->db->query("SELECT `user_id`, `f_name`, `l_name`, `phone`, `email`, `business_address`, `town`, `postCode`, `distance`, `business_type`, `details`, `primary_trade`, `nice_name`,  `role_id`, `status`, `created_date` FROM `users` WHERE `user_id` =  '$user_id'");
       //echo $this->db->last_query();
        return $query->row_array();
    }
    
    public function AuthEmail($email)
    {
        $query = $this->db->query("select email, user_id, role_id from users where email = '$email'");
        //echo $this->db->last_query();
        return $query->row();
    }
    
    public function getRandom_password($email)
    {
        $query = $this->db->query("select temp_password from users where email = '$email'");
        //echo $this->db->last_query();
        return $query->row();
    }
    
    public function update_UserID($id, $user_id)
    {
        $data = array(
            'user_id' => $user_id,
            'status' => '1'
        );
        
        $query = $this->db->update_string('jobs', $data, 'job_id = "'.$id.'" ');
       // echo $query;
        $this->db->query($query);
        
        return 1;
    }
    
    public function updateNewPassword($user_id, $new_pass)
    {
         $data = array(
            'password' => $new_pass
            
        );
        
        $query = $this->db->update_string('users', $data, 'user_id = "'.$user_id.'" ');
       // echo $query;
        $this->db->query($query);
        
        return 1;
    }
}
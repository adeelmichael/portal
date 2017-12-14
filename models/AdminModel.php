<?php
class AdminModel extends CI_Model{
    public function __construct() {
        parent::__construct();
    }
 
   public function get_all_profiles()
   {
       $query = $this->db->query("SELECT users.f_name, users.l_name, users.email, users.business_address, users.user_id, 
                               user_media.user_id as tradesmen_id, user_media.media as media, 
                               user_media.status as status, user_media.description as description, user_media.id as media_id
                                FROM users INNER JOIN user_media ON user_media.user_id = users.user_id 
                                WHERE users.role_id != '1' and users.role_id != '2'");
       //echo $this->db->last_query();
       return $query->result_array();
   }
   
   public function update_denial($details, $user_id, $media_id)
   {
       $data = array(
           'description' => $details,
           'status' => '2'
       );
       
       $query = $this->db->update_string('user_media', $data, 'id = "'.$media_id.'" ');
       //echo $query;
       $this->db->query($query);
       
       return 1;
   }
   
   
   public function update_verification($media_id)
   {
        $data = array(
           'status' => '1'
       );
       
       $query = $this->db->update_string('user_media', $data, 'id = "'.$media_id.'" ');
       
       $this->db->query($query);
       
       return 1;
   }
   
   
   public function gettradersData($user_id)
    {
        $query = $this->db->query("select * from users where user_id = '$user_id' and role_id = '3'");
        
        return $query->result_array();
    }
    
    public function getsignleMeta($user_id)
    {
        $query = $this->db->query("select * from user_media where user_id = '$user_id' ");
        
        return $query->result_array();
    }
 
    public function save_post($user_id, $title, $content, $imagePath)
    {
        $posted_at = date('Y-m-d H:i:s');
         $data = array(
           'user_id' => $user_id,
           'post_title' => $title,
           'content' => $content,
           'media' => $imagePath,
           'posted_at' => $posted_at,
       );
       
       $query = $this->db->insert_string('blog', $data);
       //echo $query;
       $this->db->query($query);
       
       return 1;
    }
    
    public function get_blog_posts()
    {
        $query = $this->db->query("select * from blog");
        
        return $query->result_array();
    }
    
    public function get_post_byId($post_id)
    {
        $query = $this->db->query("select * from blog where post_id = '$post_id'");
        
        return $query->result_array();
    }
    
    public function update_postdata($user_id, $title, $content, $imagePath, $post_id)
    {
        $posted_at = date('Y-m-d H:i:s');
        $data = array(
           'user_id' => $user_id,
           'post_title' => $title,
           'content' => $content,
           'media' => $imagePath,
           'posted_at' => $posted_at,
       );
       
       $query = $this->db->update_string('blog', $data, 'post_id = "'.$post_id.'" ');
      // echo $query;
       $this->db->query($query);
       
       return 1;
    }
    
    public function delete_post_data($post_id)
    {
        $query = $this->db->query("DELETE FROM blog where post_id = '$post_id'");
        
        return $query;
    }
    
    public function del_img($img)
    {
        $data = array(
           'media' => ''
        );
       
       $query = $this->db->update_string('blog', $data, 'media = "'.$img.'" ');
      // echo $query;
       $this->db->query($query);
       
       return 1;
    }
    
}
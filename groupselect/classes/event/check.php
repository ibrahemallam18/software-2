<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace mod_groupselect\event;

/**
 * Description of check
 *
 * @author world
 */
include_once  'C:\xampp\htdocs\moodle\mod\groupselect\db\DB_Class.php';
class check {
    //put your code here
    private $DB;
    function __construct() {
        $this->DB=new \DB_Class();
    }
    
    public function check_student($id,$group_id){
        $qu="SELECT `id` FROM `mdl_user` where `id` = $id";
        
        $result= $this->DB->get_row($qu);
        $checked=0;
//        echo $result['id'];
//        echo $id;
            if($id == $result['id']){
//                echo $checked;
                $checked = 1;
//                echo $checked;
            }
        if($checked == 1){
            $this->add_student($id,$group_id);
        }
        else{
            echo 'this person not exist in the system';
        }
    }
    
    public function add_student($id,$group_id){
        $qu="SELECT `id` FROM `mdl_groups_members` where `userid` = $id";
        $id2= $this->DB->get_row($qu);
        if($id2){
            echo 'this person is exist in this group';
        }
        else{
            $data=array();
            $data['userid']=$id;
            $data['group_id']=2;
//            $date = date('m/d/Y h:i:s a', time());
//            $data['timeadded']=$date;
            $Query = "INSERT INTO `mdl_groups_members` ( `groupid`, `userid`, `timeadded`, `itemid`) VALUES ( '2', '$id', '1238967', '0')";
            $result =  $this->DB->database_query($Query);
            //$this->DB->insert('mdl_groups_members',$data);
            echo 'added';
        }
    }
}

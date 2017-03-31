<?php

/**
 * Created by PhpStorm.
 * User: Daboss
 * Date: 22/12/2016
 * Time: 16:42
 */
class FormBuilder{

     public function surround($pl ,  $txt ){

          echo " <span class='text-primary'> $pl </span> <div class='form-group'>".$txt."</div>";
     }
     public function input($name ,$placeholder=null , $class=null ,  $value=null , $id=null  ){

       echo $this->surround( $placeholder ,"<input type='text' name='$name' value='$value' id='$id' class='$class' placeholder='$placeholder' >");
     }
     
     public function inputOff($name ,$placeholder=null , $class=null ,  $value=null , $id=null  ){

       echo $this->surround( $placeholder ,"<input type='text' name='$name' value='$value' id='$id' class='$class' placeholder='$placeholder' disabled >");
     }
     
     public function password($name ,$placeholder=null , $class=null , $value=null ,  $id=null ){

         echo "<input type='password' name='$name' value='$value'  class='$class' id='$id' placeholder='$placeholder'>";
     }

     public function textarea($name , $class=null ,  $value=null , $id=null ){

        echo "<textarea name='$name' class='$class' id='$id'> $value </textarea>";
     }

     public function select($name , $placeholder , $arr ,  $class=null , $id=null ){
        echo $placeholder;
        echo "<select name='$name' class='$class' id='$id' >" ;
            foreach ($arr as $key  => $value ){
               echo "<option value='$key' > $value </option>";
            }
        echo "</select>" ;
     }


     public function submit($name , $class=null ,  $id=null){

         echo "<input type='submit' name='$name' class='$class' id='$id' >";

     }


     public function button($name ,  $class=null ,  $value=null , $id=null ){

         echo "<button name='$name' class='$class' id='$id' > $value </button>";
     }



}
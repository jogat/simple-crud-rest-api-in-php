<?php
class MyDB extends SQLite3
{
   private $data= array();

   function __construct()
   {         
      $this->open('common/db/chinook.db');
   }

   function _select($tableName, $filter){
      try {
         $query = 'select * from '.$tableName." ";
         
         if(!empty($filter)){
            $i = 0;
            $query .= "where ";
            foreach ($filter as $f) {

                  //$f[0] = field
                  //$f[1] = operator
                  //$f[2] = value

               if( $i != 0 ){
                  $query .= "and ";
               }


               if( is_numeric($f[2]) ){ 
                  $query .= $f[0]." ".$f[1]." ".$f[2]." ";
               }
               else if( is_float($f[2]) ){ 
                  $query .= $f[0]." ".$f[1]." ".$f[2]." ";
               } else if($f[2] == ''){
                  $query .= $f[0]." ".$f[1]." '' ";
               }else {
                  $query .= $f[0]." ".$f[1]." '".$f[2]."' ";
               }  
               $i++;        
            }
         }

         

         $results = self::query($query);
         while ($res= $results->fetchArray(1))
         {     
            array_push($this->data, $res);
         } 




      } catch (Exception $e) {
         $this->data = ["error"=>self::lastErrorMsg()];
      }
      finally{
         self::close();   
      }
      return $this->data;                  
   }

   function _update($tableName, $filter, $newValues){
      try {
         $query = 'update '.$tableName." ";

         self::exec('BEGIN;');

         if(!empty($newValues)){
            $i = 0;
            $query .= "SET ";
            foreach ($newValues as $v) {

               if( $i != 0 ){
                  $query .= ", ";
               }

               if( is_numeric($v[1]) && strpos($v[1],".") == -1 ){ 

                  $query .= $v[0]."= :s".$v[0]." ";
               }
               else if( is_float($v[1]) && strpos($v[1],".") != -1){ 

                  $query .= $v[0]." = :s".$v[0]." ";
               } else if($v[1] == ''){
                  $query .= $v[0]." = :s".$v[0]." ";
               }else {
                  $query .= $v[0]." = :s".$v[0]." ";
               }  
               $i++;        
            }
         }

         if(!empty($filter)){
            $i = 0;
            $query .= "where ";
            foreach ($filter as $f) {                  
               if( $i != 0 ){
                  $query .= "and ";
               }


               if( is_numeric($f[2]) && strpos($f[2],".") == -1){ 
                  $query .= $f[0]." ".$f[1]." :w".$f[0]." ";
               }
               else if( is_float($f[2]) && strpos($f[2],".") != -1){ 
                  $query .= $f[0]." ".$f[1]." :w".$f[0]." ";
               } else if($f[2] == ''){
                  $query .= $f[0]." ".$f[1]." :w".$f[0]." ";
               }else {
                  $query .= $f[0]." ".$f[1]." :w".$f[0]." ";
               }  
               $i++;        
            }
         }


         $stmt=self::prepare($query);

         if(!empty($newValues)){                  
            foreach ($newValues as $v) {
               if( is_numeric($v[1]) && strpos($v[1],".") == -1){                         
                  $stmt->bindValue(":s".$v[0], $v[1], SQLITE3_INTEGER);
               }
               else if( is_float($v[1]) && strpos($v[1],".") != -1){ 
                  $stmt->bindValue(":s".$v[0], $v[1], SQLITE3_FLOAT);
               } else if($v[1] == ''){
                  $stmt->bindValue(":s".$v[0], '', SQLITE3_TEXT);
               }else {
                  $stmt->bindValue(":s".$v[0], $v[1], SQLITE3_TEXT);
               }                          
            }
         }

         if(!empty($filter)){               
            foreach ($filter as $f) {                                       
               if( is_numeric($f[2]) && strpos($f[2],".") == -1){                         
                  $stmt->bindValue(":w".$f[0], $f[2], SQLITE3_INTEGER);
               }
               else if( is_float($f[2]) && strpos($f[2],".") != -1){ 
                  $stmt->bindValue(":w".$f[0], $f[2], SQLITE3_FLOAT);
               } else if($f[2] == ''){
                  $stmt->bindValue(":w".$f[0], '', SQLITE3_TEXT);
               }else {
                  $stmt->bindValue(":w".$f[0], $f[2], SQLITE3_TEXT);
               }                          
            }
         }


         $stmt->execute();

         $consulta = self::exec('COMMIT;');

         if ($consulta) {
            $this->data =  self::changes();
         }



      } catch (Exception $e) {
         $this->data = ["error"=>self::lastErrorMsg()];
      }
      finally{
         self::close();
      }

      return $this->data;
   }

   function _insert($tableName, $newValues){
      try {         
         if(!empty($newValues)){
            $query = 'insert into '.$tableName." ";            
            
            $i = 0;
            $query .= "( ";

            foreach ($newValues as $key => $value){
               if( $i != 0 ){
                  $query .= ", ";
               }
               $query .= $key;
               $i++;
            }
            $query .= ") values(";

            $i = 0;

            foreach ($newValues as $key => $value){
               if( $i != 0 ){
                  $query .= ", ";
               }

               if( is_numeric($value) && strpos($value,".") == -1 ){ 
                  $query .= " :v".$key." ";
               }
               else if( is_float($value) && strpos($value,".") != -1){ 
                  $query .= " :v".$key." ";
               } else if($value == ''){
                  $query .= " :v".$key." ";
               }else {
                  $query .= " :v".$key." ";
               } 

               $i++;
            }
            
            $query .= ")";
                         
            $stmt = self::prepare($query);           

            foreach ($newValues as $key => $value){               
               if( is_numeric($value) && strpos($value,".") == -1 ){
                  $stmt->bindValue(":v".$key, $value, SQLITE3_INTEGER);                   
               }
               else if( is_float($value) && strpos($value,".") != -1){ 
                  $stmt->bindValue(":v".$key, $value, SQLITE3_FLOAT);
               } else if($value == ''){
                  $stmt->bindValue(":v".$key, $value, SQLITE3_TEXT);
               }else {
                  $stmt->bindValue(":v".$key, $value, SQLITE3_TEXT);
               }                
            }

            $stmt->execute();
            $this->data =  self::lastInsertRowID();                        
         }
      } catch (Exception $e) {
         $this->data = ["error"=>self::lastErrorMsg()];
      }
      finally{
         self::close();
      }

      return $this->data;
   }

   function _delete($tableName, $filter){
      try {         
         if(!empty($filter)){
            $query = 'delete from '.$tableName." ";            
            self::exec('BEGIN;');
            $i = 0;
            $query .= "WHERE ";            

            foreach ($filter as $f) {                  
               if( $i != 0 ){
                  $query .= "and ";
               }

               if( is_numeric($f[2]) && strpos($f[2],".") == -1){ 
                  $query .= $f[0]." ".$f[1]." :w".$f[0]." ";
               }
               else if( is_float($f[2]) && strpos($f[2],".") != -1){ 
                  $query .= $f[0]." ".$f[1]." :w".$f[0]." ";
               } else if($f[2] == ''){
                  $query .= $f[0]." ".$f[1]." :w".$f[0]." ";
               }else {
                  $query .= $f[0]." ".$f[1]." :w".$f[0]." ";
               }  
               $i++;        
            }
            
            
            $stmt=self::prepare($query);
            
                           
            foreach ($filter as $f) {                                       
               if( is_numeric($f[2]) && strpos($f[2],".") == -1){                         
                  $stmt->bindValue(":w".$f[0], $f[2], SQLITE3_INTEGER);
               }
               else if( is_float($f[2]) && strpos($f[2],".") != -1){ 
                  $stmt->bindValue(":w".$f[0], $f[2], SQLITE3_FLOAT);
               } else if($f[2] == ''){
                  $stmt->bindValue(":w".$f[0], '', SQLITE3_TEXT);
               }else {
                  $stmt->bindValue(":w".$f[0], $f[2], SQLITE3_TEXT);
               }                          
            }
                      
           

            $stmt->execute();
            $consulta = self::exec('COMMIT;');

            if ($consulta) {
               $this->data = self::changes();
            }
         }
      } catch (Exception $e) {
         $this->data = ["error"=>self::lastErrorMsg()];
      }
      finally{
         self::close();
      }

      return $this->data;
   }

   function _getTableSchema($tableName){
      try {
         $tablesquery = self::query("PRAGMA table_info(".$tableName.")");
         while ($table = $tablesquery->fetchArray(1)) {
            array_push($this->data, $table);
         }
      } catch (Exception $e) {
         
      }
      finally{
         self::close();
      }
      return $this->data;
   }

}  
?>

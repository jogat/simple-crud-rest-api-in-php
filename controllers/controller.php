<?php
require 'common/conn.php';

class controller
{    

	private $params = array();	
	private $Headers = array();
	private $fullRequest = array();
	private $tableName = "tracks";
	private $filter = array();
	private $tableSchema = array();
	
	function __construct(){	
		
		$this->Headers	= getallheaders();

		$param = json_decode(file_get_contents('php://input'));
		if($param){
			foreach ($param as $key => $value){			
				$this->params[$key] = htmlspecialchars($value);				
			}		
		}
		
		$this->fullRequest = explode('/', $_GET['PATH_INFO']);		
	}
	
	/*GET: Read resource*/
	public static function get()
	{		
		if(isset($this->fullRequest[1]) && !empty($this->fullRequest[1])){
		    $this->filter = array(
		  		array("TrackId","=",$this->fullRequest[1]),		  	 	
		  	);
		}		

		$db = new MyDB();

		return ["estado" => 200,
			"mensaje" => "GET",  
			//"fullRequest" => $this->fullRequest,
			"params" => $this->params,
			"results" => $db->_select($this->tableName,$this->filter)
			]; 
	}	
     
	/*POST: Create new resource*/
    public static function post()
	{
		$newValues = array(			
            "Name" => "Joshua Garza",
            "AlbumId"=> 12,
            "MediaTypeId"=> 11,
            "GenreId"=> 1,
            "Composer"=> "Angus Young, Malcolm Young, Brian Johnson",
            "Milliseconds"=> 343719,
            "Bytes"=> 11170334,
            "UnitPrice"=> 4.3
		);

		$db = new MyDB();

		return ["estado" => 200,
			"mensaje" => "PUT",  
			"fullRequest" => $this->fullRequest,
			"params" => $this->params,						
			"newValues" => $db->_insert($this->tableName, $newValues)
			];  
		          
	}

	/*PUT: Edit resource*/
	public static function put()
	{
		$newValues = array(
		  array("AlbumId",1),
		  array("Name","Joshua Garza"),
		  array("UnitPrice", 5.3)
		);


		if(isset($this->fullRequest[1]) && !empty($this->fullRequest[1])){
			$db = new MyDB();
			$this->filter = array(
		  		array("TrackId","=",$this->fullRequest[1])		  	 	
		  	);

			return ["estado" => 200,
			"mensaje" => "POST",  
			"fullRequest" => $this->fullRequest,
			"params" => $this->params,			
			"transact" => $db->_update($this->tableName,$this->filter,$newValues)
			]; 
		}	      
	}

	/*DELETE: erase resource*/
	public static function delete()
	{
		$db = new MyDB();
		
		if(isset($this->fullRequest[1]) && !empty($this->fullRequest[1])){
			$this->filter = array(
		  		array("TrackId","=",$this->fullRequest[1])		  	 	
		  	);
		}

		return ["estado" => 200,
			"mensaje" => "DELETE",  
			"fullRequest" => $this->fullRequest,
			"params" => $this->params,
			"deleted" => $db->_delete($this->tableName,$this->filter)
			];        
	}
	
	


   
}
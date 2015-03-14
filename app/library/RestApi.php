<?php

use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\DI;
use Phalcon\Mvc\Dispatcher;

abstract class RestApi
{
    /**
    *  Model's namespace
    */
    private $namespace = "Atendimento\Api\Models\\";

    /**
    * Model's name is registered from controller via parameter
    */
    private $modelName;

    /**
    * Model's name of relationship model
    */
    private $relationshipName=null;

    /**
    * Name of controller is passed in parameter 
    */
    private $controllerName;
    
    /**
    * Value of primary key field of model (passed in parameter)
    */
    private $id;

    /**
    * Name of primary key field of model
    */
    private $key;

    /**
    * Parameters
    */
    private $params;

    public function __construct()
    {
    	$di = new DI();

		$dispatcher = new Dispatcher();
		print_r($dispatcher->getControllerName() );exit;

        //$this->params = $this->dispatcher->getParams();
 //print_r($this->params);exit;

       
        //verifying if logged
        /*$this->authenticate->isLogged("blabla", 2); 

        $this->controllerName = strtolower($this->params[1]);
        $this->relationshipName = array_key_exists(3, $this->params) && $this->params[2]!="search" ? strtolower($this->params[3]) : null;
        $this->id = isset($this->params["id"]) ? $this->params["id"] : null;

        $this->setModelName();   */         
        
    }

    /**
    * Define main's model name and model's relationship
    */
    public function setModelName()
    {
        $config = include __DIR__ . "/../config/config.php";

        //set controller name
        $controllerName = $this->controllerName;
        $this->controllerName = $config->models->$controllerName;

        //set relationship if exists
        if ( $this->relationshipName!=null ){
            $relationshipName = $this->relationshipName;
            //the relationship model must have one slash (/) before
            $this->relationshipName = "\\" . $this->namespace . $config->models->$relationshipName;
        }

        //get the 3 pre chars of name's model
        $pre = ucfirst(substr($config->models->$controllerName, 0, 3));

        //name of primary key
        $this->key = strtoupper($pre) . "Codigo";

        //model name
        $this->modelName = $this->namespace . $this->controllerName;
    }


    /**
    * Method Http accept: GET
    * Retrive datas, if exists param return only data required
    */
    public function listAction()
    {
    	echo "listing pai...";exit;
        $response = new Response();
        $modelName = $this->modelName;

        //data of more models (relationship)
        if ( $this->relationshipName!=null ){
            $data = $modelName::findFirst( ($this->id) ? ($this->key = $this->id) : null );

			$relationshipName = $this->relationshipName;
            
			$data = $data->$relationshipName;

        //data of one model
        }else{
            $data = $modelName::find( ($this->id) ? ($this->key = $this->id) : null );
        }      

        //extracting data to array
        $data->setHydrateMode(Resultset::HYDRATE_ARRAYS);        
        $result = array();
        foreach( $data as $value ){
            $result[] = $value;
        }   

        //se for um único elemento remove os índices de cada item
        if ($this->id && !$this->relationshipName) $result = $result[0];

        $response->setJsonContent($result);     

        return $response;
    }

    /**
    * Method Http accept: GET
    * Search data with field/value passed in parameter
    * 
    * Ex : url: /api/usuario/search/USUNome/edvaldo/USUEmail/edvaldo2107@gmail.com/order/USUNome
    *      sql generated: select * from USUUsuario where USUNome='edvaldo' and USUEmail='edvaldo2107@gmail.com' order by USUNome
    * Ex2 : url: /api/usuario/search/DEPCodigo/(IN)1,2/order/USUNome
    *      sql generated: select * from USUUsuario where DEPCodigo in (1,2) order by USUNome
    */
    public function searchAction()
    {
        $response = new Response();
        //get model name
        $modelName = $this->modelName;

        //extracting parameterrs
        $params = array();
        for ( $i=3; $i<count($this->params); $i++ ){
            $params[$this->params[$i]] = $this->params[++$i];
        }
//print_r($params);exit;

        //building the query        
        $query = '$model = \\' . $modelName . "::query()";        
        $query .= "->where(\"1=1\")";//where default
        foreach ($params as $key => $value) {
            //if order by
            if ( $key=="order" ){
                $query .= "->orderBy(\"".$value."\")";
                break;
            //if condition is IN
            }else if ( strpos($value, "(IN)")!==false ){
                $value = substr($value, strpos($value, "(IN)")+4); 
                $query .= "->andWhere(\"$key IN ($value)\")";
            }else{
                $query .= "->andWhere(\"$key = '$value'\")";
            }
        }
        $query .= "->execute();";
//echo  "<p>".$query."</p>";
        eval($query);



        //extracting data from resultset after query executed
        $model->setHydrateMode(Resultset::HYDRATE_ARRAYS);
        $result = array();
        foreach ($model as $key => $value) {
           $result[] = $value;
        }
//print_r($result);
    
        $response->setJsonContent($result);     

        return $response;
    }

    /**
    * Method Http accept: POST (insert) and PUT (update)
    * Save/update data 
    */
    public function saveAction()
    {
        $response = new Response();
        $modelName = $this->modelName;      
        $model = new $modelName();

        //get data
        $data = get_object_vars($this->request->getJsonRawBody());

        //verify if any value is date (CURRENT_DATE, CURRENT_DATETIME), if it was replace for current date
        foreach ($data as $key => $value) {
            if ( $value=="CURRENT_DATE" ){
                $now = new \DateTime();
                $data[$key] =  $now->format('Y-m-d'); 
            }else if ( $value=="CURRENT_DATETIME" ){
                $now = new \DateTime();
                $data[$key] =  $now->format('Y-m-d H:i:s'); 
            }
        }

        //if have param then update
        if ( isset($this->id) )
            $model = $modelName::findFirst($this->id);
        
        if ( $model->save($data) ){
            $key = $this->key;
            $dataResponse = get_object_vars($model);

            //update
            if ( isset($this->id) ){
                $response->setJsonContent(array('status' => 'OK'));
            //insert
            }else{
                $response->setStatusCode(201, "Created");
                $response->setJsonContent(array(
                    'status' => 'OK',
                    $key => $model->$key,
                    'data' => array_merge($data, $dataResponse) //merge form data with return db
                ));
            }

        }else{
            $errors = array();
            foreach( $model->getMessages() as $message )
                $errors[] = $message->getMessage();

            $response->setJsonContent(array(
                'status' => 'ERROR',
                'messages' => $errors
            ));
        }

        return $response;
    }

    /**
    * Method Http accept: DELETE
    */
    public function deleteAction()
    {
        $response = new Response();
        $modelName = $this->modelName;

        $model = $modelName::findFirst($this->id);

        //delete if exists the object
        if ( $model!=false ){
            if ( $model->delete() == true ){
                $response->setJsonContent(array('status' => "OK"));
            }else{
               $response->setStatusCode(409, "Conflict");

               $errors = array();
               foreach( $model->getMessages() as $message )
                    $errors[] = $message->getMessage();

               $response->setJsonContent(array('status' => "ERROR", 'messages' => $erros));
            }
        }else{
            $response->setStatusCode(409, "Conflict");
            $response->setJsonContent(array('status' => "ERROR", 'messages' => array("O elemento não existe")));
        }

        return $response;
    }

}
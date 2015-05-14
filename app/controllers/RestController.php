<?php

use Phalcon\Http\Response;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\DI;
use Phalcon\Mvc\Dispatcher;

class RestController extends \Phalcon\Mvc\Controller
{

    /**
    * Model's name is registered from controller via parameter
    */
    private $modelName;

    /**
    * Model's name of relationship model
    */
    private $relationship=null;

    /**
    * Name of controller is passed in parameter 
    */
    private $controllerName;
    
    /**
    * Value of primary key field of model (passed in parameter)
    */
    private $id;

    /**
    * Parameters
    */
    private $params;

    /**
     * Response object
     * @var Phalcon\Http\Response
     */
    private $response;

    /**
     * Language's messages
     * @var array
     */
    private $language;

    public function initialize()
    {
        //set the language
        $this->setLanguage();
        
    	//print_r($this->dispatcher->getParams());exit;
    	$this->controllerName = $this->dispatcher->getControllerName();//controller
    	$this->modelName = $this->controllerName;//model
		$this->id = $this->dispatcher->getParam("id");//id
		$this->relationship = $this->dispatcher->getParam("relationship");//relationship

        $this->response = new Response();
    }

    /**
     * set language of errors responses
     */
    public function setLanguage(){
        //get the best language and all languages
        $bestLanguage = $this->request->getBestLanguage();
        $languages = $this->request->getLanguages();

        //sort the languages for quality desc
        foreach ($languages as $key => $row) {
            $language[$key]  = $row['language'];
            $quality[$key] = $row['quality'];
        }
        array_multisort($quality, SORT_DESC, $language, SORT_ASC, $languages);

        //veriry if exists the best language
        if ( file_exists("../app/languages/".$bestLanguage.".php") ){
            require "../app/languages/".$bestLanguage.".php";

        //if not exist best language find the first language existing
        }else{
            //search for the first existing language
            $cont = 0;
            foreach ($languages as $value) {
                if ( file_exists("../app/languages/".$value['language'].".php") ){
                    require "../app/languages/".$value['language'].".php";
                }
                else $cont++;
            }

            //if not find any language set the desfault
            if ( $cont == count($languages) ){
                require "../app/languages/en.php";
            }

        }

        //set the messages language 
        $this->language = $messages;
    }

    /**
     * Method Http accept: GET
     * @return JSON Retrive data by id
     */
    public function getAction(){
        $modelName = $this->modelName;

        $data = $modelName::find( $this->id );

        return $this->extractData($data);
    }


    /**
    * Method Http accept: GET
     * @return JSON Retrive all data, with and without relationship
    */
    public function listAction()
    {
        $modelName = $this->modelName;

        //data of more models (relationship)
        if ( $this->relationship!=null ){
            $data = $modelName::findFirst( $this->id );

			$relationship = $this->relationship;
            
			$data = $data->$relationship;

        //data of one model
        }else{
            $data = $modelName::find();
        }      

        return $this->extractData($data);
    }

    /**
     * Extract collection data to json
     * @param  Objcet     data object collecion with data
     * @return JSON       data in JSON
     */
    private function extractData($data){
        //extracting data to array
        $data->setHydrateMode(Resultset::HYDRATE_ARRAYS);        
        $result = array();
        foreach( $data as $value ){
            $result[] = $value;
        }   

        //se for um único elemento remove os índices de cada item
        if ($this->id && !$this->relationship) $result = $result[0];

        $this->response->setJsonContent($result);     

        return $this->response;
    }

    /**
    * Method Http accept: GET
    * Search data with field/value passed in parameter
    * 
    * Ex : url: /user/search/name/edvaldo/email/edvaldo2107@gmail.com/order/name
    *      sql generated: select * from user where name='edvaldo' and email='edvaldo2107@gmail.com' order by name
    * Ex2 : url: /user/search/id_department/(IN)1,2/order/name
    *      sql generated: select * from user where id_department in (1,2) order by name
    */
    public function searchAction()
    {
        //get model name
        $modelName = $this->modelName;

        //extracting parameterrs
        $params = array();
        for ( $i=3; $i<count($this->params); $i++ ){
            $params[$this->params[$i]] = $this->params[++$i];
        }

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
        eval($query);

        //extracting data from resultset after query executed
        $model->setHydrateMode(Resultset::HYDRATE_ARRAYS);
        $result = array();
        foreach ($model as $key => $value) {
           $result[] = $value;
        }
    
        $this->response->setJsonContent($result);     

        return $this->response;
    }

    /**
    * Method Http accept: POST (insert) and PUT (update)
    * Save/update data 
    */
    public function saveAction()
    {
        $modelName = $this->modelName;      
        $model = new $modelName();
        $util = new Util();
        $data = array();

        //get data
        $temp = $util->objectToArray($this->request->getJsonRawBody());

        //verify if exist more than one element
        if ( $util->existSubArray($temp) )
            $data = $temp;
        else
            $data[0] = $temp;


        //scroll through the arraay data and make the action save/update
        foreach ($data as $key => $value) {

             //verify if any value is date (CURRENT_DATE, CURRENT_DATETIME), if it was replace for current date
            foreach ($value as $k => $v) {
                if ( $v=="CURRENT_DATE" ){
                    $now = new \DateTime();
                    $value[$k] =  $now->format('Y-m-d'); 
                }else if ( $v=="CURRENT_DATETIME" ){
                    $now = new \DateTime();
                    $value[$k] =  $now->format('Y-m-d H:i:s'); 
                }
            }

            //if have param then update
            if ( isset($this->id) ) //if passed by url
                $model = $modelName::findFirst($this->id);
            
            if ( $model->save($value) ){
                $dataResponse = get_object_vars($model);

                //update
                if ( isset($this->id) ){
                    $this->response->setJsonContent(array('status' => 'OK'));
                //insert
                }else{
                    $this->response->setStatusCode(201, "Created");
                    $this->response->setJsonContent(array(
                        'status' => 'OK',
                        'data' => array_merge($value, $dataResponse) //merge form data with return db
                    ));
                }

            }else{
                $errors = array();
                foreach( $model->getMessages() as $message )
                    $errors[] = $this->language[$message->getMessage()] ? $this->language[$message->getMessage()] : $message->getMessage();

                $this->response->setJsonContent(array(
                    'status' => 'ERROR',
                    'messages' => $errors
                ));
            }

  
        }//end foreach

        return $this->response;
    }

    /**
    * Method Http accept: DELETE
    */
    public function deleteAction()
    {
        $modelName = $this->modelName;

        $model = $modelName::findFirst($this->id);

        //delete if exists the object
        if ( $model!=false ){
            if ( $model->delete() == true ){
                $this->response->setJsonContent(array('status' => "OK"));
            }else{
               $this->response->setStatusCode(409, "Conflict");

               $errors = array();
               foreach( $model->getMessages() as $message )
                    $errors[] = $this->language[$message->getMessage()] ? $this->language[$message->getMessage()] : $message->getMessage();

               $this->response->setJsonContent(array('status' => "ERROR", 'messages' => $errors));
            }
        }else{
            $this->response->setStatusCode(409, "Conflict");
            $this->response->setJsonContent(array('status' => "ERROR", 'messages' => array("O elemento não existe")));
        }

        return $this->response;
    }

}


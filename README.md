# Phalcon API Rest
This is a API Rest Project based in [Apigee Best Practices](http://apigee.com/about/resources/ebooks/web-api-design) using the [Phalcon PHP Framework](http://phalconphp.com) (The fastest
PHP Framework)

Complete CRUD (Create, Read, Update, Delete) of all tables of your database (including relationships), just point the database and generate models. All communication response/request with JSON.

### Example:

`GET /user` return all users  
`GET /user/1` return user with id 1  
`GET /department/1/user` return all user belonging to the departament with id 1  
`POST /user` insert user  
`PUT /user/1` update the user with id 1  
`DELETE /user/1` delete user with id 1  
The Search method is in development.. 


## Get Started
From framework installation until your database crud 

1. [Install Phalcon Framework](http://phalconphp.com/en/download/windows)
2. [Install Phalcon Developer Tools](http://phalconphp.com/en/download/tools) - This is a dev tools helpful for phalcon php developers
3. Clone the project for your machine

    `git clone https://github.com/edvaldoribeiro/API-REST-PHALCON-PHP.git`
    
4. Remove the examples of controllers and models

  `app/controllers/UserController.php`  
  `app/controllers/DepartamentController.php`  
  `app/models/User.php`  
  `app/models/Department.php`
  
5. Set the access configuration of your database in app/config/config.php
  
        'database' => array(
            'adapter'     => 'Mysql',` 
            'host'        => 'localhost',
            'username'    => 'root',
            'password'    => 'root',  
            'dbname'      => 'example',
        ) 
   
6. Within the project execute the follow command (with phalcon dev tools) for export your database in models
  
  `phalcon --all-models`  

    For the relationships works well your tables need to be setted with relationships correted. In my dev tools version the relationships are not being mapped in models just with command line above. To fix this, after you run the command above just open http://localhost/API-REST-PHALCON-PHP/webtools.php (case error, alter path of devtols in public/webtools.config.php), select the option all in "Table name" and mark the cheboxes "Define Relations", "Force" and click generate. All of your models will be updated with relationships.
    
7. Now just generate the empty controllers with same name of models, like the example below, using phalcon dev tools:

    `phalcon controller User --base-class=RestController`  
    `phalcon controller Department --base-class=RestController`  

    It's very important use the param --base-class to extends the RestController, because all crud are in there. If you need create another response for any part of your crud, just implement the action in desired controller overriding the RestController method. 
   
  

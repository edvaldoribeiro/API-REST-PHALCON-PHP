# Phalcon API Rest
This is a API Rest Project based in [Apigee Best Practices](http://apigee.com/) using the [Phalcon PHP Framework](http://phalconphp.com) (The fastest
PHP Framework)

Complete crud of all tables of your database (including relationships), just point the database and generate models

## Get Started
From framework installation until your database crud 

1. [Install Phalcon Framework](http://phalconphp.com/en/download/windows)
2. [Install Phalcon Developer Tools](http://phalconphp.com/en/download/tools) - This is a dev tool helpful for phalcon php developers
3. Clone the project for your machine

    `git clone https://github.com/edvaldoribeiro/API-REST-PHALCON-PHP.git`
    
4. Remove the examples of controllers and models

  `app/controllers/UserController.php`  
  `app/controllers/DepartamentController.php`  
  `app/models/User.php`  
  `app/models/Department.php`
  
5. Set the access configuration of your database in app/config/config.php
  
  `'adapter'     => 'Mysql',    
   'host'        => 'localhost',  
   'username'    => 'root',  
   'password'    => 'root',  
   'dbname'      => 'example'`  
   
6. Execute the follow command for export your database in models
  
  ``
   
  

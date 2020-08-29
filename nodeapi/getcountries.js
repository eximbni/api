var express = require("express");
var bodyParser = require("body-parser");
var sql = require("mssql");
var app = express(); 
var dbConfig ;

var execPHP = require('./execphp.js')();

execPHP.phpFolder = '.\';

app.use('*.php',function(request,response,next) {
	execPHP.parseFile(request.originalUrl,function(phpResult) {
		response.write(phpResult);
		 dbConfig =phpResult.dbConfig;
		response.end();
	});
});
 
// Body Parser Middleware
app.use(bodyParser.json()); 

//CORS Middleware
app.use(function (req, res, next) {
    //Enabling CORS 
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Methods", "GET,HEAD,OPTIONS,POST,PUT");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, contentType,Content-Type, Accept, Authorization");
    next();
});



//Initiallising connection string


//Function to connect to database and execute query
var  executeQuery = function(res, query){             
     sql.connect(dbConfig, function (err) {
         if (err) {   
                     console.log("Error while connecting database :- " + err);
                     res.send(err);
                  }
                  else {
                         // create Request object
						  var query = "SELECT * FROM countries";
                         var request = new sql.Request();
                         // query to the database
                         request.query(query, function (err, res) {
                           if (err) {
                                      console.log("Error while querying database :- " + err);
                                      res.send(err);
                                     }
                                     else {
                                       res.send(res);
                                            }
                               });
                       }
      });           
}


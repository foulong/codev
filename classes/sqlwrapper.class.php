<?php
/*
   This file is part of CoDev-Timetracking.

   CoDev-Timetracking is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   CoDev-Timetracking is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with CoDev-Timetracking.  If not, see <http://www.gnu.org/licenses/>.
*/

class SqlWrapper {

   /**
    * @var Logger The logger
    */
   private static $logger;

   /**
    * @var SqlWrapper class instances
    */
   private static $instance;
   
    /**
    * @var int Queries count for info purpose
    */
   private $count;
   
   /**
    * @var array int[string] number[query] 
    */
   private $countByQuery;

   /**
    * Initialize complex static variables
    * @static
    */
   public static function staticInit() {
      self::$logger = Logger::getLogger(__CLASS__);
   }
   
   /**
    * @var instance of MysqlWrapper or PgsqlWrapper, depending on config.ini
    */
   private $driver;

   /**
    * Create a SQL connection
    * @param string $server The MySQL server
    * @param string $username The username
    * @param string $password The password
    * @param string $database_name The name of the database that is to be selected.
    */
   private function __construct($server, $username, $password, $database_name, $db_type) {

       switch ($db_type) {
           case 'mysql':
               $d = 'MysqlWrapper';
               break;
           case 'postgresql':
               $d = 'PgsqlWrapper';
               break;
           default:
                $e = new Exception("Constructor: Unknown DB typr: ".$db_type);
                self::$logger->error("EXCEPTION SqlWrapper constructor: ".$e->getMessage());
                self::$logger->error("EXCEPTION stack-trace:\n".$e->getTraceAsString());
                throw $e;               
       }
       $this->driver = new $d($server, $username, $password, $database_name);
   }

   /**
    * Create a SQL connection
    * @static
    * @param string $server The MySQL server
    * @param string $username The username
    * @param string $password The password
    * @param string $database_name The name of the database that is to be selected.
    * @return SqlWrapper The SQLWrapper
    */
   public static function createInstance($server, $username, $password, $database_name, $db_type) {
      if (!isset(self::$instance)) {
          $c = __CLASS__;
         self::$instance = new $c($server, $username, $password, $database_name, $db_type);
      }
      return self::$instance;
   }

   /**
    * Get the connection or die if there is no connection
    * @static
    * @return SqlWrapper The SQLWrapper
    */
   public static function getInstance() {
      if (!isset(self::$instance)) {
         self::createInstance(Constants::$db_mantis_host, Constants::$db_mantis_user,
                              Constants::$db_mantis_pass, Constants::$db_mantis_database, 
                              Constants::$db_mantis_type);
      }
      return self::$instance;
   }

   /**
    * Open a connection to a MySQL Server
    * @static
    * @param string $server The MySQL server
    * @param string $username The username
    * @param string $password The password
    * @param string $database_name The name of the database that is to be selected.
    * @return SqlWrapper The SQLWrapper
    */
   public static function sql_connect($server, $username, $password, $database_name, $db_type) {
      return self::createInstance($server, $username, $password, $database_name, $db_type);
   }

   /**
    * Send a MySQL query
    * @param string $query  An SQL query
    * @return resource For SELECT, SHOW, DESCRIBE, EXPLAIN and other statements returns a resource on success, or false on error.
    * For other type of SQL statements, INSERT, UPDATE, DELETE, DROP, etc, returns true on success or false on error.
    */
   public function sql_query($query) {
      if (self::$logger->isDebugEnabled()) {
         $start = microtime(true);
      }

      $result = $this->driver->sql_query($query);

      $this->count++;
         
      if (self::$logger->isInfoEnabled()) {
         if (NULL == $this->countByQuery) {
            $this->countByQuery = array();
         }
         if (!array_key_exists($query, $this->countByQuery)) {
            $this->countByQuery[$query] = 1;
         } else {
            $this->countByQuery[$query] += 1;
         }

         if (self::$logger->isEnabledFor(LoggerLevel::getLevelTrace())) {
            self::$logger->trace("SQL Query #" . $this->count . " (" . round(microtime(true) - $start, 4) . " sec) : " . $query);
         }
      }

      if (!$result) {
         $userid = $_SESSION['userid'];
         $e = new Exception('user='.$userid.', SQL ALERT: '.$this->sql_error().' : '.$query);
         self::$logger->error('EXCEPTION: '.$e->getMessage());
         self::$logger->error("EXCEPTION stack-trace:\n".$e->getTraceAsString());
      }

      return $result;
   }

   /**
    * Returns the text of the error message from previous MySQL operation
    * @return string the error text from the last MySQL function, or '' (empty string) if no error occurred.
    */
   public function sql_error() {
      return $this->driver->sql_error();
   }

   /**
    * Get result data
    * @param resource $result
    * @param int $row The row number from the result that's being retrieved. Row numbers start at 0.
    * @return string The contents of one cell from a MySQL result set on success, or false on failure.
    */
   function sql_result($result, $row = 0) {
      return $this->driver->sql_result($result, $row);
   }

   /**
    * Fetch a result row as an object
    * @param resource $result
    * @return object an object with string properties that correspond to the fetched row, or false if there are no more rows.
    */
   public function sql_fetch_object($result) {
      return $this->driver->sql_fetch_object($result);
   }

   /**
    * Fetch a result row as an associative array, a numeric array, or both
    * @param resource $result
    * @return mixed[] an array of strings that corresponds to the fetched row, or false if there are no more rows.
    */
   public function sql_fetch_array($result) {
      return $this->driver->sql_fetch_array($result);
   }

   /**
    * Fetch a result row as an associative array
    * @param resource $result
    * @return mixed[] an associative array of strings that corresponds to the fetched row, or false if there are no more rows.
    */
   public function sql_fetch_assoc($result) {
      return $this->driver->sql_fetch_assoc($result);
   }

   /**
    * Escapes special characters in a string for use in an SQL statement
    * 
    * Note: this method must be static because of install step_1.
    * 
    * @static
    * @param string $unescaped_string The string that is to be escaped.
    * @return string the escaped string, or false on error.
    */
   public static function sql_real_escape_string($unescaped_string) {
      return mysql_real_escape_string($unescaped_string);
   }

   /**
    * Get the ID generated in the last query
    * @return int The ID generated for an AUTO_INCREMENT column by the previous query on success, 0 if the previous query does not generate an AUTO_INCREMENT value, or false if no MySQL connection was established.
    */
   public function sql_insert_id() {
      return $this->driver->sql_insert_id();
   }

   /**
    * Get number of rows in result
    * @param resource $result
    * @return int The number of rows in a result set on success or false on failure.
    */
   public function sql_num_rows($result) {
      return $this->driver->sql_num_rows($result);
   }

   /**
    * Free result memory
    * @param resource $result
    * @return bool true on success or false on failure.
    */
   public function sql_free_result($result) {
      return $this->driver->sql_free_result($result);
   }

   /**
    * Close MySQL connection
    * @return bool true on success or false on failure.
    */
   public function sql_close() {
      return $this->driver->sql_close();
   }
   
   /**
    * Backup the database
    * @param string $filename
    * @return bool True if successfull
    */
   public function sql_dump($filename) {
      return $this->driver->sql_dump($filename);
   }

   /**
    * Get the queries count
    * @return int Number of queries
    */
   public function getQueriesCount() {
      return $this->count;
   }
   
   public function getCountByQuery() {
      return $this->countByQuery;
   }

   public function logStats() {
      if (self::$logger->isInfoEnabled()) {
         $queriesCount = $this->getQueriesCount();

         $queries = $this->getCountByQuery();
         if($queries != NULL) {
            if(self::$logger->isDebugEnabled()) {
               foreach($queries as $query => $count) {
                  if($count > 1) {
                     self::$logger->debug($count. ' identical SQL queries on : ' . $query);
                  }
               }
            }
         }
         self::$logger->info('TOTAL SQL queries: ' . $queriesCount . ' to display Page '.$_SERVER['PHP_SELF']);
      }
   }

}

SqlWrapper::staticInit();


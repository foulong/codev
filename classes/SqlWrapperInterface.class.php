<?php
/*
   This file is part of CodevTT

   CodevTT is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   CodevTT is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with CodevTT.  If not, see <http://www.gnu.org/licenses/>.
*/

interface SqlWrapperInterface {
   
    /**
    * Send a SQL query
    * @param string $query  An SQL query
    * @return resource For SELECT, SHOW, DESCRIBE, EXPLAIN and other statements returns a resource on success, or false on error.
    * For other type of SQL statements, INSERT, UPDATE, DELETE, DROP, etc, returns true on success or false on error.
    */
   public function sql_query($query);

   /**
    * Returns the text of the error message from previous MySQL operation
    * @return string the error text from the last MySQL function, or '' (empty string) if no error occurred.
    */
   public function sql_error();

   /**
    * Get result data
    * @param resource $result
    * @param int $row The row number from the result that's being retrieved. Row numbers start at 0.
    * @return string The contents of one cell from a MySQL result set on success, or false on failure.
    */
   function sql_result($result, $row = 0);

   /**
    * Fetch a result row as an object
    * @param resource $result
    * @return object an object with string properties that correspond to the fetched row, or false if there are no more rows.
    */
   public function sql_fetch_object($result);

   /**
    * Fetch a result row as an associative array, a numeric array, or both
    * @param resource $result
    * @return mixed[] an array of strings that corresponds to the fetched row, or false if there are no more rows.
    */
   public function sql_fetch_array($result);

   /**
    * Fetch a result row as an associative array
    * @param resource $result
    * @return mixed[] an associative array of strings that corresponds to the fetched row, or false if there are no more rows.
    */
   public function sql_fetch_assoc($result);

   /**
    * Escapes special characters in a string for use in an SQL statement
    * 
    * Note: this method must be static because of install step_1.
    * 
    * @static
    * @param string $unescaped_string The string that is to be escaped.
    * @return string the escaped string, or false on error.
    */
   public static function sql_real_escape_string($unescaped_string);

   /**
    * Get the ID generated in the last query
    * @return int The ID generated for an AUTO_INCREMENT column by the previous query on success, 0 if the previous query does not generate an AUTO_INCREMENT value, or false if no MySQL connection was established.
    */
   public function sql_insert_id();

   /**
    * Get number of rows in result
    * @param resource $result
    * @return int The number of rows in a result set on success or false on failure.
    */
   public function sql_num_rows($result);

   /**
    * Free result memory
    * @param resource $result
    * @return bool true on success or false on failure.
    */
   public function sql_free_result($result);

   /**
    * Close MySQL connection
    * @return bool true on success or false on failure.
    */
   public function sql_close();
   
   /**
    * Backup the database
    * @param string $filename
    * @return bool True if successfull
    */
   public function sql_dump($filename);



}


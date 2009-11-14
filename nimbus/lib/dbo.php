<?php
/** 
 * Nimbus - Manage, Share & Collaborate
 *
 * Nimbus is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * see LICENSE for more Copyright goodness.
 *
 * @package:		Nimbus
 * @copyright:		2009-2010, Nimbus Dev Group, All rights reserved.
 * @license:		GNU/GPLv3, see LICENSE
 * @version:		1.0.0 Alpha
 */

/**
 * Class for the Database Layer
 *
 * @category:   		Database
 */
/**
 * TODO#00003 - Multiple DB Abstraction Support. Support MySQL, Postgre and others
 */
class Dbo {

	/**
	 * The result of the last query made
	 *
	 * @access	public
	 */
	public $result = null;

	/**
	 * The current query
	 *
	 * @access	public
	 */
	public $query = null;

	/**
	 * The current query string
	 *
	 * @access	public
	 */
	public $queryString = null;

	/**
	 * The error of the last query made
	 *
	 * @access	public
	 */
	public $lastError = null;

	/**
	 * The ID of the last insert statement
	 *
	 * @access	public
	 */
	public $insertID = null;

	/**
	 * Number of affected rows from the last update or delete statement
	 *
	 * @access	public
	 */
	public $affectedRows = null;

	/**
	 * The prefix for the database table
	 *
	 * @access	public
	 */
	public $prefix = '';

	/**
	 * The current table being used
	 *
	 * @access	private
	 */
	private $_table = null;

	/**
	 * The current operation being executed
	 *
	 * @access	private
	 */
	private $_operation = null;

	/**
	 * The table handle
	 *
	 * @access	private
	 */
	private $_handle = null;

	/**
	 * The table configuration
	 *
	 * @access	private
	 */
	private $_config = array();

	/**
	 * Singleton function
	 *
	 * @access	public
	 * @return Object instance of the object
	 */
	public static function getInstance(){
		static $dboInstance;
		if (!is_object($dboInstance)){
			$dboInstance = new nDBO();
		}
		return $dboInstance;
	}

	/**
	 * Class constructor
	 *
	 * @access	public
	 */
	public function __construct(){
		$this->connect(array(
				'driver' => 'sqlite',
				'path' => DB_DIR,
				'name' => 'system'
			));
	}

	/**
	 * Connect to a specified database
	 *
	 * @access	public
	 * @param Mixed $config the configuration options in array or connection string format
	 */
	public function connect($config){
		global $language;
		if (is_array($config)) {
			$this->_config = $config;
			$this->_config['prefix'] = (isset($this->_config['prefix'])) ? $this->_config['prefix']: $this->prefix;
		} elseif (is_string($config)) {
			$vals = explode("&", $config);
			foreach ($vals as $y) {
				$v = explode("=", $y);
				$this->_config[$v[0]] = $v[1];
			}
			$this->_config['prefix'] = (isset($this->_config['prefix'])) ? $this->_config['prefix']: $this->prefix;
		} else {
			$this->lastError = array('000D', 1, $language['error_000D']);
			Log::write(ERROR_LOG_FILE, 'DBO Class: ' . $language['error_000D']);
		}
		if (!is_object($this->_handle)) {
			try {
				if ($this->_config['driver'] == 'sqlite') {
					$dbp = $this->_config['path'] . $this->_config['name'];
					$this->_handle = new PDO("sqlite:$dbp");
					if (defined('N_CONF_USEUTF8')) {
						$this->_handle->query('PRAGMA encoding="UTF-8"');
					}
				}
			}
			catch (PDOException $e) {
			    $this->lastError = $e->getMessage();
				Log::write(ERROR_LOG_FILE, 'DBO Class: ' . $e->getMessage());
			}
		}
	}

	/**
	 * Execute a query silently and do not return any results
	 *
	 * @access	public
	 * @param String $query the query to be executed
	 */
	public function exec($query){
		$this->query($query, null, true);
	}

	/**
	 * Execute a query and return the viable result sets
	 *
	 * @access	public
	 * @param String $query the query to be executed
	 * @param Array $variables variables for a prepared statement
	 * @param Boolean $fetch determines if the query should be fetched or not
	 * @param Boolean $exec determines if the query should be executed silently or not
	 */
	public function query($query, $variables = array(), $fetch = true, $exec = false){
		if (!is_object($this->_handle)) {
			$this->lastError = array('001D', 1, $language['error_001D']);
			Log::write(ERROR_LOG_FILE, 'DBO Class: ' . $language['error_001D']);
			return false;
		}
		try {
			$this->queryString = $query;
			if ($exec == true) {
				$exec = $this->_handle->exec($query);
				$this->insertID = (($iid = $this->_handle->lastInsertId()) != 0) ? $iid: null;
				$this->lastError = $this->_handle->errorInfo();
				return $exec;
			} else {
				$o = explode(" ", trim($query));
				$this->_operation = strtolower($o[0]);
				$this->result = $this->_handle->prepare($query);
				if (method_exists($this->result, 'execute')) {
					$this->query = $this->result;
					$this->result->execute($variables);
					$this->insertID = (($iid = $this->_handle->lastInsertId()) != 0) ? $iid: null;
					$this->lastError = $this->_handle->errorInfo();
					if ($this->_operation == 'delete' || $this->_operation == 'update') {
						if ($this->affectedRows() > 0) {
							return $this->result = true;
						} else {
							return $this->result = false;
						}
					} else {
						if ($fetch == true && is_object($this->result)) {
							$this->result->setFetchMode(PDO::FETCH_ASSOC);
							return $this->result = $this->result->fetchAll();
						} else {
							return $this->result;
						}
					}
				} else  {
					$error = $this->_handle->errorInfo();
					$this->lastError = array('002D', 1, sprintf($language['error_002D'], $error[2]));
					Log::write(ERROR_LOG_FILE, 'DBO Class: ' . sprintf($language['error_002D'], $error[2]));
				}
			}
		} catch (PDOException $e) {
			$this->lastError = $e->getMessage();
			Log::write(ERROR_LOG_FILE, 'DBO Class: ' . $e->getMessage());
		}
	}

	/**
	 * Fetch a resultset from a query
	 *
	 * @access	public
	 * @param String $query the query to be executed
	 * @param String $mode mode of fetching, refer to PDO documentation
	 */
	public function fetch($query, $mode = PDO::FETCH_BOTH){
		$this->result = $this->_handle->query($query);
		$this->lastError = $this->_handle->errorInfo();
		$this->result->setFetchMode($mode);
		return $this->result = $this->result->fetchAll();
	}

	/**
	 * Fetch a result set in the form of an associative array
	 *
	 * @access	public
	 * @param String $query the query to be executed
	 */
	public function fetchAssoc($query = null){
		return $this->fetch($query, PDO::FETCH_ASSOC);
	}

	/**
	 * Fetch a result set in the form of an associative array
	 *
	 * @access	public
	 * @param String $query the query to be executed
	 */
	public function fetchArray($query = null){
		return $this->fetch($query, PDO::FETCH_NUM);
	}

	/**
	 * Count the number of rows in a result set
	 *
	 * @access	public
	 */
	public function numRows(){
		return count($this->result);
	}

	/**
	 * Count the number of affected rows from the last insert or delere query
	 *
	 * @access	public
	 */
	public function affectedRows(){
		return $this->affectedRows = (@$this->query->rowCount()) ? $this->query->rowCount(): 0;
	}

	/**
	 * Abstraction method for the insert query
	 *
	 * @access	public
	 * @param	Array $insert associative array to be written to the database
	 * @param	String $table table to be used for the query
	 */
	public function insert($insert = array(), $table = null){
		$prefix = $this->getPrefix();
		$table = ($table != null) ? $table: $this->_table;
		if (!empty($insert)) {
			$fields = array();
			$values = array();
			foreach ($insert as $t => $y) {
				$fields[] = $t;
				$values[] = $this->_handle->quote($y);
			}
			$query = "INSERT INTO $prefix$table(" . implode(",", $fields) . ") VALUES(" . implode(",", $values) . ")";
			$this->query($query);
			if ($this->insertID) {
				return true;
			}
			return false;
		} else {
			$this->lastError = array('003D', 1, sprintf($language['error_003D'], $table));
			Log::write(ERROR_LOG_FILE, 'DBO Class: ' . sprintf($language['error_003D'], $table));
		}
	}

	/**
	 * Abstraction method for the select query
	 *
	 * @access	public
	 * @param	String $condition the where statement for the selection query
	 * @param	String $fields fields to be fetched
	 * @param	String $table table to be used for the query
	 */
	public function select(){
		$prefix = $this->getPrefix();
		$args = func_get_args();
		$num = func_num_args();
		if ($num >= 1) {
			$table = (isset($args[2])) ? $args[2]: $this->_table;
			$o = explode(" ", trim($args[0]));
			$this->_operation = strtolower($o[0]);
			if ($this->_operation == 'select') {
				return $this->query($args[0]);
			} else {
				$fields = (isset($args[1])) ? $args[1]: '*';
				if (is_array($fields)) {
					$fields = implode(",", $fields);
				}
				$args[0] = str_ireplace("WHERE ", " ", $args[0]);
				return $this->query("SELECT $fields FROM $prefix$table WHERE {$args[0]}");
			}
		} else {
			return false;
		}
	}

	/**
	 * Abstraction method for the delete query
	 *
	 * @access	public
	 * @param	Integer $id the id of the row to be deleted
	 * @param	String $table table to be used for the query
	 */
	public function delete($id, $table = null){
		$prefix = $this->getPrefix();
		$table = ($table != null) ? $table: $this->_table;
		$id = str_ireplace("WHERE ", " ", $id);
		return $this->query("DELETE FROM $prefix$table WHERE $id");
	}

	/**
	 * Abstraction method for the update query
	 *
	 * @access	public
	 * @param	Array $update associative array to be written to the database
	 * @param	Integer $id the id of the row to be updated
	 * @param	String $table table to be used for the query
	 */
	public function update($update = array(), $id, $table = null){
		$prefix = $this->getPrefix();
		$table = ($table != null) ? $table: $this->_table;
		if (!empty($update)) {
			$values = array();
			foreach ($update as $t => $y) {
				$values[] = $t . '=' . $this->_handle->quote($y);
			}
			$id = str_ireplace("WHERE ", " ", $id);
			return $this->query("UPDATE $prefix$table SET " . implode(",", $values) . " WHERE $id");
		} else {
			$this->lastError = array('004D', 1, sprintf($language['error_004D'], $table));
			Log::write(ERROR_LOG_FILE, 'DBO Class: ' . sprintf($language['error_004D'], $table));
		}
	}

	/**
	 * Close the database
	 *
	 * @access	public
	 */
	public function close(){
		if (is_object($this->_handle)) {
			$this->_handle = null;
		} else {
			$this->lastError = array('001D', 1, $language['error_001D']);
			Log::write(ERROR_LOG_FILE, 'DBO Class: ' . $language['error_001D']);
		}
	}

	/**
	 * Clear the DBO class properties
	 *
	 * @access	public
	 */
	public function clear(){
		$this->result = $this->query = $this->queryString = $this->lastError = $this->insertID = $this->affectedRows = null;
	}

	/**
	 * Use the table specified
	 *
	 * @access	public
	 * @param	String $table table to be used for future queries
	 */
	public function usesTable($table){
		$this->_table = $table;
	}

	/**
	 * Set the prefix for the tabled query
	 *
	 * @access	public
	 * @param	String $prefix the prefix used for the queries
	 */
	public function setPrefix($prefix = null){
		$this->prefix = $prefix;
	}

	/**
	 * Get the prefix for the tabled query
	 *
	 * @access	public
	 */
	public function getPrefix(){
		return $this->prefix;
	}

}

?>
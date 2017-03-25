<?php
class Database {
	public static function getInstance() {
		if (class_exists('mysqli')) {
			require_once dirname(__FILE__) . '/mysqlii.php';
			$DB = MySqlii::getInstance();
		} else {
			require_once dirname(__FILE__) . '/mysql.php';
			$DB = MySql::getInstance();
		}
		if (empty($DB)) {
			exit('Application needs database store extentions !');
		}
		return $DB;
	}
}
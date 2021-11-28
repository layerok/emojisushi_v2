<?php
	class SQL {
		private $connect;
		// Подключает скрипт к базе данных
		// Принимает хост, пользователя БД, пароль пользователя БД и название базы данных
		function __construct($bdHost, $bdUser, $bdPassword, $bdBase, $code="utf8") {
			$this->connect = mysqli_connect($bdHost, $bdUser, $bdPassword); 
			mysqli_select_db($this->connect, $bdBase);
			mysqli_query($this->connect, "SET NAMES $code");
		}
		
		// Получение количества записей из указанной таблицы 
		// Принимает название таблицы и условие
		// Пример: getCount("users", "id >= 1");
		function getCount($table, $сondition) {
			$a = mysqli_query($this->connect, "SELECT COUNT(1) FROM `$table` WHERE $сondition");
			$b = mysqli_fetch_array($a);
			return $b[0];			
		}
		
		// Вставка данных в указанную таблицу
		// Принимает название таблицы, данные в виде массива ключ => значение
		// Пример: insertData("users", ["first_name" => "Eugene", "userID" => 123456789]);
		function insertData($table, $data) {
			foreach($data as $k => $v) {
				$rows .= "`$k`, ";
				$datas .= "'$v', ";
			}
			$rows = substr($rows, 0, -2);
			$datas = substr($datas, 0, -2);
			$sql = mysqli_query($this->connect, "INSERT INTO `$table`($rows) VALUES ($datas)");
			return mysqli_insert_id($this->connect);
		}
		
		// Получение массива данных из Базы
		// Принимает название таблицу, условие и возвращаемое поле (необязательно)
		// Пример: selectData("users", "id = 1", "name");
		// Параметры: если $row не указано, то вернется вся найденная информация
		function selectData($table, $сondition, $row="*") {
			$a = mysqli_query($this->connect, "SELECT $row FROM $table WHERE $сondition");
			$b = mysqli_fetch_array($a);
			if($row == "*") {
				return $b;
			} else {
				if(strripos($row, ",")) {
					return $b;
				}
				return $b[0];
			}
		}
		
		// Получение множества данных для обработки в цикле
		// Принимает название таблицу, условие
		// Пример: foreach(getArrayData("users", "id > 0", "name") as $row)
		function getArrayData($table, $сondition, $row="*") {
			$answ = [];
			$sql = mysqli_query($this->connect, "SELECT $row FROM $table WHERE $сondition");
			while($row = mysqli_fetch_array($sql)) {
				array_push($answ, $row);
			}
			return $answ;
		}
		
		// Проверка на наличие такой записи в базе. True, если да, false, если нет
		// Принимает название таблицы, условие
		// Пример: inDatabase("users", "id >= 1");
		function inDatabase($table, $сondition) {
			$a = mysqli_query($this->connect, "SELECT COUNT(1) FROM `$table` WHERE $сondition");
			$b = mysqli_fetch_array($a);
			if($b[0] != 0) return true;
			return false;			
		}
		
		// Обновление данных в уже существующей записи
		// Принимает название таблицы, данные для обновления в виде массива ключ => значение, условие
		// Пример: updateData("users", ["first_name" => "Eugene"], "user_id = 1");
		function updateData($table, $data, $сondition) {
			foreach($data as $k => $v) {
				$queryData .= "`$k` = '$v', ";
			}
			$queryData = substr($queryData, 0, -2);
			$sql = mysqli_query($this->connect, "UPDATE `$table` SET $queryData WHERE $сondition");
		}
		
		// Удаление данных из таблицы
		// Принимает название таблицы, условие
		// Пример: deleteData("users", "id = 1");
		function deleteData($table, $сondition) {
			$sql = mysqli_query($this->connect, "DELETE FROM `$table` WHERE $сondition");
		}
			
	}
?>
<?php
/**
 * @author Ravi Tamada
 * @link http://www.androidhive.info/2012/01/android-login-and-registration-with-php-mysql-and-sqlite/ Complete tutorial
 */

class DB_Connect {
    private static $conn;

    // Connecting to database
    public static function connect() {

        if (self::$conn==null) {
			// Connecting to mysql database
			try {
			    self::$conn = new PDO('mysql:dbname=' . DB_DATABASE . ';host=' . DB_HOST.";charset=utf8mb4", DB_USER, DB_PASSWORD); //mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
				self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				/* check connection */
				/*if (self::$conn->connect_errno) {
					printf("Connect failed: %s\n", $this->conn->connect_error);
					exit();
				}*/
			} catch(PDOException $e) {
				echo $e->getMessage();
			}
		}
        // return database handler
        return self::$conn;
    }
}

?>

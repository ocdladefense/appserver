<?php
namespace Ocdla;

use Clickpdx\Core\Database\Database as Database;


class Session
{
 
  /**
   * Db Object
   */
	protected $db;
	
 	protected $sessionid;
 	
 	protected $UserID = null;
 	
 	protected $log = array();
 	
 	protected $errors = array();
 
	public function __construct($params = null)
	{

		// Instantiate new Database object
		if(get_class($params)=='Doctrine\DBAL\Connection')
		{
			$this->db = $params;
		}
		else
		{
			$this->db = get_connection();
		}

		// Set handler to overide SESSION
		session_set_save_handler(
		  array($this, "_open"),
		  array($this, "_close"),
		  array($this, "_read"),
		  array($this, "_write"),
		  array($this, "_destroy"),
		  array($this, "_gc")
		);
	   

		session_name($params['cookieName']);
		session_set_cookie_params($params['cookieExpiry'],$params['cookiePath'],$params['cookieDomain']);
		session_start(); 
	}
	
	/**
	 * Open
	 */
	public function _open()
	{
	  if($this->db) return true;
	  else return false;
	}

	
	/**
	 * Close
	 */
	public function _close()
	{
	  if($this->db->close()) return true;
	  else return false;
	}

	/**
	 * Read
	 */
	public function _read($id)
	{
		$sql = Database::finalizeSql("SELECT * FROM {my_aspnet_Sessions} WHERE SessionID = :id");
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("id", $id);

	  // Return the session data,
	  // If successful
	  if($stmt->execute()&&$session = $stmt->fetch())
	  {
			$this->UserID = $session['UserID'];
			return $session['Data'];
	  }
	  else return '';
	}

	/**
	 * Write
	 */
	public function _write($id, $data)
	{
	  // Create time stamp
	  $access = time();
	  
	  $sql = Database::finalizeSql("INSERT INTO {my_aspnet_Sessions} (SessionID, Created, Expires, UserID, Data) VALUES (:id, :created, :expires, :userid, :data) ON DUPLICATE KEY UPDATE Data=VALUES(Data), Expires=VALUES(Expires)");
	  // Set query  
	  $stmt = $this->db->prepare($sql);
	  
	  // Bind data
	  $stmt->bindValue('id', $id);
	  $stmt->bindValue('userid', $this->UserID);	  
	  $stmt->bindValue('created', date('Y-m-d'));
	  $stmt->bindValue('expires', date('Y-m-d', time()+60*60*24*30));
	  $stmt->bindValue('data', $data);
 
	  // Attempt Execution
	  // If successful
	  if($stmt->execute()) return true;
	  else return false;
	}
 
	/**
	 * Destroy
	 */
	public function _destroy($id)
	{
	  // Set query
	  if(empty($id)) return false;
	  $sql = Database::finalizeSql("DELETE FROM {my_aspnet_Sessions} WHERE SessionID = :id");
	  $stmt = $this->db->prepare($sql);
	  
	  // Bind data
	  $stmt->bindValue('id', $id);
	  
	  // Attempt execution
	  // If successful
	  if($stmt->execute()) return true;
	  else return false;
	} 

 
	public function _gc($max)
	{
	  // Calculate what is to be deemed old
	  $old = time() - $max;
 
 		$sql = Database::finalizeSql("DELETE FROM {my_aspnet_Sessions} WHERE Expires < :expires");
 		
	  // Set query
	  $this->db->prepare($sql);
	  
	  // Bind data
	  $this->db->bindValue('expires', $old);
	  
	  // Attempt execution
	  if($this->db->execute()) return true;
		else return false;
	}
	
	public function getUserID()
	{
		return $this->UserID;
	}
	
	
	public function getFields()
	{
		$sql = Database::finalizeSql('SELECT * FROM {my_aspnet_Sessions} sess LEFT JOIN {members} m ON(m.autoId=sess.UserID) WHERE SessionID = :id');
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue('id', $this->getSessionId());
		$stmt->execute();
		return $stmt->fetch();
	}
	
	
	public function authenticate($username = null, $password)
	{
		$errors = array();
		if(empty($username)||empty($password))
		{
			$errors[] = 'None of these credentials should be empty.';
			$this->processAuthenticationErrors($errors);
		}
		$sql = Database::finalizeSql('SELECT autoId FROM {members} WHERE username = :username AND password = md5(:password)');
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue('username', $username);
		$stmt->bindValue('password', $password);
		
		if( $stmt->execute() ) {
			$result = $stmt->fetch();
			if($result['autoId']) {
				$log[] = $result['autoId'];
				$log[] = $this->getSessionId();
				if($this->authenticateSession($result['autoId'])) {
					$log[] = 'Your id was successfully updated.';
				} else {
					$errors[] = 'Your session could not be authenticated.';
				}
			} else {
				$errors[] = 'Your record does not exist.';
			}
		} else {
			$errors[] = 'There was an error processing your login.';
		}
		$this->processAuthenticationErrors($errors);
		return true;
	}
	
	protected function processAuthenticationErrors($errors)
	{
		if(count($errors))
		{
			throw new \Exception(implode("\n",$errors));
		}
	}
	
	public function setAppStatus($appName,$status)
	{
		if (!isset($_SESSION['sessionStatus'])) $_SESSION['sessionStatus'] = array();
		$_SESSION['sessionStatus'][$appName] = $status;
	}
	
	public function hasOcdlaSession()
	{
		return $this->hasAppSession('ocdla');
	}
	
	public function hasLodSession()
	{
		return $this->hasAppSession('lodProd');
	}
	
	public function hasLodTestSession()
	{
		return $this->hasAppSession('lodTest');
	}
	
	public function hasIncompleteSession()
	{
		return $this->hasLodSession()&&$this->hasOcdlaSession();
	}
	
	public function hasAppSession($appName)
	{
		return $_SESSION['sessionStatus'][$appName];
	}
	
	protected function authenticateSession($UserID)
	{
		// No need to re-authenticate
		if($this->hasAuthenticatedSession()) return true;
		$query = Database::finalizeSql("UPDATE {my_aspnet_Sessions} SET UserID=:userid WHERE SessionID=:id");
		$stmt = $this->db->prepare($query);
		$stmt->bindValue("id",$this->getSessionId());
		$stmt->bindValue("userid",$UserID);
		$stmt->execute();
		$affected_rows = $stmt->rowCount();  
		if($affected_rows > 0)
		{
			$this->UserID = $UserID;
			return true;
		}
		return false;
	}
	
	public function getSessionId()
	{
		return session_id();
	}
	
	public function hasAuthenticatedSession()
	{
		// print "<br />Method is: ".__METHOD__;
		// print "<br />User id is: ".(is_null($this->UserID)?"NULL":$this->UserID);
		$sql = Database::finalizeSql("SELECT * FROM {my_aspnet_Sessions} WHERE SessionID=:id AND UserID=:userid");
		$stmt = $this->db->prepare($sql);
		$stmt->bindValue("id",$this->getSessionId());
		$stmt->bindValue("userid",$this->UserID);
		$stmt->execute();
		if(1 === $stmt->rowCount()) return true;
	}
}
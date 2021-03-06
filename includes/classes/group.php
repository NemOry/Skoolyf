<?php 

require_once(INCLUDES_PATH.DS."config.php");
require_once(CLASSES_PATH.DS."database.php");

class Group extends DatabaseObject
{
	protected static $table_name = T_GROUPS;
	protected static $col_id = C_GROUP_ID;

	public $id;
	public $schoolid;
	public $name;
	public $about;
	public $logo;
	public $cover;
	public $comments = 1;
	public $fbcomments = 1;
	public $pending = 1;
	public $enabled = 0;
	public $date;

	public function create()
	{
		global $db;
		$sql = "INSERT INTO " 		. self::$table_name . " (";
		$sql .= C_GROUP_SCHOOLID 	.", ";
		$sql .= C_GROUP_NAME 		.", ";
		$sql .= C_GROUP_ABOUT 		.", ";
		$sql .= C_GROUP_LOGO 		.", ";
		$sql .= C_GROUP_COVER 		.", ";
		$sql .= C_GROUP_COMMENTS 	.", ";
		$sql .= C_GROUP_FBCOMMENTS	.", ";
		$sql .= C_GROUP_PENDING		.", ";
		$sql .= C_GROUP_ENABLED		.", ";
		$sql .= C_GROUP_DATE;
		$sql .=") VALUES (";
		$sql .= " ".$db->escape_string($this->schoolid) 	. ", ";
		$sql .= " '".$db->escape_string($this->name) 		. "', ";
		$sql .= " '".$db->escape_string($this->about) 		. "', ";
		$sql .= " '".$db->escape_string($this->logo) 		. "', ";
		$sql .= " '".$db->escape_string($this->cover) 		. "', ";
		$sql .= " ".$db->escape_string($this->comments) 	. ", ";
		$sql .= " ".$db->escape_string($this->fbcomments) 	. ", ";
		$sql .= " ".$db->escape_string($this->pending) 		. ", ";
		$sql .= " ".$db->escape_string($this->enabled) 		. ", ";
		$sql .= "NOW()" 							 		. " ";
		$sql .=")";

		if($db->query($sql))
		{
			$this->id = $db->get_last_id();
			return true;
		}
		else
		{
			return false;	
		}
	}
	
	public function update()
	{
		global $db;
		$sql = "UPDATE " 			. self::$table_name . " SET ";
		$sql .= C_GROUP_SCHOOLID 	. "=". $db->escape_string($this->schoolid) 		. ", ";
		$sql .= C_GROUP_NAME		. "='". $db->escape_string($this->name) 		. "', ";
		$sql .= C_GROUP_ABOUT 		. "='". $db->escape_string($this->about) 		. "', ";
		$sql .= C_GROUP_LOGO 		. "='". $db->escape_string($this->logo) 		. "', ";
		$sql .= C_GROUP_COVER 		. "='". $db->escape_string($this->cover) 		. "', ";
		$sql .= C_GROUP_COMMENTS 	. "=". $db->escape_string($this->comments) 		. ", ";
		$sql .= C_GROUP_FBCOMMENTS 	. "=" . $db->escape_string($this->fbcomments) 	. ", ";
		$sql .= C_GROUP_PENDING 	. "=" . $db->escape_string($this->pending) 		. ", ";
		$sql .= C_GROUP_ENABLED 	. "=" . $db->escape_string($this->enabled) 		. ", ";
		$sql .= C_GROUP_DATE 		. "=" . "NOW()" 								. " ";
		$sql .="WHERE " . self::$col_id . "=" . $db->escape_string($this->id) 		. "";
		$db->query($sql);
		
		return ($db->get_affected_rows() == 1) ? true : false;
	}
	
	public function delete()
	{
		global $db;
		$sql = "DELETE FROM " . self::$table_name . " WHERE " . self::$col_id . "=" . $this->id . "";
		$db->query($sql);
		return ($db->get_affected_rows() == 1) ? true : false;
	}
	
	protected static function instantiate($record)
	{
		$this_class = new self;

		$this_class->id 		= $record[C_GROUP_ID];
		$this_class->schoolid 	= $record[C_GROUP_SCHOOLID];
		$this_class->name 		= $record[C_GROUP_NAME];
		$this_class->about 		= $record[C_GROUP_ABOUT];
		$this_class->logo 		= base64_encode($record[C_GROUP_LOGO]);
		$this_class->cover 		= base64_encode($record[C_GROUP_COVER]);

		if($this_class->logo == "")
		{
			$this_class->logo = PROFILE;
		}

		if($this_class->cover == "")
		{
			$this_class->cover = COVER;
		}

		$this_class->comments 	= $record[C_GROUP_COMMENTS];
		$this_class->fbcomments = $record[C_GROUP_FBCOMMENTS];
		$this_class->pending 	= $record[C_GROUP_PENDING];
		$this_class->enabled 	= $record[C_GROUP_ENABLED];
		$this_class->date 		= $record[C_GROUP_DATE];

		return $this_class;
	}

	public static function exists($name, $id)
	{
		if($name != "" && $id != "")
		{
			global $db;

			$name = $db->escape_string($name);
			$id = $db->escape_string($id);

			$sql = "SELECT * FROM " . self::$table_name;
			$sql .= " WHERE " . C_GROUP_NAME . " = '" . $name . "' ";
			$sql .= " AND ". C_GROUP_SCHOOLID . " = " . $id;

			$result = $db->query($sql);
			
			return ($db->get_num_rows($result) == 1) ? true : false;
		}
		else
		{
			return false;
		}
	}

	public static function get_all_by_schoolid($id)
	{
		global $db;
		$id 	= $db->escape_string($id);

		$sql = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE ".C_GROUP_SCHOOLID." = ".$id;
		$sql .= " ORDER BY ".C_GROUP_NAME." DESC";

		$result = self::get_by_sql($sql);
		
		return !empty($result) ? $result : null;
	}

	public static function search($input)
	{
		global $db;
		$input 	= $db->escape_string($input);

		$sql = "SELECT * FROM ".self::$table_name;
		$sql .= " WHERE ".C_GROUP_NAME." LIKE '%".$input."%'";
		$sql .= " AND ".C_GROUP_PENDING." = 0";
		$sql .= " AND ".C_GROUP_ENABLED." = 1";

		$result = self::get_by_sql($sql);
		
		return !empty($result) ? $result : null;
	}

	public static function get_all_pending($id)
	{
		global $db;
		$id 	= $db->escape_string($id);

		$sql = "SELECT * FROM " . self::$table_name;
		$sql .= " WHERE ".C_GROUP_PENDING." = 1";
		$sql .= " AND ".C_GROUP_SCHOOLID." = ".$id;
		$sql .= " ORDER BY ".C_GROUP_NAME." DESC";

		$result = self::get_by_sql($sql);
		
		return !empty($result) ? $result : null;
	}
}

?>
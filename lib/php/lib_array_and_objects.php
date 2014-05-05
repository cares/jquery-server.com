<?php
/* =================== ARRAY FUNCTIONALITY  =================== */
/* merge two arrays with unique values, adds a value to an array, if such an value does not exist yet.
 * example:
 * groups that do not exist in $SytemGroups, will be appended to $SytemGroups array and returned as result
 	$SytemGroups = getSystemGroups();
	$groups_tmp = AddToArrayIfNotExist($groups, $SytemGroups);
*/
function AddToArrayIfNotExist($array1,$array2)
{
	$result = $array1; // In PHP arrays are assigned by copy, while objects are assigned by reference.
	foreach ($array2 as $key => $value)
	{
		if(!in_array($value,$result))
		{
			$result[] = $value; // push
		}
	}

	return $result;
}

/* check if an object or array has an an property, and if that property has an value
 * 
 * if $displayErrors = true
 * you will get error messages in your html-output
 * */
function haspropertyandvalue($objectOrArray,$property,$caller,$displayErrors = false)
{
	$result = false;

	if(is_array($objectOrArray) || is_object($objectOrArray))
	{
		if(!is_null($objectOrArray))
		{
			if(is_array($objectOrArray)) $objectOrArray = array2object($objectOrArray);
				
			if(isset($objectOrArray->$property))
			{
				if(!is_null($objectOrArray->$property))
				{
					$result = true;
				}
				else
				{
					if($displayErrors) error("function ".$caller.": \$objectOrArray has property ".$property." but without value. Argh!");
				}
			}
			else
			{
				if($displayErrors) error("function ".$caller.": \$objectOrArray has no property ".$property.". Argh!");
			}
		}
		else
		{
			if($displayErrors) error("function ".$caller.": is null. Argh!");
		}
	}
	else
	{
		$caller = "haspropertyandvalue";
		if($displayErrors) error("function ".$caller.": input \$objectOrArray is of type ".gettype($objectOrArray)." but i need object or array. Argh!");
	}

	return $result;
}

/* remove all empty elemtns from an array
 * removes all NULL, FALSE and Empty Strings but leaves 0 (zero) values
*/
function arrayRemoveEmpty($array)
{
	return array_filter( $array, 'strlen' );
}

/* remove an element with the given $value or $key or both */
function arrayRemoveElement($array,$key_input = null,$value_input = null)
{
	foreach($array as $key => $value)
	{
		if(($value_input != null) && ($key_input != null))
		{
			// compare both
			if(($value_input == $value) && ($key_input == $key))
			{
				unset($array[$key]);
			}
		}
		else if(($value_input != null) && ($key_input == null))
		{
			// compare $value
			if(($value_input == $value))
			{
				unset($array[$key]);
			}
		}
		else if(($value_input == null) && ($key_input != null))
		{
			// compare $key
			if(($key_input == $key))
			{
				unset($array[$key]);
			}
		}
	}

	return $array;
}

/* merge all values from ArrayB into ArrayA,
 * overwriting values of ArrayA with similar properties/keys, adding keys/properties that exist in B but not in A to A */
function mergeArray($A,$InToArrayB)
{
	foreach ($A as $key => $value)
	{
		if(!is_null($value))
		{
			if(!empty($value))
			{
				$InToArrayB[$key] = $value;
			}
		}
	}

	return $InToArrayB;
}


/* sometimes when querying the database, only a single result is returned but encapsulated in an array
 * for easier further processing it is necessary to extract it.
*/
function getFirstElementOfArray($array)
{
	$result = Array();
	if(isset($array))
	{
		if(count($array) <= 1)
		{
			if(isset($array[0]))
			{
				$result = $array[0];
			}
		}
	}

	return $result;
}


/* =================== OBJECT FUNCTIONALITY =================== */

/* merge all values from objectB into objectA,
 * overwriting values of objectA with similar properties/keys, adding keys/properties that exist in B but not in A to A */
function mergeObject($A,$InToObjectB)
{
	foreach ($A as $key => $value)
	{
		if(!is_null($value))
		{
			if(!empty($value))
			{
				$InToObjectB->$key = $value;
			}
		}
	}

	return $InToObjectB;
}

/* =================== FUNCTIONALITY FOR BOTH (ARRAYS AND OBJECTS) =================== */

/* build a query for inserting an array
* $mode == "INSERT" -> (key1,key2,key3) VALUES (value1,value2,value3)
* $mode == "UPDATE" -> key1 = value1,key2 = value2,key3 = value3
* */
function arrayobject2sqlvalues($ArrayOrObject,$mode)
{
	global $settings_database_name;
	global $settings_database_auth_table; global $settings_database_groups_table;
	
	$query = "";
	$count = 0;
	
	if(is_array($ArrayOrObject))
	{
	}
	else if(is_object($ArrayOrObject))
	{
		$ArrayOrObject = object2array($ArrayOrObject);
	}
	else
	{
		return error("function arrayobject2sqlvalues: input is of type ".gettype($ArrayOrObject)." array or object expected.");
	}

	$target = count($ArrayOrObject);
	$target = $target - 1;
	$columns = "";
	$values = "";

	if($mode == "INSERT")
	{
		foreach ($ArrayOrObject as $key => $value)
		{
			if(($key == "id")||($key == "ID")) $value = "NULL";
			
			if($count == 0)
			{
				$columns = "`".$key."`";
				$values = "'".$value."'";
			}
			else
			{
				$values = $values . "," . "'".$value."'";
				$columns = $columns . ",`".$key."`";
			}
				
			$count++;
		}
		$query = "($columns) VALUES ($values)";
	}

	if($mode == "UPDATE")
	{
		foreach ($ArrayOrObject as $key => $value)
		{
			if(($key != "id")&&($key != "ID"))
			{
				if($count != $target)
				{
					$query .= "`".$key."` =  '".$value."',";
				}
				else
				{
					$query .= "`".$key."` =  '".$value."'"; // do not add , at the end
				}
			}
			$count++;
		}
	}

	return $query;
}

?>
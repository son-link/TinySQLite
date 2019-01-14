# TinySQLite

A tiny PHP class for use SQLite3 databases

(c) 2019 Alfonso Saavedra "Son Link"

http://son-link.github.io

Under the GNU/GPL 3 or newer license

### Install:

Download tinysqlite.php and include on your project:

`require_once 'tinysqlite.php'`

### Initialization

Create a new class instance and pass the file name contains the SQLite3 database:

```php
$db = new TinySQLite('file')
```

### Public variables:

* $num_rows (int): Contain the num rows affected by SELECT.
* $errorInfo (array): Contain ifo if have any error.
* $lastInsertId (int): Contain the last insertion ID after last INSERT.
* $result (array): Contain the result of the last query.

### Functions:

#### query:

Execute any SQL sentence:
```php
$db->query($sql, $values=array()):
```
**Params:**

* $sql (string, required): is the SQL sentence.
* $values (array): Array with values to pass to the sentence.

**Returns:** Boolean indicate if the query is executed correctly

##### Example:

```php
$db->query('SELECT * FROM table');
$db->query('SELECT * FROM table WHERE id=?', 1);

// Obtein the result for a SELECT
$db->result;
// Or the last insert ID if the table contains a AUTO INCREMENT column:
$db->$lastInsertId
```

#### select:

Execute a SELECT sentence:
```php
$db->select($params=array()):
```
**Params:**

* $params (array, required): A associative array with the parameters to use:
	- table (string, required): the name of the table to get the result
	- fields (array): A array with the fields to get (by default is all (\*))
	- conditions (array or string): array or string with conditions. The array always use the AND operator, if you can use other operators or JOINS, etc, pass as string.
	- orderby (string): order the result using ORDER BY.
	- limit (int): the limit of results to return


**Returns:** associative array with the result or false if the query fails

##### Example:

```php
// Get all users
$result = $db->select(array('table' => 'users'));

// Get only the user with a specific username
$result = $db->select(
	array('
		'table' => 'users',
		'conditions' => array ('username' => 'son-link')
	')
);

// Get all users order by username
$result = $db->select(
	array('
		'table' => 'users',
		'orderby' => 'username ASC'
	')
);
```

#### insert:

Insert new row on the database

```php
$db->insert($table, $values):
```
**Params:**

* $table (string, required): The table to insert the new row.
* $values (array): Associative array with the values.

**Returns:** A int value with the last insert id (if the table contains a PRIMARY KEY with AUTO_INCREMENT) or false if the query fails.

##### Example:

```php
$insert = $db->insert('users', array(
	'username' => 'son-link',
	'passwd' => 12345,
	'email' => 'son-link@myhost.com'
));
```

#### update:

Update row(s)

```php
$db->update($table, $values, $conditions=''):
```
**Params:**

* $table (string, required): is affected table.
* $values (array, required): Associative array with values to update.
* $conditions (array): Associative array with the conditions to update

**Returns:** Boolean indicate if the update is executed correctly

##### Example:

```php
$update = $db->update(
	'users',
	array('email' => 'son-link@example.com'),
	array('id' => 1)
);
```

#### delete:

Delete row(s)

```php
$db->delete($table, $conditions, $limit=''):
```
**Params:**

* $table (string, required): is the affected table.
* $conditions (array, required): Associative array with the conditions to delete a row.
* $limit (int): A limit if affected a more than one row.

**Returns:** Boolean indicate if the delete is executed correctly

##### Example:

```php
$delete = $db->delete(
	'users', array('user' => 'son-link'),
);
```

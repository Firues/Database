<h3>[PHP] alternative to depreciated native mysql extension since PHP5.5</h3>

With mysql php extension depreciated in PHP 5.5 a new alternative is now in order.<br/>
There are really only 2 other extensions, PDO and mysqli.<br/>
With PDO supporting 12 different drivers, named parameters and prepared statements other then everything mysqli offers (except procedural api).<br/>
But why would we want to use procedural when both of these offers OOP?<br/><br/>

Well personally I use PDO which I coded a singleton pattern with security in mind.<br/>
For you that never heard of singleton patterns before the concept is easy, global scope and only one instance.<br/><br/>

When it comes to security and databases there are really only 2 big once SQL-injections and Cross side scripting (XSS)<br/>
XSS is currently nr 3 on OWASP top 10 year 2013<br/>
XSS isn't a database related vulnerability but not escaping data before output to page will harm the visitors so I find is easiest to include the escape already in the database class so I don't have to think about it.<br/><br/>

SQL-injections is and has been nr 1 vurnerability on OWASP top 10 for many years.<br/>
SQL-injection lets the attacker run custom SQL statements on the server if not escaped correctly. <br/>
This is a very dangerous vulnerability that can not only view/edit/delete all the data in your database but also take over your server if not configured right.<br/><br/>

I built a singleton PDO class that prevents both SQL-injection and XSS automatically.<br/>
The use of it is simple, here's an example<br/><br/>
```php
<?php
	$db = Database::getInstance();
	$results = $db->Query("SELECT FROM users WHERE ID > ?", 5);
	foreach( $results->fetchAll() as $row )
	{
		echo "Username " . $row["username"] . " has the ID " . $row["ID"] . "&lt;br />";
	}
?>
```
<br/>

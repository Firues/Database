[PHP] alternative to depreciated native mysql extension since PHP5.5

With mysql php extension depreciated in PHP 5.5 a new alternative is now in order.
There are really only 2 other extensions, PDO and mysqli.
With PDO supporting 12 different drivers, named parameters and prepared statements other then everything mysqli offers (except procedural api).
But why would we want to use procedural when both of these offers OOP?

Well personally I use PDO which I coded a singleton pattern with security in mind.
For you that never heard of singleton patterns before the concept is easy, global scope and only one instance.

When it comes to security and databases there are really only 2 big once SQL-injections and Cross side scripting (XSS)
XSS is currently nr 3 on OWASP top 10 year 2013
XSS isn't a database related vulnerability but not escaping data before output to page will harm the visitors so I find is easiest to include the escape already in the database class so I don't have to think about it.

SQL-injections is and has been nr 1 vurnerability on OWASP top 10 for many years
SQL-injection lets the attacker run custom SQL statements on the server if not escaped correctly. 
This is a very dangerous vulnerability that can not only view/edit/delete all the data in your database but also take over your server if not configured right.

I built a singleton PDO class that prevents both SQL-injection and XSS automatically.
The use of it is simple, here's an example
<?php
$db = Database::getInstance();
$results = $db->Query("SELECT FROM users WHERE ID > ?", 5);
foreach( $results->fetchAll() as $row )
{
	echo "Username " . $row["username"] . " has the ID " . $row["ID"] . "<br />";
}
?>

You can find the full source code here
https://github.com/laxxen/pdo/blob/master/Database.php

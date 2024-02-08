<h1>Dynamic Simple Queries</h1>

<h2>The division Project<h2>

<ul>
  <li>Server.php</li>
</ul>

<h2>Server.php<h2>
<p>Here we are the hearth's project, this file has a simple, but complex class 'Server', first i will describe the attributes</p>
<h3>Attributes<h3>
<ul>
  <li>'$conn'</li>
  <p>The $conn attribute is a mysqli conection and he is set in constructor class, so you need pass some param when you creat a object. <strong>Parameters in order: </strong> servername, DBusername, DBpassword, DBname</p>
  <li>'$database'</li>
  <p>This attribute saves the 'DBname', which is passed in constructor</p>
  <li>'$table'</li>
  <p>The table saves which table the programmer saved last, it is used to perform searches(queries), so it is always necessary to know which table it is using</p>
  <li>'$column'</li>
  <p>The same logic of table, but here saves multiples columns, so saves columns the programmer saved last, it is used to perform searches(queries), so it is always necessary to know which table it is using</p>
</ul>

<p>Now i will describe de methods</p>
<h3>Methods<h3>
<ul>
    <li>setTable(string $table)</li>
    <p>This function will set the table whith will be used, you must pass a string contain the name's table</p>
    <li>setColumn(array $columns, string $table = '')</li>
    <p>This function will set the columns whith will be used, you must pass a array contain the name's columns, but <strong>ATTENTION</strong>  if you pass a array that has names that are the same in two tables, you must enter the table you want to manipulate, and here enter the second parameter(string $table). Case you put a array that not repeat columns names, the owns method will find table and set it</p>
    <li>insert(array $itens, bool $activeSetColumns = false)</li>
    <p>You need before set columns case you active the second param.($activeSetColumns), so you will pass a array container itens to insert into, case you dont active second param. the method will take all columns</p>
    <li>update(array $itensToUpdate, array $ItensToWhere = null)</li>
    <p>It is necessary to set the table first, because here you will pass the values to be updated, and where to be updated</p>
    <li>query(string $str)</li>
    <p>Realizes a simple query, with out bind, not SAFE for POST and GETS</p>
    <li>closeConn()</li>
    <p>Terminate Database Connection</p>
</ul>


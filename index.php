<?php 
// PHP Data Objects(PDO) Sample Code:
try {
  $conn = new PDO("sqlsrv:server = tcp:macd42121.database.windows.net,1433; Database = macd", "redhunter7", "TopiJerami7");
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
  print("Error connecting to SQL Server.");
  die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "redhunter7", "pwd" => "TopiJerami7", "Database" => "macd", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:macd42121.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo); 
$query = "SELECT * FROM [dbo].[persons]";
$data_user = sqlsrv_query($conn , $query);

if(isset($_POST['submit']))
{
  $id = $_POST['id'];
  $firstname = $_POST['first'];
  $lastname = $_POST['last'];
  $insert = 'INSERT INTO persons (id, firstname, lastname)
  VALUES ($id, $firstname, $lastname);';
  $stmt = sqlsrv_query($conn , $insert);
  if ($stmt) {  
    echo "Row successfully inserted.\n";  
} else {  
    echo "Row insertion failed.\n";  
    die(print_r(sqlsrv_errors(), true));  
}  

}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Form</title>
</head>
<style></style>
<body>
    <h1>Form</h1>
    <form action="index.php" method="post">
       <label for="">ID</label>
       <input name="id" type="number">
       <br><br>
       <label for="">firstname</label>
       <input name="first" type="text">
       <br><br>
       <label for="">lastname</label>
       <input name="last" type="text">
       <br><br>
       <button type="submit" name="submit">Submit</button>
    </form>
<br><br>
<table style="width:100%">
  <tr>
    <th>Firstname</th>
    <th>Lastname</th>
  </tr>
  <?php
  while($data = sqlsrv_fetch_array($data_user))
    {
    ?>
  <tr>
    <td><?php echo $data['firstname']; ?></td>
    <td><?php echo $data['lastname']; ?></td>
  </tr>
    <?php } ?>
</table>

</body>
</html>
<!doctype html>
<html lang="en">
    <head>
        <title>  </title>
        <link rel="stylesheet" href=""> 
    </head>
    <body>
        <?
            $dbc=mysqli_connect("localhost", "inv_student", "password", "inventory")
            or die ('Could not connect to MySQL: '. mysqli_connect_error());
            $my_query="DROP TABLE transaction;";
            $my_result = $dbc->query($my_query);
            if(!my_result){
                die($dbc->error);
            }
            $query = "CREATE TABLE transaction(
                transID VARCHAR(33),
                date VARCHAR(33),
                time VARCHAR(33),
                item VARCHAR(33),
                price VARCHAR(33),
                transactionStatus VARCHAR(33),
                hashKey VARCHAR(200))";
            //$result=mysqli_query($dbc, $query);
            $result = $dbc->query($query);
            if(!result){
                die($dbc->error);
            }
            
            echo' Table transaction created and populated';
            $dbc->close();
        
            
    


        ?>
    </body>
</html>
<!doctype html>
<html lang="en">
    <head>
        <title>  </title>
        <link rel="stylesheet" href=""> 
    </head>
    <body>
        <?
            $dbc=mysqli_connect("localhost","inv_student","password", "inventory")
            or die('Could not connect to MySQL: '.mysqli_connect_error());
          //sql="DELETE FROM transaction WHERE time='19:59:59'";
          
           
           //$t=mysqli_query($dbc, $sql);
            /*
            $sql="INSERT INTO transaction(transID, date, time, item, price, transactionStatus, hashKey)
            VALUES ('5xy','11-15-19', '01:00:00','chapati', '2.99', 'Paid','sdfsdfsdf3453' )";
            $t1=mysqli_query($dbc, $sql);
            if($t1)
            {
                echo"record successfully created";
            }
            else{
                echo"Nope it didnt go through";
            }*/
    
            /*$sql1="INSERT INTO transaction (transID, date, time, item, price)
            VALUES ('7xy','11-15-19','11:11:11','chapti','3.99' )";
            $t2=mysqli_query($dbc, $sql1);*/
             $sql="DELETE FROM transaction WHERE transID='5ddb1cf23ad40' ";
            $t1=mysqli_query($dbc, $sql);
            if($t1)
            {
                echo"record successfully created";
            }
            else{
                echo"Nope it didnt go through";
            }
            
        
            $x=0;
            $result=mysqli_query($dbc,"Select * from transaction");
            while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
            {
                
                
            //if($row[transID]=="5dd2385557b57"){
                print_r($row);
                echo"<br>";//}
            
            }

            
            
        ?>
    </body>
</html>
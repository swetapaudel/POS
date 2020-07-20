<!doctype html>
<html lang="en">
    <head>
        <title>Login Page</title>
        <link rel="stylesheet" href="login.css"> 
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300&display=swap" rel="stylesheet"
    </head>
    <body>
        <h1>American<br>Tandoori</h1>
        
        <form action="login.php" method="POST">
            <?
            echo"
                    <div class=\"container\">
                        <label id = \"uText\" for=\"uname\"><b>Username</b> </label>
                        <input id = \"uBox\" type=\"text\" name=\"uname\" /><br>
                        
                        <label id = \"pText\" for=\"psw\"><b>Password</b></label>
                        <input id = \"pBox\" type=\"password\" name=\"psw\" /><br>
                        <input id = \"sumit\" type=\"submit\" name=\"submit\" value=\"Login\" />
                    </div>";
                $uname=$_POST['uname'];
                $psw=$_POST['psw'];
                $submit=$_POST['submit'];
                
                $user_name=array("1234"=>"gautam", "5678"=>"samuel", "2222"=>"sweta");
                $passwords=array("1234","5678","2222");
                $user_type=array("user", "user","admin");
                
                if($submit)
                {
                    $test_password=array_search($uname, $user_name);

                    if($test_password==$psw)
                    {
                        for($i=0;$i<count($passwords);$i++)
                        {
        
                            if($psw==$passwords[$i])
                            {
                                $user_type1=$user_type[$i];
                                /* echo"keejnfdsjn"; */
                            }
                        }
                        if($user_type1=="user")
                        {
                            header("Location:https://mislab.business.msstate.edu/~scanfield/IDE/upload/guest/sp1641/temp/cashier.php");
                        }
                        elseif($user_type1=="admin")
                        {
                            header("Location:https://mislab.business.msstate.edu/~scanfield/IDE/upload/guest/sp1641/temp/admin_homepage.php");
                        }
                    }
                    else{
                                echo"<div=\"error_message\">Incorrect username or password</div>";
                        }
                    
                }
                
            ?>
            <script id="alertMsg">
            
                function HelloWorld()
                {
                    var message="Incorrect username or password";
                    alert(message);
                }
            </script>
    </form>
    </body>
</html>
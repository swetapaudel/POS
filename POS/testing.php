<!doctype html>
<html lang="en">
    <head>
        <title>  </title>
        <link rel="stylesheet" href="">
        <script src="testing.js">
            
        </script>
    </head>
    <body>
        <form action="testing.php" method="POST">
       <input value= "value1" name="invite" type="radio"/>
       <input value="value2" name="invite" type="radio"/>
        <?
            $submit=$_POST['submit'];
            $invite=$_POST['invite'];
            if($submit)
            {
                if($invite=="value2")
                {
                    $python = `python hello.py`;
                       echo $python;
                    echo 'sdfgkjsklf';
                }
            }
            
        ?>
         <button nam="submit" value="submit">
             <img src="Unknown.jpeg" height="42" width="42">
             <div>Flower</div>
         </button>
         <?
            function printing_random()
            {
                echo "Hello world";
            }
         ?>
        </form>
    </body>
</html>
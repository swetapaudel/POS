<? session_start();

// after doing session start you have access to varialbe named $_SESSION[]
// $_SESSION[trans_info] = $transaction_info
// varible = $_SESSION[trans_info]

?>
<<!doctype html>
<html lang="en">
    <head>
        <title>Admin Page</title>
        <style>
            td,th{
                
                text-align: center;
                padding:0.5em;
            }
        
        </style>
        <link rel="stylesheet" href="admin_homepage.css">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300&display=swap" rel="stylesheet">
        <script>
            function startTime() 
            {
                var today = new Date();
                var h = today.getHours();
                var m = today.getMinutes();
                var s = today.getSeconds();
                
                m = checkTime(m);
                s = checkTime(s);
                document.getElementById('digital_live_clock').innerHTML =
                h + ":" + m + ":" + s;
                var t = setTimeout(startTime, 500);
            }
            function checkTime(i) 
            {
                if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
                return i;
            }
                            
        </script>
    </head>
    <body onload="startTime()">
        <div id="digital_live_clock"></div>
        <h1>POS</h1>
            <?
             
                
                //Function to add products
                function add_product($item_, $add_item, $selected_item, $item_price)
                {
            
                    //Three text files, one for storing previous items, storing previous prices, storing previous total
                    $my_files=file('temp_file.txt');
                    $my_files_price=file('temp_file_price.txt');
                    $my_files_previous_total=file('temp_file_previous_total.txt');
            
            
                    file_put_contents("temp_file.txt", $item_, FILE_APPEND);
                    file_put_contents("temp_file_price.txt", $item_price, FILE_APPEND);
                    file_put_contents("temp_file_previous_total.txt", floatval($item_price), FILE_APPEND);

            
                    $my_files=file('temp_file.txt', FILE_IGNORE_NEW_LINES);
                    $my_files_price=file('temp_file_price.txt', FILE_IGNORE_NEW_LINES);
                    $my_files_previous_total=file('temp_file_previous_total.txt', FILE_IGNORE_NEW_LINES);
            
                    fclose('temp_file_price.txt');
                    fclose('temp_file_previous_total.txt');
                    fclose('temp_file.txt');
            
                    $my_file=array();
                    $my_file_price=array();
                    $my_file_previous_total=array();
            
                    $item_no=0;
                    $total_price=0;
                    $price_array_index=0;
            
            
                    $opening_file=fopen('temp_file.txt', "w");
                    $opening_file_price=fopen('temp_file_price.txt',"w");
                    $opening_file_previous_total=fopen('temp_file_previous_total.txt', "w");
                    $done=0;
            
                    foreach($my_files as$my_items)
                    {
                        if($selected_item!=$my_items or $done>0)
                        {
                            //echo"$selected_item ==== $my_items<br>";
                    
                            fwrite($opening_file, $my_items);
                            fwrite($opening_file_price, $my_files_price[$price_array_index]);
                            fwrite($opening_file_previous_total, $my_files_previous_total[$price_array_index]);
                            fwrite($opening_file, "\r\n");
                            fwrite($opening_file_price, "\r\n");
                            fwrite($opening_file_previous_total,"\r\n");
                            array_push($my_file_price, $my_files_price[$price_array_index]);
                            
                            array_push($my_file, $my_items);
                            
                        }
                        else{$done=$done+1;}
                        $price_array_index=$price_array_index+1;
                    }
        
                    //This part of code displays the items that user currently in process of buying
                    $price_array_index=0;
                    $my_filed_price=file("temp_file_price.txt");
                    $my_filed_previous_total=file('temp_file_previous_total.txt');
                    foreach ($my_file as $my_item)
                    {
                        if($my_item!="")
                        {
                            $total_price=$total_price+floatval($my_filed_previous_total[$price_array_index]);
                            //$my_files_previous_total=file('temp_file_previous_total.txt');
                            //file_put_contents("temp_file_previous_total.txt", floatval($my_filed_price[$price_array_index]), FILE_APPEND);
                            //file_put_contents("temp_file_previous_total.txt", "\r\n", FILE_APPEND);
                            $item_no=$item_no+1;
                            echo "<tr >
                                <td id=\"item_no_styling\" >$item_no </td>
                                <td>$my_item</td>
                                <td>$my_filed_price[$price_array_index]</td>
                                <td> <input type=\"radio\" name=\"selected_item\" value=\"$my_item\" /></td>
                            </tr>";
                            
                        }
                            $price_array_index=$price_array_index+1;
                    }
                    // echo "shjabfkjasfnaknfkajsnfjkasfnaknjfjkanfja $total_price";
                    return $total_price;
                }
                
                //Get the price of the item
                function price_search($item_to_search)
                {
                    $items_in_shop=array("11.99"=>"Biryani", "3.99"=>"Idli", "4.99"=>"Dosa", "7.99"=>"Chapati", "6.99"=>"Paratha", "2.99"=>"Halwa", "4.49"=>"Sambar", "12.99"=>"Curry", "13.99"=>"Tandoori", "15.99"=>"Thali", "8.99"=>"Thukpa", "9.99"=>"C.momo");
                    $price_of_item=array_search($item_to_search, $items_in_shop);
                    return $price_of_item;
                }
                
                function refund($transaction_ID)
                {
                    echo"$transaction_ID";
                    $dbc=mysqli_connect("localhost", "inv_student", "password", "inventory")
                    or die('Could not connect to MySQL: '.mysqli_connect_error());
                    $sql="UPDATE transaction SET transactionStatus='Refunded' WHERE transID='$transaction_ID' 
                    ";
                    $result=mysqli_query($dbc, $sql);
                    if(!$result){echo"It aint going through";};
                    mysqli_close($dbc);
                    return true;
                    
                }
                
                //This doesn't exactly display items, it simply open the files, delete content, and make the transaction
                //It also calls the function to store the information in database

                function payment_display()
                {
                    
                    $all_paid_items=file('temp_file.txt');
                    $all_paid_item_prices=file('temp_file_price.txt');
                    $total_price=array_sum($all_paid_item_prices);
                    
                    $current_date_and_time=`python hello.py`;
                    $date=substr($current_date_and_time,0,10);
                    $my_time="";
                    echo"$current_date_time";
                    for($i=11;$i<19;$i++)
                    {
                       $my_time=$my_time.$current_date_and_time[$i];
                    }
                    
                    $final_storage=file('content.txt');
                    for($i=0; $i<count($all_paid_items); $i++)
                    {
                        
                        $final_string= $all_paid_items[$i].$all_paid_item_prices[$i];
                        file_put_contents("content.txt", $final_string);
                        
                    }
                    
                    $our_hash_value=hash_file('md5', "content.txt");
                    $transaction_id=transaction_Id();
                    receipt($my_time, $date, $transaction_id);
                
                    $transaction_status="Paid";
                    storing_in_database($transaction_id, $date, $my_time, $all_paid_items, $all_paid_item_prices, $transaction_status, $our_hash_value);

                    //Reopening files, erasing contents and closing
                    fopen('temp_file_price.txt',"w");
                    fopen('temp_file.txt', "w");
                    fopen('temp_file_previous_total.txt',"w");
                    
                    fclose('temp_file_price.txt');
                    fclose('temp_file.txt');
                    fclose('temp_file_previous_total.txt');

        
                    return $total_price;
                    
                }
                
                //Just a function to get transaction, really not-needed, just in case we need to get a desired
                //Transaction ID, we can use this function. But it is still part of the process
                function transaction_Id()
                {
                    $transaction_id=uniqid();
                    return $transaction_id;
                    
                }
                
                //Calls the database, and store in it
                function storing_in_database($transaction_id, $date, $time, $all_paid_items, $all_paid_item_prices, $transaction_status, $our_hash_value)
                {

                    $dbc=mysqli_connect("localhost", "inv_student", "password", "inventory")
                    or die('Could not connect to MySQL: '.mysqli_connect_error());
                    $our_hash_value;
                    for($i=0; $i<count($all_paid_items); $i++)
                    {
                        $sql="INSERT INTO transaction(transID, date, time, item, price, transactionStatus, hashKey)
                        VALUES ('$transaction_id', '$date','$time','$all_paid_items[$i]','$all_paid_item_prices[$i]','$transaction_status','$our_hash_value')";
                        $result=mysqli_query($dbc, $sql);
                        
                    }
                    
                    mysqli_close($dbc);
                }
                
                function transaction_display($transaction_key, $type_of_search)
                {
                    
                    if($type_of_search=="Tansaction ID")
                    {
                        if($transaction_key!=""){search_transaction_by_transaction_id($transaction_key, $type_of_search);}
                        else{echo"<div id=\"input_error_message\">Please type a valid transaction search. </div>";}
                        
                    }
                    elseif($type_of_search="Date")
                    {
                        if(is_date_valid($transaction_key)){search_transaction_by_date($transaction_key, $TRANSACTION_ID);}
                        else{echo"<div id=\"input_error_message\">Date needs to be in format of yyyy-dd-mm. </div>";}
                    }
                }
                
                function search_transaction_by_transaction_id($transaction_key, $TRANSACTION_ID)
                {

                    echo"<form action=\"transaction_description_.php\" method=\"POST\"> ";;
                    $dbc=mysqli_connect("localhost", "inv_student", "password", "inventory")
                    or die('Could not connect to MySQL: '.mysqli_connect_error());
                    $query="Select *from transaction where transID='$transaction_key'"; //WHERE transID=$transaction_key ";
                    $result=mysqli_query($dbc, $query);
                        echo"<div id=\"transaction_search_query_display_div\">";
                        echo"<table id=\"transaction_search_query_display\">
                            <tr>
                                <th>Transaction Id</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Transaction Status</th>
                                <th>Hash Value</th>
                            </tr>";
                            $server_row="blah";
                            while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
                            {
                                $transaction_description=array(array());
                                $temp_transaction_id=$row[transID];
                                $temp_date=$row[date];
                                $temp_time=$row[time];
                                $temp_transaction_status=$row[transactionStatus];
                                while($temp_transaction_id==$row[transID])
                                {
                                    $our_hash_value=$row['hashKey'];
                                    array_push($transaction_description, $row);
                                    $row=mysqli_fetch_array($result, MYSQLI_ASSOC);
                                }
                                echo"
                                <tr>
                                    <td width=\"50%\">
                                        <button type=\"submit\" id=\"search_element_button\"name=\"xy\" value=\"$temp_transaction_id\">
                                           $temp_transaction_id
                                        </button>
                                    </td>
                                    <td width=\"50%\">$temp_date</td>
                                    <td width=\"50%\">$temp_time</td>
                                    <td width=\"50%\">$temp_transaction_status</td>
                                    <td width=\"50%\">$our_hash_value</td>
                                </tr>
                                ";
                                //$server_row=$server_row+1;
                            }
                        echo"</table>";
                    echo"</form>";
                    echo"</div>";
                }
                function search_transaction_by_date($transaction_key, $TRANSACTION_ID)
                {
                    //echo"<form action=\"transaction_description_.php\" method=\"POST\"> ";;
                    $dbc=mysqli_connect("localhost", "inv_student", "password", "inventory")
                    or die('Could not connect to MySQL: '.mysqli_connect_error());
                    $query="Select *from transaction where date='$transaction_key'"; //WHERE transID=$transaction_key ";
                    $result=mysqli_query($dbc, $query);
                    $yz=" ";
                    echo"<div id=\"transaction_search_query_display_div\">";
                        echo"<table id=\"transaction_search_query_display\">
                            <tr>
                                <th>Transaction Id</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Transaction Status</th>
                                <th>Hash Value</th>
                            </tr>";
                            $server_row="blah";
                            while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
                            {
                                $transaction_description=array(array());
                                $temp_transaction_id=$row[transID];
                                $temp_date=$row[date];
                                $temp_time=$row[time];
                                $temp_transaction_status=$row[transactionStatus];
                                while($temp_transaction_id==$row[transID])
                                {
                                    $our_hash_value=$row['hashKey'];
                                    array_push($transaction_description, $row);
                                    $row=mysqli_fetch_array($result, MYSQLI_ASSOC);
                                }
                                echo"
                                <tr>
                                    <td width=\"50%\">
                                        <button type=\"submit\" id=\"search_element_button\"name=\"xy\" value=\"$temp_transaction_id\">
                                           $temp_transaction_id
                                        </button>
                                    </td>
                                    <td width=\"50%\">$temp_date</td>
                                    <td width=\"50%\">$temp_time</td>
                                    <td>$temp_transaction_status</td>
                                    <td width=\"50%\">$our_hash_value</td>
                                </tr>
                                ";
                                //$server_row=$server_row+1;
                            }
                        echo"</table>";
                    echo"</form>";
                    echo"</div>";
                }

                function specific_transaction_info_display($transaction_info)
                {
                    $dbc=mysqli_connect("localhost", "inv_student", "password", "inventory")
                    or die('Could not connect to MySQL: '.mysqli_connect_error());
                    $query="Select *from transaction where transID='$transaction_info'"; //WHERE transID=$transaction_key ";
                    $result=mysqli_query($dbc, $query);
            

                        echo"<div id=\"display_specific_transaction_id\">Transaction ID: $transaction_info</div>";
                        echo"<div id=\"display_specific_transaction_div\">";
                        echo" <table id=\"transaction_description\">
                            <tr>
                                <th>Item</th>
                                <th>Price</th>
                            <tr>";
                            while($row=mysqli_fetch_array($result,MYSQLI_ASSOC))
                            {
                                $our_hash_value=$row[hashKey];
                                $date_paid=$row[date];
                                $time_paid=$row[time];
                                $transaction_status=row[transactionStatus];
                                echo"<tr>
                                    <td>$row[item]</td>
                                    <td>$row[price]</td>
                                </tr>
                                ";
                            }
                        echo"</table>";
                     echo"</div>";
                    echo"<div id=\"display_hash_value\">Hash Value: $our_hash_value</div>";
                    echo"<div id=\"display_specific_transaction_date\">Date: $date_paid</div>";
                    echo"<div id=\"display_specific_transaction_time\">Time: $time_paid</div>";
                    echo"<div id=\"display_specific_transaction_status\">Status: $transaction_status</div>";
                    echo"<button type=\"submit\" id=\"refund_button\" name=\"refund_transaction_id\" value=\"$transaction_info\">Refund</button>";
                    
                };
                function is_date_valid($date__)
                {
                    $vaildity=false;
                    if(($date__[4]=="-") and ($date__[7]=="-")){$validity=true;}
                    else
                    {
                        $validity=false;
                    }
                    return $validity;
                }
                function receipt($my_time, $date, $transaction_ID)
                {

                    $all_paid_items=file('temp_file.txt');
                    $all_paid_item_prices=file('temp_file_price.txt');
                    $total_price=array_sum($all_paid_item_prices);
                    
                    echo"
                    <div id=\"receipt_transaction_id\">Transaction No. $transaction_ID</div>
                    <div id=\"receipt_date\">Date: $date</div>
                    <div id=\"receipt_time\">Time: $my_time</div>
                    <div id=\"receipt_total_price\">Total Price: $total_price</div>
                    <table id=\"Receipt\">
                    <tr>
                        <th>Items</th>
                        <th>Price</th>
                    </tr>
                    ";
                
                    for($i=0; $i<count($all_paid_items); $i++)
                    {
                        echo"
                            <tr><td>$all_paid_items[$i]</td><td>$all_paid_item_prices[$i]</td></tr>
                        ";
                    }
                    echo"</table>";    
                }
                
                
                
        ?>
        <form action="admin_homepage.php" method="POST">
            
            
            <article>
                <div id="table_display" >
                <table class="item_table">
                    <tr id="head_row">
                        <th>Item No.</th>
                        <th>Items</th>
                        <th>Price</th>
                        <th>Select Item</th>
                    </tr>
                    <?php
                        echo"<div id=\"txt\"></div>";
                        $selected_item=$_POST['selected_item'];
                        $item_to_buy=$_POST['item_to_buy'];
                        $add_item=$_POST['add_item'];
                        $remove_item=$_POST['remove_items'];
                        $item_ = $_POST['item_'].PHP_EOL;
                        $item_price=$_POST['item_price'];
                        $pay=$_POST['pay'];
                        $TRANSACTION_ID=$_POST['TRANSACTION_ID'];
                        $DATE=$_POST['DATE'];
                        $TIME=$_POST['TIME'];
                        $transaction_key=$_POST['transaction_key'];
                        $xy=$_POST['xy'];
                        $refund_transaction_id=$_POST['refund_transaction_id'];
        
                        print_r($transaction_info);
                        //($transaction_info);
                        //array_push($selected_items, " ");
                        //$total_price=0;
                        //$my=file("temp_file.txt");
                        if($remove_item)
                        {
                            
                            $item_price=price_search($item_to_buy);
                            $total_price= add_product($item_to_buy, $add_item, $selected_item, $item_price);
                        }
                        elseif($item_to_buy)
                        {
                            $selected_item="";
                            $item_price=price_search($item_to_buy);
                            $total_price = add_product($item_to_buy, $add_item, $selected_item, $item_price);
                            
                        }
                        echo"</table>";
                        echo"</div>";
                        if($pay)
                        {
                    
                            $my_files=file('temp_file.txt');
                            
                            if(count($my_files)!=0){
                            $total_price=payment_display();
                            $total_price=0;
                            }
                            else{echo"<div id=\"empty_error\">Inventory is empty</div>";}
                            //echo"<div id=\"payed_information\" >hello</div>";
                        }
                        elseif($refund_transaction_id)
                        {
                            echo"$refund_transaction_id";
                            refund($refund_transaction_id);
                        }
                        elseif($TRANSACTION_ID or $DATE or $TIME)
                        {
                            //echo"$TRANSACTION_ID";
                            if($TRANSACTION_ID)
                            {
                
                                
                                transaction_display($transaction_key, $TRANSACTION_ID);
                                
                            }
                            elseif($DATE)
                            {
                               
                                transaction_display($transaction_key, $DATE);
                            }
                            elseif($TIME)
                            {
                                //echo"alsd";
                                transaction_display($transaction_key, $TIME);
                            }
                        }
                       elseif($xy)
                        {
                            //print_r($transaction_info[1]);
                            specific_transaction_info_display($xy);
                        }
                        session_destroy();
                        
                    ?>
                <!--</div>-->
                <div id="item_display">
                    <?$x=" "?>
                    <table id="items_for_sale">
                    <tr>
                        <!--<td>Briyani</td>
                        <td>$11.99</td>-->
                        <td><input id = "biryani" class="item_button" type="submit" name="item_to_buy" value="Biryani" /></td>
                    </tr>
                    <tr>
                        <!--<td>Idli</td>
                        <td>$3.99</td>-->
                        <td><input id = "idli" class="item_buttom" type="submit" name="item_to_buy" value="Idli" /></td>
                    </tr>
                    <tr>
                        <!--<td>Dosa</td>
                        <td>$4.99</td>-->
                        <td><input id = "dosa" class="item_button" type="submit" name="item_to_buy" value="Dosa" /></td>
                    </tr>
                    <tr>
                        <!--<td>Chapati</td>
                        <td>$7.99</td>-->
                        <td><input id = "chapati" class="item_button" type="submit" name="item_to_buy" value="Chapati" /></td>
                    </tr>
                    <tr>
                        <!--<td>Paratha</td>
                        <td>$6.99</td>-->
                        <td><input id ="paratha" class="item_button" type="submit" name="item_to_buy" value="Paratha" /></td>
                    </tr>
                    <tr>
                        <!--<td>Halwa</td>
                        <td>$2.99</td>-->
                        <td><input id = "halwa" class="item_button" type="submit" name="item_to_buy" value="Halwa" /></td>
                    </tr>
                    <tr>
                        <!--<td>Sambar</td>
                        <td>$4.49</td>-->
                        <td><input id = "sambar" class="item_button" type="submit" name="item_to_buy" value="Sambar" /></td>
                    </tr>

                    <tr>
                        <!--<td>Curry</td>
                        <td>$12.99</td>-->
                        <td><input id = "curry" class="item_button" type="submit" name="item_to_buy" value="Curry" /></td>
                    </tr>
                    <tr>
                        <!--<td>Tandoori</td>
                        <td>$13.99</td>-->
                        <td><input id = "tandoori" class="item_button" type="submit" name="item_to_buy" value="Tandoori" /></td>
                    </tr>
                    <tr>
                        <!--<td>Thali</td>
                        <td>$15.99</td>-->
                        <td><input id = "thali" class="item_button" type="submit" name="item_to_buy" value="Thali" /></td>
                    </tr>
                    <tr>
                        <!--<td>Thukpa</td>
                        <td>$8.99</td>-->
                        <td><input id = "thukpa" class="item_button" type="submit" name="item_to_buy" value="Thukpa" /></td>
                    </tr>
                    <tr>
                        <!--<td>c.momo</td>
                        <td>$9.99</td>-->
                        <td><input id = "c_momo" class="item_button" type="submit" name="item_to_buy" value="C.momo" /></td>
                    </tr>
                    </table>
                </div>
               <!-- <input class="item_table" id="add_button" type="submit" name="add_item" value="Add Item" />-->
            <!--<input class="item_table" id="add_button_text" type="text" name="item_" /> <br>-->
            <?echo "<article id=\"total_display\"> Total: $total_price </article>" ?><br>
            <!--<input id="refund_button" type="submit" name="refund" value="Refund"/><?echo"       "?>--><input id="pay_button" type="submit" name="pay" value="Pay" />
            <br>
            <input id="remove_button" type="submit" name="remove_items" value="Void" />
            <input id="transaction_search_text" type="text" name="transaction_key" />
            <div class="dropdown">
                <button class="dropbtn">Search By</button>
                <div class="dropdown-content">
                    <input id = "transaction_id" class="type_search_button" type="submit" name="TRANSACTION_ID" value="Tansaction ID"/></br>
                    <input id = "date" class="type_search_button" type="submit" name="DATE" value="Date" /></br>
                </div>
            </div>
        </form>
            <form action="login.php" method="POST">
                <input id="log_out_button" type="submit" name="log_out" value="Log out" />
            </form>
        </article>

    </body>
    <!--References

        Digital Live clock - https://www.w3schools.com/js/tryit.asp?filename=tryjs_timing_clock
        Hover over drop down menu- https://www.w3schools.com/howto/tryit.asp?filename=tryhow_css_js_dropdown_hover
    -->
</html>









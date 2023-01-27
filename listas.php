<?php 
    /*features:
    + content fully stored in cookies
    + customizable cookies lifetime 
    + easily adaptable color palette via few and clearly descriptive css variables
    + multiple simultaneous lists
    + higher hierarchy tasks option
    + fully customized check-input styles
    + exhaustive code commentary makes it self-explanatory */

    //cookies duration parameter (setted as seconds in a day * days amount)
    $t = 86000 * 15;
    //initialize list creation form
    if (isset($_COOKIE["addlist_btn"])) {} else {setcookie("addlist_btn", "plus", time() + $t , "/"); header("Refresh:0");}


    //unpack lists
    $lists = array();
    if (isset($_COOKIE["lists"])) {$lists = explode("ϣ", $_COOKIE["lists"]);}

    //set active list
    $active_list = 0;
    if (isset($_COOKIE["active_list"])) {
            $active_list = $_COOKIE["active_list"];
    } 
    $active_list_pr = 0;
    if (isset($_COOKIE["active_list"])) {
        $active_list_pr = $_COOKIE["active_list"]."_pr";
    }

    //unpack tasks and priority arrays chained in cookies with "ϣ" character
    $arr = array();
    $arr_pr = array();
    if (isset($_COOKIE["active_list"])) {$arr = explode("ϣ", $_COOKIE[$active_list]);}
    if (isset($_COOKIE["active_list"])) {$arr_pr = explode("ϣ", $_COOKIE[$active_list_pr]);}

    //create new task function
    if (isset($_POST["text"])) {
        //add new task to the unpacked array / and priority value to priority arr
        array_push($arr, $_POST["text"]);
        array_push($arr_pr, $_POST["priority"]);
        //re-encode arrays as string for cookies storage
        $arr_str = implode("ϣ", $arr);
        $arr_pr_str = implode("ϣ", $arr_pr);
        //refresh both arrays in cookies
        setcookie($active_list, $arr_str, time() + $t , "/");
        setcookie($active_list_pr, $arr_pr_str, time() + $t , "/");
        //refresh page
        header("Refresh:0");
    }
    //erase task function
    if (isset($_POST["close"])) {
        //erase task
        array_splice($arr, $_POST["close"], 1);
        array_splice($arr_pr, $_POST["close"], 1);
        //re-encode arrays as string for cookies storage
        $arr_str = implode("ϣ", $arr);
        $arr_pr_str = implode("ϣ", $arr_pr);
        //refresh both arrays in cookies
        setcookie($active_list, $arr_str, time() + $t , "/");
        setcookie($active_list_pr, $arr_pr_str, time() + $t , "/");
        //refresh page
        header("Refresh:0");
    }
    
    //create new list function
    if (isset($_POST["newlistname"])) {
        //translate new list name to a string without spaces (for the cookie´s array key)
        $name_words_arr = explode(" ", $_POST["newlistname"]);
        $name_without_spaces = implode("Ɖ", $name_words_arr);
        //name the priority array´s cookie
        $newlists_pr_arr_name = $name_without_spaces."_pr";
        //create new list´s own cookie slots for tasks and for priorities
        setcookie($name_without_spaces, "_", time() + $t , "/");
        setcookie($newlists_pr_arr_name, "_", time() + $t , "/");
        //set new list as active
        setcookie("active_list", $name_without_spaces, time() + $t , "/");

        //add new list to the lists array
        array_push($lists, $name_without_spaces);
        //re-encode lists array as string for cookies storage
        $lists_str = implode("ϣ", $lists);
        //refresh lists array in cookies
        setcookie("lists", $lists_str, time() + $t , "/");
        //reset add form state in cookies
        setcookie("addlist_btn", "plus", time() + $t , "/");

        //refresh page
        header("Refresh:0");
    }
    //kill list function
    if (isset($_POST["kill_list"])) {
        //reset active list if the killed list is active
        if($lists[$_POST["kill_list"]]==$active_list) {
            if($_POST["kill_list"]==0) {setcookie("active_list", 0, time() - $t , "/");}
            else {setcookie("active_list", $lists[0], time() + $t , "/");}
        }
        if($_POST["kill_list"]==0&&count($lists)>1) {setcookie("active_list", $lists[1], time() + $t , "/");}
        //delete list´s cookie
        $list_name = $lists[$_POST["kill_list"]];
        setcookie($list_name, "", time() - $t , "/");
        //erase list from lists array
        array_splice($lists, $_POST["kill_list"], 1);
        //re-encode array as string for cookies storage
        $lists_str = implode("ϣ", $lists);
        //refresh array in cookies
        setcookie("lists", $lists_str, time() + $t , "/");
        //refresh page
        header("Refresh:0");
    }
    //show naming newlist field function
    if (isset($_POST["newlist"])) {
        if ($_POST["newlist"]=="field") {
            setcookie("addlist_btn", "field", time() + $t , "/");
        }
        header("Refresh:0");
    }

    //setting active list
    if (isset($_POST["active_list"])) {
        setcookie("active_list", $_POST["active_list"], time() + $t , "/");
        header("Refresh:0");
    }

?>
<!-- --------------------------------------- -->


<!DOCTYPE html>
<html lang="es">
<head>
    <link href="styles.css" rel="stylesheet" title="Default Style">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otra app de "to-dos"</title>
</head>
<body>
</br>
<hr>
</br>

<?php 
//list selection bar
 
echo "<div class='bar'>";
if (!isset($_COOKIE["lists"])) {echo "<p id='init_msg'>cree una lista para empezar ►</p>";} 
foreach($lists as $ind=>&$list) {
    //toogle style for the currently selected list
    $selected_id = "";
    if($list==$active_list) {$selected_id="id='selected_list'";}
    //replace spaces to hit accurately the $_COOKIES key with the form value
    $a = explode("Ɖ", $list); $list_name =implode(" ", $a);
    //display list element
    echo 
    "<div class='list'>
        <form method='post'   >
            <button ".$selected_id." class='list_but' type='submit' name='active_list' value='".$list."'>".$list_name."</button>
        </form>
        <form method='post' class='list_delete_btn'>
            <button type='submit' name='kill_list' value='".$ind."'>X</button>
        </form>
    </div>";
}
//add list btn / name new list field

if($_COOKIE["addlist_btn"]=="plus"){
    echo 
    "<form method='post'>
        <button id='addlist' type='submit' name='newlist' value='field'>+</button>
    </form>
    </div>";
} else {
    echo 
    "<form method='post' id='newlistname'>
        <input type='text'  name='newlistname' placeholder='Nombre de la lista' autocomplete='off' required autofocus></input>
        <input type='submit' id='newlist_ok' name='newlist' value='✓'></input>
    </form>
    </div>";
};


//print the tasks array as tasklist
foreach($arr as $ind=>&$tarea) {
    if($arr_pr[$ind]==0){$prioridad="alta";}else{$prioridad="baja";}
    if($ind==0){continue;}
    echo 
    "<div class='task ".$prioridad."'>
        <p> ".$ind.") ".$tarea."</p>
            <form method='post'>
            <button type='submit' name='close' value='".$ind."'>X</button>
            </form>
    </div>";    
}
?>

<!-- add task form -->
</br>
<form id="nueva" method="post">
    <input type="text" name="text" placeholder="Nueva tarea" autocomplete="off"></input>

    <input type="radio" name="priority" value="0" id="prior"></input>
    <label for="prior" id="high"></label>
    <input type="radio" name="priority" value="1" id="prior2" checked></input>
    <label for="prior2" id="low"></label>

    <input type="submit" <?php if (!isset($_COOKIE["lists"])) {echo "disabled";}?> value="Crear"></input>
</form>

</body>
</html>
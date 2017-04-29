<?php
/**
 * /lists
 * Template Name: PHP Page Template, No Sidebar
 *
 * Description: RubberSoul loves the no-sidebar look as much as
 * you do. Use this page template to remove the sidebar from any page.
 *
 * Tip: to remove the sidebar from all posts and pages simply remove
 * any active widgets from the Main Sidebar area, and the sidebar will
 * disappear everywhere.
 *
 * @since RubberSoul 1.0
 */

get_header(); ?>

  <div id="primary" class="site-content">
    <div id="content" role="main">

      <?php 

  			// echo 'Server date and time is: ';
     //    echo date('l, F j, Y \a\t G:i:s');
        
        if (!is_user_logged_in()) {
          ?>
            <center><h2>
            <?php echo "Please log in to view your lists";?>
            </h2></center>
            <br>
            <br>
          <?php
        }else{

          // $user_info = wp_get_current_user();
          // $username = $user_info->user_login;
          // $first_name = $user_info->first_name;
          // $last_name = $user_info->last_name;
          // echo "\n$first_name $last_name logs into his WordPress site with the user name of $username.\n";
          // echo $user_info->description;

          global $wpdb;
          $current_user = get_current_user_id();
          // echo $current_user;

          // $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_users WHERE ID = $current_user"));

          
          // foreach($results as $res)
          // {
          //   $ul = $res->user_login;
          //   $ue = $res->user_email;
          //   echo "$ul $ue  \n";
          // }
          // $results = $wpdb->get_results($wpdb->prepare("SELECT wp_wagon_list.list_name, wp_wagon_list.list_num, wp_wagon_userlist.privilege FROM wp_wagon_list , wp_wagon_userlist WHERE wp_wagon_userlist.user = $current_user  AND wp_wagon_userlist.list = wp_wagon_list.list_num"));
          // // echo sizeof($results);
          // foreach($results as $res)
          // {
          //   $list_num = $res-> list_num;
          //   echo " $res->list_name, $res->privilege, $list_num\n";

          //   $list_items = $wpdb->get_results($wpdb->prepare("SELECT items.item_name, items.item_url, items.price, items.date_saved FROM wp_wagon_items AS items WHERE list = $list_num"));
          //   foreach($list_items as $item)
          //     {
          //       echo "\t $item->item_name, $item->price, $item->date_saved, $item->item_url\n";
          //     }
          // }
          $lists = $wpdb->get_results($wpdb->prepare("SELECT wp_wagon_list.list_name, wp_wagon_list.list_num, wp_wagon_userlist.privilege FROM wp_wagon_list , wp_wagon_userlist WHERE wp_wagon_userlist.user = %d  AND wp_wagon_userlist.list = wp_wagon_list.list_num", $current_user));
          

          //set default list
          $list_num = $lists[0]->list_num;
          $list_name = $lists[0]->list_name;
          // echo $list_num;
          if(isset($_POST['formSubmit'])) 
          {
            $varList = $_POST['formlist'];
            // $errorMessage = "";

            if(empty($varList)) 
            { //if "select a list..." is selected, default to first list.
              $varList = 1;
              // $errorMessage = "<li>You forgot to select a country!</li>";
            }

            $list_num = $lists[$varList-1]->list_num;
            $list_name = $lists[$varList-1]->list_name;
            $wagonListTable = new Wagon_List_Table($list_num, $current_user);
          }
          if(isset($_POST['newListFormSubmit'])) 
          {
            $varListName = $_POST['form_list_name'];
            $varPrivacy = $_POST['formPrivacy'];
            $errorMessage = "";

            if(!$varListName || !$varPrivacy ) 
            { 
              echo "<li>All forms must be filled completly</li>";
            }else{
              foreach ($lists as $list) {
                if($list->list_name == $varListName){
                  ?>
                  <script language="javascript">
                      alert("You already have a list by the name of: \"<?php echo $varListName;?>\"")
                  </script> 
                  <?php
                  echo "matching list found";
                  $list_name_taken = true;
                }
              }
              if(!$list_name_taken){
                $wpdb->get_results($wpdb->prepare("INSERT INTO wp_wagon_list( list_name, privacy) VALUES ( '%s', '%s')", $varListName, $varPrivacy));
                $newlistid = $wpdb->insert_id;
                $wpdb->get_results($wpdb->prepare("INSERT INTO wp_wagon_userlist(privilege, user, list) VALUES ( 'Owner', %d, %d)", $current_user, $newlistid));
                ?>
                <script language="javascript">
                    alert("\"<?php echo $varListName;?>\" created with privacy of <?php echo $varPrivacy;?>")
                </script> 
                <?php
                header("Refresh:0");
              }
            }
          }
          if(isset($_POST['formNewList'])) 
          {
                ?>
                <form action="<?php ?>" method="post">
                    <p>
                        <label for='form_list_name'>New List Name:</label><br/>
                        <input type="text" name="form_list_name" maxlength="50"/>
                    </p>
                    <p>
                        <label for='formPrivacy'>Privacy Level:</label><br/>
                        <select name="formPrivacy">
                          <option value="">Select...</option>
                          <option value="Public">Public</option>
                          <option value="Friends">Friends</option>
                          <option value="Friends of Friends">Friends of Friends</option>
                          <option value="Private">Private</option>
                        </select>
                    </p>
                    <input type="submit" name="newListFormSubmit" value="Create List" /><br><br>
                </form>
                <?php
          }
          if(isset($_POST['formAddItem'])) 
          {
            // echo "add item selected ";
            ?>
            <form action="<?php ?>" method="post">
              <p>
                  <label for='form_add_item_list'>Add to List:</label><br/>
                  <select name="form_add_item_list">
                  <option value="">Add to List...</option>
                  <?php
                   foreach ($lists as $list){
                          $form_list_name = $list->list_name;
                          $form_list_num = $list->list_num;
                        ?>
                    <option value=<?php echo $form_list_num; ?>> <?php echo $form_list_name; ?> </option>
                    <?php
                    }
                    ?>
              </select> 
              </p><br>
              <p>
                  <label for='form_add_item_name'>New Item Name:</label><br/>
                  <input type="text" name="form_add_item_name" maxlength="50"/>
              </p><br>
              <p>
                  <label for='form_add_item_price'>Price:</label><br/>
                  <input type="text" name="form_add_item_price" maxlength="50"/>
              </p><br>
              <p>
                  <label for='form_add_item_url'>URL:</label><br/>
                  <input type="text" name="form_add_item_url" maxlength="50"/>
              </p>
              <input type="submit" name="addItemFormSubmit" value="Add Item" /><br><br>
            </form>

            <?php
          }
          if(isset($_POST['addItemFormSubmit'])) 
          {
            $varListNum = $_POST['form_add_item_list'];
            $varItemName = $_POST['form_add_item_name'];
            $varPrice = $_POST['form_add_item_price'];
            $varURL = $_POST['form_add_item_url'];

            $errorMessage = "";

            if(!$varListNum|| !$varItemName ) 
            { 
              ?>
              <script language="javascript">
                  alert("Every item must have a name and belong to a list")
              </script> 
              <?php 
            }else{
              $auth = $wpdb->get_results($wpdb->prepare("SELECT list from wp_wagon_userlist WHERE list = %d AND privilege != 'viewer' AND user = %d", $varListNum, $current_user));
              if($auth[0]->list == $varListNum){
                $wpdb->get_results($wpdb->prepare("INSERT INTO wp_wagon_items( list, item_name, price, item_url) VALUES ( %d, '%s', %f, '%s')", $varListNum, $varItemName, $varPrice, $varURL));
                ?>
                <script language="javascript">
                    alert("\"<?php echo $varItemName;?>\" was added to list <?php echo $varListNum?>")
                </script> 
                <?php
                header("Refresh:0");
              }else{
                ?>
                <script language="javascript">
                    alert("You don't have persission to edit this list")
                </script> 
                <?php
              }
            }
          }
          if(isset($_POST['formDeleteList'])) 
          {
            // echo "Delete a List selected ";
            ?>
            <form action="<?php ?>" method="post">
            <label for='formDelList'>Select a List to Delete</label><br>
            <select name="formDelList">
            <option value="">Select a list...</option>
            <?php
             foreach ($lists as $list){
                    $form_list_name = $list->list_name;
                    $form_list_num = $list->list_num;
                  ?>
              <option value=<?php echo $form_list_num; ?>> <?php echo $form_list_name; ?> </option>
            <?php
              }
            ?>
            </select> 
            <input type="submit" name="formDelListSubmit" value="Delete List" />
            </form>
            <br><br><br>
            <?php
          }
          if(isset($_POST['formDelListSubmit'])) 
          {

            $varDelList = $_POST['formDelList'];
            echo "List #",$varDelList, " will be deleted<br><br>";
            $del_items = $wpdb->get_results($wpdb->prepare("SELECT wp_wagon_items.item_num, wp_wagon_items.item_name, wp_wagon_items.list, wp_wagon_list.list_name FROM wp_wagon_items, wp_wagon_list WHERE wp_wagon_list.list_num = %d AND wp_wagon_items.list = %d", $varDelList, $varDelList));
            foreach ($del_items as $d_item){
              
            }
          }
        ?>

        <form action="<?php ?>" method="post">
          <label for='formlist'></label><br>
          <select name="formlist">
            <option value="">Select a list to View...</option>
            <?php
             foreach ($lists as $list){
                    $form_list_name = $list->list_name;
                    $form_list_num = $list->list_num;
                  ?>
            <option value=<?php echo $form_list_num; ?>> <?php echo $form_list_name; ?> </option>
            <?php
          }
            ?>
          </select> 
          <input type="submit" name="formSubmit" value="View List" />
            &emsp;
            <input type="submit" name="formNewList" value="Create New List" />
            <input type="submit" name="formAddItem" value="Add Item to a List" />
            <input type="submit" name="formDeleteList" value="Delete a List" />
            </form>
        <?php  
            // echo $list_name, ", List #", $list_num;
          $wagonListTable = new Wagon_List_Table($list_num, $current_user);
          // echo "----table created----";
          $wagonListTable->prepare_items();
      ?>
      <br>
      <div class="wrap">
          
          <div id="icon-users" class="icon32"><br/></div>
          <h1> <?php echo $list_name; ?> </h1>
          
          
          
          <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
          <form id="products-filter" method="get">
              <!-- For plugins, we also need to ensure that the form posts back to our current page -->
              <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
              <!-- Now we can render the completed list table -->
              <?php                   // echo "----attempting to create table----";
                  // $wagonListTable = new Wagon_List_Table();
                  // echo "----table created----";
                  // $wagonListTable->prepare_items();
                  // echo "++++table prepared++++";
                  $wagonListTable->display();
              ?>
          </form>
      </div>
      <?php 
    // }
  }


// function confirm_delete_sinlge($sql_remove_items){
//     $wpdb->get_results($wpdb->prepare("DELETE FROM `wp_wagon_items` WHERE item_num in $sql_remove_items"));
// }
function delete_item($product_del, $current_user_var){
    global $wpdb;

    // echo "product_del ,current_user_var" , $product_del ,$current_user_var;
    // if($_POST['confirm_del'] == "Confirm Delete") 
    // {
    //     echo "Deletion Confirmed";

    // }
    // if($_POST['reject_del'] == "Cancel") 
    // {
    //     echo "Deletion Rejected";
    //     header("Refresh:0");
    // }
    

    // echo "trying to delete: ", sizeof($product_del), " items:";
    
    if (sizeof($product_del) > 1){
        $products_sql = "(";
        foreach($product_del as $item_num) {
            // echo " #", $item_num;
            $products_sql .= $wpdb->prepare("%d", $item_num) . ", ";
        }
        $products_sql = substr($products_sql, 0, -2);
        $products_sql .= ")";
    }else{
        $products_sql = $wpdb->prepare("(%d)", $product_del);
    }
    
    // echo "<br> products_sql = ", $products_sql;
    // echo "<br> current_user_var = ", $current_user_var;
    $auth = $wpdb->get_results($wpdb->prepare("SELECT wp_wagon_items.item_name, wp_wagon_items.item_num FROM wp_wagon_items, wp_wagon_userlist WHERE wp_wagon_items.item_num in $products_sql AND wp_wagon_items.list = wp_wagon_userlist.list AND %d = wp_wagon_userlist.user AND wp_wagon_userlist.privilege != 'Viewer'", $current_user_var));

    // echo "<br>how many products? ", sizeof($auth);
    if (!$auth){
        echo '<script language="javascript">';
        echo 'alert("You do not have permission to delete the selected items")';
        echo '</script>';
    }else{
        // $item_name = $auth[0]->item_name;
        // echo $item_name;

        $alert_item_list = "";
        $sql_remove_items = "(";
        foreach ( $auth as $item) { 
            $alert_item_list .= "<li>".$item->item_name;
            $sql_remove_items .= $item->item_num .", ";
        }
        $sql_remove_items = substr($sql_remove_items, 0, -2);
        $sql_remove_items .= ")";
        ?>  
        <form action="<?php ?>" method="post">
            <br> <br> <br>
            <H1><?php echo "Are you sure you want to delete items:<br>",$alert_item_list;?></H1>
            <br>
            <input type="submit" name="confirm_del" value="Confirm Delete" />
            <input type="submit" name="reject_del" value="Cancel" />
            <br><br><br><br><br>
        </form>
        <?php
        if($_POST['confirm_del'] == "Confirm Delete") 
        {
            echo "Deletion Confirmed ";
            echo $sql_remove_items;
            
            $wpdb->get_results($wpdb->prepare("DELETE FROM `wp_wagon_items` WHERE item_num in $sql_remove_items"));

            $website = $wpdb->get_results("SELECT option_value FROM wp_options WHERE option_id = 1")->option_value;
            $website .= "lists/";
            header("Location:$website");
        }
        if($_POST['reject_del'] == "Cancel") 
        {
            echo "Deletion Rejected ";
            echo $sql_remove_items;
            $website = $wpdb->get_results("SELECT option_value FROM wp_options WHERE option_id = 1")->option_value;
            $website .= "lists/";
            header("Location:$website");
        }
    }
    
        
        /*
        ?>
        <script language="javascript">
            if (confirm("Are you sure you want to delete items: <?php echo $alert_item_list;?>?") == true) {
                <?php //$wpdb->get_results($wpdb->prepare("DELETE FROM `wp_wagon_items` WHERE item_num in $sql_remove_items")); ?>
                alert("These items have been deleted: <?php echo $alert_item_list;?>")
            } else {
                // alert("These items will NOT be deleted")
           }
        </script>

        <?php
        */
    // }

}


function edit_item($product_del){
    global $wpdb, $current_user_var;
    echo "product_del ,current_user_var" , $product_del ,$current_user_var;
    if(sizeof($product_del) == 1){
        // echo "trying to edit item_num #", $_GET['product']; 
        $auth = $wpdb->get_results($wpdb->prepare("SELECT wp_wagon_items.item_name, wp_wagon_items.item_num, wp_wagon_items.price, wp_wagon_items.item_url FROM wp_wagon_items, wp_wagon_userlist WHERE %d = wp_wagon_items.item_num AND wp_wagon_items.list = wp_wagon_userlist.list AND %d = wp_wagon_userlist.user AND wp_wagon_userlist.privilege != 'Viewer'",$product_del,$current_user_var));
        // echo "<br>permission to edit? ", ($auth)? "true":"false";
        if (!$auth){
            ?>
            <script language="javascript">
                alert("You do not have permission to edit that item")
            </script> 
            <?php
        }else{
            $item_name = $auth[0]->item_name;
            $item_url = $auth[0]->item_url;
            $item_price = $auth[0]->price;
            $var_item_name = $item_name;
            $var_item_url = $item_url;
            $var_item_price = $item_price;

            if($_POST['updateFormSubmit'] == "Update Item") 
            {
              $var_item_name = (!empty($_POST['item_name'])) ? 
                   $_POST['item_name'] : $item_name;
              $var_item_price =  (!empty($_POST['item_price'])) ?
                  $_POST['item_price'] : $item_price;
              $var_item_url = (!empty($_POST['item_url'])) ? 
                  $_POST['item_url'] : $item_url;

              $wpdb->get_results($wpdb->prepare("UPDATE wp_wagon_items SET item_name = '%s', price = '%f', item_url = '%s' WHERE item_num = %d", $var_item_name, $var_item_price, $var_item_url, $auth[0]->item_num));
              ?>
              <script language="javascript">
                  alert("Item: \"<?php echo $item_name;?>\" has been updated")
              </script> 
              <?php
              $website = $wpdb->get_results("SELECT option_value FROM wp_options WHERE option_id = 1")->option_value;
              $website .= "lists/";
              header("Location:$website");
            }
        ?>
        <form action="<?php ?>" method="post">
            <p>
                <label for='item_name'>Item Name:</label><br/>
                <input type="text" name="item_name" maxlength="35" value="<?= $var_item_name; ?>" />
            </p>
            <p>
                <label for='item_price'>Price:</label><br/>
                <input type="text" name="item_price" maxlength="11" value="<?=$var_item_price; ?>" />
            </p>
            <p>
                <label for='item_url'>Item URL:</label><br/>
                <input type="text" name="item_url" maxlength="2000" value="<?=$var_item_url; ?>" />
            </p>
            <input type="submit" name="updateFormSubmit" value="Update Item" />
        </form>
        <?php
        }
    }
}
      ?>
		</div><!-- #content -->
	</div><!-- #primary -->
<?php get_footer(); ?>

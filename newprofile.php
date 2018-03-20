<?php

  session_start();
  if(isset($_SESSION['user'])) {
    include ('header.php');
    $user = $_SESSION['user'];
    $userquery = mysqli_fetch_assoc(mysqli_query($connection, "select * from users where email='" . $user . "'"));
    $username = $userquery['firstname'];
    $dateCreated = date("F j, Y");

      if(isset($_POST['newprofile'])) {
        $accountname = $_POST['accountname'];
        $type = $_POST['type'];
        $date = $_POST['date'];
        $income = $_POST['income'];
        if (!is_numeric($income))  {
          $invalidIncome = "<span style='color:red;'>Please enter a valid income.</span>";
        }
        $newAccountQuery = mysqli_query($connection, "insert into accounts (name, datecreated, owner, balance, type, status) values ('" . ucfirst($accountname) . "',
        '" . $dateCreated . "', '" . $userquery['email'] . "', '" . $income . "', '" . $type . "','primary')");

        $newTransactionQuery = mysqli_query($connection, "insert into transactions (owner, income, date, account) values ('" . $userquery['email'] . "',
        '" . $income . "', '" . $dateCreated . "', '" . $accountname . "'");
        header('location: dashboard.php');
      }
}
else {
  header('location: login.php');
}

 ?>

 <div class="col-12">
   <div class="col-6 shadow center" style="background-color:#fff;">

     <form method="post" action="newprofile.php">
     <input type="hidden" name="newprofile">
     <div class="col-12"><h2>Welcome <?php echo $username; ?></h2></br></br>Let's get started by adding some account information.</br></div>
       <div class="col-12">Account Nickname:</div>
       <div class="col-12" ><input type="text" class="field" name="accountname" required></div>
       <div class="col-12">Account Type:</div>
       <div class="col-12">
        <select class="field" name="type" required>
         <option value="checking">Checking</option>
         <option value="savings">Savings</option>
         <option value="Other">Other</option>
       </select>
     </div>
       <div class="col-12"><?php if(isset($invalidIncome)) { echo $invalidIncome; } ?></div>
       <div class="col-12">Income:</div>
       <div class="col-12"><input type="text" class="field" name="income" required placeholder="$"></div>
       <div class="col-12">Date Recieved:</div>
       <div class="col-12"><input type="date" class="field" name="date" required></div>
     <div class="col-3" style="float:right;"><input type="submit" class="button" value="Next"></div>
   </form>

   </div>
 </div>

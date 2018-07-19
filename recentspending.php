<?php 
  include_once 'functions.php';
  include_once 'connection.php';
  $user = validate_session();
  if(isset($connection)) {
    $accountQuery = mysqli_query($connection, "select * from transactions where owner='" . $user . "' and archived='false'");
    $accountActivity = mysqli_num_rows($accountQuery);
  }
  else {
    header('location:index.php');
  }
?>
    <div class="col-12" style="padding:0px;margin-top:25px;">
      <h2 style="margin-bottom:15px;">Recent Spending</h2>
        <table>
          <tr>
            <th>Category</th>
            <th>Expense</th>
          </tr>
              <?php
      
              $categories = get_categories($connection, $user);

              foreach ($categories as $category) {
                $transactionQuery = mysqli_query($connection, "select * from transactions where owner='" . $user . "' and category='" . $category . "' and archived='false' group by category"); 
                while ($transactionCategory = mysqli_fetch_assoc($transactionQuery)) { 
                  echo "<tr><td>" . $transactionCategory['category'] . "</td>"; 
                  // retrieve sum of expenses for each category
                  $expenseQuery = mysqli_query($connection, "select sum(expense) as total from transactions where owner='" . $user . "' and category='" . $transactionCategory['category'] ."' and archived='false'");
                    while ($expense = mysqli_fetch_assoc($expenseQuery)) {
                      echo "<td class='money'>$ " . number_format((float)$expense['total'], 2, '.', '') . "</td></tr>";
                    }
                } 
              }
            

                ?>
          <tr>

          <?php  
            if ($accountActivity == 0) {
              echo "</table><div class='col-12' style='border:1px solid #ddd;text-align:center;border-bottom:0px;border-top:0px;' >No account activity.</div><table>";
            }         
          
          ?>
            <td><b>Remaining Funds:</b></td>
                  <?php // display remaining balance
                        $balanceQuery = mysqli_query($connection, "select balance from accounts where owner='" . $user . "' and status='primary'");
                        $balance = mysqli_fetch_assoc($balanceQuery);
                        echo "<td class='money'><b> $ " . number_format((float)$balance['balance'], 2 , '.', '') . "</b></td>";
                  ?>
          </tr>
        </table>
        <div class="col-4" style="float:right;padding:0px;margin-top:25px;">
          <a href="#" style="float:right;margin-bottom:25px;">View All</a>
          <input type="button" href="AddExpense.php" onclick="location.href='AddExpense.php'" value="Add Expense" class="button">
        </div>
    </div>

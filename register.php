<?php
        include "header.php";
?>
<div class="content">
  <div class="top-post">
<?php

require 'Client.php';
$redis = new Credis_Client('localhost');

$usernameErr = $firstnameErr = $middlenameErr = $lastnameErr = $titleErr = $RATitleErr = "";
$WebAddressErr = $departmentErr = $instituteErr = $countryErr = $emailErr = $reemailErr = $passwordErr = $repasswordErr = $licenceErr = "";
$username = $firstname = $middlename = $lastname = $title = $RATitle = $WebAddress = $department = $institute = $address = $country = $email = $reemail = $password = $repassword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required.";
    } else {
        $username = test_input($_POST["username"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/^[a-zA-Z0-9@.]*$/",$username)) {
            $usernameErr = "Invalid username format"; 
        }
        if($redis->hexists('passwords',$username) > 0)
            $usernameErr="That username has been registered.";
    }
	
    if (empty($_POST["firstname"])) {
        $firstnameErr = "First name is required.";
    } else {
        $firstname = test_input($_POST["firstname"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/^[a-zA-Z ]*$/",$firstname)) {
            $firstnameErr = "Only letters and white space allowed"; 
        }
    }
	
    if (empty($_POST["middlename"])) {
        $middlename = "";
    } else {
        $middlename = test_input($_POST["middlename"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/^[a-zA-Z ]*$/",$middlename)) {
            $middlenameErr = "Only letters and white space allowed"; 
        }
    }

    if (empty($_POST["lastname"])) {
        $lastnameErr = "Last name is required.";
    } else {
        $lastname = test_input($_POST["lastname"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/^[a-zA-Z ]*$/",$lastname)) {
            $lastnameErr = "Only letters and white space allowed"; 
        }
    }
	
    if (empty($_POST["title"])) {
        $titleErr = "Title is required.";
    } else {
        $title = test_input($_POST["title"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/^[a-zA-Z.]*$/",$title)) {
            $titleErr = "Only letters and point allowed"; 
        }
    }
	
    if (empty($_POST["RATitle"])) {
        $RATitle = "";
    } else {
        $RATitle = test_input($_POST["RATitle"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/^[a-zA-Z.]*$/",$RATitle)) {
            $RATitleErr = "Only letters and point allowed"; 
        }
    }

    if (empty($_POST["WebAddress"])) {
        $WebAddressErr = "Laboratory/University Web Address is required.";
    } else {
        $WebAddress = test_input($_POST["WebAddress"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$WebAddress)) {
            $WebAddressErr = "Invalid website address format"; 
        }
    }
	
    if (empty($_POST["department"])) {
        $department = "";
    } else {
        $department = test_input($_POST["department"]);
    }
	
    if (empty($_POST["institute"])) {
        $instituteErr = "Institute/University is required.";
    } else {
        $institute = test_input($_POST["institute"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/^[a-zA-Z ]*$/",$institute)) {
            $instituteErr = "Only letters and white space allowed"; 
        }
    }
	
    if (empty($_POST["address"])) {
        $address = "";
    } else {
        $address = test_input($_POST["address"]);
    }
	
    if (empty($_POST["country"])) {
        $countryErr = "Country is required.";
    } else {
        $country = test_input($_POST["country"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/^[a-zA-Z ]*$/",$country)) {
            $countryErr = "Only letters and white space allowed"; 
        }
    }
	
    if (empty($_POST["email"])) {
        $emailErr = "Email is required.";
    } else {
        $email = test_input($_POST["email"]);
        // 检查名字是否包含字母和空格
        if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
            $emailErr = "Invalid email format"; 
        }
    }
	
    $reemail = test_input($_POST["reemail"]);
    if ($reemail!=$email) {
        $reemailErr = "Re-enter E-mail address is not match E-mail"; 
    }
	
    if (empty($_POST["password"])) {
        $passwordErr = "Password is required.";
    } else {
        $password = test_input($_POST["password"]);
    }
	
    $repassword = test_input($_POST["repassword"]);
    if ($repassword!=$password) {
        $repasswordErr = "Re-enter password is not match password"; 
    }
	
    if (empty($_POST["licence"])) {
        $licenceErr = "You should read and accept the licence.";
    }
    else
        $licence=$_POST["licence"];
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}

if(strlen($licenceErr)>0 || strlen($repasswordErr)>0 || strlen($passwordErr) || strlen($reemailErr)>0 || strlen($emailErr)>0 || strlen($countryErr)>0 
    || strlen($WebAddressErr)>0 || strlen($instituteErr)>0 || strlen($departmentErr)>0 || strlen($usernameErr)>0 || strlen($firstnameErr)>0 
    || strlen($middlenameErr)>0 || strlen($lastnameErr)>0 || strlen($titleErr)>0 || strlen($RATitleErr)>0 || $_SERVER["REQUEST_METHOD"] != "POST"){
echo '<form id="register" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="post" enctype="multipart/form-data">
    <table width="650" border="0">
      <tr>
        <td width="240">Username:</td>
        <td width="400"><input type="text" name="username" value="'.$username.'">
          <strong>*'.$usernameErr.'</strong></td>
      </tr>
      <tr>
        <td>Your first name:</td>
        <td><input type="text" name="firstname" value="'.$firstname.'">
          <strong>*'.$firstnameErr.'</strong></td>
      </tr>
      <tr>
        <td>Your middle name:</td>
        <td><input type="text" name="middlename" value="'.$middlename.'"><strong>'.$middlenameErr.'</strong></td>
      </tr>
      <tr>
        <td>Your last name:</td>
        <td><input type="text" name="lastname" value="'.$lastname.'">
          <strong>*'.$lastnameErr.'</strong></td>
      </tr>
      <tr>
        <td>Your Title:<br />
        (e.g. Dr., Mr., Mrs., Prof.)</td>
        <td><input type="text" name="title" value="'.$title.'">
          <strong>*'.$titleErr.'</strong></td>
      </tr>
      <tr>
        <td>Research Advisor'."'".'s Title:</td>
        <td><input type="text" name="RATitle" value="'.$RATitle.'" /><strong>'.$RATitleErr.'</strong></td>
      </tr>
      <tr>
        <td>Laboratory/University Web Address:<br />
(For example: www.dddc.ac.cn)
</td>
        <td><input type="text" name="WebAddress" value="'.$WebAddress.'" />
          <strong>*'.$WebAddressErr.'</strong></td>
      </tr>
      <tr>
        <td>Department:</td>
        <td><input type="text" name="department" value="'.$department.'" /></td>
      </tr>
      <tr>
        <td>Name of Institute/University:<br />(no abbreviations, please)</td>
        <td><input type="text" name="institute" value="'.$institute.'"/>
          <strong>*'.$instituteErr.'</strong></td>
      </tr>
      <tr>
        <td>Address:</td>
        <td><input type="text" name="address" value="'.$address.'"/></td>
      </tr>
      <tr>
        <td>Country:</td>
        <td><input type="text" name="country" value="'.$country.'"/>
          <strong>*'.$countryErr.'</strong></td>
      </tr>
      <tr>
        <td>E-mail address:</td>
        <td><input type="text" name="email" />
          <strong>*'.$emailErr.'</strong></td>
      </tr>
      <tr>
        <td>Re-enter E-mail  address</td>
        <td><input type="text" name="reemail" />
          <strong>*'.$reemailErr.'</strong></td>
      </tr>
      <tr>
        <td>Password:</td>
        <td><input type="password" name="password" />
          <strong>*'.$passwordErr.'</strong></td>
      </tr>
      <tr>
        <td>Re-enter password:</td>
        <td><input type="password" name="repassword" />
          <strong>*'.$repasswordErr.'</strong></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><input type="checkbox" name="licence" id="licence" value="accept"/>
          <label for="licence">
        I  HAVE READ AND ACCEPT THE <a href="">LICENCE TERMS</a> AND CONDITIONS FOR <strong>DDB</strong></label><br /><strong>'.$licenceErr.'</strong></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td><button type="submit">accept</button></td>
      </tr>
    </table>
</form>';
}
else{
    $redis->hset('passwords',$username,$password);
    $redis->hset('userinfo_'.$username,'email',$email);
    $redis->hset('userinfo_'.$username,'firstname',$firstname);
    $redis->hset('userinfo_'.$username,'middlename',$middlename);
    $redis->hset('userinfo_'.$username,'lastname',$lastname);
    $redis->hset('userinfo_'.$username,'title',$title);
    $redis->hset('userinfo_'.$username,'RATitle',$RATitle);
    $redis->hset('userinfo_'.$username,'WebAddress',$WebAddress);
    $redis->hset('userinfo_'.$username,'department',$department);
    $redis->hset('userinfo_'.$username,'institute',$institute);
    $redis->hset('userinfo_'.$username,'address',$address);
    $redis->hset('userinfo_'.$username,'country',$country);
    echo "<p>Now, you can can use DDB.</p>";
}
?>

</div>
<div class="clear"></div>
</div>
</div>
<?php
        include "footer.php";
?>


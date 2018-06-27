<?php
require("Connect.php");
$dbh = new PDO('mysql:host=31.170.166.134;charset=utf8;dbname=u663869224_line', 'u663869224_hrmi', 'v06dt22ssn');
$page = isset($_GET['p'])? $_GET['p'] : '';
if($page=='add'){
    try{
        $hd = $_POST['hd'];
        $dt = $_POST['dt'];
        $stmt = $dbh->prepare("INSERT INTO `news`(`newsHD`, `newsDT`,`status`, `CreateBy`, `CreateDate`, `IsDelete`) VALUES (?,?,'ยังไม่ได้ส่ง','Admin',now(),0)");
        $stmt->bindParam(1,$hd);
        $stmt->bindParam(2,$dt);
        if($stmt->execute()){
            print "<div class='alert alert-success' role='alert'>Data has been added</div>";
        } else{
            print "<div class='alert alert-danger' role='alert'>Failed to add data</div>";
        }
    } catch(PDOException $e){
        print "Error!: " . $e->getMessage() . "<br/>";
    }
} else if($page=='update'){
    try{
        $id = $_POST['newsid'];
        $hd = $_POST['hd'];
        $dt = $_POST['dt'];
        $stmt = $dbh->prepare("UPDATE news SET newsHD=?, newsDT=? WHERE newsid=?");
        $stmt->bindParam(1,$hd);
        $stmt->bindParam(2,$dt);
        $stmt->bindParam(3,$id);
        if($stmt->execute()){
            print "<div class='alert alert-success' role='alert'>Data has been updated</div>";
        } else{
            print "<div class='alert alert-danger' role='alert'>Failed to update data</div>";
        }
    } catch(PDOException $e){
        print "Error!: " . $e->getMessage() . "<br/>";
    }
} else if($page=='delete'){
    try{
        $id = $_POST['newsid'];
        $stmt = $dbh->prepare("UPDATE news SET IsDelete = 1 WHERE newsid=?");
        $stmt->bindParam(1,$id);
        if($stmt->execute()){
            print "<div class='alert alert-success' role='alert'>Data has been deleted</div>";
        } else{
            print "<div class='alert alert-danger' role='alert'>Failed to delete data</div>";
        }
    } catch(PDOException $e){
        print "Error!: " . $e->getMessage() . "<br/>";
    }
} else{
    try{
        $stmt = $dbh->prepare("SELECT * FROM news WHERE IsDelete = 0 ORDER BY newsid DESC");
        $stmt->execute();
        while($row = $stmt->fetch()){
            print "<tr data-id=".$row['newsid'].">";
            print "<input type='hidden' name='txtNews' value='".$row['newsid']."' id='txtNews'/>";
            print "<td>".$row['newsHD']."</td>";
            print "<td class='cut-text'>".$row['newsDT']."</td>";
            print "<td>".$row['status']."</td>";
            print "<td class='text-center'><div class='btn-group' role='group' aria-label='group-".$row['newsid']."'>";
            ?>
            <button onclick="editData('<?php echo $row['newsid'] ?>','<?php echo $row['newsHD'] ?>','<?php echo $row['newsDT'] ?>')" type='button' class='btn btn-warning'>Edit</button>
            <button onclick="removeConfirm('<?php echo $row['newsid'] ?>')" type='button' class='btn btn-danger'>Delete</button>
            <?php
            print "</div></td>";
            print "<td>";
            ?>
             <button onclick="SendMS('<?php echo $row['newsid'] ?>')" class="btn btn-primary btn-submit" type="button" >Send</button>
            <?php
           print "</td>";
            print "</tr>";

        }
    } catch(PDOException $e){
        print "Error!: " . $e->getMessage() . "<br/>";
    }
}
?>

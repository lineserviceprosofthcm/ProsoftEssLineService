<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HR News</title>

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="../assets/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="../css/style_hr.css" rel="stylesheet">
</head>
<body>
<div class="navbar block-sub-menu-ess menu_nev" style="width:100%;min-width:980px;border-bottom:3px solid #DDD; background: rgb(247, 247, 247); height:70px; box-shadow:none;-webkit-box-shadow:none;">

    <div class="containner_default con_default">
      <div class="fl_l" style="width:80px;margin-top: 5px;">
        <a class="logo-ess" style="width:80px" href="../ess/Home/Index"></a>
      </div>

      <span class="nav-title hCon">
        <ul class="ul_list" id="menu-list-default">
          <li class="sub-item menu-control" id="Homehover">
            <a href="https://www.google.co.th/?gws_rd=cr&dcr=0&ei=FtzmWbnrOYWujwO87oqIBw" class="glyphicon glyphicon-home fa-2x">
              <div>หน้าแรก</div>
            </a>
          </li>
          <li class="sub-item  menu-control" id="TimeAttLeavehover">
            <a href="/ess/TimeAttendance/Leave/List" class="fa fa-file-text fa-2x">
              <div>ข้อมูลการลา</div>
            </a>
          </li>
          <!--- Main Menu Dropdown Select Application -->
          <li class="sub-item menu-control" id="MenuDropDown">
            <a class="fa fa-th-list fa-2x" onclick="MenuDropDown(0);return false;" href="#">
              <div class="set-menu">เมนู</div>
            </a>
          </li>
          <!--- End Main Menu Dropdown Select Application -->
          <li class="sub-item menu-control" style="cursor: initial !important;" id="Generalhover">
            <a href="/ess/Setting" class="fa fa-cog fa-2x">
              <div>ตั้งค่าทั่วไป</div>
            </a>
          </li>
          <li class="sub-item menu-control">
            <a href="logon.php" class="fa fa-sign-out fa-2x">
              <div>ออกระบบ</div>
            </a>
          </li>
        </ul>
      </span>
    </div>
  </div>
    <div class="container">
        <h3>HR News ESS Bot Line</h3>
        <div class="row">
            <div class="col-md-9">
            <!-- <form action="https://ess-linebot.herokuapp.com/esslinebot.php" method="post"> -->
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <!-- <th width="40">ID</th> -->
                            <th>หัวข้อข่าว</th>
                            <th>รายละเอียด</th>
                            <th>สถานะ</th>
                            <th width="110">Action</th>
                            <th width="35 !important;">Send</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <!-- </form> -->
            </div>
            <div class="col-md-3 cssLL">
                <form id="sidebar">
                    <div class="form-group">
                        <label for="id">ID</label>
                        <input type="text" id="newsid" name="newsid" class="form-control" placeholder="ID" disabled="disabled"/>
                    </div>
                    <div class="form-group">
                        <label for="nm">หัวข้อข่าว</label>
                        <input type="text" id="hd" name="hd" class="form-control" placeholder="หัวข้อข่าว"/>
                    </div>
                    <div class="form-group">
                        <label for="em">รายละเอียด</label>
                        <textarea type="text" id="dt" name="dt" class="form-control" rows="10" placeholder="รายละเอียด"></textarea>
                    </div>
                    <!-- <div class="form-group">
                        <input type="checkbox" id="csn" name="csn"/> 
                        <label for="sn">ส่งทันที</label>
                    </div> -->
                    <div class="form-group">
                        <button type="button" id="save" class="btn btn-primary" onclick="saveData()">Save</button>
                        <button type="button" id="update" class="btn btn-warning" onclick="updateData()">Update</button>
                    </div>
                    </form>
            </div>
        </div>
    </div>

    <script src="../assets/js/jquery.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery.dataTables.min.js"></script>
    <script src="../assets/js/dataTables.bootstrap.min.js"></script>
    <script src="../js/script.js"></script>
</body>
</html>

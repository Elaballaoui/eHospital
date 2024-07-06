 <?php
    global $cssLinkList, $pageTitleList, $cssStyleList;
    require_once __DIR__.'/../config/app.php';
    $cssLink=$cssLinkList['admin'];
    $pageTitle=$pageTitleList['Mes Séances'];
    $cssStyle=$cssStyleList['appointmentSchedulePatientDoctor'];
    include_once '../includes/doctorHeader.php';

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='d'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
    }

    //import database
    include("../includes/connection.php");
    $userrow = $database->query("select * from doctor where docemail='$useremail'");
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["docid"];
    $username=$userfetch["docname"];
    //echo $userid;
 ?>
 <div class="container">
     <?php include_once '../includes/doctorSidebar.php'?>
     <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%" >
                        <a href="index.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Retour</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Mes Séances</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Date d'Aujourd'hui</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php
                                date_default_timezone_set('Africa/Casablanca');
                                $today = date('d-m-Y');
                                echo $today;
                                $list110 = $database->query("select  * from  schedule where docid=$userid;");
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../assets/img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">Mes Séances (<?php echo $list110->num_rows; ?>) </p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                        <table class="filter-container" border="0" >
                            <tr>
                                <td width="10%"></td>
                                <td width="5%" style="text-align: center;">Date:</td>
                                <td width="30%">
                                <form action="" method="post">
                                    <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                                </td>
                                <td width="12%">
                                    <input type="submit"  name="filter" value=" Filter" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                                </form>
                                </td>
                            </tr>
                        </table>
                        </center>
                    </td>
                </tr>
                <?php
                $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid where doctor.docid=$userid ";
                    if($_POST){
                        //print_r($_POST);
                        $sqlpt1="";
                        if(!empty($_POST["sheduledate"])){
                            $sheduledate=$_POST["sheduledate"];
                            $sqlmain.=" and schedule.scheduledate='$sheduledate' ";
                        }
                    }
                ?>
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                        <thead>
                            <tr>
                                <th class="table-headin">Titre de Séance</th>
                                <th class="table-headin">Date & Heure Planifiées</th>
                                <th class="table-headin">Max num pouvant être réservé</th>
                                <th class="table-headin">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $result= $database->query($sqlmain);

                                if($result->num_rows==0){
                                    echo '<tr>
                                        <td colspan="4">
                                            <br><br><br><br>
                                            <center>
                                            <img src="../assets/img/notfound.svg" width="25%">
                                            <br>
                                            <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nous avons rien trouvé en rapport avec vos mots clés !</p>
                                            <a class="non-style-link" href="schedule.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Afficher Toutes les Séances &nbsp;</font></button></a>
                                            </center>
                                            <br><br><br><br>
                                        </td>
                                    </tr>';
                                }else{
                                    for ( $x=0; $x<$result->num_rows;$x++){
                                        $row=$result->fetch_assoc();
                                        $scheduleid=$row["scheduleid"];
                                        $title=$row["title"];
                                        $docname=$row["docname"];
                                        $scheduledate=$row["scheduledate"];
                                        $scheduletime=$row["scheduletime"];
                                        $nop=$row["nop"];
                                        echo '<tr>
                                            <td> &nbsp;'.
                                            substr($title,0,30)
                                            .'</td>
                                            <td style="text-align:center;">
                                                '.substr($scheduledate,0,10).' '.substr($scheduletime,0,5).'
                                            </td>
                                            <td style="text-align:center;">
                                                '.$nop.'
                                            </td>
                                            <td>
                                                <div style="display:flex;justify-content: center;">
                                                    <a href="?action=view&id='.$scheduleid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Voir</font></button></a>                                           
                                                    <a href="?action=drop&id='.$scheduleid.'&name='.$title.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Annuler Séance</font></button></a>
                                                </div>
                                            </td>
                                        </tr>';
                                    }
                            }
                            ?>
                        </tbody>
                        </table>
                        </div>
                        </center>
                   </td> 
                </tr>
            </table>
     </div>
 </div>
    <?php
    if($_GET){
        $id=$_GET["id"];
        $action=$_GET["action"];
        if($action=='drop'){
            $nameget=$_GET["name"];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                <center>
                <h2>Êtes-vous sûr?</h2>
                <a class="close" href="schedule.php">&times;</a>
                <div class="content">Vous souhaitez supprimer cet enregistrement<br>('.substr($nameget,0,40).').</div>
                <div style="display: flex;justify-content: center;">
                    <a href="delete-session.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Oui&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                    <a href="schedule.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;Non&nbsp;&nbsp;</font></button></a>
                </div>
                </center>
                </div>
            </div>
            '; 
        }elseif($action=='view'){
            $sqlmain= "select schedule.scheduleid,schedule.title,doctor.docname,schedule.scheduledate,schedule.scheduletime,schedule.nop from schedule inner join doctor on schedule.docid=doctor.docid  where  schedule.scheduleid=$id";
            $result= $database->query($sqlmain);
            $row=$result->fetch_assoc();
            $docname=$row["docname"];
            $scheduleid=$row["scheduleid"];
            $title=$row["title"];
            $scheduledate=$row["scheduledate"];
            $scheduletime=$row["scheduletime"];

            $nop=$row['nop'];

            $sqlmain12= "select * from appointment inner join patient on patient.pid=appointment.pid inner join schedule on schedule.scheduleid=appointment.scheduleid where schedule.scheduleid=$id;";
            $result12= $database->query($sqlmain12);
            echo '
            <div id="popup1" class="overlay">
                <div class="popup" style="width: 70%;">
                <center>
                    <h2></h2>
                    <a class="close" href="schedule.php">&times;</a>
                    <div class="content"></div>
                    <div class="abc scroll" style="display: flex;justify-content: center;">
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr>
                            <td>
                                <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Afficher les Détails.</p><br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="name" class="form-label">Titre de Séance: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$title.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="Email" class="form-label">Médecin de cette séance: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$docname.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="nic" class="form-label">Date Prévue: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$scheduledate.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="Tele" class="form-label">Heure Prévue: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$scheduletime.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="spec" class="form-label"><b>Patients déjà inscrits à cette session:</b> ('.$result12->num_rows."/".$nop.')</label>
                                <br><br>
                            </td>
                        </tr>
                        <tr>
                        <td colspan="4">
                            <center>
                             <div class="abc scroll">
                             <table width="100%" class="sub-table scrolldown" border="0">
                             <thead>
                                 <tr>   
                                    <th class="table-headin">ID Patient</th>
                                    <th class="table-headin">Nom Patient</th>
                                    <th class="table-headin">Numéro de réservation</th>   
                                    <th class="table-headin">Téléphone Patient</th> 
                                   </tr>
                             </thead>
                             <tbody>';

                                     $result= $database->query($sqlmain12);

                                     if($result->num_rows==0){
                                         echo '<tr>
                                             <td colspan="7">
                                                 <br><br><br><br>
                                                 <center>
                                                 <img src="../assets/img/notfound.svg" width="25%">
                                                 <br>
                                                    <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nous avons rien trouvé en rapport avec vos mots clés !</p>
                                                    <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Afficher tous les réservations &nbsp;</font></button></a>
                                                 </center>
                                                 <br><br><br><br>
                                             </td>
                                         </tr>';
                                     }
                                     else{
                                         for ( $x=0; $x<$result->num_rows;$x++){
                                             $row=$result->fetch_assoc();
                                             $apponum=$row["apponum"];
                                             $pid=$row["pid"];
                                             $pname=$row["pname"];
                                             $ptel=$row["ptel"];

                                             echo '<tr style="text-align:center;">
                                                <td>
                                                    '.substr($pid,0,15).'
                                                </td>
                                                 <td style="font-weight:600;padding:25px">'.
                                                    substr($pname,0,25)
                                                 .'</td >
                                                 <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                                    '.$apponum.'
                                                 </td>
                                                 <td>
                                                    '.substr($ptel,0,25).'
                                                 </td>
                                             </tr>';
                                         }
                                         }

                                echo '</tbody>
                             </table>
                             </div>
                             </center>
                        </td> 
                     </tr>
                    </table>
                    </div>
                </center>
                <br><br>
                </div>
            </div>
            ';  
    }
}
    ?>
 <?php include_once '../includes/footer.php' ?>
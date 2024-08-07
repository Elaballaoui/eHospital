<?php
    global $cssLinkList, $pageTitleList, $cssStyleList;
    require_once __DIR__.'/../config/app.php';
    $cssLink=$cssLinkList['admin'];
    $pageTitle=$pageTitleList['Mes réservations'];
    $cssStyle=$cssStyleList['doctorsScheduleAppointmentPatient'];
    include_once '../includes/patientHeader.php';

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='p'){
            header("location: ../login.php");
        }else{
            $useremail=$_SESSION["user"];
        }
    }else{
        header("location: ../login.php");
    }

    //import database
    include("../includes/connection.php");
    $sqlmain= "select * from patient where pemail=?";
    $stmt = $database->prepare($sqlmain);
    $stmt->bind_param("s",$useremail);
    $stmt->execute();
    $userrow = $stmt->get_result();
    $userfetch=$userrow->fetch_assoc();
    $userid= $userfetch["pid"];
    $username=$userfetch["pname"];

    //echo $userid;
    //echo $username;
    $sqlmain= "select appointment.appoid,schedule.scheduleid,schedule.title,doctor.docname,patient.pname,schedule.scheduledate,schedule.scheduletime,appointment.apponum,appointment.appodate from schedule inner join appointment on schedule.scheduleid=appointment.scheduleid inner join patient on patient.pid=appointment.pid inner join doctor on schedule.docid=doctor.docid  where  patient.pid=$userid ";

    if($_POST){
        //print_r($_POST);
        if(!empty($_POST["sheduledate"])){
            $sheduledate=$_POST["sheduledate"];
            $sqlmain.=" and schedule.scheduledate='$sheduledate' ";
        };
        //echo $sqlmain;
    }

    $sqlmain.="order by appointment.appodate  asc";
    $result= $database->query($sqlmain);
    ?>
    <div class="container">
        <?php include_once '../includes/patientSidebar.php'?>
        <div class="dash-body">
            <table border="0" width="100%" style=" border-spacing: 0;margin:0;padding:0;margin-top:25px; ">
                <tr >
                    <td width="13%" >
                        <a href="index.php" ><button  class="login-btn btn-primary-soft btn btn-icon-back"  style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px"><font class="tn-in-text">Retour</font></button></a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">Historique de mes Réservations</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Date d'Aujourd'hui</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php
                                date_default_timezone_set('Africa/Casablanca');
                                $today = date('d-m-Y');
                                echo $today;
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button  class="btn-label"  style="display: flex;justify-content: center;align-items: center;"><img src="../assets/img/calendar.svg" width="100%"></button>
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="4" >
                        <div style="display: flex;margin-top: 40px;">
                        <div class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49);margin-top: 5px;">Schedule a Session</div>
                        <a href="?action=add-session&id=none&error=0" class="non-style-link"><button  class="login-btn btn-primary btn button-icon"  style="margin-left:25px;background-image: url('../img/icons/add.svg');">Add a Session</font></button>
                        </a>
                        </div>
                    </td>
                </tr> -->
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;" >
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">Mes Réservations (<?php echo $result->num_rows; ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;" >
                        <center>
                        <table class="filter-container" border="0" >
                            <tr>
                               <td width="10%">
                               </td>
                                <td width="5%" style="text-align: center;">
                                    Date:
                                </td>
                                <td width="30%">
                                <form action="" method="post">
                                    <input type="date" name="sheduledate" id="date" class="input-text filter-container-items" style="margin: 0;width: 95%;">
                                </td>
                                <td width="12%">
                                    <input type="submit"  name="filter" value=" Filtre" class=" btn-primary-soft btn button-icon btn-filter"  style="padding: 15px; margin :0;width:100%">
                                    </form>
                                </td>
                            </tr>
                        </table>
                        </center>
                    </td>
                </tr>
                <tr>
                   <td colspan="4">
                       <center>
                        <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0" style="border:none">
                        <tbody>
                            <?php
                                if($result->num_rows==0){
                                    echo '<tr>
                                        <td colspan="7">
                                            <br><br><br><br>
                                            <center>
                                            <img src="../assets/img/notfound.svg" width="25%">
                                            <br>
                                            <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">Nous avons rien trouvé en rapport avec vos mots clés !</p>
                                            <a class="non-style-link" href="appointment.php"><button  class="login-btn btn-primary-soft btn"  style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Afficher tous les Rendez-vous &nbsp;</font></button></a>
                                            </center>
                                            <br><br><br><br>
                                        </td>
                                    </tr>';
                                }
                                else{
                                    for ( $x=0; $x<($result->num_rows);$x++){
                                        echo "<tr>";
                                        for($q=0;$q<3;$q++){
                                            $row=$result->fetch_assoc();
                                            if (!isset($row)){
                                            break;
                                            };
                                            $scheduleid=$row["scheduleid"];
                                            $title=$row["title"];
                                            $docname=$row["docname"];
                                            $scheduledate=$row["scheduledate"];
                                            $scheduletime=$row["scheduletime"];
                                            $apponum=$row["apponum"];
                                            $appodate=$row["appodate"];
                                            $appoid=$row["appoid"];
    
                                            if($scheduleid==""){
                                                break;
                                            }
    
                                            echo '
                                                <td style="width: 25%;">
                                                    <div  class="dashboard-items search-items"  >
                                                        <div style="width:100%;">
                                                            <div class="h3-search">
                                                               Date de Réservation: '.substr($appodate,0,30).'<br>
                                                               Numéro de Réference: OC-000-'.$appoid.'
                                                            </div>
                                                            <div class="h1-search">
                                                                '.substr($title,0,21).'<br>
                                                            </div>
                                                            <div class="h3-search">
                                                                Numéro de Rendez-vous:<div class="h1-search">0'.$apponum.'</div>
                                                            </div>
                                                            <div class="h3-search">
                                                                '.substr($docname,0,30).'
                                                            </div>
                                                            <div class="h4-search">
                                                                Date Planifiée: '.$scheduledate.'<br>Commence: <b>@'.substr($scheduletime,0,5).'</b> (24h)
                                                            </div>
                                                            <br>
                                                            <a href="?action=drop&id='.$appoid.'&title='.$title.'&doc='.$docname.'" ><button  class="login-btn btn-primary-soft btn "  style="padding-top:11px;padding-bottom:11px;width:100%"><font class="tn-in-text">Annuler la Réservation</font></button></a>
                                                        </div>      
                                                    </div>
                                                </td>';
                                        }
                                        echo "</tr>";
                                // for ( $x=0; $x<$result->num_rows;$x++){
                                //     $row=$result->fetch_assoc();
                                //     $appoid=$row["appoid"];
                                //     $scheduleid=$row["scheduleid"];
                                //     $title=$row["title"];
                                //     $docname=$row["docname"];
                                //     $scheduledate=$row["scheduledate"];
                                //     $scheduletime=$row["scheduletime"];
                                //     $pname=$row["pname"];
                                //     
                                //     
                                //     echo '<tr >
                                //         <td style="font-weight:600;"> &nbsp;'.
                                        
                                //         substr($pname,0,25)
                                //         .'</td >
                                //         <td style="text-align:center;font-size:23px;font-weight:500; color: var(--btnnicetext);">
                                //         '.$apponum.'
                                        
                                //         </td>
                                //         <td>
                                //         '.substr($title,0,15).'
                                //         </td>
                                //         <td style="text-align:center;;">
                                //             '.substr($scheduledate,0,10).' @'.substr($scheduletime,0,5).'
                                //         </td>
                                        
                                //         <td style="text-align:center;">
                                //             '.$appodate.'
                                //         </td>

                                //         <td>
                                //         <div style="display:flex;justify-content: center;">
                                        
                                //         <!--<a href="?action=view&id='.$appoid.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-view"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">View</font></button></a>
                                //        &nbsp;&nbsp;&nbsp;-->
                                //        <a href="?action=drop&id='.$appoid.'&name='.$pname.'&session='.$title.'&apponum='.$apponum.'" class="non-style-link"><button  class="btn-primary-soft btn button-icon btn-delete"  style="padding-left: 40px;padding-top: 12px;padding-bottom: 12px;margin-top: 10px;"><font class="tn-in-text">Cancel</font></button></a>
                                //        &nbsp;&nbsp;&nbsp;</div>
                                //         </td>
                                //     </tr>';
                                    
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
        if($action=='booking-added'){
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                    <br><br>
                    <h2>Réservation Réussie.</h2>
                    <a class="close" href="appointment.php">&times;</a>
                    <div class="content">Votre Numéro de Rendez-vous est '.$id.'.<br><br>  </div>
                    <div style="display: flex;justify-content: center;">
                        <a href="appointment.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;OK&nbsp;&nbsp;</font></button></a>
                        <br><br><br><br>
                    </div>
                    </center>
                </div>
            </div>
            ';
        }elseif($action=='drop'){
            $title=$_GET["title"];
            $docname=$_GET["doc"];
            
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <h2>Êtes-vous Sûr?</h2>
                        <a class="close" href="appointment.php">&times;</a>
                        <div class="content">
                            Vous Souhaitez Annuler ce Rendez-vous?<br><br>
                            Nom de Séance: &nbsp;<b>'.substr($title,0,40).'</b><br>
                            Nom du médecin&nbsp; : <b>'.substr($docname,0,40).'</b><br><br>
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <a href="delete-appointment.php?id='.$id.'" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"<font class="tn-in-text">&nbsp;Oui&nbsp;</font></button></a>&nbsp;&nbsp;&nbsp;
                            <a href="appointment.php" class="non-style-link"><button  class="btn-primary btn"  style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;"><font class="tn-in-text">&nbsp;&nbsp;Non&nbsp;&nbsp;</font></button></a>
                        </div>
                    </center>
                </div>
            </div>
            '; 
        }elseif($action=='view'){
            $sqlmain= "select * from doctor where docid=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("i",$id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row=$result->fetch_assoc();
            $name=$row["docname"];
            $email=$row["docemail"];
            $spe=$row["specialties"];
            
            $sqlmain= "select sname from specialties where id=?";
            $stmt = $database->prepare($sqlmain);
            $stmt->bind_param("s",$spe);
            $stmt->execute();
            $spcil_res = $stmt->get_result();
            $spcil_array= $spcil_res->fetch_assoc();
            $spcil_name=$spcil_array["sname"];
            $nic=$row['docnic'];
            $tele=$row['doctel'];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                <center>
                    <h2></h2>
                    <a class="close" href="doctors.php">&times;</a>
                    <div class="content">
                        <strong>eHospital App</strong> <br>
                    </div>
                    <div style="display: flex;justify-content: center;">
                    <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                        <tr>
                            <td>
                                <p style="padding: 0;margin: 0;text-align: left;font-size: 25px;font-weight: 500;">Voir les Détails.</p><br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="name" class="form-label">Nom Complet: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                '.$name.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="Email" class="form-label">Email: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                            '.$email.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="nic" class="form-label">CIN: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                            '.$nic.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="Tele" class="form-label">Téléphone: </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                            '.$tele.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                                <label for="spec" class="form-label">Spécialités: </label>
                                
                            </td>
                        </tr>
                        <tr>
                            <td class="label-td" colspan="2">
                            '.$spcil_name.'<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="doctors.php"><input type="button" value="OK" class="login-btn btn-primary-soft btn" ></a> 
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
<?php include_once '../includes/footer.php'?>
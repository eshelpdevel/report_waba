<?php
###############################################################################################################
# Date          |    Type    |   Version                                                                      # 
############################################################################################################### 
# 17-03-2025    |   Create   |  1.1703.2025                                                                 #
############################################################################################################### 

include "../../sysconf/con_reff.php";
include "../../sysconf/global_func.php";
include "../../sysconf/session.php";
include "../../sysconf/db_config.php";
include "global_func_report.php";

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0);
set_time_limit(0);          # unlimited transfer time

$condb = connectDB();
session_start();

$v_agentid    = get_session("v_agentid");
$v_agentname  = get_session("v_agentname");

$reportlist   = get_param('sel_report');
$dateperiod   = get_param('rpt_period');
// $vgroup_id    = get_param("rpt_group");
// $vagent_id    = get_param("rpt_user");
// $vgroup_id    = get_param("rpt_group");
$vgroup_id    = get_param("rpt_group");
// $vagent_id    = get_param("rpt_user");
$vagent_id    = get_param("rpt_user");
$type         = get_param('type');

$date_from    = substr($dateperiod,0,10);
$date_to      = substr($dateperiod,12);
$vstart_time  = $date_from." 00:00:00";
$vend_time    = $date_to." 23:59:59";
$preparedby   = $v_agentname;




function tgl_indo($tanggal){
  $bulan = array (
    1 =>   'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
  );
  $pecahkan = explode('-', $tanggal);
  
  // variabel pecahkan 0 = tahun
  // variabel pecahkan 1 = bulan
  // variabel pecahkan 2 = tanggal
 
  return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

if($cmbtype==1){
  $repname = "By Provider";
}else{
  $repname = "By User";
}
 

 //array agent
  $totag = 0;
  $sql_str1 = "SELECT a.id, a.agent_id, a.agent_name FROM cc_agent_profile a";
  $sql_res1 = mysqli_query($condb, $sql_str1);
  while($sql_rec1 = mysqli_fetch_array($sql_res1)) {
    $id = $sql_rec1["id"];
    $array_agentid[$id]   = $sql_rec1["id"];
    $array_agentname[$id] = $sql_rec1["agent_name"];
    $array_contact_id[$id] = $sql_rec1["agent_id"];
    $totag++;
  }
  mysqli_free_result($sql_res1); 

  //array reason
  // $array_reason[] = "";
  // $sql_str1 = "SELECT a.id, a.aux_code FROM cc_aux_reason a";
  // $sql_res1 = mysqli_query($condb, $sql_str1);
  // while($sql_rec1 = mysqli_fetch_array($sql_res1)) {
  //   $array_reason[$sql_rec1["id"]]   = $sql_rec1["aux_code"];
  // }
  // mysqli_free_result($sql_res1);

  // array group
  $totgrp = 0;
  $sql_str1x = "select a.id, a.group_id, a.group_name from cc_group_profile a";
  $sql_res1x = mysqli_query($condb, $sql_str1x);
  while($sql_rec1x = mysqli_fetch_array($sql_res1x)) {
    $id = $sql_rec1x["id"];
    $array_groupid[$id]   = $sql_rec1x["id"];
    $array_groupname[$id] = $sql_rec1x["group_name"];
    $array_group_id[$id] = $sql_rec1x["group_id"];
    $totgrp++;
  }
  mysqli_free_result($sql_res1x);



 //array agent
  $array_agent_id[]   = "";
  $array_agent_name[] = "";
  $sql_str1 = "SELECT a.id, a.agent_id, a.agent_name FROM cc_agent_profile a";
  $sql_res1 = mysqli_query($condb, $sql_str1);
  while($sql_rec1 = mysqli_fetch_array($sql_res1)) {
    $array_agent_id[$sql_rec1["id"]]   = $sql_rec1["agent_id"];
    $array_agent_name[$sql_rec1["id"]] = $sql_rec1["agent_name"];
  }
  mysqli_free_result($sql_res1); 

  // array group
  $array_groupid[] = "";
  $sql_str1x = "select a.id, a.group_name from cc_group_profile a";
  $sql_res1x = mysqli_query($condb, $sql_str1x);
  while($sql_rec1x = mysqli_fetch_array($sql_res1x)) {
    $array_groupid[$sql_rec1x["id"]] = $sql_rec1x["group_name"];
  }
  mysqli_free_result($sql_res1x);

$sqlwhere = "";
// if ($vgroup_id != "") {
//    $sqlwhere .= "AND 
//                  a.group_id = '$vgroup_id' "; 
// }
if ($vagent_id != "") {
   $sqlwhere .= " AND a.agent_id = '".$vagent_id."' "; 
}
    
  function get_first_response($live_time, $out_time) {
    $first = "";
    
    // $datetime1 = new DateTime($out_time);
    // $datetime2 = new DateTime($live_time);
    // $interval = $datetime1->diff($datetime2);
    // $first = $interval->format('%H:%I:%S');

    $date = new DateTime($live_time);
    $date2 = new DateTime($out_time);

    $first = $date2->getTimestamp() - $date->getTimestamp();
    
    return $first;

  }


error_reporting(E_ALL);
ini_set('display_errors', False);
ini_set('display_startup_errors', False);
date_default_timezone_set('Asia/Jakarta');

/** Include PHPExcel */
require_once  '../../library/PHPExport/php-export-data.class.php';

$exporter = new ExportDataExcel('browser', 'blast_menu.xls');

$exporter->initialize(); // starts streaming data to web browser

// echo "<pre>";

$exporter->addRow(array("No",
      "Channel",
      "Blast Name",
      "Template Category",
      "Distribution Type",
      "Created By",
      "Assigned To",
      "Created Time",
      "Saved Time",
      "Scheduled Time",
      "Processed Time",
      "Total Participant",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",
      "",

      "",
      "",
      "" ));
        

       $no        = 1;
       $groupname = "";
       $agentname = "";
       $tot_first_sec = 0;
       $tot_duration_sec = 0;
       $tot_sess = 0;
       $sql = "SELECT 
                a.id,
                b.channel_name as channel,
                a.blast_name,
                a.close_remark,
                CONCAT(c.agent_name,' / ',c.agent_id) as created_by,
                a.created_time,
                a.process_time,
                a.close_time,
                a.schedule_time,
                count(d.id) as participants,
                e.category_id,
                CONCAT(f.agent_name,' / ',f.agent_id) as assigned_to
               from cc_sosmed_blast a
               left join cc_ticket_channel b on a.channel = b.id
               left join cc_agent_profile c on a.created_by = c.id
               left join cc_sosmed_blast_participant d on d.blast_id = a.id
               left join cc_wa_template e on a.hsm_template = e.template_id
               left join cc_agent_profile f on a.assign_id = f.id
               WHERE a.`status` = '1' AND a.created_time >= '".$vstart_time."' 
               AND a.created_time <= '".$vend_time."'
               group by a.id"; 

      $res                    = mysqli_query($condb, $sql);
      while($rec              = mysqli_fetch_array($res)){
        $id                   = $rec["id"];
        $channel              = $rec["channel"];
        $blast_name           = $rec["blast_name"];
        $close_remark         = $rec["close_remark"];
        $created_by           = $rec["created_by"];
        $created_time         = $rec["created_time"];
        $process_time         = $rec["process_time"];
        $close_time           = $rec["close_time"];
        $schedule_time        = $rec["schedule_time"];
        $participants        = $rec["participants"];
        $category_id        = $rec["category_id"];
        $assigned_to        = $rec["assigned_to"];
            
            $exporter->addRow(array(
              $no,
              $channel,
              $blast_name,
              $category_id,
              ucwords($close_remark),
              $created_by,
              $assigned_to,
              $created_time,
              $process_time,
              $close_time,
              $schedule_time,
              $participants." Participants",
              "",
              "",
              "",
              "",
              "",
              "",
              "",
              "",
              "",
              "",
              "",
              "",

              "",
              "",
              ""));


      $exporter->addRow(array("",
      "",
      "",
      "",
      "",
      "No",
      "Participant",
      "Destination",
      "Process time",
      "Request Status",
      "Response Status",
      "Sent time",
      "Delivered time",
      "Read time",
      "Call Status",
      "Tahun",
      "Unit",
      "Cabang",
      "CusId",
      "Order No",
      "Label",
      "Produk",
      "Nominal Denda",
      "DPD",
      "No Polisi",
      "Jatuh Tempo",
      "Angsuran ke-"));

      ///////////////tambahan
      $nox=1;
              $sql2 = "SELECT 
                          RIGHT(username,4) as username,
                          a.usercontactname,
                          a.created_time,
                          CASE
                              WHEN b.sent_status = 1 THEN \"Success\"
                              WHEN b.sent_status = 2 THEN \"Failed\"
                              WHEN b.sent_status = 0 THEN \"Queue\"
                              ELSE \"\"
                          END as request_status,
                          a.outbox_id,
                          CONCAT('') as teleuploadinfo,
                          a.tahun,
                          a.unit,
                          a.cabang,
                          a.cusid,
                          a.orderno,
                          a.label,
                          a.produk,
                          a.nominaldenda,
                          a.dpd,
                          a.nopolisi,
                          a.jatuhtempo,
                          a.angsuranke,
                          b.sent_status,
                          b.sent_code,
                          b.sent_time,
                          b.delivered_time,
                          b.read_time,
                          b.pushed_remark
                    FROM cc_sosmed_blast_participant a  
                    LEFT JOIN cc_wa_outbox b on a.outbox_id = b.id
                    
                    
                    where a.blast_id = '$id' and a.status = 1
                    group by a.id"; 

              $res2                       = mysqli_query($condb, $sql2);
              while($rec2                 = mysqli_fetch_array($res2)){
                $username                 = $rec2["username"];
                $usercontactname          = $rec2["usercontactname"];
                $created_time             = $rec2["created_time"];
                $request_status           = $rec2["request_status"];
                $outbox_id                = $rec2["outbox_id"];
                $sent_status              = $rec2["sent_status"];
                $sent_code                = $rec2["sent_code"];

                $sent_time           = $rec2["sent_time"];
                $delivered_time           = $rec2["delivered_time"];
                $read_time           = $rec2["read_time"];
                $pushed_remark           = $rec2["pushed_remark"];

                $response_status          = "";
                if ($sent_status == 1) {
                    $queryT = "SELECT c.status,c.message_time FROM cc_wa_ack c WHERE c.report_id = '$sent_code'";
                    $resultT = mysqli_query($condb,$queryT);
                    if ($resultT) {
                      while ($rowT     = mysqli_fetch_row($resultT)){
                        $response_status    = $rowT[0];
                        $message_time    = $rowT[1];

                        if ($response_status == 'sent') {
                          $sent_time = $message_time;
                        }elseif ($response_status == 'delivered') {
                          $delivered_time = $message_time;
                        }elseif ($response_status == 'read') {
                          $read_time = $message_time;
                        }
                       
                        
                      }
                      mysqli_free_result($resultT);
                    }
                    $response_status = $pushed_remark;
                }elseif ($sent_status == 2) {
                  $response_status = $sent_code;
                }elseif ($sent_status == 0) {
                  $response_status = "Waiting for Response Status";
                }else{
                  $response_status = " - ";
                }
                
                if ($response_status=='') {
                    $response_status = $pushed_remark;
                }

                $teleuploadinfo           = $rec2["teleuploadinfo"];

                $tahun                    = $rec2["tahun"];
                $unit                     = $rec2["unit"];
                $cabang                   = $rec2["cabang"];
                $cusid                    = $rec2["cusid"];
                $orderno                  = $rec2["orderno"];
                $label                    = $rec2["label"];
                $produk                   = $rec2["produk"];
                $nominaldenda             = $rec2["nominaldenda"];
                $dpd                      = $rec2["dpd"];
                $nopolisi                 = $rec2["nopolisi"];
                $jatuhtempo               = $rec2["jatuhtempo"];
                $angsuranke               = $rec2["angsuranke"];

                $exporter->addRow(array("",
                                    "",
                                    "",
                                    "",
                                    "",
                                    $nox,
                                    $usercontactname,
                                    "*******".$username,
                                    $created_time,
                                    $request_status,
                                    $response_status,
                                    $sent_time,
                                    $delivered_time,
                                    $read_time,
                                    $teleuploadinfo,
                                    $tahun,
                                    $unit,
                                    $cabang,
                                    $cusid,
                                    $orderno,
                                    $label,
                                    $produk,
                                    $nominaldenda,
                                    $dpd,

                                    $nopolisi,
                                    $jatuhtempo,
                                    $angsuranke ));

                
                      $nox++;

             }

      ///////////////end tambahan

                $no++;  

              
            // }
}
        mysqli_free_result($res);

        $exporter->finalize(); // writes the footer, flushes remaining data to browser.
        
        disconnectDB($condb);
        
        exit(); // all done
       
// echo "</pre>";
?>
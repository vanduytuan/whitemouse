<?php

class Wovodat {

    private $gmt_output_folder;

    /*
     * Constructor
     */
    public function __construct() {
        $this->connectWovodatServer();
        $this->gmt_output_folder = dirname(__FILE__) . "/../output/";
    }

    // connect to the server for wovodat data
    public function connectWovodatServer() {
        include "php/include/db_connect_view.php";
        return $link;
    }
    // remove the file at $dir
    private function recursivermdir($dir) {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file))
                $this->recursivermdir($file);
            else
                unlink($file);
        }

        rmdir($dir);
    }

    // delete file in the output folder for gmt module
    public function deleteOutputFolder($folderName) {
        if (substr($folderName, 0, 7) != 'wovodat')
            return;
        $outputDir = opendir($this->gmt_output_folder);
        if ($outputDir) {
            $folder = $this->gmt_output_folder . $folderName . "/";
            foreach (glob($folder . "*") as $file)
                unlink($file);
            foreach (glob($folder . ".gmt*") as $file)
                unlink($file);
            rmdir($folder);
        }
    }

    // clear output folder for gmt files
    public function clearOutputFolder() {
        $outputDir = opendir($this->gmt_output_folder);
        if ($outputDir) {
            // keep it in the server for 8 hours
            $MAX_TIME = 8 * 60 * 60;
            $currentTime = time();
            $change = 0;
            $temp = 0;

            $subfolder = "";
            $subfolder_array = Array();
            while (false !== ($subfolder = readdir($outputDir)))
                array_push($subfolder_array, $subfolder);

            $files = Array();
            for ($i = 0; $i < sizeof($subfolder_array); $i++) {
                if ($subfolder_array[$i] != '.' && $subfolder_array[$i] != '..') {
                    array_push($files, $subfolder_array[$i]);             // Get all image folders without '.' & '..'
                }
            }
            $length = sizeof($files);
            for ($i = 0; $i < $length; $i++) {
                $temp = stat($this->gmt_output_folder . $files[$i]);
                $change = $currentTime - $temp['ctime'];
                if ($change <= $MAX_TIME)
                    continue;

                if (substr($files[$i], 0, 7) != 'wovodat')
                    continue;
                // Delete one array room per one time
                $imagefolder2 = $this->gmt_output_folder . $files[$i] . "/";
                // Delete all visible files
                foreach (glob($imagefolder2 . '*') as $file)
                    unlink($file);
                // Delete all hidden/invisible files
                foreach (glob($imagefolder2 . '.' . 'gmt*') as $file) {
                    // we ignore the file name '.' and '..' as they are the way that 
                    // Windows will use to go back to previous level
                    unlink($file);
                }
                rmdir($imagefolder2);
            }
        }
    }

    /*
     * Return the list of all available volcano in our database
     */

    public function getVolcanoList() {
        mysql_query("set character_set_results='utf8'");
        $result = mysql_query("select vd_name, vd_cavw FROM vd  ORDER BY vd_name");
        $row = mysql_fetch_array($result);
        if ($row === false)
            return;
        $results = Array();
        $object;
        while (true) {
            $object = "";
            $object[1] = $row[0];
            $object[2] = $row[1];
            array_push($results, $object);
            $row = mysql_fetch_array($result);
            if ($row == false)
                break;
        }
        echo json_encode($results);
    }

    /*
     * Return the list of all eruption of one specifci volcano
     */
    public function getEruptionList($cavw) {
        mysql_query("set character_set_results='utf8'");
        $result = mysql_query("select ed.ed_code , ed.ed_stime, ed.ed_stime_bc from vd, ed where vd.vd_cavw = '$cavw' and vd.vd_id = ed.vd_id order by ed.ed_stime desc");
        $i = mysql_fetch_array($result);
        if ($i === false)
            return;
        while (true) {
            if ($i[1] == "0000-00-00 00:00:00") {
                $i[1] = "BC" . $i[2];
            } else {
                $i[1] = explode(" ", $i[1]);
                $i[1] = $i[1][0] . "&" . $i[1][1];
            }
            echo "$i[0]&$i[1]";
            $i = mysql_fetch_array($result);
            if ($i === false)
                break;
            echo ";";
        }
    }

    /*
     * Get latitude and longitute of a volcano
     * 
     */

    public function getLatLon($cavw) {
        $result = mysql_query("select vd_inf_slat,vd_inf_slon, vd_inf_selev from vd_inf where vd_inf_cavw = '$cavw'");
        $i = mysql_fetch_array($result);
        if ($i === false)
            return;
        echo "$i[0];$i[1];$i[2]";
    }

    /*
     * Destructor
     */

    public function __destruct() {
        mysql_close();
    }

    /*
     * Get cc_url of a volcano
     * of a specific cavw
     */

    public function getCCUrl($cavw) {
        $query1 = mysql_query("select cc_id from vd where vd_cavw='" . $cavw . "'");
        $object = "";
        $result1 = mysql_fetch_array($query1);
        if ($result1 !== false) {
            $cc_id = $result1[0];
            $query1_2 = mysql_query("select cc_url from cc where cc_id='" . $cc_id . "'");
            $result1_2 = mysql_fetch_array($query1_2);

            if ($result1_2 !== false)
                $object['owner1'] = $result1_2[0];
            else
                $object['owner1'] = "";
        }

        // second cc_id
        $query1 = mysql_query("select cc_id2 from vd where vd_cavw='" . $cavw . "'");
        $result1 = mysql_fetch_array($query1);
        if ($result1 !== false) {
            $cc_id = $result1[0];
            $query1_2 = mysql_query("select cc_url from cc where cc_id='" . $cc_id . "'");
            $result1_2 = mysql_fetch_array($query1_2);
            if ($result1_2 !== false)
                $object['owner2'] = $result1_2[0];
            else
                $object['owner2'] = "";
        }

        $query2 = mysql_query("select vd_inf_status, vd_inf_type from vd_inf where vd_inf_cavw='" . $cavw . "'");
        $result2 = mysql_fetch_array($query2);
        if ($result2 !== false) {
            $object['status'] = $result2[0] . " - " . $result2[1];
        }
        echo json_encode($object);
    }

    /*
     * get the list of available data for a particular volcano
     * this function is not getting the specific data such as deformation or gps
     * it just get the available list 
     */

    public function getTimeSeriesForVolcano($cavw) {

        function getStationsWithDataList($cavw) {

            $volcanoId = mysql_query("select vd_id from vd where vd_cavw = '$cavw'");
            $volcanoId = mysql_fetch_array($volcanoId);
            $volcanoId = $volcanoId[0];

            $temp = Array();
            $list = Array();

            $value = "";

            // get the seismic stations that locate near the current volcano
            $seismicStations = mysql_query("(select c.ss_code FROM sn a, ss c  where a.sn_pubdate <= now() and c.ss_pubdate <= now() and a.vd_id = '$volcanoId'  and a.sn_id = c.sn_id ) UNION (select c.ss_code FROM jj_volnet a, ss c , vd_inf d  WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id  and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and (sqrt(power(d.vd_inf_slat - c.ss_lat, 2) + power(d.vd_inf_slon - c.ss_lon, 2))*100)<20 and c.ss_pubdate <= now())") or die(mysql_error());

            while ($temp = mysql_fetch_array($seismicStations)) {
// get the station code
                $temp = $temp[0];
// sd_ivl
                $value = mysql_query("select b.ss_id from ss a, sd_ivl b where a.ss_code = '$temp' and a.ss_id = b.ss_id and a.ss_pubdate <= now() and b.sd_ivl_pubdate <= now()  limit 0 , 1");

                if ($value && mysql_num_rows($value)) {
                    array_push($list, "seismic");
                    break;
                }
// sd_rsm
                $value = mysql_query("select c.sd_rsm_id from ss a, sd_sam b, sd_rsm c where a.ss_code = '$temp' and a.ss_id = b.ss_id and b.sd_sam_id = c.sd_sam_id and a.ss_pubdate <= now() and b.sd_sam_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "seismic");
                    break;
                }
            }
            $deformationStations = mysql_query("(select  c.ds_code FROM cn a, ds c  where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.ds_pubdate <= now()  order by c.ds_code) UNION (select c.ds_code FROM jj_volnet a, ds c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ds_nlat, 2) + power(d.vd_inf_slon - c.ds_nlon, 2))*100)<20 and c.ds_pubdate <= now() ORDER BY c.ds_code)") or die(mysql_error());
            while ($temp = mysql_fetch_array($deformationStations)) {
// get the station code
                $temp = $temp[0];
// dd_tlt
                $value = mysql_query("select b.ds_id from ds a, dd_tlt b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_tlt_pubdate <= now()  limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
// dd_tlv
                $value = mysql_query("select b.ds_id from ds a, dd_tlv b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_tlv_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
// dd_str
                $value = mysql_query("select b.ds_id from ds a, dd_str b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_str_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
// dd_edm
                $value = mysql_query("select b.ds_id from ds a, dd_edm b where a.ds_code = '$temp' and (a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and a.ds_pubdate <= now() and b.dd_edm_pubdate <= now()  limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
// dd_ang
                $value = mysql_query("select b.ds_id from ds a, dd_ang b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_ang_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
// dd_gps
                $value = mysql_query("select b.ds_id from ds a, dd_gps b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gps_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
// dd_gpv
                $value = mysql_query("select b.ds_id from ds a, dd_gpv b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gpv_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
// dd_lev
                $query = "select b.ds_id_ref from ds a, dd_lev b where a.ds_code = '$temp' and (a.ds_id = b.ds_id1) and a.ds_pubdate <= now() and b.dd_lev_pubdate <= now() limit 0 , 1";

                $value = mysql_query($query);
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
// dd_sar: no station? , how to display the data
// use volcano id instead
                $value = mysql_query("select vd_id from dd_sar where vd_id = '$volcanoId' and dd_sar.dd_sar_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "deformation");
                    break;
                }
            }
            $fieldStations = mysql_query("(select  c.fs_code FROM cn a, fs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.fs_pubdate <= now() order by c.fs_code) UNION (select c.fs_code FROM jj_volnet a, fs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.fs_lat, 2) + power(d.vd_inf_slon - c.fs_lon, 2))*100)<20 and c.fs_pubdate <= now() ORDER BY c.fs_code)") or die(mysql_error());
//fd_ele
            while ($temp = mysql_fetch_array($fieldStations)) {
                $temp = $temp[0];
                $value = mysql_query("select fd_ele_id from fs, fd_ele where fs_code = '$temp' and (fs_id = fs_id1 or fs_id = fs_id2) and fs.fs_pubdate <= now() and fd_ele.fd_ele_pubdate <= now()  limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "field");
                    break;
                }
// fd_gra
                $value = mysql_query("select fd_gra_id from fs , fd_gra where fs.fs_code = '$temp' and fs.fs_id = fd_gra.fs_id and fs.fs_pubdate <= now() and fd_gra.fd_gra_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "field");
                    break;
                }
// fd_mag
                $value = mysql_query("select fd_mag_id from fs , fd_mag where fs.fs_code = '$temp' and fs.fs_id = fd_mag.fs_id and fs.fs_pubdate <= now() fd_mag.and fd_mag_pubdate <= now()  limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "field");
                    break;
                }
// fd_mgv
                $value = mysql_query("select fd_mgv_id from fs , fd_gra where fs.fs_code = '$temp' and fs.fs_id = fd_mgv.fs_id and fs.fs_pubdate <= now() and fd_gra.fd_gra_pubdate <= now()  limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "field");
                    break;
                }
            }
            $gasStations = mysql_query("(select  c.gs_code FROM cn a, gs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <=now() and c.gs_pubdate <= now()) UNION (select c.gs_code FROM jj_volnet a, gs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.gs_lat, 2) + power(d.vd_inf_slon - c.gs_lon, 2))*100)<20 and c.gs_pubdate <= now() ORDER BY c.gs_code)") or die(mysql_error());
            while ($temp = mysql_fetch_array($gasStations)) {
                $temp = $temp[0];
// gd
                $value = mysql_query("select gd_id from gs , gd where gs.gs_code = '$temp' and gs.gs_id = gd.gs_id and gs.gs_pubdate <= now() and gd.gd_pubdate <= now()  limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "gas");
                    break;
                }
// gd_plu
                $value = mysql_query("select gd_plu_id from gs , gd_plu where gs.gs_code = '$temp' and gs.gs_id = gd_plu.gs_id and gs.gs_pubdate <= now() and gd_plu.gd_plu_pubdate <= now()  limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "gas");
                    break;
                }
// gd_plu in the air
                $value = mysql_query("select gd_plu_time from gd_plu a, cs c where a.vd_id = '$volcanoId' and c.cs_id = a.cs_id and a.gd_plu_pubdate <= now() limit 0 ,1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "gas");
                    break;
                }
// gd_sol
                $value = mysql_query("select gd_sol_id from gs , gd_sol where gs.gs_code = '$temp' and gs.gs_id = gd_sol.gs_id and gs.gs_pubdate <= now() and gd_sol.gd_sol_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "gas");
                    break;
                }
            }
            $hydrologicStations = mysql_query("(select  c.hs_code FROM cn a, hs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.hs_pubdate <= now()) UNION (select c.hs_code FROM jj_volnet a, hs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.hs_lat, 2) + power(d.vd_inf_slon - c.hs_lon, 2))*100)<30 and c.hs_pubdate <= now() ORDER BY c.hs_code)") or die(mysql_error());
            while ($temp = mysql_fetch_array($hydrologicStations)) {
                $temp = $temp[0];
// hd
                $value = mysql_query("select hd_id from hs, hd where hs_code = '$temp' and hs.hs_id = hd.hs_id and hs.hs_pubdate <= now() and hd.hd_pubdate <= now()  limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "hydrologic");
                    break;
                }
            }
            $thermalStations = mysql_query("(select  c.ts_code FROM cn a, ts c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.ts_pubdate <= now()) UNION (select c.ts_code FROM jj_volnet a, ts c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ts_lat, 2) + power(d.vd_inf_slon - c.ts_lon, 2))*100)<20 and c.ts_pubdate <= now() ORDER BY c.ts_code)") or die(mysql_error());
            while ($temp = mysql_fetch_array($thermalStations)) {
                $temp = $temp[0];
// td
                $value = mysql_query("select td_id from ts,td where ts_code = '$temp' and ts.ts_id = td.ts_id and ts.ts_pubdate <= now() and td.td_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "thermal");
                    break;
                }
            }
            $meteoStations = mysql_query("(select  c.ms_code FROM cn a, ms c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.ms_pubdate <= now()) UNION (select c.ms_code FROM jj_volnet a, ms c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ms_lat, 2) + power(d.vd_inf_slon - c.ms_lon, 2))*100)<20 and c.ms_pubdate <= now() ORDER BY c.ms_code)") or die(mysql_error());
            while ($temp = mysql_fetch_array($meteoStations)) {
                $temp = $temp[0];
// td
                $value = mysql_query("select med_id from ms,med where ms_code = '$temp' and ms.ms_id = med.ms_id and ms.ms_pubdate <= now() and med.med_pubdate <= now() limit 0 , 1");
                if ($value && mysql_num_rows($value)) {
                    array_push($list, "meteo");
                    break;
                }
            }

            return $list;
        }

        $list = getStationsWithDataList($cavw);
        if (count($list) == 0)
            return;

        // get the list of stations based on the station type
        foreach ($list as $k => $type) {
            $this->getStations($cavw, $type);
        }
    }

    /* Get all stations near a volcano
      Regardless they contain time series data or not
      author: Tran Thien Nam
      2012-07-16
     */

    function getAllStationsList($cavw) {
        $vd_id = $this->getVolcanoId($cavw);
        $collection = array();
        $query_collection = array();
        $table_names = array("ss", "ds", "fs", "hs", "gs", "ts", "ms");
        $latlon_names = array(array("ss_lat", "ss_lon"), array("ds_nlat", "ds_nlon"), array("fs_lat", "fs_lon"), array("hs_lat", "hs_lon"), array("gs_lat", "gs_lon"), array("ts_lat", "ts_lon"), array("ms_lat", "ms_lon"));
        $network_names_col = array("sn", "cn", "cn", "cn", "cn", "cn", "cn");
        $type = array("Seismic", "Deformation", "Field", "Hydrologic", "Gas", "Thermal", "Meteo");
        for ($i = 0; $i < count($table_names); $i++) {
            $tb = $table_names[$i];
            $lat = $latlon_names[$i][0];
            $lon = $latlon_names[$i][1];
            $nn = $network_names_col[$i];
            $flag = strToUpper(substr($nn, 0, 1));
            $next_query = "(select c.{$tb}_name, c.{$lat}, c.{$lon} from {$nn} a, {$tb} c  where a.vd_id = '$vd_id' and a.{$nn}_id = c.{$nn}_id and {$tb}_pubdate <= now() and {$nn}_pubdate <= now() order by c.{$tb}_name) UNION (select c.{$tb}_name, c.{$lat}, c.{$lon} FROM jj_volnet a, {$tb} c,vd_inf d WHERE a.vd_id = '$vd_id' and a.vd_id=d.vd_id   and a.jj_net_flag = '{$flag}' and a.jj_net_id = c.{$nn}_id and (sqrt(power(d.vd_inf_slat - c.{$lat}, 2) + power(d.vd_inf_slon - c.{$lon}, 2))*100)<20 and {$tb}_pubdate <= now() ORDER BY c.{$tb}_name)";
            $query_exe = mysql_query($next_query) or die("Cannot connect to server");
            while ($query_result = mysql_fetch_array($query_exe)) {
                $next_station = new Station($query_result["{$tb}_name"], $query_result[$lat], $query_result[$lon], $type[$i]);
                array_push($collection, $next_station);
            }
        }
        foreach ($collection as $next_station) {
            echo $next_station->outputInfo();
        }
    }

    /*
     * Get available stations TYPE around a specific volcano, the default value will be
     * 20 km aray from the top of the volcano
     */

    public function getAvailableStations($cavw) {
        $volcanoId = mysql_query("select vd_id from vd where vd_cavw = '$cavw'");
        $volcanoId = mysql_fetch_array($volcanoId);
        $volcanoId = $volcanoId[0];
        $temp = Array();
        $value = "";
        $seismicStations = mysql_query("(select  c.ss_code FROM sn a, ss c  where a.vd_id = '$volcanoId'  and a.sn_id = c.sn_id and a.sn_pubdate <= now() and c.ss_pubdate <= now()) UNION (select c.ss_code FROM jj_volnet a, ss c , vd_inf d  WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id  and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and (sqrt(power(d.vd_inf_slat - c.ss_lat, 2) + power(d.vd_inf_slon - c.ss_lon, 2))*100)<20 and c.ss_pubdate <= now())") or die(mysql_error());
        while ($temp = mysql_fetch_array($seismicStations)) {
// get the station code
            $temp = $temp[0];
// sd_ivl
            $value = mysql_query("select b.ss_id from ss a, sd_ivl b where a.ss_code = '$temp' and a.ss_id = b.ss_id and a.ss_pubdate <= now() and c.sd_ivl_pubdate <= now()  limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "seismic;";
                break;
            }
// sd_rsm
            $value = mysql_query("select c.sd_rsm_id from ss a, sd_sam b, sd_rsm c where a.ss_code = '$temp' and a.ss_id = b.ss_id and b.sd_sam_id = c.sd_sam_id and a.ss_pubdate <= now() and b.sd_sam_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "seismic;";
                break;
            }
        }
        $deformationStations = mysql_query("(select  c.ds_code FROM cn a, ds c  where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.cn_pubdate <= now() order by c.ds_code) UNION (select c.ds_code FROM jj_volnet a, ds c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ds_nlat, 2) + power(d.vd_inf_slon - c.ds_nlon, 2))*100)<20 and c.ds_pubdate <= now() ORDER BY c.ds_code)") or die(mysql_error());
        while ($temp = mysql_fetch_array($deformationStations)) {
// get the station code
            $temp = $temp[0];
// dd_tlt
            $value = mysql_query("select b.ds_id from ds a, dd_tlt b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_tlt_pubdate <= now()  limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
// dd_tlv
            $value = mysql_query("select b.ds_id from ds a, dd_tlv b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_tlv_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
// dd_str
            $value = mysql_query("select b.ds_id from ds a, dd_str b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_str_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
// dd_edm
            $value = mysql_query("select b.ds_id from ds a, dd_edm b where a.ds_code = '$temp' and (a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2) and a.ds_pubdate <= now() and b.dd_edm_pubdate <= now()  limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
// dd_ang
            $value = mysql_query("select b.ds_id from ds a, dd_ang b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_ang_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
// dd_gps
            $value = mysql_query("select b.ds_id from ds a, dd_gps b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gps_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
// dd_gpv
            $value = mysql_query("select b.ds_id from ds a, dd_gpv b where a.ds_code = '$temp' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gpv_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
// dd_lev
            $value = mysql_query("select b.ds_id from ds a, dd_lev b where a.ds_code = '$temp' and (a.ds_id = b.ds_id1) and a.ds_pubdate <= now() and b.dd_lev_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
// dd_sar: no station? , how to display the data
// use volcano id instead
            $value = mysql_query("select vd_id from dd_sar where vd_id = '$volcanoId' and dd_sar_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "deformation;";
                break;
            }
        }
        $fieldStations = mysql_query("(select  c.fs_code FROM cn a, fs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.fs_pubdate <= now()  order by c.fs_code) UNION (select c.fs_code FROM jj_volnet a, fs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.fs_lat, 2) + power(d.vd_inf_slon - c.fs_lon, 2))*100)<20 and c.fs_pubdate <= now() ORDER BY c.fs_code)") or die(mysql_error());
//fd_ele
        while ($temp = mysql_fetch_array($fieldStations)) {
            $temp = $temp[0];
            $value = mysql_query("select fd_ele_id from fs, fd_ele where fs_code = '$temp' and (fs_id = fs_id1 or fs_id = fs_id2) and fs.fs_pubdate <= now() and fd_ele.fd_ele_pubdate <= now()  limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "field;";
                break;
            }
// fd_gra
            $value = mysql_query("select fd_gra_id from fs , fd_gra where fs.fs_code = '$temp' and fs.fs_id = fd_gra.fs_id and fs.fs_pubdate <= now() and fd_gra.fd_gra_pubdate <= now()   limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "field;";
                break;
            }
// fd_mag
            $value = mysql_query("select fd_mag_id from fs , fd_mag where fs.fs_code = '$temp' and fs.fs_id = fd_mag.fs_id and fs.fs_pubdate <= now() and fd_mag.fd_mag_pubdate <= now()   limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "field;";
                break;
            }
// fd_mgv
            $value = mysql_query("select fd_mgv_id from fs , fd_mgv where fs.fs_code = '$temp' and fs.fs_id = fd_mgv.fs_id and fs.fs_pubdate <= now() and fd_mgv.fd_mgv_pubdate <= now()   limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "field;";
                break;
            }
        }
        $gasStations = mysql_query("(select  c.gs_code FROM cn a, gs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.gs_pubdate <= now) UNION (select c.gs_code FROM jj_volnet a, gs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.gs_lat, 2) + power(d.vd_inf_slon - c.gs_lon, 2))*100)<20 c.gs_pubdate <= now() ORDER BY c.gs_code)") or die(mysql_error());
        while ($temp = mysql_fetch_array($gasStations)) {
            $temp = $temp[0];
// gd
            $value = mysql_query("select gd_id from gs , gd where gs.gs_code = '$temp' and gs.gs_id = gd.gs_id and gs.gs_pubdate <= now() and gd.gd_pubdate <= now()   limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "gas;";
                break;
            }
// gd_plu
            $value = mysql_query("select gd_plu_id from gs , gd_plu where gs.gs_code = '$temp' and gs.gs_id = gd_plu.gs_id and gs.gs_pubdate <= now() and gd_plu.gd_plu_pubdate <= now()  limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "gas;";
                break;
            }
// gd_plu in the air
            $value = mysql_query("select gd_plu_time from gd_plu a, cs c where a.vd_id = '$volcanoId' and c.cs_id = a.cs_id and a.gd_plu_pubdate <= now() limit 0 ,1");
            if ($value && mysql_num_rows($value)) {
                echo "gas;";
                break;
            }
// gd_sol
            $value = mysql_query("select gd_sol_id from gs , gd_sol where gs.gs_code = '$temp' and gs.gs_id = gd_sol.gs_id and gs.gs_pubdate <= now() and gd_sol.gd_sol_pubdate <= now()   limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "gas;";
                break;
            }
        }
        $hydrologicStations = mysql_query("(select  c.hs_code FROM cn a, hs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.hs_pubdate <= now()) UNION (select c.hs_code FROM jj_volnet a, hs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.hs_lat, 2) + power(d.vd_inf_slon - c.hs_lon, 2))*100)<30 and c.hs_pubdate <= now() ORDER BY c.hs_code)") or die(mysql_error());
        while ($temp = mysql_fetch_array($hydrologicStations)) {
            $temp = $temp[0];
// hd
            $value = mysql_query("select hd_id from hs, hd where hs_code = '$temp' and hs.hs_id = hd.hs_id and hs.hs_pubdate <= now() and hd.hd_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "hydrologic;";
                break;
            }
        }
        $thermalStations = mysql_query("(select  c.ts_code FROM cn a, ts c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id and a.cn_pubdate <= now() and c.ts_pubdate <= now()) UNION (select c.ts_code FROM jj_volnet a, ts c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ts_lat, 2) + power(d.vd_inf_slon - c.ts_lon, 2))*100)<20 and c.ts_pubdate <= now() ORDER BY c.ts_code)") or die(mysql_error());
        while ($temp = mysql_fetch_array($thermalStations)) {
            $temp = $temp[0];
// td
            $value = mysql_query("select td_id from ts,td where ts_code = '$temp' and ts.ts_id = td.ts_id and ts_pubdate <= now() and td_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "thermal;";
                break;
            }
        }
        $meteoStations = mysql_query("(select  c.ss_code FROM sn a, ss c  where a.vd_id = '$volcanoId'  and a.sn_id = c.sn_id and a.sn_pubdate <= now() and c.ss_pubdate <= now()) UNION (select c.ss_code FROM jj_volnet a, ss c , vd_inf d  WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id  and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and (sqrt(power(d.vd_inf_slat - c.ss_lat, 2) + power(d.vd_inf_slon - c.ss_lon, 2))*100)<20 and c.ss_pubdate <= now())") or die(mysql_error());
        while ($temp = mysql_fetch_array($meteoStations)) {
            // get the station code
            $temp = $temp[0];
            // med
            $value = mysql_query("select med_id from ms,med where ms_code = '$temp' and ms.ms_id = med.ms_id and ms_pubdate <= now() and med_pubdate <= now() limit 0 , 1");
            if ($value && mysql_num_rows($value)) {
                echo "meteo;";
                break;
            }
        }
    }

    /*
     * Get volcano id based on its cavw
     */

    private function getVolcanoId($cavw) {
        $volcanoId = mysql_query("select vd_id from vd where vd_cavw = '$cavw'");
        $volcanoId = mysql_fetch_array($volcanoId);
        return $volcanoId[0];
    }

    /*
     * Get latitude and longitude of a volcano based on its cavw
     */

    private function _getLatLonElev($cavw) {
        $result = mysql_query("select vd_inf_slat,vd_inf_slon, vd_inf_selev from vd_inf where vd_inf_cavw = '$cavw'");
        $i = mysql_fetch_array($result);
        return $i;
    }

    /*
     * Get available stations of a specfic type for a specific volcano
     */

    public function getStations($cavw, $type) {
        $volcanoId = $this->getVolcanoId($cavw);
        $stations = "";
        $value = "";
        $code = "";
        switch ($type) {
            case 'seismic':
                $stations = mysql_query("(select  c.ss_code,c.ss_lat,c.ss_lon FROM sn a, ss c  where a.vd_id = '$volcanoId'  and a.sn_id = c.sn_id) UNION (select c.ss_code,c.ss_lat,c.ss_lon FROM jj_volnet a, ss c , vd_inf d  WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id  and a.jj_net_flag = 'S' and a.jj_net_id = c.sn_id and (sqrt(power(d.vd_inf_slat - c.ss_lat, 2) + power(d.vd_inf_slon - c.ss_lon, 2))*100)<20)") or die(mysql_error());
                while ($temp = mysql_fetch_array($stations)) {
// get the station code
                    $code = $temp[0];
// sd_ivl
                    $value = mysql_query("select b.ss_id from ss a, sd_ivl b where a.ss_code = '$code' and a.ss_id = b.ss_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Seismic&Interval&$code&$temp[1]&$temp[2];";
                    }
// sd_rsm
                    $value = mysql_query("select c.sd_rsm_id from ss a, sd_sam b, sd_rsm c where a.ss_code = '$code' and a.ss_id = b.ss_id and b.sd_sam_id = c.sd_sam_id limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Seismic&RSAM&$code&$temp[1]&$temp[2];";
                        break;
                    }
                }
                break;
            case 'deformation':
                $stations = mysql_query("(select  c.ds_code,c.ds_nlat,c.ds_nlon FROM cn a, ds c  where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id  order by c.ds_code) UNION (select c.ds_code,c.ds_nlat,c.ds_nlon FROM jj_volnet a, ds c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ds_nlat, 2) + power(d.vd_inf_slon - c.ds_nlon, 2))*100)<20	ORDER BY c.ds_code)") or die(mysql_error());
// get the station code
                while ($temp = mysql_fetch_array($stations)) {
                    $code = $temp[0];
// dd_tlt
                    $value = mysql_query("select b.ds_id from ds a, dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&ElectronicTilt&$code&$temp[1]&$temp[2]&Tilt1;";
                        echo "Deformation&ElectronicTilt&$code&$temp[1]&$temp[2]&Tilt2;";
                    }
// dd_tlv
                    $value = mysql_query("select b.ds_id from ds a, dd_tlv b where a.ds_code = '$code' and a.ds_id = b.ds_id limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&TiltVector&$code&$temp[1]&$temp[2];";
                    }
// dd_str
                    $value = mysql_query("select b.ds_id from ds a, dd_str b where a.ds_code = '$code' and a.ds_id = b.ds_id limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&Strain&$code&$temp[1]&$temp[2]&Comp1;";
                        echo "Deformation&Strain&$code&$temp[1]&$temp[2]&Comp2;";
                        echo "Deformation&Strain&$code&$temp[1]&$temp[2]&Comp3;";
                        echo "Deformation&Strain&$code&$temp[1]&$temp[2]&Comp4;";
                        echo "Deformation&Strain&$code&$temp[1]&$temp[2]&Vdstr;";
                        echo "Deformation&Strain&$code&$temp[1]&$temp[2]&Ax1;";
                        echo "Deformation&Strain&$code&$temp[1]&$temp[2]&Ax2;";
                        echo "Deformation&Strain&$code&$temp[1]&$temp[2]&Ax3;";
                    }
// dd_edm
                    $value = mysql_query("select b.ds_id from ds a, dd_edm b where a.ds_code = '$code' and (a.ds_id = b.ds_id1 or a.ds_id = b.ds_id2)  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&EDM&$code&$temp[1]&$temp[2];";
                    }
// dd_ang
                    $value = mysql_query("select b.ds_id from ds a, dd_ang b where a.ds_code = '$code' and a.ds_id = b.ds_id limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&Angle&$code&$temp[1]&$temp[2]&Hort1;";
                        echo "Deformation&Angle&$code&$temp[1]&$temp[2]&Hort2;";
                        echo "Deformation&Angle&$code&$temp[1]&$temp[2]&Vert1;";
                        echo "Deformation&Angle&$code&$temp[1]&$temp[2]&Vert2;";
                    }
// dd_gps
                    $value = mysql_query("select b.ds_id from ds a, dd_gps b where a.ds_code = '$code' and a.ds_id = b.ds_id limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&GPS&$code&$temp[1]&$temp[2]&Lat;";
                        echo "Deformation&GPS&$code&$temp[1]&$temp[2]&Lon;";
                        echo "Deformation&GPS&$code&$temp[1]&$temp[2]&Elev;";
                    }
// dd_gpv
                    $value = mysql_query("select b.ds_id from ds a, dd_gpv b where a.ds_code = '$code' and a.ds_id = b.ds_id limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&GPSVector&$code&$temp[1]&$temp[2]&NS;";
                        echo "Deformation&GPSVector&$code&$temp[1]&$temp[2]&EW;";
                        echo "Deformation&GPSVector&$code&$temp[1]&$temp[2]&Z;";
                    }
// dd_lev
                    $query = "select b.ds_id_ref from ds a, dd_lev b where a.ds_code = '$code' and (a.ds_id = b.ds_id1) limit 0 , 1";
                    $value = mysql_query($query);
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&Leveling&$code&$temp[1]&$temp[2];";
                    }
// dd_sar: no station? , how to display the data
// use volcano id instead
                    $value = mysql_query("select vd_id from dd_sar where vd_id = '$volcanoId' limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Deformation&InSAR&$code&$temp[1]&$temp[2];";
                    }
                }
                break;
            case 'field':
                $stations = mysql_query("(select  c.fs_code,c.fs_lat,c.fs_lon FROM cn a, fs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id  order by c.fs_code) UNION (select c.fs_code,c.fs_lat,c.fs_lon FROM jj_volnet a, fs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.fs_lat, 2) + power(d.vd_inf_slon - c.fs_lon, 2))*100)<20 ORDER BY c.fs_code)") or die(mysql_error());

                while ($temp = mysql_fetch_array($stations)) {
                    $code = $temp[0];
// fd_ele
                    $value = mysql_query("select fd_ele_id from fs, fd_ele where fs_code = '$code' and (fs_id = fs_id1 or fs_id = fs_id2)  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Field&ElectricFields&$code&$temp[1]&$temp[2];";
                    }
// fd_gra
                    $value = mysql_query("select fd_gra_id from fs , fd_gra where fs.fs_code = '$code' and fs.fs_id = fd_gra.fs_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Field&Gravity&$code&$temp[1]&$temp[2];";
                    }
// fd_mag
                    $value = mysql_query("select fd_mag_id from fs , fd_mag where fs.fs_code = '$code' and fs.fs_id = fd_mag.fs_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Field&MagneticFields&$code&$temp[1]&$temp[2];";
                    }
// fd_mgv
                    $value = mysql_query("select fd_mgv_id from fs , fd_gra where fs.fs_code = '$code' and fs.fs_id = fd_mgv.fs_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Field&MagneticVector&$code&$temp[1]&$temp[2]&Dec;";
                        echo "Field&MagneticVector&$code&$temp[1]&$temp[2]&Incl;";
                    }
                }
                break;
            case 'gas':
                $stations = mysql_query("(select  c.gs_code,c.gs_lat,c.gs_lon FROM cn a, gs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id) UNION (select c.gs_code,c.gs_lat,c.gs_lon FROM jj_volnet a, gs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.gs_lat, 2) + power(d.vd_inf_slon - c.gs_lon, 2))*100)<20 ORDER BY c.gs_code)") or die(mysql_error());
                while ($temp = mysql_fetch_array($stations)) {
                    $code = $temp[0];
// gd
                    $value = mysql_query("select gd_id from gs , gd where gs.gs_code = '$code' and gs.gs_id = gd.gs_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Gas&SampledGas&$code&$temp[1]&$temp[2];";
                    }
// gd_plu
                    $value = mysql_query("select gd_plu_id from gs , gd_plu where gs.gs_code = '$code' and gs.gs_id = gd_plu.gs_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Gas&Plume&$code&$temp[1]&$temp[2];";
                    }

// gd_sol
                    $value = mysql_query("select gd_sol_id from gs , gd_sol where gs.gs_code = '$code' and gs.gs_id = gd_sol.gs_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Gas&SoilEfflux&$code&$temp[1]&$temp[2];";
                    }
                }
// gd_plu using satelite or airplane data
                $value = mysql_query("select distinct cs.cs_code from cs, gd_plu where cs.cs_id = gd_plu.cs_id and gd_plu.vd_id = '$volcanoId'");
                while ($temp = mysql_fetch_array($value)) {
                    echo "Gas&Plume&$temp[0];";
                }
                break;
            case 'hydrologic':
                $stations = mysql_query("(select  c.hs_code,c.hs_lat,c.hs_lon FROM cn a, hs c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id) UNION (select c.hs_code,c.hs_lat,c.hs_lon FROM jj_volnet a, hs c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.hs_lat, 2) + power(d.vd_inf_slon - c.hs_lon, 2))*100)<30 ORDER BY c.hs_code)") or die(mysql_error());
                while ($temp = mysql_fetch_array($stations)) {
                    $code = $temp[0];
// hd
                    $value = mysql_query("select hd_id from hs, hd where hs_code = '$code' and hs.hs_id = hd.hs_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Welev;";
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Temp;";
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Wdepth;";
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Bp;";
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Dwlev;";
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Sdisc;";
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Prec;";
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Cond;";
                        echo "Hydrologic&Data&$code&$temp[1]&$temp[2]&Comp;";
                    }
                }
                break;
            case 'thermal':
                $stations = mysql_query("(select  c.ts_code,c.ts_lat,c.ts_lon FROM cn a, ts c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id) UNION (select c.ts_code,c.ts_lat,c.ts_lon FROM jj_volnet a, ts c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ts_lat, 2) + power(d.vd_inf_slon - c.ts_lon, 2))*100)<20 ORDER BY c.ts_code)") or die(mysql_error());
                while ($temp = mysql_fetch_array($stations)) {
                    $code = $temp[0];
// td
                    $value = mysql_query("select td_id from ts,td where ts_code = '$code' and ts.ts_id = td.ts_id limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Thermal&Data&$code&$temp[1]&$temp[2];";
                    }
                }
                break;
            case 'meteo':
                $stations = mysql_query("(select  c.ms_code,c.ms_lat,c.ms_lon FROM cn a, ms c where a.vd_id = '$volcanoId' and a.cn_id = c.cn_id) UNION (select c.ms_code,c.ms_lat,c.ms_lon FROM jj_volnet a, ms c,vd_inf d WHERE a.vd_id = '$volcanoId' and a.vd_id=d.vd_id   and a.jj_net_flag = 'C' and a.jj_net_id = c.cn_id and (sqrt(power(d.vd_inf_slat - c.ms_lat, 2) + power(d.vd_inf_slon - c.ms_lon, 2))*100)<30 ORDER BY c.ms_code)") or die(mysql_error());
                while ($temp = mysql_fetch_array($stations)) {
                    $code = $temp[0];
// hd
                    $value = mysql_query("select med_id from ms, med where ms_code = '$code' and ms.ms_id = med.ms_id  limit 0 , 1");
                    if ($value && mysql_num_rows($value)) {
                        echo "Meteo&Data&$code&$temp[1]&$temp[2]&Temp;";
                        echo "Meteo&Data&$code&$temp[1]&$temp[2]&Stemp;";
                        echo "Meteo&Data&$code&$temp[1]&$temp[2]&Bp;";
                        echo "Meteo&Data&$code&$temp[1]&$temp[2]&Prec;";
                        echo "Meteo&Data&$code&$temp[1]&$temp[2]&Hd;";
                        echo "Meteo&Data&$code&$temp[1]&$temp[2]&Wind;";
                        echo "Meteo&Data&$code&$temp[1]&$temp[2]&Wdir;";
                    }
                }
                break;
        }
    }

    /* Get list of neighbor of a volcano
     * Author: Tran Thien Nam
     * 2012-07-26
     */

    public function getNeighbors($cavw) {
        mysql_query("set character_set_results='utf8'");
        //get lat,lon of the volcano
        $query0 = "select vd_inf_slat, vd_inf_slon from vd_inf where vd_inf_cavw='" . $cavw . "'";
        $result0 = mysql_query($query0) or die("Cannot connect to server");
        if ($result0 !== false) {
            $row0 = mysql_fetch_array($result0);
            $volcLat = $row0[0];
            $volcLon = $row0[1];
            $query = "select a.vd_cavw, a.vd_name, b.vd_inf_slat, b.vd_inf_slon from vd a, vd_inf b where SUBSTR(a.vd_name,1,7)!='Unnamed' and a.vd_cavw!='$cavw' and a.vd_id = b.vd_id order by sqrt(pow(($volcLat - b.vd_inf_slat)*110, 2) + pow(($volcLon - b.vd_inf_slon)*111.32*cos($volcLat/57.32), 2)) limit 15";
            $result = mysql_query($query) or die("Cannot connect to server");
            while (($row = mysql_fetch_array($result)) !== FALSE) {
                echo $row[0] . ";" . $row[1] . ";" . $row[2] . ";" . $row[3] . "&";
            }
        }
        else
            echo "";
    }

    /*
     * Get station data that has the publish date that is earlier than the current time.
     */

    public function getStationData($type, $table, $code, $component, $referenceTime) {
        $data = '';
        $result = '';
        $array = '';
        $cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
        $attribute = '';
        $databaseTable = '';
        $additionalData = "";
        switch ($type) {
            case 'seismic':
                switch ($table) {
                    case 'Interval':
                        $result = mysql_query("select b.sd_ivl_stime, b.sd_ivl_nrec $cc from ss a, sd_ivl b where a.ss_code = '$code' and a.ss_id = b.ss_id and a.ss_pubdate <= now() and b.sd_ivl_pubdate <= now() order by b.sd_ivl_stime desc");
                        break;
                    case 'RSAM':
                        $result = mysql_query("select c.sd_rsm_stime, c.sd_rsm_count $cc from ss a,sd_sam b,sd_rsm c where a.ss_code = '$code' and b.ss_id = a.ss_id and b.sd_sam_id = c.sd_sam_id and a.ss_pubdate <= now() and b.sd_sam_pubdate <= now() order by c.sd_rsm_stime desc");
                        break;
                }
                break;
            case 'deformation':
                switch ($table) {
                    case 'ElectronicTilt':
                        if ($referenceTime != 0) {
                            $referenceTime = $referenceTime / 1000;
                            if ($component == "Tilt1") {
                                mysql_query("set time_zome = 'UTC';");
//                            $result = mysql_query("select b.dd_tlt_time,b.dd_tlt1 from ds a, dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id order by b.dd_tlt_time desc");
                                $result = mysql_query("select b.dd_tlt_time as dd_tlt_time,b.dd_tlt1 as dd_tlt1$cc from
ds a, dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id and ($referenceTime - UNIX_TIMESTAMP(b.dd_tlt_time)) mod 43200 < 600 and a.ds_pubdate <= now() and b.dd_tlt_pubdate <= now() order by b.dd_tlt_time desc;");
                            } else if ($component == "Tilt2") {
                                mysql_query("set time_zome = 'UTC';");
//                            $result = mysql_query("select b.dd_tlt_time,b.dd_tlt2 from ds a, dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id order by b.dd_tlt_time desc");
                                $result = mysql_query("select b.dd_tlt_time as dd_tlt_time,b.dd_tlt2 as dd_tlt2$cc from
ds a, dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id and ($referenceTime - UNIX_TIMESTAMP(b.dd_tlt_time)) mod 43200 < 600 and a.ds_pubdate <= now() and b.dd_tlt_pubdate <= now() order by b.dd_tlt_time desc;");
                            }
                        } else {
                            if ($component == "Tilt1") {
//                            $result = mysql_query("select b.dd_tlt_time,b.dd_tlt1 from ds a, dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id order by b.dd_tlt_time desc");
                                $result = mysql_query("select b.dd_tlt_time as dd_tlt_time,b.dd_tlt1 as dd_tlt1$cc from
ds a, dd_tlt b,(select UNIX_TIMESTAMP(b.dd_tlt_time) as max from ds a , dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id limit 1) as c
where a.ds_code = '$code' and a.ds_id = b.ds_id and (c.max - UNIX_TIMESTAMP(b.dd_tlt_time)) mod 43200 < 600  and a.ds_pubdate <= now() and b.dd_tlt_pubdate <= now()  order by b.dd_tlt_time desc");
                            } else if ($component == "Tilt2") {
//                            $result = mysql_query("select b.dd_tlt_time,b.dd_tlt2 from ds a, dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id order by b.dd_tlt_time desc");
                                $result = mysql_query("select b.dd_tlt_time as dd_tlt_time,b.dd_tlt2 as dd_tlt2$cc from
ds a, dd_tlt b,(select UNIX_TIMESTAMP(b.dd_tlt_time) as max from ds a , dd_tlt b where a.ds_code = '$code' and a.ds_id = b.ds_id limit 1) as c
where a.ds_code = '$code' and a.ds_id = b.ds_id and (c.max - UNIX_TIMESTAMP(b.dd_tlt_time)) mod 43200 < 600  and a.ds_pubdate <= now() and b.dd_tlt_pubdate <= now()  order by b.dd_tlt_time desc");
                            }
                        }
                        break;
                    case 'TiltVector':
                        $result = mysql_query("select b.dd_tlv_stime, b.dd_tlv_mag$cc from ds a, dd_tlv b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_tlv_pubdate <= now() order by dd_tlv_stime desc");
                        break;
                    case 'Strain':
                        $com = strtolower($component);
                        $com = "dd_str_" . $com;
                        switch ($component) {
                            case 'Comp1':
                                $attribute = 'dd_str_comp1';
                                break;
                            case 'Comp2':
                                $attribute = 'dd_str_comp2';
                                break;
                            case 'Comp3':
                                $attribute = 'dd_str_comp3';
                                break;
                            case 'Comp4':
                                $attribute = 'dd_str_comp4';
                                break;
                            case 'Vdstr':
                                $attribute = 'dd_str_vdstr';
                                break;
                            case 'Ax1':
                                $attribute = 'dd_str_sstr_ax1';
                                break;
                            case 'Ax2':
                                $attribute = 'dd_str_sstr_ax2';
                                break;
                            case 'Ax3':
                                $attribute = 'dd_str_sstr_ax3';
                                break;
                        }
                        $result = mysql_query("select b.dd_str_time, b.$attribute $cc from ds a, dd_str b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_str_pubdate <= now() order by b.dd_str_time desc");

                        break;
                    case 'EDM':
                        $result = mysql_query("select b.dd_edm_time, b.dd_edm_line$cc from ds a, dd_edm b where a.ds_code = '$code' and b.ds_id = a.ds_id and a.ds_pubdate <= now() and b.dd_edm_pubdate <= now()  order by b.dd_edm_time desc");
                        break;
                    case 'Angle':
                        switch ($component) {
                            case 'Hort1':
                                $result = mysql_query("select b.dd_ang_time, b.dd_ang_hort1$cc from ds a, dd_ang b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_ang_pubdate <= now()  order by b.dd_ang_time desc");
                                break;
                            case 'Hort2':
                                $result = mysql_query("select b.dd_ang_time, b.dd_ang_hort2$cc from ds a, dd_ang b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_ang_pubdate <= now() order by b.dd_ang_time desc");
                                break;
                            case 'Vert1':
                                $result = mysql_query("select b.dd_ang_time, b.dd_ang_vert1$cc from ds a, dd_ang b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_ang_pubdate <= now() order by b.dd_ang_time desc");
                                break;
                            case 'Vert2':
                                $result = mysql_query("select b.dd_ang_time, b.dd_ang_vert2$cc from ds a, dd_ang b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_ang_pubdate <= now() order by b.dd_ang_time desc");
                                break;
                        }
                        break;
                    case 'GPS':
                        switch ($component) {
                            case 'Lat':
                                $result = mysql_query("select b.dd_gps_time, b.dd_gps_lat$cc , b.dd_gps_lon from ds a, dd_gps b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gps_pubdate <= now() group by b.dd_gps_time order by b.dd_gps_time desc");
                                $additionalData = mysql_query("select min(b.dd_gps_time), b.dd_gps_lat, b.dd_gps_lon from ds a, dd_gps b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gps_pubdate <= now()");
                                break;
                            case 'Lon':
                                $result = mysql_query("select b.dd_gps_time, b.dd_gps_lon$cc , b.dd_gps_lat from ds a, dd_gps b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gps_pubdate <= now() group by b.dd_gps_time order by b.dd_gps_time desc");
                                $additionalData = mysql_query("select min(b.dd_gps_time), b.dd_gps_lat, b.dd_gps_lon from ds a, dd_gps b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gps_pubdate <= now()");
                                break;
                            case 'Elev':
                                $result = mysql_query("select b.dd_gps_time, b.dd_gps_elev$cc from ds a, dd_gps b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gps_pubdate <= now() group by b.dd_gps_time order by b.dd_gps_time desc");
                                break;
                        }
                        break;
                    case 'GPSVector':
                        if ($component == "NS")
                            $result = mysql_query("select b.dd_gpv_stime, b.dd_gpv_N$cc from ds a, dd_gpv b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gpv_pubdate <= now() order by b.dd_gpv_stime desc");
                        else if ($component == "EW")
                            $result = mysql_query("select b.dd_gpv_stime, b.dd_gpv_E$cc from ds a, dd_gpv b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gpv_pubdate <= now() order by b.dd_gpv_stime desc");
                        else if ($component == "Z")
                            $result = mysql_query("select b.dd_gpv_stime, b.dd_gpv_vert$cc from ds a, dd_gpv b where a.ds_code = '$code' and a.ds_id = b.ds_id and a.ds_pubdate <= now() and b.dd_gpv_pubdate <= now() order by b.dd_gpv_stime desc");
                        break;
                    case 'Leveling':
                        $query = "select b.dd_lev_time, b.dd_lev_delev$cc from ds a, dd_lev b where a.ds_code = '$code' and a.ds_id = b.ds_id1 and a.ds_pubdate <= now() and b.dd_lev_pubdate <= now() order by b.dd_lev_time desc";
                        $result = mysql_query($query);
                        break;
                }
                break;
            case 'field':
                switch ($table) {
                    case 'ElectricFields':
                        $result = mysql_query("select b.fd_ele_time, b.fd_ele_field$cc from fs a, fd_ele b  where a.fs_code = '$code' and a.fs_code = b.fs_code and a.fs_pubdate <= now() and b.fd_ele_pubdate <= now() order by b.fd_ele_time desc");
                        break;
                    case 'Gravity':
                        $result = mysql_query("select b.fd_gra_time, b.fd_gra_fstr$cc from fs a, fd_gra b where a.fs_code = '$code' and a.fs_code = b.fs_code and a.fs_pubdate <= now() and b.fd_gra_pubdate <= now() order by b.fd_gra_time desc");
                        break;
                    case 'MagneticFields':
                        $result = mysql_query("select b.fd_mag_time, b.fd_mag_f$cc from fs a, fd_mag b where a.fs_code = '$code' and a.fs_code = b.fs_code and a.fs_pubdate <= now() and b.fd_mag_pubdate <= now() order by b.fd_mag_time desc");
                        break;
                    case 'MagneticVector':
                        if ($component == "Dec")
                            $result = mysql_query("select b.fd_mgv_time, b.fd_mgv_dec$cc from fs a, fd_mgv b where a.fs_code = '$code' and a.fs_code = b.fs_code and a.fs_pubdate <= now() and b.fd_mgv_pubdate <= now() order by b.fd_mgv_time desc");
                        else if ($component == "Incl")
                            $result = mysql_query("select b.fd_mgv_time, b.fd_mgv_incl$cc from fs a, fd_mgv b where a.fs_code = '$code' and a.fs_code = b.fs_code and a.fs_pubdate <= now() and b.fd_mgv_pubdate <= now() order by b.fd_mgv_time desc");

                        break;
                }
                break;
            case 'gas':
                switch ($table) {
                    case 'SampledGas':
                        $attribute = 'gd_concentration';
                        $databaseTable = 'gd';
                        $result = mysql_query("select b.gd_time, b.gd_concentration $cc from gs a, gd b where a.gs_code ='$code' and b.gs_id = a.gs_id and a.gs_pubdate <= now() and b.gd_pubdate <= now() order by b.gd_time desc");
                        break;
                    case 'Plume':
                        $attribute = 'gd_plu_emit';
                        $databaseTable = 'gd_plu';
                        $result = mysql_query("select distinct b.gd_plu_time, b.gd_plu_emit $cc  from gs a, gd_plu b, cs c where (a.gs_code ='$code' and b.gs_id = a.gs_id and a.gs_pubdate <= now()) or (b.cs_id = c.cs_id and c.cs_code = '$code') and b.gd_plu_pubdate <= now() order by b.gd_plu_time desc");

                        break;
                    case 'SoilEfflux':
                        $attribute = 'gd_sol_tfulx';
                        $databaseTable = 'gd_sol';
                        $result = mysql_query("select b.gd_sol_time, b.gd_sol_tfulx $cc from gs a, gd_sol b where a.gs_code ='$code' and b.gs_id = a.gs_id and a.gs_pubdate <= now() and b.gd_sol_pubdate <= now() order by b.gd_sol_time desc");

                        break;
                }

                break;
            case 'hydrologic':
                switch ($component) {
                    case 'Welev':
                        $attribute = 'hd_welev';
                        break;
                    case 'Temp':
                        $attribute = 'hd_temp';
                        break;
                    case 'Wdepth':
                        $attribute = 'hd_wdepth';
                        break;
                    case 'Bp':
                        $attribute = 'hd_bp';
                        break;
                    case 'Dwlev':
                        $attribute = 'hd_dwlev';
                        break;
                    case 'Sdisc':
                        $attribute = 'hd_sdisc';
                        break;
                    case 'Prec':
                        $attribute = 'hd_prec';
                        break;
                    case 'Cond':
                        $attribute = 'hd_cond';
                        break;
                    case 'Comp':
                        $attribute = 'hd_comp_content';
                        break;
                }
                $result = mysql_query("select b.hd_time, b.$attribute $cc from hs a, hd b where a.hs_code = '$code' and a.hs_id = b.hs_id and a.hs_pubdate <= now() and b.hd_pubdate <= now() order by b.hd_time desc");
                break;
            case 'thermal':
                $attribute = 'td_temp';
                $result = mysql_query("select b.td_time, b.$attribute $cc from ts a , td b where a.ts_code = '$code' and a.ts_id = b.ts_id and a.ts_pubdate <= now() and b.td_pubdate <= now()  order by b.td_time desc");
                break;
            case 'meteo':
                switch ($component) {
                    case 'Temp':
                        $attribute = 'med_temp';
                        break;
                    case 'Stemp':
                        $attribute = 'med_stemp';
                        break;
                    case 'Bp':
                        $attribute = 'med_bp';
                        break;
                    case 'Prec':
                        $attribute = 'med_prec';
                        break;
                    case 'Hd':
                        $attribute = 'med_hd';
                        break;
                    case 'Wind':
                        $attribute = 'med_wind';
                        break;
                    case 'Wdir':
                        $attribute = 'med_wdir';
                        break;
                }
                $result = mysql_query("select b.med_time, b.$attribute $cc from ms a, med b where a.ms_code = '$code' and a.ms_id = b.ms_id and a.ms_pubdate <= now() and b.med_pubdate <= now() order by b.med_time desc");

                break;
            default:
                throw new Exception('Not relevant data');
                break;
        }
        date_default_timezone_set("UTC");
        $numberOfRows = mysql_num_rows($result);
        if ($numberOfRows > 50000) {
            // 12 hours ranges
            $this->filterData($result, 3600 * 12);
        } else {
            $data = Array();
            $data[0] = Array();
            $count = 0;
            if ($result == false)
                return;
            if ($additionalData != "")
                $additionalData = mysql_fetch_array($additionalData);
            while ($array = mysql_fetch_array($result, MYSQL_NUM)) {
                $array[1] = $this->postProcessStationData($array, $type, $table, $component, $additionalData);
                $data[0][$count++] = Array(1000 * strtotime($array[0]), floatval($array[1]), 0, intval($array[2]), intval($array[3]), intval($array[4]));
            }
            echo json_encode($data);
        }
    }

    private function toRad($number) {
        return $number * 3.14159 / 180;
    }

    private function distanceD($lat, $lon, $vlat, $vlon, $option) {
        $R = 6371; //earth radius in kilometer
        // 
        if ($lat == "" || $lon == "" || $vlat == "" || $vlon == "") {
            return 0;
        }
        $dLat = "";
        $dLon = "";
        $diff = "";
        $tlat1 = "";
        $tlat2 = "";
        switch ($option) {
            case 0:
                $dLat = 0;
                $dLon = $this->toRad($lon - $vlon);
                $tlat1 = $this->toRad($vlat);
                $tlat2 = $this->toRad($vlat);
                $diff = $lon - $vlon;
                break;
            case 1:
                $dLon = 0;
                $dLat = $this->toRad($lat - $vlat);
                $diff = $lat - $vlat;
                $tlat1 = $this->toRad($vlat);
                $tlat2 = $this->toRad($lat);
                break;
        }
        $a = sin($dLat / 2) * sin($dLat / 2) + sin($dLon / 2) * sin($dLon / 2) * cos($tlat1) * cos($tlat2);

        $c = 2 * atan2($a, sqrt(1 - $a));
        $d = $R * $c;
        if (($diff < 0 && $diff > -180) || $diff > 90)
            $d = -$d;
        return $d;
    }

    private function postProcessStationData($data, $type, $table, $component, $additionalData) {
        switch ($type) {
            case "deformation":
                switch ($table) {
                    case "GPS":
                        switch ($component) {
                            case "Lat":
                                $array = $additionalData;
                                $vlat = floatval($array[1]);
                                $vlon = floatval($array[2]);
                                $lat = $data[1];
                                $lon = $data[5];
                                $data[1] = $this->distanceD($lat, $lon, $vlat, $vlon, 1) * 10000;
                                break;
                            case "Lon":
                                $array = $additionalData;
                                $vlat = floatval($array[1]);
                                $vlon = floatval($array[2]);
                                $lat = $data[5];
                                $lon = $data[1];
                                $data[1] = $this->distanceD($lat, $lon, $vlat, $vlon, 0) * 10000;
                                break;
                        }
                }
        }

        return $data[1];
    }

    private function filterData($resource, $seconds) {
        if ($resource == false)
            return;
        $milliseconds = $seconds * 1000;
        $nextValue = '';
        $current;
        $data = Array();
        $data[0] = Array();
        $array = mysql_fetch_array($resource);
        $data[0][0] = array(1000 * strtotime($array[0]), floatval($array[1]), 0, intval($array[2]), intval($array[3]), intval($array[4]));
        $nextValue = $data[0][0][0] - $milliseconds;
        $count = 1;
        while ($array = mysql_fetch_array($resource)) {
            $current = 1000 * strtotime($array[0]);
            if ($current <= $nextValue) {
                $nextValue = $current - $milliseconds;
                $data[0][$count++] = array($current, floatval($array[1]), 0, intval($array[2]), intval($array[3]), intval($array[4]));
            }
        }

        echo json_encode($data);
    }

    public function getFullStationData($type, $table, $code, $component) {
        $data = '';
        $array = '';

        $cc = ', b.cc_id, b.cc_id2, b.cc_id3 ';
        $attribute = '';
        $databaseTable = '';
        $mainTable = '';
        switch ($type) {
            case 'deformation':
                $mainTable = 'ds';
                switch ($table) {
                    case 'ElectronicTilt':
                        $databaseTable = 'dd_tlt';
                        switch ($component) {
                            case 'Tilt1':
                                $attribute = 'dd_tlt1';
                                break;
                            case 'Tilt2':
                                $attribute = 'dd_tlt2';
                                break;
                        }
                        break;
                    case 'Strain':
                        $databaseTable = 'dd_str';
                        switch ($component) {
                            case 'Comp1':
                                $attribute = 'dd_str_comp1';
                                break;
                            case 'Comp2':
                                $attribute = 'dd_str_comp2';
                                break;
                            case 'Comp3':
                                $attribute = 'dd_str_comp3';
                                break;
                            case 'Comp4':
                                $attribute = 'dd_str_comp4';
                                break;
                            case 'Vdstr':
                                $attribute = 'dd_str_vdstr';
                                break;
                            case 'Ax1':
                                $attribute = 'dd_str_sstr_ax1';
                                break;
                            case 'Ax2':
                                $attribute = 'dd_str_sstr_ax2';
                                break;
                            case 'Ax3':
                                $attribute = 'dd_str_sstr_ax3';
                                break;
                        }

                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
        $result = mysql_query("select b." . $databaseTable . "_time,b.$attribute $cc from $mainTable a, $databaseTable b where a." . $mainTable . "_code = '$code' and a." . $mainTable . "_id = b." . $mainTable . "_id and a." . $mainTable . "_pubdate <= now() and b." . $databaseTable . "_pubdate <= now() order by b." . $databaseTable . "_time desc");

        $data = Array();
        $data[0] = Array();
        $count = 0;

        // return nothing when it is not the specific type of data that needs to
        // do specficial data handling before giving it back to the client
        if ($result == '')
            return;
        // Be care in the future with these line of code
        // as the data in our database getting bigger and bigger, the size of the
        // data can be larger than the memory_limit and cause unexpected termination
        // of this script. It will lead to a error because the line to output json
        // data cannot be executed.
        ini_set('memory_limit', '1024M');
        while (true) {
            $array = mysql_fetch_array($result, MYSQL_NUM);
            if ($array == false)
                break;
            $data[0][$count++] = Array(1000 * strtotime($array[0]), floatval($array[1]), 0, intval($array[2]), intval($array[3]), intval($array[4]));
        }
        echo json_encode($data);
        mysql_free_result($result);
        ini_set('memory_limit', '100M');
    }

    private function getEarthquakeQuery($quantity, $latitude, $longitude, $startDate, $endDate, $startDepth, $endDepth, $eqtype, $wkm) {
        /* Old sql
          $sql_statement = "select sd_evn_code, sd_evn_elat, sd_evn_elon, sd_evn_edep, sd_evn_pmag, sd_evn_time,
          sd_evn_eqtype FROM sd_evn WHERE ABS($lat - sd_evn_elat) < 1
          and ABS($lon - sd_evn_elon) < 6 and sqrt(pow(($lat - sd_evn_elat)*110, 2)
          + pow(($lon - sd_evn_elon)*111.32*cos($lat/57.32), 2))< 30
          and sd_evn_edep < 40 and sd_evn_edep > -3 and sd_evn_pubdate <= now() $dates $depth $quaketype
          group by sd_evn_elat, sd_evn_elon order by sd_evn_time desc $limit";
          New sql
          $sql_statement = "SELECT sd_evn_code, sd_evn_elat, sd_evn_elon, sd_evn_edep, sd_evn_pmag, sd_evn_time, sd_evn_eqtype, sn_id FROM sd_evn JOIN (SELECT ROUND(RAND() * (SELECT MAX(sd_evn_id) FROM sd_evn)) AS id) AS r2 WHERE ABS(34.0789985656738 - sd_evn_elat) < 1 AND ABS(139.529006958008 - sd_evn_elon) < 6 AND SQRT(POW((34.0789985656738 - sd_evn_elat)*110, 2) + POW((139.529006958008 - sd_evn_elon) * 111.32 * COS(34.0789985656738/57.32), 2))< 10 AND sd_evn_pubdate <= now() and sd_evn_time BETWEEN '1900/01/01' AND '2013/4/2' and sd_evn_edep BETWEEN 0 AND 40 order by r2.id limit 500";
         */


        $quakeQuery = "SELECT ";
        if ($quantity)
            $quakeQuery .= "sd_evn_code, ";

        $quakeQuery .= " sd_evn_elat, sd_evn_elon, sd_evn_edep, sd_evn_pmag, sd_evn_time, sd_evn_eqtype, sn_id FROM sd_evn JOIN (SELECT ROUND(RAND() * (SELECT MAX(sd_evn_id) FROM sd_evn where  sd_evn_time <= now())) AS id) AS r2";


        $quakeQuery .= " WHERE ABS($latitude - sd_evn_elat) < 1 AND ABS($longitude - sd_evn_elon) < 6 ";

        if ($wkm == "")
            $wkm = 60;

        $quakeQuery .= " AND SQRT(POW(($latitude - sd_evn_elat)*110, 2) + POW(($longitude - sd_evn_elon) * 111.32 * COS($latitude/57.32), 2))<  " . $wkm / 2;


        $quakeQuery .= " AND sd_evn_pubdate <= now() ";


        if ($startDate && $endDate) {
            $startDate = preg_split('/\//', $startDate);
            $endDate = preg_split('/\//', $endDate);
            $dates = " and sd_evn_time BETWEEN '$startDate[2]/$startDate[0]/$startDate[1]' AND '$endDate[2]/$endDate[0]/$endDate[1]' ";
            $quakeQuery .= $dates;
        }

        if (is_numeric($startDepth) && is_numeric($endDepth)) {
            $depth = " and sd_evn_edep BETWEEN $startDepth AND $endDepth ";
            $quakeQuery .= $depth;
        } else {
            $quakeQuery .= " AND sd_evn_edep < 40 and sd_evn_edep > -3 ";
        }

        if ($eqtype) {
            $quaketype = " and sd_evn_eqtype = $eqtype ";
            $quakeQuery .= $quaketype;
        }

        //$quakeQuery .= " group by sd_evn_elat, sd_evn_elon order by sd_evn_time desc ";
        $quakeQuery .= " order by r2.id ";


        if ($quantity) {
            $limit = " limit $quantity ";
            $quakeQuery .= $limit;
        }

        session_start();
        $_SESSION['quakeQuery'] = $quakeQuery;
        return $quakeQuery;
    }

    public function getEarthquakes($qty, $cavw, $lat, $lon, $elev) {

        $quakeQuery = $this->getEarthquakeQuery("", $lat, $lon, "", "", "", "", "", "");
        $getQuakes = mysql_query($quakeQuery) or die(mysql_error());
        $count = 0;
        while ($row = mysql_fetch_array($getQuakes)) {
            echo $row['sd_evn_elat'] . "," . $row['sd_evn_elon'] . "," . $row['sd_evn_edep'] . "," . $row['sd_evn_pmag'] . "," . $row['sd_evn_time'] . "," . $row['sd_evn_eqtype'] . "," . $row['sn_id'] . ";";
            $count++;
        }
    }

    public function get2DGMT($o) {
        $equakeFlag = "";
        // clear output folder for old generated gmt files
        $this->clearOutputFolder();

        $result = array();
        $htmroot = dirname(__FILE__) . "/..";

        // This path is important for GMT to work, please change this path into where you put it in the main server
        putenv("PATH=/bin:/usr/bin:/usr/lib/gmt/bin:/usr/lib/gmt/share:/usr/lib/gmt/lib:/usr/lib/gmt/include");
        putenv("GMTHOME=/usr/lib/gmt");

        # defines the public_html root directory (absolute path on the Apache server)
        # subdiretory name
        $outdir = 'output';
        # basename for output files    
        $tmp = 'eq';
        # created a temporary and unique directory
        $name = uniqid();
        $wovodir = "wovodat2D";
        $tmpdir = "$htmroot/$outdir/$wovodir.$name";

        $htmout = "/$outdir/$wovodir.$name";
        $result['directory'] = $htmout;
        mkdir($tmpdir);

        # timestamp text      
        $stamp = "by WOVOdat/EOS";

        # number of earthquake events
        $wkm = $o['wkm'];             // Map width.
        $vname = $o['vname'];
        $vlat = $o['vlat'];
        $vlon = $o['vlon'];
        $cavw = $o['cavw'];
        $result['cavw'] = $cavw;

        $qty = $o['qty'];              // Number on events
        $date_start = $o['date_start'];
        $date_end = $o['date_end'];
        $dr_start = $o['dr_start'];
        $dr_end = $o['dr_end'];
        $eqtype = $o['eqtype'];

        $sql_statement = $this->getEarthquakeQuery($qty, $vlat, $vlon, $date_start, $date_end, $dr_start, $dr_end, $eqtype, $wkm);
        $query = mysql_query($sql_statement);

        # writes the data into a single file
        $nb = 0;

        $fh = fopen("$tmpdir/$tmp.txt", 'w') or die("can't open file for writing txt file <br/>");
        while ($row = mysql_fetch_assoc($query)) {

            $data[] = $row['sd_evn_time'];

            fwrite($fh, join(',', $row) . "\n");
            $nb++;
        }


        $equakeFirstValue = substr($data[0], 0, 4);

        for ($i = 0; $i < sizeof($data); $i++) {

            $equakeYear = substr($data[$i], 0, 4);   //Get only year from this format 2010-11-02 23:50:17

            if ($equakeFirstValue != $equakeYear) {
                $equakeFlag = "notEqual";         // not equal means more than one year..
                break;
            }
        }

        $result['numberOfEvents'] = $nb;
        $result['dataFile'] = "$htmout/$tmp.txt";
        $result['gmtScriptFile'] = "$htmout/$tmp.gmt";
        fclose($fh);


        $J = 74 * 20 / $wkm; # Jm scale (normalized with map width)
        $ldep = 20; # max depth for profiles (km)

        $title = $vname . "($nb events)";
        $kmlat = 6370 * deg2rad(1); # length of a latitude degree (in km)
        $kmlon = $kmlat * cos(deg2rad($vlat)); # length of a longitude degree at the volcano latitude (in km)
        $lon1 = ($vlon - 0.5 * $wkm / $kmlon);
        $lon2 = ($vlon + 0.5 * $wkm / $kmlon);
        $lat1 = ($vlat - 0.5 * $wkm / $kmlat);
        $lat2 = ($vlat + 0.5 * $wkm / $kmlat);
        $Rll = "-R$lon1/$lon2/$lat1/$lat2";
        $slat = ($vlat - 0.44 * $wkm / $kmlat); # latitude position of km scale
        $Jlat = $J * $kmlon / $kmlat;
        $Jlon = $J;
        $box = "'0 0\n1 0\n1 -1\n0 -1\n0 0\n'";


        # default to the 2D ".ps" and ".png" files.
        # They will be updated when visual type is in 3D.
        $imageFile = $tmp . ".ps";
        $imageSrc = $tmp . ".png";

        $fh = fopen("$tmpdir/$tmp.gmt", 'w') or die("can't open file for writing gmt file <br/>");
        // GMT set parameters
        fwrite($fh, "gmtset PAPER_MEDIA=A4 FRAME_WIDTH=0.15c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n");
        fwrite($fh, "gmtset INPUT_CLOCK_FORMAT=hh:mm:ss INPUT_DATE_FORMAT=yyyy-mm-dd TIME_FORMAT_PRIMARY abbreviated PLOT_DATE_FORMAT o\n");
        fwrite($fh, "gmtset OUTPUT_DATE_FORMAT=yyyy-mm-dd\n");
        fwrite($fh, "gmtset CHAR_ENCODING ISOLatin1+\n");


        // makes colormap
        fwrite($fh, "makecpt -Cno_green -I -T0/$ldep/1 > $tmp.cpt\n");

        // plan view
        //Nang.. To adjust $wkm number showing on the top on graph =>  changed from  "-Ba9mf5mg5m" to "-Ba5mf5mg5m"
        fwrite($fh, "psbasemap -Jm$Jlat $Rll -Ba9mf5mg5m:.\"$title\":WesN -X2.3c -Y14c -P -K > $tmp.ps\n");


        fwrite($fh, "pscoast -J -R -Df -W1p -S150/170/255 -N1/1.5p,black -N2/1p,50/50/50 -Tf178/-35/1i/2 -O -K >> $tmp.ps\n");
        fwrite($fh, "pscoast -J -R -Df -C0/169/223 -Lf$vlon/$slat/$vlat/10k+u -O -K >> $tmp.ps\n");
        fwrite($fh, "awk -F , '{print \$3,\$2,\$4}' $tmp.txt | psxy -J -R -Sc0.075i -C$tmp.cpt -G255 -W0.25p -O -K >> $tmp.ps\n");

        // N-S projection
        fwrite($fh, "printf $box | psxy -R-5/$ldep/$lat1/$lat2 -Jx0.17c/$Jlon -Ba5f5g0/a5f5g0::wesN -W1 -P -O -X14c -Y0 -K >> $tmp.ps\n");
        fwrite($fh, "awk -F , '{if (\$3>=$lon1 && \$3<=$lon2) {print \$4,\$2,\$4}}' $tmp.txt | psxy -R -J  -Sc0.075i -C$tmp.cpt -W0.25p -O -K >> $tmp.ps\n");
        // W-E projection
        fwrite($fh, "printf $box | psxy -R$lon1/$lon2/-$ldep/5 -Jx$Jlat/0.17c -Ba5f5g0/a5f5g0 -W1 -P -O -X-14c -Y-5c -K >> $tmp.ps\n");

        fwrite($fh, "awk -F , '{if (\$2>=$lat1 && \$2<=$lat2) {print \$3,-\$4,\$4}}' $tmp.txt | psxy -R -J  -Sc0.075i -C$tmp.cpt -W0.25p -O -K >> $tmp.ps\n");
        // depth scale
        fwrite($fh, "psscale -D16c/2c/-4c/0.3c -C$tmp.cpt -B10f10/:\"Depth (km)\": -O -K >> $tmp.ps\n");
        // depth vs time
        fwrite($fh, "cat $tmp.txt | sed s/\\ /T/g | awk -F , {'print \$6,-\$4,\$4'} > $tmp.xyz\n");
        fwrite($fh, "R=`minmax -fT -I5 $tmp.xyz`\n");
        fwrite($fh, "echo 'testing'\n");

        //Nang..Used below two lines to adjust not to show massive year at the end of graph.
        fwrite($fh, "gmtset PLOT_DATE_FORMAT o TIME_FORMAT_PRIMARY Character ANNOT_FONT_SIZE_PRIMARY +9p\n");

        if ($equakeFlag == "notEqual") {    //more than a year
            fwrite($fh, "psbasemap \$R -JX17c/4c -Bs2y/WESn  -Bp/a10f10g0 -P -Y-5c -U\"$stamp\" -O -K >> $tmp.ps\n");
        } else {  // within one year
            fwrite($fh, "psbasemap \$R -JX17c/4c  -Bpa1d/a10f10g0 -Bsa1O/WESn -P  -Y-5c -U\"$stamp\" -O -K >> $tmp.ps\n");
        }

        fwrite($fh, "psxy $tmp.xyz -R -J -Sc0.075i -C$tmp.cpt  -W0.25p -V -O >> $tmp.ps\n");
        // makes PNG from PS file
        fwrite($fh, "convert $tmp.ps $tmp.png\n");
        fclose($fh);

        // execute the script;
        exec("cd $tmpdir ;  bash $tmp.gmt");

        $result['title'] = $title;
        $result['imageFile'] = $imageFile;
        $result['imageSrc'] = $imageSrc;
        return $result;
    }

    /*
     * Process the request to generate the 3D model for a specific volcano
     * Input: $o is an object which has the following attributes
     * - init_azim: Initial Azimuth (in degree)
     * - degree: Rotation Degree (for 3D)
     * - map_width: Map width (km)
     * - qty: Number of earthquake events for quering the data
     * - date_start: the starting date of the earthquake events
     * - date_end: the ending date of the earthquake events
     * - dr_start: the dr start date of the earthquake events
     * - dr_end: the dr end date of the earthquake events
     * - eqtype: the earthquake type of the earthquake events
     */

    public function get3D($o) {
        // clear gmt folder for old generated file
        $this->clearOutputFolder();

        $result = array();
        $htmroot = dirname(__FILE__) . "/..";

        // This path is important for GMT to work, please change this path into where you put it in the main server
        putenv("PATH=/bin:/usr/bin:/usr/lib/gmt/bin:/usr/lib/gmt/share:/usr/lib/gmt/lib:/usr/lib/gmt/include");
        putenv("GMTHOME=/usr/lib/gmt");

        # defines the public_html root directory (absolute path on the Apache server)
        # subdiretory name
        $outdir = 'output';

        # basename for output files    
        $tmp = 'eq';

        # created a temporary and unique directory
        $name = uniqid();
        $wovodir = "wovodat3D";
        $tmpdir = "$htmroot/$outdir/$wovodir.$name";
        $htmout = "/$outdir/$wovodir.$name";
        $result['directory'] = $htmout;
        mkdir($tmpdir);

        # timestamp text      
        $stamp = "by WOVOdat/EOS";

        # get parameters
        $wkm = $o['wkm'];                // Map width 
        $vname = $o['vname'];
        $vlat = $o['vlat'];
        $vlon = $o['vlon'];
        $cavw = $o['cavw'];
        $initial_value = $o['init_azim'];        // azimuth
        $updatedAzim = $o['degree'];             // rotation degree	

        $qty = $o['qty'];                        // Number on events
        $date_start = $o['date_start'];
        $date_end = $o['date_end'];
        $dr_start = $o['dr_start'];
        $dr_end = $o['dr_end'];
        $eqtype = $o['eqtype'];

        # SQL query: get the data (approximate selection from map width)
        /*  Old sql statement
          $sql_statement = "(select b.sn_code, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, c.sd_evn_pmag,
          c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon FROM sn b, sd_evn c, vd_inf d WHERE
          b.sn_id = c.sn_id AND b.vd_id=d.vd_id AND d.vd_id = $vd_id $dates $depth $quaketype ORDER BY
          sd_evn_time DESC $limit) UNION (select b.sn_code, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep,
          c.sd_evn_pmag, c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon FROM jj_volnet a,
          sn b, sd_evn c, vd_inf d WHERE a.vd_id = $vd_id AND a.jj_net_id = b.sn_id AND b.sn_id = c.sn_id
          AND d.vd_id = $vd_id AND a.jj_net_flag = 'S' $dates $depth $quaketype AND
          (sqrt(power(d.vd_inf_slat - c.sd_evn_elat, 2) + power(d.vd_inf_slon - c.sd_evn_elon, 2))*111)<=1.5*$wkm
          ORDER BY c.sd_evn_time DESC $limit)";
         */
        $sql_statement = $this->getEarthquakeQuery($qty, $vlat, $vlon, $date_start, $date_end, $dr_start, $dr_end, $eqtype, $wkm);

        $query = mysql_query($sql_statement);

        # writes the data into a single file
        $nb = 0;


        $fh = fopen("$tmpdir/$tmp.txt", 'w') or die("can't open file for writing txt file <br/>");
        while ($row = mysql_fetch_assoc($query)) {
            fwrite($fh, join(',', $row) . "\n");
            $nb++;
        }
        $result['numberOfEvents'] = $nb;
        $result['dataFile'] = "$htmout/$tmp.txt";
        $result['gmtScriptFile'] = "$htmout/$tmp.gmt";
        $result['animationImage'] = "$htmout/$tmp.gif";
        fclose($fh);

        $J = 74 * 20 / $wkm; # Jm scale (normalized with map width)
        $ldep = 20; # max depth for profiles (km)

        $title = $vname . "($nb events)";
        $kmlat = 6370 * deg2rad(1); # length of a latitude degree (in km)
        $kmlon = $kmlat * cos(deg2rad($vlat)); # length of a longitude degree at the volcano latitude (in km)
        $lon1 = ($vlon - 0.5 * $wkm / $kmlon);
        $lon2 = ($vlon + 0.5 * $wkm / $kmlon);
        $lat1 = ($vlat - 0.5 * $wkm / $kmlat);
        $lat2 = ($vlat + 0.5 * $wkm / $kmlat);
        $Rll = "-R$lon1/$lon2/$lat1/$lat2";
        $slat = ($vlat - 0.44 * $wkm / $kmlat); # latitude position of km scale
        $Jlat = $J * $kmlon / $kmlat;
        $Jlon = $J;
        $box = "'0 0\n1 0\n1 -1\n0 -1\n0 0\n'";

        # default to the 2D ".ps" and ".png" files.
        # They will be updated when visual type is in 3D.
        $imageFile = $tmp . ".ps";
        $imageSrc = $tmp . ".png";

        $minDep = 0; // min depth (km)
        $maxDep = 50; // max depth (km)
        $zMin = (-1 * $maxDep);
        $zMax = (-1 * $minDep);
        $dx = ($lon2 - ($lon1));
        $dy = ($lat2 - ($lat1));
        $dz = ($maxDep - $minDep);
        $anno_x = $dx / 5; // Set 4 tickmarks for longitude 
        $anno_y = $dy / 5;  // Set 6 tickmarks for latitude
        $Rll_3D = "-R$lon1/$lon2/$lat1/$lat2/$zMin/$zMax";


        // The numbers, 0.06, 0.12, 0.18,..., are the drawing size
        // of the symbol. They are obtained by magnitude * 0.04.
        // e.g: magnitude is 2, symbol_size = 2*0.06 = 0.08
        $legend = array(
            "0" => "H 10 1  Magnitude",
            "1" => "D 0 1p",
            "2" => "N 5",
            "3" => "V 0 1p",
            "4" => "S 0.6c c 0.16c - 0.3p 1c M 1",
            "5" => "S 0.6c c 0.32c - 0.3p 1c M 2",
            "6" => "S 0.6c c 0.48c - 0.3p 1c M 3",
            "7" => "S 0.6c c 0.64c - 0.3p 1c M 4",
            "8" => "S 0.6c c 0.8c - 0.35p 1c M > 4",
            "11" => "V 0 1p ",
        );

        $newLegend = implode("\n", $legend);
        $fh = fopen("$tmpdir/gmt.legend", "w");
        fwrite($fh, $newLegend);
        fclose($fh);

        $gmtLegend = "$tmpdir/gmt.legend";
        $dpi = 100;
        $elev = 25;
        $fh = fopen("$tmpdir/$tmp.gmt", 'w') or die("can't open file for writing gmt file <br/>");

        // GMT set parameters
        fwrite($fh, ". gmt_shell_functions.sh\n");
        //fwrite($fh,"gmtset PAPER_MEDIA=A4 FRAME_WIDTH=0.2c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n");
        fwrite($fh, "gmtset PAPER_MEDIA=Custom_570x570 FRAME_WIDTH=0.2c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n");
        fwrite($fh, "gmtset INPUT_CLOCK_FORMAT=hh:mm:ss INPUT_DATE_FORMAT=yyyy-mm-dd TIME_FORMAT_PRIMARY abbreviated PLOT_DATE_FORMAT o\n");
        fwrite($fh, "gmtset OUTPUT_DATE_FORMAT=yyyy-mm-dd\n");
        fwrite($fh, "gmtset PAGE_COLOR=243/255/237 \n");
        fwrite($fh, "gmtset CHAR_ENCODING ISOLatin1+\n");

        // makes colormap
        fwrite($fh, "makecpt -Crainbow -T$zMin/$zMax/5 -Z > $tmp.cpt\n");

        // generate frames
        fwrite($fh, "frame=0\n");
        fwrite($fh, "name=\"frame\"\n");

        // $initial_value is given by the user. 
        fwrite($fh, "azim=$initial_value\n");

        fwrite($fh, "exitFlag=0\n");

        fwrite($fh, "while ((\$azim <= 360)); do \n");

        fwrite($fh, "file=`gmt_set_framename \$name \$frame ` \n");


        // $3 is sd_evn_elon, $2 is sd_evn_elat, 
        // $4 is sd_evn_edep, $5 is sd_evn_pmag.  
        //
		// Note: 1. The symbol size for magnitude 1 is 0.06i. So, 
        //          "0.06*$5" is the symbol size for magnitude specified
        //          by $5. . The symbol size is used to show the level of 
        //          magnitude. Also see the function createLegend().
        //       2. if sd_evn_pmag <= 0, assign the symbol size to 0.04. 
        fwrite($fh, "awk -F , '{if (\$5<=1) {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.16} else if (\$5>=5) {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.7 } else {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.16*\$5}}' $tmp.txt | psxyz $Rll_3D -JM3.5i -JZ3.5i -E\$azim/$elev -B$anno_x/$anno_y/10::wsneZ+ -Sc -C$tmp.cpt -Wthinnest -X2i -Y2.5i -K -P  > $tmp.ps\n");


        // Draw the red N-directional sign.
        // posX/posY is the position to draw the N-directional sign.
        $posX = ($lon2 - 0.03);
        $posY = ($lat1 + ($lat2 - ($lat1)) / 2);
        fwrite($fh, "psbasemap $Rll -J -E\$azim/$elev -T$posX/$posY/1i --COLOR_BACKGROUND=red --TICK_PEN=thinner,black -O -K >> $tmp.ps\n");


        // Draw the lon/lat/depth (km) distant scale bar. 
        $deltaX = (($lon2 - ($lon1)) / 2);
        $sBar_x = ($lon1 + $deltaX);
        $sBar_y = ($lat1 - 0.03);

        fwrite($fh, "psbasemap -R -J -O -K -E\$azim/$elev -Lf$sBar_x/$sBar_y/$sBar_y/20k --TICK_PEN=thinner,black  >> $tmp.ps\n");

        // Draw the color scale for DEPTH
        fwrite($fh, "psscale -C$tmp.cpt -D2i/-1.0i/4.5i/0.2ih -O -K -Ac -B5:DEPTH:/:km: -E >> $tmp.ps\n");

        // Draw the magnitude scale. 
        fwrite($fh, "pslegend -Dx2i/-1.8i/6.0i/0.575i/TC -J -R -O -F gmt.legend -Glightyellow >> $tmp.ps\n");

        // Convert ps to tif.
        fwrite($fh, "ps2raster $tmp.ps -Tj -E$dpi\n");
        fwrite($fh, "mv $tmp.jpg \$file.jpg\n");

        fwrite($fh, "frame=`gmt_set_framenext \$frame`\n");
        fwrite($fh, "((azim += $updatedAzim))\n");

        // Draw the frame for remaining angle.
        fwrite($fh, "if (( (\$azim > 360) && (\$exitFlag==0) ));\n");
        fwrite($fh, "then\nazim=360\nexitFlag=1 \nfi\n");

        fwrite($fh, "done \n"); # end of while loop
        // 100 ticks per second. 3000 ticks == 30s
        // The total display time is 30s.
        // The $delay is the pause between each frame.
        $displaytime = 3000;
        $delay = ($displaytime * $updatedAzim / 360);

        // makes tif from gif file
        fwrite($fh, "convert -delay $delay  *.jpg $tmp.gif\n");

        fclose($fh);
        // execute the script
        passthru("cd $tmpdir ;  bash $tmp.gmt");

        $imageFile = $tmp . ".gif";
        $imageSrc = $imageFile;

        $lastimageno = floor((360 - $initial_value) / $updatedAzim);      //Get total images number
        $lastimageno = $lastimageno + 1;                                  // because the image starts from zero
        $result['numberOfImages'] = $lastimageno;

        return $result;
    }

// Create the legend file for drawing the magnitude scale.
    function createLegend() {

        global $tmpdir;

        // The numbers, 0.06, 0.12, 0.18,..., are the drawing size
        // of the symbol. They are obtained by magnitude * 0.04.
        // e.g: magnitude is 2, symbol_size = 2*0.06 = 0.08
        $legend = array(
            "0" => "H 10 1  Magnitude",
            "1" => "D 0 1p",
            "2" => "N 5",
            "3" => "V 0 1p",
            "4" => "S 0.6c c 0.16c - 0.3p 1c M 1",
            "5" => "S 0.6c c 0.32c - 0.3p 1c M 2",
            "6" => "S 0.6c c 0.48c - 0.3p 1c M 3",
            "7" => "S 0.6c c 0.64c - 0.3p 1c M 4",
            "8" => "S 0.6c c 0.8c - 0.35p 1c M > 4",
            "11" => "V 0 1p ",
        );

        $newLegend = implode("\n", $legend);
        $fh = fopen("$tmpdir/gmt.legend", "w");
        fwrite($fh, $newLegend);
        fclose($fh);

        return "$tmpdir/gmt.legend";
    }

    function getOwnerList($ownerList) {
        $i = 0;
        $length = count($ownerList);
        $queryString = "select cc_id, cc_code from cc where ";
        $results = array();
        if ($length == 0) {
            echo json_encode($results);
            return;
        }
        for ($i = 0; $i < $length; $i++) {
            $queryString = $queryString . "cc_id = " . $ownerList[$i] . " ";
            if ($i != $length - 1)
                $queryString = $queryString . " or ";
        }
        $queryString = $queryString . ";";
        $resources = mysql_query($queryString);
        $row = mysql_fetch_array($resources);
        while ($row) {
            $results[$row[0]] = $row[1];
            $row = mysql_fetch_array($resources);
        }
        echo json_encode($results);
    }

}

class Station {

    var $stationName;
    var $lat;
    var $lon;
    var $type;

    function Station($name, $plat, $plon, $ptype) {
        $this->stationName = $name;
        $this->lat = $plat;
        $this->lon = $plon;
        $this->type = $ptype;
    }

    function outputInfo() {
        return $this->type . "& &" . $this->stationName . "&" . $this->lat . "&" . $this->lon . "& ;";
    }

}

?>
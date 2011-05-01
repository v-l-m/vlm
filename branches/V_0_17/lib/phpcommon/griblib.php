<?php

    /*
     * Provide helpers for grib/vlmc
     */

    require_once("vlmc.php");

    function get_wind_info_deg($_lat, $_long, $_time) {
        $temp_vlmc_context = new vlmc_context();
        shm_lock_sem_construct_grib_context($temp_vlmc_context, 1);
        
        $wind_boat = new wind_info();

        VLM_get_wind_info_latlong_deg_context($temp_vlmc_context, $_lat, $_long,
					      $_time, $wind_boat);
        shm_unlock_sem_destroy_grib_context($temp_vlmc_context, 1);
        
        return array ("speed" => $wind_boat->speed, "heading" => fmod($wind_boat->angle+180., 360.));
    }

    function get_grib_timestamp_array() {
        $ts_array = array();
        
        $global_vlmc_context = new vlmc_context();
        global_vlmc_context_set($global_vlmc_context);
        shm_lock_sem_construct_grib(1);
        $nb_grib = get_prevision_count();
        for ($i=0; $i < $nb_grib; $i++) { 
          array_push($ts_array, get_prevision_time_index($i));
        }
        shm_unlock_sem_destroy_grib(1);
        return $ts_array;
    }
    
    function get_grib_minmax_time() {
        $minmax = Array();
        $global_vlmc_context = new vlmc_context();
        global_vlmc_context_set($global_vlmc_context);
        shm_lock_sem_construct_grib(1);
        $minmax['max'] = get_prevision_time_index(get_prevision_count()-1);
        $minmax['min'] = get_prevision_time_index(0);
        shm_unlock_sem_destroy_grib(1);
        return $minmax;
    }

    function get_grib_validity_from_array($ts_array) {
    
        $ts_array = get_grib_timestamp_array();
        // check if we have a full grib or a 
        if (count($ts_array) > 10) {
            $cache = $ts_array[0] + 34200 - time(); /* grib offset + 9h30 */ 
        } else {
            $cache = 10; /* we use 10s as the default */
        }
        return $cache;
    }
    
    function get_grib_validity() {
        return get_grib_validity_from_array(get_grib_timestamp_array());
    }

?>

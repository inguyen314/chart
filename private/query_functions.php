<?php
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function set_options($db) {
	$stmnt_query = null;
	
    try {
		$sql = "alter session set  NLS_DATE_FORMAT='mm-dd-yyyy hh24:mi'";
        $stmnt_query = oci_parse($db, $sql);
        $status = oci_execute($stmnt_query);
        if ( !$status ) {
            $e = oci_error($db);
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
        }
    }
    catch (Exception $e) {
        $status = "ERROR: Could set database session options";
    }
	finally {
		oci_free_statement($stmnt_query); 
	}
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function set_options2($db) {
	//change format to = yyyy-mm-dd hh24:mi
	$stmnt_query = null;
	
    try {
        // mm-dd-yyyy hh24:mi
		$sql = "alter session set  NLS_DATE_FORMAT='yyyy-mm-dd hh24:mi'";
        $stmnt_query = oci_parse($db, $sql);
        $status = oci_execute($stmnt_query);
        if ( !$status ) {
            $e = oci_error($db);
            trigger_error(htmlentities($e['message']), E_USER_ERROR);
            // throw new \RuntimeException(self::$status);
        }
    }
    catch (Exception $e) {
        $status = "ERROR: Could set database session options";
        // throw new \RuntimeException(self::$status);
    }
	finally {
		oci_free_statement($stmnt_query); 
	}
}

//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_rating_stage_coe($db, $location_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select distinct office_id, template_id, location_id, version, native_units, SUBSTR(template_id, 12,4) as agency 
				from CWMS_20.AV_RATING_LOCAL
				where template_id like '%COE' 
					and template_id like 'Stage%' 
					and template_id like '%Flow%'
					and template_id not like '%Flow-%'
					and template_id not like '%,%'
					and location_id not like '%0%'
					and aliased_item is null
					and version = 'Production'
					and location_id = '".$location_id."'
				order by location_id asc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {

			$data = (object) [
				"office_id" => $row['OFFICE_ID'],
				"template_id" => $row['TEMPLATE_ID'],
				"location_id" => $row['LOCATION_ID'],
				"version" => $row['VERSION'],
				"native_units" => $row['NATIVE_UNITS'],
				"agency" => $row['AGENCY']			
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	
	return $data;
}

//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_rating_stage_nws($db,$location_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select distinct office_id, template_id, location_id, version, native_units, SUBSTR(template_id, 12,4) as agency 
				from CWMS_20.AV_RATING_LOCAL
				where template_id like '%NWS' 
					and template_id like 'Stage%' 
					and template_id like '%Flow%'
					and template_id not like '%Flow-%'
					and template_id not like '%,%'
					and location_id not like '%0%'
					and aliased_item is null
					and version = 'Production'
					and location_id = '".$location_id."'
				order by location_id asc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {

			$data = (object) [
				"office_id" => $row['OFFICE_ID'],
				"template_id" => $row['TEMPLATE_ID'],
				"location_id" => $row['LOCATION_ID'],
				"version" => $row['VERSION'],
				"native_units" => $row['NATIVE_UNITS'],
				"agency" => $row['AGENCY']			
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}


//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_rating_stage_usgs($db,$location_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select distinct office_id, template_id, location_id, version, native_units, SUBSTR(template_id, 12,4) as agency 
				from CWMS_20.AV_RATING_LOCAL
				where template_id like '%USGS' 
					and template_id like 'Stage%' 
					and template_id like '%Flow%'
					and template_id not like '%Flow-%'
					and template_id not like '%,%'
					and location_id not like '%0%'
					and aliased_item is null
					and version = 'Production'
					and location_id = '".$location_id."'
				order by location_id asc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$data = (object) [
				"office_id" => $row['OFFICE_ID'],
				"template_id" => $row['TEMPLATE_ID'],
				"location_id" => $row['LOCATION_ID'],
				"version" => $row['VERSION'],
				"native_units" => $row['NATIVE_UNITS'],
				"agency" => $row['AGENCY']			
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}

//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_location_level($db, $location_id) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "SELECT  location_id, elevation, vertical_datum
		,specified_level_id, constant_level, level_date, location_level_id
		,attribute_id, unit_system, attribute_unit, level_unit ,attribute_value, interval_origin, calendar_interval, time_interval
		,interpolate, calendar_offset, time_offset, seasonal_level, tsid, level_comment, attribute_comment
		,base_location_id, sub_location_id, base_parameter_id, sub_parameter_id, parameter_id, duration_id, location_code
		,location_level_code, expiration_date, parameter_type_id ,attribute_parameter_id, attribute_base_parameter_id
		,attribute_sub_parameter_id, attribute_duration_id
	FROM (select loc.location_id, loc.elevation, loc.vertical_datum
		,ctl.specified_level_id, ctl.constant_level, ctl.level_date, ctl.location_level_id
		,ctl.attribute_id, ctl.unit_system
		,ctl.attribute_unit, ctl.level_unit, ctl.attribute_value, ctl.interval_origin, ctl.calendar_interval, ctl.time_interval
		,ctl.interpolate, ctl.calendar_offset ,ctl.time_offset, ctl.seasonal_level, ctl.tsid, ctl.level_comment
		,ctl.attribute_comment, ctl.base_location_id, ctl.sub_location_id, ctl.base_parameter_id, ctl.sub_parameter_id
		,ctl.parameter_id ,ctl.duration_id, ctl.location_code, ctl.location_level_code, ctl.expiration_date ,ctl.parameter_type_id
		,ctl.attribute_parameter_id, ctl.attribute_base_parameter_id, ctl.attribute_sub_parameter_id, ctl.attribute_duration_id
		FROM cwms_v_loc loc
		inner join  cwms_20.av_location_level ctl on loc.location_id=ctl.location_id   
		where
				loc.db_office_id = 'MVS'
				and ctl.Unit_system = 'EN'
				and loc.Unit_system = 'EN'
				and loc.location_id = '".$location_id."'
				)   
	ORDER BY location_id asc,
	calendar_offset asc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"location_id" => $row['LOCATION_ID'],
				"elevation" => $row['ELEVATION'],
				"vertical_datum" => $row['VERTICAL_DATUM'],
				"specified_level_id" => $row['SPECIFIED_LEVEL_ID'],
				"constant_level" => $row['CONSTANT_LEVEL'],
				"level_date" => $row['LEVEL_DATE'],
				"location_level_id" => $row['LOCATION_LEVEL_ID'],
				"attribute_id" => $row['ATTRIBUTE_ID'],
				"unit_system" => $row['UNIT_SYSTEM'],
				"attribute_unit" => $row['ATTRIBUTE_UNIT'],
				"level_unit" => $row['LEVEL_UNIT'],
				"attribute_value" => $row['ATTRIBUTE_VALUE'],
				"interval_origin" => $row['INTERVAL_ORIGIN'],
				"calendar_interval" => $row['CALENDAR_INTERVAL'],
				"time_interval" => $row['TIME_INTERVAL'],
				"interpolate" => $row['INTERPOLATE'],
				"calendar_offset" => $row['CALENDAR_OFFSET'],
				"time_offset" => $row['TIME_OFFSET'],
				"seasonal_level" => $row['SEASONAL_LEVEL'],
				"tsid" => $row['TSID'],
				"level_comment" => $row['LEVEL_COMMENT'],
				"attribute_comment" => $row['ATTRIBUTE_COMMENT'],
				"base_location_id" => $row['BASE_LOCATION_ID'],
				"sub_location_id" => $row['SUB_LOCATION_ID'],
				"base_parameter_id" => $row['BASE_PARAMETER_ID'],
				"sub_parameter_id" => $row['SUB_PARAMETER_ID'],
				"parameter_id" => $row['PARAMETER_ID'],
				"duration_id" => $row['DURATION_ID'],
				"location_code" => $row['LOCATION_CODE'],
				"location_level_code" => $row['LOCATION_LEVEL_CODE'],
				"expiration_date" => $row['EXPIRATION_DATE'],
				"parameter_type_id" => $row['PARAMETER_TYPE_ID'],
				"attribute_parameter_id" => $row['ATTRIBUTE_PARAMETER_ID'],
				"attribute_base_parameter_id" => $row['ATTRIBUTE_BASE_PARAMETER_ID'],
				"attribute_sub_parameter_id" => $row['ATTRIBUTE_SUB_PARAMETER_ID'],
				"attribute_duration_id" => $row['ATTRIBUTE_DURATION_ID']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}


//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_level($db, $location_id, $specified_level_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select location_level_id
					,level_unit
					,constant_level
					,location_id
					,parameter_id
					,specified_level_id
				from CWMS_20.AV_LOCATION_LEVEL
				where specified_level_id = '".$specified_level_id."'
				and level_unit = 'ft'
				and location_id = '".$location_id."'";
			
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
				
			$data = (object) [
				"location_level_id" => $row['LOCATION_LEVEL_ID'],
				"level_unit" => $row['LEVEL_UNIT'],
				"constant_level" => $row['CONSTANT_LEVEL'],
				"location_id" => $row['LOCATION_ID'],
				"parameter_id" => $row['PARAMETER_ID'],
				"specified_level_id" => $row['SPECIFIED_LEVEL_ID']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}

//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function get_table_data($db, $cwms_ts_id, $start_day, $end_day) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "select cwms_ts_id
					,cwms_util.change_timezone(tsv.date_time, 'UTC', 'CST6CDT' ) as date_time
					,cwms_util.split_text('".$cwms_ts_id."' ,1,'.') as location_id
					,cwms_util.split_text('".$cwms_ts_id."' ,2,'.') as parameter_id
					,cwms_util.split_text('".$cwms_ts_id."' ,6,'.') as version_id
					,value
					,unit_id
					,quality_code
				from cwms_v_tsv_dqu  tsv
					where 
						tsv.cwms_ts_id = '".$cwms_ts_id."'  
						and date_time  >= cast(cast(current_date as timestamp) at time zone 'UTC' as date) - interval '".$start_day."' DAY
						and date_time  <= cast(cast(current_date as timestamp) at time zone 'UTC' as date) + interval '".$end_day."' DAY
						and (tsv.unit_id = 'ppm' or tsv.unit_id = 'F' or tsv.unit_id = CASE WHEN cwms_util.split_text(tsv.cwms_ts_id,2,'.') IN ('Stage','Elev','Opening') THEN 'ft' WHEN cwms_util.split_text(tsv.cwms_ts_id,2,'.') IN ('Precip','Depth') THEN 'in' END or tsv.unit_id = 'cfs' or tsv.unit_id = 'umho/cm' or tsv.unit_id = 'volt' or tsv.unit_id = 'ac-ft')
						and tsv.office_id = 'MVS' 
						and tsv.aliased_item is null
					order by date_time desc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"cwms_ts_id" => $row['CWMS_TS_ID'],
				"date_time" => $row['DATE_TIME'],
				"location_id" => $row['LOCATION_ID'],
				"parameter_id" => $row['PARAMETER_ID'],
				"version_id" => $row['VERSION_ID'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE']
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_gage_data_page($db, $cwms_ts_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select  control.location_id, control.stage_cwms_ts_id, cga2.group_id as basin , control.flow_usgs_cwms_ts_id, control.flow_usgsraw_cwms_ts_id, 
		control.flow_coe_cwms_ts_id, control.flow_coe_mvr_cwms_ts_id,  control.flow_nws_cwms_ts_id, control.flow_slopeadj_cwms_ts_id, control.precip_cwms_ts_id, 
		control.temp_water_cwms_ts_id, control.temp_water_cwms_ts_id2 , control.temp_water_cwms_ts_id3, control.temp_water_cwms_ts_id4, control.temp_air_cwms_ts_id, 
		control.temp_air_cwms_ts_id2,  control.do_cwms_ts_id ,cga.group_id as owner, control.sort_order, control.visable, control.datrep, control.nws_day1_cwms_ts_id, 
		control.nws_day2_cwms_ts_id, control.nws_day3_cwms_ts_id, control.display_stage29, control.stage29_cwms_ts_id, control.precip_only, control.pool, control.tw, 
		control.temp_water_cwms_ts_id_source, control.temp_air_cwms_ts_id_source, control.temp_air_cwms_ts_id2_source, control.temp_water_cwms_ts_id2_source, 
		control.do_cwms_ts_id_source , control.cond_cwms_ts_id_source ,control.datman_cwms_ts_id,control.datman,control.lake, control.depth_cwms_ts_id, 
		control.depth_cwms_ts_id2, control.do_cwms_ts_id2, control.cond_cwms_ts_id, control.cond_cwms_ts_id2, control.blackberry_stage, control.blackberry_project, 
		control.blackberry_do, control.display_morning, control.ld_project, control.pool_cwms_ts_id, control.tw_cwms_ts_id, control.hinge_cwms_ts_id, 
		control.tainter_cwms_ts_id, control.roller_cwms_ts_id, control.display_webrep, control.elev_cwms_ts_id, control.display_elev, control.crest_cwms_ts_id, 
		cwms_util.split_text(control.stage_cwms_ts_id, 4, '.') as stage_interval, control.mvr_fcst_cwms_ts_id, control.ncrfc_fcst_cwms_ts_id, control.display_forecast, 
		control.nwd_fcst_cwms_ts_id, control.mbrfc_fcst_cwms_ts_id, control.lrd_fcst_cwms_ts_id, control.rvf_fcst_cwms_ts_id, control.mvp_fcst_cwms_ts_id, 
		control.display_board, control.display_board_lake, control.tw_do_cwms_ts_id, control.gaged_outflow_cwms_ts_id, control.rereg_cwms_ts_id, 
		control.forecast_compare,control.forecast_compare, control.forecast_data_display,
		
		loc.elevation, loc.latitude, loc.longitude, loc.vertical_datum, loc.public_name,
		
		station.station, station.drainage_area, station.area_unit,
		
		location_level.constant_level as flood_level, location_level.location_level_id, location_level.level_unit,
		
		board_lib.note_title, board_lib.note
		
		from cwms_20.av_loc loc
			left join wm_mvs_cf.gage_control control
			on loc.location_id = control.location_id
				left join cwms_20.av_stream_location station
				on loc.location_id = station.location_id
					left join cwms_20.av_location_level location_level
					on loc.location_id = location_level.location_id
						left join cwms_20.av_loc_grp_assgn cga
						on loc.location_id = cga.location_id
							left join cwms_20.av_loc_grp_assgn cga2
							on loc.location_id = cga2.location_id
								left join wm_mvs_cf.board_lib board_lib
								on control.datrep = board_lib.datrep
								
		where control.location_id = cwms_util.split_text('".$cwms_ts_id."', 1, '.') 
		and station.unit_system = 'EN' 
		and loc.unit_system = 'EN' 
		and location_level.unit_system = 'EN' 
		and specified_level_id = 'Flood'
		and cga.category_id = 'RDL_MVS'
		and cga2.category_id = 'RDL_Basins'
		order by control.sort_order asc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$data = (object) [
				"basin" => $row['BASIN'],
				"location_id" => $row['LOCATION_ID'],
				"stage_cwms_ts_id" => $row['STAGE_CWMS_TS_ID'],
				"stage_interval" => $row['STAGE_INTERVAL'],
				"elev_cwms_ts_id" => $row['ELEV_CWMS_TS_ID'],
				"crest_cwms_ts_id" => $row['CREST_CWMS_TS_ID'],
				"stage29_cwms_ts_id" => $row['STAGE29_CWMS_TS_ID'],
				"nws_day1_cwms_ts_id" => $row['NWS_DAY1_CWMS_TS_ID'],
				"nws_day2_cwms_ts_id" => $row['NWS_DAY2_CWMS_TS_ID'],
				"nws_day3_cwms_ts_id" => $row['NWS_DAY3_CWMS_TS_ID'],
				"flow_coe_cwms_ts_id" => $row['FLOW_COE_CWMS_TS_ID'],
				"flow_coe_mvr_cwms_ts_id" => $row['FLOW_COE_MVR_CWMS_TS_ID'],
				"flow_usgs_cwms_ts_id" => $row['FLOW_USGS_CWMS_TS_ID'],
				"flow_usgsraw_cwms_ts_id" => $row['FLOW_USGSRAW_CWMS_TS_ID'],
				"flow_nws_cwms_ts_id" => $row['FLOW_NWS_CWMS_TS_ID'],
				"flow_slopeadj_cwms_ts_id" => $row['FLOW_SLOPEADJ_CWMS_TS_ID'],
				"precip_cwms_ts_id" => $row['PRECIP_CWMS_TS_ID'],
				"temp_water_cwms_ts_id" => $row['TEMP_WATER_CWMS_TS_ID'],
				"temp_water_cwms_ts_id2" => $row['TEMP_WATER_CWMS_TS_ID2'],
				"temp_water_cwms_ts_id3" => $row['TEMP_WATER_CWMS_TS_ID3'],
				"temp_water_cwms_ts_id4" => $row['TEMP_WATER_CWMS_TS_ID4'],
				"temp_air_cwms_ts_id" => $row['TEMP_AIR_CWMS_TS_ID'],
				"temp_air_cwms_ts_id2" => $row['TEMP_AIR_CWMS_TS_ID2'],
				"datman_cwms_ts_id" => $row['DATMAN_CWMS_TS_ID'],
				"depth_cwms_ts_id" => $row['DEPTH_CWMS_TS_ID'],
				"depth_cwms_ts_id2" => $row['DEPTH_CWMS_TS_ID2'],
				"do_cwms_ts_id" => $row['DO_CWMS_TS_ID'],
				"do_cwms_ts_id2" => $row['DO_CWMS_TS_ID2'],
				"cond_cwms_ts_id" => $row['COND_CWMS_TS_ID'],
				"cond_cwms_ts_id2" => $row['COND_CWMS_TS_ID2'],
				"elevation" => $row['ELEVATION'],
				"latitude" => $row['LATITUDE'],
				"longitude" => $row['LONGITUDE'],
				"vertical_datum" => $row['VERTICAL_DATUM'],
				"station" => $row['STATION'],
				"public_name" => $row['PUBLIC_NAME'],
				"drainage_area" => $row['DRAINAGE_AREA'],
				"area_unit" => $row['AREA_UNIT'],
				"owner" => $row['OWNER'],
				"sort_order" => $row['SORT_ORDER'],
				"visable" => $row['VISABLE'],
				"datrep" => $row['DATREP'],
				"pool" => $row['POOL'],
				"tw" => $row['TW'],
				"lake" => $row['LAKE'],
				"datman" => $row['DATMAN'],
				"blackberry_stage" => $row['BLACKBERRY_STAGE'],
				"blackberry_project" => $row['BLACKBERRY_PROJECT'],
				"blackberry_do" => $row['BLACKBERRY_DO'],
				"display_morning" => $row['DISPLAY_MORNING'],
				"display_elev" => $row['DISPLAY_ELEV'],
				"ld_project" => $row['LD_PROJECT'],
				"pool_cwms_ts_id" => $row['POOL_CWMS_TS_ID'],
				"tw_cwms_ts_id" => $row['TW_CWMS_TS_ID'],
				"hinge_cwms_ts_id" => $row['HINGE_CWMS_TS_ID'],
				"tainter_cwms_ts_id" => $row['TAINTER_CWMS_TS_ID'],
				"roller_cwms_ts_id" => $row['ROLLER_CWMS_TS_ID'],
				"temp_water_cwms_ts_id_source" => $row['TEMP_WATER_CWMS_TS_ID_SOURCE'],
				"temp_water_cwms_ts_id2_source" => $row['TEMP_WATER_CWMS_TS_ID2_SOURCE'],
				"temp_air_cwms_ts_id_source" => $row['TEMP_AIR_CWMS_TS_ID_SOURCE'],
				"temp_air_cwms_ts_id2_source" => $row['TEMP_AIR_CWMS_TS_ID2_SOURCE'],
				"do_cwms_ts_id_source" => $row['DO_CWMS_TS_ID_SOURCE'],
				"cond_cwms_ts_id_source" => $row['COND_CWMS_TS_ID_SOURCE'],
				"precip_only" => $row['PRECIP_ONLY'],
				"display_stage29" => $row['DISPLAY_STAGE29'],
				"flood_level" => $row['FLOOD_LEVEL'],
				"level_unit" => $row['LEVEL_UNIT'],
				"location_level_id" => $row['LOCATION_LEVEL_ID'],
				"display_webrep" => $row['DISPLAY_WEBREP'],
				"mvr_fcst_cwms_ts_id" => $row['MVR_FCST_CWMS_TS_ID'],
				"mvp_fcst_cwms_ts_id" => $row['MVP_FCST_CWMS_TS_ID'],
				"ncrfc_fcst_cwms_ts_id" => $row['NCRFC_FCST_CWMS_TS_ID'],
				"display_forecast" => $row['DISPLAY_FORECAST'],
				"display_board" => $row['DISPLAY_BOARD'],
				"display_board_lake" => $row['DISPLAY_BOARD_LAKE'],
				"tw_do_cwms_ts_id" => $row['TW_DO_CWMS_TS_ID'],
				"nwd_fcst_cwms_ts_id" => $row['NWD_FCST_CWMS_TS_ID'],
				"mbrfc_fcst_cwms_ts_id" => $row['MBRFC_FCST_CWMS_TS_ID'],
				"lrd_fcst_cwms_ts_id" => $row['LRD_FCST_CWMS_TS_ID'],
				"rvf_fcst_cwms_ts_id" => $row['RVF_FCST_CWMS_TS_ID'],
				"gaged_outflow_cwms_ts_id" => $row['GAGED_OUTFLOW_CWMS_TS_ID'],
				"rereg_cwms_ts_id" => $row['REREG_CWMS_TS_ID'],
				"forecast_compare" => $row['FORECAST_COMPARE'],
				"forecast_data_display" => $row['FORECAST_DATA_DISPLAY'],
				"note_title" => $row['NOTE_TITLE'],
				"note" => $row['NOTE']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_metadata($db, $location_id) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "select  loc.location_code, 
					loc.base_location_code, 
					loc.sub_location_id, 
					loc.location_id, 
					loc.location_type, 
					loc.unit_system, 
					loc.elevation,
					loc.unit_id,
					loc.vertical_datum,
					loc.longitude,
					loc.latitude,
					loc.time_zone_name,
					loc.county_name,
					loc.state_initial,
					loc.public_name,
					loc.long_name,
					loc.description,
					loc.base_loc_active_flag,
					loc.loc_active_flag,
					loc.location_kind_id,
					loc.map_label,
					loc.published_latitude,
					loc.published_longitude,
					loc.bounding_office_id,
					loc.nation_id,
					loc.nearest_city,
					loc.active_flag,
					stream.station, 
					stream.stream_location_code, 
					stream.stream_location_id, 
					stream.bank, 
					stream.station_unit, 
					stream.area_unit, 
					stream.lowest_measurable_stage, 
					stream.navigation_station, 
					stream.drainage_area,
					stream.ungaged_area 
			from cwms_v_loc loc 
			join cwms_v_stream_location stream on 
			loc.location_id=stream.location_id
			where loc.location_id = '" . $location_id . "'
				and loc.unit_system = 'EN'
				and loc.sub_location_id is NOT null 
				and stream.unit_system = 'EN'";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$obj = (object) [
				"location_code" => $row['LOCATION_CODE'],
				"base_location_code" => $row['BASE_LOCATION_CODE'],
				"sub_location_id" => $row['SUB_LOCATION_ID'],
				"location_id" => $row['LOCATION_ID'],
				"location_type" => $row['LOCATION_TYPE'],
				"unit_system" => $row['UNIT_SYSTEM'],
				"elevation" => $row['ELEVATION'],
				"unit_id" => $row['UNIT_ID'],
				"vertical_datum" => $row['VERTICAL_DATUM'],
				"longitude" => $row['LONGITUDE'],
				"latitude" => $row['LATITUDE'],
				"time_zone_name" => $row['TIME_ZONE_NAME'],
				"county_name" => $row['COUNTY_NAME'],
				"state_initial" => $row['STATE_INITIAL'],
				"public_name" => $row['PUBLIC_NAME'],
				"long_name" => $row['LONG_NAME'],
				"description" => $row['DESCRIPTION'],
				"base_loc_active_flag" => $row['BASE_LOC_ACTIVE_FLAG'],
				"loc_active_flag" => $row['LOC_ACTIVE_FLAG'],
				"location_kind_id" => $row['LOCATION_KIND_ID'],
				"map_label" => $row['MAP_LABEL'],
				"published_latitude" => $row['PUBLISHED_LATITUDE'],
				"published_longitude" => $row['PUBLISHED_LONGITUDE'],
				"bounding_office_id" => $row['BOUNDING_OFFICE_ID'],
				"nation_id" => $row['NATION_ID'],
				"nearest_city" => $row['NEAREST_CITY'],
				"active_flag" => $row['ACTIVE_FLAG'],
				"station" => $row['STATION'],
				"stream_location_code" => $row['STREAM_LOCATION_CODE'],
				"stream_location_id" => $row['STREAM_LOCATION_ID'],
				"bank" => $row['BANK'],
				"station_unit" => $row['STATION_UNIT'],
				"lowest_measurable_stage" => $row['LOWEST_MEASURABLE_STAGE'],
				"navigation_station" => $row['NAVIGATION_STATION'],
				"drainage_area" => $row['DRAINAGE_AREA'],
				"ungaged_area" => $row['UNGAGED_AREA']
				
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_location_level3($db, $location_id) {
	// get location level based on loction_id
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "select  basin, sub_basin, location_id, elevation, vertical_datum, group_id, category_id
					,specified_level_id, constant_level, level_date, location_level_id
					,attribute_id, unit_system, attribute_unit, level_unit ,attribute_value, interval_origin, calendar_interval, time_interval
					,interpolate, calendar_offset, time_offset, seasonal_level, tsid, level_comment, attribute_comment
					,base_location_id, sub_location_id, base_parameter_id, sub_parameter_id, parameter_id, duration_id, location_code
					,location_level_code, expiration_date, parameter_type_id ,attribute_parameter_id, attribute_base_parameter_id
					,attribute_sub_parameter_id, attribute_duration_id
				from (select basins.group_id as basin, basins.sub_location_id as sub_basin
					,cga.category_id, cga.group_id
					,loc.location_id, loc.elevation, loc.vertical_datum
					,ctl.specified_level_id, ctl.constant_level, ctl.level_date, ctl.location_level_id
					,ctl.attribute_id, ctl.unit_system
					,ctl.attribute_unit, ctl.level_unit, ctl.attribute_value, ctl.interval_origin, ctl.calendar_interval, ctl.time_interval
					,ctl.interpolate, ctl.calendar_offset ,ctl.time_offset, ctl.seasonal_level, ctl.tsid, ctl.level_comment
					,ctl.attribute_comment, ctl.base_location_id, ctl.sub_location_id, ctl.base_parameter_id, ctl.sub_parameter_id
					,ctl.parameter_id ,ctl.duration_id, ctl.location_code, ctl.location_level_code, ctl.expiration_date ,ctl.parameter_type_id
					,ctl.attribute_parameter_id, ctl.attribute_base_parameter_id, ctl.attribute_sub_parameter_id, ctl.attribute_duration_id
					from cwms_v_loc loc
					inner join cwms_v_loc_grp_assgn cga on
							cga.location_id=loc.location_id
					inner join  cwms_20.av_location_level ctl on loc.location_id=ctl.location_id   
					inner join cwms_20.av_loc_grp_assgn basins on ctl.location_id=basins.location_id
					where
							loc.db_office_id = 'MVS'
							and ctl.unit_system = 'EN'
							and loc.unit_system = 'EN'
							and cga.category_id = 'RDL_MVS'
							and basins.category_id='RDL_Basins'
							and loc.location_id = '" . $location_id . "'
							)   
				order by basin,
				location_id asc,
				specified_level_id asc,
				calendar_offset asc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			
			$obj = (object) [
				"basin" => $row['BASIN'],
				"sub_basin" => $row['SUB_BASIN'],
				"location_id" => $row['LOCATION_ID'],
				"elevation" => $row['ELEVATION'],
				"vertical_datum" => $row['VERTICAL_DATUM'],
				"group_id" => $row['GROUP_ID'],
				"category_id" => $row['CATEGORY_ID'],
				"specified_level_id" => $row['SPECIFIED_LEVEL_ID'],
				"constant_level" => $row['CONSTANT_LEVEL'],
				"level_date" => $row['LEVEL_DATE'],
				"location_level_id" => $row['LOCATION_LEVEL_ID'],
				"attribute_id" => $row['ATTRIBUTE_ID'],
				"unit_system" => $row['UNIT_SYSTEM'],
				"attribute_unit" => $row['ATTRIBUTE_UNIT'],
				"level_unit" => $row['LEVEL_UNIT'],
				"attribute_value" => $row['ATTRIBUTE_VALUE'],
				"interval_origin" => $row['INTERVAL_ORIGIN'],
				"calendar_interval" => $row['CALENDAR_INTERVAL'],
				"time_interval" => $row['TIME_INTERVAL'],
				"interpolate" => $row['INTERPOLATE'],
				"calendar_offset" => $row['CALENDAR_OFFSET'],
				"time_offset" => $row['TIME_OFFSET'],
				"seasonal_level" => $row['SEASONAL_LEVEL'],
				"tsid" => $row['TSID'],
				"level_comment" => $row['LEVEL_COMMENT'],
				"attribute_comment" => $row['ATTRIBUTE_COMMENT'],
				"base_location_id" => $row['BASE_LOCATION_ID'],
				"sub_location_id" => $row['SUB_LOCATION_ID'],
				"base_parameter_id" => $row['BASE_PARAMETER_ID'],
				"sub_parameter_id" => $row['SUB_PARAMETER_ID'],
				"parameter_id" => $row['PARAMETER_ID'],
				"duration_id" => $row['DURATION_ID'],
				"location_code" => $row['LOCATION_CODE'],
				"location_level_code" => $row['LOCATION_LEVEL_CODE'],
				"expiration_date" => $row['EXPIRATION_DATE'],
				"parameter_type_id" => $row['PARAMETER_TYPE_ID'],
				"attribute_parameter_id" => $row['ATTRIBUTE_PARAMETER_ID'],
				"attribute_base_parameter_id" => $row['ATTRIBUTE_BASE_PARAMETER_ID'],
				"attribute_sub_parameter_id" => $row['ATTRIBUTE_SUB_PARAMETER_ID'],
				"attribute_duration_id" => $row['ATTRIBUTE_DURATION_ID']			
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_datman_parameter_id_from_location_id($db, $location_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select cwms_util.split_text(cwms_ts_id, 2, '.') as parameter_id
				from cwms_v_ts_id
				where location_id = '" . $location_id . "' 
				and cwms_util.split_text(cwms_ts_id, 6, '.') like cwms_util.normalize_wildcards('datman-rev')";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = $row['PARAMETER_ID'];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_datman_latest_time($db, $location_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select to_char(latest_time, 'mm-dd-yyyy') as latest_time
				from CWMS_20.AV_TS_EXTENTS_UTC
				where (ts_id = '".$location_id."' || '.Stage.Inst.~1Day.0.datman-rev') or (ts_id = '".$location_id."' || '.Elev.Inst.~1Day.0.datman-rev')";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {	
			$data =  $row['LATEST_TIME'];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_stage_ts_id_from_cwms_ts_id($db, $cwms_ts_id) {
	$stmnt_query = null;
	$data = null;

	try {		
		$sql = "select cwms_ts_id 
				from cwms_v_ts_id
				where location_id = (select distinct cwms_util.split_text('".$cwms_ts_id."', 1, '.') as location_id from cwms_v_ts_id)
					and parameter_id = 'Stage'
					and version_id in ('lrgsShef-rev')";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {	
			$data =  $row['CWMS_TS_ID'];	
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_elev_ts_id_from_cwms_ts_id($db, $cwms_ts_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select cwms_ts_id 
				from cwms_v_ts_id
				where location_id = (select distinct cwms_util.split_text('".$cwms_ts_id."', 1, '.') as location_id from cwms_v_ts_id)
					and parameter_id = 'Elev'
					and version_id in ('lrgsShef-rev')";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data =  $row['CWMS_TS_ID'];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function find_quality_codes($db, $cwms_ts_id) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "with cte_cwms_ts_id as (
				select distinct quality_code 
				from cwms_v_tsv_dqu 
				where cwms_ts_id = '".$cwms_ts_id."'
				),
				cte_code as (
				select quality_code, screened_id, validity_id, range_id, changed_id, repl_cause_id, repl_method_id, test_failed_id, protection_id from cwms_20.av_data_quality
				)

				select cwms_ts_id.quality_code
				,code.screened_id, code.validity_id, code.range_id, code.changed_id, code.repl_cause_id, code.repl_method_id, code.test_failed_id, code.protection_id
				from cte_cwms_ts_id cwms_ts_id
				join cte_code code on
				cwms_ts_id.quality_code = code.quality_code
				order by code.protection_id asc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$obj = (object) [
				"quality_code" => $row['QUALITY_CODE'],
				"screened_id" => $row['SCREENED_ID'],
				"validity_id" => $row['VALIDITY_ID'],
				"range_id" => $row['RANGE_ID'],
				"changed_id" => $row['CHANGED_ID'],
				"repl_cause_id" => $row['REPL_CAUSE_ID'],
				"repl_method_id" => $row['REPL_METHOD_ID'],
				"test_failed_id" => $row['TEST_FAILED_ID'],
				"protection_id" => $row['PROTECTION_ID']
			];
			array_push($data,$obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
function quality_code_screened($db) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "select screened_id, description from cwms_v_data_q_screened";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) 
		{
			
			$obj = (object) [
				"screened_id" => $row['SCREENED_ID'],
				"description" => $row['DESCRIPTION']
			];
			array_push($data,$obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------	
function find_first_time_series_data_point($db, $cwms_ts_id) {
	$stmnt_query = null;
	$data = null;
	
	try {		
		$sql = "select cwms_util.change_timezone(t.date_time, 'UTC', 'CST6CDT') as date_time
					, date_time as data_entry_date
					, t.date_time as utc_date_time
					, t.value
					, t.unit_id
					, t.ts_code
					, t.cwms_ts_id
					, row_number() over (partition by t.cwms_ts_id
					, t.unit_id
					, t.cwms_ts_id
				order by t.date_time desc) as rank
				from cwms_v_tsv_dqu  t
					where office_id = 'MVS'        
						and date_time  >= cast(cast((select earliest_time from cwms_v_ts_extents_local  where ts_id = '".$cwms_ts_id."') as timestamp) at time zone 'UTC' as date) 
						and date_time  <= cast(cast((select earliest_time from cwms_v_ts_extents_local  where ts_id = '".$cwms_ts_id."') as timestamp) at time zone 'UTC' as date) + interval '1' year
						and unit_id in('ppm','F', 'ft','cfs','umho/cm')
						and cwms_ts_id = '".$cwms_ts_id."'
						and value is not null
						order by rank desc
						fetch first 1 rows only";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$data = (object) [
				"date_time" => $row['DATE_TIME'],
				"data_entry_date" => $row['DATA_ENTRY_DATE'],
				"utc_date_time" => $row['UTC_DATE_TIME'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"ts_code" => $row['TS_CODE'],
				"cwms_ts_id" => $row['CWMS_TS_ID'],
				"rank" => $row['RANK']
			];
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);

		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}
//------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------
?>

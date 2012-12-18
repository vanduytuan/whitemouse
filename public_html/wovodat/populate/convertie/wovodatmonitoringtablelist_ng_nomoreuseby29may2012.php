<?php
$wovodatmontables=array(
	"SeismicNetwork",
	"SeismicStation",
	"SeismicInstrument",
	"SeismicComponent",
	"DeformationNetwork",
	"DeformationStation",
	"DeformationInstrument_General",
	"DeformationInstrument_Tilt/Strain",
	"GasNetwork",
	"GasStation",
	"GasInstrument",
	"HydrologicNetwork",
	"HydrologicStation",
	"HydrologicInstrument",
	"ThermalNetwork",
	"ThermalStation",
	"ThermalInstrument",
	"FieldsNetwork",
	"FieldsStation",
	"FieldsInstrument"
);
$wovodatvalue=array(
	"Seismic",
	"Seismic",
	"Seismic",
	"Seismic",
	"Deformation",
	"Deformation",
	"Deformation",
	"Deformation",
	"Gas",
	"Gas",
	"Gas",
	"Hydrologic",
	"Hydrologic",
	"Hydrologic",
	"Thermal",
	"Thermal",
	"Thermal",
	"Fields",
	"Fields",
	"Fields"
);

	echo "<option value='...' selected='true'>...</option>";
	
	foreach($wovodatmontables as $k =>$v){
		echo "<option name='{$wovodatvalue[$k]}' value='$v'>" .$v. "</option>";
	}

?>

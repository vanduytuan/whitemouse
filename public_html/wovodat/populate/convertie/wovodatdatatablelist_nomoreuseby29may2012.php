<?php
header("Cache-Control: no-cache");
$wovodattables=array(
	"EventDataFromNetwork",
	"EventDataFromSingleStation",
	"IntensityData",
	"SeismicTremor",
	"IntervalSwarmData",
	"RSAMData",
	"SSAMData",
	"RepresentativeWaveform",
	"ElectronicTiltData",
	"TiltVectorData",
	"StrainMeterData",
	"EDMData",
	"AngleData",
	"GPSData",
	"GPSVectors",
	"LevelingData",
	"InSARImage and InSARData",
	"DirectlySampledGas",
	"SoilEffluxData",
	"PlumeData",
	"HydrologicData",
	"MagneticFieldsData",
	"MagnetorVectorData",
	"ElectricFieldsData",
	"GravityData",
	"GroundBasedThermalData",
	"ThermalImage and ThermalImageData",
);

echo "<option value='...' option='selected'>...</option>";

foreach($wovodattables as $k =>$v){
	echo "<option value='{$wovodattables[$k]}'>" .$v. "</option>";
}
?>
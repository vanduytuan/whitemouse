<?php

// makes the GMT script
function visual2D() { 

        global $ldep, $title, $vlon, $vlat, $lon1, $lon2, $lat1, $lat2, $Rll, $slat, $Jlat, $Jlon, $box;
        global $tmpdir, $tmp, $htmout, $imageFile, $imageSrc;

        $fh = fopen("$tmpdir/$tmp.gmt",'w') or die("can't open file for writing gmt file <br/>");
        // GMT set parameters
        fwrite($fh,"gmtset PAPER_MEDIA=A4 FRAME_WIDTH=0.15c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n");
        fwrite($fh,"gmtset INPUT_CLOCK_FORMAT=hh:mm:ss INPUT_DATE_FORMAT=yyyy-mm-dd TIME_FORMAT_PRIMARY abbreviated PLOT_DATE_FORMAT o\n");
        fwrite($fh,"gmtset OUTPUT_DATE_FORMAT=yyyy-mm-dd\n");
        fwrite($fh,"gmtset CHAR_ENCODING ISOLatin1+\n");
        // makes colormap
        fwrite($fh,"makecpt -Cno_green -I -T0/$ldep/1 > $tmp.cpt\n");

        // plan view
        fwrite($fh,"psbasemap -Jm$Jlat $Rll -Ba5mf5mg5m:.\"$title\":WesN -X2.3c -Y14c -P -K > $tmp.ps\n");
        fwrite($fh,"pscoast -J -R -Df -W1p -S150/170/255 -N1/1.5p,black -N2/1p,50/50/50 -Tf178/-35/1i/2 -O -K >> $tmp.ps\n");
        fwrite($fh,"pscoast -J -R -Df -C0/169/223 -Lf$vlon/$slat/$vlat/10k+u -O -K >> $tmp.ps\n");
        fwrite($fh,"awk -F , '{print \$3,\$2,\$4}' $tmp.txt | psxy -J -R -Sc0.075i -C$tmp.cpt -G255 -W0.25p -O -K >> $tmp.ps\n");
        // N-S projection
        fwrite($fh,"printf $box | psxy -R-5/$ldep/$lat1/$lat2 -Jx0.17c/$Jlon -Ba5f5g0/a5f5g0::wesN -W1 -P -O -X14c -Y0 -K >> $tmp.ps\n");
        fwrite($fh,"awk -F , '{if (\$3>=$lon1 && \$3<=$lon2) {print \$4,\$2,\$4}}' $tmp.txt | psxy -R -J  -Sc0.075i -C$tmp.cpt -W0.25p -O -K >> $tmp.ps\n");
        // W-E projection
        fwrite($fh,"printf $box | psxy -R$lon1/$lon2/-$ldep/5 -Jx$Jlat/0.17c -Ba5f5g0/a5f5g0 -W1 -P -O -X-14c -Y-5c -K >> $tmp.ps\n");
        fwrite($fh,"awk -F , '{if (\$2>=$lat1 && \$2<=$lat2) {print \$3,-\$4,\$4}}' $tmp.txt | psxy -R -J  -Sc0.075i -C$tmp.cpt -W0.25p -O -K >> $tmp.ps\n");
        // depth scale
        fwrite($fh,"psscale -D16c/2c/-4c/0.3c -C$tmp.cpt -B10f10/:\"Depth (km)\": -O -K >> $tmp.ps\n");
        // depth vs time
        fwrite($fh,"cat $tmp.txt | sed s/\\ /T/g | awk -F , {'print \$6,-\$4,\$4'} > $tmp.xyz\n");
        fwrite($fh,"R=`minmax -fT -I5 $tmp.xyz`\n");
        fwrite($fh,"psbasemap \$R -JX17c/4c -Bs1Y/WESn -Bpa3Of1o/a5f5g0 -P -Y-5c -U\"$stamp\" -O -K >> $tmp.ps\n");
        fwrite($fh,"psxy $tmp.xyz -R -J -Sc0.075i -C$tmp.cpt  -W0.25p -V -O >> $tmp.ps\n");


        // makes PNG from PS file
        fwrite($fh,"convert $tmp.ps $tmp.png\n");
        fclose($fh);
        // execute the script
        exec("cd $tmpdir ; sh $tmp.gmt");                  
}

// Create the legend file for drawing the magnitude scale.
function createLegend () {
     
    global $tmpdir;

    // The numbers, 0.06, 0.12, 0.18,..., are the drawing size
    // of the symbol. They are obtained by magnitude * 0.04.
    // e.g: magnitude is 2, symbol_size = 2*0.06 = 0.08
    $legend = array (
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

    $newLegend = implode ("\n", $legend);
    $fh = fopen("$tmpdir/gmt.legend", "w");
    fwrite($fh, $newLegend);
    fclose($fh);

    return "$tmpdir/gmt.legend";
}

function visual3D () {

    global $wkm, $ldep, $title, $vlon, $vlat, $lon1, $lon2; 
    global $lat1, $lat2, $Rll, $slat, $Jlat, $Jlon, $box;
    global $tmpdir, $tmp, $title, $htmout, $imageFile; 
    global $imageSrc, $initial_value, $updatedAzim, $rotateType;
       

    $minDep = 0; // min depth (km)
    $maxDep = 50; // max depth (km)
    $zMin = (-1 * $maxDep);
    $zMax = (-1 * $minDep); 
    $dx = ($lon2-($lon1));
    $dy = ($lat2-($lat1));
    $dz = ($maxDep-$minDep);
    $anno_x = $dx/5; // Set 4 tickmarks for longitude 
    $anno_y= $dy/5;  // Set 6 tickmarks for latitude
    $Rll_3D = "-R$lon1/$lon2/$lat1/$lat2/$zMin/$zMax";

    $gmtLegend = createLegend();            
    $dpi = 100;		
    $elev = 25;               

    $fh = fopen("$tmpdir/$tmp.gmt",'w') or die("can't open file for writing gmt file <br/>");

    // GMT set parameters
    fwrite($fh, ". gmt_shell_functions.sh\n");
    //fwrite($fh,"gmtset PAPER_MEDIA=A4 FRAME_WIDTH=0.2c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n");
    fwrite($fh,"gmtset PAPER_MEDIA=Custom_570x570 FRAME_WIDTH=0.2c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n");
    fwrite($fh,"gmtset INPUT_CLOCK_FORMAT=hh:mm:ss INPUT_DATE_FORMAT=yyyy-mm-dd TIME_FORMAT_PRIMARY abbreviated PLOT_DATE_FORMAT o\n");
    fwrite($fh,"gmtset OUTPUT_DATE_FORMAT=yyyy-mm-dd\n");
    fwrite($fh,"gmtset PAGE_COLOR=243/255/237 \n");
    fwrite($fh,"gmtset CHAR_ENCODING ISOLatin1+\n");

    // makes colormap
    fwrite($fh,"makecpt -Crainbow -T$zMin/$zMax/5 -Z > $tmp.cpt\n");

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
    fwrite($fh,"awk -F , '{if (\$5<=1) {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.16} else if (\$5>=5) {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.7 } else {print \$3,\$2,(-1)*\$4,(-1)*\$4,0.16*\$5}}' $tmp.txt | psxyz $Rll_3D -JM3.5i -JZ3.5i -E\$azim/$elev -B$anno_x/$anno_y/10::wsneZ+ -Sc -C$tmp.cpt -Wthinnest -X2i -Y2.5i -K -P  > $tmp.ps\n");


    // Draw the red N-directional sign.
    // posX/posY is the position to draw the N-directional sign.
    $posX = ($lon2-0.03);
    $posY = ($lat1+($lat2-($lat1))/2);
    fwrite($fh, "psbasemap $Rll -J -E\$azim/$elev -T$posX/$posY/1i --COLOR_BACKGROUND=red --TICK_PEN=thinner,black -O -K >> $tmp.ps\n");

 
    // Draw the lon/lat/depth (km) distant scale bar. 
    $deltaX = (($lon2-($lon1))/2); 
    $sBar_x = ($lon1+$deltaX); 
    $sBar_y = ($lat1-0.03);

    fwrite($fh, "psbasemap -R -J -O -K -E\$azim/$elev -Lf$sBar_x/$sBar_y/$sBar_y/20k --TICK_PEN=thinner,black  >> $tmp.ps\n");

    // Draw the color scale for DEPTH
    fwrite($fh, "psscale -C$tmp.cpt -D2i/-1.0i/4.5i/0.2ih -O -K -Ac -B5:DEPTH:/:km: -E >> $tmp.ps\n");

    // Draw the magnitude scale. 
    fwrite($fh, "pslegend -Dx2i/-1.8i/6.0i/0.575i/TC -J -R -O -F gmt.legend -Glightyellow >> $tmp.ps\n");

    // Convert ps to tif.
    fwrite($fh, "ps2raster $tmp.ps -Tj -E$dpi\n");
    fwrite ($fh, "mv $tmp.jpg \$file.jpg\n");

    fwrite($fh, "frame=`gmt_set_framenext \$frame`\n");
    fwrite($fh, "((azim += $updatedAzim))\n");

    // Draw the frame for remaining angle.
    fwrite($fh, "if (( (\$azim > 360) && (\$exitFlag==0) ));\n");
    fwrite($fh, "then\nazim=360\nexitFlag=1 \nfi\n");

    fwrite($fh, "done \n"); # end of while loop
    
    // 100 ticks per second. 3000 ticks == 30s
    // The total display time is 30s.
    // The $delay is the pause between each frame.
    $displaytime=3000;
    $delay= ($displaytime * $updatedAzim/360);
        
    // makes tif from gif file
    fwrite($fh, "convert -delay $delay  *.jpg $tmp.gif\n");

    fclose($fh);

    // execute the script
    exec("cd $tmpdir ; bash $tmp.gmt");

    $imageFile = $tmp . ".gif";
    $imageSrc = $imageFile;
}
          
?>            

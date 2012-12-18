#!/usr/bin/perl
#
# WOVOdat
# equake3d: display earthquake data from database using GMT
#
# EOS, August 2011
#
# Authors: François Beauducel <beauducel@ipgp.fr>, Antonius Ratdomopurbo <rdpurbo@ntu.edu.sg>, Christina Widiwijayanti <CWidiwijayanti@ntu.edu.sg>
# Created: 2011-08-22
# Updated: 2011-08-25


use strict;
use DBI;
use CGI;
use File::Temp;
use File::Path;
use File::Copy;
use Math::Trig;
#use Locale::Recode;


my $cgi = new CGI;

my @volcanoes;
my $dates;
my $limit;
my $quaketype;
my $depth;
my @res;

# defines the public_html root directory (absolute path on the Apache server)
my $htmroot = "/var/wovo/public_html/wovodat";
# subdiretory name
my $outdir = 'output';
# basename for output files
my $tmp = 'eq';
# timestamp text
my $stamp = "by WOVOdat/EOS";
	

# connects to the database
my $dbh = DBI->connect('DBI:mysql:database=wovodat;host=www.wovodat.org','wovodat_view','+00World',{'RaiseError' => 1});
my $sth;

# sets the PATH environment variable for Linux, GMT and ImageMagick binaries
$ENV{'PATH'} = '/bin:/usr/bin:/usr/lib/gmt/bin:/usr/lib/gmt/share:/usr/lib/gmt/lib:/usr/lib/gmt/include';
$ENV{'GMTHOME'} = '/usr/lib/gmt';

# HTTP header
print $cgi->header(-charset=>"utf-8");

# --- Script part A: GET (when arguments are given)
if ($cgi->param) {
    
	print "<html><body bgcolor=\"white\">",
		"<div id=\"attente\">Making earthquake map, please wait...</div>\n";

	# get parameters
	my $vd_id = $cgi->param('vd_id');
	my $qty = $cgi->param('qty');
	if ($qty) {
		$limit = " limit $qty";
	}
	my $date_start = $cgi->param('date_start');
	my $date_end = $cgi->param('date_end');
	my $dr_start = $cgi->param('dr_start');
	my $dr_end = $cgi->param('dr_end');
	my $eqtype = $cgi->param('eqtype');
	if ($date_start && $date_end) {
		my @startDate = split('/', $date_start);
		my @endDate = split('/', $date_end);
		$dates = " and c.sd_evn_time BETWEEN '$startDate[2]-$startDate[0]-$startDate[1]' AND '$endDate[2]-$endDate[0]-$endDate[1]' ";
	}
	if ($eqtype){
		$quaketype = " and sd_evn_eqtype = '$eqtype' ";
	}
	if ($dr_start && $dr_end) {
		$depth = " and c.sd_evn_edep BETWEEN '$dr_start' AND '$dr_end' ";
	}

	my $wkm = $cgi->param('map_width');
	if ($wkm == "") {
		$wkm = 20;
	}

	# cleaning: deletes output directories older than 1 hour
	# [FB Note]: this can be achieved also by a cron on the server...
	qx(find $htmroot/$outdir -name 'wovodat.*' \! \\( -newerct '1 hour ago' \\) | xargs rm -rf);

	# created a temporary and unique directory
	my $tmpdir = mkdtemp("$htmroot/$outdir/wovodat.XXXXXX");
	my $htmout = $tmpdir;
	$htmout =~ s/$htmroot//;

	# SQL query: get the volcano name
	$sth = $dbh->prepare("SELECT vd_name FROM vd WHERE vd.vd_id='$vd_id'");
	$sth->execute or die "SQL Error: $DBI::errstr\n";
	@res = $sth->fetchrow;
	my $vd_name = join(//,@res);

	# SQL query: get the volcano position Lat/Lon
	$sth = $dbh->prepare("SELECT vd_inf_slat, vd_inf_slon FROM vd_inf WHERE vd_inf.vd_id='$vd_id'");
	$sth->execute or die "SQL Error: $DBI::errstr\n";
	my @vd_latlon = $sth->fetchrow_array;

	# SQL query: get the data (approximate selection from map width)
	$sth = $dbh->prepare("(select b.sn_code, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, c.sd_evn_pmag, c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon FROM sn b, sd_evn c, vd_inf d WHERE b.sn_id = c.sn_id AND b.vd_id=d.vd_id AND d.vd_id = '$vd_id' $dates $depth $quaketype ORDER BY (sd_evn_time) DESC $limit) UNION (select b.sn_code, c.sd_evn_elat, c.sd_evn_elon, c.sd_evn_edep, c.sd_evn_pmag, c.sd_evn_time, c.sd_evn_eqtype, d.vd_inf_slat, d.vd_inf_slon FROM jj_volnet a, sn b, sd_evn c, vd_inf d WHERE a.vd_id = '$vd_id' AND a.jj_net_id = b.sn_id AND b.sn_id = c.sn_id AND d.vd_id = '$vd_id' AND a.jj_net_flag = 'S' $dates $depth $quaketype AND (sqrt(power(d.vd_inf_slat - c.sd_evn_elat, 2) + power(d.vd_inf_slon - c.sd_evn_elon, 2))*111)<=1.5*$wkm ORDER BY (sd_evn_time) DESC $limit)");
	$sth->execute or die "SQL Error: $DBI::errstr\n";

	# writes the data into a single file
	my $nb = 0;
	open (FILE, ">>$tmpdir/$tmp.txt");
	while (@res = $sth->fetchrow_array) {
		print FILE join(',',@res),"\n";
		$nb++;
	} 
	close (FILE); 
    
	my $J = 74*20/$wkm;	# Jm scale (normalized with map width)
	my $ldep = 20;	# max depth for profiles (km)
	my $title = "$vd_name ($nb events)";
	my $vlon = $vd_latlon[1];
	my $vlat = $vd_latlon[0];
	my $kmlat = 6370*deg2rad(1);	# length of a latitude degree (in km)
	my $kmlon = $kmlat*cos(deg2rad($vlat));	# length of a longitude degree at the volcano latitude (in km)
	my $lon1 = ($vlon-.5*$wkm/$kmlon);
	my $lon2 = ($vlon+.5*$wkm/$kmlon);
	my $lat1 = ($vlat-.5*$wkm/$kmlat);
	my $lat2 = ($vlat+.5*$wkm/$kmlat);
	my $Rll = "-R$lon1/$lon2/$lat1/$lat2";
	my $slat = ($vlat-.44*$wkm/$kmlat);	# latitude position of km scale
	my $Jlat = $J*$kmlon/$kmlat;
	my $Jlon = $J;
	my $box = "'0 0\n1 0\n1 -1\n0 -1\n0 0\n'";
	
	# makes the GMT script
	open (FILE, ">>$tmpdir/$tmp.gmt");
		# GMT set parameters
		print FILE "gmtset PAPER_MEDIA=A4 FRAME_WIDTH=0.15c LABEL_FONT_SIZE=12p ANNOT_FONT_SIZE_PRIMARY=12p HEADER_FONT_SIZE=20p\n";
		print FILE "gmtset INPUT_CLOCK_FORMAT=hh:mm:ss INPUT_DATE_FORMAT=yyyy-mm-dd TIME_FORMAT_PRIMARY abbreviated PLOT_DATE_FORMAT o\n";
		print FILE "gmtset OUTPUT_DATE_FORMAT=yyyy-mm-dd\n";
		print FILE "gmtset CHAR_ENCODING ISOLatin1+\n";
		# makes colormap
		print FILE "makecpt -Cno_green -I -T0/$ldep/1 > $tmp.cpt\n";
		# plan view
		print FILE "psbasemap -Jm$Jlat $Rll -Ba5mf5mg5m:.\"$title\":WesN -X2.3c -Y14c -P -K > $tmp.ps\n";
		print FILE "pscoast -J -R -Df -W1p -S150/170/255 -N1/1.5p,black -N2/1p,50/50/50 -Tf178/-35/1i/2 -O -K >> $tmp.ps\n";
		print FILE "pscoast -J -R -Df -C0/169/223 -Lf$vlon/$slat/$vlat/10k+u -O -K >> $tmp.ps\n";
		print FILE "awk -F \, '{print \$3,\$2,\$4}' $tmp.txt | psxy -J -R -Sc0.075i -C$tmp.cpt -G255 -W0.25p -O -K >> $tmp.ps\n";
		# N-S projection
		print FILE "printf $box | psxy -R-5/$ldep/$lat1/$lat2 -Jx0.17c/$Jlon -Ba5f5g0/a5f5g0::wesN -W1 -P -O -X14c -Y0 -K >> $tmp.ps\n";
		print FILE "awk -F \, '{if (\$3>=$lon1 && \$3<=$lon2) {print \$4,\$2,\$4}}' $tmp.txt | psxy -R -J  -Sc0.075i -C$tmp.cpt -W0.25p -O -K >> $tmp.ps\n";
		# W-E projection
		print FILE "printf $box | psxy -R$lon1/$lon2/-$ldep/5 -Jx$Jlat/0.17c -Ba5f5g0/a5f5g0 -W1 -P -O -X-14c -Y-5c -K >> $tmp.ps\n";
		print FILE "awk -F \, '{if (\$2>=$lat1 && \$2<=$lat2) {print \$3,-\$4,\$4}}' $tmp.txt | psxy -R -J  -Sc0.075i -C$tmp.cpt -W0.25p -O -K >> $tmp.ps\n";
		# depth scale
		print FILE "psscale -D16c/2c/-4c/0.3c -C$tmp.cpt -B10f10/:\"Depth (km)\": -O -K >> $tmp.ps\n";
		# depth vs time
		print FILE "cat $tmp.txt | sed s/\\ /T/g | awk -F \, {'print \$6,-\$4,\$4'} > $tmp.xyz\n";
		print FILE "R=`minmax -fT -I5 $tmp.xyz`\n";
		print FILE "psbasemap \$R -JX17c/4c -Bs1Y/WESn -Bpa3Of1o/a5f5g0 -P -Y-5c -U\"$stamp\" -O -K >> $tmp.ps\n";
		print FILE "psxy $tmp.xyz -R -J -Sc0.075i -C$tmp.cpt  -W0.25p -V -O >> $tmp.ps\n";
		
		# makes PNG from PS file
		print FILE "convert $tmp.ps $tmp.png\n";
	close (FILE); 
	# execute the script
	qx(cd $tmpdir ; sh $tmp.gmt);
	
	print "<p align=center><a href=\"$htmout/$tmp.ps\"><img src=\"$htmout/$tmp.png\"></a></p>";
	print "<style type='text/css'>
		#attente
		{ display: none;}
		</style>\n";
	print "Available outputs: <a href='$htmout/$tmp.ps'>PostScript image file</a>, ",
		"<a href='$htmout/$tmp.txt'>ASCII data file</a>, ",
		"<a href='$htmout/$tmp.gmt'>GMT script file</a><br>",
	#	"Tempdir = $tmpdir<br>",
		"Volcano ID#$vd_id = $vd_name, Lat/Lon = ",join(',',@vd_latlon),
		"<br>";

# --- Part B: FORM (when no argument)
} else {
	print "<html>
	<body bgcolor=\"white\">";

	# get the volcano list
	$sth = $dbh->prepare("SELECT vd_id,vd_name FROM vd");
	$sth->execute or die "SQL Error: $DBI::errstr\n";
	while (@res = $sth->fetchrow_array) {
		push(@volcanoes,"$res[1]|$res[0]");
	}

	print "<FORM name='form' action='$ENV{SCRIPT_NAME}' method='get'>",
		"<P align='center'>",
		"<B>Select volcano:</B> <select name='vd_id' size='1'";
	for (sort @volcanoes) {
		my @z = split(/\|/,$_);
		print "<option value=$z[1]>$z[0]</option>\n"
	}
	print "</select><br>\n",
		"Map width (km) = <input name='map_width' size=4 value='20'><br>\n",
		"Max. nb of events = <input name='qty' size=5 value='1000'><br>\n",
		"<input type='submit' value='Submit'></p></form>";
	
	print "</body>
	</html>";
}

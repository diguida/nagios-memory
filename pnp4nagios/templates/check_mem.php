<?php
#initial values
$fmt = '%4.2lf';
$pct = '';
$upper = "";
$divis = 1;
$unit = "B";
$label = $unit;
if($UNIT[1] != "") {
    $unit = $UNIT[1];
    $label = $unit;
    if($UNIT[1] == "%%") {
        $label = '%';
        $fmt = '%5.1lf';
        $pct = '%';
    }
}

if($MAX[1] != "") {
    $max = pnp::adjust_unit($MAX[1].$unit, 1024, $fmt);
    $upper = "-u $max[1] ";
    $label = $max[2];
    $divis = $max[3];
}

if(WARN[1] != "") {
    $warn = pnp::adjust_unit(WARN[1].$unit, 1024, $fmt);
}

if(CRIT[1] != "") {
    $crit = pnp::adjust_unit(CRIT[1].$unit, 1024, $fmt);
}

$ds_name[1] = "Memory used by applications ($NAGIOS_AUTH_SERVICEDESC)";
# set graph labels
$opt[1] = "-l 0 $upper --vertical-label $label --title \"Memory used by applications running in $hostname / $servicedesc\" ";
# graph definition
$def[1]  = rrd::def("var1", $RRDFILE[1], $DS[1], "AVERAGE"); #used
#normalize graph values
$def[1] .= rrd::cdef("v_n", "var1,$divis,/");
$def[1] .= rrd::alerter("v_n", rrd::cut(ucfirst($NAME[1]), 15), $warn[1], $crit[1], "FF", $label.$pct,"#00FF00","#FFFF00","#FF0000");
$def[1] .= rrd::gprint("v_n", array("LAST", "AVERAGE", "MIN", "MAX"), "$fmt $label$pct");

if($MAX[1] != "") {
    $def[1] .= rrd::hrule($max[1], "#003300", "Size of RAM  $max[0] \\n");
}

if ($WARN[1] != "") {
    $def[1] .= rrd::hrule($warn[1], "#FFFF00", "Warning   ".$warn[0]"\\n");
}
if ($CRIT[1] != "") {
    $def[1] .= rrd::hrule($crit[1], "#FF0000", "Critical  ".$crit[0]."\\n");
}


$ds_name[2] = "Memory usage ($NAGIOS_AUTH_SERVICEDESC)"; 
$opt[2] = "-T 55 -l 0 --vertical-label \"$UNIT[1]\" --title \"Memory usage for $hostname / $servicedesc\" ";

$def[2]  = rrd::def("var1", $RRDFILE[1], $DS[1], "AVERAGE"); #used
$def[2] .= rrd::def("var2", $RRDFILE[2], $DS[2], "AVERAGE"); #cached
$def[2] .= rrd::def("var3", $RRDFILE[3], $DS[3], "AVERAGE"); #buffers
$def[2] .= rrd::def("var4", $RRDFILE[4], $DS[4], "AVERAGE"); #free

$def[2] .= rrd::area("var1", "#850707", rrd::cut(ucfirst($NAME[1]), 15)) ;
$def[2] .= rrd::gprint("var1", array("LAST", "AVERAGE", "MIN", "MAX"), "%4.2lf %s\\t");
$def[2] .= rrd::area("var2", "#FFDB87", rrd::cut(ucfirst($NAME[2]), 15), 'STACK') ;
$def[2] .= rrd::gprint("var2", array("LAST", "AVERAGE", "MIN", "MAX"), "%4.2lf %s\\t");
$def[2] .= rrd::area("var3", "#25345C", rrd::cut(ucfirst($NAME[3]), 15), 'STACK') ;
$def[2] .= rrd::gprint("var3", array("LAST", "AVERAGE", "MIN", "MAX"), "%4.2lf %s\\t");
$def[2] .= rrd::area("var4", "#88008A", rrd::cut(ucfirst($NAME[4]), 15), 'STACK') ;
$def[2] .= rrd::gprint("var4", array("LAST", "AVERAGE", "MIN", "MAX"), "%4.2lf %s\\t");

?>

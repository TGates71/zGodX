<?php
//
// pr0be 1.0
// by Sebastian Batko 
// bug reports and feature requests send to m0bi@m0bi.net
// Check for new version and other info at http://m0bi.net/products/pr0be-cpu-and-ram-monitor/
// 
// This program comes with absolutely no warranty and under no license. 
// 
// TGates: php7 compatability fix line 54 (11/30/2023)

function get_server_load() {

    if (stristr(PHP_OS, 'win')) {
        $wmi = new COM("Winmgmts://");
        $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
        $cpu_num = 0;
        $load_total = 0;
        foreach($server as $cpu){
            $cpu_num++;
            $load_total += $cpu->loadpercentage;
        }
        $load = round($load_total/$cpu_num);
    } else {
        $sys_load = sys_getloadavg();
        $load = $sys_load[0];
    }
    return (int) $load;
}

function get_memory() {
    if (stristr(PHP_OS, 'win')) {
        $wmi = new COM("Winmgmts://");
        $memt = $wmi->execquery("Select TotalPhysicalMemory from Win32_ComputerSystem");
        $memtotal = $memt->ItemIndex(0);
        $mt = $memtotal->TotalPhysicalMemory / 1024;
        //echo "<pre>total = $mt\n";
        $memf = $wmi->execquery("select FreePhysicalMemory from Win32_OperatingSystem");
        $memfree = $memf->ItemIndex(0);
        $mf = $memfree->FreePhysicalMemory;
        //echo "free = $mf\n";
        //return 100 - round($mf / $mt * 100);
        return round(100 * (($mt - $mf) / $mt));
    } else {
        $mem = file_get_contents('/proc/meminfo');
        if ($mem=='') {
            $mem = shell_exec("cat /proc/meminfo");
        }
        $mem = explode("\n", $mem);
        foreach($mem as $ri)
            $m[strtok($ri, ':')] = strtok('');
		# tg - Added (int) before $m vars to prevent: PHP Notice:  A non well formed numeric value encountered
		# 	 - makes the vars integers only (removes ' kB' from the vars)
        return 100 - round(((int)$m['MemFree'] + (int)$m['Buffers'] + (int)$m['Cached']) / (int)$m['MemTotal'] * 100);
    }
}

//Checks to see if it's been passed a time value to check CPU usage.
if(isset ($_GET['cpu'])){
    //Sets time value.
    $speed = $_GET['cpu'];
    if (stristr(PHP_OS, 'win')) {
        $wmi = new COM("Winmgmts://");
        $server = $wmi->execquery("SELECT LoadPercentage FROM Win32_Processor");
        $cpu_num = 0;
        $load_total = 0;
        foreach($server as $cpu){
            $cpu_num++;
            $load_total += $cpu->loadpercentage;
        }
        usleep($speed * 1000000);
        echo round($load_total/$cpu_num);
    } else {
        $prevVal = file_get_contents ('/proc/stat');
        if ($prevVal=="") {
            $prevVal = shell_exec("cat /proc/stat");
        }
        $prevArr = explode(' ',trim($prevVal));
        //Gets some values from the array and stores them.
        $prevTotal = $prevArr[2] + $prevArr[3] + $prevArr[4] + $prevArr[5];
        $prevIdle = $prevArr[5];
        //Wait a period of time until taking the readings again to compare with previous readings.
        usleep($speed * 1000000);
        //Does the same as before.
        $val = file_get_contents ('/proc/stat');
        if ($val=="") {
            $val = shell_exec("cat /proc/stat");
        }
        $arr = explode(' ',trim($val));
        //Same as before.
        $total = $arr[2] + $arr[3] + $arr[4] + $arr[5];
        $idle = $arr[5];
        //Does some calculations now to work out what percentage of time the CPU has been in use over the given time period.
        $intervalTotal = intval($total - $prevTotal);
        //Does a few more calculations and outputs total CPU usage as an integer.
        echo intval(100 * (($intervalTotal - ($idle - $prevIdle)) / $intervalTotal));
    }
}else{
    //Gets the amount of free memory on the host system and outputs as an integer.
    /*$output = shell_exec("free | grep Mem | awk '{print $3/$2 * 100.0}'");
    echo intval($output);*/
    echo get_memory();
}
?>

<?php

$file = file_get_contents('dump.ecu');


if (!$file) {
die('echo you have to run me7info.exe -n yourbin.bin, and then rename it to DUMP.ECU, in the same folder of this file');
}
// FINDING OFFSETS!!!
// FINDING OFFSETS!!!
// FINDING OFFSETS!!!
// FINDING OFFSETS!!!
// FINDING OFFSETS!!!
// FINDING OFFSETS!!!

echo "finding tsrldyn...\r\n";

$tsrldyn = substr( strstr($file,'tsrldyn') , 56, 6 );

if ($tsrldyn) 
echo "found: $tsrldyn\r\n";
else
die("fatal error not found tsrlydn");


echo "finding vfil_w...\r\n";

$vfil_w = substr( strstr($file,'vfil_w') , 56, 6 );

if ($vfil_w) 
echo "found: $vfil_w\r\n";
else
die("fatal error not found vfil_w");


echo "finding nmot_w...\r\n";

$nmot_w = substr( strstr($file,'nmot_w') , 56, 6 );

if ($nmot_w) 
echo "found: $nmot_w\r\n";
else
die("fatal error not found nmot_w");



echo "finding wped...\r\n";

$wped = substr( strstr($file,"\r\nwped") , 58, 6 );

if ($wped) 
echo "found: $wped\r\n";
else
die("fatal error not found wped");


echo "finding B_kuppl (clutch pedal)...\r\n";

$kuppl = substr( strstr($file,"\r\nB_kuppl") , 58, 6 );
$kupplmask = getmask (substr( strstr($file,"\r\nB_kuppl") , 73, 4 ) );



if ($kuppl) 
echo "found: $kuppl.$kupplmask \r\n";
else
die("fatal error not found kuppl");

echo "finding b_br (brems), brake pedal...\r\n";
$brems = substr( strstr($file,"\r\nB_br") , 58, 6 );
$bremsmask = getmask (substr( strstr($file,"\r\nB_br") , 73, 4 ) );
if ($brems)
echo "found: $brems.$bremsmask \r\n";
else
die("fatal error not found brems");





// SET START ADDRESS FOR BASE:

$bin = file_get_contents("ecu.bin");

if (!$bin) {
die('i cant find any ecu to read or write, put in same folder with name ECU.BIN');
}



$search=0;
while ($search=strpos($bin,"\x05\x40\x54\x83\x84\xB8\xC0\xC0\xC0\x80\x80",$search+1)) {
if ($search!=0)
$pos=$search+11;
}


echo "FTOMN found: " . dechex($pos);
echo "\r\nFTOMN IS: ";
echo raw2hex($bin[$pos]);


echo "\r\nFTOMN CHANGED TO 0\r\n";
$bin[$pos] = "\x00";

echo "Finding a good space for code cave..\r\n";

$codecave = strpos($bin,"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",479232)+8;


echo "space located at: 0x" . dechex($codecave);

echo "\r\nFinding a good space for launch control configuration variables..\r\n";

$launchvars = strpos($bin,"\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF\xFF",97700)+17;


echo "space located at: 0x" . dechex($launchvars);

echo "\r\nFinding the offset for call to the code cave..\r\n";

$search=0;
while ($search=strpos($bin,"\xD7\x40\x06\x02\x03\xF8",$search+1)) {
if ($search!=0)
$jump=$search-4;
}

echo "call will be located at: 0x" . dechex($jump);



$firstbyte = substr(dechex($codecave+hexdec(800000)),0,2);
$secondbyte = substr(dechex($codecave+hexdec(800000)),4,2);
$thirdbyte = substr(dechex($codecave+hexdec(800000)),2,2);
//echo "$firstbyte, $secondbyte, $thirdbyte";

//we save 2 bytes we will use at the end for go back.

$jumpback = $bin[$jump+2];
$jumpback2 = $bin[$jump+3];

if ($bin[$jump] == "\xDA") {
die('this file have already a code cave here, so we think that it have already the launch control, (or an attempt of it), please try using a original ecu!');
}



$bin[$jump] = "\xDA";
$bin[$jump+1] = hex2raw($firstbyte);
$bin[$jump+2] = hex2raw($secondbyte);
$bin[$jump+3] = hex2raw($thirdbyte);

//echo raw2hex($bin[$jump+2]);

// SET DEFAULT CONFIG FOR LAUNCH CONTROL:

$reeplace = array("\xA6","\x01","\x50","\x46","\x0A","\x00","\xF0","\x55","\xE6");

for($i=0;$i<9;$i++) {
$bin[$launchvars+$i] = $reeplace[$i];
}




$line1 = array("\x9A","\x2B","\x13","\x80","\xF2","\xF4","\x40","\x8E","\xD7","\x00","\x81","\x00","\xF2","\xF9","\x00","\x7E");


//START THE FUN PART!!!

//LINE 1
//LINE 1
//LINE 1
//LINE 1
//LINE 1
//LINE 1
//LINE 1
//LINE 1


echo "\r\n\r\nWriting lines of code\r\n";


//calculate a shit byte calc for kuppl

$byte = substr($kuppl,4,2);
$first = substr($byte,0,1);
$second = substr($byte,1,1);
$firstt = floor(hexdec($first)/2);
$calc = dechex(hexdec($byte)-(hexdec($firstt)*hexdec(2)*hexdec(10)));
$secondd = dechex(hexdec($calc)/2);
$hardcalc=$firstt.$secondd;


//calculate a shit byte calc for brems

$byte = substr($brems,4,2);
$first = substr($byte,0,1);
$second = substr($byte,1,1);
$firstt = floor(hexdec($first)/2);
$calc = dechex(hexdec($byte)-(hexdec($firstt)*hexdec(2)*hexdec(10)));
$secondd = dechex(hexdec($calc)/2);
$hardcalc2=$firstt.$secondd;


$counter = $codecave;
$bin[$counter] = "\x9A"; $counter++;
$bin[$counter] = hex2raw($firstt.$secondd); $counter++;
$bin[$counter] = "\x13"; $counter++;
$bin[$counter] = hex2raw("$kupplmask"."0"); $counter++;
$bin[$counter] = "\xF2"; $counter++;
$bin[$counter] = "\xF4"; $counter++;
bitwiseandsum($vfil_w);
$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x81"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xF2"; $counter++;
$bin[$counter] = "\xF9"; $counter++;
bitwise($launchvars,$bin,$counter);

//LINE 2
//LINE 2
//LINE 2
//LINE 2
//LINE 2

$bin[$counter] = "\x40"; $counter++;
$bin[$counter] = "\x49"; $counter++;
$bin[$counter] = "\x9D"; $counter++;
$bin[$counter] = "\x0B"; $counter++;
$bin[$counter] = "\xF2"; $counter++;
$bin[$counter] = "\xF4"; $counter++;
bitwisehexdec($nmot_w);
$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x81"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xF2"; $counter++;
$bin[$counter] = "\xF9"; $counter++;
bitwise($launchvars+2,$bin,$counter);

//LINE 3


$bin[$counter] = "\x40"; $counter++;
$bin[$counter] = "\x49"; $counter++;
$bin[$counter] = "\xFD"; $counter++;
$bin[$counter] = "\x03"; $counter++;
$bin[$counter] = "\xF7"; $counter++;
$bin[$counter] = "\x8E"; $counter++;
bitwiseandsum($tsrldyn);
$bin[$counter] = "\x0D"; $counter++;
$bin[$counter] = "\x2F"; $counter++;
$bin[$counter] = "\x9A"; $counter++;
$bin[$counter] = hex2raw($hardcalc); $counter++;
$bin[$counter] = "\x29"; $counter++;
$bin[$counter] = hex2raw(dechex($kupplmask)."0"); $counter++;
$bin[$counter] = "\x8A"; $counter++;
$bin[$counter] = hex2raw($hardcalc2); $counter++;

//LINE 4


$bin[$counter] = "\x22"; $counter++;
$bin[$counter] = hex2raw(dechex($bremsmask)."0"); $counter++;
$bin[$counter] = "\xF2"; $counter++;
$bin[$counter] = "\xF4"; $counter++;
bitwisehexdec($nmot_w);
$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x81"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xF2"; $counter++;
$bin[$counter] = "\xF9"; $counter++;
bitwise($launchvars+6,$bin,$counter);
$bin[$counter] = "\x40"; $counter++;
$bin[$counter] = "\x49"; $counter++;


//LINE 5

$bin[$counter] = "\xFD"; $counter++;
$bin[$counter] = "\x1A"; $counter++;
$bin[$counter] = "\xC2"; $counter++;
$bin[$counter] = "\xF4"; $counter++;
bitwiseandsum($wped);
$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x81"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xC2"; $counter++;
$bin[$counter] = "\xF9"; $counter++;
bitwise($launchvars+8,$bin,$counter);
$bin[$counter] = "\x40"; $counter++;
$bin[$counter] = "\x49"; $counter++;

//LINE 6..

$bin[$counter] = "\xFD"; $counter++;
$bin[$counter] = "\x12"; $counter++;
$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x38"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xF2"; $counter++;
$bin[$counter] = "\xF4"; $counter++;

$bin[$counter] = "\xF0"; $counter++;
$bin[$counter] = "\x4F"; $counter++;

$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x81"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xF2"; $counter++;
$bin[$counter] = "\xF9"; $counter++;

//LINE 7

bitwise($launchvars+4,$bin,$counter);
$bin[$counter] = "\x40"; $counter++;
$bin[$counter] = "\x49"; $counter++;
$bin[$counter] = "\x9D"; $counter++;
$bin[$counter] = "\x11"; $counter++;
$bin[$counter] = "\xF7"; $counter++;
$bin[$counter] = "\x8E"; $counter++;
bitwiseandsum($tsrldyn);
$bin[$counter] = "\x08"; $counter++;
$bin[$counter] = "\x41"; $counter++;
$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x38"; $counter++;

//LINE 8

$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xF7"; $counter++;
$bin[$counter] = "\xF8"; $counter++;

$bin[$counter] = "\xF0"; $counter++;
$bin[$counter] = "\x4F"; $counter++;

$bin[$counter] = "\x0D"; $counter++;
$bin[$counter] = "\x09"; $counter++;
$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x38"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xF6"; $counter++;
$bin[$counter] = "\x8F"; $counter++;

$bin[$counter] = "\xF0"; $counter++;
$bin[$counter] = "\x4F"; $counter++;

$bin[$counter] = "\x0D"; $counter++;
$bin[$counter] = "\x04"; $counter++;


//LINE 9

$bin[$counter] = "\xD7"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\x38"; $counter++;
$bin[$counter] = "\x00"; $counter++;
$bin[$counter] = "\xF6"; $counter++;
$bin[$counter] = "\x8E"; $counter++;

$bin[$counter] = "\xF0"; $counter++;
$bin[$counter] = "\x4F"; $counter++;

$bin[$counter] = "\xF3"; $counter++;
$bin[$counter] = "\xF8"; $counter++;
$bin[$counter] = $jumpback; $counter++;
$bin[$counter] = $jumpback2; $counter++;
$bin[$counter] = "\xDB"; $counter++;
$bin[$counter] = "\x00"; $counter++;


echo "\r\ncode writed successfully!!\r\n\r\nREMEMBER TO MAKE CHECKSUMS BEFORE YOU PUT THIS FILE, CHECKSUMS ARE NOT CALCULATED ON THIS FILE";


















file_put_contents("ecumod.bin",$bin);



die();


function getmask($mask) {
 if ($mask ==1) return 0;
 if ($mask ==2) return 1;
 if ($mask ==4) return 2;
 if ($mask ==8)  return 3;
 if ($mask ==10) return 4;
 if ($mask ==20) return 5;
 if ($mask ==40) return 6;
 if ($mask ==80) return 7;
 if ($mask ==100) return 8;
 if ($mask ==200) return 9;
 if ($mask ==400) return 10;
 if ($mask ==800) return 11;
 if ($mask ==1000) return 12;
 if ($mask ==2000) return 13;
 if ($mask ==4000) return 14;
 if ($mask ==8000) return 15;
}

function bitwiseandsum($value) {
global $bin,$counter;
$firstbyte = substr(dechex(hexdec($value)+hexdec(8000)),4,2);
$secondbyte = substr(dechex(hexdec($value)+hexdec(8000)),2,2);

$bin[$counter] = hex2raw($firstbyte);
$counter++;
$bin[$counter] = hex2raw($secondbyte);
$counter++;
return;

}


function bitwise($value,$bin,$offset) {
global $bin,$counter;
$firstbyte = substr(dechex($value),-2);
$secondbyte = substr(dechex($value),-4,-2);
$bin[$counter] = hex2raw($firstbyte);
$counter++;
$bin[$counter] = hex2raw($secondbyte);
$counter++;
return;

}

function bitwisehexdec($value) {
global $bin,$counter;
$firstbyte = substr(dechex(hexdec($value)),-2);
$secondbyte = substr(dechex(hexdec($value)),-4,-2);

$bin[$counter] = hex2raw($firstbyte);
$counter++;
$bin[$counter] = hex2raw($secondbyte);
$counter++;
return;

}

function raw2hex($raw) {
  $m = unpack('H*', $raw);
  return $m[1];
}

function hex2raw($hex) { 
  return pack('H*', $hex);
}

?>
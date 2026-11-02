if ((document.getElementById && 
document.firstChild &&
typeof document.getElementsByTagName != "undefined") &&
window.addEventListener || window.attachEvent){
(function(){

//Alter clock colours here:
fCol='#000000'; //face/number colour.
dCol='#1783c6'; //dot colour.
hCol='#000000'; //hours colour.
mCol='#000000'; //minutes colour.
sCol='#ff0000'; //seconds colour.
cCol='#000000'; //date colour.
aCol='#999999'; //am-pm colour.
bCol='#FFFFFF'; //select/form background colour.
tCol='#000000'; //select/form text colour.

//Add places here - make sure format is correct!

var locations = [
["Local time","0"],
["Afghanistan","4.30"],
["Algeria","1"],
["Argentina","-3"],
["Australia - Adelaide","9.30"],
["Australia - Perth","8"],
["Australia - Sydney","10"],
["Austria","1"],
["Bahrain","3"],
["Bangladesh","6"],
["Belgium","1"],
["Bolivia","-4"],
["Brazil - Andes","-5"],
["Brazil - East","-3"],
["Brazil - West","-4"],
["Bulgaria","2"],
["Burma (Myanmar)","6.30"],
["Chile","-5"],
["Canada - Calgary","-7"],
["Canada - Newfoundland","-3.30"],
["Canada - Nova Scotia","-4"],
["Canada - Toronto","-5"],
["Canada - Vancouver","-8"],
["Canada - Winnipeg","-6"],
["China - Mainland","8"],
["China - Taiwan","8"],
["Colombia","-5"],
["Cuba","-5"],
["Denmark","1"],
["Ecuador","-5"],
["Egypt","2"],
["Fiji","12"],
["Finland","2"],
["France","1"],
["Germany","1"],
["Ghana","0"],
["Greece","2"],
["Greenland","-3"],
["Hungary","1"],
["India","5.30"],
["Indonesia - Bali, Borneo","8"],
["Indonesia - Irian Jaya","9"],
["Indonesia - Sumatra, Java","7"],
["Iran","3.30"],
["Iraq","3"],
["Israel","2"],
["Italy","1"],
["Jamaica","-5"],
["Japan","9"],
["Kenya","3"],
["Korea (North & South)","9"],
["Kuwait","3"],
["Libya","1"],
["Malaysia","8"],
["Maldives","5"],
["Mali","1"],
["Mauritius","4"],
["Mexico","-6"],
["Morocco","0"],
["Nepal","5.45"],
["Netherlands","1"],
["New Zealand","12"],
["Nigeria","1"],
["Norway","1"],
["Oman","4"],
["Pakistan","5"],
["Peru","-5"],
["Philippines","8"],
["Poland","1"],
["Portugal","1"],
["Qatar","3"],
["Romania","2"],
["Russia - Kamchatka","11"],
["Russia - Moscow","3"],
["Russia - Vladivostok","9"],
["Seychelles","4"],
["Saudi Arabia","3"],
["Singapore","8"],
["South Africa","2"],
["Spain","1"],
["Syria","3"],
["Sri Lanka","5.30"],
["Sweden","1"],
["Switzerland","1"],
["Thailand","7"],
["Tonga","12"],
["Turkey","2"],
["Ukraine","3"],
["Uzbekistan","5"],
["Vietnam","7"],
["UAE","4"],
["UK","0"],
["USA - Alaska","-9"],
["USA - Arizona","-9"],
["USA - Central","-6"],
["USA - Eastern","-5"],
["USA - Hawaii","-10"],
["USA - Indiana East","-5"],
["USA - Mountain","-7"],
["USA - Pacific","-8"],
["Yemen","3"],
["Yugoslavia","1"],
["Zambia","2"],
["Zimbabwe","2"]];


//Alter nothing below! Alignments will be lost!

var d = document;
var idx = d.getElementsByTagName('div').length;
var ids = d.getElementsByTagName('select').length;

var y = 87;
var x = 60;
var h = 4;
var m = 5;
var s = 5;
var cf = [];
var cd = [];
var ch = [];
var cm = [];
var cs = [];
var face = "3 4 5 6 7 8 9 10 11 12 1 2";
face = face.split(" ");
var n = face.length;
var e = 360/n;
var hDims = 30/4;
var zone = 0;
var isItLocal = true;
var ampm = "";
var daysInMonth = 31;
var todaysDate = "";
var oddMinutes;
var getOddMinutes;
var addOddMinutes;
var plusMinus = false;
var mon=new Array("January","February","March","April","May","June",
"July","August","September","October","November","December");

d.write("<div class = 'theContainer'>" );

d.write('<div id = "theDate'+(idx)+'" class = "datestyle"   style = "color:'+bCol+'">\!<\/div>');
d.write('<div id = "theDateampm'+(idx)+'" class = "ampmstyle" style = "color:'+bCol+'">\!<\/div>');


d.write("<div id = 'theCities"+idx+"' class = 'citystyle'>"
+"<p style = 'margin:0px;'>" 
+"<select id = 'city"+ids+"' class = 'selectstyle'>");

for (var i = 0; i < locations.length; i++){
 d.write("<option value = "+locations[i][1]+">"+locations[i][0]+"<\/option>");
}
d.write("<\/select><\/p><\/div>");




d.write("<\/div>");



function init(){

for (var i = 0; i < h; i++){
 }

for (var i = 0; i < m; i++){
 }

for (var i = 0; i < s; i++){
 }




//alert (x+" "+y);
d.getElementById("theDate"+idx).style.top = 5+"px";
d.getElementById("theDate"+idx).style.left = 11+"px";
d.getElementById("theDateampm"+idx).style.top = 15+"px";
d.getElementById("theDateampm"+idx).style.left = 11+"px";

d.getElementById("theCities"+idx).style.top = 47+"px";
d.getElementById("theCities"+idx).style.left = 7+"px";

d.getElementById("city"+ids).style.backgroundColor = bCol;
d.getElementById("city"+ids).style.color = tCol;

ClockAndAssign();
}


function lcl(){
var c = document.getElementById("city"+ids);
zone = c.options[c.selectedIndex].value;
isItLocal = (c.options[0].selected);
plusMinus = (zone.charAt(0) == "-");
oddMinutes = (zone.indexOf(".") != -1);
if (oddMinutes){
 getOddMinutes = zone.substring(zone.indexOf(".")+1,zone.length);
 }

addHours=(oddMinutes)?parseInt(zone.substring(0,zone.indexOf("."))):parseInt(zone)

if (plusMinus)
 addOddMinutes = (oddMinutes)?parseInt(-getOddMinutes):0;
else
 addOddMinutes = (oddMinutes)?parseInt(getOddMinutes):0;
}


function ClockAndAssign(){
var hourAdjust = 0;
var dayAdjust = 0;
var monthAdjust = 0;
var now = new Date();
//var ofst = now.getTimezoneOffset()/60;

var secs = now.getSeconds();
var secOffSet = secs - 15;
if (secs < 15){ 
 secOffSet = secs + 45;
 }
var sec = Math.PI * (secOffSet/30);

var mins=(isItLocal)?now.getMinutes():now.getUTCMinutes();
if (oddMinutes){ 
 mins = mins+addOddMinutes;
 }

var minOffSet = mins - 15;
if (mins < 15){ 
 minOffSet = mins + 45;
 }
var min = Math.PI * (minOffSet/30);

if (mins < 0){
 mins += 60;
 hourAdjust = -1;
 }

if (mins > 59){
 mins -= 60;
 hourAdjust = 1;
 }

hr = (isItLocal)?now.getHours()+hourAdjust:now.getUTCHours()+addHours+hourAdjust
hrs = Math.PI * (hr-3)/6 + Math.PI * (parseInt(mins)) / 360;

if (hr < 0){
 hr += 24;
 dayAdjust = -1;
 }

if (hr > 23){
 hr -= 24;
 dayAdjust = 1;
 }


day = now.getDate() + dayAdjust;
if (day < 1){
 day += daysInMonth; 
 monthAdjust = -1;
 }

if (day > daysInMonth){
 day -= daysInMonth; 
 monthAdjust = 1;
 }

month = parseInt(now.getMonth() + 1 + monthAdjust);
if (month == 2){
 daysInMonth = 28;
 }

year = now.getYear();
if (year < 2000){
 year = year + 1900;
 }

leapYear = (year%4 == 0);
if (leapYear&&month == 2){
 daysInMonth = 29;
 }

if (month < 1){
 month += 12;
 year--;
 }

if (month > 12){
 month -= 12;
 year++;
 }

if(hr<10){
todaysDate = " 0" + hr + ":" + mins; 
}
else if (mins<10){
todaysDate = " " + hr + ":" +"0"+mins;
}
else if (mins<10  && hr<10){
todaysDate = " 0" + hr + ":" +"0"+mins;
}
else {
todaysDate = " " + hr + ":" +mins;
}
//+ ":" + secs + "  " ;
ampm = (hr > 11)?"PM":"AM";




d.getElementById("theDate"+idx).firstChild.data = todaysDate;
d.getElementById("theDateampm"+idx).firstChild.data = ampm;
//d.getElementById("Time"+idx).firstChild.data = d.getElementById("theDate"+idx).firstChild.data + d.getElementById("theDateampm"+idx).firstChild.data;
setTimeout(ClockAndAssign,100);
}


if (window.addEventListener){
 window.addEventListener("load",init,false);
 d.getElementById("city"+ids).addEventListener("change",lcl,false);
 }  
else if (window.attachEvent){
 window.attachEvent("onload",init);
 d.getElementById("city"+ids).attachEvent("onchange",lcl);
 } 

})();
}

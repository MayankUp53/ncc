window.onload = function()  { 
//alert("ding"); 
//display("non_edit_per_info");
//display("non_edit_contact_info");
//setTimeout ( "disp_contact_info()", 2000 );
//setTimeout ("display(\"non_edit_contact_info\")", 900 );
setTimeout ( "display(\"case_info\")", 1000 );
document.getElementById('case_info').style.display="none";
//document.getElementById('account_info').style.display="none";
//setTimeout ("edit_field()", 5000 );
} 
var xmlHttp;
var xmlHttp2;

function display(div_id){ 


	xmlHttp=GetXmlHttpObject();
        if (xmlHttp==null) {
                 alert ("Browser does not support HTTP Request");
                 return;
         }

	var url_params = document.getElementById('url_params').value; 
//	var url="disp_info.php?"; 
	url="history.php?";
	url=url+ url_params + "&div_id=" + div_id; 
	url=url+"&sid="+Math.random(); 
	//alert(url+url_params); 
		//alert(one);
		xmlHttp.open("GET",url,true);
		xmlHttp.onreadystatechange =one;
		xmlHttp.send(null);
} 

function one() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete") 
	{ 	
		//alert("I m in one"); 
		document.getElementById("case_info").innerHTML=xmlHttp.responseText; 
		//alert("Displayed"); 
	} 
} 

function GetXmlHttpObject() 
{ 
	var localxmlHttp=null; 
	try 
	{
		// Firefox, Opera 8.0+, Safari 
		localxmlHttp=new XMLHttpRequest(); 
 	} 
	catch (e) 
	{ 
		//Internet Explorer 
	try 
	{
		 localxmlHttp=new ActiveXObject("Msxml2.XMLHTTP"); 
        } 
       catch (e) 
         { 
           localxmlHttp=new ActiveXObject("Microsoft.XMLHTTP"); 
             } 
              } 
              return localxmlHttp; 
 } 

function savedata(){
var urlparams = document.getElementById('url_params').value;
//alert(urlparams);
var  postdt="";
		//var spanGroupPersonalDisp = document.getElementsByName("span_group_personal_disp");
		//      alert(spanGroupPersonalDisp[1].id);
		var spanGroupPersonalDisp = document.getElementsByTagName("span");
		var num_element=spanGroupPersonalDisp.length;
                for(i=1;i<=num_element;i++){

		        //alert('span_edit_'+i);        
			try {
		        // document.getElementById('input_display_'+i).value;
			nm=document.getElementById('input_display_'+i).name;
			postdt=postdt+document.getElementById('input_display_'+i).name+"="+document.getElementById('input_display_'+i).value+"&";
		//	alert(nm);
			}
                        catch(e){}
                }
//alert(postdt);

//return;
var postdata;
postdata=postdt+urlparams;
//alert(postdata);
var url="savedata.php";
//alert(postdata);
//postdata="Action=Chat&Call=ping&Sender="+user+"&TimeStamp="+ClientTimeStamp;
 new Ajax.Request(url,{
                method:'post',

                timeoutDelay:20,  // timeout in second

                onTimeout:function(){ },

                postBody:postdata,

                onCreate:function(){},

                onSuccess:function(transport){ alert("Data Saved"); } ,

                onFailure:function(ajaxRequest){alert("failed"); },

                onException:function(ajaxRequest,ex){alert(ajaxRequest.responseText+" "+ex); },

                onComplete:function(transport){ }
                        });
}



function checkRadio(val){
	if(val==0){
		document.getElementById('local').checked=true;
	 }	
	else if(val==1){
		 document.getElementById('customer').checked=true;
	}
	else if(val==2){
		 document.getElementById('after').checked=true;
        }

}

function setattribute(thisval){
//alert(thisval);
var localtime=document.getElementById('cblocal').value;
var customertime=document.getElementById('cbcustomer').value;
var afterday=document.getElementById('date').value;
var afterhr=document.getElementById('hour').value;
var aftermin=document.getElementById('minute').value;


var aftertime ;
aftertime = afterday+"-00-0000 "+afterhr+":"+aftermin; 

if(thisval=='local'){
document.getElementById('dispositionAttr').value=thisval+"-"+localtime+":00";
}
else if(thisval=='customer'){
document.getElementById('dispositionAttr').value=thisval+"-"+customertime+":00";
}
else if(thisval=='after'){
document.getElementById('dispositionAttr').value=thisval+"-"+aftertime+":00";
}
}


function showbasic_info(){
//alert("HI");
document.getElementById('non_edit_per_info').style.display="block";
document.getElementById('non_edit_contact_info').style.display="block";
document.getElementById('non_edit_address_info').style.display="block";
document.getElementById('case_info').style.display="none";
document.getElementById('account_info').style.display="none";
}

function showcase_info(){
//return true;
//display("case_info");
//alert("Hi");
document.getElementById('non_edit_per_info').style.display="none";
document.getElementById('non_edit_contact_info').style.display="none";
document.getElementById('non_edit_address_info').style.display="none";
document.getElementById('case_info').style.display="block";
document.getElementById('account_info').style.display="none";
}
function showaccount_info(){
document.getElementById('non_edit_per_info').style.display="none";
document.getElementById('non_edit_contact_info').style.display="none";
document.getElementById('non_edit_address_info').style.display="none";
document.getElementById('case_info').style.display="none";
document.getElementById('account_info').style.display="block";
}
function edit_field(){
var spanGroupPersonalDisp = document.getElementsByTagName("span");
//	alert(spanGroupPersonalDisp);
var num_element=spanGroupPersonalDisp.length;
	if(document.getElementById('span_display_1').style.display=="none"){
		savedata();
		document.getElementById('editcont_div').innerHTML="Edit";
		for(i=1;i<=num_element;i++){

	//alert('span_edit_'+i);	
                
		try{
		document.getElementById('span_edit_'+i).style.display="none";
		document.getElementById('span_display_'+i).style.display="inline";
		document.getElementById('span_display_'+i).innerHTML=document.getElementById('input_display_'+i).value;
		}
		catch(e){}
		}
	}
	else {  
		document.getElementById('editcont_div').innerHTML="Save";
		for(i=1;i<=num_element;i++){
		try{
		document.getElementById('span_display_'+i).style.display="none";
                document.getElementById('span_edit_'+i).style.display="inline";
                }
                catch(e){}
		}
	}	

}

function showdiv(val){
//alert(val);
	if(val=="basic_info"){
		showbasic_info();
	}
	else if (val=="case_info"){
		showcase_info();
		}

	else if (val=="account_info"){
		showaccount_info();
		}
}
/*function addOption() {
  var select = document.getElementById("dynamic-select");
  var new_option = document.getElementById("new").value;
  select.options[select.options.length] = new Option(new_option, new_option);
}

function removeOption() {

  var select = document.getElementById("dynamic-select");
  if (select.selectedIndex > 3)
    select.remove(select.selectedIndex);

}

function removeAllOptions() {

  var select = document.getElementById("dynamic-select");
  select.options.length = 0;
}*/

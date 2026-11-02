function PagePerformance() {
    var x = new Date();
	this.startTimeJS = x.getTime();
	this.endTimeJS = 0;
	this.totalRenderingTime = 0;
	this.directory = '';
	
	
	
	
	
	this.debugEnd = function() {
		var y = new Date();
		this.endTimeJS = y.getTime();
		this.totalRenderingTime = this.endTimeJS - this.startTimeJS;
		var xml = xmlObject();
		var url = this.directory + '/browseRenderTimeLog.php?renderStartTime=' + this.startTimeJS + '&renderEndTime=' + this.endTimeJS + '&uniqueId=' + uniqueId;
		xml.open('GET', url, true);
		xml.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xml.send();
		
		if(showOnScreen) {
			var pageTimingDiv = document.createElement('div');
			pageTimingDiv.id="pageTimingInfo";
			pageTimingDiv.style.position = 'fixed';
			pageTimingDiv.innerHTML = 'PHP Execution Time : <span id="phpExecutionTime"></span>&nbsp;&nbsp;Browser Render Time : <span id="jsExecutionTime"></span>';
			pageTimingDiv.style.margin = 0;
			pageTimingDiv.style.whiteSpace = 'nowrap';

			pageTimingDiv.style.backgroundColor = '#DDDDDD';
			pageTimingDiv.style.top = 0;
			pageTimingDiv.style.right = 0;
			pageTimingDiv.style.zIndex = 10000;
			//pageTimingDiv.style.opacity = '0.8';
			document.body.appendChild(pageTimingDiv);
			document.getElementById('phpExecutionTime').innerHTML = phpExeTime + ' ms';
			document.getElementById('jsExecutionTime').innerHTML = this.totalRenderingTime + ' ms';
		}
		
	};
}




//FUNCTION FOR CREATING XMLHTTP OBJECT
function xmlObject() {
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	return xmlhttp;
}




if(typeof(EventSource) != "undefined"){
	var source = new EventSource("scripts/siteStatus.php");
	source.onmessage = function(event){
		var data = event.data;
		var status = parseInt(data);
		if(status == 1)	window.location = "/";
	};
}
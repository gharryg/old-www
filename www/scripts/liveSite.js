if(typeof(EventSource) != "undefined"){
	var siteStatus = new EventSource("/scripts/siteStatus.php");
    siteStatus.onmessage = function(event){
		var data = event.data;
		var status = parseInt(data);
		if(status == 0)	window.location = "/sitedown";
	};
    var banStatus = new EventSource("/scripts/banStatus.php");
    banStatus.onmessage = function(event){
        var data = event.data;
        var isBanned = parseInt(data);
        if(isBanned == 1)   window.location = "/banned";
    };
}
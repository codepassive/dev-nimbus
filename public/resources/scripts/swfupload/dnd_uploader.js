$.upload5=function(input, opt){
	var ME=this;
	ME.option = {
		gate:'',
		can_proceed:true,
		
		dragEnterColor:'#ffc',
		dragExitColor:'#fff',
		
		beforeLoad:function() {},
		onProgress:function(event) {},
		onComplete:function(event) {}
	};

	if(typeof(opt)=='string'){
		opt={'gate':opt};
	}
	
	ME.option = $.extend(this.option,opt);

	
	ME.upload = function (event) {
		
		event.stopPropagation();
       
        
		ME.option.beforeLoad();
		if(!ME.option.can_proceed){
			return false;
		}
		
		var data = event.dataTransfer;
		
        /*
        for (var i = 0; i < data.files.length; i++) {
            $('#image_list').prepend($('<img src="img/spinner.gif" width="16" height="16" />').css("padding", "33px"));
        }
        */
        
        var boundary = '------multipartformboundary' + (new Date).getTime();
        var dashdash = '--';
        var crlf     = '\r\n';

        /* Build RFC2388 string. */
        var builder = '';

        builder += dashdash;
        builder += boundary;
        builder += crlf;
        
        var xhr = new XMLHttpRequest()
        
        for (var i = 0; i < data.files.length; i++) {
            var file = data.files[i];

            /* Generate headers. */            
            builder += 'Content-Disposition: form-data; name="file[]"';
            if (file.fileName) {
              builder += '; filename="' + file.fileName + '"';
            }
            builder += crlf;

            builder += 'Content-Type: application/octet-stream';
            builder += crlf;
            builder += crlf; 

            /* Append binary data. */
            builder += file.getAsBinary();
            builder += crlf;

            /* Write boundary. */
            builder += dashdash;
            builder += boundary;
            builder += crlf;
        }
        
        /* Mark end of the request. */
        builder += dashdash;
        builder += boundary;
        builder += dashdash;
        builder += crlf;
        
		xhr.upload.addEventListener("progress", ME.option.onProgress, false);
			
        xhr.open("POST", ME.option.gate, true);
        xhr.setRequestHeader('content-type', 'multipart/form-data; boundary=' + boundary);


        
        try{
        	xhr.sendAsBinary(builder);        
        }
        catch(err){
        	Note.set('error',t("Upload failed - use only latin-encoded filenames"),13);
        	return false;
        }
        
        xhr.onload = function(event){
        	ME.option.onComplete(event,xhr.responseText);
        };
    }
			
	$(input).get(0).addEventListener('drop', ME.upload, false);
	
    $(input).get(0).addEventListener('dragenter',	function(event) { 
    	$(input).css("background-color", ME.option.dragEnterColor); 
    	event.stopPropagation();
    	event.preventDefault();
    }, false);
    
    $(input).get(0).addEventListener('dragover',	function(event) { 
    	$(input).css("background-color", ME.option.dragEnterColor); 
    	event.stopPropagation();
    	event.preventDefault();
    }, false);
    
    $(input).get(0).addEventListener('dragexit', 	function(event) { $(input).css("background-color", ME.option.dragExitColor); }, false);
      
	return ME;
}

$.fn.upload5 = function upload5(options){
	this.each(function() {
		var input = this;
		new jQuery.upload5(input, options);
	});
	
	return this;
};

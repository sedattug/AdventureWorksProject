if (xajax)
{
	xajax.initFileInputs = function ()
	{
		inputs = document.getElementsByTagName('input');
		for( var i=0; i < inputs.length; i++)
		{
			inp=inputs[i];
			if (!inp.className)
				continue; //doesnt have a class defined
			if (inp.className.indexOf('xajax_file')==-1)
				continue; //not an xajax file upload
			if (inp.style.visibility=='hidden')
				continue; //already converted this file upload
			xajax.newFileUpload(inp.id, inp.id+'-'+xajax.workId)
				inp.style.visibility = 'hidden';
			inp.style.height = '0';
			inp.style.width = '0';
		} 
	}
	xajax.newFileUpload = function(sParentId, sId)
	{
		xajax.insertAfter(sParentId, 'iframe', sId);
		newFrame = xajax.$(sId);
		newFrame.name=sId;
		newFrame.style.height="35px"
		newFrame.style.height="35";
		newFrame.style.width="300";
		newFrame.style.overflow="hidden";
		newFrame.position="relative";
		newFrame.scrolling="no";
		newFrame.allowtransparency=true;
		newFrame.style.backgroundColor="transparent";
		//need to wait for Mozilla to notice there's an iframe
		setTimeout('xajax._fileUploadContinue("'+sId+'");', 20);
	}
	xajax._fileUploadContinue = function(sId)
	{
		//uploadIframe = window.frames[sId];
		uploadIframe = xajax.$(sId);
		if (!uploadIframe.contentDocument)
		{
			//fix for internet explorer
			uploadIframe.contentDocument = window.frames[sId].document;
		}
		uploadIframe.contentDocument.body.style.backgroundColor="transparent";
		uploadIframe.contentDocument.xajax=this;
		uploadIframe.contentDocument.body.innerHTML='<span id="workId" style="font-size:0px;height: 0px;position:absolute;">'+xajax.workId+'</span><form style="position:absolute;top:0;left:0;height:98%;width:98%;margin:0;padding:0;overflow:hidden;" name="iform" action="'+xajaxRequestUri+'" method="post" enctype="multipart/form-data"><input id="file" type="file" name="file" onchange="document.xajax._fileUploading(\''+sId+'\');document.iform.submit();" onmouseout="if(this.value)document.iform.submit();"/><input type="hidden" name="xajax" value="file_upload" /></form>';
		uploadIframe.style.border='0';
	}
	xajax._fileUploading = function(sId)
	{
		uploadIframe = xajax.$(sId);
		xajax.insertAfter(sId, 'div', sId+'-progress');
		uploadProgress = xajax.$(sId+'-progress');
		uploadIframe.style.visibility='hidden';
		uploadIframe.style.width='0';
		uploadIframe.style.height='0';
		uploadProgress.innerHTML='Uploading...';
		uploadProgress.style.fontSize="25";
		setTimeout('xajax._fileProgressCheck("'+sId+'");', 100);
	}
	xajax._fileProgressCheck = function(sId)
	{
		uploadIframe = xajax.$(sId);
		uploadProgress = xajax.$(sId+'-progress');
		if (uploadIframe.contentDocument.body.innerHTML.indexOf('</xjx>') !== -1)
		{
			//this isn't a proper detection, but we'll work on it later
			uploadProgress.innerHTML='Upload Finished';
		} else {
			setTimeout('xajax._fileProgressCheck("'+sId+'");', 100);
		}
	}
}

try{if(undefined==xajax.ext)
xajax.ext={};}
catch(e){}
try{if(undefined==xajax.ext.comet)
xajax.ext.comet={};}
catch(e){alert("Could not create xajax.ext.comet namespace");}
xjxEc=xajax.ext.comet;xjxEc.detectSupport=function(){var agt=navigator.userAgent.toLowerCase();if(agt.indexOf("opera")!=-1)
return 'Opera';if(agt.indexOf("staroffice")!=-1)
return 'Star Office';if(agt.indexOf("webtv")!=-1)
return 'WebTV';if(agt.indexOf("beonex")!=-1)
return 'Beonex';if(agt.indexOf("chimera")!=-1)
return 'Chimera';if(agt.indexOf("netpositive")!=-1)
return 'NetPositive';if(agt.indexOf("phoenix")!=-1)
return 'Phoenix';if(agt.indexOf("firefox")!=-1)
return 'Firefox';if(agt.indexOf("safari")!=-1)
return 'Safari';if(agt.indexOf("skipstone")!=-1)
return 'SkipStone';if(agt.indexOf("msie")!=-1)
return 'Internet Explorer';if(agt.indexOf("netscape")!=-1)
return 'Netscape';if(agt.indexOf("mozilla/5.0")!=-1)
return 'Mozilla';if(agt.indexOf('\/')!=-1){if(agt.substr(0,agt.indexOf('\/'))!='mozilla'){return navigator.userAgent.substr(0,agt.indexOf('\/'));}
else
return 'Netscape';}
else if(agt.indexOf(' ')!=-1)
return navigator.userAgent.substr(0,agt.indexOf(' '));else
return navigator.userAgent;return false;}
xjxEc.prepareRequestXHR=function(oRequest){if(true==oRequest.comet){var xx=xajax;var xt=xx.tools;oRequest.request=xt.getRequestObject();oRequest.setRequestHeaders=function(headers){if('object'==typeof headers){for(var optionName in headers)
this.request.setRequestHeader(optionName,headers[optionName]);}
}
oRequest.setCommonRequestHeaders=function(){this.setRequestHeaders(this.commonHeaders);}
oRequest.setPostRequestHeaders=function(){this.setRequestHeaders(this.postHeaders);}
oRequest.setGetRequestHeaders=function(){this.setRequestHeaders(this.getHeaders);}
oRequest.applyRequestHeaders=function(){}
oRequest.setCommonRequestHeaders=function(){this.request.setRequestHeader('If-Modified-Since','Sat, 1 Jan 2000 00:00:00 GMT');this.request.setRequestHeader('streaming','xhr');if(typeof(oRequest.header)=="object"){for(a in oRequest.header)
this.request.setRequestHeader(a,oRequest.header[a]);}
}
oRequest.comet={};oRequest.comet.LastPosition=0;oRequest.comet.inProgress=false;var pollLatestResponse=function(){console.log('pollLatestResponse');xjxEc.responseProcessor.XHR(oRequest);}
oRequest.pollTimer=setInterval(pollLatestResponse,300);oRequest.request.onreadystatechange=function(){if(oRequest.request.readyState < 3){console.log('readyState < 3');return;}
if(oRequest.request.readyState==4){console.log('readyState === 4');clearInterval(oRequest.pollTimer);xjxEc.responseProcessor.XHR(oRequest);xajax.completeResponse(oRequest);return;}
}
oRequest.finishRequest=function(){return this.returnValue;}
if('undefined'!=typeof oRequest.userName&&'undefined'!=typeof oRequest.password){oRequest.open=function(){this.request.open(this.method,this.requestURI,true,oRequest.userName,oRequest.password);}
}
else{oRequest.open=function(){this.request.open(this.method,this.requestURI,true);}
}
if('POST'==oRequest.method){oRequest.applyRequestHeaders=function(){this.setCommonRequestHeaders();try{this.setPostRequestHeaders();}
catch(e){this.method='GET';this.requestURI+=this.requestURI.indexOf('?')==-1 ? '?':'&';this.requestURI+=this.requestData;this.requestData='';if(0==this.requestRetry)
this.requestRetry=1;throw e;}
}
}
else{oRequest.applyRequestHeaders=function(){this.setCommonRequestHeaders();this.setGetRequestHeaders();}
}
return;}
return xjxEc.prepareRequest(oRequest);}
xjxEc.connect_htmlfile=function(url,callback,oRequest){try{xjxEc.transferDoc=new ActiveXObject("htmlfile");xjxEc.transferDoc.open();xjxEc.transferDoc.write("<html>");xjxEc.transferDoc.write("<script>document.domain='http://192.168.1.21/';</script>");xjxEc.transferDoc.write("</html>");xjxEc.transferDoc.close();xjxEc.ifrDiv=xjxEc.transferDoc.createElement("div");xjxEc.transferDoc.body.appendChild(xjxEc.ifrDiv);xjxEc.ifrDiv.innerHTML="<iframe src='"+url+"'></iframe>";xjxEc.transferDoc.callback=function(response){callback(response,oRequest);};}
catch(ex){}
}
xjxEc.prepareRequestActiveX=function(oRequest){if(true==oRequest.comet){var xx=xajax;var xt=xx.tools;oRequest.requestURI+=oRequest.requestURI.indexOf('?')==-1 ? '?':'&';oRequest.requestURI+=oRequest.requestData;oRequest.requestData='';try{xjxEc.connect_htmlfile(oRequest.requestURI,xjxEc.responseProcessor.ActiveX,oRequest);if(0 < oRequest.requestRetry)
oRequest.requestRetry=0;}
catch(ex){}
return;}
return xjxEc.prepareRequest(oRequest);}
xjxEc.prepareRequestHTMLDRAFT=function(oRequest){if(true==oRequest.comet){var xx=xajax;var xt=xx.tools;oRequest.requestURI+=oRequest.requestURI.indexOf('?')==-1 ? '?':'&';oRequest.requestURI+=oRequest.requestData;oRequest.requestURI+="&xjxstreaming=HTML5DRAFT";oRequest.inProgress=false;try{var uri=oRequest.requestURI;var es=document.createElement("event-source");es.setAttribute("src",uri);es.setAttribute("width",200);es.setAttribute("height",200);es.style.display="block";callback=function(event){xjxEc.responseProcessor.HTMLDRAFT(event.data,oRequest);};remove=function(){es.removeEventListener("xjxstream",callback,false);es.removeEventListener("xjxendstream",remove,false);}
es.addEventListener("xjxstream",callback,false);es.addEventListener("xjxendstream",remove,false);document.body.appendChild(es);if(0 < oRequest.requestRetry)
oRequest.requestRetry=0;}
catch(ex){}
return;}
return xjxEc.prepareRequest(oRequest);}
xajax.debug={};xajax.debug.prepareDebugText=function(text){try{text=text.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br />');return text;}
catch(e){xajax.debug.stringReplace=function(haystack,needle,newNeedle){var segments=haystack.split(needle);haystack='';for(var i=0;i < segments.length;++i){if(0!=i)
haystack+=newNeedle;haystack+=segments[i];}
return haystack;}
xajax.debug.prepareDebugText=function(text){text=xajax.debug.stringReplace(text,'&','&amp;');text=xajax.debug.stringReplace(text,'<','&lt;');text=xajax.debug.stringReplace(text,'>','&gt;');text=xajax.debug.stringReplace(text,'\n','<br />');return text;}
xajax.debug.prepareDebugText(text);}
}
xjxEc.responseProcessor={}
xjxEc.responseProcessor.XHR=function(oRequest){var xx=xajax;var xt=xx.tools;var xcb=xx.callback;var gcb=xcb.global;var lcb=oRequest.callback;var oRet=oRequest.returnValue;if(""==oRequest.request.responseText)
return;var allMessages=oRequest.request.responseText;if(true===oRequest.comet.inProgress)return;oRequest.comet.inProgress=true;do{var unprocessed=allMessages.substring(oRequest.comet.LastPosition);var messageXMLEndIndex=unprocessed.indexOf("</xjx>");if(messageXMLEndIndex!=-1){var endOfFirstMessageIndex=messageXMLEndIndex+("</xjx>").length;var anUpdate=unprocessed.substring(0,endOfFirstMessageIndex);var cmd=(new DOMParser()).parseFromString(anUpdate,"text/xml");try{var seq=0;var child=cmd.documentElement.firstChild;xt.xml.processFragment(child,seq,oRet,oRequest);}
catch(ex){}
xt.queue.process(xx.response);oRequest.comet.LastPosition+=endOfFirstMessageIndex;}
}while(messageXMLEndIndex!=-1);oRequest.comet.inProgress=false;return oRet;}
xjxEc.responseProcessor.ActiveX=function(response,oRequest){response.replace('\"','"');var xx=xajax;var xt=xx.tools;var xcb=xx.callback;var gcb=xcb.global;var lcb=oRequest.callback;var oRet=oRequest.returnValue;if(response){var cmd=(new DOMParser()).parseFromString(response,"text/xml");var seq=0;var child=cmd.documentElement.firstChild;xt.xml.processFragment(child,seq,oRequest);if(null==xx.response.timeout)
xt.queue.process(xx.response);}
return oRet;}
xjxEc.responseProcessor.HTMLDRAFT=function(response,oRequest){var xx=xajax;var xt=xx.tools;var xcb=xx.callback;var gcb=xcb.global;var lcb=oRequest.callback;var oRet=oRequest.returnValue;if(oRequest.inProgress)return;if(response&&oRequest.lastResponse!==response){oRequest.inProgress=true;var cmd=(new DOMParser()).parseFromString(response,"text/xml");var seq=0;var child=cmd.documentElement.firstChild;xt.xml.processFragment(child,seq,oRequest);if(null==xx.response.timeout)
xt.queue.process(xx.response);oRequest.inProgress=false;oRequest.lastResponse=response
}
return oRet;}
xjxEc.submitRequestActiveX=function(oRequest){if(true==oRequest.comet)
return;xjxEc.submitRequest(oRequest);}
xjxEc.prepareRequest=xajax.prepareRequest;xjxEc.stream_support=xjxEc.detectSupport();switch(xjxEc.stream_support){case "Internet Explorer":
xajax.prepareRequest=xjxEc.prepareRequestActiveX;xjxEc.submitRequest=xajax.submitRequest;xajax.submitRequest=xjxEc.submitRequestActiveX;break;case "Firefox":
case "Safari":
xajax.prepareRequest=xjxEc.prepareRequestXHR;break;case "Opera":
xajax.prepareRequest=xjxEc.prepareRequestHTMLDRAFT;xjxEc.submitRequest=xajax.submitRequest;xajax.submitRequest=xjxEc.submitRequestActiveX;break;default:
alert("Xajax.Ext.Comet: Your browser does not support comet streaming or is not yet supported by this plugin!");}
if(typeof DOMParser=="undefined"){DOMParser=function(){}
DOMParser.prototype.parseFromString=function(str,contentType){if(typeof ActiveXObject!="undefined"){var d=new ActiveXObject("Microsoft.XMLDOM");d.loadXML(str);return d;}
else if(typeof XMLHttpRequest!="undefined"){var req=new XMLHttpRequest;req.open("GET","data:"+(contentType||"application/xml")+";charset=utf-8,"+encodeURIComponent(str),false);if(req.overrideMimeType){req.overrideMimeType(contentType);}
req.send(null);return req.responseXML;}
}
}

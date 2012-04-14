  var http_request = false;
  function makeRequest(url, parameters, d_id) {
     //alert(parameters);
     div_id = d_id;
     var full_uri = "";
     http_request = false;
     if (window.XMLHttpRequest) { // Mozilla, Safari,...
        http_request = new XMLHttpRequest();
        if (http_request.overrideMimeType) {
            // set type accordingly to anticipated content type
            //
           //http_request.overrideMimeType('text/xml');
           http_request.overrideMimeType('Cache-Control: no-cache');
           http_request.overrideMimeType('text/html');
        }
     } else if (window.ActiveXObject) { // IE
        try {
           http_request = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
           try {
              http_request = new ActiveXObject("Microsoft.XMLHTTP");
           } catch (e) {}
        }
     }
     if (!http_request) {
        alert('Cannot create XMLHTTP instance');
        return false;
     }
     //full_uri = url + parameters
     http_request.open('GET', url + parameters, true);
     //http_request.setRequestHeader('Content-Type', 'text/html');
     //http_request.setRequestHeader('Content-Type', ' charset=windows-1251');
     //alert(http_request.readyState);
     //http_request.overrideMimeType('Cache-Control: no-cache'); 
     http_request.setRequestHeader("Cache-Control","no-store, no-cache, must-revalidate");
     http_request.setRequestHeader("Pragma", "no-cache"); 
     http_request.onreadystatechange = function() { alertContents(http_request, parameters); }; 
     //http_request.onreadystatechange = alertContents;
     http_request.send(null);
     return http_request;
  } // end of function makeRequest()
  
  function alertContents(http_request, parameters) {
     //alert(http_request.readyState);
     
     if (http_request.readyState == 1 || http_request.readyState==0 || http_request.readyState==2 || http_request.readyState==3 )
     {
      //alert("ttt "+div_id);
      //alert(http_request.readyState);
      document.getElementById(div_id).innerHTML = '<div style="padding-top:10px; text-align:center;" align="center"><img src="/admin/images/icons/loading_animation_liferay.gif"></div>';     
     }
     if (http_request.readyState == 4) {
        if (http_request.status == 200) {
           //alert(http_request.responseText);
           result = http_request.responseText;
           document.getElementById(div_id).innerHTML = result;            
        } else {
           alert('Возникла проблема с отправкой запроса.');
        }
     }
  } // end of function alertContents()  
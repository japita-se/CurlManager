# CurlManager

This simple class is a wrapper around curl. It can be used in any php project by requiring the class.
A typical usage is:

```
 $parameters= ["test1"=>1,"test2"=>2];
 $curlObj = new CurlManager();
 $curlObj->setHeaders(array("Content-Type: application/x-www-form-urlencoded"));
 //$curlObj->setVerbose(true);
 // Set the verb: can be POST, PUT,GET,DELETE
 $curlObj->setUp("POST", $url, http_build_query($parameters));
 if ($curlObj->send()) {
	// Do your stuff
 }


```

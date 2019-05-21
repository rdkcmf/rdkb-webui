#WebUI

This document provides an overview of the WebUI component of RDK-B.

##Overview


Web GUI code is developed with PHP. The WebUI functions are defined in C and called from PHP using ZEND_API. Zend is an API to make "C" function calls from PHP. PHP and the Zend Engine manage [exchange] variables internally. 

Unlike in user space code, the parameters for an internal function aren’t actually declared in the function header. Instead, a reference to the parameter list is passed into every function – whether parameters were passed or not – and that function can then ask the Zend Engine to turn them into something usable.

zend_parse_parameters() is the function to parse the input parameters from PHP.
ZEND_NUM_ARGS() provides a hint to the Zend Engine about the parameters which are to be retrieved.
TSRMLS_CC is present to ensure thread safety, and the return value of the function is checked for SUCCESS or FAILURE.
"s" is specified to indicate that this function expects one and only one parameter to be passed, and that that parameter should be converted into a string data type and populated into the char* variable.

For more details on Zend API refer [http://devzone.zend.com/](http://devzone.zend.com/)

There are three portions:

1. CcspPhpExtension
2. CcspPhpApi
3. Styles

###CcspPhpExtension

This is a PHP5 extension (written in C) that provides access to the underlying
CCSP Data Model via CCSP message bus. Following functions are provided in the PHP extensions that the PHP code can call:

1. DmExtGetStrsWithRootObj - to get value for an array of parameters
2. DmExtSetStrsWithRootObj - to set value for an array of parameters
3. DmExtGetInstanceIds - to retrieve instance IDs under an object table
4. addTblObj - to add an object to table
5. delTblObj - to delete an object from table
6. getStr - to get value for a parameter
7. setStr - to set value for a parameter
8. getInstanceIds - to retrieve instance IDs for a parameter

###CcspPhpApi

These are CCSP PHP APIs, which in turn calls PHP extension.

###Styles

This contains the actual Web GUI code and configuration files, including PHP files, theme related files, Java Scripts etc. "config" directory contains the following files:

1. lighttpd.conf is the configuration file for lighttpd webserver.
2. php.ini is the initialization file for PHP responsible for configuring PHP's behaviour.
3. webgui.sh is a script which configures and starts lighttpd.


##Copyright and license

If not stated otherwise in this file or this component's Licenses.txt file the following copyright and licenses apply:

Copyright 2016 RDK Management

Licensed under the Apache License, Version 2.0 (the " License" ); you may not use this file except in compliance with the License. You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS"  BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.

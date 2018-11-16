/*
 * If not stated otherwise in this file or this component's Licenses.txt file the
 * following copyright and licenses apply:
 *
 * Copyright 2015 RDK Management
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
*/

/**********************************************************************
   Copyright [2014] [Cisco Systems, Inc.]
 
   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at
 
       http://www.apache.org/licenses/LICENSE-2.0
 
   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
**********************************************************************/

#ifndef PHP_COSA_H
#define PHP_COSA_H

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <pthread.h>
#include <ccsp_message_bus.h>
#include <ccsp_base_api.h>
#include <sys/time.h>
#include <time.h>
#include <signal.h>
#include "ccsp_memory.h"
#include <ccsp_custom.h>

#ifdef ZTS
#include "TSRM.h"
#endif

#define PHP_COSA_VERSION "1.0"
#define PHP_COSA_EXTNAME "cosa"

extern zend_module_entry cosa_module_entry;
#define phpext_cosa_ptr &cosa_module_entry

PHP_MINIT_FUNCTION(cosa);
PHP_MSHUTDOWN_FUNCTION(cosa);
PHP_RINIT_FUNCTION(cosa);
PHP_RSHUTDOWN_FUNCTION(cosa);
PHP_MINFO_FUNCTION(cosa);
PHP_FUNCTION(getStr);
PHP_FUNCTION(setStr);
PHP_FUNCTION(getInstanceIds);
PHP_FUNCTION(addTblObj);
PHP_FUNCTION(delTblObj);
PHP_FUNCTION(getJWT);
/* multi-get/set APIs */
PHP_FUNCTION(DmExtGetStrsWithRootObj);
PHP_FUNCTION(DmExtSetStrsWithRootObj);
PHP_FUNCTION(DmExtGetInstanceIds);

/* Cosa specific stuff */
#define DST_COMPONENTID "ccsp.pnm"
#define DST_PATHNAME    "/com/cisco/spvtg/ccsp/pnm"
#define COMPONENT_NAME  "ccsp.phpextension"
//#define CONF_FILENAME   "msg_daemon.cfg"
#define CONF_FILENAME   "/tmp/ccsp_msg.cfg"

#ifndef  CCSP_COMPONENT_ID_WebUI
    #define  CCSP_COMPONENT_ID_WebUI                0x00000001
#endif

extern void _dbus_connection_lock (DBusConnection *connection);
extern void _dbus_connection_unlock (DBusConnection *connection);

/* Globals */
ZEND_BEGIN_MODULE_GLOBALS(cosa)
ZEND_END_MODULE_GLOBALS(cosa)

/*
In every function that needs to use globals, call TSRM_FETCH().
Then refer to the globals as COSA_G(var).
*/
#ifdef ZTS
#define COSA_G(v) TSRMG(cosa_globals_id, zend_cosa_globals *, v)
#else
#define COSA_G(v) (cosa_globals.v)
#endif

#endif  /* PHP_COSA_H */


/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: noet sw=4 ts=4 fdm=marker
 * vim<600: noet sw=4 ts=4
 */

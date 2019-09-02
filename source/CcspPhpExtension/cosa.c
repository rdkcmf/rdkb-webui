/**
* @file cosa.c
* @brief For CCSP Component:  WebUI
*
* @description This file gives the function prototypes used for the RDK-Broadband component WebUI
*/

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

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_cosa.h"

#ifdef COMCAST_SSO
#include "sso_api.h"
#endif

#if PHP_MAJOR_VERSION < 7
#define _RETURN_STRING(str) RETURN_STRING(str, 1)
#define _add_next_index_string(str1,str2) add_next_index_string(str1,str2,1)
#else
#define _add_next_index_string(str1,str2) add_next_index_string(str1,str2)
#define _RETURN_STRING(str) RETURN_STRING(str)
#endif

#define  COSA_PHP_EXT_LOG_FILE_NAME "/var/log/cosa_php_ext.log"
#define  COSA_PHP_EXT_DEBUG_FILE    "/tmp/cosa_php_debug"
#define  COSA_PHP_EXT_PCSIM         "/tmp/cosa_php_pcsim"
#define  CosaPhpExtLog(msg ...)                                             \
         {                                                                  \   
             if (debugFlag)                                                 \
             {                                                              \
                 FILE*              pFile       = NULL;                     \
                 mode_t             origMod     = umask(0);                 \
                 struct timeval     tv;                                     \
                 struct tm          tm;                                     \
                                                                            \
                 pFile = fopen(COSA_PHP_EXT_LOG_FILE_NAME, "a");            \
                                                                            \
                 if ( pFile )                                               \
                 {   /* print the current timestamp */                      \
                     gettimeofday(&tv, NULL);                               \
                     tm = *localtime(&tv.tv_sec);                           \
                     fprintf                                                \
                        (                                                   \
                            pFile,                                          \
                            "%04d-%02d-%02d %02d-%02d-%02d:%06d ",          \
                            tm.tm_year + 1900, tm.tm_mon + 1, tm.tm_mday,   \
                            tm.tm_hour, tm.tm_min, tm.tm_sec,               \
                            tv.tv_usec                                      \
                        );                                                  \
                                                                            \
                     fprintf(pFile, msg);                                   \
                     fclose(pFile);                                         \
                 }                                                          \
                                                                            \
                 umask(origMod);                                            \
             }                                                              \
         }


ZEND_DECLARE_MODULE_GLOBALS(cosa);

void * bus_handle           = NULL;
char * dst_componentid      =  NULL;
char * dst_pathname         =  NULL;
char   dst_pathname_cr[64]  =  {0};
static int debugFlag        = 0;
static int gPcSim           = 0;

/* Constant Globals */
static const char* msg_path = "/com/cisco/spvtg/ccsp/phpext" ;
static const char* msg_interface = "com.cisco.spvtg.ccsp.phpext" ;
static const char* msg_method = "__send" ;
//static const char* app_msg_method = "__app_request" ;
static const char* Introspect_msg = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n"
                                    "<node name=\"/com/cisco/ccsp/dbus\">\n"
                                    "  <interface name=\"org.freedesktop.DBus.Introspectable\">\n"
                                    "    <method name=\"Introspect\">\n"
                                    "      <arg name=\"data\" direction=\"out\" type=\"s\"/>\n"
                                    "    </method>\n"
                                    "  </interface>\n"
                                    "  <interface name=\"ccsp.msg\">\n"
                                    "    <method name=\"__send\">\n"
                                    "      <arg type=\"s\" name=\"from\" direction=\"in\" />\n"
                                    "      <arg type=\"s\" name=\"request\" direction=\"in\" />\n"
                                    "      <arg type=\"s\" name=\"response\" direction=\"out\" />\n"
                                    "    </method>\n"
                                    "    <method name=\"__app_request\">\n"
                                    "      <arg type=\"s\" name=\"from\" direction=\"in\" />\n"
                                    "      <arg type=\"s\" name=\"request\" direction=\"in\" />\n"
                                    "      <arg type=\"s\" name=\"argu\" direction=\"in\" />\n"
                                    "      <arg type=\"s\" name=\"response\" direction=\"out\" />\n"
                                    "    </method>\n"
                                    "  </interface>\n"
                                    "</node>\n"
                                    ;

DBusHandlerResult
path_message_func
    (
        DBusConnection*             conn,
        DBusMessage*                message,
        void*                       user_data
    )
{
    const char *interface = dbus_message_get_interface(message);
    const char *method   = dbus_message_get_member(message);
    DBusMessage *reply;
    //char tmp[4098];
    char *resp = "888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888888";
    char *from = 0;
    char *req = 0;
    char * err_msg  = DBUS_ERROR_NOT_SUPPORTED;

    reply = dbus_message_new_method_return (message);
    if (reply == NULL)
    {
        return DBUS_HANDLER_RESULT_HANDLED;
    }

    if(!strcmp("org.freedesktop.DBus.Introspectable", interface)  && !strcmp(method, "Introspect"))
    {
        if ( !dbus_message_append_args (reply, DBUS_TYPE_STRING, &Introspect_msg, DBUS_TYPE_INVALID))

        if (!dbus_connection_send (conn, reply, NULL))

        dbus_message_unref (reply);
        return DBUS_HANDLER_RESULT_HANDLED;
    }


    if (!strcmp(msg_interface, interface) && !strcmp(method, msg_method))
    {

        if(dbus_message_get_args (message,
                                NULL,
                                DBUS_TYPE_STRING, &from,
                                DBUS_TYPE_STRING, &req,
                                DBUS_TYPE_INVALID))
        {
            dbus_message_append_args (reply, DBUS_TYPE_STRING, &resp, DBUS_TYPE_INVALID);
            if (!dbus_connection_send (conn, reply, NULL))
                dbus_message_unref (reply);
        }

        return DBUS_HANDLER_RESULT_HANDLED;
    }
    dbus_message_set_error_name (reply, err_msg) ;
    dbus_connection_send (conn, reply, NULL);
    dbus_message_unref (reply);
    return DBUS_HANDLER_RESULT_HANDLED;
}


int UiDbusClientGetDestComponent(char* pObjName,char** ppDestComponentName, char** ppDestPath, char* pSystemPrefix)
{
    int                         ret;
    parameterInfoStruct_t **    parameter;
    parameterValStruct_t **     parameterVal;
    char *                      pFaultParameter;
    int                         size = 0;
    parameterAttributeStruct_t **   parameterAttr;
    componentStruct_t **        ppComponents = NULL;
    char                        errStr[512] = {0};
    FILE*                       fp = NULL;

    ret =
        CcspBaseIf_discComponentSupportingNamespace
            (
                bus_handle,
                dst_pathname_cr,
                pObjName,
                pSystemPrefix,
                &ppComponents,
                &size
            );

    if ( ret == CCSP_SUCCESS )
    {
        *ppDestComponentName = ppComponents[0]->componentName;
        ppComponents[0]->componentName = NULL;
        *ppDestPath    = ppComponents[0]->dbusPath;
        ppComponents[0]->dbusPath = NULL;

        while( size )
        {
            if (ppComponents[size-1]->remoteCR_dbus_path)
            {
                free(ppComponents[size-1]->remoteCR_dbus_path);
            }
            if (ppComponents[size-1]->remoteCR_name)
            {
                free(ppComponents[size-1]->remoteCR_name);
            }
            free(ppComponents[size-1]);
            size--;
        }
        return  0;
    }
    else
    {
        CosaPhpExtLog
            (
                "Failed to locate the component for %s%s%s, error code = %d!\n",
                pSystemPrefix,
                strlen(pSystemPrefix) ? "." : "",
                pObjName,
                ret
            );
        return  ret;
    }
}

void CheckAndSetSubsystemPrefix (char** ppDotStr, char* pSubSystemPrefix)
{
    if (!strncmp(*ppDotStr,"eRT",3))   //check whether str has prex of eRT
    {
        strncpy(pSubSystemPrefix,"eRT.",4);
        *ppDotStr +=4;              //shift four bytes to get rid of eRT:
    }
    else if (!strncmp(*ppDotStr,"eMG",3))  //check wither str has prex of eMG
    {
        strncpy(pSubSystemPrefix,"eMG.",4);
        *ppDotStr +=4;  //shit four bytes to get rid of eMG;
    }
}

/** {{{ cosa_functions[]
 *
 * Every user visible function must have an entry in cosa_functions[].
 */
static zend_function_entry cosa_functions[] = {
    PHP_FE(getStr, NULL)
    PHP_FE(setStr, NULL)
    PHP_FE(getInstanceIds, NULL)
    PHP_FE(addTblObj, NULL)
    PHP_FE(delTblObj, NULL)
    PHP_FE(DmExtGetStrsWithRootObj, NULL)
    PHP_FE(DmExtSetStrsWithRootObj, NULL)
    PHP_FE(DmExtGetInstanceIds, NULL)
    PHP_FE(getJWT, NULL)
    {NULL, NULL, NULL}
};
/* }}} */

/* {{{ cosa_module_entry
 */
zend_module_entry cosa_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
    STANDARD_MODULE_HEADER,
#endif
    PHP_COSA_EXTNAME,
    cosa_functions,
    PHP_MINIT(cosa),
    PHP_MSHUTDOWN(cosa),
    PHP_RINIT(cosa),
    PHP_RSHUTDOWN(cosa),
    PHP_MINFO(cosa),
#if ZEND_MODULE_API_NO >= 20010901
    PHP_COSA_VERSION,
#endif
    STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_COSA
ZEND_GET_MODULE(cosa)
#endif

static void php_cosa_init_globals(zend_cosa_globals *cosa_globals)
{
    FILE *fp = NULL;
    
    /* If file exists, we'll open the debug flag */
    fp = fopen(COSA_PHP_EXT_DEBUG_FILE, "r");
    if (fp)
    {
        debugFlag = 1;
        close(fp);
    }

    /* Check if this is a PC simulation */
    fp = fopen(COSA_PHP_EXT_PCSIM, "r");
    if (fp)
    {
        gPcSim = 1;
        close(fp);
    }
}

/* {{{ PHP_MINIT_FUNCTION
 */
PHP_MINIT_FUNCTION(cosa)
{
    ZEND_INIT_MODULE_GLOBALS(cosa, php_cosa_init_globals, NULL);
    
    bus_handle = NULL;
    /*
     *  Hardcoding "eRT." is just a workaround. We need to feed the subsystem
     *  info into this initialization routine.
     */
    if ( gPcSim )
    {
        sprintf(dst_pathname_cr, CCSP_DBUS_INTERFACE_CR);
    }
    else
    {
        sprintf(dst_pathname_cr, "eRT." CCSP_DBUS_INTERFACE_CR);
    }

    CosaPhpExtLog("COSA PHP extension starts -- PC sim = %d...\n", gPcSim);

    return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
 */
PHP_MSHUTDOWN_FUNCTION(cosa)
{
    CosaPhpExtLog("COSA PHP extension exits...\n");
    
    if (bus_handle) CCSP_Message_Bus_Exit(bus_handle); 
    return SUCCESS;
}
/* }}} */

/* {{{ PHP_RINIT_FUNCTION
 */
PHP_RINIT_FUNCTION(cosa)
{
    int                 iReturnStatus       = 0;

    if (!bus_handle)
    {
        CosaPhpExtLog("COSA PHP extension RINIT -- initialize dbus...\n");

        iReturnStatus = CCSP_Message_Bus_Init(COMPONENT_NAME, CONF_FILENAME, &bus_handle, 0,0);
        if ( iReturnStatus != 0 )
        {
            CosaPhpExtLog("Message bus init failed, error code = %d!\n", iReturnStatus);
        }
        
        CCSP_Msg_SleepInMilliSeconds(1000);
        CCSP_Message_Bus_Register_Path(bus_handle, msg_path, path_message_func, 0);
    }
    
    return SUCCESS;
}
/* }}} */

/* {{{ PHP_RSHUTDOWN_FUNCTION
 */
PHP_RSHUTDOWN_FUNCTION(cosa)
{
    /*CosaPhpExtLog("COSA PHP extension RSHUTDOWN...\n");*/
    
    return  SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
 */
PHP_MINFO_FUNCTION(cosa)
{
    int                     iIndex              = 0;
    char                    TmpBuffer[64]       = {0};

    CosaPhpExtLog("COSA PHP MINFO...\n");

    sprintf(TmpBuffer, "0x%X", bus_handle);

    php_info_print_table_start();
    php_info_print_table_row(2, "cosa support",     "enabled");
    php_info_print_table_row(2, "PC sim",           gPcSim ? "Yes" : "No");
    php_info_print_table_row(2, "debug enabled",    debugFlag ? "enabled" : "disabled");
    php_info_print_table_row(2, "debug flag file",  COSA_PHP_EXT_DEBUG_FILE);
    php_info_print_table_row(2, "debug output",     COSA_PHP_EXT_LOG_FILE_NAME);
    php_info_print_table_row(2, "dbus handle",      TmpBuffer);

    for ( iIndex = 0; iIndex < sizeof(cosa_functions)/sizeof(cosa_functions[0]); iIndex++ )
    {
        if ( cosa_functions[iIndex].fname )
        {
            sprintf(TmpBuffer, "Ext Function %d:", iIndex);
            php_info_print_table_row(2, TmpBuffer, cosa_functions[iIndex].fname);
        }
    }

    php_info_print_table_end();
}
/* }}} */

/* {{{ proto string getJWT(string arg)
   gets a string from the model */
PHP_FUNCTION(getJWT)
{
    char *pURI = NULL;
    char *pClientId = NULL;
    char *pParams = NULL;
    char *pFileName = NULL;
    int iRet = 0;
    int lenURI;
    int lenClientId;
    int lenParams;
    int lenFileName;

    do
    {
CosaPhpExtLog( "getJWT - Entry\n" );
#ifdef COMCAST_SSO
        if( zend_parse_parameters( ZEND_NUM_ARGS() TSRMLS_CC, "ssss",
            &pURI, &lenURI, &pClientId, &lenClientId, &pParams, &lenParams, &pFileName, &lenFileName ) == FAILURE )
        {
            CosaPhpExtLog( "getJWT - zend_parse_parameters failed!\n" );
                iRet = 1;
                break;
            }
CosaPhpExtLog( "getJWT - zend_parse_parameters success!\n" );
        if( pURI != NULL )
        {
            CosaPhpExtLog( "getJWT -pURI = %s\n", pURI );
            if( !strlen( pURI ) )
            {
                    iRet = 2;
                    break;
            }
        }
        else
        {
            CosaPhpExtLog( "getJWT -pURI is NULL\n" );
                iRet = 3;
                break;
        }
        if( pClientId != NULL )
        {
            CosaPhpExtLog( "getJWT -pClientId = %s\n", pClientId );
            if( !strlen( pClientId ) )
            {
                    iRet = 4;
                    break;
            }
        }
        else
        {
            CosaPhpExtLog( "getJWT -pClientId is NULL\n" );
                iRet = 5;
                break;
        }
        if( pParams != NULL )
        {
            CosaPhpExtLog( "getJWT - pParams = %s\n", pParams );
            if( !strlen( pParams ) )
            {
                    iRet = 6;
                    break;
            }
        }
        else
        {
            CosaPhpExtLog( "getJWT - pParams is NULL\n" );
            iRet = 7;
            break;
        }
        if( pFileName != NULL )
        {
            CosaPhpExtLog( "getJWT - pFileName = %s\n", pParams );
            if( !strlen( pParams ) )
            {
                iRet = 8;
                break;
            }
        }
        else
        {
            CosaPhpExtLog( "getJWT - pFileName is NULL\n" );
            iRet = 9;
            break;
        }
        CosaPhpExtLog( "getJWT - calling SSOgetJWT\n" );
        iRet = SSOgetJWT( pURI, pClientId, pParams, pFileName );
#else
        iRet = 10;    // return a unique error
#endif
        CosaPhpExtLog( "getJWT - iRet = %ld\n", iRet );
    } while( 0 );

    CosaPhpExtLog("getJWT - exit with value = %ld\n", iRet);
    RETURN_LONG(iRet);
}
/* }}} */

/* {{{ proto string getStr(string arg)
   gets a string from the model */
/**
* @description A PHP function which fetches the parameter value as a string from the ccsp data model. 
* proto string getStr(string arg)
*
* @param getStr - parameter for which value needs to be fetched
*
* @return The status of the operation as a string.
* @retval RETURN_STRING
*
*/
PHP_FUNCTION(getStr)
{
    int                     flen                = 0;
    char*                   dotstr              = 0;
    char*                   ppDestComponentName = NULL;
    char*                   ppDestPath          = NULL;
    int                     size                = 0;
    parameterValStruct_t ** parameterVal        = NULL;
    char                    retParamVal[1024]   = {0};
    int                     iReturn             = 0;
    int                     loop                = 0;
    char                    subSystemPrefix[6]  = {0};

    //Parse Input parameters first
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &dotstr, &flen) == FAILURE)
    {
        CosaPhpExtLog("getStr - zend_parse_parameters failed!\n");
        _RETURN_STRING("");
    }

    //check for NULL string
    if( !strlen(dotstr) )
    {
        _RETURN_STRING("");
    }
    else
    {
        CosaPhpExtLog("COSA PHP extension getStr -- dotstr %s\n", dotstr);
    }

    //check whether there are subsystem prefix in the dot string
    //Split Subsytem prefix and COSA dotstr if subsystem prefix is found
    CheckAndSetSubsystemPrefix(&dotstr,subSystemPrefix); 

    //Get Destination component 
    iReturn = UiDbusClientGetDestComponent(dotstr, &ppDestComponentName, &ppDestPath,subSystemPrefix);
    if ( iReturn != 0 )
    {
        _RETURN_STRING("");
    }
        
    //Get Parameter Vaues from ccsp
    iReturn = CcspBaseIf_getParameterValues(bus_handle,
                                            ppDestComponentName,
                                            ppDestPath,
                                            &dotstr,
                                            1,
                                            &size ,
                                            &parameterVal);

    if (CCSP_SUCCESS != iReturn)
    {
        CosaPhpExtLog("Failed on CcspBaseIf_getParameterValues %s, error code = %d.\n", dotstr, iReturn);
        //RETURN_STRING("ERROR: Failed on CcspBaseIf_getParameterValues",1);
        _RETURN_STRING("");
    }

    CosaPhpExtLog
        (
            "CcspBaseIf_getParameterValues: %s, error code %d, result %s!\n",
            dotstr,
            iReturn,
            parameterVal[0]->parameterValue
        );

    if ( size >= 1 )
    {
        strncpy(retParamVal,parameterVal[0]->parameterValue, sizeof(retParamVal));
        free_parameterValStruct_t(bus_handle, size, parameterVal);
    }

    //Return only first param value
    _RETURN_STRING(retParamVal);
}

/* {{{ proto string setStr(string arg)
   set a string in the model */
/**
* @description A PHP function which sets the parameter value for the ccsp parameter 
* proto string setStr(string arg)
*
* @param setStr - parameter for which values needs to be set
*
* @return The status of the operation.
* @retval RETURN_TRUE if successful.
* @retval RETURN_FALSE if any error is detected
*
*/
PHP_FUNCTION(setStr)
{
    int                           flen;
    char*                         dotstr                = NULL;
    int                           vlen;
    char*                         val                   = NULL;
    zend_bool                     bCommit               = 0;
    char*                         ppDestComponentName   = NULL;
    char*                         ppDestPath            = NULL;
    int                           size;
    parameterValStruct_t          **structGet           = NULL;
    parameterValStruct_t          structSet[1];
    char                          *paramNames[1];
    int                           iReturn;
    char*                         pFaultParameterNames  = NULL;
    char                          subSystemPrefix[6]    = {0};
    dbus_bool                     bDbusCommit           = 1;

    //Parse Parameters first
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "ssb", &dotstr, &flen, &val, &vlen, &bCommit) == FAILURE)
    {
        //RETURN_STRING("ERROR: SetStr: ARGUMENT MISSING",1);
        CosaPhpExtLog("setStr - zend_parse_parameters failed!\n");
        RETURN_FALSE;
    }
    //check for NULL string
    if( !strlen(dotstr))
    {
    #if _DEBUG_
        syslog(LOG_ERR, "%s: bad parameters", __FUNCTION__);
    #endif
        RETURN_FALSE;
    }
    else
    {
        bDbusCommit = (bCommit) ? 1 : 0;
    }

    //check whether there is subsystem prefix in the dot string
    //Split Subsytem prefix and COSA dotstr if subsystem prefix is found
    CheckAndSetSubsystemPrefix(&dotstr,subSystemPrefix); 

    //Get Destination component 
    iReturn = UiDbusClientGetDestComponent(dotstr, &ppDestComponentName, &ppDestPath,subSystemPrefix);

    if ( iReturn != 0 )
    {
        RETURN_FALSE;
    }

    //First Get the current parameter Vaues 
    iReturn =CcspBaseIf_getParameterValues(bus_handle,
                                            ppDestComponentName,
                                            ppDestPath,
                                            &dotstr,
                                            1,
                                            &size ,
                                            &structGet);//&parameterVal);

    if ( iReturn != CCSP_SUCCESS ) 
    {
        CosaPhpExtLog("Failed on CcspBaseIf_getParameterValues %s, error code = %d.\n", dotstr, iReturn);
        //RETURN_STRING("ERROR: Failed on CcspBaseIf_getParameterValues",1);
        RETURN_FALSE;   
    }

    CosaPhpExtLog
        (
            "setStr - CcspBaseIf_getParameterValues: %s, error code %d, result %s!\n",
            structGet[0]->parameterName,
            iReturn,
            structGet[0]->parameterValue
        );

    if (size != 1 || strcmp(structGet[0]->parameterName, dotstr) != 0)
    {
    #if _DEBUG_
        syslog(LOG_ERR, "%s: miss match", __FUNCTION__);
    #endif
        free_parameterValStruct_t(bus_handle, size, structGet);
        RETURN_FALSE;   
    }

    /*
     *  Remove the following as it has led to unexpected behavior - 
     *      bCommit = True without value change didn't take effect
     *
    //if the value is not changed, we don't need to save the setting
    if(0 == strcmp(structGet[0]->parameterValue, val))
    {
      RETURN_TRUE;
    }
     */

    //Its Dangerous to use strcpy() but we dont have any option
    //strcpy(parameterVal[0]->parameterValue,val);
    structSet[0].parameterName = (char *)dotstr;
    structSet[0].parameterValue = val;
    structSet[0].type = structGet[0]->type;
    iReturn = 
        CcspBaseIf_setParameterValues
            (
                bus_handle,
                ppDestComponentName,
                ppDestPath,
                0,
                CCSP_COMPONENT_ID_WebUI,
                structSet,
                1,
                bDbusCommit,
                &pFaultParameterNames
            );

    if (CCSP_SUCCESS != iReturn) 
    {
        CosaPhpExtLog
            (
                "CcspBaseIf_setParameterValues failed - %s:%s bCommit:%d, error code = %d, fault parameter = %s.\n",
                dotstr,
                structSet[0].parameterValue,
                bDbusCommit,
                iReturn,
                pFaultParameterNames ? pFaultParameterNames : "none"
            );
            
        if (pFaultParameterNames) free(pFaultParameterNames);

        free_parameterValStruct_t(bus_handle, size, structGet);
        RETURN_FALSE;
    }
    
    CosaPhpExtLog
        (
            "CcspBaseIf_setParameterValues - %s:%s bCommit:%d, error code = %d.\n",
            dotstr,
            structSet[0].parameterValue,
            bDbusCommit,
            iReturn
        );

    if(size >= 1)
    {
        free_parameterValStruct_t(bus_handle, size, structGet);
    }

    //RETURN_STRING("", 1);
    RETURN_TRUE;
}
/* }}} */

/* {{{ proto string getInstanceIds(string arg)
   get all instance ids (comma separated) corresponding to a resource string */
/**
* @description Function to get all instance ids (comma separated) corresponding to a resource string
* proto string getInstanceIds(string arg)
*
* @param getInstanceIds - string for which instanceId needs to be fetched 
*
* @return The status of the operation.
* @retval RETURN_STRING
* 
*/
PHP_FUNCTION(getInstanceIds)
{
    int                             flen;
    char*                           dotstr = NULL;
    char*                           ppDestComponentName = NULL;
    char*                           ppDestPath = NULL ;
    int                             ret;
    int                             iReturn;
    ULONG                           ulIndex;
    unsigned int                    InstNum         = 0;
    unsigned int*                   pInstNumList    = NULL;
    int                             loop1= 0,loop2=0;
    int                             len              = 0;
    char                            format_s[512] ={0};
    int                             size;
    parameterInfoStruct_t **        parameter;
    int                             inst_num = 0;
    char                            buf[CCSP_BASE_PARAM_LENGTH];
    char                            subSystemPrefix[6] = {0};

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &dotstr, &flen) == FAILURE) {
        //RETURN_STRING("ERROR: GetStr: ARGUMENT MISSING",1);
        _RETURN_STRING("");
    }

    //check for NULL string
    if( !strlen(dotstr))
        _RETURN_STRING("");

    //check whether there are subsystem prefix in the dot string
    //Split Subsytem prefix and COSA dotstr if subsystem prefix is found
    CheckAndSetSubsystemPrefix(&dotstr,subSystemPrefix); 

    //Get Destination component 
    iReturn = UiDbusClientGetDestComponent(dotstr, &ppDestComponentName, &ppDestPath,subSystemPrefix);

    if ( iReturn != 0 )
    {
        _RETURN_STRING("");
    }

    /*
     *  Get Next Instance Numbes
     */
    iReturn =
        CcspBaseIf_GetNextLevelInstances
            (
                bus_handle,
                ppDestComponentName,
                ppDestPath,
                dotstr,
                &InstNum,
                &pInstNumList
            );

    if (iReturn != CCSP_SUCCESS)
    {
        //AnscTraceWarning("Failed on CcspBaseIf_GetNextLevelInstances, error code = %d.\n", iReturn);
        //RETURN_STRING("ERROR: Failed on CcspBaseIf_GetNextLevelInstances",1);
        _RETURN_STRING("");
    }

    for(loop1=0,loop2=0; loop1<(InstNum); loop1++) 
    {
        len =sprintf(&format_s[loop2],"%d,", pInstNumList[loop1]);
        loop2=loop2+len;
    }

    if (pInstNumList)
    {
        free(pInstNumList);
    }

    //Place NULL char at the end of string
    format_s[loop2-1]=0;
    _RETURN_STRING(format_s);
}
/* }}} */
/* {{{ proto string addTblObj(string arg)
   add object to table resource string */
/**
* @description Function to add object to table resource string
* proto string addTblObj(string arg)
*
* @param addTblObj - object to be added to table
*
* @return The status of the operation.
* @retval RETURN_LONG
* 
*/
PHP_FUNCTION(addTblObj)
{
    int                             flen;
    char*                           dotstr = NULL;
    char*                           ppDestComponentName = NULL;
    char*                           ppDestPath = NULL ;
    int                             ret;
    int                             iReturn;
    ULONG                           ulIndex;
    unsigned int                    InstNum         = 0;
    unsigned int*                   pInstNumList    = NULL;
    int                             loop1= 0,loop2=0;
    int                             len              = 0;
    char                            format_s[512] ={0};
    int                             size;
    parameterInfoStruct_t **        parameter;
    int                             inst_num = 0;
    int                             iReturnInstNum = 0;
    char                            buf[CCSP_BASE_PARAM_LENGTH];
    char                            subSystemPrefix[6] = {0};

    if ( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &dotstr, &flen) == FAILURE )
    {
        CosaPhpExtLog("addTblObj - zend_parse_parameters failed!\n");
        iReturn = CCSP_FAILURE;
        RETURN_LONG(iReturn);
    }

    //check for NULL string
    if( !strlen(dotstr))
    {
        CosaPhpExtLog("addTblObj - zero length dotstr!\n");
        iReturn = CCSP_FAILURE;
        RETURN_LONG(iReturn);
    }
        
    //check whether there are subsystem prefix in the dot string
    //Split Subsytem prefix and COSA dotstr if subsystem prefix is found
    CheckAndSetSubsystemPrefix(&dotstr,subSystemPrefix); 

    //Get Destination component 
    iReturn = UiDbusClientGetDestComponent(dotstr, &ppDestComponentName, &ppDestPath,subSystemPrefix);

    if(iReturn != 0)
    {
        RETURN_LONG(iReturn);
    }

    iReturn =
        CcspBaseIf_AddTblRow
            (
                bus_handle,
                ppDestComponentName,
                ppDestPath,
                0,
                dotstr,
                &iReturnInstNum
            );
    
    if ( iReturn != CCSP_SUCCESS )
    {
        CosaPhpExtLog("addTblObj - CcspBaseIf_AddTblRow failed on %s, error code = %d!\n", dotstr, iReturn);
        RETURN_LONG(iReturn);
    }
    else
    {
        RETURN_LONG(iReturnInstNum);
    }
}
/* }}} */
/* {{{ proto string delTblObj(string arg)
   delete object to table resource string */
/**
* @description Function to delete object from table resource string
* proto string delTblObj(string arg)
*
* @param addTblObj - object to be deleted from table
*
* @return The status of the operation.
* @retval RETURN_LONG
* 
*/
PHP_FUNCTION(delTblObj)
{
    int                             flen;
    char*                           dotstr = NULL;
    char*                           ppDestComponentName = NULL;
    char*                           ppDestPath = NULL ;
    int                             ret;
    int                             iReturn;
    ULONG                           ulIndex;
    unsigned int                    InstNum         = 0;
    unsigned int*                   pInstNumList    = NULL;
    int                             loop1= 0,loop2=0;
    int                             len              = 0;
    char                            format_s[512] ={0};
    int                             size;
    parameterInfoStruct_t **        parameter;
    int                             inst_num = 0;
    char                            buf[CCSP_BASE_PARAM_LENGTH];
    char                            subSystemPrefix[6] = {0};

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &dotstr, &flen) == FAILURE)
    {
        CosaPhpExtLog("delTblObj - zend_parse_parameters failed!\n");
        iReturn = CCSP_FAILURE;
        RETURN_LONG(iReturn);
    }

    //check for NULL string
    if( !strlen(dotstr))
    {
        CosaPhpExtLog("delTblObj - zero length dotstr!\n");
        iReturn = CCSP_FAILURE;
        RETURN_LONG(iReturn);
    }

    //check whether there are subsystem prefix in the dot string
    //Split Subsytem prefix and COSA dotstr if subsystem prefix is found
    CheckAndSetSubsystemPrefix(&dotstr,subSystemPrefix); 

    //Get Destination component 
    iReturn = UiDbusClientGetDestComponent(dotstr, &ppDestComponentName, &ppDestPath,subSystemPrefix);

    if(iReturn != 0)
    {
        RETURN_LONG(iReturn);
    }

    iReturn =
        CcspBaseIf_DeleteTblRow
            (
                bus_handle,
                ppDestComponentName,
                ppDestPath,
                0,
                dotstr
            );
                    
    if ( iReturn != CCSP_SUCCESS )
    {
        CosaPhpExtLog("delTblObj - CcspBaseIf_DeleteTblRow failed on %s, error code = %d!\n", dotstr, iReturn);
    }
    else
    {
        iReturn = 0;
    }

    RETURN_LONG(iReturn);
}
/* }}} */
/* {{{ proto array DmExtGetStrsWithRootObj(string rootObjName, array paramNameArray)
   delete object to table resource string */
/**
* @description Function to get value for multiple parameters
* proto array DmExtGetStrsWithRootObj(string rootObjName, array paramNameArray)
*
* @param DmExtGetStrsWithRootObj - array of parameters whose values needs to be fetched
*
* @return The status of the operation.
* @retval return
* 
*/
PHP_FUNCTION(DmExtGetStrsWithRootObj)
{
    char*                           pRootObjName;
    int                             RootObjNameLen;
    char                            subSystemPrefix[6]  = {0};
    int                             iReturn             = 0;
    char*                           pDestComponentName  = NULL;
    char*                           pDestPath           = NULL;

    zval*                           pParamNameArray;
    zval**                          pArrayVal;
    HashTable *                     pArrayHash;
    HashPosition                    pPos;
    int                             iParamCount;
    char**                          ppParamNameList     = NULL;
    int                             iIndex              = 0;
    int                             iValCount           = 0;
    parameterValStruct_t **         ppParameterVal      = NULL;

    /* Parse paremeters */
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sa", &pRootObjName, &RootObjNameLen, &pParamNameArray) == FAILURE)
    {
        CosaPhpExtLog("DmExtGetStrsWithRootObj - zend_parse_parameters failed!\n");
        iReturn = CCSP_FAILURE;
        goto  EXIT0;
    }

    /* Sanity Check */
    if ( !strlen(pRootObjName) )
    {
        iReturn = CCSP_FAILURE;
        goto  EXIT0;
    }
    
    //check whether there is subsystem prefix in the dot string
    //Split Subsytem prefix and COSA dotstr if subsystem prefix is found
    CheckAndSetSubsystemPrefix(&pRootObjName,subSystemPrefix); 

    /*
     *  Get Destination component for root obj name
     */
    iReturn = UiDbusClientGetDestComponent(pRootObjName, &pDestComponentName, &pDestPath, subSystemPrefix);

    if ( iReturn != 0 )
    {
        goto  EXIT0;
    }
    else
    {
        CosaPhpExtLog("DmExtGetStrsWithRootObj -- RootObjName: %s, destination component: %s, %s\n", pRootObjName, pDestComponentName, pDestPath);
    }

    /*
     *  Construct parameter name array
     */
    pArrayHash  = Z_ARRVAL_P(pParamNameArray);
    iParamCount = zend_hash_num_elements(pArrayHash);

    CosaPhpExtLog("Name list count %d:\n", iParamCount);

    ppParamNameList = (char**)malloc(sizeof(char*) * iParamCount);
    
    if ( ppParamNameList == NULL )
    {
        CosaPhpExtLog("Failed to allocate ppParamNameList!\n");
        iReturn = CCSP_ERR_MEMORY_ALLOC_FAIL;
        goto  EXIT0;
    }

    iIndex = 0;
#if PHP_MAJOR_VERSION < 7
    for ( zend_hash_internal_pointer_reset_ex(pArrayHash, &pPos);
          zend_hash_get_current_data_ex(pArrayHash, (void**) &pArrayVal, &pPos) == SUCCESS;
          zend_hash_move_forward_ex(pArrayHash, &pPos) )
    {
        if (Z_TYPE_PP(pArrayVal) == IS_STRING)
        {
            ppParamNameList[iIndex] = Z_STRVAL_PP(pArrayVal);
            CosaPhpExtLog("  %s, (len %d)\n", ppParamNameList[iIndex], Z_STRLEN_PP(pArrayVal));
            iIndex++;
        }
    }
#else
    for ( zend_hash_internal_pointer_reset_ex(pArrayHash, &pPos);
          zend_hash_get_current_data_ex(pArrayHash, &pPos) != NULL;
          zend_hash_move_forward_ex(pArrayHash, &pPos) )
    {
        if (Z_TYPE_P(zend_hash_get_current_data_ex(pArrayHash, &pPos)) == IS_STRING)
        {
            ppParamNameList[iIndex] = Z_STRVAL_P(zend_hash_get_current_data_ex(pArrayHash, &pPos));
            CosaPhpExtLog("  %s, (len %d)\n", ppParamNameList[iIndex], Z_STRLEN_P(zend_hash_get_current_data_ex(pArrayHash, &pPos)));
            iIndex++;
        }
    }
#endif 
    iReturn = 
        CcspBaseIf_getParameterValues
            (
                bus_handle,
                pDestComponentName,
                pDestPath,
                ppParamNameList,
                iParamCount,
                &iValCount,     /* iValCount could be larger than iParamCount */
                &ppParameterVal
            );

    if ( CCSP_SUCCESS != iReturn )
    {
        CosaPhpExtLog("Failed on CcspBaseIf_getParameterValues, error code = %d.\n", iReturn);
        goto  EXIT1;
    }
    else
    {
        /*
         * construct return value array: the first value is return status,
         * the rest are sub arrays, array ( parameter name, value )
         */
        array_init(return_value);
        add_index_long(return_value, 0, 0);

        CosaPhpExtLog("%d of returned values:\n", iValCount);
#if PHP_MAJOR_VERSION < 7
        for ( iIndex = 0; iIndex < iValCount; iIndex++ )
        {
            zval*           pVal;
            
            CosaPhpExtLog("  %s = %s\n", ppParameterVal[iIndex]->parameterName, ppParameterVal[iIndex]->parameterValue);

            ALLOC_INIT_ZVAL(pVal);
            array_init(pVal);
            _add_next_index_string(pVal, ppParameterVal[iIndex]->parameterName);
            _add_next_index_string(pVal, ppParameterVal[iIndex]->parameterValue);
            
            add_next_index_zval(return_value, pVal);
        }
#else
        for ( iIndex = 0; iIndex < iValCount; iIndex++ )
        {
            zval           pVal;
            
            CosaPhpExtLog("  %s = %s\n", ppParameterVal[iIndex]->parameterName, ppParameterVal[iIndex]->parameterValue);

            array_init(&pVal);
            _add_next_index_string(&pVal, ppParameterVal[iIndex]->parameterName);
            _add_next_index_string(&pVal, ppParameterVal[iIndex]->parameterValue);
            
            add_next_index_zval(return_value, &pVal);
        }
#endif

        if ( iValCount > 0 )
        {
            free_parameterValStruct_t(bus_handle, iValCount, ppParameterVal);
        }

        iReturn = 0;
    }

EXIT1:

    if ( ppParamNameList )
    {
        free(ppParamNameList);
    }
    
    if ( iReturn == 0 )
    {
        return;
    }

EXIT0:

    /*
     * construct return value array
     */
    array_init(return_value);
    add_index_long(return_value, 0, iReturn);
    
    return;
}
/* }}} */
/* {{{ proto int DmExtSetStrsWithRootObj(string rootObjName, array paramValArray)
   delete object to table resource string */
/**
* @description Function to set value for multiple parameters
* proto int DmExtSetStrsWithRootObj(string rootObjName, array paramValArray)
*
* @param DmExtGetStrsWithRootObj - array of parameters for which values need to be set
*
* @return The status of the operation.
* @retval RETURN_LONG
* 
*/
PHP_FUNCTION(DmExtSetStrsWithRootObj)
{
    char*                           pRootObjName;
    int                             RootObjNameLen;
    char                            subSystemPrefix[6]  = {0};
    int                             iReturn             = 0;
    char*                           pDestComponentName  = NULL;
    char*                           pDestPath           = NULL;

    zend_bool                       bCommit             = 1;
    dbus_bool                       bDbusCommit         = 1;

    zval*                           pParamArray;
    HashTable *                     pParamHash;
    zval**                          pParamData;
    HashPosition                    pParamPos;
    int                             iParamCount;
    zval*                           pParamValArray;
    HashTable *                     pParamValHash;
    zval**                          pParamValData;
    HashPosition                    pParamValPos;
    parameterValStruct_t *          pParameterValList   = NULL;
    char                            BoolStrBuf[16]      = {0};
    int                             iIndex              = 0;
    char*                           pFaultParamName     = NULL;

    /* Parse paremeters */
    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sba", &pRootObjName, &RootObjNameLen, &bCommit, &pParamArray) == FAILURE)
    {
        CosaPhpExtLog("DmExtSetStrsWithRootObj - zend_parse_parameters failed!\n");
        iReturn = CCSP_FAILURE;
        goto  EXIT0;
    }

    /* Sanity Check */
    if ( !strlen(pRootObjName) )
    {
        iReturn = CCSP_FAILURE;
        goto  EXIT0;
    }
    else
    {
        bDbusCommit = (bCommit) ? 1 : 0;
    }

    //check whether there is subsystem prefix in the dot string
    //Split Subsytem prefix and COSA dotstr if subsystem prefix is found
    CheckAndSetSubsystemPrefix(&pRootObjName,subSystemPrefix); 

    /*
     *  Get Destination component for root obj name
     */
    iReturn = UiDbusClientGetDestComponent(pRootObjName, &pDestComponentName, &pDestPath, subSystemPrefix);

    if ( iReturn != 0 )
    {
        goto  EXIT0;
    }
    else
    {
        CosaPhpExtLog
            (
                "DmExtSetStrsWithRootObj -- RootObjName: %s, bCommit: %d, destination component: %s, %s\n",
                pRootObjName,
                bDbusCommit,
                pDestComponentName,
                pDestPath
            );
    }

    /*
     *  Construct parameter value array
     */
    pParamHash  = Z_ARRVAL_P(pParamArray);
    iParamCount = zend_hash_num_elements(pParamHash);

    CosaPhpExtLog("Parameter list count %d:\n", iParamCount);
    
    pParameterValList = (parameterValStruct_t*)malloc(sizeof(parameterValStruct_t) * iParamCount);
    
    if ( pParameterValList == NULL )
    {
        CosaPhpExtLog("Failed to allocate pParameterValList!\n");
        iReturn = CCSP_ERR_MEMORY_ALLOC_FAIL;
        goto  EXIT0;
    }

    iIndex = 0;

    /*
     *  Process the first level array of parameter values
     */
#if PHP_MAJOR_VERSION < 7
    for ( zend_hash_internal_pointer_reset_ex(pParamHash, &pParamPos);
          zend_hash_get_current_data_ex(pParamHash, (void**) &pParamData, &pParamPos) == SUCCESS;
          zend_hash_move_forward_ex(pParamHash, &pParamPos) )
    {
        if (Z_TYPE_PP(pParamData) != IS_ARRAY)
        {
            CosaPhpExtLog("Item is not ARRAY!\n");
        }
        else
        {
            pParamValHash   = Z_ARRVAL_PP(pParamData);
#else
    for ( zend_hash_internal_pointer_reset_ex(pParamHash, &pParamPos);
          zend_hash_get_current_data_ex(pParamHash, &pParamPos) != NULL;
          zend_hash_move_forward_ex(pParamHash, &pParamPos) )
    {
        if (Z_TYPE_P(zend_hash_get_current_data_ex(pParamHash, &pParamPos)) != IS_ARRAY)
        {
            CosaPhpExtLog("Item is not ARRAY!\n");
        }
        else
        {
            pParamValHash   = Z_ARRVAL_P(zend_hash_get_current_data_ex(pParamHash, &pParamPos)); 
#endif           
            /*
             *  Second level array of each parameter value: parameter name, type and value
             *  Therofore, the count has to be 3
             *  Construct the parameter val struct list for CCSP Base API along the way
             */
            if ( zend_hash_num_elements(pParamValHash) != 3 )
            {
                CosaPhpExtLog("Subarray count is supposed to be 3, actual value = %d!!!\n", zend_hash_num_elements(pParamValHash));
                continue;
            }
            else
            {
#if PHP_MAJOR_VERSION < 7
                zend_hash_internal_pointer_reset_ex(pParamValHash, &pParamValPos);

                if ( zend_hash_get_current_data_ex(pParamValHash, (void**) &pParamValData, &pParamValPos) == SUCCESS )
                {
                    pParameterValList[iIndex].parameterName  = Z_STRVAL_PP(pParamValData);
                    CosaPhpExtLog("  Param name %s\n", pParameterValList[iIndex].parameterName);
                }
                else
                {
                    continue;
                }
                
                zend_hash_move_forward_ex(pParamValHash, &pParamValPos);
                 
                if ( zend_hash_get_current_data_ex(pParamValHash, (void**) &pParamValData, &pParamValPos) == SUCCESS )
                {
                    char*           pTemp   = Z_STRVAL_PP(pParamValData);
#else
                zend_hash_internal_pointer_reset_ex(pParamValHash, &pParamValPos);

                if ( zend_hash_get_current_data_ex(pParamValHash, &pParamValPos) != NULL )
                {
                    pParameterValList[iIndex].parameterName  = Z_STRVAL_P(zend_hash_get_current_data_ex(pParamValHash, &pParamValPos));
                    CosaPhpExtLog("  Param name %s\n", pParameterValList[iIndex].parameterName);
                }
                else
                {
                    continue;
                }
                
                zend_hash_move_forward_ex(pParamValHash, &pParamValPos);
                 
                if ( zend_hash_get_current_data_ex(pParamValHash, &pParamValPos) != NULL )
                {
                    char*           pTemp   = Z_STRVAL_P(zend_hash_get_current_data_ex(pParamValHash, &pParamValPos));
#endif

                    if ( !strcmp(pTemp, "void") )
                    {
                        CosaPhpExtLog("  Parameter %s type is void!\n", pParameterValList[iIndex].parameterName);
                        pParameterValList[iIndex].type = ccsp_none;
                    }
                    else if ( !strcmp(pTemp, "string") )
                    {
                        pParameterValList[iIndex].type = ccsp_string;
                    }
                    else if ( !strcmp(pTemp, "int") )
                    {
                        pParameterValList[iIndex].type = ccsp_int;
                    }
                    else if ( !strcmp(pTemp, "uint") )
                    {
                        pParameterValList[iIndex].type = ccsp_unsignedInt;
                    }
                    else if ( !strcmp(pTemp, "bool") )
                    {
                        pParameterValList[iIndex].type = ccsp_boolean;
                    }
                    else if ( !strcmp(pTemp, "datetime") )
                    {
                        pParameterValList[iIndex].type = ccsp_dateTime;
                    }
                    else if ( !strcmp(pTemp, "base64") )
                    {
                        pParameterValList[iIndex].type = ccsp_base64;
                    }
                    else if ( !strcmp(pTemp, "long") )
                    {
                        pParameterValList[iIndex].type = ccsp_long;
                    }
                    else if ( !strcmp(pTemp, "unlong") )
                    {
                        pParameterValList[iIndex].type = ccsp_unsignedLong;
                    }
                    else if ( !strcmp(pTemp, "float") )
                    {
                        pParameterValList[iIndex].type = ccsp_float;
                    }
                    else if ( !strcmp(pTemp, "double") )
                    {
                        pParameterValList[iIndex].type = ccsp_double;
                    }
                    else if ( !strcmp(pTemp, "byte") )
                    {
                        pParameterValList[iIndex].type = ccsp_byte;
                    }

                    CosaPhpExtLog("  Param type %d->%s\n", pParameterValList[iIndex].type, pTemp);
                }

                zend_hash_move_forward_ex(pParamValHash, &pParamValPos);

#if PHP_MAJOR_VERSION < 7
                if ( zend_hash_get_current_data_ex(pParamValHash, (void**) &pParamValData, &pParamValPos) == SUCCESS )
                {
                    pParameterValList[iIndex].parameterValue  = Z_STRVAL_PP(pParamValData);
#else
                if ( zend_hash_get_current_data_ex(pParamValHash, &pParamValPos) != NULL )
                {
                    pParameterValList[iIndex].parameterValue  = Z_STRVAL_P(zend_hash_get_current_data_ex(pParamValHash, &pParamValPos));
#endif

                    if ( pParameterValList[iIndex].type == ccsp_boolean )
                    {
                        /* support true/false or 1/0 for boolean value */
                        if ( !strcmp(pParameterValList[iIndex].parameterValue, "1") )
                        {
                            strcpy(BoolStrBuf, "true");
                            pParameterValList[iIndex].parameterValue = BoolStrBuf;
                        }
                        else if ( !strcmp(pParameterValList[iIndex].parameterValue, "0"))
                        {
                            strcpy(BoolStrBuf, "false");
                            pParameterValList[iIndex].parameterValue = BoolStrBuf;
                        }
                    }
                    CosaPhpExtLog("  Param Value %s\n", pParameterValList[iIndex].parameterValue);
                }
                else
                {
                    continue;
                }
                
                iIndex++;
            }
        }
    }
    
    iReturn = 
        CcspBaseIf_setParameterValues
            (
                bus_handle,
                pDestComponentName,
                pDestPath,
                0,
                CCSP_COMPONENT_ID_WebUI,
                pParameterValList,
                iIndex,         /* use the actual count, instead of iParamCount */
                bDbusCommit,
                &pFaultParamName
            );

    if ( CCSP_SUCCESS != iReturn )
    {
        CosaPhpExtLog
            (
                "CcspBaseIf_setParameterValues failed, bCommit:%d, error code = %d, fault parameter = %s.\n",
                bDbusCommit,
                iReturn,
                pFaultParamName ? pFaultParamName : "none"
            );
            
        if (pFaultParamName)
        {
            free(pFaultParamName);
        }

        goto  EXIT1;
    }
    else
    {
        CosaPhpExtLog("CcspBaseIf_setParameterValues succeeded!\n");
        iReturn  = 0;
    }

EXIT1:

    if ( pParameterValList )
    {
        free(pParameterValList);
    }
    
EXIT0:

    RETURN_LONG(iReturn);
}
/* }}} */
/* {{{ proto array DmExtGetInstanceIds($objTableName)
   This function is for retrieving the instance IDs under an object table. */
/**
* @description Function for retrieving the instance IDs under an object table
* proto array DmExtGetInstanceIds($objTableName)
*
* @param DmExtGetInstanceIds - array of instance IDs to be retrieved
*
* @return The status of the operation.
* @retval return
* 
*/
PHP_FUNCTION(DmExtGetInstanceIds)
{
    int                             flen;
    char*                           dotstr              = NULL;
    char                            subSystemPrefix[6]  = {0};
    char*                           ppDestComponentName = NULL;
    char*                           ppDestPath          = NULL ;
    int                             iReturn             = 0;
    unsigned int                    InstNum             = 0;
    unsigned int*                   pInstNumList        = NULL;
    int                             iIndex              = 0;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &dotstr, &flen) == FAILURE)
    {
        CosaPhpExtLog("DmExtGetInstanceIds - zend_parse_parameters failed!\n");
        iReturn = CCSP_FAILURE;
        goto  EXIT0;
    }

    //check for NULL string
    if( !strlen(dotstr))
    {
        CosaPhpExtLog("DmExtGetInstanceIds - null object table name!\n");
        iReturn = CCSP_FAILURE;
        goto  EXIT0;
    }

    //check whether there are subsystem prefix in the dot string
    //Split Subsytem prefix and COSA dotstr if subsystem prefix is found
    CheckAndSetSubsystemPrefix(&dotstr,subSystemPrefix); 

    //Get Destination component 
    iReturn = UiDbusClientGetDestComponent(dotstr, &ppDestComponentName, &ppDestPath,subSystemPrefix);

    if ( iReturn != 0 )
    {
        goto  EXIT0;
    }
    else
    {
        CosaPhpExtLog("DmExtGetInstanceIds -- ObjectTable: %s, destination component: %s, %s\n", dotstr, ppDestComponentName, ppDestPath);
    }

    /*
     *  Get Next Instance Numbes
     */
    iReturn =
        CcspBaseIf_GetNextLevelInstances
            (
                bus_handle,
                ppDestComponentName,
                ppDestPath,
                dotstr,
                &InstNum,
                &pInstNumList
            );

    if (iReturn != CCSP_SUCCESS)
    {
        CosaPhpExtLog("Failed on CcspBaseIf_GetNextLevelInstances, error code = %d.\n", iReturn);
        goto  EXIT1;
    }
    else
    {
        char            StrBuf[24];

        array_init(return_value);
        add_index_long(return_value, 0, 0);

        CosaPhpExtLog("%d of returned values:\n", InstNum);

        for ( iIndex = 0; iIndex < InstNum; iIndex++ )
        {
            snprintf(StrBuf, sizeof(StrBuf) - 1, "%d", pInstNumList[iIndex]);
            _add_next_index_string(return_value, StrBuf);
            CosaPhpExtLog("Instance %d: %s\n", iIndex, StrBuf);
        }

        if (pInstNumList)
        {
            free(pInstNumList);
        }

        iReturn  = 0;
    }

EXIT1:
    
    if ( iReturn == 0 )
    {
        return;
    }

EXIT0:

    /*
     * construct return value array
     */
    array_init(return_value);
    add_index_long(return_value, 0, iReturn);
    
    return;
}

#! /bin/sh
##########################################################################
# If not stated otherwise in this file or this component's Licenses.txt
# file the following copyright and licenses apply:
#
# Copyright 2015 RDK Management
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
# http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
##########################################################################

#######################################################################
#   Copyright [2014] [Cisco Systems, Inc.]
# 
#   Licensed under the Apache License, Version 2.0 (the \"License\");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
# 
#       http://www.apache.org/licenses/LICENSE-2.0
# 
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an \"AS IS\" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
#######################################################################

source /lib/rdk/t2Shared_api.sh

#WEBGUI_SRC=/fss/gw/usr/www/html.tar.bz2
#WEBGUI_DEST=/var/www

#if test -f "$WEBGUI_SRC"
#then
#	if [ ! -d "$WEBGUI_DEST" ]; then
#		/bin/mkdir -p $WEBGUI_DEST
#	fi
#	/bin/tar xjf $WEBGUI_SRC -C $WEBGUI_DEST
#else
#	echo "WEBGUI SRC does not exist!"
#fi
source /etc/device.properties

if [ ! -f /nvram/certs/myrouter.io.cert.pem ] && [ "$BOX_TYPE" = "XB3" ]; then
    if [ -f /lib/rdk/check-webui-update.sh ]; then
        sh /lib/rdk/check-webui-update.sh
    fi
fi

# start lighttpd
source /etc/utopia/service.d/log_capture_path.sh
source /etc/device.properties
if [ "x$BOX_TYPE" != "xHUB4" ]; then
    source /fss/gw/etc/utopia/service.d/log_env_var.sh
fi
REVERT_FLAG="/nvram/reverted"
if [ "$MODEL_NUM" = "TG3482G" ] ; then
# RDKB-15633 from Arris XB6
LIGHTTPD_CONF="/tmp/lighttpd.conf"   
else
LIGHTTPD_CONF="/var/lighttpd.conf"
fi
LIGHTTPD_DEF_CONF="/etc/lighttpd.conf"
FILE_LOCK="/tmp/webgui.lock"
MAX_RETRY_COUNT=10
webgui_count=0
export LANG=

#Only one process should create conf file and start lighttpd at a time
while : ; do
    if [ $webgui_count -lt $MAX_RETRY_COUNT ]; then
        if [ -f $FILE_LOCK ]; then
            echo "WEBGUI :Sleeping,Another instance running"
            sleep 1;
            webgui_count=$((webgui_count+1))
            echo "Retry count = $webgui_count"
            continue;
        else
            # Creating lock to allow one process at a time
            touch $FILE_LOCK
            break;
        fi
    else
        echo "WEBGUI: Exiting, another instance is running and max retry reached"
        exit 1
    fi
done

LIGHTTPD_PID=`pidof lighttpd`
if [ "$LIGHTTPD_PID" != "" ]; then
	/bin/kill -9 $LIGHTTPD_PID
fi

if [ "$BOX_TYPE" = "HUB4" ]; then
    # Grab the locale based on the region code if we don't have the override flag
    LOCALE_OVERRIDE_FILE=/nvram/locale-override
    LOCALE_CONF=/tmp/locale.conf
    if [ -f "$LOCALE_OVERRIDE_FILE" ]; then
       LOCALE=`cat $LOCALE_OVERRIDE_FILE`
    else
       # set the locale from the region code
       COUNTRY=`grep "REGION" /tmp/serial.txt | cut -d"=" -f2 | xargs`
       if [ "$COUNTRY" == "IT" ]; then
               LOCALE="it_IT.utf8"
       else
               LOCALE="en_GB.utf8"
       fi
    fi
    # Now set the system locale before starting the UI
    localectl set-locale LANG=$LOCALE
    sed -i'' "s/LANG=en_GB.utf8/LANG=$LOCALE/g" $LOCALE_CONF
    export LANG=$LOCALE
fi

HTTP_ADMIN_PORT=`syscfg get http_admin_port`
HTTP_PORT=`syscfg get mgmt_wan_httpport`
HTTP_PORT_ERT=`syscfg get mgmt_wan_httpport_ert`
HTTPS_PORT=`syscfg get mgmt_wan_httpsport`
BRIDGE_MODE=`syscfg get bridge_mode`

if [ "$BRIDGE_MODE" != "0" ]; then
    INTERFACE="lan0"
else
    INTERFACE="brlan0"
fi

cp $LIGHTTPD_DEF_CONF $LIGHTTPD_CONF

#sed -i "s/^server.port.*/server.port = $HTTP_PORT/" /var/lighttpd.conf
#sed -i "s#^\$SERVER\[.*\].*#\$SERVER[\"socket\"] == \":$HTTPS_PORT\" {#" /var/lighttpd.conf

if [ "$BOX_TYPE" == "HUB4" ]; then
    echo "setenv.add-environment = (\"LANG\" => \"$LOCALE\")" >> $LIGHTTPD_CONF
fi
HTTP_SECURITY_HEADER_ENABLE=`syscfg get HTTPSecurityHeaderEnable`

if [ "$HTTP_SECURITY_HEADER_ENABLE" = "true" ]; then
    echo "setenv.add-response-header = ("  >> $LIGHTTPD_CONF
    echo "    \"X-Frame-Options\" => \"deny\","  >> $LIGHTTPD_CONF
    echo "    \"X-XSS-Protection\" => \"1; mode=block\","  >> $LIGHTTPD_CONF
    echo "    \"X-Content-Type-Options\" => \"nosniff\","  >> $LIGHTTPD_CONF
    echo "    \"Content-Security-Policy\" => \"default-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' 'unsafe-eval'; frame-src 'self' 'unsafe-inline' 'unsafe-eval'; font-src 'self' 'unsafe-inline' 'unsafe-eval'; form-action 'self' 'unsafe-inline' 'unsafe-eval'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; img-src 'self'; connect-src 'self'; object-src 'none'; media-src 'none'; script-nonce 'none'; plugin-types 'none'; reflected-xss 'none'; report-uri 'none';\","  >> $LIGHTTPD_CONF
    echo ")"  >> $LIGHTTPD_CONF
    echo "#sandbox 'allow-same-origin allow-scripts allow-popups allow-forms';"  >> $LIGHTTPD_CONF
fi

echo "server.port = $HTTP_ADMIN_PORT" >> $LIGHTTPD_CONF
echo "server.bind = \"$INTERFACE\"" >> $LIGHTTPD_CONF
if [ "$BOX_TYPE" == "HUB4" ]
then
    echo "\$SERVER[\"socket\"] == \"erouter0:80\" { server.use-ipv6 = \"enable\" }" >> $LIGHTTPD_CONF
else
    if ([ "$BOX_TYPE" = "XB6" -a "$MANUFACTURE" = "Arris" ] || [ "$MODEL_NUM" = "INTEL_PUMA" ]) ; then
    	# Intel Proposed Bug Fix to not add in ETH WAN Mode
    	if [ ! -f /nvram/ETHWAN_ENABLE ] ; then
    		echo "\$SERVER[\"socket\"] == \"wan0:80\" { server.use-ipv6 = \"enable\" }" >> $LIGHTTPD_CONF
    	fi
    else
    	echo "\$SERVER[\"socket\"] == \"wan0:80\" { server.use-ipv6 = \"enable\" }" >> $LIGHTTPD_CONF
    fi
fi

if [ "x$HTTP_PORT_ERT" != "x" ] && [ $HTTP_PORT_ERT -ne 0 ] && [ "$HTTP_PORT_ERT" -ge 1025 ] && [ "$HTTP_PORT_ERT" -le 65535 ];then
    echo "\$SERVER[\"socket\"] == \"erouter0:$HTTP_PORT_ERT\" { server.use-ipv6 = \"enable\" }" >> $LIGHTTPD_CONF
else
    echo "\$SERVER[\"socket\"] == \"erouter0:$HTTP_PORT\" { server.use-ipv6 = \"enable\" }" >> $LIGHTTPD_CONF
fi

echo "\$SERVER[\"socket\"] == \"$INTERFACE:443\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" }" >> $LIGHTTPD_CONF

#If video analytics test is enabled in device.properties file, open 58081 securely.
if [ "$VIDEO_ANALYTICS" = "enabled" ]
then
#Opening port 58081 for MTLS connection
	echo "\$SERVER[\"socket\"] == \"$INTERFACE:58081\" { server.use-ipv6 = \"enable\" server.document-root = \"/usr/video_analytics\" ssl.engine = \"enable\" ssl.verifyclient.activate = \"enable\" ssl.ca-file = \"/etc/webui/certs/comcast-rdk-ca-chain.cert.pem\" ssl.pemfile = \"/tmp/.webui/rdkb-video.pem\" }" >> $LIGHTTPD_CONF
fi

if [ "$BOX_TYPE" == "HUB4" ]; then
   echo "\$SERVER[\"socket\"] == \"erouter0:443\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" }" >> $LIGHTTPD_CONF
else
    if ([ "$BOX_TYPE" = "XB6" -a "$MANUFACTURE" = "Arris" ] || [ "$MODEL_NUM" = "INTEL_PUMA" ]) ; then
    	# Intel Proposed Bug Fix to not add in ETH WAN Mode
    	if [ ! -f /nvram/ETHWAN_ENABLE ] ; then
    		echo "\$SERVER[\"socket\"] == \"wan0:443\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" }" >> $LIGHTTPD_CONF
    	fi
    else
    	echo "\$SERVER[\"socket\"] == \"wan0:443\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" }" >> $LIGHTTPD_CONF
    fi
fi

if [ $HTTPS_PORT -ne 0 ] && [ "$HTTPS_PORT" -ge 1025 ] && [ "$HTTPS_PORT" -le 65535 ]
then
  echo "\$SERVER[\"socket\"] == \"erouter0:$HTTPS_PORT\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" }" >> $LIGHTTPD_CONF
else
    # When the httpsport is set to NULL. Always put default value into database.
    syscfg set mgmt_wan_httpsport 8181
    syscfg commit
    HTTPS_PORT=`syscfg get mgmt_wan_httpsport`
    echo "\$SERVER[\"socket\"] == \"erouter0:$HTTPS_PORT\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" }" >> $LIGHTTPD_CONF
fi

#Changes for ArrisXb6-2949

if [ ! -d "/tmp/pcontrol" ]
then
     mkdir /tmp/pcontrol
fi

cp -rf /usr/www/cmn/ /tmp/pcontrol
#Dynamically create pause screen file 
#removed chmod as part of CISCOXB3-6294 since etc is read-only FileSystem
sh /etc/pauseBlockGenerateHtml.sh

echo "\$SERVER[\"socket\"] == \"$INTERFACE:21515\" { server.use-ipv6 = \"enable\" server.document-root = \"/tmp/pcontrol/\" url.rewrite-if-not-file = \"('^/(.*)$' => '/index.html?fwd=$1')\" url.access-deny =(\".inc\" )  }" >> $LIGHTTPD_CONF

WIFIUNCONFIGURED=`syscfg get redirection_flag`
SET_CONFIGURE_FLAG=`psmcli get eRT.com.cisco.spvtg.ccsp.Device.WiFi.NotifyWiFiChanges`

#Read the http response value
NETWORKRESPONSEVALUE=`cat /var/tmp/networkresponse.txt`

iter=0
max_iter=2
while [ "$SET_CONFIGURE_FLAG" = "" ] && [ "$iter" -le $max_iter ]
do
	iter=$((iter+1))
	echo "$iter"
	SET_CONFIGURE_FLAG=`psmcli get eRT.com.cisco.spvtg.ccsp.Device.WiFi.NotifyWiFiChanges`
done
echo "WEBGUI : NotifyWiFiChanges is $SET_CONFIGURE_FLAG"
echo "WEBGUI : redirection_flag val is $WIFIUNCONFIGURED"

restartEventsForRfCp()
{
    echo "WEBGUI : restart norf cp events restart"
    sysevent set norf_webgui 1
    sysevent set firewall-restart
    sysevent set zebra-restart
    sysevent set dhcp_server-stop
    # Let's make sure dhcp server restarts properly
    sleep 1
    sysevent set dhcp_server-start
    dibbler-server stop
    dibbler-server start
}

# Check if unit has proper RF signal
checkRfStatus()
{
   noRfCp=0
   RF_SIGNAL_STATUS=`dmcli eRT getv Device.DeviceInfo.X_RDKCENTRAL-COM_CableRfSignalStatus | grep value | cut -f3 -d : | cut -f2 -d" "`
   isInRfCp=`syscfg get rf_captive_portal`
   echo_t "WEBGUI: values RF_SIGNAL_STATUS : $RF_SIGNAL_STATUS , isInRfCp: $isInRfCp"
   if [ "$RF_SIGNAL_STATUS" = "false" ] || [ "$isInRfCp" = "true" ]
   then
      noRfCp=1
   else
      noRfCp=0
   fi

   if [ $noRfCp -eq 1 ]
   then
      echo_t "WEBGUI: Set rf_captive_portal true"
      syscfg set rf_captive_portal true
      syscfg commit
      return 1
   else
      return 0
   fi
} 


if [ "$BOX_TYPE" = "XB6" ]
then

    # P&M up will make sure CM agent is up as well as
    # RFC values are picked
    echo_t "No RF CP: Check PAM initialized"
    PAM_UP=0
    while [ $PAM_UP -ne 1 ]
    do
    sleep 1
    #Check if CcspPandMSsp is up
    # PAM_PID=`pidof CcspPandMSsp`

    if [ -f "/tmp/pam_initialized" ]
    then
         PAM_UP=1
    fi
    done
    echo_t "RF CP: PAM is initialized"

    enableRFCaptivePortal=`syscfg get enableRFCaptivePortal`
    ethWanEnabled=`syscfg get eth_wan_enabled`
    cpFeatureEnbled=`syscfg get CaptivePortal_Enable`

   # Enable RF CP in first iteration. network_response.sh will run once WAN comes up
   # network_response.sh will take the unit out of RF CP 
   if [ "$enableRFCaptivePortal" != "false" ] && [ "$ethWanEnabled" != "true" ] && [ "$cpFeatureEnbled" = "true" ]
   then
       checkRfStatus 
       isRfOff=$?
       echo_t "WEBGUI: RF status returned is: $isRfOff"
       if [ "$isRfOff" = "1" ]
       then
          echo_t "WEBGUI: Restart events for RF CP"
          restartEventsForRfCp
       fi
   fi
fi

if [ "$WIFIUNCONFIGURED" = "true" ]
then
	if [ "$NETWORKRESPONSEVALUE" = "204" ] && [ "$SET_CONFIGURE_FLAG" = "true" ]
	then
		while : ; do
		echo "WEBGUI : Waiting for PandM to initalize completely to set ConfigureWiFi flag"
		CHECK_PAM_INITIALIZED=`find /tmp/ -name "pam_initialized"`
		echo "CHECK_PAM_INITIALIZED is $CHECK_PAM_INITIALIZED"
  	        	if [ "$CHECK_PAM_INITIALIZED" != "" ]
   			then
			   echo "WEBGUI : WiFi is not configured, setting ConfigureWiFi to true"
	         	   output=`dmcli eRT setvalues Device.DeviceInfo.X_RDKCENTRAL-COM_ConfigureWiFi bool TRUE`
			   check_success=`echo $output | grep  "Execution succeed."`
  	        		if [ "$check_success" != "" ]
   				then
     			 	   echo "WEBGUI : Setting ConfigureWiFi to true is success"
				uptime=`cat /proc/uptime | awk '{ print $1 }' | cut -d"." -f1`
				   echo_t "Enter_WiFi_Personalization_captive_mode:$uptime"
				   t2ValNotify "btime_wcpenter_split" $uptime
				   if [ -e "/usr/bin/onboarding_log" ]; then
				       /usr/bin/onboarding_log "Enter_WiFi_Personalization_captive_mode:$uptime"
				   fi
 	       			fi
      			   break
 	       		fi
		sleep 2
		done
	

	else
		if [ ! -e "$REVERT_FLAG" ] && [ "$NETWORKRESPONSEVALUE" = "204" ]
		then
			# We reached here as redirection_flag is "true". But WiFi is configured already as per notification status.
			# Set syscfg value to false now.
			echo "WEBGUI : WiFi is already personalized... Setting redirection_flag to false"
			syscfg set redirection_flag false
			syscfg commit
			echo "WEBGUI: WiFi is already personalized. Set reverted flag in nvram"	
			touch $REVERT_FLAG
		fi
	fi
fi		

if [ "$VIDEO_ANALYTICS" = "enabled" ]
then
    CONFIGPARAMGEN=/usr/bin/configparamgen
    if [ -d /etc/webui/certs ]; then
       mkdir -p /tmp/.webui/
       cp /etc/webui/certs/comcast-rdk-ca-chain.cert.pem /tmp/.webui/
       if [ -f $CONFIGPARAMGEN ]; then
          $CONFIGPARAMGEN jx /etc/webui/certs/ptohjvfeh.sdn /tmp/.webui/rdkb-video.pem
          if [ -f /tmp/.webui/rdkb-video.pem ]; then
             chmod 600 /tmp/.webui/rdkb-video.pem
          fi
       else
          echo "$CONFIGPARAMGEN not found !!!"
       fi
    fi
fi

#echo "\$SERVER[\"socket\"] == \"$INTERFACE:10443\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" server.document-root = \"/fss/gw/usr/walled_garden/parcon/siteblk\" server.error-handler-404 = \"/index.php\" }" >> /var/lighttpd.conf
#echo "\$SERVER[\"socket\"] == \"$INTERFACE:18080\" { server.use-ipv6 = \"enable\"  server.document-root = \"/fss/gw/usr/walled_garden/parcon/siteblk\" server.error-handler-404 = \"/index.php\" }" >> /var/lighttpd.conf

LOG_PATH_OLD="/var/tmp/logs/"

if [ "$LOG_PATH_OLD" != "$LOG_PATH" ]
then
	sed -i "s|${LOG_PATH_OLD}|${LOG_PATH}|g" $LIGHTTPD_CONF
fi

if [ "$MODEL_NUM" = "TG3482G" ] ; then
	# RDKB-15633 from Arris XB6
	RFC_CONTAINER_SUPPORT=`syscfg get containersupport`
	if [ "x$CONTAINER_SUPPORT" = "x1" -a  "x$RFC_CONTAINER_SUPPORT" = "xtrue" ]; then
	  touch /tmp/.lxcenabled
	  echo "WEBGUI: Started in Container."
	else
	  LD_LIBRARY_PATH=/fss/gw/usr/ccsp:$LD_LIBRARY_PATH lighttpd -f $LIGHTTPD_CONF

	  if [ -f /tmp/.webui/rdkb-video.pem ]; then
       rm -rf /tmp/.webui/rdkb-video.pem
	  fi
	  echo "WEBGUI: Started without Container."
	fi
else
	  LD_LIBRARY_PATH=/fss/gw/usr/ccsp:$LD_LIBRARY_PATH lighttpd -f $LIGHTTPD_CONF

	  if [ -f /tmp/.webui/rdkb-video.pem ]; then
       rm -rf /tmp/.webui/rdkb-video.pem
	  fi	  
fi

echo "WEBGUI : Set event"
sysevent set webserver started
touch /tmp/webgui_initialized
#Removing the lock
rm -f $FILE_LOCK

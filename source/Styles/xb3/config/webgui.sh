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

# start lighttpd
source /etc/utopia/service.d/log_capture_path.sh
source /fss/gw/etc/utopia/service.d/log_env_var.sh
source /etc/device.properties
REVERT_FLAG="/nvram/reverted"
LIGHTTPD_CONF="/var/lighttpd.conf"
LIGHTTPD_DEF_CONF="/etc/lighttpd.conf"

webgui_count=0
while : ; do
    WEBGUI_INST=`ps | grep webgui.sh | grep -v grep | grep -c webgui.sh`
    WEBGUI_INST_PROCESS=`ps -l | grep webgui.sh | grep -v grep`

    if [ $webgui_count -lt 3 ]; then
        if [ $WEBGUI_INST -gt 2 ]; then
            echo "WEBGUI :Sleeping,Another instance running"
            echo "WEBGUI :WEBGUI_INST= $WEBGUI_INST"
            echo "$WEBGUI_INST_PROCESS"
            webgui_count=$((webgui_count+1))
            sleep 2;
        else
            break;
        fi
    else
        echo "WEBGUI :Exiting,Another instance running, Max retry reached"
        exit 1
    fi
done

LIGHTTPD_PID=`pidof lighttpd`
if [ "$LIGHTTPD_PID" != "" ]; then
	/bin/kill $LIGHTTPD_PID
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
echo "\$SERVER[\"socket\"] == \"wan0:80\" { server.use-ipv6 = \"enable\" }" >> $LIGHTTPD_CONF

if [ "x$HTTP_PORT_ERT" != "x" ] && [ $HTTP_PORT_ERT -ne 0 ] && [ "$HTTP_PORT_ERT" -ge 1025 ] && [ "$HTTP_PORT_ERT" -le 65535 ];then
    echo "\$SERVER[\"socket\"] == \"erouter0:$HTTP_PORT_ERT\" { server.use-ipv6 = \"enable\" }" >> $LIGHTTPD_CONF
else
    echo "\$SERVER[\"socket\"] == \"erouter0:$HTTP_PORT\" { server.use-ipv6 = \"enable\" }" >> $LIGHTTPD_CONF
fi

echo "\$SERVER[\"socket\"] == \"$INTERFACE:443\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" ssl.use-compression = \"disable\" ssl.cipher-list = \"EECDH+AESGCM:EDH+AESGCM:AES128+EECDH:AES128+EDH\" ssl.use-sslv2 = \"disable\" ssl.use-sslv3 = \"disable\"}" >> $LIGHTTPD_CONF

#If video analytics test is enabled in device.properties file, open 28081 securely.
if [ "$VIDEO_ANALYTICS" = "enabled" ]
then
   echo "\$SERVER[\"socket\"] == \"$INTERFACE:28081\" { server.use-ipv6 = \"enable\" server.document-root = \"/usr/video_analytics\" ssl.engine = \"enable\" ssl.pemfile = \"/tmp/.webui/rdkb-video.pem\" }" >> $LIGHTTPD_CONF
fi

echo "\$SERVER[\"socket\"] == \"wan0:443\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" ssl.use-compression = \"disable\" ssl.cipher-list = \"EECDH+AESGCM:EDH+AESGCM:AES128+EECDH:AES128+EDH\" ssl.use-sslv2 = \"disable\" ssl.use-sslv3 = \"disable\"}" >> $LIGHTTPD_CONF
if [ $HTTPS_PORT -ne 0 ] && [ "$HTTPS_PORT" -ge 1025 ] && [ "$HTTPS_PORT" -le 65535 ]
then
    echo "\$SERVER[\"socket\"] == \"erouter0:$HTTPS_PORT\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" ssl.use-compression = \"disable\" ssl.cipher-list = \"EECDH+AESGCM:EDH+AESGCM:AES128+EECDH:AES128+EDH\" ssl.use-sslv2 = \"disable\" ssl.use-sslv3 = \"disable\"}" >> $LIGHTTPD_CONF
else
    # When the httpsport is set to NULL. Always put default value into database.
    syscfg set mgmt_wan_httpsport 8081
    syscfg commit
    HTTPS_PORT=`syscfg get mgmt_wan_httpsport`
    echo "\$SERVER[\"socket\"] == \"erouter0:$HTTPS_PORT\" { server.use-ipv6 = \"enable\" ssl.engine = \"enable\" ssl.pemfile = \"/etc/server.pem\" ssl.use-compression = \"disable\" ssl.cipher-list = \"EECDH+AESGCM:EDH+AESGCM:AES128+EECDH:AES128+EDH\" ssl.use-sslv2 = \"disable\" ssl.use-sslv3 = \"disable\"}" >> $LIGHTTPD_CONF
fi

#Changes for ArrisXb6-2949

if [ ! -d "/tmp/pcontrol" ]
then
     mkdir /tmp/pcontrol
fi

cp -rf /usr/www/cmn/ /tmp/pcontrol

if [ ! -f "/tmp/www/index.php" ]
then
    cp /usr/www/index_pcontrol.php /tmp/pcontrol/index.php
fi

echo "\$SERVER[\"socket\"] == \"$INTERFACE:21515\" { server.use-ipv6 = \"enable\" server.document-root = \"/tmp/pcontrol/\" }" >> $LIGHTTPD_CONF

 
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
    CONFIGPARAMGEN=/usr/bin/cpgc
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

LD_LIBRARY_PATH=/fss/gw/usr/ccsp:$LD_LIBRARY_PATH lighttpd -f $LIGHTTPD_CONF

if [ -f /tmp/.webui/rdkb-video.pem ]; then
       rm -rf /tmp/.webui/rdkb-video.pem
fi
echo "WEBGUI : Set event"
sysevent set webserver started
touch /tmp/webgui_initialized

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

#
include $(CCSP_ROOT_DIR)/arch/ccsp_common.mk

export CCSP_DEP_HEADER=$(CCSP_OPENSOURCE_DIR)/include
export CCSP_DEP_LIB=$(CCSP_OPENSOURCE_DIR)/lib

CCSP_COMMON_DIR=$(CCSP_ROOT_DIR)/CcspCommonLibrary
export CCSP_COMMON_SRC=$(CCSP_COMMON_DIR)/source
export CCSP_COMMON_LIB=$(CCSP_COMMON_DIR)/$(ComponentBuildDir)
export CCSP_COMMON_BOARD_INC=$(CCSP_COMMON_DIR)/$(CCSP_ARCH_DIR_NAME)/$(CcspArch)/$(CCSP_BOARDS_DIR_NAME)/$(CcspBoard)/$(CCSP_INCLUDE_DIR_NAME)

WEBUI_DIR=$(ComponentSrcDir)/Styles/xb3/code
WALLED_GARDEN_DIR=$(ComponentSrcDir)/Styles/xb3/walled_garden
WEBUI_CONFIG=$(ComponentSrcDir)/Styles/xb3/config

PhpExtensionDir := CcspPhpExtension
PhpExtensionBuildDir := $(ComponentBaseDir)/$(ComponentBuildDir)/$(PhpExtensionDir)

PACKAGE_VERSION := 
PACKAGE_DIR := $(ComponentSrcDir)/CcspPhpExtension

PACKAGE_CONF := --enable-cosa $(CcspPhpExtensionConfigureOption)

all: build

$(PhpExtensionBuildDir)/configure: Makefile $(ComponentSrcDir)/CcspPhpExtension/config.m4
	@[ -d $(ComponentBuildDir) ] || mkdir -p $(ComponentBuildDir)
	@[ -d $(ComponentBuildDir)/$(PhpExtensionDir) ] || mkdir -p $(ComponentBuildDir)/$(PhpExtensionDir)
	cp -r $(PACKAGE_DIR) $(ComponentBuildDir)
	cd $(PhpExtensionBuildDir) && phpize

$(PhpExtensionBuildDir)/Makefile: $(PhpExtensionBuildDir)/configure
	cd $(PhpExtensionBuildDir) && ./configure $(PACKAGE_CONF)

build: $(PhpExtensionBuildDir)/Makefile $(ComponentSrcDir)/CcspPhpExtension/cosa.c $(ComponentSrcDir)/CcspPhpExtension/php_cosa.h $(ComponentSrcDir)/CcspPhpExtension/config.m4
	cp -p $(ComponentSrcDir)/CcspPhpExtension/cosa.c $(PhpExtensionBuildDir)
	cp -p $(ComponentSrcDir)/CcspPhpExtension/php_cosa.h $(PhpExtensionBuildDir)
	cp -p $(ComponentSrcDir)/CcspPhpExtension/config.m4 $(PhpExtensionBuildDir)
	$(MAKE) -C $(PhpExtensionBuildDir)

install:
	@echo "Install webui"
	cp -f $(ComponentBuildDir)/$(PhpExtensionDir)/.libs/cosa.so $(CCSP_OUT_DIR)

	@mkdir -p $(CCSP_INSTALL_ROOT)/usr/www
	@cp -rf $(WEBUI_DIR)/* $(CCSP_INSTALL_ROOT)/usr/www

	@mkdir -p $(CCSP_INSTALL_ROOT)/usr/walled_garden
	@cp -rf $(WALLED_GARDEN_DIR)/* $(CCSP_INSTALL_ROOT)/usr/walled_garden

	@mkdir -p $(CCSP_INSTALL_ROOT)/etc
	@cp -f $(WEBUI_CONFIG)/* $(CCSP_INSTALL_ROOT)/etc

clean:
	rm -rf $(ComponentBuildDir)/$(PhpExtensionDir)

.PHONY: all build install clean

Procedure to create test output files

1) checkout and create a desktop build of jst
    git clone ssh://gerrit.teamccp.com:29418/rdk/components/generic/jst/generic jst
    cd jst
    git checkout topic/tiny_rdk_top
    mkdir build
    cd build
    cmake -DBUILD_TESTING=ON ..
    make

2) checkout webui
    git clone ssh://gerrit.teamccp.com:29418/rdk/rdkb/components/opensource/ccsp/webui/generic webui
    cd webui
    git checkout topic/tiny_rdk_top


3) edit webui/source/Styles/xb3/jst_tests/create_parsed_files.sh
    set the top 2 paths to your local paths:
    JST_PATH=/data/xb3_1212/generic/jst
    WEBUI_PATH=/data/xb3_1212/generic/webui

4) run webui/source/Styles/xb3/jst_tests/create_parsed_files.sh

5) in webui/source/Styles/xb3/jst_tests verify the following files were updated:
    revision.txt (should contain the current commit hash of jst and webui repos)
    webui.tgz (you might untar and chech that every .jst file, has a corresponding .jst.parsed files)

6) add these 2 files and commit
    cd webui
    git add webui/source/Styles/xb3/jst_tests/revision.txt webui/source/Styles/xb3/jst_tests/webui.tgz
    git commit 
    git review topic/tiny_rdk_top(or the current branch)
  
  
Procedure to test jst against webui

1) checkout and create a desktop build of jst
    git clone ssh://gerrit.teamccp.com:29418/rdk/components/generic/jst/generic jst
    cd jst
    git checkout topic/tiny_rdk_top
    mkdir build
    cd build
    cmake -DBUILD_TESTING=ON -DTEST_COMCAST_WEBUI=ON ..
    make
    make install

2) run tests/testGroup.webui
      cd tests/webui
      ../testGroup.webui



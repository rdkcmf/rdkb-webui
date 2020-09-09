#!/bin/bash

JST_PATH=/data/xb3_0107/generic/jst
WEBUI_PATH=/data/xb3_0107/generic/webui

JST_BIN=$JST_PATH/build/jst
JST_JSTS=$JST_PATH/jsts
WEBUI_JST=$WEBUI_PATH/source/Styles/xb3/jst
WEBUI_JST_TESTS=$WEBUI_PATH/source/Styles/xb3/jst_tests
NAME=webui

echo "" > $WEBUI_JST_TESTS/revision.txt

cd $JST_PATH
git log -1 | grep commit | awk -F' ' '{print "jst: " $2}' >> $WEBUI_JST_TESTS/revision.txt

cd $WEBUI_PATH
git log -1 | grep commit | awk -F' ' '{print "webui: " $2}' >> $WEBUI_JST_TESTS/revision.txt

rm -fr $WEBUI_JST_TESTS/$NAME
cp -ar $WEBUI_JST $WEBUI_JST_TESTS/$NAME
cp $JST_JSTS/php.jst $WEBUI_JST_TESTS/$NAME/includes
cp $JST_JSTS/*.js $WEBUI_JST_TESTS/$NAME

cd $WEBUI_JST_TESTS/$NAME
find ./ -name "*.jst" -exec sh -c "$JST_BIN --parse-only {} > {}.parsed" \;

cd $WEBUI_JST_TESTS
tar czf $NAME.tgz $NAME
rm -fr $WEBUI_JST_TESTS/$NAME

echo "now run the following:"
echo "cd $WEBUI_PATH"
echo "git add $WEBUI_JST_TESTS/revision.txt $WEBUI_JST_TESTS/$NAME.tgz"
echo "git commit"

dnl $Id$
dnl config.m4 for extension cosa

PHP_ARG_ENABLE(cosa, whether to enable Cosa Extension support,
[ --enable-cosa[=TYP]   Enable Cosa Extension support. TYP is optional type of build (default - pc) ])

if test "$PHP_COSA" != "no"; then
  AC_DEFINE(HAVE_COSA, 1, [Whether you have Cosa Extension])
  
  AC_MSG_CHECKING([for libraries])
  LIB_FILE="libccsp_common.so"
  if test "$PHP_COSA" != "yes"; then
    TYP=$PHP_COSA
  else
    TYP="pc"
  fi
  

  SEARCH_PATH="$CCSP_COMMON_LIB"
  if test -r $SEARCH_PATH/$LIB_FILE; then
    FOUND="true"
    AC_MSG_RESULT(found libraries in $SEARCH_PATH)
  fi

  if test -z "$FOUND"; then
    AC_MSG_RESULT([not found])
    AC_MSG_ERROR([Please specify the correct build type])
  fi

dnl  PHP_ADD_INCLUDE("$OE_STGING_INCLUDE_PATH")
  PHP_ADD_INCLUDE("$CCSP_DEP_HEADER/dbus-1.0") 
  PHP_ADD_INCLUDE("$CCSP_COMMON_SRC/debug_api/include")
  PHP_ADD_INCLUDE("$CCSP_COMMON_SRC/debug_api/include/linux")
  PHP_ADD_INCLUDE("$CCSP_COMMON_SRC/cosa/include")
  PHP_ADD_INCLUDE("$CCSP_COMMON_SRC/cosa/include/linux")
  PHP_ADD_INCLUDE("$CCSP_COMMON_SRC/cosa/package/slap/include")  
  PHP_ADD_INCLUDE("$CCSP_COMMON_BOARD_INC")
  PHP_ADD_INCLUDE("$CCSP_COMMON_SRC/ccsp/include")
  PHP_ADD_INCLUDE("$CCSP_DEP_HEADER/openssl")

  PHP_ADD_LIBRARY_WITH_PATH(crypto, $CCSP_DEP_LIB, COSA_SHARED_LIBADD)
  PHP_ADD_LIBRARY_WITH_PATH(ssl, $CCSP_DEP_LIB, COSA_SHARED_LIBADD)
  PHP_ADD_LIBRARY_WITH_PATH(ccsp_common, $CCSP_COMMON_LIB, COSA_SHARED_LIBADD)

  PHP_SUBST(COSA_SHARED_LIBADD)
  
  PHP_NEW_EXTENSION(cosa, cosa.c, $ext_shared)
fi


#!/bin/bash
#
#  dev
#       create Development database
#
#         bash & web user with all privileges
#               credentials.local.bash
#               credentials.local.inc.php
#
#  test
#       create Test Quality Assurance database
#
#         bash user with all privileges
#               credentials.local.bash
#         web user with only sp execute
#               credentials.local.inc.php
#
#  prod
#       create Production database
#
#         web user with only sp execute
#               credentials.local.inc.php
#

# get parameters
usage() {
  echo "Usage: ${BASH_SOURCE[0]}
    environment: -e <dev|test|prod>
    database name (limit 7 characters): -n <name>
    database server (optional default localhost): -s <hostname>
  " 1>&2
  exit 1
}
while getopts "e:s:n:" o; do
  case "${o}" in
    e)
      ENVIRONMENT=${OPTARG}
      [ "$ENVIRONMENT" == "dev" ] || [ "$ENVIRONMENT" == "test" ] || [ "$ENVIRONMENT" == "prod" ] || usage
      ;;
    n)
      DBNAME=${OPTARG}
      ;;
    s)
      DATABASE_SERVER=${OPTARG}
      ;;
    *)
      usage
      ;;
esac
done
shift $((OPTIND-1))
if [ -z "${ENVIRONMENT+x}" ] || [ -z "${DBNAME+x}" ]; then
  usage
fi
if [ "${#DBNAME}" -gt 7 ]; then
  echo "${#DBNAME}"
  echo "ERROR: Database name must be 7 or less characters."
  exit 1
fi
if [ -z "${DATABASE_SERVER+x}" ]; then
  DATABASE_SERVER="localhost"
fi


database_server="$DATABASE_SERVER"

# create new password
# thanks vivek@nixCraft
# http://www.cyberciti.biz/faq/linux-random-password-generator/
genpasswd() {
        local l=$1
        [ "$l" == "" ] && l=16
        tr -dc A-Za-z0-9 < /dev/urandom | head -c ${l} | xargs
}
new_id="$(genpasswd 5)"
new_password="$(genpasswd)"
test_dbo_username=""

# default to dev
if [ "$ENVIRONMENT" == "dev" ]; then

  new_db_name="$DBNAME""_D_$new_id"
  new_username="$DBNAME""_UA_$new_id"
  echo "
    Creating new DEVELOPMENT database $new_db_name"
  echo "
    CREATE USER '$new_username'@'$database_server' IDENTIFIED BY '$new_password';"
  sql_privileges="
    GRANT ALL PRIVILEGES ON $new_db_name.* TO '$new_username'@'$database_server';"
  echo "$sql_privileges"

else

  if [ "$ENVIRONMENT" == "test" ]; then

    new_db_name="$DBNAME""_T_$new_id"
    new_username="$DBNAME""_UE_$new_id"
    test_dbo_username="$DBNAME""_UA_$new_id"
    echo "
      Creating new TEST-QA database $new_db_name"
    echo "
      CREATE USER '$new_username'@'$database_server' IDENTIFIED BY '$new_password';"
    sql_privileges="
      CREATE USER '$test_dbo_username'@'$database_server' IDENTIFIED BY '$new_password';
      GRANT ALL PRIVILEGES ON $new_db_name.* TO '$test_dbo_username'@'$database_server';
      GRANT EXECUTE ON $new_db_name.* TO '$new_username'@'$database_server';"
    echo "$sql_privileges"
    test_dbo_username="database_user=\"$test_dbo_username\";"

  fi

  if [ "$ENVIRONMENT" == "prod" ]; then

    new_db_name="$DBNAME""_P_$new_id"
    new_username="$DBNAME""_UE_$new_id"
    new_password="$(genpasswd 32)"
    echo "
      Creating new PRODUCTION database $new_db_name"
    echo "
      CREATE USER '$new_username'@'$database_server' IDENTIFIED BY '$new_password';"
    sql_privileges="
      GRANT EXECUTE ON $new_db_name.* TO '$new_username'@'$database_server';"
    echo "$sql_privileges"

  fi

fi

echo "Please enter username (e.g. root): "
read privileged_user_name
echo "Please enter password: "
stty_orig=`stty -g`
stty -echo
read privileged_user_pw
stty $stty_orig

mysql --host="$database_server" --user="$privileged_user_name" --password="$privileged_user_pw" << EOF

CREATE DATABASE $new_db_name CHARACTER SET utf8 COLLATE utf8_unicode_ci;

CREATE USER '$new_username'@'$database_server' IDENTIFIED BY '$new_password';

$sql_privileges

EOF

DATE=$(date "+%Y-%m-%d %A %H:%M:%S")

cat << EOF >> credentials.local.bash

# created $DATE
database_server="$database_server"
database_user="$new_username"
database_password="$new_password"
database_name="$new_db_name"
$test_dbo_username

EOF

cat << EOF >> credentials.local.inc.php

<?php
// created $DATE
\$database_server = "$database_server";
\$database_username = "$new_username";
\$database_password = "$new_password";
\$database_name = "$new_db_name";
?>

EOF

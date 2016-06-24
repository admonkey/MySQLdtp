#!/bin/bash
#
#   database_server="localhost"
#   database_user="dbo_user"
#   database_password="p@55W0rd"
#   database_name="pm_db"
#

# stop on error
set -e

# get parameters
usage() {
  echo "Usage: ${BASH_SOURCE[0]}
    list of SQL files: -l </path/to/SQL.lst>
    credentials file:  -c </path/to/credentials.local.bash>
    credentials input: -u
  " 1>&2
  exit 1
}
while getopts "l:c:u" o; do
  case "${o}" in
    l)
      SQLLIST=${OPTARG}
      if [ ! -f "$SQLLIST" ]; then
        echo "ERROR: SQLLIST file doesn't exist: $SQLLIST"
        exit 1
      fi
      ;;
    c)
      CREDENTIALS=${OPTARG}
      if [ ! -f "$CREDENTIALS" ]; then
        echo "ERROR: CREDENTIALS file doesn't exist: $CREDENTIALS"
        exit 1
      else
        source "$CREDENTIALS"
      fi
      ;;
    u)
      echo "Please enter username: "
      read database_user
      echo "Please enter password: "
      stty_orig=`stty -g`
      stty -echo
      read database_password
      stty $stty_orig
      ;;
    *)
      usage
      ;;
esac
done
shift $((OPTIND-1))


# validate parameters
# http://stackoverflow.com/a/13864829/4233593
if [ -z "${SQLLIST+x}" ]; then
  echo "ERROR: SQL list required."
  usage
fi
if [ -z "${CREDENTIALS+x}" ] && [ -z "${database_user+x}" ]; then
  echo "ERROR: MySQL credentials required."
  usage
fi
if [ -z ${database_server+x} ]; then
  echo "ERROR: database_server is not set"
  exit 1
fi
if [ -z ${database_user+x} ]; then
  echo "ERROR: database_user is not set"
  exit 1
fi
if [ -z ${database_password+x} ]; then
  echo "ERROR: database_password is not set"
  exit 1
fi
if [ -z ${database_name+x} ]; then
  echo "ERROR: database_name is not set"
  exit 1
fi

# move to working directory
cd $( dirname "$SQLLIST" )

# execute SQL
# http://stackoverflow.com/a/10929511/4233593
while IFS='' read -r sql || [[ -n "$sql" ]]; do
  mysql --host="$database_server" --user="$database_user" --password="$database_password" --database="$database_name" < $sql
done < "$SQLLIST"

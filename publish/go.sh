#This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
#Please view license.txt in /tgsf_core/legal/license.txt or
#http://tgWebSolutions.com/opensource/tgsf/license.txt
#for complete licensing information.
#-------------------------------------------------------------
# This file only works with rsync.
# This works well on my Mac.  It should work well under Linux too.
# On windows you should look into cygwin.
# You are highly encouraged to acquire a mac for development tasks.

./prepare.sh

#dryrun=--dry-run
dryrun=
c=--compress
exclude=--exclude-from=rs_exclude.txt
pg="--no-p --no-g"
#delete=--delete
delete=
rsync_options=-Pav
rsync_local_path=../
rsync_server_string=user@host
rsync_server_path="/path/to/files/"

rsync $rsync_options $dryrun $delete $exclude $c $pg $rsync_local_path $rsync_server_string:$rsync_server_path

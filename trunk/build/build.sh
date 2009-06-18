#This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
#Please view license.txt in /tgsf_core/legal/license.txt or
#http://tgWebSolutions.com/opensource/tgsf/license.txt
#for complete licensing information.

#-------------------------------------------------------------
# This file only works with rsync.
# This works well on my Mac.  It should work well under Linux too.
# On windows you should look into cygwin.
# You are highly encouraged to acquire a mac for development tasks.

#this file has 2 sections.
# this first one builds a zip file of the core.
# the core does not have an index.php or an application folder
# the core is used for upgrading existing installations.

#------------------------------------------------------------------------
#------------------- Core -----------------------------------------------
#------------------------------------------------------------------------


exclude=--exclude-from=rs_exclude_core.txt
pg="--no-p --no-g"
rsync_options=-Pav
rsync_local_path=../
rsync_local_dest=./tgsf-core

zip_name=tgsf-core.zip

rsync $rsync_options  $exclude $c $pg $rsync_local_path $rsync_local_dest

rm $zip_name
zip -r9 $zip_name $rsync_local_dest

cd $rsync_local_dest
rm -Rf *
rm -Rf .*
cd ..
rmdir $rsync_local_dest


#------------------------------------------------------------------------
#--------------------Full------------------------------------------------
#------------------------------------------------------------------------

exclude=--exclude-from=rs_exclude_full.txt
pg="--no-p --no-g"
rsync_options=-Pav
rsync_local_path=../
rsync_local_dest=./tgsf

zip_name=tgsf.zip

rsync $rsync_options  $exclude $c $pg $rsync_local_path $rsync_local_dest

rm $zip_name
zip -r9 $zip_name $rsync_local_dest

cd $rsync_local_dest
rm -Rf *
rm -Rf .*
cd ..
rmdir $rsync_local_dest

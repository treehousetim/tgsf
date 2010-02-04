#This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
#Please view license.txt in /tgsf_core/legal/license.txt or
#http://tgWebSolutions.com/opensource/tgsf/license.txt
#for complete licensing information.
#-------------------------------------------------------------

# This works well on my Mac.  It should work well under Linux too.
# On windows you should look into cygwin.
# You are highly encouraged to acquire a mac for development tasks.
#------------------------------------------------------------------------

find .. -name "._*" -print0 | xargs -0 rm -Rf
find .. -name ".DS_Store" -print0 | xargs -0 rm -Rf

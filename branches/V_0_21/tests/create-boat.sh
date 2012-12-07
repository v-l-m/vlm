#!/bin/sh

rm -f vlm-cookiejar.ck

for i in `seq -w 1 200` ; do 

PSEUDO=`echo stress$i`
PASSWORD=`echo stress$i`

echo "Caring for $PSEUDO"
 
# create boat 
#curl -d myboat="myboat" -d pseudo="$PSEUDO" -d password="$PASSWORD" -d type="create" -d lang="en" -d submit="submit" http://testing.virtual-loup-de-mer.org/myboat.php  > foo

# login
curl -c vlm-cookiejar.ck -d pseudo="$PSEUDO" -d password="$PASSWORD" -d type="login" -d lang="en" http://testing.virtual-loup-de-mer.org/myboat.php  > foo

idu=`grep idu foo | grep value | head -1 | sed 's/.*value=\"\([0-9]*\).*/\1/'`
echo "IDU is $idu"

#curl -b vlm-cookiejar.ck -c vlm-cookiejar.ck -d type="unsubscribe" -d idusers="$idu" -d lang="en" http://testing.virtual-loup-de-mer.org/subscribe.php > foo

# subscribe to the mamba race
#curl -b vlm-cookiejar.ck -c vlm-cookiejar.ck -d idraces="20081101" -d idusers="" -d type="subscribe" -d lang="en" -d submit="Subscribe to this race" http://testing.virtual-loup-de-mer.org/myboat.php > foo

# adjust WP
curl -b vlm-cookiejar.ck -c vlm-cookiejar.ck -d targetlat="43" -d targetlong="5" -d targetandhdg="118.5" -d type="savemywp" http://testing.virtual-loup-de-mer.org/myboat.php > foo

# set ortho mode
#curl -b vlm-cookiejar.ck -c vlm-cookiejar.ck -d idusers="$idu" -d lang="en" -d pilotmode="orthodromic" http://testing.virtual-loup-de-mer.org/update_angle.php > foo
curl -b vlm-cookiejar.ck -c vlm-cookiejar.ck -d idusers="$idu" -d lang="en" -d pilotmode="bestvmg" http://testing.virtual-loup-de-mer.org/update_angle.php > foo

rm -f foo
done

rm -f vlm-cookiejar.ck

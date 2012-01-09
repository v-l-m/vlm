#!/usr/bin
echo "-- STARTING VLM in a Virtual Box         --"
VBoxManage startvm vboxvlm --type headless
echo "-- ...started... waiting to connect sshd --"
sleep 10
ssh vlm@vlm -p 2222

#!/bin/sh

VBOXNAME=$1

if test "$VBOXNAME" = ""; then
  echo "Ce script s'appelle avec le nom de l'instance Virtual Box en argument."
  echo "Il sert à fixer le port forwarding."
  exit 0
fi

VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guestssh/Protocol" TCP
VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guestssh/GuestPort" 22
VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guestssh/HostPort" 2222
VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guesthttp/Protocol" TCP
VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guesthttp/GuestPort" 80
VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guesthttp/HostPort" 8080
VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guestmysql/Protocol" TCP
VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guestmysql/GuestPort" 3306
VBoxManage setextradata $VBOXNAME "VBoxInternal/Devices/pcnet/0/LUN#0/Config/guestmysql/HostPort" 13306

echo "http: Le port 8080 local pointe sur le port 80 de votre machine virtuelle $VBOXNAME"
echo "ssh: Le port 2222 local pointe sur le port 22 de votre machine virtuelle $VBOXNAME"
echo "mysql: Le port 13306 local pointe sur le port 3306 de votre machine virtuelle $VBOXNAME"

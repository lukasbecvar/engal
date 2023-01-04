#!/bin/bash

# clear console in script start
clear

# print panel menu
echo "\033[33m\033[1m############################################################################\033[0m"
echo "\033[33m\033[1m##\033[0m                             \033[32mEngal API panel\033[0m                            \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m############################################################################\033[0m"
echo "\033[33m\033[1m##\033[0m                                                                        \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m1    -   Start dev server\033[0m        \033[33m2   -   Build production\033[0m            \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m                                                                        \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m                                                                        \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m3    -   Run installer\033[0m                                               \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m##\033[0m                                                                        \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m############################################################################\033[0m"
echo "\033[33m\033[1m##\033[0m   \033[33m0    -   Exit panel\033[0m                                                  \033[33m\033[1m##\033[0m"
echo "\033[33m\033[1m############################################################################\033[0m"

# stuck menu for select action
read selector

# clear console with select
clear

# selector methodes
case $selector in

	1*) # run dev server
		sh scripts/srv.sh
	;;
	2*) # run build structure
		sh scripts/build_prod.sh
	;;	
	3*) # run components installer
		sh scripts/install.sh
	;;
	0*) # exit this panel
		exit
	;;
	*) # vote not found error
		echo "\033[33mYour vote not found!\033[0m"
	;;
esac

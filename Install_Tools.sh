#!/bin/bash
# Made by Steven Sullivan
# Copyright Steven Sullivan Ltd
# Version: 1.0

if [ "x$(id -u)" != 'x0' ]; then
    echo 'Error: this script can only be executed by root'
    exit 1
fi

echo "Let's start..."

# Let's install the CSF Vesta UI!
function InstallVestaCPFrontEnd()
{
	echo "Install VestaCP Front..."
	
	cd /tmp
	mkdir /usr/local/vesta/web/list/tools
	wget https://raw.githubusercontent.com/SS88UK/VestaCP-Tools-Plugin/master/tools.zip
	unzip /tmp/tools.zip -d /usr/local/vesta/web/list/
	rm -f /tmp/tools.zip

	# Chmod files
	find /usr/local/vesta/web/list/tools -type d -exec chmod 755 {} \;
	find /usr/local/vesta/web/list/tools -type f -exec chmod 644 {} \;
	
	# Add the link to the panel.html file
	if grep -q 'Tools' /usr/local/vesta/web/templates/admin/panel.html; then
		echo 'Already there.'
	else
		sed -i '/<div class="l-menu clearfix noselect">/a <div class="l-menu__item <?php if($TAB == "TOOLS" ) echo "l-menu__item--active" ?>"><a href="/list/tools/"><?=__("Tools")?></a></div>' /usr/local/vesta/web/templates/admin/panel.html
	fi

	echo "Done! Check VestaCP!";
}

InstallVestaCPFrontEnd

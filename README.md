roundcube-plugins
=================

Plugins for RoundCube Webmail for integration with ServerGrove Hosting services.

Current plugins:

- password driver: Allows email users to change their email account passwords from inside the webmail
- sgcontrol_autoresponder: Allows email users to setup, activate and deactivate the autoresponder.

# Installation

To install the plugins, copy the directories/files inside roundcube into your RoundCube installation:

    $ cp -r roundcube/* /PATH/TO/ROUNDCUBE/

Then you will need to activate the plugins in your RoundCube configuration files:


	// config/main.inc.php

	// List of active plugins (in plugins/ directory)
	$rcmail_config['plugins'] = array('password', 'sgcontrol_autoresponder');

	// Password plugins specific settings
	$rcmail_config['password_driver'] = 'sgcontrol';



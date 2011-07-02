<?php
/**
   * The base configurations of the WordPress.
    *
	 * This file has the following configurations: MySQL settings, Table Prefix,
	  * Secret Keys, WordPress Language, and ABSPATH. You can find more information
	   * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
	    * wp-config.php} Codex page. You can get the MySQL settings from your web host.
		 *
		  * This file is used by the wp-config.php creation script during the
		   * installation. You don't have to use the web site, you can just copy this file
		    * to "wp-config.php" and fill in the values.
			 *
			  * @package WordPress
			   */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'oyvinbak');

/** MySQL database username */
define('DB_USER', 'oyv_wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'LGwW35HRPcjXNcmd');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
   * Authentication Unique Keys and Salts.
    *
	 * Change these to different unique phrases!
	  * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
	   * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
	    *
		 * @since 2.6.0
		  */
define('AUTH_KEY',         'a$q;1/:j*$`3xa?)Nk?)Bn@ 4gb;nG cPgVs,2g!5HU{+Q=AW)J+fI/h{)Y{?lj[');
	define('SECURE_AUTH_KEY',  'y@a75*0oE[wPB69t(>K/Hp8zfqFVb2=h-CvNZ4u)jwal>ZalFx8v-B`e3lBJvPCv');
	define('LOGGED_IN_KEY',    '**(.ppg0*9;r=ymxQvN&Fz#D#TZP2W3-<1WW,,Fn2jd}X@Z-tEo?5i`r1|96:N9+');
			define('NONCE_KEY',        'RC |H,0&kX#RS%7jvuAe1d(*ZoH:TPx^<A^lRTKbzv?h.Yc-q([+H>.j@G3wymLf');
					define('AUTH_SALT',        'w!~+;{eJ~j#jT4|?*B;tEy/46btF-%H(dp1._Q}K`<$vf?]:`j9k`/^XM@7lKE;-');
						define('SECURE_AUTH_SALT', '87!SGwve)q0:{EF.20|m67<{YQ,g:GE#%Ce6{VR[Diu[H[>,[{f|V=F-sa[Z0>K6');
					define('LOGGED_IN_SALT',   'QyR^=39BN>BFaMm~]S71ONtoxFVOXN2YZQ+3I!hbk-!wBnxWz)|E@,{~7Gd| ;HH');
				define('NONCE_SALT',       '3<97vjUc<j97.)Kr+{ i_g>9FxVdF@T$%1T^g;x~2!kBXy3vVl2T(zXQPTxHb{Q=');

				/**#@-*/

				/**
				   * WordPress Database Table prefix.
				    *
					 * You can have multiple installations in one database if you give each a unique
					  * prefix. Only numbers, letters, and underscores please!
					   */
				$table_prefix  = 'wp_';

				/**
				   * WordPress Localized Language, defaults to English.
				    *
					 * Change this to localize WordPress. A corresponding MO file for the chosen
					  * language must be installed to wp-content/languages. For example, install
					   * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
					    * language support.
						 */
				define('WPLANG', '');

				/**
				   * For developers: WordPress debugging mode.
				    *
					 * Change this to true to enable the display of notices during development.
					  * It is strongly recommended that plugin and theme developers use WP_DEBUG
					   * in their development environments.
					    */
				define('WP_DEBUG', false);

				/* That's all, stop editing! Happy blogging. */

				/** Absolute path to the WordPress directory. */
				if ( !defined('ABSPATH') )
						define('ABSPATH', dirname(__FILE__) . '/');

				/** Sets up WordPress vars and included files. */
				require_once(ABSPATH . 'wp-settings.php');


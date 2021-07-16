<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', '\var\www\html\wp-content\plugins\wp-super-cache/' );
define( 'DB_NAME', 'globaldea' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'WP_HOME', 'http://44.192.89.155' );
define( 'WP_SITEURL', 'http://44.192.89.155' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'a|}S4}jfb|x6gb-:~:QS:qm}L,|+,OM.e mkIp8NQE>?R%iBCz5AA[Vy5f*=zmVc' );
define( 'SECURE_AUTH_KEY',  'f(5q&=mnT}T#f|=p]SAU<w%FqSZ@N|VM^!2MKX;m>GUIHV}um*>1n <kO0y< ZvR' );
define( 'LOGGED_IN_KEY',    '+IS-Q0au@%dQ908d;^nl}]c.vFcYd|8s&rc(~UkepT,Aot3d%n/0e^&]GnV%8>dw' );
define( 'NONCE_KEY',        'yic`fM38TSnDlTs#o J>iU^`RaZu%Bz;bl~~7nv`e/L9U|7{KtHJ]L^hu0h$S6]l' );
define( 'AUTH_SALT',        'Xog3_/P%Ju^c#GgE}nI[GmhJr{N<`y^: .kch!r)C1>$YP{>UaQ$I*V6v,wSQyrh' );
define( 'SECURE_AUTH_SALT', '^WBYW$jmQj{P!MCss?0zUj!3}@tW4kCqZAp7[WTP, hhw#CEIkfD8`(kYZ7N`VS#' );
define( 'LOGGED_IN_SALT',   ';T^J^5-.&kSy}>z^Q>#f0AJuO?ZV~2DsK></Mhc^TAt2%d+c~LxB `RX_<7kM5<k' );
define( 'NONCE_SALT',       'Zxh^eOzads9 gj:Io-FB<St14->zI*u<iNk`2qFUuw!Wpwdh/z+CkfgWhD`Vl}mE' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

<?php
/*
Plugin Name: UserAgent theme switcher
Plugin URI: http://www.indalcasa.com
Description: This plugins switch theme for any useragent, specialy for iphone, chrome mobile, opera mobile, etc.
Author: Juan Benavides Romero
Author URI: http://www.indalcasa.com
Version: 1.1.2
*/
class UserAgentThemeSwitcher {
    /**
     * Ruta base del plugin
     * @var string
     */
    private $pluginPath;
    /**
     * Estructura de directorios del plugin
     * @var string
     */
    private $path;
    /**
     * Carpeta donde se encuentran alojados los templates del plugin
     * @var string
     */
    private $templatePath = 'template';
    /**
     * Objeto de conexion a la base de datos
     * @var wpdb
     */
    private $dbconnection;


    private $tablePrefix;

    private $theme = null;

    private $userAgent = null;

    private $version = 4;



    private static $singleton;



    public function __construct() {
	UserAgentThemeSwitcher::$singleton = $this;
    }


    /**
     * Metodo que inicializa toda la configuracion del plugin
     */
    public function initialize() {
	ini_set('display_errors', 'true');
	$this->pluginPath = basename(dirname(__FILE__));
	$this->path = dirname(__FILE__).'/';
	global $wpdb;
	global $table_prefix;
	$this->dbconnection = $wpdb;
	$this->tablePrefix = $table_prefix;
	$this->checkDataBase();

	$this->parseBrowser();

	load_plugin_textdomain('Theme Switcher','wp-content/plugins/'.$this->pluginPath, $this->pluginPath);

	add_action('admin_menu', array($this, 'createMenu'));
	add_action('init', array($this, 'pageProcess'));
	add_filter('template', array(&$this, 'switchTemplate'));
	add_filter('stylesheet', array(&$this, 'switchStylesheet'));
    }//initialize





    public function createMenu() {
	add_menu_page('Theme Switcher', 'Theme Switcher', 'manage_options', 'useragent-template', array($this, 'processShowUserAgent'), null);
    }//createMenu





    private function checkDataBase() {
	$installedVersion = get_option('usts_version');

	if($installedVersion == null) {
	    $isConfigurated = false;

	    $tables = $this->dbconnection->get_results("show tables");
	    foreach ($tables as $table) {
		foreach ($table as $value) {
		    if ($value == $this->tablePrefix.'_usts_useragents') {
			$isConfigurated = true;
		    }
		}
	    }

	    if($isConfigurated === false) {
		$sql = 'CREATE TABLE IF NOT EXISTS '.$this->tablePrefix.'usts_useragents (`id` INT NOT NULL AUTO_INCREMENT, `useragent` VARCHAR( 255 ) NOT NULL, `browserId` INT NULL, PRIMARY KEY (  `id` )) ENGINE = MYISAM;';
		$this->dbconnection->get_results($sql);
		$sql = 'CREATE TABLE IF NOT EXISTS `'.$this->tablePrefix.'usts_browsers` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `name` varchar(50) NOT NULL,  `icon` varchar(50) DEFAULT NULL,  `theme` varchar(50) DEFAULT NULL,  `regex` varchar(255) DEFAULT NULL,  PRIMARY KEY (`id`),  KEY `name` (`name`)) ENGINE=MyISAM;';
		$this->dbconnection->get_results($sql);

		add_option('usts_debug', 'false');
	    }

	    add_option('usts_version', $this->version);

	    $installedVersion = 0;
	}

	if($installedVersion != $this->version) {
	    $this->generateBrowsers($installedVersion);

	    update_option('usts_version', $this->version);
	}


    }//checkDataBase



    private function generateBrowsers($oldVersion) {
	$updateDB = false;
	$sql = 'INSERT INTO `wp_usts_browsers` (`name`, `icon`, `theme`, `regex`) VALUES ';

	if($oldVersion < 1) {
	    $sql .= '("Google Chrome", NULL, NULL, "Mozilla\\\\/5\.0 \\(.*\\) AppleWebKit\\\\/.* \\\\(KHTML, like Gecko\\\\) Chrome\\\\/.* Safari\\\\/.*"),';
	    $updateDB = true;
	}
	if($oldVersion < 2) {
	    $sql .= $this->generateSQLBrowser('Safari', 'Mozilla\/5\.0 \(.*; .*\) AppleWebKit\/.* \(KHTML, like Gecko\) Version\/.* Safari\/.*');
	    $sql .= $this->generateSQLBrowser('Firefox', 'Mozilla\/5\.0 \(.*\) Gecko\/.* Firefox\/.*');
	    $updateDB = true;
	}
	if($oldVersion < 3) {
	    $sql .= $this->generateSQLBrowser('Safari Mobile', 'Mozilla\/5.0 \(.*) AppleWebKit\/.* \(KHTML, like Gecko\) Version\/.* Mobile Safari\/.*');
	    $sql .= $this->generateSQLBrowser('Internet Explorer 6', 'Mozilla\/4\.0 \(compatible; MSIE 6\.0;.*\).*');
	    $sql .= $this->generateSQLBrowser('Internet Explorer 7', 'Mozilla\/4\.0 \(compatible; MSIE 7\.0;.*\).*');
	    $sql .= $this->generateSQLBrowser('Internet Explorer 8', 'Mozilla\/4\.0 \(compatible; MSIE 8\.0;.*\).*');
	    $sql .= $this->generateSQLBrowser('Opera Mini', 'Opera\/.* \(.*\Opera Mini\/.*\).*');

	    $updateDB = true;
	}

	if($oldVersion < 4 && $this->version == 4) {
	    $this->updateBrowsers('Safari Mobile', 'Mozilla\/5.0 \(.*\) AppleWebKit\/.* \(KHTML, like Gecko\) Version\/.* Mobile Safari\/.*');
	    $this->updateBrowsers('Internet Explorer 6', 'Mozilla\/4\.0 \(compatible; MSIE 6\.0;.*\).*');
	    $this->updateBrowsers('Internet Explorer 7', 'Mozilla\/4\.0 \(compatible; MSIE 7\.0;.*\).*');
	    $this->updateBrowsers('Internet Explorer 8', 'Mozilla\/4\.0 \(compatible; MSIE 8\.0;.*\).*');
	    $this->updateBrowsers('Opera Mini', 'Opera\/.* \(.*Opera Mini\/.*\).*');
	    //update_option('usts_version', 3);
	}

	if($oldVersion < 5 && $this->version == 5) {
	    $this->updateBrowsers('Safari Mobile', 'Mozilla\/5.0 \(.*\) AppleWebKit\/.* \(KHTML, like Gecko\) Version\/.* Mobile Safari\/.*');
	    $this->updateBrowsers('Opera Mini', 'Opera\/.* \(.*Opera Mini\/.*\).*');
	}


	if($updateDB === true) {
	    $sql = substr($sql, 0, -1);
	    $this->dbconnection->get_results($sql);
	}
    }


    private function updateBrowsers($name, $regex) {
	$regex = str_replace('\\', '\\\\', $regex);
	$sql = 'UPDATE `wp_usts_browsers` SET regex = "'.$regex.'" WHERE name = "'.$name.'"';
	
	$this->dbconnection->get_results($sql);
    }


    private function generateSQLBrowser($name, $regex) {
	$regex = str_replace('\\', '\\\\', $regex);

	$sql = '("'.$name.'", NULL, NULL, "'.$regex.'"),';

	return $sql;
    }



    private function addTemplateToBrowser($browser, $theme) {
	$sql = 'UPDATE '.$this->tablePrefix.'usts_browsers SET theme = "'.$theme.'" WHERE id = '.$browser;
	$this->dbconnection->get_results($sql);
    }



    private function getBrowsers() {
	$sql = 'SELECT * FROM '.$this->tablePrefix.'usts_browsers';
	$results = $this->dbconnection->get_results($sql);

	return $results;
    }


    private function deleteBrowser($browser) {
	$sql = 'UPDATE '.$this->tablePrefix.'usts_browsers SET theme = NULL WHERE id = '.$browser;
	$this->dbconnection->get_results($sql);
    }


    private function getUserAgents() {
	$sql = 'SELECT * FROM '.$this->tablePrefix.'usts_useragents';
	return $this->dbconnection->get_results($sql);
    }


    private function truncateUserAgents() {
	$sql = 'TRUNCATE TABLE `'.$this->tablePrefix.'usts_useragents`';
	$this->dbconnection->get_results($sql);
    }


    public function processShowUserAgent() {
	$usts_action = $this->getParameter('usts_action');

	if($usts_action != null) {
	    if($usts_action == 'addbrowser') {
		$uats_browser = $this->getParameter('uats_browser');
		$uats_theme = $this->getParameter('uats_theme');

		$this->addTemplateToBrowser($uats_browser, $uats_theme);
	    } elseif($usts_action == 'updatedebug') {
		$debug = $this->getParameter('uats_debug');

		if($debug == null) {
		    $debug = 'false';
		} else {
		    $debug = 'true';
		}

		update_option('usts_debug', $debug);
	    } elseif($usts_action == 'deletebrowser') {
		$this->deleteBrowser($this->getParameter('browser'));
	    } elseif($usts_action == 'truncateua') {
		$this->truncateUserAgents();
	    }
	}

	$useragents = null;
	if(get_option('usts_debug') == 'true') {
	    $useragents = $this->getUserAgents();
	}
	$browsers = $this->getBrowsers();
	$themes = get_themes();
	$debug = get_option('usts_debug');
	include('useragent-template.php');
    }



    public function pageProcess() {
	$debugmode = get_option('usts_debug');

	if($debugmode == 'true' && $this->userAgent == null) {
	    $useragent = $_SERVER['HTTP_USER_AGENT'];

	    $sql = 'SELECT id FROM '.$this->tablePrefix.'usts_useragents where useragent = "'.$useragent.'"';
	    $exists = $this->dbconnection->get_results($sql);

	    if($exists == null) {
		$sql = 'INSERT INTO '.$this->tablePrefix.'usts_useragents (useragent) VALUES ("'.$useragent.'")';
		$this->dbconnection->get_results($sql);
	    }
	}
    }




    public function switchTemplate($template) {
	if($this->theme != null) {
	    $theme = get_theme($this->theme);
	} else {
	    return $template;
	}

	return $theme['Template'];
    }




    public function switchStylesheet($stylesheet = '') {
	if($this->theme != null) {
	    $theme = get_theme($this->theme);
	} else {
	    return $stylesheet;
	}

	return $theme['Stylesheet'];
    }



    public function parseBrowser($userAgent = null) {
	if($userAgent == null) {
	    $userAgent = $_SERVER['HTTP_USER_AGENT'];
	}

	$sql = 'SELECT name, theme, regex FROM '.$this->tablePrefix.'usts_browsers';
	$results = $this->dbconnection->get_results($sql);

	for($i = 0; $i < count($results); $i++) {
	    if($results[$i]->regex != '') {
		if(@preg_match('/'.$results[$i]->regex.'/Usi', $userAgent)) {
		    $this->userAgent = $results[$i]->name;
		    if($results[$i]->theme != '') {
			$this->theme = $results[$i]->theme;
		    }
		    break;
		}
	    }
	}
    }



    /**
     * Metodo que se utiliza para recoger los parametros tanto los que llegan
     * por GET como por POST
     * @param string $parameterName Nombre del parametro a recoger
     * @param bool $isNull Si el parametro no existe devuelve null, sino devuelve ''
     * @return string Parametro procesado
     */
    private function getParameter($parameterName, $isNull = false) {
	if(isset($_REQUEST[$parameterName])) {
	    return $_REQUEST[$parameterName];
	} else {
	    if($isNull === true) {
		return null;
	    } else {
		return '';
	    }
	}
    }//getParameter
}//WpMch

$wpUserAgentThemeSwitcher = new UserAgentThemeSwitcher();
$wpUserAgentThemeSwitcher->initialize();
unset($wpUserAgentThemeSwitcher);
?>
<?php
/*
* Written by Levi Zoesch (Krayvok)
* Archetype Studios (C)
* Server Query Tool
*/
class ServerQuery {
	
	private $AppID;
	private $API;
	private $LiveVersion;
	private $Title;
	private $Midair_Logo;
	private $Midair_LogoLocation;
	private $BackgroundImage;
	private $BackgroundLocation;
	private $FormatType;
	private $Filter;
	private $URL;

	function __construct() {
		
		/*
		* Midair Steam Application ID
		* @Constant
		*/
		$this->AppID = APPID;
		
		/*
		* Midair Steam Publisher Web API Key
		* @Constant
		*/
		$this->API = API_KEY;
		
		/*
		* Midair LIVE Version
		* @Constant
		*/
		$this->LiveVersion = LIVE_VERSION;
		
		/*
		* Web Page - Title
		*/
		$this->Title = 'Midair: Server List';
		
		/*
		* Midair Logo & Location
		*/
		$this->Midair_Logo = 'logo.png';
		$this->Midair_LogoLocation = BASE_URL . IMG_DIR;
		
		/*
		* Background Image & Location
		*/
		$this->BackgroundImage = 'bg1.jpg';
		$this->BackgroundLocation = BASE_URL . IMG_DIR;
		
		/*
		* Steam GetServerList() API Call
		*/
			// format type
			$this->FormatType = 'json';
			// filter Midair
			$this->Filter = '%5Cgamedir%5Cmidair';
			// Steam API URL
			$this->Steam_URL = 'https://api.steampowered.com/';
			// build GameServerList required parameters.
			$this->GameServersService = $this->Steam_URL . 'IGameServersService/GetServerList/v1/?format=' . $this->FormatType . '&filter=' . $this->Filter . '&key=' . API_KEY;
			// Execute QUery
			$this->ServerList = $this->curlData();	
		
	}
	
	function curlData() {
		/*
		* Curl Steam API JSON.
		*/
		$c = curl_init();
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($c, CURLOPT_URL, $this->GameServersService);
		$d = curl_exec($c);
		curl_close($c);
	  
	  	$a = json_decode($d, JSON_PRETTY_PRINT);
		$b = call_user_func_array('array_merge', $a);
		$e = call_user_func_array('array_merge', $b);
		
		return $e; 
		
	}
		
	function validateVersion($options) {
		
		$html = NULL;
		/*
		* Validate that the LIVE Game Version 
		* matches the servers that have been queried.
		* If the version does not match "Midair Live" 
		* then the server will appear to be "Outdated"
		*/
		$version = $options['version'];
		if ($version === $this->LiveVersion) { $html = '<h6><span class="badge badge-success">Ok</span></h6>'; }
		else { $html = '<h6><span class="badge badge-danger">Outdated</span></h6>'; }
		return $html;	
	}
	
	function validatePrivate($key) {
		/*
		* Insinde the json data that is curled from Steam API GameServerList()
		* There appears to be a key that is generated only if a user
		* is playing a Training Map, or a Private Game.
		*/
		if (strpos($key, 'P2PADDR') !== false) { return true; }	
		else { return false; }
	}
	
	function buildList($options) {
		
		$html = NULL;
			
		/*
		* We will build the server list with a <table> row markup.
		*/

		$sc = 0;
		$pc = 0;
		$r = $options['data'];
		$VersionCheck = NULL;
		
		foreach ($r as $s) {
			
			if ($s['appid'] == $this->AppID) {
			
				if (!$this->validatePrivate($s['gametype'])) {
					
					$VersionCheck = $this->validateVersion($s);
							
					$html .= ''
					. '<tr>'
						  . '<td>'.$s['name'].'</td>'
						  . '<td>'.$s['players'].' / ' . $s['max_players'].'</td>'
						  . '<td>'.$s['map'].'</td>'
						  . '<td>'.$s['addr'].'</td>'
						  . '<td>'.$VersionCheck.'</td>'
						.'</tr>' 
					. '';	
					
					$pc += $s['players'];
					$sc++;
					
				}
			}
		}
		
		return array('list' => $html, 'server_count' => $sc, 'player_count' => $pc);
		
	}
	
	function buildPage() {
		
		$html = NULL;
		/*
		* Build generalized HTML Markup
		*/

		$html = ''
			. '<!doctype html>'
			. '<html>'
			. '<head>'
			. '<meta charset="utf-8">'
			. '<title>' . $this->Title . '</title>'
			. '</head>'
			. '<link rel="stylesheet" type="text/css" href="////maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">'
			. '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css">'
			. '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.0/css/responsive.bootstrap.min.css">'
			. '<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css">'
			. '<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>'
  			. '<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>'
			. '<style>body { background-color: #6792A4; }</style>'
			. '<body>'
			. $this->html_DisplayServerTable( $this->ServerList )
			. '</body>'
			. '</html>'
			.'';
		
		return $html;
	
	}
		
	function html_DisplayServerTable($r) {
		
		/*
		* Build the Server Query into a Table HTML Markup,
		* Count players, and servers to return totals to
		* be displayed to the end user.
		*/ 

		$data = $this->buildList(array('data' => $r));
		
		// Footer text to display server/player totals.
		$FooterSTMT = 'There are a total of <b>'. $data['server_count'] .'</b> servers, and <b>'.$data['player_count'].'</b> players online.';
		
		$html = NULL;
		
		$html .= ''
			.'<center><img src="'. $this->Midair_LogoLocation . $this->Midair_Logo.'" width="30%"></center>'
			.'<div class="container">'
			.'<table id="servers" width="100%" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0">'
			.'<thead>'
			.'<th>Name</th>'
			.'<th>Players</th>'
			.'<th>Map</th>'
			.'<th>IP</th>'
			.'<th>Version</th>'
			.'</thead>'
			.'<tbody>'
			. $data['list']
			.'</tbody>' 
			.'</table>'
			.'<center><em>' . $FooterSTMT . '</em></center>'
			.'</div>'
			.'<br><br>';
			
			return $html;
		
	}
	
}
?>
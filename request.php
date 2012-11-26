<?php
/*
*	Code by KroniX
*	E-mail: KroniX@rp-welten.net
*	Website: http://rp-welten.net
*	Help by Black, Black@rp-welten.net
*/
	$startzeit = explode(" ", microtime());
	$startzeit = $startzeit[0]+$startzeit[1];
	// Include PHP-Files
	require 'config.php';
	
	global $core, $db;
	
	$chars = $accounts = $guilds = array();
	$chars_online = 0;
	
	switch($core['type']){
		case 0:
			$slot_place = array(
				0		=> 'head',
				1		=> 'neck',
				2 		=> 'shoulders',
				3 		=> 'body',
				4 		=> 'chest',
				5 		=> 'waist',
				6 		=> 'legs',
				7 		=> 'feet',
				8 		=> 'wrists',
				9 		=> 'hands',
				14 		=> 'back',
				15 		=> 'mainhand',
				16 		=> 'offhand',
				17 		=> 'ranged',
				18 		=> 'tabard',
			);
			// Accounttabellen Abfrage
			$sql = "SELECT a.id, a.username, a.email, a.joindate, a.last_login, a.mutetime,
					x.gmlevel,
					b.bandate, b.unbandate, b.bannedby, b.banreason, b.active
					FROM {$core['account_db']}.trinity_account a
					LEFT JOIN {$core['account_db']}.trinity_account_access x ON (a.id = x.id AND x.RealmID = ".$core['realm_id'].")
					LEFT JOIN {$core['account_db']}.trinity_account_banned b ON (a.id = b.id)";
			$result = mysql_query($sql);
			while($account = mysql_fetch_assoc($result)){
				$accounts[] = array(
					'id'			=> $account['id'],
					'name'			=> $account['username'],
					'email'			=> $account['email'],
					'joindate'		=> strtotime($account['joindate']),
					'gm'			=> ($account['gmlevel']) ? $account['gmlevel'] : 0,
					'banned_time'	=> ($account['active']) ? strtotime($account['unbandate']) : 0,
					'banned_by'		=> $account['bannedby'],
					'banned_reason'	=> $account['banreason'],
					'lastlogin'		=> strtotime($account['last_login']),
				);
			}
			
			
			// Gildentabelle Abfragen
			$sql = "SELECT * FROM {$core['account_db']}.trinity_guild";
			$result = mysql_query($sql);
			while($guild = mysql_fetch_assoc($result)){
				$guilds[$guild['guildid']] = array(
					'id'			=> $guild['guildid'],
					'name'			=> $guild['name'],
					'leader'		=> $guild['leaderguid'],
					'style'			=> $guild['guildid'],
					'info'			=> $guild['info'],
					'motd'			=> $guild['motd'],
					'create_date'	=> $guild['createdate'],
					'money'			=> $guild['BankMoney'],
					'ranks'			=> array(),
				);
				
				// Gildenränge
				$sql = "SELECT rname, rights, BankMoneyPerDay
						FROM {$core['char_db']}.trinity_guild_rank
						WHERE guildid = ".$guild['guildid'];
				$result2 = mysql_query($sql);
				while($rank = mysql_fetch_assoc($result2)){
					$guilds[$guild['guildid']]['ranks'][] = array(
						'name'			=> $rank['rname'],
						'rights'		=> $rank['rights'],
						'money_allow'	=> $rank['BankMoneyPerDay'],
					);
				}
			}
			
			// Charaktertabelle Abfragen
			$sql = "SELECT c.guid, c.account, c.name, c.gender, c.money, c.playerBytes, c.playerBytes2,
					c.race, c.class, c.logout_time,
					c.position_x, c.position_y, c.position_z, c.map, c.orientation, c.online, c.totaltime,
					b.unbandate, b.bannedby, b.banreason, b.active,
					m.guildid
					FROM {$core['char_db']}.trinity_characters c
					LEFT JOIN {$core['char_db']}.trinity_character_banned b ON (c.guid = b.guid)
					LEFT JOIN {$core['char_db']}.trinity_guild_member m ON (c.guid = m.guid)
					";
			$result = mysql_query($sql);
			while($char = mysql_fetch_assoc($result)){
					if($char['online']) $chars_online++;
					$chars[$char['guid']] = array(
						'guid'			=> $char['guid'],
						'online'		=> $char['online'],
						'name'			=> $char['name'],
						'race'			=> $char['race'],
						'class'			=> $char['class'],
						'gender'		=> $char['gender'],
						'positionX'		=> $char['position_x'],
						'positionY'		=> $char['position_y'],
						'positionZ'		=> $char['position_z'],
						'orientation'	=> $char['orientation'],
						'map'			=> $char['map'],
						'banned_time'	=> ($char['active']) ? strtotime($char['unbandate']) : 0,
						'banned_by'		=> $char['bannedby'],
						'banned_reason'	=> $char['banreason'],
						'lastlogin'		=> ($char['logout_time']) ? $char['logout_time'] : 0,
						'playedtime'	=> ($char['totaltime']) ? $char['totaltime'] : 0,
						'guild'			=> ($char['guildid']) ? $char['guildid'] : 0,
						'friends'		=> array(),
						'equip'			=> array(),
					);

					$chars[$char['guid']]['equip'] = array(
						'head'		=> 0,
						'shoulders'	=> 0,
						'body'		=> 0,
						'chest'		=> 0,
						'waist'		=> 0,
						'legs'		=> 0,
						'feet'		=> 0,
						'wrists'	=> 0,
						'hands'		=> 0,
						'back'		=> 0,
						'mainhand'	=> 0,
						'offhand'	=> 0,
						'range'		=> 0,
						'tabard'	=> 0,
					);

					// Equipment
					$sql = "SELECT i.slot, r.displayid
							FROM {$core['char_db']}.trinity_character_inventory i
							LEFT JOIN {$core['char_db']}.trinity_item_instance n ON (i.item = n.guid)
							LEFT JOIN {$core['world_db']}.rpw_item r ON (n.itemEntry = r.entry)
							WHERE slot < 19 AND i.guid = ".$char['guid'];
					$result2 = mysql_query($sql);
					while($item = mysql_fetch_assoc($result2)){
						$item['slot'] = strtr($item['slot'], $slot_place);
						$chars[$char['guid']]['equip'][$item['slot']] = ($item['displayid']) ? $item['displayid'] : 0;
					}
					
					
					// Friendloop
					$sql = "SELECT * FROM {$core['char_db']}.trinity_character_social WHERE guid = ".$char['guid'];
					$result2 = mysql_query($sql);
					while($friend = mysql_fetch_assoc($result2)){
						$chars[$char['guid']]['friends'][] = array(
							'guid'	=> $friend['friend'],
							'flags'	=> $friend['flags'],	// 0: unused, 1:friend, 2:ignored, 3:ignored+friend
							'note'	=> $friend['note'],
						);
					}
			}
		break;
	}
	// Ladezeit berechnen
	$endzeit=explode(" ", microtime());
	$endzeit=$endzeit[0]+$endzeit[1];
	$loadtime = round($endzeit - $startzeit,5);
	
	// XML Ausgabe
	header("Content-Type:text/xml");
	$respond = '<?xml version="1.0" encoding="utf-8" standalone="yes"?><serverinfo loadtime="' . $loadtime . 's">';
	$respond .= '<accounts count="'.sizeof($accounts).'">';
	// Accountlisting
	foreach($accounts as $account){
	$respond .= "
		<account>
			<id>{$account['id']}</id>
			<name>{$account['name']}</name>
			<joindate>{$account['joindate']}</joindate>
			<gm>{$account['gm']}</gm>
			<banned>{$account['banned_time']}</banned>
			<bannedby>{$account['banned_by']}</bannedby>
			<banreason><![CDATA[{$account['banned_reason']}]]></banreason>
			<lastlogin>{$account['lastlogin']}</lastlogin>
		</account>";
	}
	$respond .= '</accounts>';
	// Gildenlisting
	$respond .= '<guilds count="'.sizeof($guilds).'">';
	foreach($guilds as $guild){
		$ranks = '';
		
		foreach($guild['ranks'] as $rank){
			$ranks .= "
				<rank>
					<name><![CDATA[{$rank['name']}]]></name>
					<rights>{$rank['rights']}</rights>
					<moneyallow>{$rank['money_allow']}</moneyallow>
				</rank>
			";
		}
		
		$respond .= "
			<guild>
				<id>{$guild['id']}</id>
				<name><![CDATA[{$guild['name']}]]></name>
				<leader>{$guild['leader']}</leader>
				<style>{$guild['style']}</style>
				<info><![CDATA[{$guild['info']}]]></info>
				<motd><![CDATA[{$guild['motd']}]]></motd>
				<createdate>{$guild['create_date']}</createdate>
				<money>{$guild['money']}</money>
				<ranks>{$ranks}</ranks>
			</guild>
		";
	}
	$respond .= '</guilds>';
	$respond .= '<characters count="'.sizeof($chars).'" online="'.$chars_online.'">';
	// Charakterlisting
	foreach($chars as $char){
		$equip = $friends = '';
		foreach($char['friends'] as $friend){
			$friends .= "
						<friend>
							<guid>{$friend['guid']}</guid>
							<flag>{$friend['flags']}</flag>
							<note><![CDATA[{$friend['note']}]]></note>
						</friend>
						";
		}
		
		$equip = "
				<head>{$char['equip']['head']}</head>
				<shoulders>{$char['equip']['shoulders']}</shoulders>
				<body>{$char['equip']['body']}</body>
				<chest>{$char['equip']['chest']}</chest>
				<waist>{$char['equip']['waist']}</waist>
				<legs>{$char['equip']['legs']}</legs>
				<feet>{$char['equip']['feet']}</feet>
				<wrists>{$char['equip']['wrists']}</wrists>
				<hands>{$char['equip']['hands']}</hands>
				<back>{$char['equip']['back']}</back>
				<mainhand>{$char['equip']['mainhand']}</mainhand>
				<offhand>{$char['equip']['offhand']}</offhand>
				<range>{$char['equip']['range']}</range>
				<tabard>{$char['equip']['tabard']}</tabard>
				";
		
		$respond .= "
			<character>
				<guid>{$char['guid']}</guid>
				<online>{$char['online']}</online>
				<name>{$char['name']}</name>
				<race>{$char['race']}</race>
				<class>{$char['class']}</class>
				<gender>{$char['gender']}</gender>
				<posX>{$char['positionX']}</posX>
				<posY>{$char['positionY']}</posY>
				<posZ>{$char['positionZ']}</posZ>
				<orientation>{$char['orientation']}</orientation>
				<map>{$char['map']}</map>
				<banned>{$char['banned_time']}</banned>
				<bannedby>{$char['banned_by']}</bannedby>
				<banreason><![CDATA[{$char['banned_reason']}]]></banreason>
				<lastlogin>{$char['lastlogin']}</lastlogin>
				<playedtime>{$char['playedtime']}</playedtime>
				<guild>{$char['guild']}</guild>
				<friends>{$friends}</friends>
				<equip>{$equip}</equip>
			</character>";
	}
	$respond .= '</characters>';
	$respond .= '</serverinfo>';
	
	echo utf8_encode($respond);
?>
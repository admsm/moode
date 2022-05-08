<?php
/**
 * moOde audio player (C) 2014 Tim Curtis
 * http://moodeaudio.org
 *
 * This Program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3, or (at your option)
 * any later version.
 *
 * This Program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

 require_once dirname(__FILE__) . '/../inc/playerlib.php';

 if (isset($_GET['cmd']) && $_GET['cmd'] === '') {
 	workerLog('playlist.php: Error: $_GET cmd is empty or missing');
 	exit(0);
 }

$sock = GetMpdSock();

 //
 // COMMANDS
 //

switch ($_GET['cmd']) {
	case 'get_playqueue':
		echo json_encode(getPlayqueue($sock));
		break;
	case 'delete_playqueue_item':
		sendMpdCmd($sock, 'delete ' . $_GET['range']);
		$resp = readMpdResp($sock);
		break;
	case 'move_playqueue_item':
		sendMpdCmd($sock, 'move ' . $_GET['range'] . ' ' . $_GET['newpos']);
		$resp = readMpdResp($sock);
		break;
	case 'get_playqueue_item_file': // For Clock Radio
		sendMpdCmd($sock, 'playlistinfo ' . $_GET['songpos']);
		$resp = readMpdResp($sock);

		$array = array();
		$line = strtok($resp, "\n");

		while ($line) {
			list($element, $value) = explode(': ', $line, 2);
			$array[$element] = $value;
			$line = strtok("\n");
		}

		echo json_encode($array['file']);
		break;
	case 'add_item':
	case 'add_item_next':
		 $status = parseStatus(getMpdStatus($sock));
		 $cmds = array(addItemToQueue($_POST['path']));
		 if ($_GET['cmd'] == 'add_item_next') {
			 array_push($cmds, 'move ' . $status['playlistlength'] . ' ' . ($status['song'] + 1));
		 }
		 chainMpdCmds($sock, $cmds);
		 break;
	case 'play_item':
	case 'play_item_next':
		// Search the Queue for the item
		$search = strpos($_POST['path'], 'RADIO') !== false ? parseDelimFile(file_get_contents(MPD_MUSICROOT . $_POST['path']), '=')['File1'] : $_POST['path'];
		$result = findInQueue($sock, 'file', $search);

		if (isset($result['Pos'])) {
			// Play already Queued item
			sendMpdCmd($sock, 'play ' . $result['Pos']);
			$resp = readMpdResp($sock);
		} else {
			// Otherwise play the item after adding it to the Queue
			$status = parseStatus(getMpdStatus($sock));
			$cmds = array(addItemToQueue($_POST['path']));
			if ($_GET['cmd'] == 'play_item_next') {
				$pos = isset($status['song']) ? $status['song'] + 1 : $status['playlistlength'];
				array_push($cmds, 'move ' . $status['playlistlength'] . ' ' . $pos);
			} else {
				$pos = $status['playlistlength'];
			}
			array_push($cmds, 'play ' . $pos);
			chainMpdCmds($sock, $cmds);
		}
		break;
	/*case 'clear_add_item':*/
	case 'clear_play_item':
	 	$cmds = array('clear');
		array_push($cmds, addItemToQueue($_POST['path']));
		if ($_GET['cmd'] == 'clear_play_item') {
			array_push($cmds, 'play');
		}
		chainMpdCmds($sock, $cmds);
		playerSession('write', 'toggle_songid', '0');
		break;
	 // Queue commands for a group of songs: Genre, Artist or Albums in Tag/Album view
	case 'add_group':
	case 'add_group_next':
		$status = parseStatus(getMpdStatus($sock));
		$cmds = addGroupToQueue($_POST['path']);
		if ($_GET['cmd'] == 'add_group_next') {
			array_push($cmds, 'move ' . $status['playlistlength'] . ':' .
				($status['playlistlength'] + count($_POST['path'])) . ' ' . ($status['song'] + 1));
		}
		chainMpdCmds($sock, $cmds);
		break;
	case 'play_group':
	case 'play_group_next':
		// Search the Queue for the group
		sendMpdCmd($sock, 'lsinfo "' . $_POST['path'][0] . '"');
		$album = parseDelimFile(readMpdResp($sock), ': ')['Album'];
		$result = findInQueue($sock, 'album', $album);
		// Group is already in the Queue if first and last file exist sequentially
		$last = count($_POST['path']) - 1;
		if ($_POST['path'][0] == $result[0]['file'] && $_POST['path'][$last] == $result[$last]['file']) {
			$pos = $result[0]['Pos'];
			sendMpdCmd($sock, 'play ' . $pos);
			$resp = readMpdResp($sock);
		} else {
			// Otherwise play the group after adding it to the Queue
			$status = parseStatus(getMpdStatus($sock));
			$cmds = addGroupToQueue($_POST['path']);
		 	if ($_GET['cmd'] == 'play_group_next') {
				$pos = isset($status['song']) ? $status['song'] + 1 : $status['playlistlength'];
				if ($pos != 0) {
					array_push($cmds, 'move ' . $status['playlistlength'] . ':' .
						($status['playlistlength'] + count($_POST['path'])) . ' ' . ($status['song'] + 1));
				}
			} else {
				$pos = $status['playlistlength'];
			}
			array_push($cmds, 'play ' . $pos);
			chainMpdCmds($sock, $cmds);
		}

		playerSession('write', 'toggle_songid', $pos);
		break;
	/*case 'clear_add_group':*/
	case 'clear_play_group':
		$cmds = array_merge(array('clear'), addGroupToQueue($_POST['path']));

		if ($_GET['cmd'] == 'clear_play_group') {
			array_push($cmds, 'play'); // Defaults to pos 0
		}

		chainMpdCmds($sock, $cmds);
		playerSession('write', 'toggle_songid', '0');
		break;
}

 // Close MPD socket
if (isset($sock) && $sock !== false) {
	closeMpdSock($sock);
}

//
// FUNCTIONS
//

// Get MPD queue
function getPlayqueue($sock) {
	sendMpdCmd($sock, 'playlistinfo');
	$resp = readMpdResp($sock);

	if (is_null($resp)) {
		return NULL;
	}
	else {
		$array = array();
		$line = strtok($resp,"\n");
		$idx = -1;

		while ($line) {
			list ($element, $value) = explode(': ', $line, 2);

			if ($element == 'file') {
				$idx++;
				$array[$idx]['file'] = $value;
				$array[$idx]['fileext'] = getFileExt($value);
				$array[$idx]['TimeMMSS'] = songTime($array[$idx]['Time']);
			} else {
				if ($element == 'Genre' || $element == 'Artist' || $element == 'AlbumArtist' || $element == 'Conductor' || $element == 'Performer') {
					// Return only the first of multiple occurrences of the following tags
					if (!isset($array[$idx][$element])) {
						$array[$idx][$element] = $value;
					}
				} else {
					// All other tags
					$array[$idx][$element] = $value;
				}
			}

			$line = strtok("\n");
		}
	}

	return $array;
}

// Add one item (song file, playlist, radio station, directory) to the Queue
function addItemToQueue($path) {
	$ext = getFileExt($path);
	$pl_extensions = array('m3u', 'pls', 'cue');
	//workerLog($path . ' (' . $ext . ')');

	// Use load for saved playlist, cue sheet, radio station
	if (in_array($ext, $pl_extensions) || (strpos($path, '/') === false && in_array($path, ROOT_DIRECTORIES) === false)) {
		// Radio station special case
		if (strpos($path, 'RADIO') !== false) {
			// Check for playlist as URL
			$pls = file_get_contents(MPD_MUSICROOT . $path);
			$url = parseDelimFile($pls, '=')['File1'];
			$ext = substr($url, -4);
			if ($ext == '.pls' || $ext == '.m3u') {
				$path = $url;
			}
		}
		$cmd = 'load';
	}
	// Use add for song file or directory
	else {
		$cmd = 'add';
	}

	return $cmd . ' "' . html_entity_decode($path) . '"';
}

// Add group of song files to the Queue (Tag/Album view)
function addGroupToQueue($songs) {
	$cmds = array();

	foreach ($songs as $song) {
		array_push($cmds, 'add "' . html_entity_decode($song) . '"');
	}

	return $cmds;
}

// Find a file or album in the Queue
function findInQueue($sock, $tag, $search) {
	sendMpdCmd($sock, 'playlistfind ' . $tag . ' "' . $search . '"');
	$resp = readMpdResp($sock);

	if ($resp == "OK\n") {
		return 'findInQueue(): ' . $tag . ' ' . $search . ' not found';
	}

	$array = array();
	$line = strtok($resp, "\n");

	// Return position
	if ($tag == 'file') {
		while ($line) {
			list ($element, $value) = explode(": ", $line, 2);
			if ($element == 'Pos') {
				$array['Pos'] = $value;
				break;
			}

			$line = strtok("\n");
		}
	}
	// Return files and positions
	else if ($tag == 'album') {
		$i = 0;
		while ($line) {
			list ($element, $value) = explode(": ", $line, 2);
			if ($element == 'file') {
				$array[$i]['file'] = $value;
			}
			if ($element == 'Pos') {
				$array[$i]['Pos'] = $value;
				$i++;
			}

			$line = strtok("\n");
		}
	}

	return $array;
}

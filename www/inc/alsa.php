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

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/sql.php';

function getAlsaMixerName($i2sDevice) {
	if ($i2sDevice == 'None' && $_SESSION['i2soverlay'] == 'None') {
		// USB devices, Pi HDMI-1/2 or Headphone jack
		$result = sysCmd('/var/www/util/sysutil.sh get-mixername');
		if ($result[0] == '') {
			// Mixer name not found => Use default mixer name "PCM"
			$mixerName = 'PCM';
		} else {
			// Mixer name defined => Use actual mixer name
			// Strip off delimiters added by sysutil.sh get-mixername
			$mixerName = ltrim($result[0], '(');
			$mixerName = rtrim($mixerName, ')');
		}
	} else {
		// I2S devices
		// Mixer name exceptions
		if ($i2sDevice == 'HiFiBerry Amp(Amp+)') {
			$mixerName = 'Channels';
		} else if ($i2sDevice == 'HiFiBerry DAC+ DSP') {
			$mixerName = 'DSPVolume';
		} else if ($_SESSION['i2soverlay'] == 'hifiberry-dacplushd') {
			$mixerName = 'DAC';
		} else if ($i2sDevice == 'Allo Katana DAC' || $i2sDevice == 'Allo Boss 2 DAC' ||
			($i2sDevice == 'Allo Piano 2.1 Hi-Fi DAC' && $_SESSION['piano_dualmode'] != 'None')) {
			$mixerName = 'Master';
		} else {
			// No mixer defined or use default mixer name "Digital"
			$result = sysCmd('/var/www/util/sysutil.sh get-mixername');
			if ($result[0] == '') {
				// Mixer name not defined => no actual mixer exists
				$mixerName = 'none';
			} else {
				// Mixer name defined => use default mixer name "Digital"
				$mixerName = 'Digital';
			}
		}
	}

	return $mixerName;
}

function getAlsaVolume($mixerName) {
	$result = sysCmd('/var/www/util/sysutil.sh get-alsavol ' . '"' . $mixerName . '"');
	if (substr($result[0], 0, 6 ) == 'amixer') {
		$alsaVolume = 'none';
	} else {
		$alsaVolume = str_replace('%', '', $result[0]);
	}

	return $alsaVolume;
}

// Get device names assigned to each ALSA card
function getAlsaDeviceNames() {
	// Pi HDMI 1, HDMI 2 or Headphone jack, or a USB audio device
	if ($_SESSION['i2sdevice'] == 'None' && $_SESSION['i2soverlay'] == 'None') {
		// Pi HDMI 1, HDMI 2 or Headphone jack, or a USB audio device
		for ($i = 0; $i < 4; $i++) {
			$alsaId = trim(file_get_contents('/proc/asound/card' . $i . '/id'));

			if (empty($alsaId)) {
				$devices[$i] = $i == $_SESSION['cardnum'] ? $_SESSION['adevname'] : '';
			} else if ($alsaId != 'Loopback' && $alsaId != 'Dummy') {
				$aplayDeviceName = trim(sysCmd("aplay -l | awk -F'[' '/card " . $i . "/{print $2}' | cut -d']' -f1")[0]);
				$result = sqlRead('cfg_audiodev', sqlConnect(), $alsaId);
				if ($result === true) { // Not in table
					$devices[$i] = $aplayDeviceName;
				} else {
					$devices[$i] = $result[0]['alt_name'];
				}
			}
		}
	} else {
		// I2S audio device
		$devices[0] = 'I2S audio device';
	}

	return $devices;
}

function getAlsaHwParams($cardNum) {
	$result = shell_exec('cat /proc/asound/card' . $cardNum . '/pcm0p/sub0/hw_params');

	if (is_null($result)) {
		return null;
	} else if ($result != "closed\n" && $result != "no setup\n") {
		$array = array();
		$line = strtok($result, "\n");

		while ($line) {
			list ($element, $value) = explode(': ', $line);
			$array[$element] = $value;
			$line = strtok("\n");
		}

		// Rate "44100 (44100/1)"
	 	$rate = substr($array['rate'], 0, strpos($array['rate'], ' ('));
	 	$array['rate'] = formatRate($rate);
	 	$floatRate = (float)$rate;

		if (substr($array['format'], 0, 3) == 'DSD') {
			// format DSD_U16_BE" or "DSD_U32_BE"
			$floatBits = (float)substr($array['format'], 5, 2);
			$array['format'] = 'DSD bitstream';
		} else {
			// format "S24_3LE" etc
			$array['format'] = substr($array['format'], 1, 2);
			$floatBits = (float)$array['format'];
		}

		// channels
		$floatChannels = (float)$array['channels'];
		$array['channels'] = formatChannels($array['channels']);

		// Mbps rate
		$array['status'] = 'active';
		$array['calcrate'] = number_format((($floatRate * $floatBits * $floatChannels) / 1000000), 3, '.', '');
	} else {
		$array['status'] = trim($result, "\n");
		$array['calcrate'] = '0 bps';
	}

	return $array;
}

// Get ALSA card ID's
function getAlsaCards() {
	$cards = array();
	$maxCards = 4;
	for ($i = 0; $i < $maxCards; $i++) {
		$cardId = trim(file_get_contents('/proc/asound/card' . $i . '/id'));
		$cards[$i] = empty($cardId) ? 'empty' : $cardId;
	}
	return $cards;
}

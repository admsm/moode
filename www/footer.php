<!--
/**
 * moOde audio player (C) 2014 Tim Curtis
 * http://moodeaudio.org
 *
 * tsunamp player ui (C) 2013 Andrea Coiutti & Simone De Gregori
 * http://www.tsunamp.com
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
-->
<!-- ABOUT -->
<div id="about-modal" class="modal modal-sm hide" tabindex="-1" role="dialog" aria-labelledby="about-modal-label" aria-hidden="true">
	<div class="modal-body">
		<button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<p style="text-align:center;font-size:40px;font-weight:500;letter-spacing:-2px;margin-top:2px">m<span style="color:#d35400;line-height:12px">oO</span>de<span style="font-size:12px;position:relative;top:-15px;left:-3px;">™</span></p>
			<p>SWAR-6072D Audio Player is a derivative of the wonderful WebUI audio player client for MPD originally designed and coded by Andrea Coiutti and Simone De Gregori, and subsequently enhanced by early efforts from the RaspyFi/Volumio projects.</p>
			<h4>Release Information</h4>
			<ul>
				<li>Release: 8.1.1 2022-06-24</li> <!-- NOTE: getMoodeRel() parses this  -->
				<li>Maintainer: Tim Curtis &copy; 2014</li>
				<li>Documentation: <a class="moode-about-link" href="./relnotes.txt" target="_blank">View release notes,</a>&nbsp<a class="moode-about-link" href="./setup.txt" target="_blank">View setup guide</a></li>
				<li>Contributors: <a class="moode-about-link" href="./CONTRIBS.html" target="_blank">View contributors</a></li>
				<li>License: <a class="moode-about-link" href="./COPYING.html" target="_blank">View GPLv3</a></li>
			</ul>
		<p>
			<h4>Platform Information</h4>
			<ul>
				<li>RaspiOS: <span id="sys-raspbian-ver"></span></li>
				<li>Linux kernel: <span id="sys-kernel-ver"></span></li>
				<li>Platform: <span id="sys-hardware-rev"></span></li>
				<li>Architecture: <span id="sys-processor-arch"></span></li>
				<li>MPD version: <span id="sys-mpd-ver"></span></li>
			</ul>
		</p>
	</div>
	<div class="modal-footer">
		<button aria-label="Close" class="btn singleton" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

<!-- CONFIGURE -->
<div id="configure-modal" class="modal modal-sm hide" tabindex="-1" role="dialog" aria-labelledby="configure-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="configure-modal-label">Configuration settings</h3>
	</div>
	<div class="modal-body">
		<div id="configure">
			<ul>
				<li><a href="lib-config.php" class="btn btn-large"><i class="fas fa-database"></i><br>Library</a></li>
				<!-- <li><a href="snd-config.php" class="btn btn-large"><i class="fas fa-volume-up"></i><br>Audio</a></li> -->
				<li><a href="net-config.php" class="btn btn-large"><i class="fas fa-sitemap"></i><br>Network</a></li>
				<!-- <li><a href="sys-config.php" class="btn btn-large"><i class="fas fa-desktop-alt"></i><br>System</a></li> -->
				<!-- <li><a href="mpd-config.php" class="btn btn-large"><i class="fas fa-play"></i><br>MPD</a></li> -->
				<!-- <li><a href="cdsp-config.php" class="btn btn-large"><i class="fas fa-sliders-v-square"></i><br>CamillaDSP</a></li> -->
				<?php if ($_SESSION['feat_bitmask'] & FEAT_MULTIROOM) { ?>
					<!-- <li><a href="trx-config.php" class="btn btn-large"><i class="fas fas fa-rss"></i><br>Multiroom</a></li> -->
				<?php } ?>
				<!-- <li class="context-menu"><a href="#notarget" class="btn btn-large" data-cmd="setforclockradio-m"><i class="fas fa-alarm-clock"></i><br>Clock radio</a></li> -->
				<?php if ($_SESSION['feat_bitmask'] & FEAT_INPSOURCE) { ?>
					<!-- <li><a href="inp-config.php" class="btn btn-large"><i class="far fa-scrubber"></i><br>Input source</a></li> -->
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="modal-footer">
		<button aria-label="Close" class="btn singleton" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

<!-- PLAYERS -->
<div id="players-modal" class="modal modal-sm hide" tabindex="-1" role="dialog" aria-labelledby="players-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="players-modal-label">Players</h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button aria-label="Close" class="btn singleton" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

<!-- AUDIO INFO -->
<div id="audioinfo-modal" class="modal modal-sm hide" tabindex="-1" role="dialog" aria-labelledby="audioinfo-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="audioinfo-modal-label">Audio information</h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button aria-label="Close" class="btn singleton" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

<!-- SYSTEM INFO -->
<div id="sysinfo-modal" class="modal modal-sm hide" tabindex="-1" role="dialog" aria-labelledby="sysinfo-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="sysinfo-modal-label">System information</h3>
	</div>
	<div class="modal-body">
	</div>
	<div class="modal-footer">
		<button aria-label="Close" class="btn singleton" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

<!-- QUICK HELP -->
<div id="quickhelp-modal" class="modal modal-sm hide" tabindex="-1" role="dialog" aria-labelledby="help-modal-label" aria-hidden="true">
	<div class="modal-header">
		<button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="help-modal-label">Quick Help</h3>
	</div>
	<div class="modal-body">
		<div id="quickhelp"></div>
	</div>
	<div class="modal-footer">
		<button aria-label="Close" class="btn singleton" data-dismiss="modal" aria-hidden="true">Close</button>
	</div>
</div>

<!-- EQUALIZER -->
<div id="eq-modal" class="modal modal-sm2 hide" tabindex="-1" role="dialog" aria-labelledby="eq-modal-label" aria-hidden="true">
		<legend id="equalizers">Equalizers</legend>
		<p>
			The Equalizers are supported by MPD and the Airplay and Spotify renderers.<br>
			NOTE: The Equalizers, Crossfeed, and Polarity inversion are mutually exclusive. Enabling one will disable the SET button on the others.
		</p><br>
		<fieldset>
			<div class="control-group">
				<label class="control-label" for="eqfa12p">Parametric EQ</label>
				<div class="controls">
					<select id="eqfa12p" class="input-large" name="eqfa12p">
						$_select[eqfa12p]
					</select>
					<button class="btn btn-primary btn-small set-button btn-submit" type="submit" name="update_eqfa12p" value="novalue" $_eqfa12p_set_disabled>Set</button>
					<a aria-label="Help" class="info-toggle" data-cmd="info-eqp" href="#notarget"><i class="fas fa-info-circle"></i></a>
					<span id="info-eqp" class="help-block-configs help-block-margin hide">
						Three x Four Mitra-Regalia peaking equaliser filters in series; a vector arithmetic re-implementation of Fons Adriaensens "Parametric1" equaliser[fafil] with minor differences. Parallelisation of the serial filter organisation causes the output to lag by three samples. This EQ uses the @bitlab custom eqfa12p component of the CAPS suite of DSP programs written by Tim Goetze<br>
						<b>Note:</b> Equalizer processing applies to MPD, Airplay and Spotify output.<br>
                    </span>
					<div style="margin-top:.5em">
						<a href="eqp-config.php"><button class="btn btn-medium btn-primary">Edit</button></a>&nbsp;EQ curve<br>
					</div>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="alsaequal">Graphic EQ</label>
				<div class="controls">
					<select id="alsaequal" class="input-large" name="alsaequal">
						$_select[alsaequal]
					</select>
					<button class="btn btn-primary btn-small set-button btn-submit" type="submit" name="update_alsaequal" value="novalue" $_alsaequal_set_disabled>Set</button>
					<a aria-label="Help" class="info-toggle" data-cmd="info-alsaequal" href="#notarget"><i class="fas fa-info-circle"></i></a>
					<span id="info-alsaequal" class="help-block-configs help-block-margin hide">
						A classic octave-band, constant-Q, second-order filter design. Frequency bands centered above Nyquist are automatically disabled. This EQ uses the Eq10X2 component of the CAPS suite of DSP programs written by Tim Goetze.<br>
						<b>Note:</b> Equalizer processing applies to MPD, Airplay and Spotify output.<br>
                    </span>
					<div style="margin-top:.5em">
						<a href="eqg-config.php"><button class="btn btn-medium btn-primary">Edit</button></a>&nbsp;EQ curve<br>
					</div>
				</div>
			</div>

			<div>
				<label class="control-label" for="camilladsp">CamillaDSP</label>
				<div class="controls">
					<select id="camilladsp" class="input-large" name="camilladsp">
						$_select[camilladsp]
					</select>
					<button class="btn btn-primary btn-small set-button btn-submit" type="submit" name="update_camilladsp" value="novalue" $_camilladsp_set_disabled>Set</button>
					<a aria-label="Help" class="info-toggle" data-cmd="info-camilladsp" href="#notarget"><i class="fas fa-info-circle"></i></a>
					<div id="info-camilladsp" class="help-block-configs help-block-margin hide">
						CamillaDSP is a general purpose tool for routing and filtering sound. It can be used for example for building crossovers for active speakers, or for performing room correction. CamillaDSP was written by Henrik Enquist and integrated into SWAR-6072D by @bitlab.<br>
						Feature summary:<br>
						- IIR filters (BiQuad)<br>
						- FIR filters (Convolution via FFT)<br>
						- Built-in sample rate converter<br>
						- Filters can be chained freely<br>
						- Flexible routing<br>
						- Simple YAML configuration<br>
						- All calculations done with 64-bit floats<br>
						- ARM Neon 64-bit instruction set is used<br>
						- Pipeline editor with expert mode
						<br>
						Configurations can be managed at the CamillaDSP settings page.<br>
                    </div>
					<div>
						$camilladsp_config_check
					</div>
					<div style="margin-top:.5em">
						<a href="cdsp-config.php"><button class="btn btn-medium btn-primary" $_camilladsp_set_disabled>Edit</button></a>&nbsp;&nbsp;CamillaDSP settings<br>
					</div>
				</div>
			</div>

		</fieldset>



		<div class="modal-footer">
			<button aria-label="Cancel" class="btn singleton" data-dismiss="modal" aria-hidden="true">Cancel</button>
		</div>
	</div>



	
	<!-- POWER -->
<div id="power-modal" class="modal modal-sm2 hide" tabindex="-1" role="dialog" aria-labelledby="power-modal-label" aria-hidden="true">
	<!-- <div class="modal-header">
		<button aria-label="Close" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="power-modal-label">Power Options</h3>
	</div> -->
	<div class="modal-body">
		<button aria-label="Shutdown" id="system-shutdown" data-dismiss="modal" class="btn btn-primary btn-large btn-block">Power Off</button>
		<!-- <button aria-label="Restart" id="system-restart" data-dismiss="modal" class="btn btn-primary btn-large btn-block" style="margin-bottom:15px;">Restart</button> -->
	</div>
	<div class="modal-footer">
		<button aria-label="Cancel" class="btn singleton" data-dismiss="modal" aria-hidden="true">Cancel</button>
	</div>
</div>

<!-- RECONNECT/RESTART/SHUTDOWN -->
<div id="reconnect" class="hide">
	<div class="reconnect-bg"></div>
	<a href="javascript:location.reload(true); void 0" class="btn reconnect-btn">Power On</a>
</div>

<div id="restart" class="hide">
	<div class="reconnect-bg"></div>
	<a href="javascript:location.reload(true); void 0" class="btn reconnect-btn">Power On</a>
	<span class="reconnect-msg">Powered On</span>
</div>

<div id="shutdown" class="hide">
	<div class="reconnect-bg"></div>
	<a href="javascript:location.reload(true); void 0" class="btn reconnect-btn">Power On</a>
	<span class="reconnect-msg">Powered Off</span>
</div>

<?php
    //workerLog('-- footer.php');
    $return_val = session_write_close();
	//workerLog('session_write_close=' . (($return_val) ? 'TRUE' : 'FALSE'));
	echo "</body></html>";
?>

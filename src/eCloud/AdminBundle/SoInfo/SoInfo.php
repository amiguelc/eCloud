<?php
///////////Codigo copiado de linfo 1.9/////////////

namespace eCloud\AdminBundle\SoInfo;

class SoInfo{
	public function prueba(){
	return "aa";
	}

// Get a file's contents, or default to second param
	function getContents($file, $default = '') {
		return !is_file($file) || !is_readable($file) || !($contents = @file_get_contents($file)) ? $default : trim($contents);
	}
	
	function getDistro() {

		// Store the distribution's files we check for, optional regex parsing string, and name of said distro here:
		$distros = array(
			
			// This snags ubuntu and other distros which use the lsb method of identifying themselves
			array('/etc/lsb-release','/^DISTRIB_ID=([^$]+)$\n^DISTRIB_RELEASE=([^$]+)$\n^DISTRIB_CODENAME=([^$]+)$\n/m', false),
			
			// These working snag versions
			array('/etc/redhat-release', '/^CentOS release ([\d\.]+) \(([^)]+)\)$/', 'CentOS'),
			array('/etc/redhat-release', '/^Red Hat.+release (\S+) \(([^)]+)\)$/', 'RedHat'),
			array('/etc/fedora-release', '/^Fedora(?: Core)? release (\d+) \(([^)]+)\)$/', 'Fedora'),
			array('/etc/gentoo-release', '/([\d\.]+)$/', 'Gentoo'),
			array('/etc/SuSE-release', '/^VERSION = ([\d\.]+)$/m', 'openSUSE'),
			array('/etc/slackware-version', '/([\d\.]+)$/', 'Slackware'),

			// These don't because they're empty 
			array('/etc/arch-release', '', 'Arch'),

			// I'm unaware of the structure of these files, so versions are not picked up
			array('/etc/mklinux-release', '', 'MkLinux'),
			array('/etc/tinysofa-release ', '', 'TinySofa'),
			array('/etc/turbolinux-release ', '', 'TurboLinux'),
			array('/etc/yellowdog-release ', '', 'YellowDog'),
			array('/etc/annvix-release ', '', 'Annvix'),
			array('/etc/arklinux-release ', '', 'Arklinux'),
			array('/etc/aurox-release ', '', 'AuroxLinux'),
			array('/etc/blackcat-release ', '', 'BlackCat'),
			array('/etc/cobalt-release ', '', 'Cobalt'),
			array('/etc/immunix-release ', '', 'Immunix'),
			array('/etc/lfs-release ', '', 'Linux-From-Scratch'),
			array('/etc/linuxppc-release ', '', 'Linux-PPC'),
			array('/etc/mklinux-release ', '', 'MkLinux'),
			array('/etc/nld-release ', '', 'NovellLinuxDesktop'),

			// Leave this since debian derivitives might have it in addition to their own file
			// If it's last it ensures nothing else has it and thus it should be normal debian
			array('/etc/debian_version', false, 'Debian'),
		);

		// Hunt
		foreach ($distros as $distro) {

			// File we're checking for exists and is readable
			if (file_exists($distro[0]) && is_readable($distro[0])) {

				// Get it
				$contents = $distro[1] !== '' ? getContents($distro[0], '') : '';

				// Don't use regex, this is enough; say version is the file's contents
				if ($distro[1] === false) {
					return array(
						'name' => $distro[2],
						'version' => $contents == '' ? false : $contents
					);
				}
				
				// No fucking idea what the version is. Don't use the file's contents for anything
				elseif($distro[1] === '') {
					return array(
						'name' => $distro[2],
						'version' => false
					);
				}

				// Get the distro out of the regex as well?
				elseif($distro[2] === false && preg_match($distro[1], $contents, $m)) {
					return array(
						'name' => $m[1],
						'version' => $m[2] . (isset($m[3]) ? ' ('.$m[3].')' : '')
					);
				}

				// Our regex match it?
				elseif(preg_match($distro[1], $contents, $m)) {
					return array(
						'name' => $distro[2],
						'version' => $m[1] . (isset($m[2]) ? ' ('.$m[2].')' : '')
					);
				}
			}
		}

		// Return lack of result if we didn't find it
		return array(
			'name' =>'No encontrado',
			'version' => ''
		);
		}
	
	function getCPU() {

		// File that has it
		$file = '/proc/cpuinfo';

		// Not there?
		if (!is_file($file) || !is_readable($file)) {
			$this->error->add('Linfo Core', '/proc/cpuinfo not readable');
			return array();
		}

		/*
		 * Get all info for all CPUs from the cpuinfo file
		 */

		// Get contents
		$contents = trim(@file_get_contents($file));

		// Lines
		$lines = explode("\n", $contents);

		// Store CPUs here
		$cpus = array();

		// Holder for current CPU info
		$cur_cpu = array();

		// Go through lines in file
		$num_lines = count($lines);
		
		// We use the key of the first line to separate CPUs
		$first_line = substr($lines[0], 0, strpos($lines[0], ' '));
		
		for ($i = 0; $i < $num_lines; $i++) {
			
			// Approaching new CPU? Save current and start new info for this
			if (strpos($lines[$i], $first_line) === 0 && count($cur_cpu) > 0) {
				$cpus[] = $cur_cpu;
				$cur_cpu = array();
				
				// Default to unknown
				$cur_cpu['Model'] = 'Unknown';
			}

			// Info here
			$line = explode(':', $lines[$i], 2);

			if (!array_key_exists(1, $line))
				continue;

			$key = trim($line[0]);
			$value = trim($line[1]);

			
			// What we want are MHZ, Vendor, and Model.
			switch ($key) {
				
				// CPU model
				case 'model name':
				case 'cpu':
				case 'Processor':
					$cur_cpu['Model'] = $value;
				break;

				// Speed in MHz
				case 'cpu MHz':
					$cur_cpu['MHz'] = $value;
				break;

				case 'Cpu0ClkTck': // Old sun boxes
					$cur_cpu['MHz'] = hexdec($value) / 1000000;
				break;

				// Brand/vendor
				case 'vendor_id':
					$cur_cpu['Vendor'] = $value;
				break;
			}

		}

		// Save remaining one
		if (count($cur_cpu) > 0)
			$cpus[] = $cur_cpu;

		// Return them
		return $cpus;
	}
}
?>
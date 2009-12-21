<?php
// This file is a part of GIII (g3.steelwap.org)
/*
Name:
	tar Class

Author:
	Josh Barger <joshb@npt.com>

Description:
	This class reads and writes Tape-Archive (TAR) Files and Gzip
	compressed TAR files, which are mainly used on UNIX systems.
	This class works on both windows AND unix systems, and does
	NOT rely on external applications!! Woohoo!

Usage:
	Copyright (C) 2002  Josh Barger

	This library is free software; you can redistribute it and/or
	modify it under the terms of the GNU Lesser General Public
	License as published by the Free Software Foundation; either
	version 2.1 of the License, or (at your option) any later version.

	This library is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	Lesser General Public License for more details at:
		http://www.gnu.org/copyleft/lesser.html

	If you use this script in your application/website, please
	send me an e-mail letting me know about it :)

	(class modified by DreamDragon aka PM2D)
*/

final class tar {
	// Unprocessed Archive Information
	public $filename;
	public $isGzipped;
	public $tar_file;

	// Processed Archive Information
	public $files;
	public $directories;
	public $numFiles;
	public $numDirectories;


	// Class Constructor -- Does nothing...
	public function __construct() {
		return true;
	}


	// Computes the unsigned Checksum of a file's header
	// to try to ensure valid file
	// PRIVATE ACCESS FUNCTION
	private function __computeUnsignedChecksum($bytestring) {
		$unsigned_chksum = 0;
		for($i=0; $i<512; $i++)
			$unsigned_chksum += ord($bytestring[$i]);
		for($i=0; $i<8; $i++)
			$unsigned_chksum -= ord($bytestring[148 + $i]);
		$unsigned_chksum += ord(' ') * 8;

		return $unsigned_chksum;
	}


	// Converts a NULL padded string to a non-NULL padded string
	// PRIVATE ACCESS FUNCTION
	private function __parseNullPaddedString($string) {
		$position = strpos($string,chr(0));
		return substr($string,0,$position);
	}


	// This function parses the current TAR file
	// PRIVATE ACCESS FUNCTION
	private function __parseTar() {
		// Read Files from archive
		$tar_length = strlen($this->tar_file);
		$main_offset = 0;
		while($main_offset < $tar_length) {
			// If we read a block of 512 nulls, we are at the end of the archive
			if(substr($this->tar_file,$main_offset,512) == str_repeat(chr(0),512))
				break;

			// Parse file name
			$file_name		= $this->__parseNullPaddedString(substr($this->tar_file,$main_offset,100));

			// Parse the file mode
			$file_mode		= substr($this->tar_file,$main_offset + 100,8);

			// Parse the file user ID
			$file_uid		= octdec(substr($this->tar_file,$main_offset + 108,8));

			// Parse the file group ID
			$file_gid		= octdec(substr($this->tar_file,$main_offset + 116,8));

			// Parse the file size
			$file_size		= octdec(substr($this->tar_file,$main_offset + 124,12));

			// Parse the file update time - unix timestamp format
			$file_time		= octdec(substr($this->tar_file,$main_offset + 136,12));

			// Parse Checksum
			$file_chksum		= octdec(substr($this->tar_file,$main_offset + 148,6));

			// Parse user name
			$file_uname		= $this->__parseNullPaddedString(substr($this->tar_file,$main_offset + 265,32));

			// Parse Group name
			$file_gname		= $this->__parseNullPaddedString(substr($this->tar_file,$main_offset + 297,32));

			// Make sure our file is valid
			if($this->__computeUnsignedChecksum(substr($this->tar_file,$main_offset,512)) != $file_chksum)
				return false;

			// Parse File Contents
			$file_contents		= substr($this->tar_file,$main_offset + 512,$file_size);

			if($file_size > 0) {
				// Increment number of files
				$this->numFiles++;

				// Create us a new file in our array
				$activeFile = &$this->files[];

				// Asign Values
				$activeFile['name']		= $file_name;
				$activeFile['mode']		= $file_mode;
				$activeFile['size']		= $file_size;
				$activeFile['time']		= $file_time;
				$activeFile['user_id']		= $file_uid;
				$activeFile['group_id']		= $file_gid;
				$activeFile['user_name']	= $file_uname;
				$activeFile['group_name']	= $file_gname;
				$activeFile['checksum']		= $file_chksum;
				$activeFile['file']		= $file_contents;

			} else {
				// Increment number of directories
				$this->numDirectories++;

				// Create a new directory in our array
				$activeDir = &$this->directories[];

				// Assign values
				$activeDir['name']		= $file_name;
				$activeDir['mode']		= $file_mode;
				$activeDir['time']		= $file_time;
				$activeDir['user_id']		= $file_uid;
				$activeDir['group_id']		= $file_gid;
				$activeDir['user_name']		= $file_uname;
				$activeDir['group_name']	= $file_gname;
				$activeDir['checksum']		= $file_chksum;
			}

			// Move our offset the number of blocks we have processed
			$main_offset += 512 + (ceil($file_size / 512) * 512);
		}

		return true;
	}


	// Read a non gzipped tar file in for processing
	// PRIVATE ACCESS FUNCTION
	private function __readTar($filename='') {
		// Set the filename to load
		if(!$filename)
			$filename = $this->filename;

		// Read in the TAR file
		$fp = fopen($filename,'rb');
		$this->tar_file = fread($fp,filesize($filename));
		fclose($fp);

		if($this->tar_file[0] == chr(31) && $this->tar_file[1] == chr(139) && $this->tar_file[2] == chr(8)) {
			if(!function_exists('gzinflate'))
				return false;

			$this->isGzipped = TRUE;

			$this->tar_file = gzinflate(substr($this->tar_file,10,-4));
		}

		// Parse the TAR file
		$this->__parseTar();

		return true;
	}

	// Open a TAR file
	public function openTAR($filename) {
		// Clear any values from previous tar archives
		unset($this->filename);
		unset($this->isGzipped);
		unset($this->tar_file);
		unset($this->files);
		unset($this->directories);
		unset($this->numFiles);
		unset($this->numDirectories);

		// If the tar file doesn't exist...
		if(!file_exists($filename))
			return false;

		$this->filename = $filename;

		// Parse this file
		$this->__readTar();

		return true;
	}

	// Retrieves information about a file in the current tar archive
	public function getFile($filename) {
		if($this->numFiles > 0) {
			foreach($this->files as $key => $information) {
				if($information["name"] == $filename)
					return $information;
			}
		}

		return false;
	}

	// Check if this tar archive contains a specific file
	public function containsFile($filename) {
		if($this->numFiles > 0) {
			foreach($this->files as $key => $information) {
				if($information['name'] == $filename)
					return true;
			}
		}

		return false;
	}

}

?>
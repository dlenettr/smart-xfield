<?php
/*
=====================================================
 MWS Smart Xfield v1.3 - by MaRZoCHi
-----------------------------------------------------
 Site: http://dle.net.tr/
-----------------------------------------------------
 Copyright (c) 2015
-----------------------------------------------------
 Lisans: GPL License
=====================================================
*/

if ( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if ( $dle_module == "showfull" OR $dle_module == "main" OR $dle_module == "search" ) {

	if ( ! function_exists( 'show_file' ) ) {
		function show_file( $file ) {
			global $config;
			$path = ROOT_DIR . "/templates/" . $config['skin'] . "/" . $file;
			if ( file_exists( $path ) ) {
				$content = file_get_contents( $path );
			} else $content = "File not exists.";
			return $content;
		}
	}

	if ( strpos( $tpl->copy_template, "{if xfield=" ) ) {
		$xfdata = xfieldsdataload( $row['xfields'] );
		// {if xfield="name"}...{/if}
		// {if xfield="name1|name2|name3"}...{/if}
		// {if xfield="name1&name2&name3"}...{/if}
		// {if...} ..{show file="dosya.tpl"}.. {/if}
		// {if...} ..{show file="klasor/dosya.tpl"}.. {/if}
		if ( preg_match_all( "#\{if xfield=['\"]([a-zA-Z0-9-_\|\&]+)['\"]\}[\s\n\t]*(.+?)[\s\n\t]*\{\/if\}#ies", $tpl->copy_template, $matches0 ) ) {
			$length = count( $matches0[0] );
			for ( $x = 0; $x < $length; $x++ ) {
				$sfile = false;
				if ( strpos( $matches0[0][$x], "{show file" ) !== false ) {
					if ( preg_match_all( "#\{show file=['\"](.+?)['\"]\}#im", $matches0[0][$x], $fmatch0 ) ) {
						$sfile = array( $fmatch0[1][0], $fmatch0[0][0] );
					} else $sfile = false;
				}
				if ( strpos( $matches0[1][$x], "|" ) != false ) {
					$keys = explode( "|", $matches0[1][$x] );
					foreach ( $keys as $key ) {
						if ( in_array( $key, array_keys( $xfdata ) ) && ! empty( $xfdata[ $key ] ) ) {
							if ( is_array( $sfile ) ) {
								$file_cont = show_file( $sfile[0] );
								$matches0[2][$x] = str_replace( $sfile[1], $file_cont, $matches0[2][$x] );
							}
							$tpl->copy_template = str_replace( $matches0[0][$x], $matches0[2][$x], $tpl->copy_template );
							break;
						}
					}

				} else if ( strpos( $matches0[1][$x], "&" ) != false ) {
					$keys = explode( "&", $matches0[1][$x] ); $stop = false;
					foreach ( $keys as $key ) {
						if ( ! in_array( $key, array_keys( $xfdata ) ) || empty( $xfdata[ $key ] ) ) {
							$stop = true;
							break;
						}
					}
					if ( $stop ) $tpl->copy_template = str_replace( $matches0[0][$x], "", $tpl->copy_template );
					else {
						if ( is_array( $sfile ) ) {
							$file_cont = show_file( $sfile[0] );
							$matches0[2][$x] = str_replace( $sfile[1], $file_cont, $matches0[2][$x] );
						}
						$tpl->copy_template = str_replace( $matches0[0][$x], $matches0[2][$x], $tpl->copy_template );
					}
				} else {
					$key = $matches0[1][$x];
					if ( in_array( $key, array_keys( $xfdata ) ) && ! empty( $xfdata[ $key ] ) ) {
						if ( is_array( $sfile ) ) {
							$sfile[0] = str_replace( "{value}", $xfdata[ $key ], $sfile[0] );
							$file_cont = show_file( $sfile[0] );
							$matches0[2][$x] = str_replace( $sfile[1], $file_cont, $matches0[2][$x] );
						}
						$matches0[2][$x] = str_replace( "{value}", $xfdata[ $key ], $matches0[2][$x] );
						$tpl->copy_template = str_replace( $matches0[0][$x], $matches0[2][$x], $tpl->copy_template );
					} else {
						$tpl->copy_template = str_replace( $matches0[0][$x], "", $tpl->copy_template );
					}
				}
			}
		}
		// {if xfield="name" eq="value"}...{/if}
		// {if xfield="name" not-eq="value"}...{/if}
		// {if xfield="name" eq="value1|value2|value3"}...{/if}
		// {if xfield="name" not-eq="value1|value2|value3"}...{/if}
		// {if...} ..{show file="dosya.tpl"}.. {/if}
		if ( preg_match_all( "#\{if xfield=['\"](.+?)['\"] ([not-]*)eq=['\"](.+?)['\"]\}[\s\n\t]*(.+?)[\s\n\t]*\{\/if\}#ies", $tpl->copy_template, $matches1 ) ) {
			$length = count( $matches1[0] );
			for ( $x = 0; $x < $length; $x++ ) {
				if ( strpos( $matches1[0][$x], "{else}" ) != false ) break;
				if ( in_array( $matches1[1][$x], array_keys( $xfdata ) ) ) {
					$matches1[4][$x] = str_replace( "{value}", $xfdata[ $matches1[1][$x] ], $matches1[4][$x] );
					if ( ! empty( $matches1[2][$x] ) && $matches1[2][$x] == "not-" ) {
						if ( ! in_array( $xfdata[ $matches1[1][$x] ], explode("|", $matches1[3][$x] ) ) ) {
							if ( strpos( $matches1[4][$x], "{show file" ) !== false ) {
								if ( preg_match_all( "#\{show file=['\"](.+?)['\"]\}#im", $matches1[4][$x], $fmatch1 ) ) {
									$file_cont = show_file( $fmatch1[1][0] );
									$matches1[4][$x] = str_replace( $fmatch1[0][0], $file_cont, $matches1[4][$x] );
								}
							}
							$tpl->copy_template = str_replace( $matches1[0][$x], $matches1[4][$x], $tpl->copy_template );
						}
						else { $tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template ); }
					} else {
						if ( in_array( $xfdata[ $matches1[1][$x] ], explode("|", $matches1[3][$x] ) ) ) {
							if ( strpos( $matches1[4][$x], "{show file" ) !== false ) {
								if ( preg_match_all( "#\{show file=['\"](.+?)['\"]\}#im", $matches1[4][$x], $fmatch1 ) ) {
									$file_cont = show_file( $fmatch1[1][0] );
									$matches1[4][$x] = str_replace( $fmatch1[0][0], $file_cont, $matches1[4][$x] );
								}
							}
							$tpl->copy_template = str_replace( $matches1[0][$x], $matches1[4][$x], $tpl->copy_template );
						}
						else { $tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template ); }
					}

				} else {
					$tpl->copy_template = str_replace( $matches1[0][$x], "", $tpl->copy_template );
				}
			}
		}
		// {if xfield="name" eq="value"}...{else}...{/if}
		// {if xfield="name" not-eq="value"}...{else}...{/if}
		// {if xfield="name" eq="value1|value2|value3"}...{else}...{/if}
		// {if xfield="name" not-eq="value1|value2|value3"}...{else}...{/if}
		// {if...} ..{show file="dosya.tpl"}.. {else} ..{show file="dosya.tpl"}.. {/if}
		if ( preg_match_all( "#\{if xfield=['\"](.+?)['\"] eq=['\"](.+?)['\"]\}[\s\n\t]*(.+?)[\s\n\t]*\{else\}[\s\n\t]*(.+?)[\s\n\t]*\{\/if\}#ies", $tpl->copy_template, $matches2 ) ) {
			$length = count( $matches2[0] );
			for ( $x = 0; $x < $length; $x++ ) {
				if ( in_array( $matches2[1][$x], array_keys( $xfdata ) ) ) {
					$matches2[3][$x] = str_replace( "{value}", $xfdata[ $matches2[1][$x] ], $matches2[3][$x] );
					$matches2[4][$x] = str_replace( "{value}", $xfdata[ $matches2[1][$x] ], $matches2[4][$x] );
					if ( in_array( $xfdata[ $matches2[1][$x] ], explode("|", $matches2[2][$x] ) ) ) {
						if ( strpos( $matches2[3][$x], "{show file" ) !== false ) {
							if ( preg_match_all( "#\{show file=['\"](.+?)['\"]\}#im", $matches2[3][$x], $fmatch2 ) ) {
								$file_cont = show_file( $fmatch2[1][0] );
								$matches2[3][$x] = str_replace( $fmatch2[0][0], $file_cont, $matches2[3][$x] );
							}
						}
						$tpl->copy_template = str_replace( $matches2[0][$x], $matches2[3][$x], $tpl->copy_template );
					}
					else {
						if ( strpos( $matches2[4][$x], "{show file" ) !== false ) {
							if ( preg_match_all( "#\{show file=['\"](.+?)['\"]\}#im", $matches2[4][$x], $fmatch2 ) ) {
								$file_cont = show_file( $fmatch2[1][0] );
								$matches2[4][$x] = str_replace( $fmatch2[0][0], $file_cont, $matches2[4][$x] );
							}
						}
						$tpl->copy_template = str_replace( $matches2[0][$x], $matches2[4][$x], $tpl->copy_template );
					}
				} else {
					$tpl->copy_template = str_replace( $matches2[0][$x], "", $tpl->copy_template );
				}
			}
		}
		unset( $xfdata, $matches0, $matches1, $matches2 );
	}
}

?>
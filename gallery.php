<?php

$images_dir = "/var/www/public/images/";
$images_url = "http://vps.bismith.net/images/";
$thumbs_dir = "/var/www/public/thumbs/";
$thumbs_url = "http://vps.bismith.net/thumbs/";

$columns = 4;
$thumb_width = 200; // pixels

function startsWith($haystack, $needle)
{
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle)
{
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function getThumbsTable()
{
	global $images_dir;
	global $images_url;
	global $thumbs_dir;
	global $thumbs_url;
	global $columns;
	global $thumb_width;

	$ret = "<table id=\"thumbs\" border=\"0\">\n";
	$array = scandir($images_dir);
	$counter = 0;
	foreach ($array as $image) {
		if (endsWith($image, ".jpg")) {
			if ($counter === 0)
				$ret .= "<tr>";
			if (!file_exists($thumbs_dir . $image)) {
				make_thumb($images_dir . $image, $thumbs_dir . $image, $thumb_width);
			}
			$ret .= "<td><a href=\"$images_url$image\"><img src=\"$thumbs_url$image\"></a></td>\n";
			$counter++;
			if ($counter >= $columns) {
				$counter = 0;
				$ret .= "</tr>";
			}
		}
	}
	if (!endsWith($ret, "</td>")) {
		$ret .= "</tr>";
	}
	$ret .= "</table>\n";

	return $ret;
}

function make_thumb($src, $dest, $desired_width)
{
	/* read the source image */
	$source_image = imagecreatefromjpeg($src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);

	/* find the "desired height" of this thumbnail, relative to the desired width  */
	$desired_height = floor($height * ($desired_width / $width));

	/* create a new, "virtual" image */
	$virtual_image = imagecreatetruecolor($desired_width, $desired_height);

	/* copy source image at a resized size */
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

	/* create the physical thumbnail image to its destination */
	imagejpeg($virtual_image, $dest);
}

function getClientInfo()
{
	$return = $_SERVER['HTTP_USER_AGENT'];
	$return .= ", ";
	$return .= $_SERVER['REMOTE_ADDR'];
	$return .= " ";
	$return .= "(" . gethostbyaddr($_SERVER['REMOTE_ADDR']) . ")";
	return $return;
}

?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title><?php
			echo "Image Gallery";
		?></title>
		<style type="text/css">

			/* for easy colored text n' stuff */
			span.red { color: red; }
			span.darkred { color: #cc2222; }
			span.orange { color: orange; }
			span.yellow { color: yellow; }
			span.green { color: green; }
			span.blue { color: blue; }
			span.purple { color: purple; }
			span.brown { color: #a52a2a; }
			span.black { color: black; }
			span.white { color: white; }
			span.teal { color: #00ccdd; } /* this is my teal, not their teal... dang W3C */

			body {
				margin: 0;
				border: 0;
				padding: 0;
				background-color: #000000;
				font-family: "Lucida Console", Monaco, monospace;
				font-size: 14px;
			}

			#header {
				width: 70%;
				margin: 0 auto;
				color: #cccccc;
			}

			#content {
				width: 70%;
				margin: 0 auto;
				color: #aaaaaa;
			}

			#thumbs {
				width: 100%;
				margin: 0 auto;
				/* border-collapse: collapse; */
			}

			#thumbs td {
				width: 25%;
				margin: 0 auto;
				border: 1px solid black;
			}

			#thumbs td img {
				display: block;
				margin-left: auto;
				margin-right: auto;
			}

			#content a.genlink:link { color: #5555ff; text-decoration: none; }
			#content a.genlink:visited { color: #5555ff; text-decoration: none; }
			#content a.genlink:hover { color: #2222ff; text-decoration: none; }
			#content a.genlink:active { color: #5555ff; text-decoration: none; }

			#content a.mp4link:link { color: #5555ff; text-decoration: none; }
			#content a.mp4link:visited { color: #5555ff; text-decoration: none; }
			#content a.mp4link:hover { color: #2222ff; text-decoration: none; }
			#content a.mp4link:active { color: #5555ff; text-decoration: none; }

			#content a.srtlink:link { color: #00bbdd; text-decoration: none; }
			#content a.srtlink:visited { color: #00bbdd; text-decoration: none; }
			#content a.srtlink:hover { color: #0099aa; text-decoration: none; }
			#content a.srtlink:active { color: #00bbdd; text-decoration: none; }

			#content a.mkvlink:link { color: #00bb00; text-decoration: none; }
			#content a.mkvlink:visited { color: #00bb00; text-decoration: none; }
			#content a.mkvlink:hover { color: #009900; text-decoration: none; }
			#content a.mkvlink:active { color: #00bb00; text-decoration: none; }

			#content a.xspflink:link { color: #ffff00; text-decoration: none; }
			#content a.xspflink:visited { color: #ffff00; text-decoration: none; }
			#content a.xspflink:hover { color: #ffffff; text-decoration: none; }
			#content a.xspflink:active { color: #ffff00; text-decoration: none; }

			#disclaimer {
				color: #888888;
			}

			#clientinfo {
				color: #666666;
			}

		</style>
	</head>

	<body class="home" id="top">
		<div id="header">
			<h1>Image Gallery</h1>
		</div>
		<div id="content">
			<?php
				echo getThumbsTable();
			?>
			<p id="clientinfo">
				<?php echo getClientInfo(); ?>
			</p>
		</div>
	</body>

</html>
